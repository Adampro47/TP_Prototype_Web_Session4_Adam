<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curl Stat - Statistiques</title>
    <link rel="stylesheet" href="./CSS/style.css">

    
    
</head>
<body>  
    <main>
        <h2>Statistiques générales</h2>
        <p>Comparez ou affichez une seule catégorie.</p>
        <header>
            <button id="creerEquipe" onclick="ouvrirFormulaireCreationEquipe()">Créer une équipe</button>
            <button id="AjouterStatistique" onclick="ouvrirFormulaireChoixDuJoueur()">Ajouter des statistiques à son équipe</button>
            <button id="VoirStatistique" onclick="ouvrirFormulaireCreationEquipe()">Voir les statistiques de son équipe</button>
        </header>
        ---
        <button id="toggle-mode">Passer en mode Affichage</button>
        
        <div id="comparison-mode">
            <select id="niveau-select-1">
                <option value="u15">U15</option>
                <option value="u18-garcons">U18 Garçons</option>
                <option value="u18-filles">U18 Filles</option>
                <option value="u20-garcons">U20 Garçons</option>
                <option value="u20-filles">U20 Filles</option>
                <option value="mon_equipe">Mon équipe</option>
            </select>
            
            <select id="niveau-select-2">
                <option value="u15">U15</option>
                <option value="u18-garcons">U18 Garçons</option>
                <option value="u18-filles">U18 Filles</option>
                <option value="u20-garcons">U20 Garçons</option>
                <option value="u20-filles">U20 Filles</option>

            </select>
        </div>
        
        <div id="single-mode" style="display: none;">
            <select id="niveau-select-single">
                <option value="u15">U15</option>
                <option value="u18-garcons">U18 Garçons</option>
                <option value="u18-filles">U18 Filles</option>
                <option value="u20-garcons">U20 Garçons</option>
                <option value="u20-filles">U20 Filles</option>
            </select>
        </div>


        <div class="form-popup" id="FormulaireCreationEquipe">
        <form action="Connecter.php" method="post" id="FormulaireCreationEquipe" class="form-container">
            <h1>Création de l'équipe</h1>

            <label for="Premier"><b>Premier</b></label>
            <input type="text" placeholder="Nom du premier" name="Premier" id="Premier" required>

            <label for="Deuxième"><b>Deuxième</b></label>
            <input type="text" placeholder="Nom du Deuxième" name="Deuxième" id="Deuxième" required>

            <label for="Troisième"><b>Troisième</b></label>
            <input type="text" placeholder="Nom du Troisième" name="Troisième" id="Troisième" required>

            <label for="Quatrième"><b>Quatrième</b></label>
            <input type="text" placeholder="Nom du Quatrième" name="Quatrième" id="Quatrième" required>

            <button type="button" class="btn" onclick="VerifierCreationEquipe()">Creer</button>
        </form>
        </div>


        <div class="form-popup" id="FormulaireChoixDuJoueur">
        <form action="Connecter.php" method="post" id="FormulaireAjoutStatistique" class="form-container">
            <h1>Choisir le joueur</h1>

            <button type="button" class="btn" onclick="VerifierCreationEquipe()"><?php echo("") ?></button>
        </form>
        </div>


        <div class="form-popup" id="FormulaireAjoutStatistique">
        <form action="Connecter.php" method="post" id="FormulaireAjoutStatistique" class="form-container">
            <h1>Ajout de statistique pour le joueur : </h1>

            <button type="button" class="btn" onclick="VerifierCreationEquipe()">Creer</button>

            <button type="button" class="btn" onclick="VerifierCreationEquipe()">Creer</button>
        </form>
        </div>


        <canvas id="statsChart"></canvas>
    </main>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./JS/ScriptGraphique.js"></script>
</html>
