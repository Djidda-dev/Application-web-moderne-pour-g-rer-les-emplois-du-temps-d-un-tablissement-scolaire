<?php
require_once 'database.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_module'])) {
        throw new Exception("Données invalides");
    }

    $id = $_POST['id_module'];
    $nom = trim($_POST['nom_module']);
    $desc = trim($_POST['description']);
    $volume = (int)$_POST['volume_horaire'];

    $pdo->beginTransaction();
    
    // Vérification conflit de nom
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE NOM_MODULE = ? AND ID_MODULE != ?");
    $stmt->execute([$nom, $id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ce nom de module est déjà utilisé");
    }

    // Calcul du nouveau volume restant
    $stmt = $pdo->prepare("SELECT volume_horaire, volume_horaire_restant FROM modules WHERE ID_MODULE = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();
    
    $diff = $volume - $current['volume_horaire'];
    $nouveau_restant = $current['volume_horaire_restant'] + $diff;

    // Validation du volume restant
    if ($nouveau_restant < 0) {
        throw new Exception("Le volume horaire ne peut pas être inférieur aux heures déjà utilisées");
    }

    // Mise à jour
    $stmt = $pdo->prepare("UPDATE modules SET 
        NOM_MODULE = ?,
        DESCRIPTION = ?,
        volume_horaire = ?,
        volume_horaire_restant = ?
        WHERE ID_MODULE = ?");
    
    $stmt->execute([$nom, $desc, $volume, $nouveau_restant, $id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("Aucune modification effectuée");
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Module mis à jour avec succès'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}