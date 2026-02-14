<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Documents - OnlyOffice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            margin-bottom: 30px;
            border-radius: 10px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .document-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .document-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .document-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .document-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .document-meta {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .document-actions {
            display: flex;
            gap: 10px;
        }

        .document-actions .btn {
            flex: 1;
            justify-content: center;
            padding: 8px 16px;
            font-size: 13px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
        }

        .modal-header {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-help {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📁 My Documents</h1>
            <p>Manage your spreadsheets, documents, and presentations</p>
            <div class="header-actions">
                <div>Total: {{ $documents->total() }} documents</div>
                <button class="btn" onclick="openUploadModal()">
                    📤 Upload Document
                </button>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                ✗ {{ session('error') }}
            </div>
        @endif

        @if($documents->count() > 0)
            <div class="documents-grid">
                @foreach($documents as $doc)
                    <div class="document-card">
                        <div class="document-icon">
                            @if($doc->file_type === 'xlsx' || $doc->file_type === 'xls')
                                📊
                            @elseif($doc->file_type === 'docx' || $doc->file_type === 'doc')
                                📄
                            @elseif($doc->file_type === 'pptx' || $doc->file_type === 'ppt')
                                📽️
                            @else
                                📎
                            @endif
                        </div>
                        <div class="document-title">{{ $doc->title }}</div>
                        <div class="document-meta">
                            {{ strtoupper($doc->file_type) }} •
                            {{ number_format($doc->file_size / 1024, 1) }} KB<br>
                            Updated: {{ $doc->updated_at->diffForHumans() }}
                        </div>
                        <div class="document-actions">
                            <a href="{{ route('onlyoffice.editor', $doc) }}" class="btn btn-primary">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('onlyoffice.destroy', $doc) }}" method="POST" style="flex: 1;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Delete this document?')"
                                        style="width: 100%;">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 30px;">
                {{ $documents->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h2>No documents yet</h2>
                <p>Upload your first document to get started</p>
                <button class="btn btn-primary" onclick="openUploadModal()" style="margin-top: 20px;">
                    📤 Upload Document
                </button>
            </div>
        @endif
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Upload Document</div>
            <form action="{{ route('onlyoffice.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Document Title (Optional)</label>
                    <input type="text" name="title" class="form-input" placeholder="Leave empty to use filename">
                </div>
                <div class="form-group">
                    <label class="form-label">Select File</label>
                    <input type="file" name="file" required class="form-input">
                    <div class="form-help">
                        Supported: Excel (.xlsx, .xls), Word (.docx, .doc), PowerPoint (.pptx, .ppt), CSV, TXT
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn" onclick="closeUploadModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUploadModal() {
            document.getElementById('uploadModal').classList.add('active');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('uploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUploadModal();
            }
        });
    </script>
</body>
</html>
