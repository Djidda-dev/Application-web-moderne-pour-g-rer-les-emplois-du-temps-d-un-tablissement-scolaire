<?php
require_once 'database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_module'])) {
        throw new Exception("Données invalides");
    }

    $id = $_POST['id_module'];
    $nom = trim($_POST['nom_module']);
    $desc = trim($_POST['description']);

    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("UPDATE modules SET 
        NOM_MODULE = ?,
        DESCRIPTION = ?
        WHERE ID_MODULE = ?");
    $stmt->execute([$nom, $desc, $id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("Aucune modification effectuée");
    }
    
    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}