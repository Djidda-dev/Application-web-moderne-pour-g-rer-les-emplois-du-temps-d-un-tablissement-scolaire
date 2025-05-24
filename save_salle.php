<?php
require_once 'database.php';

try {
    if (empty($_POST['nom_salle']) || empty($_POST['description'])) {
        throw new Exception("Tous les champs doivent être remplis");
    }

    $nom = trim($_POST['nom_salle']);
    $desc = trim($_POST['description']);

    // Vérification existence
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM salles WHERE NOM_SALLE = ?");
    $stmt->execute([$nom]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Cette salle existe déjà");
    }

    // Insertion
    $stmt = $pdo->prepare("INSERT INTO salles (NOM_SALLE, DESCRIPTION) VALUES (?, ?)");
    $stmt->execute([$nom, $desc]);
    $newId = $pdo->lastInsertId();

    // Retour HTML
    echo '<li class="list-group-item d-flex justify-content-between align-items-center" data-id="'.$newId.'">
            <div>
                '.htmlspecialchars($nom).'
                <div class="text-muted small">'.htmlspecialchars($desc).'</div>
            </div>
            <div>
                <span class="badge bg-primary rounded-pill me-2">ID: '.$newId.'</span>
                <button class="btn btn-warning btn-sm btn-edit"
                        data-id="'.$newId.'"
                        data-nom="'.htmlspecialchars($nom).'"
                        data-description="'.htmlspecialchars($desc).'">
                    Modifier
                </button>
                <button class="btn btn-danger btn-sm btn-delete" data-id="'.$newId.'">Supprimer</button>
            </div>
          </li>';

} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}