<?php require_once 'database.php'; 
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
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#form-classe").submit(function(event) {
            event.preventDefault(); // Empêche le rechargement de la page

            $.ajax({
                type: "POST",
                url: "save_classe.php",
                data: $(this).serialize(),
                success: function(response) {
                    $("#liste-classes").append(response); // Ajoute la nouvelle classe à la liste
                    alert("Classe ajoutée avec succès !");
                    $("#niveau").val(""); // Réinitialise le champ
                },
                error: function() {
                    alert("Une erreur est survenue.");
                }
            });
        });
    });
</script>

</body>
</html>
