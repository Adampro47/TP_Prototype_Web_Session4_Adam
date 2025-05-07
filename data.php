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
        echo json_encode(['status' => 'success']);
        exit;
        // Si l'email n'existe pas, on peut continuer l'exécution
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la vérification de l\'email']);
        exit;
    }  
}

function ObtenirIdAvecEmail($email) {
    try {
        $maConnexionPDO = getConnexionBd(); // Connexion à la base de données
        $pdoRequete = $maConnexionPDO->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $pdoRequete->bindParam(':email', $email, PDO::PARAM_STR);
        $pdoRequete->execute();
        $resultat = $pdoRequete->fetchColumn();
        if ($resultat != null){
            return($resultat);
        }
        return null;
    } catch (Exception $e) {
        return null;
        exit;
    }  
}

function obtenirNomEquipe() {
    try {
        $maConnexionPDO = getConnexionBd(); // Connexion à la base de données
        $pdoRequete = $maConnexionPDO->prepare("SELECT equipe_name FROM `equipe`");
        $pdoRequete->execute();
        $resultats = $pdoRequete->fetchAll(PDO::FETCH_COLUMN); // Récupère toutes les valeurs de la colonne equipe_name

        echo json_encode(['status' => 'success', 'equipes' => $resultats]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la récupération des équipes : '.$e->getMessage()]);
        exit;
    }
}

function insertEquipe($nom, $categorie) {
    try {
        $maConnexionPDO = getConnexionBdCreation();

        $pdoRequete = $maConnexionPDO->prepare("
            INSERT INTO equipe (equipe_name, categorie)
            VALUES (:nom, :categorie)
        ");

        $pdoRequete->bindParam(':nom', $nom, PDO::PARAM_STR);
        $pdoRequete->bindParam(':categorie', $categorie, PDO::PARAM_STR);

        $pdoRequete->execute();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }    
    echo json_encode(['status' => 'success']);
}


////////////////////////////
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function obtenirStatParCategorie($categorie_id) {
    error_log("data.php - obtenirStat - Fonction");
    try {
        $maConnexionPDO = getConnexionBd();
        $pdoRequete = $maConnexionPDO->prepare("SELECT * FROM stats WHERE category_id = :categorie_id");
        $pdoRequete->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
        $pdoRequete->execute();
        $resultat = $pdoRequete->fetchAll(PDO::FETCH_ASSOC);

        if ($resultat != null) {
            echo json_encode(['status' => 'success', 'stats' => $resultat]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Aucune statistique trouvée.']);
        }
        return true;
    } catch (Exception $e) {
        error_log($e);
        echo json_encode(['status' => 'error', 'message' => 'Erreur dans la requête à la BD : '.$e]);
        return false;
    }
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
            $equipe = $_POST['Equipe'];
            $categorie = $_POST['select_Equipe'];
            error_log($equipe);
            error_log($categorie);

            insertEquipe($equipe,$categorie);
            echo json_encode(['status' => 'success', 'message' => 'Équipe créée (pas pour de vrai) : '.$equipe.''.$categorie.'']);
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
        elseif ($action == 'obtenirStat') {
            $category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
            error_log("data.php - obtenirStat - category_id: ".$category_id);
            obtenirStatParCategorie($category_id);
        }
        elseif ($action == 'obtenirStatParCategorie') {
            //$equipe = $_POST['select_Equipe2'];
            
            obtenirStatParCategorie();
            
            /*$statistiques = getStatsByCategory($_POST['select_Equipe2']);
            echo json_encode(['status' => 'success', 'stat' => $statistiques]);*/
        }
        elseif ($action == 'AjouterCompteBD') {
            session_name('Session_Curl_Stat');
            ini_set("session.cookie_lifetime", 1000000);
            ini_set("session.use_cookies", 1);
            ini_set("session.use_only_cookies" , 1);
            ini_set("session.use_strict_mode", 1);
            ini_set("session.cookie_httponly", 1);
            ini_set("session.cookie_secure", 1);
            ini_set("session.cookie_samesite" , "Strict");
            ini_set("session.cache_limiter" , "nocache");
            ini_set("session.hash_function" , "sha512");
            session_start();
            if ($_SESSION['code_verifié']){
                $email = $_POST['email'];
                $mot_de_passe = $_POST['mot_de_passe'];
                insertUtilisateur($email,$mot_de_passe);
                $_SESSION['user_id'] = ObtenirIdAvecEmail($email);
                echo json_encode(['status' => 'success']);
            }
            echo json_encode(['status' => 'error','message' => 'Le code doit être vérifié']);
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
        elseif ($action == 'obtenirNomEquipe') {
            obtenirNomEquipe();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'L\'action est manquante!']);
    }
}
?>
