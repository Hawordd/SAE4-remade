<?php
use DBConfig\Database;

// Database connection function
function dbConnect(): PDO {
    return Database::getConnection();
}
$mabdd = dbConnect();

// Vérifiez la connexion
if ($mabdd->connect_error) {
    die("Erreur de connexion : " . $mabdd->connect_error);
}
// Préparez la requête SQL en utilisant des requêtes préparées pour des raisons de sécurité
$requete = 'SELECT * FROM UTILISATEUR WHERE UTILISATEUR.Mail_Uti=?';
$stmt = $mabdd->prepare($requete);
$stmt->bind_param("s", $_SESSION['Mail_Uti']); // "s" indique que la valeur est une chaîne de caractères
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
$mabdd->close();
?>