<?php require_once 'database.php'; 

try { 
    $filieres = $pdo->query("SELECT * FROM filieres ORDER BY NOM_FILIERE ASC")->fetchAll(PDO::FETCH_ASSOC); 
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

    <!-- Liste des filières existantes -->
    <div class="card">
        <div class="card-header">Liste des filières existantes</div>
        <div class="card-body">
            <ul id="liste-filieres" class="list-group">
                <?php foreach ($filieres as $filiere) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($filiere['NOM_FILIERE']) ?>
                        <span class="badge bg-primary rounded-pill">ID: <?= $filiere['ID_FILIERE'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#form-filiere").submit(function(event) {
            event.preventDefault();

            $.ajax({
                type: "POST",
                url: "save_filiere.php",
                data: $(this).serialize(),
                success: function(response) {
                    $("#liste-filieres").append(response);
                    alert("Filière ajoutée avec succès !");
                    $("#nom_filiere, #description").val("");
                },
                error: function(xhr) {
                    $("#error-message").removeClass("d-none").text(xhr.responseText);
                }
            });
        });
    });
</script>

</body>
</html>
