<?php require_once 'database.php'; 

try { 
    $salles = $pdo->query("SELECT * FROM salles ORDER BY NOM_SALLE ASC")->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) { 
    die("Erreur : " . $e->getMessage()); 
} 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Salles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Gestion des Salles</h1>

    <div id="error-message" class="alert alert-danger d-none"></div>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter une salle</div>
        <div class="card-body">
            <form id="form-salle">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="nom_salle">Nom de la salle :</label>
                        <input type="text" name="nom_salle" id="nom_salle" class="form-control" required maxlength="100">
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

    <!-- Liste des salles -->
    <div class="card">
        <div class="card-header">Liste des Salles</div>
        <div class="card-body">
            <ul id="liste-salles" class="list-group">
                <?php foreach ($salles as $salle) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $salle['ID_SALLE'] ?>">
                        <div>
                            <?= htmlspecialchars($salle['NOM_SALLE']) ?>
                            <div class="text-muted small"><?= htmlspecialchars($salle['DESCRIPTION']) ?></div>
                        </div>
                        <div>
                            <span class="badge bg-primary rounded-pill me-2">ID: <?= $salle['ID_SALLE'] ?></span>
                            <button class="btn btn-warning btn-sm btn-edit"
                                    data-id="<?= $salle['ID_SALLE'] ?>"
                                    data-nom="<?= htmlspecialchars($salle['NOM_SALLE']) ?>"
                                    data-description="<?= htmlspecialchars($salle['DESCRIPTION']) ?>">
                                Modifier
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $salle['ID_SALLE'] ?>">Supprimer</button>
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
                <h5 class="modal-title">Modifier la salle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-salle">
                    <input type="hidden" name="id_salle" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Nom de la salle :</label>
                        <input type="text" name="nom_salle" id="edit-nom" class="form-control" required>
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
    // Gestion de l'ajout
    $("#form-salle").submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "save_salle.php",
            data: $(this).serialize(),
            success: (response) => {
                const $newSalle = $(response);
                $("#liste-salles").append($newSalle);
                attachEventHandlers();
                $("#form-salle")[0].reset();
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
            
            if(confirm("Supprimer définitivement cette salle ?")) {
                $.ajax({
                    type: "POST",
                    url: "delete_salle.php",
                    data: { id_salle: id },
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
            url: "update_salle.php",
            data: $("#form-edit-salle").serialize(),
            success: (response) => {
                const formData = $("#form-edit-salle").serializeArray().reduce((obj, item) => {
                    obj[item.name] = item.value;
                    return obj;
                }, {});
                
                $(`li[data-id="${formData.id_salle}"]`)
                    .find("div:first")
                    .html(`
                        ${formData.nom_salle}
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