<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="./CSS/style.css">
    <script src="./JS/Script.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CurlStat</title>
</head>

<body>
    <main>
        <h2>CurlStat</h2>
        <header>
            <div class="header-buttons">
                <button class="open-button" id="boutonOuvrirConnexion" onclick="ouvrirFormulaireConnexion()">Connexion</button>
                <button class="open-button" id="boutonOuvrirCreation" onclick="ouvrirFormulaireCreation()">Créer un compte</button>
            </div>
        </header>
    
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


        <div class="form-popup" id="FormulaireConnexion">
        <form  onsubmit="VerifierConnexion()" method="post" id="FormulaireConnexion" class="form-container">
            <h1>Connexion</h1>

            <label for="email"><b>Email</b></label>
            <input type="text" placeholder="Entrez l'email" name="email" id="email" required>

            <label for="psw"><b>Mot de passe</b></label>
            <input type="password" placeholder="Entrez votre mot de passe" name="psw" id="psw" required>

            <button type="submit" class="btn" style="display:none;">Se connecter</button>
            <button type="button" class="btn cancel" onclick="VerifierConnexion()">Se connecter</button>
        </form>
        </div>

        <div class="form-popup" id="FormulaireCreation">
            <form onsubmit="AjouterCompte()" id="formulaireCreation" class="form-container">
                <h1>Creer un compte</h1>

                <label for="emailCreation"><b>Email</b></label>
                <input type="text" placeholder="Entrez l'email" name="email" id="emailCreation" required>

                <!-- <label for="numeroTel"><b>Téléphone</b></label>
                <input type="text" placeholder="Entrez le numéro" name="telephone" id="telephonecreation" required> -->

                <label for="pswCreation"><b>Mot de passe</b></label>
                <input type="password" placeholder="Entrez votre mot de passe" name="psw" id="pswCreation" required>

                <input type="button" class="btn cancel" onclick="AjouterCompte()" value="Créer">
            </form>
        </div>

        <div class="form-popup" id="FormulaireVerifier2FA">
            <form onsubmit="VerifierCode(event)" id="Verifier2FA" class="form-container">
                <h1>Verifier le code</h1>

                <label for="code2fa"><b>Code</b></label>
                <input type="text" placeholder="Entrez le code" name="code2fa" id="code2fa" required>

                <input type="button" class="btn cancel" onclick="VerifierCode(event)" value="Vérifier">
            </form>
        </div>

        <div class="form-popup" id="FormulaireVerifier2FAConnexion">
            <form onsubmit="VerifierCodeConnexion(event)" id="Verifier2FAConnexion" class="form-container">
                <h1>Verifier le code</h1>

                <label for="code2faConnexion"><b>Code</b></label>
                <input type="text" placeholder="Entrez le code" name="code2faConnexion" id="code2faConnexion" required>

                <input type="button" class="btn cancel" onclick="VerifierCodeConnexion(event)" value="Vérifier">
            </form>
        </div>

        <canvas id="statsChart"></canvas>
    </main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./JS/ScriptGraphique.js"></script>
</body>

</html>