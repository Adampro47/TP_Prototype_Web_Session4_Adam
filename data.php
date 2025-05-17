<?php
session_name('Session_Curl_Stat');
ini_set("session.cookie_lifetime", 1000000);
ini_set("session.use_cookies", 1);
ini_set("session.use_only_cookies", 1);
ini_set("session.use_strict_mode", 1);
ini_set("session.cookie_httponly", 1);
ini_set("session.cookie_secure", 1);
ini_set("session.cookie_samesite", "Strict");
ini_set("session.cache_limiter", "nocache");
ini_set("session.hash_function", "sha512");
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Paramètres de connexion
$servername = "127.0.0.1";
$username = "bedardh25techinf_visiteur";
$usernameCreation = "bedardh25techinf_user_creation";
$password = "curl_stat";
$passwordCreation = "curl_stat_Creation";
$dbname = "bedardh25techinf_Curl_stat";


// Connexion BD
function getConnexionBd() {
    global $servername, $username, $password, $dbname;
    return new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}

function getConnexionBdCreation() {
    global $servername, $usernameCreation, $passwordCreation, $dbname;
    return new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $usernameCreation, $passwordCreation, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}

function insertUtilisateur($email, $mot_de_passe) {
    try {
        $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $pdo = getConnexionBdCreation();
        $requete = $pdo->prepare("INSERT INTO utilisateurs (email, mot_de_passe) VALUES (:email, :mot_de_passe)");
        $requete->bindParam(':email', $email);
        $requete->bindParam(':mot_de_passe', $hashedPassword);
        $requete->execute();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

function verifierUtilisateur($email, $mot_de_passe) {
    try {
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE email = :email");
        $requete->bindParam(':email', $email);
        $requete->execute();
        $result = $requete->fetch();

        if ($result && password_verify($mot_de_passe, $result['mot_de_passe'])) {
            $_SESSION['user_id'] = ObtenirIdAvecEmail($email);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Identifiants invalides']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function verifierEmailExistant($email) {
    try {
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
        $requete->bindParam(':email', $email);
        $requete->execute();
        $count = $requete->fetchColumn();

        if ($count > 0) {
            echo json_encode(['status' => 'error', 'message' => 'L\'email est déjà utilisé']);
            exit;
        }
        echo json_encode(['status' => 'success']);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la vérification de l\'email']);
        exit;
    }
}

function ObtenirIdAvecEmail($email) {
    try {
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $requete->bindParam(':email', $email);
        $requete->execute();
        return $requete->fetchColumn() ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function obtenirNomEquipe() {
    try {
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT id_equipe, equipe_name FROM equipe");
        $requete->execute();
        $equipes = $requete->fetchAll();
        echo json_encode(['status' => 'success', 'equipes' => $equipes]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur BD : '.$e->getMessage()]);
        exit;
    }
}

function insertEquipe($nom, $categorie) {
    try {
        $pdo = getConnexionBd();

        // Vérifier si le nom existe déjà
        $check = $pdo->prepare("SELECT COUNT(*) FROM equipe WHERE equipe_name = :nom");
        $check->bindParam(':nom', $nom);
        $check->execute();

        if ($check->fetchColumn() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Ce nom d\'équipe est déjà utilisé.']);
            exit;
        }
        $pdo = getConnexionBdCreation();
        $requete = $pdo->prepare("INSERT INTO equipe (equipe_name, categorie) VALUES (:nom, :categorie)");
        $requete->bindParam(':nom', $nom);
        $requete->bindParam(':categorie', $categorie);
        $requete->execute();

        echo json_encode(['status' => 'success']);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}


function obtenirStatParCategorie($categorie_id) {
    try {
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT * FROM stats WHERE category_id = :categorie_id");
        $requete->bindParam(':categorie_id', $categorie_id);
        $requete->execute();
        $result = $requete->fetchAll();

        if ($result) {
            echo json_encode(['status' => 'success', 'stats' => $result]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Aucune statistique trouvée.']);
        }
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur BD : '.$e->getMessage()]);
        exit;
    }
}

function obtenirStatistiques() {
    return ['Stat1', 'Stat2', 'Stat3'];
}
function rejoindreEquipe($idUtilisateur, $idEquipe) {
    try {
        $pdo = getConnexionBdCreation();
        $requete = $pdo->prepare("INSERT INTO equipe_joueur (id_equipe, id_joueur) VALUES (:idEquipe, :idJoueur)");
        $requete->bindParam(':idEquipe', $idEquipe, PDO::PARAM_INT);
        $requete->bindParam(':idJoueur', $idUtilisateur, PDO::PARAM_INT);
        $requete->execute();

        echo json_encode(['status' => 'success', 'message' => 'Équipe rejointe avec succès']);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l’ajout à l’équipe : ' . $e->getMessage()]);
        exit;
    }
}
function creerEvenement($nom, $date, $idEquipe) {
    try {
        $pdo = getConnexionBdCreation();
        $stmt = $pdo->prepare("INSERT INTO evenements (nom, date_event, id_equipe) VALUES (:nom, :date_event, :id_equipe)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':date_event', $date);
        $stmt->bindParam(':id_equipe', $idEquipe, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function getEquipeParJoueur($idJoueur) {
    try {
        $pdo = getConnexionBd();
        $stmt = $pdo->prepare("SELECT id_equipe FROM equipe_joueur WHERE id_joueur = :id");
        $stmt->bindParam(':id', $idJoueur, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return null;
    }
}
// Traitement des requêtes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'creerEquipe':
            insertEquipe($_POST['Equipe'], $_POST['select_Equipe']);
            break;

        case 'ajouterStatistique':
            $statistiques = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'select_') === 0) {
                    $statistiques[] = $value;
                }
            }
            echo json_encode(['status' => 'success', 'message' => 'Statistiques ajoutées', 'data' => $statistiques]);
            exit;

        case 'obtenirNomJoueur':
            echo json_encode([
                'status' => 'success',
                'premier' => 't1',
                'deuxieme' => 't2',
                'troisieme' => 't3',
                'quatrieme' => 't4'
            ]);
            exit;

        case 'obtenirStat':
            $category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
            obtenirStatParCategorie($category_id);
            break;

        case 'obtenirStatParCategorie':
            $categorie = $_POST['select_Equipe2'] ?? null;
            if ($categorie !== null) {
                obtenirStatParCategorie($categorie);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Catégorie manquante']);
                exit;
            }
            break;

        case 'AjouterCompteBD':
            
            if (!empty($_SESSION['code_verifié'])) {
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $mot_de_passe = $_POST['mot_de_passe'];
                if (!$email || !$mot_de_passe) {
                    echo json_encode(['status' => 'error', 'message' => 'Champs invalides']);
                    exit;
                }
                insertUtilisateur($email, $mot_de_passe);
                $_SESSION['user_id'] = ObtenirIdAvecEmail($email);
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Le code doit être vérifié']);
            }
            exit;

        case 'verifierConnexion':
            $email = $_POST['email'];
            $mot_de_passe = $_POST['mot_de_passe'];
            verifierUtilisateur($email, $mot_de_passe);
            break;

        case 'verifierEmail':
            $email = $_POST['email'];
            verifierEmailExistant($email);
            break;

        case 'obtenirNomEquipe':
            obtenirNomEquipe();
            break;

        case 'rejoindreEquipe':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
                exit;
            }
            $idEquipe = $_POST['select_Equipe'] ?? null;
            if (!$idEquipe) {
                echo json_encode(['status' => 'error', 'message' => 'ID d’équipe manquant']);
                exit;
            }
            rejoindreEquipe($_SESSION['user_id'], $idEquipe);
            break;
        case 'creerEvenement':
            $nom = $_POST['nomEvenement'];
            $date = $_POST['dateEvenement'];
            
            // Obtenir l'équipe de l'utilisateur (à implémenter selon ta logique)
            $idEquipe = getEquipeParJoueur($_SESSION['user_id']);
            
            if ($idEquipe !== null && $idEquipe !== '' && $nom && $date) {
                creerEvenement($nom, $date, $idEquipe);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Informations manquantes'.$nom.$date.$idEquipe]);
                exit;
            }
            break;
        case 'verifierRejoignable':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
                exit;
            }
            try {
                $pdo = getConnexionBd();
                $requete = $pdo->prepare("SELECT COUNT(*) FROM equipe_joueur WHERE id_joueur = :id");
                $requete->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
                $requete->execute();
                $dejaRejoint = $requete->fetchColumn() > 0;
                echo json_encode(['status' => 'success', 'dejaRejoint' => $dejaRejoint]);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => 'Erreur BD']);
            }
            exit;

        default:
            error_log("Action inconnue : " . $action);
            echo json_encode(['status' => 'error', 'message' => 'Action inconnue']);
            exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'L\'action est manquante!']);
    exit;
}
?>
