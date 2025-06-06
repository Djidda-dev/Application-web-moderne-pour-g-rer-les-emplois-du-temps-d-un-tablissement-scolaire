<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['id_prof'])) {
    try {
        $pdo->beginTransaction();
        
        $id = $_POST['id_prof'];
        $stmt = $pdo->prepare("DELETE FROM professeurs WHERE ID_PROF = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Professeur introuvable");
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