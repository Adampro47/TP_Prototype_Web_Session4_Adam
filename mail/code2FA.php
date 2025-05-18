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

function EnvoyerCode($adresse_destinataire){
    $code = rand(100000, 999999);

    if (envoyerMail($adresse_destinataire, "Votre code est : " . $code)) {
        return $code;
    } else {
        return null;
    }
}

function envoyerMail($to, $message) {
    $subject = 'Code de vérification';
    $headers = 
        'From: bedardh25@techinfo420.ca' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    return mail($to, $subject, $message, $headers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if (!$action) {
        echo json_encode(['status' => 'error', 'message' => 'L\'action est manquante!']);
        exit;
    }

    if ($action === 'EnvoyerCode') {
        $mail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $mail = filter_var($mail, FILTER_VALIDATE_EMAIL);

        if (!$mail) {
            echo json_encode(['status' => 'error', 'message' => 'Email invalide']);
            exit;
        }

        $creer_code = EnvoyerCode($mail);

        if ($creer_code !== null) {
            $_SESSION['code'] = $creer_code;
            echo json_encode(['status' => 'success', 'message' => 'Code envoyé']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Le code n\'a pas été envoyé']);
        }

    } elseif ($action === 'VerifierCode') {
        $code_entree = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_NUMBER_INT);

        if (isset($_SESSION['code'])) {
            if ((int)$code_entree === (int)$_SESSION['code']) {
                $_SESSION['code_verifié'] = true;
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Mauvais code']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Code non trouvé']);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Action invalide']);
    }
}
?>
