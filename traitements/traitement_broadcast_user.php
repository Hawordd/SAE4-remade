<?php
if(!isset($_SESSION)){
        session_start();
        }
// Database connection
use DBConfig\Database;

// Database connection function
function dbConnect(): PDO {
    return Database::getConnection();
}
// Connect to database
$bdd = dbConnect();
$message = $_POST['message'];
if (isset($_SESSION["Id_Uti"]) && isset($message)) {
  $message = $bdd->quote($message);

  $bdd->query('CALL broadcast_Utilisateur(' . $_SESSION["Id_Uti"] . ', ' . htmlspecialchars($message) . ');');
  header("Location: ../messagerie.php");
} else {
  echo "error";
  echo $message;
  var_dump(isset($_SESSION["Id_Uti"]));
  var_dump(isset($message));

}

?>