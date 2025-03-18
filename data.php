<?php
$statsData = [
        "Placement" => [0, 0, 0, 3, 4, 4, 3, 4, 3, 4],
        "Sortie" => [3, 4, 4, 4, 3, 3, 2, 4, 3, 3],
        "Raise" => [4, 4, 3, 3, 3, 4, 3, 4, 3, 3],
        "Garde" => [3, 3, 4, 3, 4, 4, 3, 4, 3, 3],
        "Double sortie" => [3, 3, 4, 3, 4, 3, 4, 3, 2, 4],
        "Placement gelé" => [3, 2, 4, 4, 3, 4, 4, 3, 3, 3]
];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function obtenirEquipe() {
    return array('Premier', 'Deuxième', 'Troisième', 'Quatrième');
}

function obtenirStatistiques() {
    return array('Stat1', 'Stat2', 'Stat3');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'creerEquipe') {
            $premier = $_POST['Premier'];
            $deuxieme = $_POST['Deuxieme'];
            $troisieme = $_POST['Troisieme'];
            $quatrieme = $_POST['Quatrième'];
            $equipe = $_POST['select_Equipe'];

            $post_string = http_build_query($_POST);
            /*echo json_encode(['status' => 'success', 'message' => $post_string]);*/
            echo json_encode(['status' => 'success', 'message' => 'Équipe créée avec succès!'.$premier.''.$deuxieme.''.$troisieme.''.$quatrieme.''.$equipe.'']);
        } 
        elseif ($action == 'ajouterStatistique') {
            $statistiques = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'select_') === 0) {
                    $statistiques[] = $value;
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Statistiques ajoutées',
                'data' => $statistiques
            ]);
        }
        elseif ($action == 'obtenirNomJoueur') {
            $p = 't1';
            $d = 't2';
            $t = 't3';
            $q = 't4';
            echo json_encode(['status' => 'success', 'premier' => $p,'deuxieme' => $d,'troisieme' => $t,'quatrieme' => $q]);
        }
        elseif ($action == 'obtenirStatMonEquipe') {
            echo json_encode(['status' => 'success', 'stats' => $statsData]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'L\'action est manquante!']);
    }
}
?>
