<?php
if(!isset($_SESSION)){
    session_start();
}

if(isset($_SESSION, $_SESSION['tempPopup'])){
    $_POST['popup'] = $_SESSION['tempPopup'];
    unset($_SESSION['tempPopup']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>L'étal en ligne</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style_general.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
</head>

<body>
    <div class="container">
        <div class="leftColumn">
            <img class="logo" href="index.php" src="img/logo.png">
            <div class="contenuBarre">

            </div>
        </div>
        <div class="rightColumn">
            <div class="topBanner">
                <div class="divNavigation">
                    <a class="bontonDeNavigation" href="index.php">Accueil</a>
                    <?php
                    if (isset($_SESSION["Id_Uti"])){
                        echo '<a class="bontonDeNavigation" href="messagerie.php">Messagerie</a>';
                        echo '<a class="bontonDeNavigation" href="achats.php">Achats</a>';
                    }
                    if (isset($_SESSION["isProd"]) && $_SESSION["isProd"] == true){
                        echo '<a class="bontonDeNavigation" href="produits.php">Produits</a>';
                        echo '<a class="bontonDeNavigation" href="delivery.php">Commandes</a>';
                    }
                    if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == true){
                        echo '<a class="bontonDeNavigation" href="panel_admin.php">Panel Admin</a>';
                    }
                    ?>
                </div>
                <form method="post">
                    <input type="submit"
                        value="<?php echo !isset($_SESSION['Mail_Uti']) ? 'Se Connecter' : htmlspecialchars($_SESSION['Mail_Uti']); ?>"
                        class="boutonDeConnection">
                    <input type="hidden" name="popup"
                        value="<?php echo isset($_SESSION['Mail_Uti']) ? 'info_perso' : 'sign_in'; ?>">
                </form>
            </div>
            <div class="contenuPage">

            </div>
            <div class="basDePage">
                <form method="post">
                    <input type="submit" value="Signaler un dysfonctionnement" class="lienPopup">
                    <input type="hidden" name="popup" value="contact_admin">
                </form>
                <form method="post">
                    <input type="submit" value="CGU" class="lienPopup">
                    <input type="hidden" name="popup" value="cgu">
                </form>
            </div>
        </div>
    </div>
    <?php require "popups/gestion_popups.php";?>
</body>

</html>