<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Arial', sans-serif;
        }
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
            color: #fff;
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
        .card {
            margin-bottom: 20px;
        }
        .card-header {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Barre latérale -->
    <div class="sidebar">
        <h4 class="text-center text-white mb-4">Tableau de bord</h4>
        <a href="#" class="nav-link"><i class="fas fa-home"></i> Accueil</a>
        <a href="#classes" class="nav-link"><i class="fas fa-school"></i> Classes</a>
        <a href="#modules" class="nav-link"><i class="fas fa-book"></i> Modules</a>
        <a href="#filieres" class="nav-link"><i class="fas fa-graduation-cap"></i> Filières</a>
        <a href="#professeurs" class="nav-link"><i class="fas fa-user-tie"></i> Professeurs</a>
        <a href="#salles" class="nav-link"><i class="fas fa-building"></i> Salles</a>
        <a href="#seances" class="nav-link"><i class="fas fa-calendar"></i> Séances</a>
        <a href="#etudiants" class="nav-link"><i class="fas fa-users"></i> Étudiants</a>
    </div>

    <!-- Contenu principal -->
    <div class="content">
        <h2 class="text-center my-4">Gestion des données</h2>
        
        <div class="row g-4">

                        <!-- Classes -->
            <div class="col-lg-4 col-md-6" id="classes">
                <div class="card text-center">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-school fa-2x"></i> Classes
                    </div>
                    <div class="card-body">
                        <p>Gérer les classes : ajouter, modifier ou supprimer.</p>
                        <a href="add_classe.php" class="btn btn-warning">Gérer les Classes</a>
                    </div>
                </div>
            </div>

            <!-- Modules -->
            <div class="col-lg-4 col-md-6" id="modules">
                <div class="card text-center">
                    <div class="card-header bg-secondary text-white">
                        <i class="fas fa-book fa-2x"></i> Modules
                    </div>
                    <div class="card-body">
                        <p>Gérer les modules : ajouter, modifier ou supprimer.</p>
                        <a href="add_module.php" class="btn btn-secondary">Gérer les Modules</a>
                    </div>
                </div>
            </div>

                        <!-- Étudiants -->
            <div class="col-lg-4 col-md-6" id="etudiants">
                <div class="card text-center">
                    <div class="card-header bg-dark text-white">
                        <i class="fas fa-users fa-2x"></i> Étudiants
                    </div>
                    <div class="card-body">
                        <p>Gérer les étudiants : ajouter, modifier ou supprimer.</p>
                        <a href="add_etudiant.php" class="btn btn-dark">Gérer les Étudiants</a>
                    </div>
                </div>
            </div>

            <!-- Filières -->
            <div class="col-lg-4 col-md-6" id="filieres">
                <div class="card text-center">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-graduation-cap fa-2x"></i> Filières
                    </div>
                    <div class="card-body">
                        <p>Gérer les filières : ajouter, modifier ou supprimer.</p>
                        <a href="add_filiere.php" class="btn btn-success">Gérer les Filières</a>
                    </div>
                </div>
            </div>

                        <!-- Professeurs -->
            <div class="col-lg-4 col-md-6" id="professeurs">
                <div class="card text-center">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-user-tie fa-2x"></i> Professeurs
                    </div>
                    <div class="card-body">
                        <p>Gérer les professeurs : ajouter, modifier ou supprimer.</p>
                        <a href="add_prof.php" class="btn btn-primary">Gérer les Professeurs</a>
                    </div>
                </div>
            </div>

            <!-- Salles -->
            <div class="col-lg-4 col-md-6" id="salles">
                <div class="card text-center">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-building fa-2x"></i> Salles
                    </div>
                    <div class="card-body">
                        <p>Gérer les salles : ajouter, modifier ou supprimer.</p>
                        <a href="add_salle.php" class="btn btn-info">Gérer les Salles</a>
                    </div>
                </div>
            </div>

            <!-- Séances -->
            <div class="col-lg-4 col-md-6" id="seances">
                <div class="card text-center">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-calendar fa-2x"></i> Séances
                    </div>
                    <div class="card-body">
                        <p>Gérer les séances : ajouter, modifier ou supprimer.</p>
                        <a href="add_seance.php" class="btn btn-danger">Gérer les Séances</a>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>










