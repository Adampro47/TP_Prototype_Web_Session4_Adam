var connexion = {
    "email@test.test":"Test",
    "test@email.test":"Test2",
    "a":"a"
}
function ouvrirFormulaireConnexion() {
    document.getElementById("FormulaireConnexion").style.display = "block";
    document.getElementById("FormulaireCreation").style.display = "none"; 
  }
function ouvrirFormulaireCreation() {
    document.getElementById("FormulaireCreation").style.display = "block";
    document.getElementById("FormulaireConnexion").style.display = "none"; 
}

function VerifierConnexion() {
    var email = document.getElementById("email").value;
    var mdp = document.getElementById("psw").value;

    if (connexion[email] != undefined) {
        if (mdp == connexion[email]){
            window.location.href = "Connecter.php";
        } else {alert("Mot de passe incorrect");}

    } else {
        alert("Email non trouver");
    }
}
function AjouterCompte() {
    var email = document.getElementById("emailCreation").value; 
    var mdp = document.getElementById("pswCreation").value;      
    var telephone = document.getElementById("telephonecreation").value; 
    alert(telephone);     
    if (connexion[email] != undefined) {
        alert("Email déjà utilisé");
    } 
    else {
        connexion[email] = mdp;
    }
    document.getElementById("emailCreation").value = ""; 
    document.getElementById("pswCreation").value = "";  
    document.getElementById("FormulaireCreation").style.display = "none";
    EnvoyerCode(telephone);
    document.getElementById("Verifier2FA_tel").style.display = "block";
}
function EnvoyerCode(telephone) {
    alert("Dans Envoyer code");
        
        const formData = new FormData();
        formData.append('action', 'EnvoyerCode');
        formData.append('telephone', telephone);
        console.log(formData[telephone]);
        alert("Envoi vers code2fa");
        fetch('./mail/code2FA.php', {
            method: 'POST',
            body: formData
        
        }).then(response => {
            if (!response.ok) {
                throw new Error('Erreur de serveur : ' + response.statusText);
            }
            return response.json();
        }).then(data => {
            console.log('Réponse du serveur :', data);
            if (data.status === 'success') {
                console.log(data.code);
                alert(JSON.stringify(data.code));
            } else {
                alert('Erreur : ' + data.message);
            }
        }).catch(error => {
            
            console.error('Erreur lors de la requête fetch:', error);
        });
}