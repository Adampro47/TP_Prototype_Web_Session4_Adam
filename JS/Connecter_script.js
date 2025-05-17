window.addEventListener('load', function () {
    fetch('data.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'verifierRejoignable' })
    }).then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.dejaRejoint) {
                const btn = document.getElementById('RejoindreEquipe');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('disabled');
                    btn.textContent = "Équipe déjà rejointe";
                }
            }
        });
});

const formEvent = document.getElementById('formCreationEvenement');
if (formEvent) {
    formEvent.addEventListener('submit', function (event) {
        alert("test");
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'creerEvenement');
        alert("test1");

        fetch('data.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Événement créé.');
                    document.getElementById("FormulaireCreationEvenement").style.display = "none";
                } else {
                    alert('Erreur : ' + data.message);
                }
            });
    });
}

const formRejoindre = document.getElementById('formRejoindreEquipe');
if (formRejoindre) {
    formRejoindre.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'rejoindreEquipe');

        fetch('data.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    document.getElementById("formRejoindreEquipe").style.display = "none";
                    const btn = document.getElementById('RejoindreEquipe');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('disabled');
                        btn.textContent = "Équipe déjà rejointe";
                    }
                } else {
                    alert('Erreur : ' + data.message);
                }
            }).catch(error => {
                console.error('Erreur fetch :', error);
            });
    });
}

const testForm = document.getElementById('formtestsql');
if (testForm) {
    testForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData();
        formData.append('action', 'obtenirStatCategorie');

        fetch('data.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (!response.ok) throw new Error('Erreur de serveur : ' + response.statusText);
            return response.json();
        }).then(data => {
            if (data.status === 'success') {
                alert(JSON.stringify(data.stat));
            } else {
                alert('Erreur : ' + data.message);
            }
        }).catch(error => {
            console.error('Erreur lors de la requête fetch:', error);
        });
    });
}

const btnAjouterStat = document.getElementById('AjouterStatistique');
if (btnAjouterStat) {
    btnAjouterStat.addEventListener('click', function (event) {
        event.preventDefault();
        const formData = new FormData();
        formData.append('action', 'obtenirNomJoueur');

        fetch('data.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (!response.ok) throw new Error('Erreur de serveur : ' + response.statusText);
            return response.json();
        }).then(data => {
            if (data.status === 'success') {
                ouvrirFormulaireChoixDuJoueur(data.premier, data.deuxieme, data.troisieme, data.quatrieme);
            } else {
                alert('Erreur : ' + data.message);
            }
        }).catch(error => {
            console.error('Erreur lors de la requête fetch:', error);
        });
    });
}

const choixForm = document.getElementById('formChoixDuJoueur');
if (choixForm) {
    choixForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const selectElement = document.getElementById('JoueurChoisi');
        const joueurchoisiTexte = selectElement.options[selectElement.selectedIndex].text;
        ouvrirFormulaireAjoutStatistique(joueurchoisiTexte);
    });
}

const creationForm = document.getElementById('formCreationEquipe');
if (creationForm) {
    creationForm.addEventListener('submit', function (event) {
        event.preventDefault();
        alert("creer equipe");
        const formData = new FormData(this);
        formData.append('action', 'creerEquipe');

        fetch('data.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
            .then(data => {
                console.log('Réponse serveur:', data);
                if (data.status === 'success') {
                    fermerFormulaireCreationEquipe();
                    alert(data.message || "Équipe créée.");
                } else {
                    alert('Erreur : ' + data.message);
                }
            }).catch(error => {
                console.error('Erreur fetch :', error);
            });
    });
}

const ajoutStatForm = document.getElementById('formAjoutStatistique');
if (ajoutStatForm) {
    ajoutStatForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        const selectItems = document.querySelectorAll('.select-item');
        selectItems.forEach((select, index) => {
            if (select.value !== 'null' && select.value !== '') {
                formData.append(`select_${index}`, select.value);
            }
        });

        formData.append('action', 'ajouterStatistique');

        fetch('data.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (!response.ok) throw new Error('Erreur de serveur : ' + response.statusText);
            return response.json();
        }).then(data => {
            if (data.status === 'success') {
                alert(data.message);
                fermerFormulaireAjoutStatistique();
            }
        }).catch(error => {
            console.error('Erreur lors de la requête fetch:', error);
        });
    });
}

// Fonction en dehors du load
function ouvrirFormulaireCreationEvenement() {
    const formDiv = document.getElementById("FormulaireCreationEvenement");
    if (formDiv) formDiv.style.display = "block";
}
