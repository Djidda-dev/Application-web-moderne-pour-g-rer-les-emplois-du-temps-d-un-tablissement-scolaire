<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=gestion_etudiants', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupération dynamique des classes
$stmt = $pdo->query("SELECT ID_CLASSE, NIVEAU FROM classes ORDER BY NIVEAU");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des étudiants et modules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4">Listes des etudiants par modules</h1>
        
        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <label for="classe" class="form-label">Sélectionnez une classe :</label>
                <select id="classe" class="form-select">
                    <option value="">-- Choisissez une classe --</option>
                    <?php foreach ($classes as $classe) : ?>
                        <option value="<?= htmlspecialchars($classe['ID_CLASSE']) ?>"><?= htmlspecialchars($classe['NIVEAU']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div id="loading" class="text-center d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>

        <div id="resultat"></div>
    </div>

    <script>
        $(document).ready(function() {
            $("#classe").change(function() {
                const classeId = $(this).val();
                if (!classeId) return;

                $("#loading").removeClass("d-none");

                $.ajax({
                    type: "GET",
                    url: "get_infos_classe.php",
                    data: { classe: classeId },
                    success: function(response) {
                        $("#resultat").html(response);
                    },
                    error: function() {
                        $("#resultat").html("<div class='alert alert-danger'>Erreur lors du chargement des informations</div>");
                    },
                    complete: function() {
                        $("#loading").addClass("d-none");
                    }
                });
            });
        });
    </script>
</body>
</html>
