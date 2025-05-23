<?php 
require_once 'database.php'; 
try { 
    // Récupération des classes
    $classes = $pdo->query("SELECT * FROM classes ORDER BY NIVEAU")->fetchAll(); 
    // Récupération des filières
    $filieres = $pdo->query("SELECT * FROM filieres ORDER BY NOM_FILIERE ASC")->fetchAll(); 
} catch (PDOException $e) { 
    die("Erreur : " . $e->getMessage()); 
} 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Ajout du script Bootstrap -->
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">Gestion des Classes</h1>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter une nouvelle classe</div>
        <div class="card-body">
            <form id="form-classe">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="niveau">Nom de la classe :</label>
                        <input type="text" name="niveau" id="niveau" class="form-control" required placeholder="Ex: IRT3, Licence 1..." maxlength="50">
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
                    <div class="col-12 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des classes existantes -->
    <div class="card">
        <div class="card-header">Liste des classes existantes</div>
        <div class="card-body">
            <ul id="liste-classes" class="list-group">
                <?php foreach ($classes as $classe) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($classe['NIVEAU']) ?>
                        <span class="badge bg-primary rounded-pill">ID: <?= $classe['ID_CLASSE'] ?></span>
                        
                        <div>
                            <button class="btn btn-warning btn-sm btn-edit" data-id="<?= $classe['ID_CLASSE'] ?>" data-niveau="<?= htmlspecialchars($classe['NIVEAU']) ?>">Modifier</button>
                            <form action="delete_classe.php" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')">
                                <input type="hidden" name="id_classe" value="<?= $classe['ID_CLASSE'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
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
                <h5 class="modal-title">Modifier la classe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-classe">
                    <input type="hidden" name="id_classe" id="edit-id">
                    <label class="form-label" for="edit-niveau">Nom de la classe :</label>
                    <input type="text" name="niveau" id="edit-niveau" class="form-control" required>
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
    $("#form-classe").submit(function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: "save_classe.php",
            data: $(this).serialize(),
            success: function(response) {
                $("#liste-classes").append(response);
                alert("Classe ajoutée avec succès !");
                $("#niveau").val("");

                updateEvents(); // Mettre à jour les événements AJAX
            },
            error: function() {
                alert("Une erreur est survenue.");
            }
        });
    });

    function updateEvents() {
        $(document).on("click", ".btn-edit", function() {
            const idClasse = $(this).data("id");
            const niveauClasse = $(this).data("niveau");

            $("#edit-id").val(idClasse);
            $("#edit-niveau").val(niveauClasse);

            $("#editModal").modal("show"); // Afficher le modal
        });

        $(document).on("click", "#saveEdit", function() {
            $.ajax({
                type: "POST",
                url: "update_classe.php",
                data: $("#form-edit-classe").serialize(),
                success: function(response) {
                    alert("Classe modifiée avec succès !");
                    location.reload();
                },
                error: function() {
                    alert("Une erreur est survenue.");
                }
            });
        });
    }

    updateEvents(); // Activer les événements AJAX au chargement de la page
});
</script>

</body>
</html>
