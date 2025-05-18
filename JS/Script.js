window.addEventListener('error', function (e) {
    if (e.message.includes("Cannot read properties of null (reading 'getLabelAndValue')")
    ) {
        e.preventDefault();
    }
});
let emailTemp = "";
let mdpTemp = "";
function ouvrirFormulaireConnexion() {
    document.getElementById("FormulaireConnexion").style.display = "block";
    document.getElementById("FormulaireCreation").style.display = "none"; 
  }
function ouvrirFormulaireCreation() {
    document.getElementById("FormulaireCreation").style.display = "block";
    document.getElementById("FormulaireConnexion").style.display = "none"; 
}

//////////////////////////////////////////////////////////////////
// Verification si le compte auquel je veux me connecter existe
//////////////////////////////////////////////////////////////////

function VerifierConnexion() {
    const email = document.getElementById('email').value;
    const mot_de_passe = document.getElementById('psw').value;

    const formData = new FormData();
    formData.append('action', 'verifierConnexion');
    formData.append('email', email);
    formData.append('mot_de_passe', mot_de_passe);

    fetch('./data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur de serveur : ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log('Réponse du serveur :', data);
        if (data.status === 'success') {
            EnvoyerCode(email);
            document.getElementById('FormulaireConnexion').style.display = 'none';
            document.getElementById('FormulaireVerifier2FAConnexion').style.display = 'block';
        } else {
            alert('Erreur de connexion : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur lors de la requête fetch:', error);
    });
}



////////////////////////////////////
// Partie pour la 2FA
////////////////////////////////////


function EnvoyerCode(email) {  
    const formData = new FormData();
    formData.append('action', 'EnvoyerCode');
    formData.append('email', email);
    fetch('./mail/code2FA.php', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (!response.ok) {
            throw new Error('Erreur de serveur : ' + response.statusText);
        }
        return response.json();
    }).then(data => {
        if (data.status === 'success') {
            return true;
        }
    }).catch(error => {
        console.error('Erreur lors de la requête fetch:', error);
    });
}

function VerifierCode(event) {
    event.preventDefault();
    var code = document.getElementById("code2fa").value; 
    const formData = new FormData();
    formData.append('code', code);
    formData.append('action', 'VerifierCode');
    fetch('./mail/code2FA.php', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (!response.ok) {
            throw new Error('Erreur de serveur : ' + response.statusText);
        }
        return response.json();
    }).then(data => {
        if (data.status === 'success') {
            AjouterCompteBD();
            window.location.href = "Connecter.php";
        }
        else {
            alert('Erreur de connexion : ' + data.message);
        }
    }).catch(error => {
        
        console.error('Erreur lors de la requête fetch:', error);
    });
}

function VerifierCodeConnexion(event) {
    event.preventDefault();
    var code = document.getElementById("code2faConnexion").value; 
    const formData = new FormData();
    formData.append('code', code);
    formData.append('action', 'VerifierCode');
    fetch('./mail/code2FA.php', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (!response.ok) {
            throw new Error('Erreur de serveur : ' + response.statusText);
        }
        return response.json();
    }).then(data => {
        if (data.status === 'success') {
            window.location.href = "Connecter.php";
        }
        else {
            alert('Erreur de connexion : ' + data.message);
        }
    }).catch(error => {
        
        console.error('Erreur lors de la requête fetch:', error);
    });
}

////////////////////////////////////
// Partie pour la création du compte
////////////////////////////////////

async function AjouterCompte() {
    var email = document.getElementById("emailCreation").value; 
    var mdp = document.getElementById("pswCreation").value;

    if (mdp == "") {
        alert("Il faut un mot de passe");
        return;
    }

    emailTemp = email;
    mdpTemp = mdp;

    const emailValide = await VerifierEmail();
    if (!emailValide) {
        return;
    }

    EnvoyerCode(email);
    document.getElementById("FormulaireCreation").style.display = "none"; 
    document.getElementById("FormulaireVerifier2FA").style.display = "block";
}

function AjouterCompteBD() {
    const formData = new FormData();
    formData.append('action', 'AjouterCompteBD');
    formData.append('mot_de_passe', mdpTemp);
    formData.append('email', emailTemp);
    mdpTemp = "";
    emailTemp = "";
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
        } else {
            alert('Erreur : ' + data.message);
        }
    }).catch(error => {
        console.error('Erreur lors de la requête fetch:', error);
    });
}

async function VerifierEmail() {
    var email = document.getElementById("emailCreation").value; 
    const formData = new FormData();
    formData.append('action', 'verifierEmail');
    formData.append('email', email);

    try {
        const response = await fetch('./data.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Erreur de serveur : ' + response.statusText);
        }

        const data = await response.json();
        console.log('Réponse du serveur :', data);

        if (data.status === 'success') {
            return true;
        } else {
            alert('Erreur : ' + data.message);
            return false;
        }
    } catch (error) {
        console.error('Erreur lors de la requête fetch:', error);
        return false;
    }
}
async function chargerOptionsComparaison() {
    const formData = new FormData();
    formData.append('action', 'obtenirEvenementsEtCategories');

    const response = await fetch('data.php', { method: 'POST', body: formData });
    const data = await response.json();
    if (data.status !== 'success') {
        console.error('Erreur chargement :', data.message);
        return;
    }

    const select1 = document.getElementById('niveau-select-1');
    const select2 = document.getElementById('niveau-select-2');
    [select1, select2].forEach(select => {
        select.innerHTML = '<option value="null">Choisir…</option>';
        const optGroupCat = document.createElement('optgroup');
        optGroupCat.label = "Catégories";

        data.categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = `c:${cat.id}`;
            option.textContent = cat.nom;
            optGroupCat.appendChild(option);
        });

        select.appendChild(optGroupCat);
    });
}
window.addEventListener('load', () => {
    chargerOptionsComparaison();
});
