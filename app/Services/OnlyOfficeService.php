<?php

namespace App\Services;

use App\Models\OnlyOfficeDocument;
use App\Models\DocumentVersion;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OnlyOfficeService
{
    protected $serverUrl;
    protected $jwtSecret;
    protected $jwtEnabled;

    public function __construct()
    {
        $this->serverUrl = config('onlyoffice.server_url');
        $this->jwtSecret = config('onlyoffice.jwt_secret');
        $this->jwtEnabled = config('onlyoffice.jwt_enabled');
    }

    /**
     * Generate OnlyOffice config untuk editor
     */
    public function getEditorConfig(OnlyOfficeDocument $document, $userId)
    {
        $user = \App\Models\User::find($userId);

        // ✅ Untuk Docker, gunakan host.docker.internal
        // OnlyOffice di Docker perlu akses Laravel di host Windows
        // $baseUrl = 'http://localhost:8000';
        $baseUrl = rtrim(config('onlyoffice.app_url'), '/');
        // $baseUrl = 'http://host.docker.internal:8000';

        $config = [
            'documentType' => $this->getDocumentType($document->file_type),
            'document' => [
                'title' => $document->title,
                'url' => $baseUrl . '/api/onlyoffice/' . $document->id . '/download',
                'fileType' => $document->file_type,
                'key' => $document->key,
                'permissions' => [
                    'edit' => true,
                    'download' => true,
                    'print' => true,
                    'comment' => true,
                    'review' => true,
                ]
            ],
            'editorConfig' => [
                'mode' => 'edit',
                'lang' => 'id',
                'callbackUrl' => $baseUrl . '/api/onlyoffice/' . $document->id . '/callback',
                'user' => [
                    'id' => (string) $userId,
                    'name' => $user->name ?? 'User ' . $userId
                ],
                'customization' => [
                    'autosave' => true,
                    'forcesave' => true,
                    'commentAuthorOnly' => false,
                    'comments' => true,
                    'chat' => true,
                    'compactHeader' => false,
                    'feedback' => true,
                    'help' => true,
                    'hideRightMenu' => false,
                    'review' => true,
                    'spellcheck' => false
                ]
            ]
        ];

        if ($this->jwtEnabled && $this->jwtSecret) {
            $config['token'] = JWT::encode($config, $this->jwtSecret, 'HS256');
        }

        return $config;
    }

    protected function getDocumentType($fileType)
    {
        $types = [
            'xlsx' => 'cell',
            'xls' => 'cell',
            'csv' => 'cell',
            'docx' => 'word',
            'doc' => 'word',
            'txt' => 'word',
            'pptx' => 'slide',
            'ppt' => 'slide',
        ];

        return $types[$fileType] ?? 'word';
    }

    public function handleCallback($documentId, $data)
    {
        $document = OnlyOfficeDocument::findOrFail($documentId);

        $status = $data['status'] ?? 0;

        switch ($status) {
            case 2:
            case 3:
            case 6:
                return $this->saveDocument($document, $data);

            case 1:
                $document->update(['status' => 'editing']);
                break;

            case 4:
                $document->update(['status' => 'saved']);
                break;
        }

        return ['error' => 0];
    }

    protected function saveDocument($document, $data)
    {
        if (!isset($data['url'])) {
            return ['error' => 1];
        }

        try {
            $fileContent = file_get_contents($data['url']);

            if ($fileContent === false) {
                return ['error' => 1];
            }

            $this->createVersion($document);

            file_put_contents($document->full_path, $fileContent);

            $document->update([
                'status' => 'saved',
                'last_modified' => now(),
                'key' => Str::random(32)
            ]);

            return ['error' => 0];
        } catch (\Exception $e) {
            Log::error('OnlyOffice save error: ' . $e->getMessage());
            return ['error' => 1];
        }
    }

    protected function createVersion($document)
    {
        $versionPath = 'onlyoffice/versions/' . $document->id . '/v' . time() . '.' . $document->file_type;

        Storage::copy($document->file_path, $versionPath);

        DocumentVersion::create([
            'document_id' => $document->id,
            'version' => $document->versions()->count() + 1,
            'file_path' => $versionPath,
            'created_by' => auth()->id() ?? $document->user_id,
        ]);
    }
}
