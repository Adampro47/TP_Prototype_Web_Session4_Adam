<?php

// 8192794516@msg.telus.com
//require_once 'env.php';

// $destinataire = "claude.boutet@cegepat.qc.ca";
// $destinataire = getenv("SMS");

function EnvoyerCode($adresse_destinataire){
    //$destinataire = "8192794516@msg.telus.com";
    $code = rand(100000,999999);
    
    // Démarre la session avant d'utiliser $_SESSION
    session_start();
    
    // Stocke le code dans la session
    $_SESSION['code'] = $code;

    if (envoyerMail($adresse_destinataire, "Votre code est : ".$code)) {
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = filter_input(INPUT_POST,"action",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($action == 'EnvoyerCode') {
            $mail = filter_input(INPUT_POST,"email",FILTER_VALIDATE_EMAIL);
            $creer_code = EnvoyerCode($mail);
            error_log("code".$creer_code);
            // Log seulement si $creer_code n'est pas null
            if ($creer_code !== null) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Le code n\'a pas été envoyé']);
            }
        }
        elseif ($action == 'VerifierCode') {
            $code_entree = $_POST['code'];
            session_start();
            if (isset($_SESSION['code'])) {
                $code = $_SESSION['code'];
                error_log("Code reçu: " . $code_entree);
                error_log("Code stocké: " . $code);
                if ($code_entree == $code) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Mauvais code']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Code non trouvé']);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'L\'action est manquante!']);
    }
}
?>
