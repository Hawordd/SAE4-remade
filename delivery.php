<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require "language.php"; ?>
    <title><?php echo $htmlMarque; ?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style_general.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
</head>

<body>
    <?php
        if(!isset($_SESSION)){
            session_start();
        }
    require_once 'DBConfig/Database.php';
    require_once 'DBConfig/Config.php';
        use DBConfig\Database;

        function dbConnect(): PDO {
            return Database::getConnection();
        }

        $bdd = dbConnect();
        $utilisateur = htmlspecialchars($_SESSION["Id_Uti"]);
        
        $filtreCategorie = isset($_POST["typeCategorie"]) ? htmlspecialchars($_POST["typeCategorie"]) : 0;
    ?>

    <div class="container">
        <div class="leftColumn">
            <img class="logo" href="index.php" src="img/logo.png">
            <div class="contenuBarre">
                <center>
                    <p><strong><?php echo $htmlFiltrerParDeuxPoints; ?></strong></p>
                    <br>
                </center>
                <?php echo $htmlStatut; ?>
                <br>

                <form action="delivery.php" method="post">
                    <label>
                        <input type="radio" name="typeCategorie" value="0"
                            <?php if($filtreCategorie==0) echo 'checked="true"';?>> <?php echo $htmlTOUT; ?>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="typeCategorie" value="1"
                            <?php if($filtreCategorie==1) echo 'checked="true"';?>> <?php echo $htmlENCOURS; ?>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="typeCategorie" value="2"
                            <?php if($filtreCategorie==2) echo 'checked="true"';?>> <?php echo $htmlPRETE; ?>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="typeCategorie" value="4"
                            <?php if($filtreCategorie==4) echo 'checked="true"';?>> <?php echo $htmlLIVREE; ?>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="typeCategorie" value="3"
                            <?php if($filtreCategorie==3) echo 'checked="true"';?>> <?php echo $htmlANNULEE; ?>
                    </label>

                    <br>
                    <br>
                    <center>
                        <input type="submit" value="<?php echo $htmlFiltrer; ?>">
                    </center>
                </form>
            </div>
        </div>
        <div class="rightColumn">
            <div class="topBanner">
                <div class="divNavigation">
                    <a class="bontonDeNavigation" href="index.php"><?php echo $htmlAccueil?></a>
                    <?php
                        if (isset($_SESSION["Id_Uti"])){
                            echo'<a class="bontonDeNavigation" href="messagerie.php">'.$htmlMessagerie.'</a>';
                            echo'<a class="bontonDeNavigation" href="achats.php">'.$htmlAchats.'</a>';
                        }
                        if (isset($_SESSION["isProd"]) && $_SESSION["isProd"]==true){
                            echo'<a class="bontonDeNavigation" href="produits.php">'.$htmlProduits.'</a>';
                            echo'<a class="bontonDeNavigation" href="delivery.php">'.$htmlCommandes.'</a>';
                        }
                        if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"]==true){
                            echo'<a class="bontonDeNavigation" href="panel_admin.php">'.$htmlPanelAdmin.'</a>';
                        }
                    ?>
                </div>
                <form method="post">
                    <?php
                    if(!isset($_SESSION)){
                        session_start();
                    }
                    if(isset($_SESSION, $_SESSION['tempPopup'])){
                        $_POST['popup'] = $_SESSION['tempPopup'];
                        unset($_SESSION['tempPopup']);
                    }
                    ?>

                    <input type="submit"
                        value="<?php if (!isset($_SESSION['Mail_Uti'])){ echo($htmlSeConnecter);} else {echo $_SESSION['Mail_Uti'];}?>"
                        class="boutonDeConnection">
                    <input type="hidden" name="popup"
                        value=<?php if(isset($_SESSION['Mail_Uti'])){echo '"info_perso"';}else{echo '"sign_in"';}?>>
                </form>
            </div>
            <div class="contenuPage">
                <?php
                if ($filtreCategorie != 0){
                    $query = 'SELECT Desc_Statut, Id_Commande, COMMANDE.Id_Uti, UTILISATEUR.Nom_Uti, UTILISATEUR.Prenom_Uti, COMMANDE.Id_Statut 
                    FROM COMMANDE 
                    INNER JOIN info_producteur ON COMMANDE.Id_Prod=info_producteur.Id_Prod 
                    INNER JOIN STATUT ON COMMANDE.Id_Statut=STATUT.Id_Statut 
                    INNER JOIN UTILISATEUR ON COMMANDE.Id_Uti=UTILISATEUR.Id_Uti 
                    WHERE info_producteur.Id_Uti = :utilisateur AND COMMANDE.Id_Statut = :filtreCategorie';
                    $queryGetCommande = $bdd->prepare($query);
                    $queryGetCommande->bindParam(':utilisateur', $utilisateur, PDO::PARAM_INT);
                    $queryGetCommande->bindParam(':filtreCategorie', $filtreCategorie, PDO::PARAM_INT);            
                } else {
                    $query = 'SELECT Desc_Statut, Id_Commande, COMMANDE.Id_Uti, UTILISATEUR.Nom_Uti, UTILISATEUR.Prenom_Uti, COMMANDE.Id_Statut 
                    FROM COMMANDE 
                    INNER JOIN info_producteur ON COMMANDE.Id_Prod=info_producteur.Id_Prod 
                    INNER JOIN STATUT ON COMMANDE.Id_Statut=STATUT.Id_Statut 
                    INNER JOIN UTILISATEUR ON COMMANDE.Id_Uti=UTILISATEUR.Id_Uti 
                    WHERE info_producteur.Id_Uti = :utilisateur';

                    $queryGetCommande = $bdd->prepare($query);
                    $queryGetCommande->bindParam(':utilisateur', $utilisateur, PDO::PARAM_INT);
                }
                $queryGetCommande->execute();
                $returnQueryGetCommande = $queryGetCommande->fetchAll(PDO::FETCH_ASSOC);
                
                if(count($returnQueryGetCommande) == 0){
                    echo $htmlAucuneCommande;
                } else {
                    foreach($returnQueryGetCommande as $commande) {
                        $Id_Commande = $commande["Id_Commande"];
                        $Desc_Statut = mb_strtoupper($commande["Desc_Statut"]);
                        $Nom_Client = mb_strtoupper($commande["Nom_Uti"]);
                        $Prenom_Client = $commande["Prenom_Uti"];
                        $Id_Statut = $commande["Id_Statut"];
                        $Id_Uti = $commande["Id_Uti"];
                        
                        $total = 0;
                        $query = 'SELECT Nom_Produit, Qte_Produit_Commande, Prix_Produit_Unitaire, Nom_Unite_Prix 
                                FROM produits_commandes 
                                WHERE Id_Commande = :idCommande';
                        $queryGetProduitCommande = $bdd->prepare($query);
                        $queryGetProduitCommande->bindParam(':idCommande', $Id_Commande, PDO::PARAM_INT);
                        $queryGetProduitCommande->execute();
                        $returnQueryGetProduitCommande = $queryGetProduitCommande->fetchAll(PDO::FETCH_ASSOC);
                        $nbProduit = count($returnQueryGetProduitCommande);

                        if ($nbProduit > 0) {
                            echo '<div class="commande">';
                            echo $htmlClient, $Prenom_Client." ".$Nom_Client;
                            echo '</br>';
                            echo $htmlCOMMANDE, $Desc_Statut." <br>";
                            if (($Id_Statut != 4) && ($Id_Statut != 3)) {
                ?>
                <form action="change_status_commande.php" method="post">
                    <select name="categorie">
                        <option value=""><?php echo $htmlModifierStatut; ?></option>
                        <option value="1"><?php echo $htmlENCOURS; ?></option>
                        <option value="2"><?php echo $htmlPRETE; ?></option>
                        <option value="3"><?php echo $htmlANNULEE; ?></option>
                        <option value="4"><?php echo $htmlLIVREE; ?></option>
                    </select>
                    <input type="hidden" name="idCommande" value="<?php echo $Id_Commande?>">
                    <button type="submit"><?php echo $htmlConfirmer; ?></button>
                </form>
                <?php
                            }
                        }
                        
                        foreach($returnQueryGetProduitCommande as $produit) {
                            $Nom_Produit = $produit["Nom_Produit"];
                            $Qte_Produit_Commande = $produit["Qte_Produit_Commande"];
                            $Nom_Unite_Prix = $produit["Nom_Unite_Prix"];
                            $Prix_Produit_Unitaire = $produit["Prix_Produit_Unitaire"];
                            $prix_ligne = intval($Prix_Produit_Unitaire) * intval($Qte_Produit_Commande);
                            
                            echo "- " . $Nom_Produit ." - ".$Qte_Produit_Commande.' '.$Nom_Unite_Prix.' * '.$Prix_Produit_Unitaire.'€ = '.$prix_ligne.'€';
                            echo "</br>";
                            $total += $prix_ligne;
                        }

                        if ($nbProduit > 0) {
                            echo '<input type="button" onclick="window.location.href=\'messagerie.php?Id_Interlocuteur='.$Id_Uti.'\'" value="'.$htmlEnvoyerMessage.'"><br>';
                ?>
                <form action="download_pdf.php" method="post">
                    <input type="hidden" name="idCommande" value="<?php echo $Id_Commande?>">
                    <button type="submit"><?php echo $htmlGenererPDF; ?></button>
                </form>
                <?php
                            echo '<div class="aDroite"'.$htmlTotalDeuxPoints, $total.'€</div>';
                            echo '</div><br>'; 
                        }
                    }
                }
                ?>
            </div>
            <div class="basDePage">
                <form method="post">
                    <input type="submit" value="<?php echo $htmlSignalerDys?>" class="lienPopup">
                    <input type="hidden" name="popup" value="contact_admin">
                </form>
                <form method="post">
                    <input type="submit" value="<?php echo $htmlCGU?>" class="lienPopup">
                    <input type="hidden" name="popup" value="cgu">
                </form>
            </div>
        </div>
    </div>
    <?php require "popups/gestion_popups.php";?>
</body>

</html>