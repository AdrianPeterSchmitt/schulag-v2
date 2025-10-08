<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchulAG v2 - CodeIgniter 4</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 800px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        .status {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .status-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .status-item:last-child {
            border-bottom: none;
        }
        .status-label {
            font-weight: bold;
            width: 200px;
            color: #495057;
        }
        .status-value {
            color: #212529;
        }
        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        .next-steps {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
        }
        .next-steps h3 {
            color: #2196F3;
            margin-bottom: 10px;
        }
        .next-steps ul {
            margin-left: 20px;
        }
        .next-steps li {
            margin: 5px 0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 SchulAG v2</h1>
        <p style="color: #666; font-size: 1.1em; margin-bottom: 30px;">
            AG-Verwaltung für Förderschulen - Neu gebaut mit CodeIgniter 4
        </p>

        <div class="status">
            <h3 style="margin-bottom: 15px; color: #333;">📊 System-Status</h3>
            
            <div class="status-item">
                <span class="status-label">Framework:</span>
                <span class="status-value">
                    <span class="badge badge-info">CodeIgniter <?= esc($ci_version) ?></span>
                </span>
            </div>
            
            <div class="status-item">
                <span class="status-label">Environment:</span>
                <span class="status-value">
                    <span class="badge badge-<?= $environment === 'production' ? 'success' : 'info' ?>">
                        <?= esc(ucfirst($environment)) ?>
                    </span>
                </span>
            </div>
            
            <div class="status-item">
                <span class="status-label">Datenbank:</span>
                <span class="status-value">
                    <span class="badge badge-<?= $db_connected ? 'success' : 'danger' ?>">
                        <?= $db_connected ? '✓ Verbunden' : '✗ Nicht verbunden' ?>
                    </span>
                </span>
            </div>
            
            <div class="status-item">
                <span class="status-label">PHP Version:</span>
                <span class="status-value"><?= phpversion() ?></span>
            </div>
        </div>

        <div class="next-steps">
            <h3>🚀 Migration läuft!</h3>
            <p><strong>Status:</strong> Grundstruktur fertig</p>
            <ul>
                <li>✅ Datenbank-Migrationen</li>
                <li>✅ Models (8 Stück)</li>
                <li>✅ AllocationService (Losverfahren)</li>
                <li>⏳ Controller (in Arbeit)</li>
                <li>⏳ Views mit HTMX</li>
                <li>⏳ Frontend Assets</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 30px; color: #999; font-size: 0.9em;">
            <p>SchulAG v2.0 - Von Laravel zu CodeIgniter 4</p>
            <p>Optimiert für Shared Hosting</p>
        </div>
    </div>
</body>
</html>
