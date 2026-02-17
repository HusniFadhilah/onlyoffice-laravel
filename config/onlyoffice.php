<?php

return [
    'server_url' => env('ONLYOFFICE_SERVER', 'https://onlyoffice-server.husnifd.my.id'),

    // ✅ URL Laravel yang bisa diakses oleh OnlyOffice Document Server
    // beda dengan APP_URL kalau APP_URL masih localhost
    'app_url' => env('ONLYOFFICE_APP_URL', env('APP_URL')),

    'jwt_secret' => env('ONLYOFFICE_JWT_SECRET', ''),
    'jwt_enabled' => filter_var(env('ONLYOFFICE_JWT_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'jwt_header' => env('ONLYOFFICE_JWT_HEADER', 'Authorization'),

    'callback_timeout' => 30,

    'allowed_extensions' => ['docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt', 'pdf', 'txt', 'csv'],

    'storage_path' => storage_path('app/onlyoffice'),
];
