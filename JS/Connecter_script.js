window.addEventListener('load', function () {
    fetch('data.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'verifierRejoignable' })
    }).then(response => response.json())
      .then(data => {
        const btnRejoindre = document.getElementById('RejoindreEquipe');
        const btnAjouter = document.getElementById('AjouterStatistique');
        const btnCreerEvent = document.querySelector('button[onclick="ouvrirFormulaireCreationEvenement()"]');
        const btnCreer = document.getElementById('creerEquipe');

        if (data.status === 'success') {
            if (data.dejaRejoint) {
                if (btnRejoindre) {
                    btnRejoindre.disabled = true;
                    btnRejoindre.classList.add('disabled');
                    btnRejoindre.textContent = "Équipe déjà rejointe";
                }
                if (btnCreer) {
                btnCreer.disabled = true;
                btnCreer.classList.add('disabled');
                btnCreer.textContent = "Déjà dans une équipe";
            }
            } else {
                if (btnAjouter) {
                    btnAjouter.disabled = true;
                    btnAjouter.classList.add('disabled');
                    btnAjouter.textContent = "Rejoignez une équipe d’abord";
                }
                if (btnCreerEvent) {
                    btnCreerEvent.disabled = true;
                    btnCreerEvent.classList.add('disabled');
                    btnCreerEvent.textContent = "Rejoignez une équipe d’abord";
                }
            }
        }
    });
});

const formChoixEvenement = document.getElementById('FormulaireChoixEvenement');
if (formChoixEvenement) {
    formChoixEvenement.addEventListener('submit', function (event) {
        event.preventDefault();

        const evenementSelect = document.getElementById("nomEvenement");
        const nomEvenementTexte = evenementSelect.options[evenementSelect.selectedIndex].text;

        if (evenementSelect.value === "null") {
            alert("Veuillez choisir un événement.");
            return;
        }


        ouvrirFormulaireAjoutStatistique(nomEvenementTexte);
    });
}
function ouvrirFormulaireCreationEquipe() {
    const form = document.getElementById("FormulaireCreationEquipe");
    if (form) {
        form.style.display = "block";
    }
}
function ouvrirFormulaireJoindreEquipe() {
    const form = document.getElementById("FormulaireRejoindreEquipe");
    const select = document.getElementById("nomEquipe");

    select.innerHTML = `<option value="null">---</option>`;

    const formData = new FormData();
    formData.append('action', 'obtenirNomEquipe');

    fetch('data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            data.equipes.forEach(equipe => {
                const option = document.createElement('option');
                option.value = equipe.id_equipe;
                option.textContent = equipe.equipe_name;
                select.appendChild(option);
            });
            form.style.display = "block";
        } else {
            alert("Erreur : " + data.message);
        }
    })
    .catch(error => {
        console.error("Erreur lors du fetch :", error);
        alert("Erreur de chargement des équipes.");
    });
}

const formEvent = document.getElementById('formCreationEvenement');
if (formEvent) {
    formEvent.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'creerEvenement');
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

const btnAjouterStat = document.getElementById('AjouterStatistique');
if (btnAjouterStat) {
    btnAjouterStat.addEventListener('click', function (event) {
        obtenirNomEvenement();
    });
}

const creationForm = document.getElementById('formCreationEquipe');
if (creationForm) {
    creationForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'creerEquipe');

        fetch('data.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
            .then(data => {
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
        const formData = new FormData();


        formData.append('action', 'ajouterStatistique');
        formData.append('type_lancer', document.getElementById('type-lancer').value);
        formData.append('evenement_id', this.dataset.evenement);

        document.querySelectorAll('#selectList .select-item').forEach(select => {
            if (select.name.startsWith('select_') && select.value !== 'null' && select.value !== '') {
                formData.append(select.name, select.value);
            }
        });
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

function fermerFormulaireCreationEquipe() {
    const form = document.getElementById("FormulaireCreationEquipe");
    if (form) {
        form.style.display = "none";
        document.getElementById("formCreationEquipe").reset();
    }
}
function ouvrirFormulaireCreationEvenement() {
    const formDiv = document.getElementById("FormulaireCreationEvenement");
    if (formDiv) formDiv.style.display = "block";
}

function ouvrirFormulaireAjoutStatistique() {
    document.getElementById('selectList').dataset.index = 1;
    document.getElementById('selectList').innerHTML = `
        <li>
            <select name="select_0" class="select-item" onchange="addNewSelect(this)">
                <option value="null" selected>Choisir...</option>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </li>
    `;

    document.getElementById("popupFormulaireChoixEvenement").style.display = "none";
    document.getElementById("FormulaireAjoutStatistique").style.display = "block";

    const evenementSelect = document.getElementById("nomEvenement");
    if (!evenementSelect || evenementSelect.selectedIndex === -1) {
        console.error("Événement non sélectionné");
        return;
    }

    const nomEvenementTexte = evenementSelect.options[evenementSelect.selectedIndex].text;


    const label = document.getElementById("Joueur-ajout-de-stat");
    label.textContent = `Ajouter des statistiques au tournoi : ${nomEvenementTexte}`;


    document.getElementById("formAjoutStatistique").dataset.evenement = evenementSelect.value;
}

function obtenirNomEvenement() {
    const formData = new FormData();
    formData.append('action', 'obtenirNomEvenement');
    
    fetch('data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Erreur de serveur : ' + response.statusText);
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            const select = document.getElementById("nomEvenement");
            select.innerHTML = "";
            data.evenement.forEach(evenement => {
                const option = document.createElement("option");
                option.value = evenement.id_event;
                option.textContent = evenement.nom;
                select.appendChild(option);
            });
            document.getElementById("popupFormulaireChoixEvenement").style.display = "block";
        } else {
            alert('Erreur : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur lors de la requête fetch:', error);
    });
}

function addNewSelect(selectElement) {
    const selectList = document.getElementById('selectList');

    if (selectElement.value !== 'null') {
        const currentIndex = parseInt(selectList.dataset.index || '1');

        const newLi = document.createElement('li');
        newLi.innerHTML = `
            <select name="select_${currentIndex}" class="select-item" onchange="addNewSelect(this)">
                <option value="null" selected>Choisir...</option>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        `;

        selectList.appendChild(newLi);
        selectList.dataset.index = currentIndex + 1;
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

        const optGroupEquipe = document.createElement('optgroup');
        optGroupEquipe.label = "Équipe";
        const optMonEquipe = document.createElement('option');
        optMonEquipe.value = 'equipe:mon_equipe';
        optMonEquipe.textContent = 'Mon équipe (tous les événements)';
        optGroupEquipe.appendChild(optMonEquipe);
        select.appendChild(optGroupEquipe);

        const optGroupEv = document.createElement('optgroup');
        optGroupEv.label = "Mes événements";

        const optGroupCat = document.createElement('optgroup');
        optGroupCat.label = "Catégories";

        data.evenements.forEach(ev => {
            const option = document.createElement('option');
            option.value = `e:${ev.id_event}`;
            option.textContent = ev.nom;
            optGroupEv.appendChild(option);
        });

        data.categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = `c:${cat.id}`;
            option.textContent = cat.nom;
            optGroupCat.appendChild(option);
        });

        select.appendChild(optGroupCat);
        select.appendChild(optGroupEv);
    });
}
function fermerFormulaireAjoutStatistique() {
    document.getElementById("FormulaireAjoutStatistique").style.display = "none";
    document.getElementById("selectList").innerHTML = "";
    document.getElementById("selectList").dataset.index = "1";
}
window.addEventListener('load', () => {
    chargerOptionsComparaison();
});
