<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./CSS/style.css">
    <script src="./JS/Script.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CurlStat</title>
</head>
<body>

<button class="open-button" id=boutonOuvrirConnexion onclick="ouvrirFormulaireConnexion()">Connexion</button>
<button class="open-button" id=boutonOuvrirCreation onclick="ouvrirFormulaireCreation()">Création</button>

<div class="form-popup" id="FormulaireConnexion">
<form action="Connecter.php" method="post" id="FormulaireConnexion" class="form-container">
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
    <form action="Connecter.php" method="post" id="formulaireCreation" class="form-container">
        <h1>Creer un compte</h1>

        <label for="emailCreation"><b>Email</b></label>
        <input type="text" placeholder="Entrez l'email" name="email" id="emailCreation" required>

        <label for="pswCreation"><b>Mot de passe</b></label>
        <input type="password" placeholder="Entrez votre mot de passe" name="psw" id="pswCreation" required>

        <button type="button" class="btn cancel" onclick="AjouterCompte()">Créer</button>
    </form>
</div>
</body>
</html>