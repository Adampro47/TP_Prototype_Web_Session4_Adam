function getCategoryNumber(categoryName) {
    switch (categoryName) {
        case 'u15': return 1;
        case 'u18-garcons': return 2;
        case 'u18-filles': return 3;
        case 'u20-garcons': return 4;
        case 'u20-filles': return 5;
        default:
            console.error('Catégorie non reconnue :', categoryName);
            return -1;
    }
}

async function getMonEquipeCategoryId() {
    const formData = new FormData();
    formData.append('action', 'obtenirMonCategorie');

    try {
        const response = await fetch('data.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.status === 'success') return data.categorie;
        console.error('Erreur pour obtenir la catégorie :', data.message);
    } catch (err) {
        console.error('Erreur fetch getMonEquipeCategoryId :', err);
    }
    return -1;
}

function extraireTypeEtId(valeur) {
    if (!valeur.includes(':')) return { type: null, id: null };

    const [type, id] = valeur.split(':');
    return { type: type === 'm' ? 'equipe' : type, id };
}

async function obtenirStatEquipe() {
    const formData = new FormData();
    formData.append('action', 'obtenirStatMonEquipe');

    try {
        const response = await fetch('data.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            console.error("Erreur HTTP :", response.status);
            return null;
        }

        const data = await response.json();

        if (data.status === 'success') {
            const noms = {
                1: "Placement", 2: "Sortie", 3: "Raise",
                4: "Garde", 5: "Double sortie", 6: "Placement gelé"
            };

            const resultat = {};
            for (const item of data.stats) {
                const nom = noms[item.type_de_lancer_id];
                if (!resultat[nom]) resultat[nom] = [];
                resultat[nom][item.indice - 1] = item.valeur;
            }
            return resultat;
        } else {
            console.error("Statut erreur renvoyé par le serveur :", data.message);
        }
    } catch (err) {
        console.error("Erreur lors de fetch obtenirStatEquipe :", err);
    }

    return null;
}

async function obtenirStat(idCategorie) {
    const formData = new FormData();
    formData.append('action', 'obtenirStat');
    formData.append('category_id', idCategorie);

    try {
        const response = await fetch('data.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) throw new Error("HTTP error " + response.status);
        const data = await response.json();

        if (data.status === 'success') {
            const noms = {
                1: "Placement", 2: "Sortie", 3: "Raise",
                4: "Garde", 5: "Double sortie", 6: "Placement gelé"
            };

            const resultat = {};
            for (const item of data.stats) {
                const nom = noms[item.type_de_lancer_id];
                if (!resultat[nom]) resultat[nom] = [];
                resultat[nom][item.indice - 1] = item.valeur;
            }

            return resultat;
        } else {
            console.error("Erreur serveur obtenirStat :", data.message);
        }
    } catch (err) {
        console.error("Erreur réseau obtenirStat :", err);
    }
    return null;
}

async function obtenirStatParEvenement(eventId) {
    const formData = new FormData();
    formData.append('action', 'obtenirStatEvenementEquipe');
    formData.append('event_id', eventId);

    try {
        const response = await fetch('data.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error("Erreur HTTP : " + response.status);
        }

        const data = await response.json();

        if (data.status === 'success') {
            const noms = {
                1: "Placement",
                2: "Sortie",
                3: "Raise",
                4: "Garde",
                5: "Double sortie",
                6: "Placement gelé"
            };

            const resultat = {};

            for (const item of data.stats) {
                const nom = noms[item.type_de_lancer_id];
                if (!resultat[nom]) {
                    resultat[nom] = [];
                }
                resultat[nom][item.indice - 1] = item.valeur;
            }

            return resultat;
        } else {
            console.error("Erreur serveur (obtenirStatParEvenement) :", data.message);
            return null;
        }

    } catch (error) {
        console.error("Erreur réseau (obtenirStatParEvenement) :", error);
        return null;
    }
}


function calculMoyenneParType(data) {
    const types = ["Placement", "Sortie", "Raise", "Garde", "Double sortie", "Placement gelé"];
    return types.map(type => {
        const valeurs = data[type];
        if (!Array.isArray(valeurs) || !valeurs.length) {
            return 0;
        }
        const somme = valeurs.reduce((a, b) => a + b, 0);
        return (somme / valeurs.length) * 25;
    });
}

async function updateChart() {
    statsChart.data.datasets = [];

    const raw1 = document.getElementById('niveau-select-1')?.value;
    const raw2 = document.getElementById('niveau-select-2')?.value;
    if (!raw1 || !raw2 || raw1 === 'null' || raw2 === 'null') return;

    const obj1 = extraireTypeEtId(raw1);
    const obj2 = extraireTypeEtId(raw2);
    const getStats = async (obj) => {
        switch (obj.type) {
            case 'e': return await obtenirStatParEvenement(obj.id);
            case 'c': return await obtenirStat(obj.id);
            case 'equipe': return await obtenirStatEquipe();
            default: return null;
        }
    };

    const stats1 = await getStats(obj1);
    const stats2 = await getStats(obj2);
    if (!stats1 || !stats2) {
        alert("Erreur : données manquantes");
        return;
    }

    const moyennes1 = nettoyerMoyennes(calculMoyenneParType(stats1));
    const moyennes2 = nettoyerMoyennes(calculMoyenneParType(stats2));
    if (moyennes1.some(isNaN) || moyennes2.some(isNaN)) {
        console.error("Une des moyennes contient une valeur invalide", moyennes1, moyennes2);
        alert("Erreur : une des sources de données contient des valeurs invalides.");
        return;
    }
    
    statsChart.data.labels = ["Placement", "Sortie", "Raise", "Garde", "Double sortie", "Placement gelé"];
    statsChart.data.datasets = [
        {
            label: `Choix 1`,
            data: moyennes1,
            backgroundColor: 'rgba(54, 162, 235, 0.5)'
        },
        {
            label: `Choix 2`,
            data: moyennes2,
            backgroundColor: 'rgba(255, 99, 132, 0.5)'
        }
    ];
    if (!statsChart.data.labels || statsChart.data.labels.length === 0) {
        console.error("Erreur critique : labels manquants pour le graphique.");
        return;
    }
    statsChart.update();
}

function nettoyerMoyennes(moyennes) {
    return moyennes.map(val => (isFinite(val) && !isNaN(val)) ? val : 0);
}

const ctx = document.getElementById('statsChart')?.getContext('2d');
if (ctx) {
    var statsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                tooltip: {
                    enabled: false
                }
            }
        }
    });
} else {
    console.error("Canvas 'statsChart' introuvable.");
}


const originalConsoleError = console.error;
console.error = function (...args) {
    if (
        typeof args[0] === 'string' &&
        args[0].includes("Cannot read properties of null (reading 'getLabelAndValue')")
    ) {
        return;
    }
    originalConsoleError.apply(console, args);
};


document.getElementById('niveau-select-1')?.addEventListener('change', () => updateChart());
document.getElementById('niveau-select-2')?.addEventListener('change', () => updateChart());

