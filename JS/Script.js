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
    //var telephone = document.getElementById("telephonecreation").value; 
    //alert(telephone);     
    if (connexion[email] != undefined) {
        alert("Email déjà utilisé");
    } 
    else {
        connexion[email] = mdp;
    }
    document.getElementById("emailCreation").value = ""; 
    document.getElementById("pswCreation").value = "";  
    var code = EnvoyerCode(email);
    if (code = null){
        alert("code null");
        return;
    }
    document.getElementById("Verifier2FA").style.display = "block";
}
function EnvoyerCode(email) {  
        const formData = new FormData();
        formData.append('action', 'EnvoyerCode');
        formData.append('email', email);
        console.log(formData[email]);
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
            } else {
                alert('Erreur : ' + data.message);
            }
        }).catch(error => {
            console.error('Erreur lors de la requête fetch:', error);
        });
}
function VerifierCode() {
        alert("VerifierCode");
        var code = document.getElementById("code2fa").value; 
        const formData = new FormData();
        formData.append('code', code);
        formData.append('action', 'VerifierCode');
        console.log(formData[code]);
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
                alert("OK");
                document.getElementById("Verifier2FA").style.display = "none";
                document.getElementById("FormulaireCreation").style.display = "none";
            } else {
                alert('Erreur : ' + data.message);
            }
        }).catch(error => {
            
            console.error('Erreur lors de la requête fetch:', error);
        });
}
function AjouterCompteBD() {
    var email = document.getElementById("emailCreation").value; 
    var mdp = document.getElementById("pswCreation").value;
    const formData = new FormData();
    formData.append('action', 'EnvoyerCode');
    formData.append('mot_de_passe', mdp);
    console.log(formData[email]);
    fetch('./data.php', {
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
            alert("Le compte à été créer avec succès");
        } else {
            alert('Erreur : ' + data.message);
        }
    }).catch(error => {
        
        console.error('Erreur lors de la requête fetch:', error);
    });
}