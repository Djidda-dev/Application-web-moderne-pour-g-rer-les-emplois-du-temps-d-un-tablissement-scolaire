<?php require_once 'database.php'; 

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
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($module['NOM_MODULE']) ?>
                        <span class="badge bg-primary rounded-pill">ID: <?= $module['ID_MODULE'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#form-module").submit(function(event) {
            event.preventDefault();

            $.ajax({
                type: "POST",
                url: "save_module.php",
                data: $(this).serialize(),
                success: function(response) {
                    $("#liste-modules").append(response);
                    alert("Module ajouté avec succès !");
                    $("#nom_module, #description").val("");
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
