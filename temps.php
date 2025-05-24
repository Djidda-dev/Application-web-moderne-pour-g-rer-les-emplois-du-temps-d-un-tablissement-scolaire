<?php
require_once 'database.php';

header("Content-Type: application/xml");
header("Content-Disposition: attachment; filename=temps.xml");

if (isset($_GET['classe'])) {
    $id_classe = $_GET['classe'];

    try {
        $stmt = $pdo->prepare("SELECT c.jour, c.heure_debut, c.heure_fin, p.nom_prof, m.nom_module, s.nom_salle
                               FROM cours c
                               JOIN professeurs p ON c.id_prof = p.id_prof
                               JOIN modules m ON c.id_module = m.id_module
                               JOIN salles s ON c.id_salle = s.id_salle
                               WHERE c.id_classe = ?");
        $stmt->execute([$id_classe]);
        $seances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Génération du XML
        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><emploi/>");
        $xml->addAttribute("classe", $id_classe);

        foreach ($seances as $seance) {
            $seanceNode = $xml->addChild("seance");
            $seanceNode->addAttribute("jour", $seance['jour']);
            $seanceNode->addAttribute("debut", $seance['heure_debut']);
            $seanceNode->addAttribute("fin", $seance['heure_fin']);
            $seanceNode->addAttribute("prof", $seance['nom_prof']);
            $seanceNode->addAttribute("module", $seance['nom_module']);
            $seanceNode->addAttribute("salle", $seance['nom_salle']);
        }

        echo $xml->asXML();

    } catch (PDOException $e) {
        die("Erreur SQL : " . $e->getMessage());
    }
} else {
    die("Erreur : ID de classe manquant.");
}
?>
