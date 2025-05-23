<?php
require_once 'database.php';

try {
    if (empty($_POST['id_module']) || empty($_POST['nom_module']) || empty($_POST['description'])) {
        throw new Exception("Tous les champs doivent être remplis");
    }

    $id_module = trim($_POST['id_module']);
    $nom_module = trim($_POST['nom_module']);
    $description = trim($_POST['description']);

    // Vérification existence
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE ID_MODULE = ?");
    $stmt->execute([$id_module]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ce module existe déjà");
    }

    // Insertion
    $stmt = $pdo->prepare("INSERT INTO modules (ID_MODULE, NOM_MODULE, DESCRIPTION) VALUES (?, ?, ?)");
    $stmt->execute([$id_module, $nom_module, $description]);

    // Retour HTML
    echo '<li class="list-group-item d-flex justify-content-between align-items-center" data-id="'.$id_module.'">
            <div>
                '.htmlspecialchars($nom_module).'
                <div class="text-muted small">'.htmlspecialchars($description).'</div>
            </div>
            <div>
                <span class="badge bg-primary rounded-pill me-2">ID: '.htmlspecialchars($id_module).'</span>
                <button class="btn btn-warning btn-sm btn-edit"
                        data-id="'.$id_module.'"
                        data-nom="'.htmlspecialchars($nom_module).'"
                        data-description="'.htmlspecialchars($description).'">
                    Modifier
                </button>
                <button class="btn btn-danger btn-sm btn-delete" data-id="'.$id_module.'">Supprimer</button>
            </div>
          </li>';

} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}