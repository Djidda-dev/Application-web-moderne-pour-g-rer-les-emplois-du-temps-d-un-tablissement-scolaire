<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['id_filiere'])) {
    try {
        $pdo->beginTransaction();
        
        $id = $_POST['id_filiere'];
        $stmt = $pdo->prepare("DELETE FROM filieres WHERE ID_FILIERE = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Filière introuvable");
        }
        
        $pdo->commit();
        http_response_code(200);
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}