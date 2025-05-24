<?php require_once 'database.php'; 

try { 
    $profs = $pdo->query("SELECT * FROM professeurs ORDER BY NOM_PROF ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("Erreur : " . $e->getMessage()); 
} 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Professeurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Gestion des Professeurs</h1>

    <div id="error-message" class="alert alert-danger d-none"></div>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter un Professeur</div>
        <div class="card-body">
            <form id="form-prof">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="nom_prof">Nom du Professeur :</label>
                        <input type="text" name="nom_prof" id="nom_prof" class="form-control" required maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="tel">Téléphone :</label>
                        <input type="text" name="tel" id="tel" class="form-control" required maxlength="20">
                    </div>
                    <div class="col-12 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des professeurs -->
    <div class="card">
        <div class="card-header">Liste des Professeurs</div>
        <div class="card-body">
            <ul id="liste-profs" class="list-group">
                <?php foreach ($profs as $prof) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $prof['ID_PROF'] ?>">
                        <div>
                            <?= htmlspecialchars($prof['NOM_PROF']) ?>
                            <div class="text-muted small"><?= htmlspecialchars($prof['TEL']) ?></div>
                        </div>
                        <div>
                            <button class="btn btn-warning btn-sm btn-edit"
                                    data-id="<?= $prof['ID_PROF'] ?>"
                                    data-nom="<?= htmlspecialchars($prof['NOM_PROF']) ?>"
                                    data-tel="<?= htmlspecialchars($prof['TEL']) ?>">
                                Modifier
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $prof['ID_PROF'] ?>">Supprimer</button>
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
                <h5 class="modal-title">Modifier le professeur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-prof">
                    <input type="hidden" name="id_prof" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Nom :</label>
                        <input type="text" name="nom_prof" id="edit-nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone :</label>
                        <input type="text" name="tel" id="edit-tel" class="form-control" required>
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
    // Ajout d'un professeur
    $("#form-prof").submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "save_prof.php",
            data: $(this).serialize(),
            success: (response) => {
                const $newProf = $(response);
                $("#liste-profs").append($newProf);
                attachEventHandlers();
                $("#form-prof")[0].reset();
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
            $("#edit-tel").val(data.tel);
            $("#editModal").modal("show");
        });

        // Suppression
        $(".btn-delete").off('click').click(function() {
            const id = $(this).data("id");
            const $li = $(this).closest("li");
            
            if(confirm("Supprimer définitivement ce professeur ?")) {
                $.ajax({
                    type: "POST",
                    url: "delete_prof.php",
                    data: { id_prof: id },
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
            url: "update_prof.php",
            data: $("#form-edit-prof").serialize(),
            success: (response) => {
                const formData = $("#form-edit-prof").serializeArray().reduce((obj, item) => {
                    obj[item.name] = item.value;
                    return obj;
                }, {});
                
                $(`li[data-id="${formData.id_prof}"]`)
                    .find("div:first")
                    .html(`
                        ${formData.nom_prof}
                        <div class="text-muted small">${formData.tel}</div>
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