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

    <!-- Liste des étudiants -->
    <div class="card">
        <div class="card-header">Liste des Étudiants</div>
        <div class="card-body">
            <ul id="liste-etudiants" class="list-group">
                <?php foreach ($etudiants as $etudiant) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($etudiant['NOM_ET']) . ' ' . htmlspecialchars($etudiant['PRENOM_ET']) ?>
                        <span class="badge bg-primary rounded-pill">Inscription: <?= htmlspecialchars($etudiant['NUM_INSCRIPTION']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#form-etudiant").submit(function(event) {
            event.preventDefault();

            $.ajax({
                type: "POST",
                url: "save_etudiant.php",
                data: $(this).serialize(),
                success: function(response) {
                    $("#liste-etudiants").append(response);
                    alert("Étudiant ajouté avec succès !");
                    $("#num_inscription, #id_filiere, #nom_et, #prenom_et, #adresse").val("");
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
