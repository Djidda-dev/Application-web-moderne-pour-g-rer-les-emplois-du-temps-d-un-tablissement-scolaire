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

    // Récupération des étudiants
    $stmt = $pdo->prepare("SELECT NUM_INSCRIPTION, NOM_ET, PRENOM_ET FROM ETUDIANTS WHERE ID_FILIERE = (SELECT ID_FILIERE FROM CLASSES WHERE ID_CLASSE = ?)");
    $stmt->execute([$classeId]);
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des modules
    $stmt = $pdo->prepare("SELECT m.ID_MODULE, m.NOM_MODULE FROM MODULES m 
                           JOIN COURS c ON c.ID_MODULE = m.ID_MODULE 
                           WHERE c.ID_CLASSE = ?");
    $stmt->execute([$classeId]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Informations de la classe - <?= htmlspecialchars($classe) ?></title>
</head>
<body>
    <h2>Classe : <?= htmlspecialchars($classe) ?></h2>

    <!-- Liste des étudiants -->
    <h3>Étudiants</h3>
    <?php if (!empty($etudiants)) : ?>
        <ul class="list-group">
            <?php foreach ($etudiants as $etudiant) : ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($etudiant['NOM_ET']) . " " . htmlspecialchars($etudiant['PRENOM_ET']) ?> 
                    <span class="badge bg-primary"><?= htmlspecialchars($etudiant['NUM_INSCRIPTION']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p class="alert alert-info">Aucun étudiant trouvé.</p>
    <?php endif; ?>

    <!-- Liste des modules -->
    <h3>Modules enseignés</h3>
    <?php if (!empty($modules)) : ?>
        <ul class="list-group">
            <?php foreach ($modules as $module) : ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($module['NOM_MODULE']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p class="alert alert-info">Aucun module trouvé.</p>
    <?php endif; ?>
</body>
</html>
