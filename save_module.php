<?php
require_once 'database.php';

try {
    if (empty($_POST['id_module']) || empty($_POST['nom_module']) || empty($_POST['description']) || empty($_POST['volume_horaire'])) {
        throw new Exception("Tous les champs doivent être remplis");
    }

    $id = trim($_POST['id_module']);
    $nom = trim($_POST['nom_module']);
    $desc = trim($_POST['description']);
    $volume = (int)$_POST['volume_horaire'];

    // Vérification existence
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE ID_MODULE = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ce module existe déjà");
    }

    // Insertion
    $stmt = $pdo->prepare("INSERT INTO modules (ID_MODULE, NOM_MODULE, DESCRIPTION, volume_horaire, volume_horaire_restant) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $nom, $desc, $volume, $volume]);

    // Retour HTML
    echo '<li class="list-group-item d-flex justify-content-between align-items-center" data-id="'.$id.'">
            <div>
                <strong>'.htmlspecialchars($nom).'</strong>
                <div class="text-muted small">'.htmlspecialchars($desc).'</div>
                <div class="text-info mt-1">
                    Volume horaire: '.$volume.'h (Reste: '.$volume.'h)
                </div>
            </div>
            <div>
                <span class="badge bg-primary rounded-pill me-2">ID: '.htmlspecialchars($id).'</span>
                <button class="btn btn-warning btn-sm btn-edit"
                        data-id="'.$id.'"
                        data-nom="'.htmlspecialchars($nom).'"
                        data-description="'.htmlspecialchars($desc).'"
                        data-volume="'.$volume.'">
                    Modifier
                </button>
                <button class="btn btn-danger btn-sm btn-delete" data-id="'.$id.'">Supprimer</button>
            </div>
          </li>';

} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}