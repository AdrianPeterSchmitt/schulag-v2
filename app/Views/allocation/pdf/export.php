<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>AG-Zuteilung <?= esc($schoolyear) ?></title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
        }
        h1 {
            color: #333;
            font-size: 18pt;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        h2 {
            color: #666;
            font-size: 14pt;
            margin-top: 20px;
            margin-bottom: 10px;
            background-color: #f0f0f0;
            padding: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #4A5568;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .meta {
            font-size: 9pt;
            color: #666;
            margin-top: 10px;
        }
        .stats {
            margin: 20px 0;
            padding: 10px;
            background-color: #e8f4f8;
            border-left: 4px solid #3182ce;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .ag-section {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AG-Zuteilung <?= esc($schoolyear) ?></h1>
        <p class="meta">Erstellt am: <?= esc($generatedAt) ?></p>
    </div>

    <?php if (isset($run) && !empty($run)): ?>
    <div class="stats">
        <h3>Run-Statistiken (ID: <?= esc($run['id'] ?? 'N/A') ?>)</h3>
        <p>
            <strong>Schüler gesamt:</strong> <?= esc($run['total_students'] ?? 0) ?><br>
            <strong>Zugewiesen:</strong> <?= esc($run['total_assigned'] ?? 0) ?><br>
            <strong>Rest-Warteliste:</strong> <?= esc($run['total_rest_waitlist'] ?? 0) ?><br>
            <strong>AGs:</strong> <?= esc($run['total_offers'] ?? 0) ?><br>
            <strong>Gesamt-Kapazität:</strong> <?= esc($run['total_capacity'] ?? 0) ?>
        </p>
    </div>
    <?php endif; ?>

    <h2>Zuteilungen nach AGs</h2>

    <?php foreach ($offers as $offer): ?>
        <?php
        $offerAllocations = $allocationsByOffer[$offer['id']] ?? [];
        $studentCount = count($offerAllocations);
        ?>
        <div class="ag-section">
            <h3>
                <?= esc($offer['club']['titel'] ?? 'Unbekannte AG') ?> 
                (<?= $studentCount ?> / <?= esc($offer['capacity']) ?> Plätze)
            </h3>
            
            <?php if ($offer['club']['beschreibung_kurz']): ?>
                <p style="font-size: 10pt; color: #666; margin: 5px 0 15px 0;">
                    <?= esc($offer['club']['beschreibung_kurz']) ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($offerAllocations)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nr.</th>
                            <th>Schüler-ID</th>
                            <th>Status</th>
                            <th>Zugewiesen am</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offerAllocations as $index => $allocation): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= esc($allocation['student_id']) ?></td>
                                <td><?= esc($allocation['status']) ?></td>
                                <td><?= date('d.m.Y', strtotime($allocation['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #999; font-style: italic;">Keine Zuteilungen</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="footer">
        SchulAG v2.0 - AG-Verwaltungssystem - Seite <span class="pagenum"></span>
    </div>
</body>
</html>

