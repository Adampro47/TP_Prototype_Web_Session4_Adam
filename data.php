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
require_once '/home/bedardh25techinf/etc/bedard.h25.techinfo420.ca/connexion.php';


function logMessage($message) {
    $fichierLog = '/home/bedardh25techinf/logs/curlstat.log';
    $date = date('Y-m-d H:i:s');
    $texte = "[$date] $message" . PHP_EOL;

    file_put_contents($fichierLog, $texte, FILE_APPEND);
}


function insertUtilisateur($email, $mot_de_passe) {
    try {
        $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $pdo = getConnexionBdCreation();
        $requete = $pdo->prepare("INSERT INTO utilisateurs (email, mot_de_passe) VALUES (:email, :mot_de_passe)");
        $requete->bindParam(':email', $email);
        $requete->bindParam(':mot_de_passe', $hashedPassword);
        $requete->execute();
        logMessage("Utilisateur ajouté a la base de donnée : $email");
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l’insertion.']);
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
            logMessage("Utilisateur connecté : $email");
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Identifiants invalides']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l’insertion.']);
    }
    exit;
}

function verifierEmailExistant($email) {
    try {
        logMessage("Vérification de l'existance de l'email : $email");
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
        $requete->bindParam(':email', $email);
        $requete->execute();
        $count = $requete->fetchColumn();

        if ($count > 0) {
            echo json_encode(['status' => 'error', 'message' => 'L\'email est déjà utilisé ou est invalide']);
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
        logMessage("Récupération de l'ID avec l'email : $email");
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

function obtenirNomEvenement() {
    try {
        $Id_equipe = ObtenirIDEquipeAvecIDJoueur();
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT id_event, nom FROM evenements WHERE id_equipe = :idequipe");
        $requete->bindParam(':idequipe', $Id_equipe);
        $requete->execute();
        $evenments = $requete->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'evenement' => $evenments]);
        exit;
    } catch (Exception $e) {
        error_log("Erreur : " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Erreur BD : ' . $e->getMessage()]);
        exit;
    }
}

function ObtenirIDEquipeAvecIDJoueur() {
    try {
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT id_equipe FROM equipe_joueur WHERE id_joueur = :idjoueur");
        $requete->bindParam(':idjoueur', $_SESSION['user_id']);
        $requete->execute();
        return $requete->fetchColumn() ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function ObtenirNomEquipeAvecIDEquipe() {
    try {
        $Id_equipe = ObtenirIDEquipeAvecIDJoueur();
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT equipe_name FROM equipe WHERE id_equipe = :idequipe");
        $requete->bindParam(':idequipe',  $Id_equipe);
        $requete->execute();
        return $requete->fetchColumn() ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function insertEquipe($nom, $categorie) {
    try {
        $pdo = getConnexionBd();


        $requete = $pdo->prepare("SELECT COUNT(*) FROM equipe WHERE equipe_name = :nom");
        $requete->bindParam(':nom', $nom);
        $requete->execute();

        if ($requete->fetchColumn() > 0) {
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
        logMessage("Utilisateur $idUtilisateur a rejoint l'équipe $idEquipe");
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
        $requete = $pdo->prepare("INSERT INTO evenements (nom, date_event, id_equipe) VALUES (:nom, :date_event, :id_equipe)");
        $requete->bindParam(':nom', $nom);
        $requete->bindParam(':date_event', $date);
        $requete->bindParam(':id_equipe', $idEquipe, PDO::PARAM_INT);
        $requete->execute();
        logMessage("Événement créé : $nom pour l'équipe $idEquipe");
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function getEquipeParJoueur($idJoueur) {
    try {
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT id_equipe FROM equipe_joueur WHERE id_joueur = :id");
        $requete->bindParam(':id', $idJoueur, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchColumn();
    } catch (Exception $e) {
        return null;
    }
}

function ajouterStatistiquesEvenement($post, $user_id) {
    error_log("Début ajout statistique");

    $idEvenement = $post['evenement_id'] ?? null;
    $typeLancerNom = $post['type_lancer'] ?? null;

    error_log("ID événement reçu : " . $idEvenement);
    error_log("Type lancer reçu : " . $typeLancerNom);

    $typeMapping = [
        'Placement' => 1,
        'Sortie' => 2,
        'Raise' => 3,
        'Garde' => 4,
        'Double sortie' => 5,
        'Placement gelé' => 6
    ];

    $typeId = $typeMapping[$typeLancerNom] ?? null;

    if (!$idEvenement || !$typeId) {
        error_log("Erreur : idEvenement ou typeId manquant");
        echo json_encode(['status' => 'error', 'message' => 'Événement ou type de lancer invalide']);
        exit;
    }

    $valeurs = [];
    foreach ($post as $key => $valeur) {
        if (strpos($key, 'select_') === 0 && is_numeric($valeur)) {
            $indice = intval(str_replace('select_', '', $key)) + 1;
            $valeurs[] = ['indice' => $indice, 'valeur' => intval($valeur)];
        }
    }

    error_log("Valeurs à insérer : " . json_encode($valeurs));

    try {
       
        $pdo = getConnexionBd();

        $idEquipe = ObtenirIDEquipeAvecIDJoueur();
        error_log("ID équipe : " . $idEquipe);

        $requete = $pdo->prepare("SELECT categorie FROM equipe WHERE id_equipe = :id_equipe");
        $requete->bindParam(':id_equipe', $idEquipe);
        $requete->execute();
        $categoryId = $requete->fetchColumn();

        error_log("ID catégorie : " . $categoryId);

        if (!$idEquipe || !$categoryId) {
            error_log("Erreur : équipe ou catégorie introuvable");
            echo json_encode(['status' => 'error', 'message' => 'Équipe ou catégorie introuvable']);
            exit;
        }

        $pdo = getConnexionBdCreation();
        $requete = $pdo->prepare("
            INSERT INTO stats (category_id, type_de_lancer_id, valeur, indice, id_equipe, id_event)
            VALUES (:category_id, :type_de_lancer_id, :valeur, :indice, :id_equipe, :id_event)
        ");
        foreach ($valeurs as $v) {
            $requete->execute([
                ':category_id' => $categoryId,
                ':type_de_lancer_id' => $typeId,
                ':valeur' => $v['valeur'],
                ':indice' => $v['indice'],
                ':id_equipe' => $idEquipe,
                ':id_event' => $idEvenement
            ]);
            error_log("Insertion OK : " . json_encode($v));
        }
        logMessage("Statistiques ajoutées pour l'événement $idEvenement, équipe $idEquipe");
        echo json_encode(['status' => 'success', 'message' => 'Statistiques ajoutées']);
    } catch (Exception $e) {
        error_log("Erreur BD : " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Erreur BD : ' . $e->getMessage()]);
    }

    exit;
}

function obtenirStatParEvenement($id_event) {
    try {
        $pdo = getConnexionBd();
        $requete = $pdo->prepare("SELECT * FROM stats WHERE id_event = :event_id");
        $requete->bindParam(':event_id', $id_event, PDO::PARAM_INT);
        $requete->execute();
        $result = $requete->fetchAll();

        if ($result) {
            echo json_encode(['status' => 'success', 'stats' => $result]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Aucune statistique pour cet événement.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur BD : ' . $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'creerEquipe':
            $nom = filter_input(INPUT_POST, 'Equipe', FILTER_SANITIZE_STRING);
            $categorie = filter_input(INPUT_POST, 'select_Equipe', FILTER_SANITIZE_NUMBER_INT);
            insertEquipe($nom, $categorie);
            break;

        case 'obtenirStatEvenementEquipe':
            $eventId = filter_input(INPUT_POST, 'event_id', FILTER_SANITIZE_NUMBER_INT);
            if (!$eventId) {
                echo json_encode(['status' => 'error', 'message' => 'ID événement manquant']);
                exit;
            }

            $idEquipe = ObtenirIDEquipeAvecIDJoueur();
            $pdo = getConnexionBd();
            $requete = $pdo->prepare("SELECT * FROM stats WHERE id_event = :event AND id_equipe = :equipe");
            $requete->execute([':event' => $eventId, ':equipe' => $idEquipe]);
            $stats = $requete->fetchAll();
            echo json_encode(['status' => 'success', 'stats' => $stats]);
            exit;

        case 'obtenirEvenementsEtCategories':
            try {
                $pdo = getConnexionBd();

                $idEquipe = ObtenirIDEquipeAvecIDJoueur();
                $requete = $pdo->prepare("SELECT id_event, nom FROM evenements WHERE id_equipe = :id_equipe");
                $requete->execute([':id_equipe' => $idEquipe]);
                $evenements = $requete->fetchAll();

                $requete = $pdo->query("SELECT id, 
                    CASE id 
                        WHEN 1 THEN 'U15' 
                        WHEN 2 THEN 'U18 Garçons'
                        WHEN 3 THEN 'U18 Filles'
                        WHEN 4 THEN 'U20 Garçons'
                        WHEN 5 THEN 'U20 Filles'
                        ELSE CONCAT('Catégorie ', id)
                    END AS nom 
                    FROM categories");
                $categories = $requete->fetchAll();

                echo json_encode([
                    'status' => 'success',
                    'evenements' => $evenements,
                    'categories' => $categories
                ]);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;

        case 'ajouterStatistique':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
                exit;
            }
            ajouterStatistiquesEvenement($_POST, $_SESSION['user_id']);
            break;

        case 'obtenirStatsEvenement':
            $eventId = filter_input(INPUT_POST, 'event_id', FILTER_SANITIZE_NUMBER_INT);
            if (!$eventId) {
                echo json_encode(['status' => 'error', 'message' => 'ID événement manquant']);
                exit;
            }
            $pdo = getConnexionBd();
            $requete = $pdo->prepare("SELECT * FROM stats WHERE id_event = :event_id");
            $requete->bindParam(':event_id', $eventId);
            $requete->execute();
            $stats = $requete->fetchAll();
            echo json_encode(['status' => 'success', 'stats' => $stats]);
            exit;

        case 'obtenirMonCategorie':
            $idEquipe = ObtenirIDEquipeAvecIDJoueur();
            if (!$idEquipe) {
                echo json_encode(['status' => 'error', 'message' => 'Aucune équipe trouvée']);
                exit;
            }
            $pdo = getConnexionBd();
            $requete = $pdo->prepare("SELECT categorie FROM equipe WHERE id_equipe = :id");
            $requete->bindParam(':id', $idEquipe, PDO::PARAM_INT);
            $requete->execute();
            $categorie = $requete->fetchColumn();
            echo json_encode(['status' => 'success', 'categorie' => $categorie]);
            exit;

        case 'obtenirStat':
            $id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
            if (!$id) {
                echo json_encode(['status' => 'error', 'message' => 'ID manquant']);
                exit;
            }
            $pdo = getConnexionBd();
            $requete = $pdo->prepare("SELECT * FROM stats WHERE category_id = ?");
            $requete->execute([$id]);
            $stats = $requete->fetchAll();
            echo json_encode(['status' => 'success', 'stats' => $stats]);
            exit;

        case 'obtenirStatParCategorie':
            $categorie = filter_input(INPUT_POST, 'select_Equipe2', FILTER_SANITIZE_NUMBER_INT);
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
                $mot_de_passe = $_POST['mot_de_passe'] ?? null;
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
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $mot_de_passe = $_POST['mot_de_passe'] ?? null;
            verifierUtilisateur($email, $mot_de_passe);
            break;

        case 'obtenirStatMonEquipe':
            error_log("StatMonEquipe");
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
                exit;
            }

            $idEquipe = ObtenirIDEquipeAvecIDJoueur();
            if (!$idEquipe) {
                echo json_encode(['status' => 'error', 'message' => 'Équipe introuvable']);
                exit;
            }

            try {
                $pdo = getConnexionBd();
                $requete = $pdo->prepare("SELECT * FROM stats WHERE id_equipe = :id");
                $requete->bindParam(':id', $idEquipe, PDO::PARAM_INT);
                $requete->execute();
                echo json_encode(['status' => 'success', 'stats' => $requete->fetchAll()]);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;

        case 'verifierEmail':
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            verifierEmailExistant($email);
            break;

        case 'obtenirNomEquipe':
            obtenirNomEquipe();
            break;

        case 'obtenirNomEvenement':
            obtenirNomEvenement();
            break;

        case 'rejoindreEquipe':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
                exit;
            }
           $idEquipe = filter_input(INPUT_POST, 'select_Equipe', FILTER_SANITIZE_NUMBER_INT);
            if (!$idEquipe) {
                echo json_encode(['status' => 'error', 'message' => 'ID d’équipe manquant']);
                exit;
            }
            rejoindreEquipe($_SESSION['user_id'], $idEquipe);
            break;
        case 'creerEvenement':
            $nom = filter_input(INPUT_POST, 'nomEvenement', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $date = filter_input(INPUT_POST, 'dateEvenement', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
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
