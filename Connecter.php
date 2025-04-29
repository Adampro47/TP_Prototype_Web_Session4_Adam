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
        <header>
            <button id="creerEquipe" onclick="ouvrirFormulaireCreationEquipe()">Créer une équipe</button>
            <button id="AjouterStatistique" >Ajouter des statistiques à son équipe</button>
            <button id="VoirStatistique" onclick="ouvrirFormulaireCreationEquipe()">Voir les statistiques de son équipe</button>
        </header>
        <h2></h2>
        <p>Comparez ou affichez une seule catégorie.</p>

        <button id="toggle-mode">Passer en mode Affichage</button>
        
        <div id="comparison-mode">
            <select id="niveau-select-1">
                <option value="u15">U15</option>
                <option value="u18-garcons">U18 Garçons</option>
                <option value="u18-filles">U18 Filles</option>
                <option value="u20-garcons">U20 Garçons</option>
                <option value="u20-filles">U20 Filles</option>
                <option value="mon-equipe">Mon équipe</option>
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
            <form id="formCreationEquipe" method="POST" class="form-container">
                <h1>Création de l'équipe</h1>
                <label for="Premier"><b>Premier</b></label>
                <input type="text" placeholder="Nom du premier" name="Premier" id="Premier" required>
                <label for="Deuxième"><b>Deuxième</b></label>
                <input type="text" placeholder="Nom du Deuxième" name="Deuxieme" id="Deuxieme" required>
                <label for="Troisième"><b>Troisième</b></label>
                <input type="text" placeholder="Nom du Troisième" name="Troisieme" id="Troisieme" required>
                <label for="Quatrième"><b>Quatrième</b></label>
                <input type="text" placeholder="Nom du Quatrième" name="Quatrième" id="Quatrième" required>
                <label for="categorieEquipe">Choisir la catégorie de l'équipe</label>
                <ul id="selectListCategorieDeEquipe">
                    <li><select name="select_Equipe" class="select-item">
                        <option value="u15">U15</option>
                        <option value="u18-garcons">U18 Garçons</option>
                        <option value="u18-filles">U18 Filles</option>
                        <option value="u20-garcons">U20 Garçons</option>
                        <option value="u20-filles">U20 Filles</option>
                    </select></li>
                </ul>
                <button type="submit" class="btn">Créer l'équipe</button>
            </form>
        </div>

        <div class="form-popup" id="FormulaireChoixDuJoueur">
            <form method="post" id="formChoixDuJoueur" class="form-container">
                <h1>Choisir le joueur</h1>
                <select id="JoueurChoisi">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
                <button type="submit" class="btn">Choisir le joueur</button>
            </form>
        </div>

        <div class="form-popup" id="FormulaireAjoutStatistique">
            <form id="formAjoutStatistique" method="POST" class="form-container">
                <h1>Ajouter des statistiques</h1>
                <label id="Joueur-ajout-de-stat">Joueur</label>
                
                <label for="type-lancer">Type de lancer :</label>
                <select name="type_lancer" id="type-lancer">
                    <option value="Placement">Placement</option>
                    <option value="Sortie">Sortie</option>
                    <option value="Raise">Raise</option>
                    <option value="Garde">Garde</option>
                    <option value="Double sortie">Double sortie</option>
                    <option value="Placement gelé">Placement gelé</option>
                </select>

                <label for="nombre">Nombre :</label>
                <ul id="selectList">
                    <li><select name="select_0" class="select-item" onchange="addNewSelect(this)">
                        <option value="null" selected>Choisir...</option>
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select></li>
                </ul>
                <button type="submit" class="btn">Ajouter</button>
            </form>
        </div>
        <div class="form" id="testsqlform">
            <form id="formtestsql" method="POST" class="form-container">
                <h1>TestStat</h1>
                <label for="categorieEquipe">Choisir la catégorie de l'équipe</label>
                <ul id="choisircategorieequipetestsql">
                    <li><select name="select_Equipe2" class="select-item">
                        <option value="u15">U15</option>
                        <option value="u18-garcons">U18 Garçons</option>
                        <option value="u18-filles">U18 Filles</option>
                        <option value="u20-garcons">U20 Garçons</option>
                        <option value="u20-filles">U20 Filles</option>
                    </select></li>
                </ul>
                <button type="submit" class="btn">tester</button>
            </form>
        </div>
        <canvas id="statsChart"></canvas>
    </main>


</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./JS/ScriptGraphique.js"></script>
<script src="./JS/Connecter_script.js"></script>


</html>
