<?php
require_once 'database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_prof'])) {
        throw new Exception("Données invalides");
    }

    $id = $_POST['id_prof'];
    $nom = trim($_POST['nom_prof']);
    $tel = trim($_POST['tel']);

    $pdo->beginTransaction();
    
    // Vérification unicité téléphone
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM professeurs WHERE TEL = ? AND ID_PROF != ?");
    $stmt->execute([$tel, $id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ce numéro est déjà utilisé");
    }

    $stmt = $pdo->prepare("UPDATE professeurs SET 
        NOM_PROF = ?,
        TEL = ?
        WHERE ID_PROF = ?");
    $stmt->execute([$nom, $tel, $id]);
    
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