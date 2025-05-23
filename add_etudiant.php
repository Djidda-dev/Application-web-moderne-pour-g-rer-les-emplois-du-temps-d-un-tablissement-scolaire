<?php require_once 'database.php'; 

try { 
    $filieres = $pdo->query("SELECT * FROM filieres ORDER BY NOM_FILIERE ASC")->fetchAll(PDO::FETCH_ASSOC);
    $etudiants = $pdo->query("SELECT * FROM etudiants ORDER BY NOM_ET ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("Erreur : " . $e->getMessage()); 
} 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Étudiants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Gestion des Étudiants</h1>

    <div id="error-message" class="alert alert-danger d-none"></div>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter un étudiant</div>
        <div class="card-body">
            <form id="form-etudiant">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="num_inscription">Numéro d'inscription :</label>
                        <input type="text" name="num_inscription" id="num_inscription" class="form-control" required maxlength="15">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="id_filiere">Filière :</label>
                        <select name="id_filiere" id="id_filiere" class="form-select" required>
                            <option value="">--Sélectionnez une filière--</option>
                            <?php foreach ($filieres as $filiere) : ?>
                                <option value="<?= $filiere['ID_FILIERE'] ?>"> <?= htmlspecialchars($filiere['NOM_FILIERE']) ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="nom_et">Nom :</label>
                        <input type="text" name="nom_et" id="nom_et" class="form-control" required maxlength="25">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="prenom_et">Prénom :</label>
                        <input type="text" name="prenom_et" id="prenom_et" class="form-control" required maxlength="25">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="adresse">Adresse :</label>
                        <input type="text" name="adresse" id="adresse" class="form-control" required maxlength="70">
                    </div>
                    <div class="col-12 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<!-- ... le code existant reste inchangé jusqu'à la liste des étudiants ... -->
<div class="card">
    <div class="card-header">Liste des Étudiants</div>
    <div class="card-body">
        <ul id="liste-etudiants" class="list-group">
            <?php foreach ($etudiants as $etudiant) : ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <?= htmlspecialchars($etudiant['NOM_ET']) . ' ' . htmlspecialchars($etudiant['PRENOM_ET']) ?>
                        <div class="text-muted small"><?= htmlspecialchars($etudiant['ADRESSE']) ?></div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary rounded-pill me-2">Inscription: <?= htmlspecialchars($etudiant['NUM_INSCRIPTION']) ?></span>
                        <button class="btn btn-warning btn-sm btn-edit" 
                                data-id="<?= $etudiant['NUM_INSCRIPTION'] ?>"
                                data-nom="<?= htmlspecialchars($etudiant['NOM_ET']) ?>"
                                data-prenom="<?= htmlspecialchars($etudiant['PRENOM_ET']) ?>"
                                data-adresse="<?= htmlspecialchars($etudiant['ADRESSE']) ?>"
                                data-filiere="<?= $etudiant['ID_FILIERE'] ?>">
                            Modifier
                        </button>
                        <form action="delete_etudiant.php" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet étudiant ?')">
                            <input type="hidden" name="num_inscription" value="<?= $etudiant['NUM_INSCRIPTION'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'étudiant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-etudiant">
                    <input type="hidden" name="num_inscription" id="edit-num">
                    <div class="mb-3">
                        <label class="form-label">Nom :</label>
                        <input type="text" name="nom_et" id="edit-nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prénom :</label>
                        <input type="text" name="prenom_et" id="edit-prenom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filière :</label>
                        <select name="id_filiere" id="edit-filiere" class="form-select" required>
                            <?php foreach ($filieres as $filiere) : ?>
                                <option value="<?= $filiere['ID_FILIERE'] ?>"><?= htmlspecialchars($filiere['NOM_FILIERE']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adresse :</label>
                        <input type="text" name="adresse" id="edit-adresse" class="form-control" required>
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
$(document).ready(function() {
    // Gestion de l'ajout d'un étudiant
    $("#form-etudiant").submit(function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: "save_etudiant.php",
            data: $(this).serialize(),
            success: function(response) {
                // Convertir la réponse en élément jQuery
                const $newStudent = $(response);
                
                // Corriger la structure des boutons
                const deleteBtn = $newStudent.find('.btn-danger');
                const numInscription = deleteBtn.data('id');
                
                // Recréer la structure du formulaire de suppression
                const newForm = `
                    <form class="d-inline" onsubmit="return false;">
                        <input type="hidden" name="num_inscription" value="${numInscription}">
                        <button type="button" class="btn btn-danger btn-sm btn-delete">Supprimer</button>
                    </form>
                `;
                
                // Remplacer le bouton existant
                deleteBtn.replaceWith(newForm);
                
                // Ajouter à la liste
                $("#liste-etudiants").append($newStudent);
                
                // Mettre à jour les gestionnaires d'événements
                attachEventHandlers();
                alert("Étudiant ajouté avec succès !");
                $("#form-etudiant")[0].reset();
            },
            error: function(xhr) {
                console.error("Erreur AJAX :", xhr.responseText);
                alert("Une erreur est survenue.");
            }
        });
    });

    // Gestionnaire d'événements commun
    const attachEventHandlers = () => {
        // Modification
        $(".btn-edit").off('click').click(function() {
            const data = $(this).data();
            $("#edit-num").val(data.id);
            $("#edit-nom").val(data.nom);
            $("#edit-prenom").val(data.prenom);
            $("#edit-adresse").val(data.adresse);
            $("#edit-filiere").val(data.filiere);
            $("#editModal").modal("show");
        });

        // Suppression AJAX
        $(".btn-delete").off('click').click(function() {
            const num = $(this).closest('form').find('input').val();
            if(confirm("Supprimer cet étudiant ?")) {
                $.ajax({
                    type: "POST",
                    url: "delete_etudiant.php",
                    data: { num_inscription: num },
                    success: () => {
                        $(this).closest('li').fadeOut(300, () => $(this).remove());
                    },
                    error: (xhr) => {
                        alert("Erreur : " + xhr.responseText);
                    }
                });
            }
        });
    };

    // Enregistrement des modifications
    $("#saveEdit").click(function() {
        $.ajax({
            type: "POST",
            url: "update_etudiant.php",
            data: $("#form-edit-etudiant").serialize(),
            success: (response) => {
                // Mettre à jour les infos dans la liste
                const formData = $("#form-edit-etudiant").serializeArray().reduce((obj, item) => {
                    obj[item.name] = item.value;
                    return obj;
                }, {});
                
                const $li = $(`li:has(button[data-id="${formData.num_inscription}"])`);
                $li.find(".btn-edit")
                    .data('nom', formData.nom_et)
                    .data('prenom', formData.prenom_et)
                    .data('adresse', formData.adresse)
                    .data('filiere', formData.id_filiere);
                
                $li.find("div:first").html(`
                    ${formData.nom_et} ${formData.prenom_et}
                    <div class="text-muted small">${formData.adresse}</div>
                `);
                
                $("#editModal").modal("hide");
            },
            error: (xhr) => {
                console.error("Erreur AJAX :", xhr.responseText);
                alert("Une erreur est survenue.");
            }
        });
    });

    // Initialisation
    attachEventHandlers();
});
</script>

</body>
</html>