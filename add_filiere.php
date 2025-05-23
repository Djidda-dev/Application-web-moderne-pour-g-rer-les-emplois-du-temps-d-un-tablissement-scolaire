<?php require_once 'database.php'; 

try { 
    $filieres = $pdo->query("SELECT * FROM filieres ORDER BY ID_FILIERE ASC")->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) { 
    die("Erreur : " . $e->getMessage()); 
} 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Filières</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Gestion des Filières</h1>

    <div id="error-message" class="alert alert-danger d-none"></div>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter une nouvelle filière</div>
        <div class="card-body">
            <form id="form-filiere">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="nom_filiere">Nom de la filière :</label>
                        <input type="text" name="nom_filiere" id="nom_filiere" class="form-control" required maxlength="100">
                    </div>
                    <div class="col-md-6">
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

    <!-- Liste des filières -->
    <div class="card">
        <div class="card-header">Liste des filières</div>
        <div class="card-body">
            <ul id="liste-filieres" class="list-group">
                <?php foreach ($filieres as $filiere) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $filiere['ID_FILIERE'] ?>">
                        <div>
                            <?= htmlspecialchars($filiere['NOM_FILIERE']) ?>
                            <div class="text-muted small"><?= htmlspecialchars($filiere['DESCRIPTION']) ?></div>
                        </div>
                        <div>
                            <span class="badge bg-primary rounded-pill me-2">ID: <?= $filiere['ID_FILIERE'] ?></span>
                            <button class="btn btn-warning btn-sm btn-edit"
                                    data-id="<?= $filiere['ID_FILIERE'] ?>"
                                    data-nom="<?= htmlspecialchars($filiere['NOM_FILIERE']) ?>"
                                    data-description="<?= htmlspecialchars($filiere['DESCRIPTION']) ?>">
                                Modifier
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $filiere['ID_FILIERE'] ?>">Supprimer</button>
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
                <h5 class="modal-title">Modifier la filière</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-filiere">
                    <input type="hidden" name="id_filiere" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Nom de la filière :</label>
                        <input type="text" name="nom_filiere" id="edit-nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description :</label>
                        <input type="text" name="description" id="edit-description" class="form-control" required>
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
    // Ajout d'une filière
    $("#form-filiere").submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "save_filiere.php",
            data: $(this).serialize(),
            success: (response) => {
                const $newFiliere = $(response);
                $("#liste-filieres").append($newFiliere);
                attachEventHandlers();
                $("#form-filiere")[0].reset();
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
            $("#edit-description").val(data.description);
            $("#editModal").modal("show");
        });

        // Suppression
        $(".btn-delete").off('click').click(function() {
            const id = $(this).data("id");
            const $li = $(this).closest("li");
            
            if(confirm("Supprimer définitivement cette filière ?")) {
                $.ajax({
                    type: "POST",
                    url: "delete_filiere.php",
                    data: { id_filiere: id },
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
        $.ajax({
            type: "POST",
            url: "update_filiere.php",
            data: $("#form-edit-filiere").serialize(),
            success: (response) => {
                const formData = $("#form-edit-filiere").serializeArray().reduce((obj, item) => {
                    obj[item.name] = item.value;
                    return obj;
                }, {});
                
                $(`li[data-id="${formData.id_filiere}"]`)
                    .find("div:first")
                    .html(`
                        ${formData.nom_filiere}
                        <div class="text-muted small">${formData.description}</div>
                    `);
                
                $("#editModal").modal("hide");
            },
            error: (xhr) => {
                alert("Erreur : " + xhr.responseText);
            }
        });
    });

    attachEventHandlers();
});
</script>
</body>
</html>