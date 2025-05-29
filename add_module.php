<?php 
require_once 'database.php';

try { 
    $modules = $pdo->query("SELECT * FROM modules ORDER BY NOM_MODULE ASC")->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) { 
    die("Erreur : " . $e->getMessage()); 
} 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Modules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .volume-horaire {
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Gestion des Modules</h1>

    <div id="error-message" class="alert alert-danger d-none"></div>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter un module</div>
        <div class="card-body">
            <form id="form-module">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="id_module">ID du module :</label>
                        <input type="text" name="id_module" id="id_module" class="form-control" required maxlength="15">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="nom_module">Nom du module :</label>
                        <input type="text" name="nom_module" id="nom_module" class="form-control" required maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="volume_horaire">Volume horaire :</label>
                        <input type="number" name="volume_horaire" id="volume_horaire" class="form-control" min="1" max="1000" value="30" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Description :</label>
                        <input type="text" name="description" id="description" class="form-control" required maxlength="255">
                    </div>
                    <div class="col-12 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des modules -->
    <div class="card">
        <div class="card-header">Liste des Modules</div>
        <div class="card-body">
            <ul id="liste-modules" class="list-group">
                <?php foreach ($modules as $module) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $module['ID_MODULE'] ?>">
                        <div>
                            <strong><?= htmlspecialchars($module['NOM_MODULE']) ?></strong>
                            <div class="text-muted small"><?= htmlspecialchars($module['DESCRIPTION']) ?></div>
                            <div class="volume-horaire mt-1">
                                Volume horaire: <?= $module['volume_horaire'] ?>H
                                <!-- </?php if(isset($module['volume_horaire_restant'])): ?>
                                    (Reste: </?= $module['volume_horaire_restant'] ?>H)
                                </?php endif; ?> -->
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-primary rounded-pill me-2">ID: <?= $module['ID_MODULE'] ?></span>
                            <button class="btn btn-warning btn-sm btn-edit"
                                    data-id="<?= $module['ID_MODULE'] ?>"
                                    data-nom="<?= htmlspecialchars($module['NOM_MODULE']) ?>"
                                    data-desc="<?= htmlspecialchars($module['DESCRIPTION']) ?>"
                                    data-volume="<?= $module['volume_horaire'] ?>">
                                Modifier
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $module['ID_MODULE'] ?>">Supprimer</button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-module">
                    <input type="hidden" name="id_module" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Nom du module :</label>
                        <input type="text" name="nom_module" id="edit-nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Volume horaire :</label>
                        <input type="number" name="volume_horaire" id="edit-volume" class="form-control" min="1" max="1000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description :</label>
                        <input type="text" name="description" id="edit-desc" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="saveEdit">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Gestion de l'ajout
    $("#form-module").submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "save_module.php",
            data: $(this).serialize(),
            success: (response) => {
                const $newModule = $(response);
                $("#liste-modules").append($newModule);
                attachEventHandlers();
                $("#form-module")[0].reset();
                $("#volume_horaire").val(30);
            },
            error: (xhr) => {
                $("#error-message").removeClass("d-none").text(xhr.responseText);
            }
        });
    });

    // Gestion des événements
    const attachEventHandlers = () => {
        // Modification
        $(".btn-edit").off('click').click(function() {
            const data = $(this).data();
            $("#edit-id").val(data.id);
            $("#edit-nom").val(data.nom);
            $("#edit-desc").val(data.desc);
            $("#edit-volume").val(data.volume);
            $("#editModal").modal("show");
        });

        // Suppression
        $(".btn-delete").off('click').click(function() {
            const id = $(this).data("id");
            const $li = $(this).closest("li");
            
            if(confirm("Supprimer définitivement ce module ?")) {
                $.ajax({
                    type: "POST",
                    url: "delete_module.php",
                    data: { id_module: id },
                    success: () => {
                        $li.fadeOut(300, () => $li.remove());
                    },
                    error: (xhr) => {
                        alert("Erreur : " + xhr.responseText);
                    }
                });
            }
        });
    };

    // Sauvegarde des modifications
    $("#saveEdit").click(() => {
        const formData = {
            id_module: $("#edit-id").val(),
            nom_module: $("#edit-nom").val(),
            description: $("#edit-desc").val(),
            volume_horaire: $("#edit-volume").val()
        };

        $.ajax({
            type: "POST",
            url: "update_module.php",
            data: formData,
            success: (response) => {
                const data = JSON.parse(response);
                if(data.success) {
                    location.reload(); // Rechargement simple et efficace
                } else {
                    alert("Erreur: " + (data.error || "Inconnue"));
                }
            },
            error: (xhr) => {
                alert("Erreur serveur : " + xhr.responseText);
            }
        });
    });

    attachEventHandlers();
});
</script>
</body>
</html>