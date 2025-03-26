const statsData = {
    "u15": {
        "Placement": [3, 4, 2, 3, 4, 4, 3, 2, 4, 3],
        "Sortie": [2, 3, 4, 3, 3, 2, 4, 3, 2, 3],
        "Raise": [4, 4, 3, 3, 2, 4, 3, 3, 2, 3],
        "Garde": [3, 3, 4, 2, 3, 4, 3, 4, 2, 3],
        "Double sortie": [2, 3, 2, 3, 4, 4, 3, 3, 2, 4],
        "Placement gelé": [3, 2, 4, 4, 3, 3, 4, 2, 3, 3]
    },
    "u18-garcons": {
        "Placement": [4, 4, 3, 3, 4, 4, 3, 4, 3, 4],
        "Sortie": [3, 4, 4, 4, 3, 3, 2, 4, 3, 3],
        "Raise": [4, 4, 3, 3, 3, 4, 3, 4, 3, 3],
        "Garde": [3, 3, 4, 3, 4, 4, 3, 4, 3, 3],
        "Double sortie": [3, 3, 4, 3, 4, 3, 4, 3, 2, 4],
        "Placement gelé": [3, 2, 4, 4, 3, 4, 4, 3, 3, 3]
    },
    "u18-filles": {
        "Placement": [3, 4, 3, 3, 2, 4, 3, 4, 2, 3],
        "Sortie": [2, 3, 4, 3, 3, 3, 4, 3, 2, 4],
        "Raise": [3, 4, 3, 3, 4, 4, 3, 3, 2, 3],
        "Garde": [3, 3, 4, 4, 3, 4, 3, 4, 3, 3],
        "Double sortie": [2, 3, 4, 3, 4, 3, 4, 3, 2, 4],
        "Placement gelé": [3, 2, 4, 4, 3, 4, 4, 3, 3, 3]
    },
    "u20-garcons": {
        "Placement": [4, 4, 3, 3, 4, 4, 3, 4, 3, 4],
        "Sortie": [3, 4, 4, 4, 3, 3, 2, 4, 3, 3],
        "Raise": [4, 4, 3, 3, 3, 4, 3, 4, 3, 3],
        "Garde": [3, 3, 4, 3, 4, 4, 3, 4, 3, 3],
        "Double sortie": [3, 3, 4, 3, 4, 3, 4, 3, 2, 4],
        "Placement gelé": [3, 2, 4, 4, 3, 4, 4, 3, 3, 3]
    },
    "u20-filles": {
        "Placement": [3, 4, 3, 3, 2, 4, 3, 4, 2, 3],
        "Sortie": [2, 3, 4, 3, 3, 3, 4, 3, 2, 4],
        "Raise": [3, 4, 3, 3, 4, 4, 3, 3, 2, 3],
        "Garde": [3, 3, 4, 4, 3, 4, 3, 4, 3, 3],
        "Double sortie": [2, 3, 4, 3, 4, 3, 4, 3, 2, 4],
        "Placement gelé": [3, 2, 4, 4, 3, 4, 4, 3, 3, 3]
    }
};

const ctx = document.getElementById('statsChart').getContext('2d');
var statsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: Object.keys(statsData["u15"]),
        datasets: []
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});

var comparisonMode = true;

function toggleMode() {
    comparisonMode = !comparisonMode;
    document.getElementById('comparison-mode').style.display = comparisonMode ? 'block' : 'none';
    document.getElementById('single-mode').style.display = comparisonMode ? 'none' : 'block';
    document.getElementById('toggle-mode').textContent = comparisonMode ? 'Passer en mode Affichage' : 'Passer en mode Comparaison';
    updateChart();
}

function updateChart() {
    statsChart.data.datasets = [];
    if (comparisonMode) {
        const niveau1 = document.getElementById('niveau-select-1').value;
        const niveau2 = document.getElementById('niveau-select-2').value;
        let stats1;
        if (niveau1 === 'mon-equipe'){
            stats1 = obtenirStatMonEquipe();
        }
        else{
            stats1 = statsData[niveau1];}

        alert(stats1);
        
        
        const stats2 = statsData[niveau2];
        alert(stats2)
        const moyennes1 = Object.values(stats1).map(scores => (scores.reduce((acc, val) => acc + val, 0) / scores.length) * 25);
        const moyennes2 = Object.values(stats2).map(scores => (scores.reduce((acc, val) => acc + val, 0) / scores.length) * 25);
        
        statsChart.data.datasets.push(
            { label: niveau1, data: moyennes1, backgroundColor: 'rgba(54, 162, 235, 0.5)' },
            { label: niveau2, data: moyennes2, backgroundColor: 'rgba(255, 99, 132, 0.5)' }
        );
    } else {
        const niveau = document.getElementById('niveau-select-single').value;
        const stats = statsData[niveau];
        const moyennes = Object.values(stats).map(scores => (scores.reduce((acc, val) => acc + val, 0) / scores.length) * 25);
        
        statsChart.data.datasets.push(
            { label: niveau, data: moyennes, backgroundColor: 'rgba(75, 192, 192, 0.5)' }
        );
    }
    statsChart.update();
}
function obtenirStatMonEquipe(){
        alert('DansObtenirStatMonEquipe');
        const formData = new FormData();
        formData.append('action', 'obtenirStatMonEquipe');
        fetch('data.php', {
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
                alert(data.stats['Placement']);
                return data.stats;
            } else {
                alert('Erreur : ' + data.message);
            }
        }).catch(error => {
            console.error('Erreur lors de la requête fetch:', error);
        });
    ;
}
document.getElementById('toggle-mode').addEventListener('click', toggleMode);
document.getElementById('niveau-select-1').addEventListener('change', updateChart);
document.getElementById('niveau-select-2').addEventListener('change', updateChart);
document.getElementById('niveau-select-single').addEventListener('change', updateChart);



function ouvrirFormulaireCreationEquipe() {
    document.getElementById("FormulaireCreationEquipe").style.display = "block";
}

function fermerFormulaireCreationEquipe() {
    document.getElementById("FormulaireCreationEquipe").style.display = "none";
}

function ouvrirFormulaireAjoutStatistique(joueur) {
    document.getElementById("FormulaireChoixDuJoueur").style.display = "none";
    document.getElementById("FormulaireAjoutStatistique").style.display = "block";
    document.getElementById("Joueur-ajout-de-stat");
    document.textContent = joueur;
    console.log(joueur)
}
function fermerFormulaireAjoutStatistique() {
    document.getElementById("FormulaireAjoutStatistique").style.display = "none";
}

function ouvrirFormulaireChoixDuJoueur(premier,deuxieme,troisieme,quatrieme) {
    const select = document.getElementById("JoueurChoisi");
    select.children[0].textContent = premier;
    select.children[1].textContent = deuxieme;
    select.children[2].textContent = troisieme;
    select.children[3].textContent = quatrieme;
    document.getElementById("FormulaireChoixDuJoueur").style.display = "block";
}
function addNewSelect(selectElement) {
    const selectList = document.getElementById('selectList');
    
    if (selectElement.value !== 'null') {
        const newSelect = document.createElement('li');
        newSelect.innerHTML = `
            <select class="select-item" onchange="addNewSelect(this)">
                <option value="null" selected>Choisir...</option>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        `;
        selectList.appendChild(newSelect);
    }
}