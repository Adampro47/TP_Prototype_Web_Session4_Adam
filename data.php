<?php
header('Content-Type: application/json');
// Paramètres de connexion à la base de données
$servername = "127.0.0.1";
$username = "bedardh25techinf_visiteur";
$usernameCreation = "bedardh25techinf_user_creation";
$password = "curl_stat";
$passwordCreation = "curl_stat_Creation";
$dbname = "bedardh25techinf_Curl_stat";


// Fonction pour créer une connexion à la base de données
function getConnexionBd() {
    global $servername, $username, $password, $dbname;
    return new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}
// Fonction pour créer une connexion à la base de données pour la création d'utilisateur
function getConnexionBdCreation() {
    global $servername, $usernameCreation, $passwordCreation, $dbname;
    return new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $usernameCreation, $passwordCreation, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}
// Fonction pour insérer un nouvel utilisateur dans la base de données
function insertUtilisateur($email, $mot_de_passe) {
    try {
        $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $maConnexionPDO = getConnexionBdCreation();

        $pdoRequete = $maConnexionPDO->prepare("
            INSERT INTO utilisateurs (email, mot_de_passe)
            VALUES (:email, :mot_de_passe)
        ");

        $pdoRequete->bindParam(':email', $email, PDO::PARAM_STR);
        $pdoRequete->bindParam(':mot_de_passe', $hashedPassword, PDO::PARAM_STR);

        $pdoRequete->execute();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }    
}
// Fonction pour vérifier les identifiants de l'utilisateur dans la base de données
function verifierUtilisateur($email, $mot_de_passe) {
    try {
        $maConnexionPDO = getConnexionBd(); // Utilise l'utilisateur "visiteur"
        $pdoRequete = $maConnexionPDO->prepare("
            SELECT mot_de_passe FROM utilisateurs WHERE email = :email
        ");
        $pdoRequete->bindParam(':email', $email, PDO::PARAM_STR);
        $pdoRequete->execute();
        $resultat = $pdoRequete->fetch();

        if ($resultat && password_verify($mot_de_passe, $resultat['mot_de_passe'])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Identifiants invalides']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
// Fonction pour vérifier si l'email existe déjà dans la base de données
function verifierEmailExistant($email) {
    try {
        $maConnexionPDO = getConnexionBd(); // Connexion à la base de données
        $pdoRequete = $maConnexionPDO->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
        $pdoRequete->bindParam(':email', $email, PDO::PARAM_STR);
        $pdoRequete->execute();
        $resultat = $pdoRequete->fetchColumn(); // Retourne le nombre d'occurrences

        // Si l'email existe déjà, on renvoie un message d'erreur
        if ($resultat > 0) {
            echo json_encode(['status' => 'error', 'message' => 'L\'email est déjà utilisé']);
            exit;
        }

        // Si l'email n'existe pas, on peut continuer l'exécution
        return false;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la vérification de l\'email']);
        exit;
    }
}
function getStatsByCategory($categoryId) {
    global $servername, $username, $password, $dbname;

    try {
        // Créer une connexion PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête SQL pour récupérer les données par catégorie
        $sql = "
            SELECT tdl.nom AS type_de_lancer, s.valeur
            FROM stats s
            JOIN type_de_lancer tdl ON s.type_de_lancer_id = tdl.id
            WHERE s.category_id = :category_id
            ORDER BY tdl.nom, s.indice;
        ";

        // Préparer et exécuter la requête
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        // Initialiser un tableau pour stocker les résultats
        $data = [];

        // Récupérer les données et les organiser dans un format spécifique
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $typeDeLancer = $row['type_de_lancer'];
            $valeur = $row['valeur'];

            // Ajouter les valeurs à un tableau multidimensionnel
            if (!isset($data[$typeDeLancer])) {
                $data[$typeDeLancer] = [];
            }

            $data[$typeDeLancer][] = $valeur;
        }

        // Fermer la connexion
        $conn = null;

        // Retourner le tableau formaté en JSON
        return json_encode($data);

    } catch (PDOException $e) {
        // Gérer les erreurs de connexion ou de requête
        return json_encode(["error" => "Erreur : " . $e->getMessage()]);
    }
}
////////////////////////////
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
 // Les requetes AJAX sont traitées ici
// Vérifiez si la requête est une requête POST
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
        elseif ($action == 'obtenirStatCategorie') {
            $equipe = $_POST['select_Equipe2'];
            echo json_encode(['status' => 'success', 'stat' => $equipe]);
            /*$statistiques = getStatsByCategory($_POST['select_Equipe2']);
            echo json_encode(['status' => 'success', 'stat' => $statistiques]);*/
        }
        elseif ($action == 'AjouterCompteBD') {
            
            $email = $_POST['email'];
            $mot_de_passe = $_POST['mot_de_passe'];
            insertUtilisateur($email,$mot_de_passe);
            echo json_encode(['status' => 'success']);
        }
        elseif ($action == 'verifierConnexion') {
            $email = $_POST['email'];
            $mot_de_passe = $_POST['mot_de_passe'];
            verifierUtilisateur($email, $mot_de_passe);
        }
        elseif ($action == 'verifierEmail') {
            $email = $_POST['email'];
            verifierEmailExistant($email);
        }
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'L\'action est manquante!']);
    }
}
?>
