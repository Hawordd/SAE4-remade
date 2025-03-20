<?php
if(!isset($_SESSION)) {
    session_start();
}
require "language.php";
use DBConfig\Database;

if (!isset($_SESSION["Id_Uti"])) {
    header("Location: index.php");
    exit;
}

function dbConnect(): PDO {
    return Database::getConnection();
}

$utilisateur = htmlspecialchars($_SESSION["Id_Uti"]);
$Id_Produit_Update = htmlspecialchars($_POST["modifyIdProduct"]);
$_SESSION["Id_Produit"] = $Id_Produit_Update;

$bdd = dbConnect();
$queryGetProducts = $bdd->prepare('SELECT * FROM PRODUIT WHERE Id_Produit = :Id_Produit_Update');
$queryGetProducts->bindParam(':Id_Produit_Update', $Id_Produit_Update, PDO::PARAM_INT);
$queryGetProducts->execute();
$returnQueryGetProducts = $queryGetProducts->fetchAll(PDO::FETCH_ASSOC);

$IdProd = $returnQueryGetProducts[0]["Id_Prod"];
$Nom_Produit = $returnQueryGetProducts[0]["Nom_Produit"];
$Id_Type_Produit = $returnQueryGetProducts[0]["Id_Type_Produit"];
$Qte_Produit = $returnQueryGetProducts[0]["Qte_Produit"];
$Id_Unite_Stock = $returnQueryGetProducts[0]["Id_Unite_Stock"];
$Prix_Produit_Unitaire = $returnQueryGetProducts[0]["Prix_Produit_Unitaire"];
$Id_Unite_Prix = $returnQueryGetProducts[0]["Id_Unite_Prix"];

$queryIdProd = $bdd->prepare('SELECT Id_Prod FROM PRODUCTEUR WHERE Id_Uti = :utilisateur');
$queryIdProd->bindParam(':utilisateur', $utilisateur, PDO::PARAM_INT);
$queryIdProd->execute();
$returnQueryIdProd = $queryIdProd->fetchAll(PDO::FETCH_ASSOC);
$Id_Prod = $returnQueryIdProd[0]["Id_Prod"];

$queryGetProducts = $bdd->prepare('SELECT Id_Produit, Nom_Produit, Desc_Type_Produit, Prix_Produit_Unitaire, Nom_Unite_Prix, Qte_Produit, Nom_Unite_Stock FROM Produits_d_un_producteur WHERE Id_Prod = :idProd');
$queryGetProducts->bindParam(':idProd', $Id_Prod, PDO::PARAM_INT);
$queryGetProducts->execute();                            
$returnQueryGetProducts = $queryGetProducts->fetchAll(PDO::FETCH_ASSOC);

