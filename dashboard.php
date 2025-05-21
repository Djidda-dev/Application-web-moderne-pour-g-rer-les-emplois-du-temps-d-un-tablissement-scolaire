<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=gestion_etudiants', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gestion Éducation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body class="bg-light">
    
    <!-- Barre latérale -->
    <div class="sidebar">
        <h4 class="text-center text-white mb-4">Tableau de bord</h4>
        <a href="#" class="nav-link"><i class="fas fa-home"></i> Accueil</a>
        <a href="#emploi" class="nav-link"><i class="fas fa-calendar"></i> Emploi du temps</a>
        <a href="add_etudiant.php" class="nav-link"><i class="fas fa-users"></i> Étudiants</a>
        <a href="add_module.php" class="nav-link"><i class="fas fa-book"></i> Modules</a>
        <a href="graphique_charge_horaire.php" class="nav-link"><i class="fas fa-chart-bar"></i> Graphique Professeurs</a>
        <a href="add_seance.php" class="nav-link"><i class="fas fa-edit"></i> Saisie Séances</a>
        <a href="Gestion_donnes.php" class="nav-link"><i class="fas fa-cogs"></i> Gestion des données</a>
    </div>

    <!-- Contenu principal -->
    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="#">Dashboard Éducation</a>
            </div>
        </nav>

        <div class="container py-5">
            <h1 class="text-center mb-4">Dashboard Éducation</h1>

            <!-- Onglets -->
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" href="#" data-tab="emploi">Emploi du temps</a></li>
            <li class="nav-item"><a class="nav-link" href="add_etudiant.php" data-tab="etudiants">Étudiants</a></li>
            <li class="nav-item"><a class="nav-link" href="add_module.php" data-tab="modules">Modules</a></li>
            <li class="nav-item"><a class="nav-link" href="graphique_charge_horaire.php" data-tab="graphique">Graphique des Professeurs</a></li>
            <li class="nav-item"><a class="nav-link" href="add_seance.php" data-tab="seances">Saisie des Séances</a></li>
            <li class="nav-item"><a class="nav-link" href="Gestion_donnes.php" data-tab="gestion">Gestion des données</a></li>
        </ul>

            <!-- Contenus des onglets -->
            <div id="emploi" class="tab-content active">
                <h3>Emploi du temps</h3>
                <select id="classe" class="form-select">
                    <option value="">-- Sélectionnez une classe --</option>
                    <?php
                    $stmt = $pdo->query("SELECT ID_CLASSE, NIVEAU FROM CLASSES ORDER BY NIVEAU");
                    while ($classe = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$classe['ID_CLASSE']}'>" . htmlspecialchars($classe['NIVEAU']) . "</option>";
                    }
                    ?>
                </select>
                <div id="emploi_du_temps"></div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".nav-link").click(function() {
                $(".tab-content").removeClass("active");
                $("#" + $(this).data("tab")).addClass("active");
            });

            $("#classe").change(function() {
                const classeId = $(this).val();
                if (!classeId) return;

                $.get("get_emploi_du_temps.php", { classe: classeId }, function(response) {
                    $("#emploi_du_temps").html(response);
                });

                $.get("get_infos_classe.php", { classe: classeId }, function(response) {
                    $("#infos_etudiants").html(response);
                });
            });

            // Graphique des heures
            $.getJSON("get_charge_horaire.php", function(data) {
                const ctx = document.getElementById('chart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Heures enseignées',
                            data: data.values,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    }
                });
            });
        });
    </script>
</body>
</html>
