<?php
require_once 'database.php';

try {
    // Vérification de la connexion
    if (!$pdo) {
        throw new Exception("Erreur de connexion à la base de données");
    }

    // Récupération des données avec des noms de table en minuscules
    $classes = $pdo->query("SELECT * FROM classes")->fetchAll(PDO::FETCH_ASSOC);
    $profs = $pdo->query("SELECT * FROM professeurs")->fetchAll(PDO::FETCH_ASSOC);
    $modules = $pdo->query("SELECT * FROM modules")->fetchAll(PDO::FETCH_ASSOC);
    $salles = $pdo->query("SELECT * FROM salles")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une séance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-center">Ajouter une nouvelle séance</h1>
        
        <div id="error-message" class="alert alert-danger d-none"></div>
        
        <form id="formSeance">
            <div class="row g-3">
                <!-- Sélection de la classe -->
                <div class="col-md-6">
                    <label class="form-label">Classe :</label>
                    <select name="classe" class="form-select" required>
                        <?php foreach ($classes as $classe) : ?>
                            <option value="<?= $classe['ID_CLASSE'] ?>">
                                <?= htmlspecialchars($classe['NIVEAU']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Sélection du module -->
                <div class="col-md-6">
                    <label class="form-label">Module :</label>
                    <select name="module" class="form-select" required>
                        <?php foreach ($modules as $module) : ?>
                            <option value="<?= $module['ID_MODULE'] ?>">
                                <?= htmlspecialchars($module['NOM_MODULE']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Professeur, Salle, Jour, Horaires -->
                <div class="col-md-6">
                    <label class="form-label">Professeur :</label>
                    <select name="prof" class="form-select" required>
                        <?php foreach ($profs as $prof) : ?>
                            <option value="<?= $prof['ID_PROF'] ?>">
                                <?= htmlspecialchars($prof['NOM_PROF']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Salle :</label>
                    <select name="salle" class="form-select" required>
                        <?php foreach ($salles as $salle) : ?>
                            <option value="<?= $salle['ID_SALLE'] ?>">
                                <?= htmlspecialchars($salle['NOM_SALLE']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jour :</label>
                    <select name="jour" class="form-select" required>
                        <option value="Lundi">Lundi</option>
                        <option value="Mardi">Mardi</option>
                        <option value="Mercredi">Mercredi</option>
                        <option value="Jeudi">Jeudi</option>
                        <option value="Vendredi">Vendredi</option>
                        <option value="Samedi">Samedi</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Heure de début :</label>
                    <input type="time" name="heure_debut" class="form-control" required step="900">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Heure de fin :</label>
                    <input type="time" name="heure_fin" class="form-control" required step="900">
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $("#formSeance").submit(function(event) {
                event.preventDefault(); // Empêcher le rechargement
                $.ajax({
                    type: "POST",
                    url: "save_seance.php",
                    data: $(this).serialize(),
                    success: function(response) {
                        alert("Séance ajoutée avec succès !");
                        $("#formSeance")[0].reset(); // Réinitialiser le formulaire
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
