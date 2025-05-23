<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['id_module'])) {
    try {
        $pdo->beginTransaction();
        
        $id = $_POST['id_module'];
        $stmt = $pdo->prepare("DELETE FROM modules WHERE ID_MODULE = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Module introuvable");
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