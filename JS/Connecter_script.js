 ///Test sql
 document.getElementById('formtestsql').addEventListener('submit', function(event) {
    event.preventDefault();  // Empêcher le rechargement de la page
    
    const formData = new FormData();
    formData.append('action', 'obtenirStatCategorie');
    

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
            console.log(data.stat);
            alert(JSON.stringify(data.stat));  // Afficher les statistiques dans un format lisible
        } else {
            alert('Erreur : ' + data.message);
        }
    }).catch(error => {
        
        console.error('Erreur lors de la requête fetch:', error);
    });
});

        // Obtenir liste joueur
    document.getElementById('AjouterStatistique').addEventListener('click', function(event) {
        const formData = new FormData();
        event.preventDefault(); 
        formData.append('action', 'obtenirNomJoueur');
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
                ouvrirFormulaireChoixDuJoueur(data.premier,data.deuxieme,data.troisieme,data.quatrieme);
                
            } else {
                alert('Erreur : ' + data.message);
            }
        }).catch(error => {
            console.error('Erreur lors de la requête fetch:', error);
        });
    });
        /////
        document.getElementById('formChoixDuJoueur').addEventListener('submit', function(event) {
        event.preventDefault();
        var selectElement = document.getElementById('JoueurChoisi');
        var joueurchoisiTexte = selectElement.options[selectElement.selectedIndex].text;
        ouvrirFormulaireAjoutStatistique(joueurchoisiTexte);
    });
        // Obtenir liste joueur
    document.getElementById('AjouterStatistique').addEventListener('click', function(event) {
        const formData = new FormData();
        event.preventDefault(); 
        formData.append('action', 'obtenirNomJoueur');
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
                ouvrirFormulaireChoixDuJoueur(data.premier,data.deuxieme,data.troisieme,data.quatrieme);
                
            } else {
                alert('Erreur : ' + data.message);
            }
        }).catch(error => {
            console.error('Erreur lors de la requête fetch:', error);
        });
    });
        // Creer équipe
    document.getElementById('formCreationEquipe').addEventListener('submit', function(event) {
        event.preventDefault(); 
        const formData = new FormData(this);
        formData.append('action', 'creerEquipe'); 
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
                fermerFormulaireCreationEquipe();
                alert(data.message); 
            } else {
                alert('Erreur : ' + data.message);
            }
        }).catch(error => {
            console.error('Erreur lors de la requête fetch:', error);
        });
    });

    // Envoyer stat

    document.getElementById('formAjoutStatistique').addEventListener('submit', function(event) {
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
        if (!response.ok) {
            throw new Error('Erreur de serveur : ' + response.statusText);
        }
        return response.json();
    }).then(data => {
        console.log('Réponse du serveur :', data);
        if (data.status === 'success') {
            alert(data.message);
            fermerFormulaireAjoutStatistique();
        }
    }).catch(error => {
        console.error('Erreur lors de la requête fetch:', error);
    });
});