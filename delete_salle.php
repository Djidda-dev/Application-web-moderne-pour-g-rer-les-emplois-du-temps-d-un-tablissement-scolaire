<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['id_salle'])) {
    try {
        $pdo->beginTransaction();
        
        $id = $_POST['id_salle'];
        $stmt = $pdo->prepare("DELETE FROM salles WHERE ID_SALLE = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Salle introuvable");
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