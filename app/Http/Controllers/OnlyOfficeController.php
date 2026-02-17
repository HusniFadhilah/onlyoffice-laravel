<?php

namespace App\Http\Controllers;

use App\Models\OnlyOfficeDocument;
use App\Services\OnlyOfficeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OnlyOfficeController extends Controller
{
    protected $onlyOfficeService;

    public function __construct(OnlyOfficeService $onlyOfficeService)
    {
        $this->onlyOfficeService = $onlyOfficeService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'title' => 'nullable|string|max:255'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $allowed = ['xlsx', 'xls', 'docx', 'doc', 'pptx', 'ppt', 'csv', 'txt'];
        if (!in_array($extension, $allowed)) {
            return back()->with('error', 'File type not supported');
        }

        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('onlyoffice/documents', $filename, 'public');

        $document = OnlyOfficeDocument::create([
            'user_id' => Auth::id(),
            'title' => $request->title ?? $file->getClientOriginalName(),
            'filename' => $filename,
            'file_path' => $path,
            'file_type' => $extension,
            'file_size' => $file->getSize()
        ]);

        return redirect()->route('onlyoffice.editor', $document);
    }

    public function editor(OnlyOfficeDocument $document)
    {
        $config = $this->onlyOfficeService->getEditorConfig(
            $document,
            Auth::id()
        );

        return view('onlyoffice.editor', [
            'document' => $document,
            'config' => $config,
            'serverUrl' => config('onlyoffice.server_url')
        ]);
    }

    public function download(OnlyOfficeDocument $document)
    {
        Log::info('OO DOWNLOAD HIT', [
            'ip' => request()->ip(),
            'ua' => request()->userAgent(),
            'method' => request()->method(),
            'headers' => [
                'range' => request()->header('Range'),
                'auth' => request()->header('Authorization'),
                'host' => request()->header('Host'),
            ],
        ]);

        return response()->file($document->full_path, [
            'Content-Disposition' => 'inline; filename="' . $document->filename . '"',
            'Cache-Control' => 'public',
        ]);
    }

    public function callback(Request $request, $documentId)
    {
        Log::info('OO CALLBACK HIT', [
            'ip' => $request->ip(),
            'ua' => $request->userAgent(),
            'body' => $request->all(),
            'headers' => [
                'auth' => $request->header('Authorization'),
                'host' => $request->header('Host'),
            ],
        ]);
        $data = $request->all();

        Log::info('OnlyOffice callback', $data);

        $result = $this->onlyOfficeService->handleCallback($documentId, $data);

        return response()->json($result);
    }

    public function index()
    {
        $documents = OnlyOfficeDocument::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('onlyoffice.index', compact('documents'));
    }

    public function destroy(OnlyOfficeDocument $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::delete($document->file_path);
        $document->delete();

        return redirect()->route('onlyoffice.index')->with('success', 'Document deleted');
    }

    protected function canAccess($document, $userId)
    {
        if ($document->user_id === $userId) {
            return true;
        }

        return $document->shares()
            ->where('user_id', $userId)
            ->exists();
    }
}
