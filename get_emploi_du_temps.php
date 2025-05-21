<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gestion_etudiants', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Vérification et récupération de la classe
    $classeId = $_GET['classe'] ?? '';

    if (empty($classeId)) {
        throw new Exception('Aucune classe sélectionnée');
    }

    // Vérification si la classe existe
    $stmt = $pdo->prepare("SELECT NIVEAU FROM CLASSES WHERE ID_CLASSE = ?");
    $stmt->execute([$classeId]);
    $classe = $stmt->fetchColumn();

    if (!$classe) {
        throw new Exception('Classe non valide');
    }

    // Récupération des cours
    $query = "SELECT c.JOUR, c.HEURE_DEBUT, c.HEURE_FIN, s.NOM_SALLE, p.NOM_PROF, m.NOM_MODULE 
              FROM COURS c
              JOIN PROFESSEURS p ON c.ID_PROF = p.ID_PROF
              JOIN MODULES m ON c.ID_MODULE = m.ID_MODULE
              JOIN SALLES s ON c.ID_SALLE = s.ID_SALLE
              WHERE c.ID_CLASSE = ?
              ORDER BY FIELD(c.JOUR, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), 
                       c.HEURE_DEBUT";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$classeId]);
    $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organisation par jour
    $emploiDuTemps = [];
    foreach ($cours as $c) {
        $emploiDuTemps[$c['JOUR']][] = $c;
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
</head>
<body>
    <?php if (!empty($emploiDuTemps)) : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Jour</th>
                        <th>Horaire</th>
                        <th>Salle</th>
                        <th>Professeur</th>
                        <th>Module</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emploiDuTemps as $jour => $coursJour) : ?>
                        <?php foreach ($coursJour as $cours) : ?>
                            <tr>
                                <td><?= htmlspecialchars($jour) ?></td>
                                <td><?= date('H:i', strtotime($cours['HEURE_DEBUT'])) ?> - <?= date('H:i', strtotime($cours['HEURE_FIN'])) ?></td>
                                <td><?= htmlspecialchars($cours['NOM_SALLE']) ?></td>
                                <td><?= htmlspecialchars($cours['NOM_PROF']) ?></td>
                                <td><?= htmlspecialchars($cours['NOM_MODULE']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <div class="alert alert-info">Aucun cours programmé pour cette classe</div>
    <?php endif; ?>
</body>
</html>
