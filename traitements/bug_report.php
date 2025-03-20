<?php


use DBConfig\Database;

// Database connection function
function dbConnect(): PDO {
    return Database::getConnection();
}
$bdd = dbConnect();
$message = $_POST['message'];
if (isset($_SESSION["Id_Uti"]) && isset($message)) {
  
  $bdd->query('CALL broadcast_admin(' . $_SESSION["Id_Uti"] . ', \'' . htmlspecialchars($message) . '\');');
} else {
  
  $bdd->query('CALL broadcast_admin(0 , \''. $_POST["mail"]. htmlspecialchars($message) . '\');');
}