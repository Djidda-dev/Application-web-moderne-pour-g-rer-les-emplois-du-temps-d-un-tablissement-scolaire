<?php
require_once 'database.php';

try {
    if (empty($_POST['nom_prof']) || empty($_POST['tel'])) {
        throw new Exception("Tous les champs doivent être remplis");
    }

    $nom = trim($_POST['nom_prof']);
    $tel = trim($_POST['tel']);

    // Vérification unicité téléphone
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM professeurs WHERE TEL = ?");
    $stmt->execute([$tel]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ce numéro existe déjà");
    }

    // Insertion
    $stmt = $pdo->prepare("INSERT INTO professeurs (NOM_PROF, TEL) VALUES (?, ?)");
    $stmt->execute([$nom, $tel]);
    $newId = $pdo->lastInsertId();

    // Retour HTML
    echo '<li class="list-group-item d-flex justify-content-between align-items-center" data-id="'.$newId.'">
            <div>
                '.htmlspecialchars($nom).'
                <div class="text-muted small">'.htmlspecialchars($tel).'</div>
            </div>
            <div>
                <button class="btn btn-warning btn-sm btn-edit"
                        data-id="'.$newId.'"
                        data-nom="'.htmlspecialchars($nom).'"
                        data-tel="'.htmlspecialchars($tel).'">
                    Modifier
                </button>
                <button class="btn btn-danger btn-sm btn-delete" data-id="'.$newId.'">Supprimer</button>
            </div>
          </li>';

} catch (Exception $e) {
    http_response_code(400);
    echo $e->getMessage();
}