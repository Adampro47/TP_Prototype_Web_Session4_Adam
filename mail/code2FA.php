<?php
// 8192794516@msg.telus.com
//require_once 'env.php';

// $destinataire = "claude.boutet@cegepat.qc.ca";
// $destinataire = getenv("SMS");
function EnvoyerCode($numer0_destinataire){
    //$destinataire = "8192794516@msg.telus.com";
    error_log($numer0_destinataire);
    $destinataire = $numer0_destinataire.'@msg.telus.com';
    error_log($destinataire);
    $code = rand(100000,999999);

    session_start();
    $_SESSION['code'] = $code;

    if (envoyerMail($destinataire, "Votre code est : ".$code)) {
        //echo "<p>Message envoyé à ". $destinataire."</p>";
        return $code;
    } 
    else {
        //echo "<p>Message non envoyé à ". $destinataire."</p>";
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
        $action = $_POST['action'];
        if ($action == 'EnvoyerCode') {
            $telephone = $_POST['telephone'];
            error_log($telephone);
            $code = EnvoyerCode($telephone);
            if ($code != null) {
                echo json_encode(['status' => 'success', 'code' => $code]);
            }
            else {
                $post_string = http_build_query($_POST);
                echo json_encode(['status' => 'error', 'message' => 'le code a pas envoyer']);
            }
        } 
    } else {
        echo json_encode(['status' => 'error', 'message' => 'L\'action est manquante!']);
    }
}
?>