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
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($prof['NOM_PROF']) ?>
                        <span class="badge bg-primary rounded-pill">Téléphone: <?= htmlspecialchars($prof['TEL']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#form-prof").submit(function(event) {
            event.preventDefault();

            $.ajax({
                type: "POST",
                url: "save_prof.php",
                data: $(this).serialize(),
                success: function(response) {
                    $("#liste-profs").append(response);
                    alert("Professeur ajouté avec succès !");
                    $("#nom_prof, #tel").val("");
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
