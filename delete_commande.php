<?php
use DBConfig\Database;

function dbConnect(): PDO {
    return Database::getConnection();
}

try {
    $bdd = dbConnect();
    
    $Id_Commande = filter_input(INPUT_POST, 'deleteValeur', FILTER_SANITIZE_STRING);
    
    if (!$Id_Commande) {
        throw new Exception("Invalid order ID");
    }
    
    $queryGetProduitCommande = $bdd->prepare('SELECT Id_Produit, Qte_Produit_Commande FROM produits_commandes WHERE Id_Commande = :Id_Commande');
    $queryGetProduitCommande->bindParam(":Id_Commande", $Id_Commande, PDO::PARAM_STR);
    $queryGetProduitCommande->execute();
    $products = $queryGetProduitCommande->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        $updateProduit = "UPDATE PRODUIT SET Qte_Produit = Qte_Produit + :Qte_Produit_Commande WHERE Id_Produit = :Id_Produit";
        $bindUpdateProduit = $bdd->prepare($updateProduit);
        $bindUpdateProduit->bindParam(':Qte_Produit_Commande', $product['Qte_Produit_Commande'], PDO::PARAM_INT);
        $bindUpdateProduit->bindParam(':Id_Produit', $product['Id_Produit'], PDO::PARAM_INT);
        $bindUpdateProduit->execute();
    }
    
    $updateStatutCommande = "UPDATE COMMANDE SET Id_Statut = 3 WHERE Id_Commande = :Id_Commande";
    $bindUpdateStatutCommande = $bdd->prepare($updateStatutCommande);
    $bindUpdateStatutCommande->bindParam(':Id_Commande', $Id_Commande, PDO::PARAM_INT);
    $bindUpdateStatutCommande->execute();
    
    header('Location: achats.php');
    exit;
    
} catch (Exception $e) {
    header('Location: achats.php?error=1');
    exit;
}
?>