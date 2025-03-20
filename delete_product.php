<?php
use DBConfig\Database;

function dbConnect(): PDO {
    return Database::getConnection();
}

try {
    $bdd = dbConnect();
    
    $Id_Produit = filter_input(INPUT_POST, 'deleteIdProduct', FILTER_SANITIZE_STRING);
    
    if (!$Id_Produit) {
        throw new Exception("Invalid product ID");
    }
    
    $delProduitCommande = $bdd->prepare('DELETE FROM produits_commandes WHERE Id_Produit = :Id_Produit');
    $delProduitCommande->bindParam(":Id_Produit", $Id_Produit, PDO::PARAM_STR);
    $delProduitCommande->execute();
    
    $delContenu = $bdd->prepare('DELETE FROM CONTENU WHERE Id_Produit = :Id_Produit');
    $delContenu->bindParam(":Id_Produit", $Id_Produit, PDO::PARAM_STR);
    $delContenu->execute();
    
    $delProduct = $bdd->prepare('DELETE FROM PRODUIT WHERE Id_Produit = :Id_Produit');
    $delProduct->bindParam(":Id_Produit", $Id_Produit, PDO::PARAM_STR);
    $delProduct->execute();
    
    $imgpath = "img_produit/" . $Id_Produit . ".png";
    if (file_exists($imgpath)) {
        unlink($imgpath);
    }
    
    header('Location: produits.php');
    exit;
    
} catch (Exception $e) {
    header('Location: produits.php?error=1');
    exit;
}
?>