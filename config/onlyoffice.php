<?php

return [
    // OnlyOffice Document Server URL
    'server_url' => env('ONLYOFFICE_SERVER', 'https://onlyoffice-server.husnifd.my.id'),

    // JWT Settings (untuk security)
    'jwt_secret' => env('ONLYOFFICE_JWT_SECRET', ''),
    'jwt_enabled' => env('ONLYOFFICE_JWT_ENABLED', false),
    'jwt_header' => env('ONLYOFFICE_JWT_HEADER', 'Authorization'),

    // Callback settings
    'callback_timeout' => 30,

    // Allowed file types
    'allowed_extensions' => [
        'docx',
        'xlsx',
        'pptx',
        'doc',
        'xls',
        'ppt',
        'pdf',
        'txt',
        'csv'
    ],

    // Storage path
    'storage_path' => storage_path('app/onlyoffice'),
];
