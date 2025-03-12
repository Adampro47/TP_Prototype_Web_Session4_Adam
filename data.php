<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Exemple de fonctions utilisées
function obtenirEquipe() {
    // Code pour obtenir l'équipe
    return array('Premier', 'Deuxième', 'Troisième', 'Quatrième');
}

function obtenirStatistiques() {
    // Code pour obtenir les statistiques
    return array('Stat1', 'Stat2', 'Stat3');
}

// Gérer les requêtes AJAX pour créer une équipe ou ajouter des statistiques
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier si la clé 'action' existe dans $_POST
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'creerEquipe') {
            $premier = $_POST['Premier'];
            $deuxieme = $_POST['Deuxieme'];
            $troisieme = $_POST['Troisieme'];
            $quatrieme = $_POST['Quatrième'];

            // Sauvegarder les données ou effectuer d'autres actions nécessaires
            echo json_encode(['status' => 'success', 'message' => 'Équipe créée avec succès!']);
        } 
        if ($action == 'ajouterStatistique') {
            $type_lancer = $_POST['type_lancer'];
            $note = $_POST['note'];

            // Ajouter la statistique ou effectuer d'autres actions nécessaires
            echo json_encode(['status' => 'success', 'message' => 'Statistique ajoutée!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'L\'action est manquante!']);
    }
}
?>
