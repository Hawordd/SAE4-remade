<?php
use DBConfig\Database;

// Database connection function
function dbConnect(): PDO {
    return Database::getConnection();
}
$connexion = dbConnect();

// Vérifiez la connexion
if ($connexion->connect_error) {
    die("Erreur de connexion : " . $connexion->connect_error);  
}
// Préparez la requête SQL en utilisant des requêtes préparées pour des raisons de sécurité
$requete = 'SELECT * FROM UTILISATEUR WHERE UTILISATEUR.Mail_Uti=?';
$stmt = $connexion->prepare($requete);
$stmt->bind_param("s", $_SESSION['Mail_Uti']); // "s" indique que la valeur est une chaîne de caractères
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
$connexion->close();
?>