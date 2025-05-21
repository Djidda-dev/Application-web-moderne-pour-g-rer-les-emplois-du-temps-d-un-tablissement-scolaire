<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=gestion_etudiants', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupération des données pour le graphique
$stmt = $pdo->query("
    SELECT p.NOM_PROF, SUM(TIMESTAMPDIFF(HOUR, c.HEURE_DEBUT, c.HEURE_FIN)) AS heures_total
    FROM COURS c
    JOIN PROFESSEURS p ON c.ID_PROF = p.ID_PROF
    GROUP BY p.ID_PROF
");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Préparation des données pour Chart.js
$labels = [];
$values = [];
foreach ($data as $row) {
    $labels[] = $row['NOM_PROF'];
    $values[] = $row['heures_total'];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Visualisation des charges horaires</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Charge horaire des enseignants</h1>
        <canvas id="chart"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Heures enseignées',
                    data: <?= json_encode($values) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>









