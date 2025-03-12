<?php
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
            echo json_encode(['status' => 'success', 'message' => 'Équipe créée avec succès!'.$premier.''.$deuxieme.''.$troisieme.''.$quatrieme.'']);
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

    } else {
        echo json_encode(['status' => 'error', 'message' => 'L\'action est manquante!']);
    }
}
?>
