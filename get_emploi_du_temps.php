<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gestion_etudiants', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $classeId = $_GET['classe'] ?? '';
    if (empty($classeId)) throw new Exception('Aucune classe sélectionnée');

    $stmt = $pdo->prepare("SELECT NIVEAU FROM CLASSES WHERE ID_CLASSE = ?");
    $stmt->execute([$classeId]);
    $classe = $stmt->fetchColumn();
    if (!$classe) throw new Exception('Classe non valide');

    $query = "SELECT c.JOUR, c.HEURE_DEBUT, c.HEURE_FIN, s.NOM_SALLE, p.NOM_PROF, m.NOM_MODULE, m.ID_MODULE 
              FROM COURS c
              JOIN PROFESSEURS p ON c.ID_PROF = p.ID_PROF
              JOIN MODULES m ON c.ID_MODULE = m.ID_MODULE
              JOIN SALLES s ON c.ID_SALLE = s.ID_SALLE
              WHERE c.ID_CLASSE = ?
              ORDER BY FIELD(c.JOUR, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), c.HEURE_DEBUT";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$classeId]);
    $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organisation par jour
    $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    $emploiDuTemps = [];
    foreach ($jours as $jour) $emploiDuTemps[$jour] = [];
    foreach ($cours as $c) $emploiDuTemps[$c['JOUR']][] = $c;

    // Modules et heures
    $moduleIds = array_unique(array_column($cours, 'ID_MODULE'));
    $volumesModules = [];
    if (!empty($moduleIds)) {
        $in = str_repeat('?,', count($moduleIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT ID_MODULE, volume_horaire FROM modules WHERE ID_MODULE IN ($in)");
        $stmt->execute($moduleIds);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $volumesModules[$row['ID_MODULE']] = $row['volume_horaire'];
        }
    }

    // Calcul du cumul progressif des heures pour chaque module
    $cumulHeures = [];
    foreach ($jours as $jour) {
        if (!empty($emploiDuTemps[$jour])) {
            foreach ($emploiDuTemps[$jour] as $idx => $c) {
                $idModule = $c['ID_MODULE'];
                $duree = (strtotime($c['HEURE_FIN']) - strtotime($c['HEURE_DEBUT'])) / 3600;
                if (!isset($cumulHeures[$idModule])) $cumulHeures[$idModule] = 0;
                $cumulHeures[$idModule] += $duree;
                // On ajoute le cumul dans la séance pour l'affichage
                $emploiDuTemps[$jour][$idx]['CUMUL'] = $cumulHeures[$idModule];
            }
        }
    }
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emploi du temps - <?= htmlspecialchars($classe) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .edt-header {
            background: linear-gradient(90deg, #0d6efd 60%, #6610f2 100%);
            color: #fff;
            padding: 30px 0 20px 0;
            margin-bottom: 30px;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        }
        .edt-table th, .edt-table td {
            text-align: center;
            vertical-align: top;
            background: #fff;
            border: none;
        }
        .edt-table th {
            background: #e9ecef;
            font-size: 1.1em;
            letter-spacing: 1px;
            border-top: 2px solid #0d6efd;
        }
        .edt-case {
            background: linear-gradient(120deg, #e0f7fa 60%, #b2ebf2 100%);
            border-left: 5px solid #0d6efd;
            border-radius: 12px;
            margin-bottom: 18px;
            padding: 12px 10px 10px 18px;
            box-shadow: 0 2px 8px rgba(13,110,253,0.07);
            text-align: left;
            min-width: 180px;
            min-height: 90px;
            transition: box-shadow 0.2s;
        }
        .edt-case:hover {
            box-shadow: 0 4px 16px rgba(13,110,253,0.15);
        }
        .edt-horaire {
            color: #6610f2;
            font-weight: bold;
            font-size: 1.05em;
        }
        .edt-module {
            font-weight: bold;
            font-size: 1.08em;
            color: #0d6efd;
            margin-top: 2px;
        }
        .edt-prof {
            font-size: 0.98em;
            color: #333;
            margin-bottom: 2px;
        }
        .edt-volume {
            font-size: 0.95em;
            color: #198754;
            font-weight: bold;
        }
        .edt-salle {
            font-size: 0.90em;
            color: #888;
        }
        @media (max-width: 991px) {
            .edt-table th, .edt-table td { font-size: 0.95em; }
            .edt-case { min-width: 120px; font-size: 0.92em; }
        }
        @media (max-width: 767px) {
            .edt-table th, .edt-table td { font-size: 0.90em; }
            .edt-case { min-width: 80px; font-size: 0.88em; }
        }
    </style>
</head>
<body>
    <div class="edt-header text-center shadow-sm mb-4">
        <h1 class="mb-1">Emploi du temps</h1>
        <h4 class="mb-0"><?= htmlspecialchars($classe) ?></h4>
    </div>
    <div class="container pb-5">
        <?php if (!empty($cours)) : ?>
            <div class="table-responsive">
                <table class="table edt-table align-middle">
                    <thead>
                        <tr>
                            <?php foreach ($jours as $jour): ?>
                                <th><?= strtoupper($jour) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($jours as $jour): ?>
                                <td>
                                    <?php if (!empty($emploiDuTemps[$jour])): ?>
                                        <?php foreach ($emploiDuTemps[$jour] as $c): ?>
                                            <div class="edt-case mb-3">
                                                <div class="edt-horaire"><?= date('H\hi', strtotime($c['HEURE_DEBUT'])) ?> - <?= date('H\hi', strtotime($c['HEURE_FIN'])) ?></div>
                                                <div class="edt-module"><?= htmlspecialchars($c['NOM_MODULE']) ?></div>
                                                <div class="edt-prof"><?= htmlspecialchars($c['NOM_PROF']) ?></div>
                                                <div class="edt-volume">
                                                    <?php
                                                        $idModule = $c['ID_MODULE'];
                                                        $cumul = isset($c['CUMUL']) ? $c['CUMUL'] : 0;
                                                        if (isset($volumesModules[$idModule])) {
                                                            $heures = floor($cumul);
                                                            $minutes = round(($cumul - $heures) * 60);
                                                            $cumulAff = $heures . 'H' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                                            echo "{$cumulAff}/{$volumesModules[$idModule]}H";
                                                        }
                                                    ?>
                                                </div>
                                                <div class="edt-salle"><?= htmlspecialchars($c['NOM_SALLE']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">Aucun cours programmé pour cette classe</div>
        <?php endif; ?>
    </div>
</body>
</html>
