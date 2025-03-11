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
    if (connexion[email] != undefined) {
        alert("Email déjà utilisé");
    } 
    else {
        connexion[email] = mdp;
        alert("Compte créé avec succès !");
    }
    document.getElementById("emailCreation").value = ""; 
    document.getElementById("pswCreation").value = "";  
    document.getElementById("FormulaireCreation").style.display = "none"; 
}