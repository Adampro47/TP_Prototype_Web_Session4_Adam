<?php
// 8192794516@msg.telus.com
//require_once 'env.php';

// $destinataire = "claude.boutet@cegepat.qc.ca";
// $destinataire = getenv("SMS");

function EnvoyerCode($adresse_destinataire){
    //$destinataire = "8192794516@msg.telus.com";
    $code = rand(100000,999999);
    
    // Stocke le code dans la session
    

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
            if ($creer_code != null) {
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
                $_SESSION['code'] = $creer_code;
                echo json_encode(['status' => 'success','message' => 'Code envoyé']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Le code n\'a pas été envoyé']);
            }
        }
        elseif ($action == 'VerifierCode') {
            $code_entree = filter_input(INPUT_POST,"code",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
            if (isset($_SESSION['code'])) {
                error_log("VerifierCode-code2FA");
                
                $code = $_SESSION['code'];
                error_log($code);
                if ($code_entree == $code) {
                    error_log("VerifierCode: Code correct");
                    $_SESSION['code_verifié'] = true;
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