if(isset($_SESSION['tempPopup'])) {
    $_POST['popup'] = $_SESSION['tempPopup'];
    unset($_SESSION['tempPopup']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title><?php echo $htmlMarque; ?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style_general.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
</head>

<body>
    <div class="container">
        <div class="leftColumn">
            <img class="logo" href="index.php" src="img/logo.png">
            <div class="contenuBarre">
                <center>
                    <p><strong><?php echo $htmlAjouterProduit?></strong></p>
                    <form action="modify_product.php" method="post" enctype="multipart/form-data">
                        <label for="nomProduit"><?php echo $htmlProduitDeuxPoints?> </label>
                        <input type="hidden" name="IdProductAModifier" value="<?php echo $Id_Produit_Update ?>">
                        <input type="text" id="nomProduit" name="nomProduit"
                            value="<?php echo htmlspecialchars($Nom_Produit)?>" required><br><br>

                        <select name="categorie">
                            <?php 
                            $categories = [
                                1 => $htmlFruit,
                                2 => $htmlLégume,
                                3 => $htmlGraine,
                                4 => $htmlViande,
                                5 => $htmlVin,
                                6 => $htmlAnimaux,
                                7 => $htmlPlanche
                            ];
                            echo "<option value=\"{$Id_Type_Produit}\">{$categories[$Id_Type_Produit]}</option>";
                            foreach ($categories as $id => $name) {
                                if ($id != $Id_Type_Produit) {
                                    echo "<option value=\"{$id}\">{$name}</option>";
                                }
                            }
                            ?>
                        </select>
                        <br><br>

                        <?php echo $htmlPrix?>
                        <input style="width: 50px;" value="<?php echo htmlspecialchars($Prix_Produit_Unitaire)?>"
                            type="number" min="0" name="prix" required>€

                        <label>
                            <input type="radio" name="unitPrix" value="1"
                                <?php echo ($Id_Unite_Prix == 1) ? 'checked="checked"' : ''; ?>>
                            <?php echo $htmlLeKilo; ?>
                        </label>
                        <label>
                            <input type="radio" name="unitPrix" value="4"
                                <?php echo ($Id_Unite_Prix == 4) ? 'checked="checked"' : ''; ?>>
                            <?php echo $htmlLaPiece; ?>
                        </label>

                        <br><br>Stock :
                        <input type="number" value="<?php echo htmlspecialchars($Qte_Produit)?>" style="width: 50px;"
                            min="0" name="quantite" required>

                        <?php
                        $unites = [
                            1 => $htmlKg,
                            2 => $htmlL,
                            3 => $htmlM2,
                            4 => $htmlPiece
                        ];
                        
                        foreach ($unites as $id => $name) {
                            echo "<label>";
                            echo "<input type=\"radio\" name=\"unitQuantite\" value=\"{$id}\"" . ($Id_Unite_Stock == $id ? ' checked="checked"' : '') . "> {$name}";
                            echo "</label>";
                        }
                        ?>

                        <br><br>
                        <input type="file" name="image" accept=".png">
                        <br><br>
                        <input type="submit" value="<?php echo $htmlConfirmerModifProd?>">
                    </form>
                    <br>
                    <form action="produits.php" method="post">
                        <input type="submit" value="<?php echo $htmlAnnulerModifProd?>">
                    </form>
                    <br><br><br>
                </center>
            </div>
        </div>

        <div class="rightColumn">
            <div class="topBanner">
                <div class="divNavigation">
                    <a class="bontonDeNavigation" href="index.php"><?php echo $htmlAccueil?></a>
                    <?php
                    if (isset($_SESSION["Id_Uti"])) {
                        echo '<a class="bontonDeNavigation" href="messagerie.php">'.$htmlMessagerie.'</a>';
                        echo '<a class="bontonDeNavigation" href="achats.php">'.$htmlAchats.'</a>';
                    }
                    if (isset($_SESSION["isProd"]) && $_SESSION["isProd"]) {
                        echo '<a class="bontonDeNavigation" href="produits.php">'.$htmlProduits.'</a>';
                        echo '<a class="bontonDeNavigation" href="delivery.php">'.$htmlCommandes.'</a>';
                    }
                    if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"]) {
                        echo '<a class="bontonDeNavigation" href="panel_admin.php">'.$htmlPanelAdmin.'</a>';
                    }
                    ?>
                </div>
                <form method="post">
                    <input type="submit"
                        value="<?php echo !isset($_SESSION['Mail_Uti']) ? $htmlSeConnecter : htmlspecialchars($_SESSION['Mail_Uti']); ?>"
                        class="boutonDeConnection">
                    <input type="hidden" name="popup"
                        value="<?php echo isset($_SESSION['Mail_Uti']) ? 'info_perso' : 'sign_in'; ?>">
                </form>
            </div>

            <p>
                <center><u><?php echo $htmlMesProduitsEnStock?></u></center>
            </p>

            <div class="gallery-container">
                <?php
                if(empty($returnQueryGetProducts)) {
                    echo $htmlAucunProduitEnStock;
                } else {
                    echo '<style>
                        form { display: inline-block; margin-right: 1px; }
                        button { display: inline-block; }
                    </style>';
                    
                    foreach($returnQueryGetProducts as $product) {
                        $Id_Produit = $product["Id_Produit"];
                        $nomProduit = $product["Nom_Produit"];
                        $typeProduit = $product["Desc_Type_Produit"];
                        $prixProduit = $product["Prix_Produit_Unitaire"];
                        $QteProduit = $product["Qte_Produit"];
                        $unitePrixProduit = $product["Nom_Unite_Prix"];
                        $Nom_Unite_Stock = $product["Nom_Unite_Stock"];
                        
                        if ($QteProduit > 0) {
                            echo '<div class="square1">';
                            echo $htmlProduitDeuxPoints . ' ' . htmlspecialchars($nomProduit) . "<br>";
                            echo $htmlTypeDeuxPoints . ' ' . htmlspecialchars($typeProduit) . "<br><br>";
                            echo '<img class="img-produit" src="img_produit/' . $Id_Produit . '.png" alt="'.$htmlImageNonFournie.'" style="width: 85%; height: 70%;" ><br>';
                            echo $htmlPrix . ' ' . htmlspecialchars($prixProduit) .' €/'.htmlspecialchars($unitePrixProduit). "<br>";
                            echo $htmlStockDeuxPoints . ' ' . htmlspecialchars($QteProduit) .' '.htmlspecialchars($Nom_Unite_Stock). "<br>";
                            
                            if ($Id_Produit == $Id_Produit_Update) {
                                echo '<input type="submit" disabled="disabled" value="'.$htmlModification.'"/>';
                            } else {
                                echo '<form action="product_modification.php" method="post">';
                                echo '<input type="hidden" name="modifyIdProduct" value="'.htmlspecialchars($Id_Produit).'">';
                                echo '<button type="submit">'.$htmlModifier.'</button>';
                                echo '</form>';
                            }
                            
                            echo '<form action="delete_product.php" method="post">';
                            echo '<input type="hidden" name="deleteIdProduct" value="'.htmlspecialchars($Id_Produit).'">';
                            echo '<button type="submit">'.$htmlSupprimer.'</button>';
                            echo '</form>';
                            echo '</div>';
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