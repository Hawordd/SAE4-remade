<?php
    require "language.php" ; 
?>
<?php
    use DBConfig\Database;

    function dbConnect(): PDO {
        return Database::getConnection();
    }
    $Id_Uti = htmlspecialchars($_SESSION["Id_Uti"]);

    $bdd = dbConnect();
    $queryNbProduits = $bdd->query(('SELECT MAX(Id_Produit) FROM PRODUIT;'));
    $returnqueryNbProduits = $queryNbProduits->fetchAll(PDO::FETCH_ASSOC);
    $nbProduits = $returnqueryNbProduits[0]["MAX(Id_Produit)"] + 1;

    $queryIdProd = $bdd->prepare('SELECT Id_Prod FROM PRODUCTEUR WHERE Id_Uti=:Id_Uti;');
    $queryIdProd->bindParam(":Id_Uti", $Id_Uti, PDO::PARAM_STR);
    $queryIdProd->execute();

    $returnQueryIdProd = $queryIdProd->fetchAll(PDO::FETCH_ASSOC);
    $IdProd = $returnQueryIdProd[0]["Id_Prod"];
    $Nom_Produit = $_POST["nomProduit"];
    $Type_De_Produit = $_POST["categorie"];
    $Prix = $_POST["prix"];
    $Unite_Prix = $_POST["unitPrix"];
    $Quantite = $_POST["quantite"];
    $Unite_Quantite = $_POST["unitQuantite"];
    
    $insertionProduit = "INSERT INTO PRODUIT (Id_Produit, Nom_Produit, Id_Type_Produit, Id_Prod, Qte_Produit, Id_Unite_Stock, Prix_Produit_Unitaire, Id_Unite_Prix) VALUES (:nbProduits, :Nom_Produit, :Type_De_Produit, :IdProd :Quantite, :Unite_Quantite, :Prix, :Unite_Prix)";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_FILES["image"])) {
            $targetDir = __DIR__ . "/img_produit/";
            $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $newFileName = $nbProduits . '.' . $extension;
            $targetPath = $targetDir . $newFileName;

            if (file_exists($targetPath)) {
                unlink($targetPath);
                echo $htmlSuppImgSucces.".<br>";
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                echo "<br>".$htmlImgTelecSucces, $newFileName."<br>";
            } else {
                echo $htmlImgTelecRate . error_get_last()['message'] . "<br>";
                header('Location: mes_produits.php?erreur='. error_get_last()['message']);
            }
        } else {
            echo $htmlSelecImg."<br>";
        }
    }
    
    $_SESSION["Id_Produit"] = htmlspecialchars($nbProduits);

    $bindInsertProduct = $bdd->prepare($insertionProduit);
    $bindInsertProduct->bindParam(':nbProduits', $nbProduits, PDO::PARAM_INT);
    $bindInsertProduct->bindParam(':Nom_Produit', $Nom_Produit, PDO::PARAM_STR);
    $bindInsertProduct->bindParam(':Type_De_Produit', $Type_De_Produit, PDO::PARAM_INT);
    $bindInsertProduct->bindParam(':IdProd', $IdProd, PDO::PARAM_INT);
    $bindInsertProduct->bindParam(':Quantite', $Quantite, PDO::PARAM_INT);
    $bindInsertProduct->bindParam(':Unite_Quantite', $Unite_Quantite, PDO::PARAM_INT);
    $bindInsertProduct->bindParam(':Prix', $Prix, PDO::PARAM_INT);
    $bindInsertProduct->bindParam(':Unite_Prix', $Unite_Prix, PDO::PARAM_INT);
    $bindInsertProduct->execute();

    header('Location: produits.php');
?>