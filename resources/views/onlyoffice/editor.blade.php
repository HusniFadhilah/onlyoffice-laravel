<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $document->title }} - OnlyOffice Editor</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        #editor {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        #loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-size: 18px;
            color: #666;
            z-index: 9999;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

    </style>
</head>
<body>
    <div id="loading">
        <div class="spinner"></div>
        <p>Loading OnlyOffice Editor...</p>
        <p style="font-size: 14px; color: #999;">Please wait while we prepare your document</p>
    </div>

    <div id="editor"></div>
    {{ $serverUrl }}
    <script src="{{ rtrim($serverUrl, '/') }}/web-apps/apps/api/documents/api.js"></script>

    <script>
        const config = @json($config);
        console.log("ONLYOFFICE CONFIG:", config);
        console.log(config.document.url, config.editorConfig.callbackUrl);
        config.events = {
            onReady: function() {
                console.log('OnlyOffice Editor Ready');
                document.getElementById('loading').style.display = 'none';
            },

            onError: function(event) {
                console.error('OnlyOffice Error:', event);

                var errorData = '';
                if (event && event.data) {
                    errorData = JSON.stringify(event.data);
                } else {
                    errorData = JSON.stringify(event);
                }

                document.getElementById('loading').innerHTML =
                    '<div style="color: #ef4444; padding: 20px;">' +
                    '<h3>❌ Error Loading Document</h3>' +
                    '<p>Error: ' + errorData + '</p>' +
                    '<p><a href="{{ route("onlyoffice.index") }}" style="color: #667eea;">← Back to Documents</a></p>' +
                    '</div>';
            },

            onDocumentStateChange: function(event) {
                if (event && event.data) {
                    console.log('Document modified');
                }
            },

            onRequestEditRights: function() {
                console.log('Edit rights requested');
            }
        };

        window.docEditor = new DocsAPI.DocEditor("editor", config);

    </script>
</body>
</html>
