<?php
use DBConfig\Database;

function dbConnect(): PDO {
    return Database::getConnection();
}

require "language.php";

$bdd = dbConnect();

$Id_Produit = htmlspecialchars($_POST["IdProductAModifier"]);
$Nom_Produit = htmlspecialchars($_POST["nomProduit"]);
$Categorie = htmlspecialchars($_POST["categorie"]);
$Prix = htmlspecialchars($_POST["prix"]);
$Prix_Unite = htmlspecialchars($_POST["unitPrix"]);
$Quantite = htmlspecialchars($_POST["quantite"]);
$Quantite_Unite = htmlspecialchars($_POST["unitQuantite"]);

$updateProduit = "UPDATE PRODUIT SET Nom_Produit = :Nom_Produit, Id_Type_Produit = :Categorie, Qte_Produit = :Quantite, Id_Unite_Stock = :Quantite_Unite, Prix_Produit_Unitaire = :Prix, Id_unite_Prix = :Prix_Unite WHERE Id_Produit = :Id_Produit";
$stmt = $bdd->prepare($updateProduit);
$stmt->bindParam(':Nom_Produit', $Nom_Produit);
$stmt->bindParam(':Categorie', $Categorie);
$stmt->bindParam(':Quantite', $Quantite);
$stmt->bindParam(':Quantite_Unite', $Quantite_Unite);
$stmt->bindParam(':Prix', $Prix);
$stmt->bindParam(':Prix_Unite', $Prix_Unite);
$stmt->bindParam(':Id_Produit', $Id_Produit);
$stmt->execute();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] != UPLOAD_ERR_NO_FILE) {
        $targetDir = __DIR__ . "/img_produit/";
        
        if(!isset($_SESSION)){
            session_start();
        }
        
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $newFileName = $Id_Produit . '.' . $extension;
        $targetPath = $targetDir . $newFileName;
        
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            echo "<br>' $htmlImgTelecSucces $newFileName<br>";
        } else {
            echo $htmlImgTelecRate . error_get_last()['message'] . "<br>";
            header('Location: produits.php?erreur=' . error_get_last()['message']);
            exit;
        }
    }
}

header('Location: produits.php');
exit;
?>