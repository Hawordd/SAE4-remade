<?php
require "language.php";
require_once __DIR__ . "/../DBConfig/Database.php";
require_once __DIR__ . "/../DBConfig/Config.php";

// Set up logging
$logFile = __DIR__ . '/../logs/auth.log';
$logDir = dirname($logFile);

// Create logs directory if it doesn't exist
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

function logAuth($message, $level = 'INFO') {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $logEntry = "[$timestamp][$level][$ipAddress] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

try {
    // Retrieve form data
    $pwd = $_POST['pwd'] ?? '';
    $Mail_Uti = $_POST['mail'] ?? '';

    logAuth("Login attempt for email: " . substr($Mail_Uti, 0, 3) . '***' . strstr($Mail_Uti, '@'));

    // Handle password attempts
    if (!isset($_SESSION['test_pwd'])) {
        $_SESSION['test_pwd'] = 5;
    }

    // Use Database class instead of hardcoding credentials
    $bdd = \DBConfig\Database::getConnection();

    // Check if user email exists
    $queryIdUti = $bdd->prepare('SELECT Id_Uti, Pwd_Uti FROM UTILISATEUR WHERE Mail_Uti = :mail');
    $queryIdUti->execute(['mail' => $Mail_Uti]);
    $returnQueryIdUti = $queryIdUti->fetch(PDO::FETCH_ASSOC);

    // Handle invalid email
    if ($returnQueryIdUti == false) {
        unset($Id_Uti);
        $_SESSION['erreur'] = $htmlAdresseMailInvalide;
        logAuth("Invalid email address: $Mail_Uti", 'WARNING');
    } else {
        // Extract user ID and hashed password
        $Id_Uti = $returnQueryIdUti["Id_Uti"];
        $hashedPassword = $returnQueryIdUti["Pwd_Uti"];

        // Verify password
        if (password_verify($pwd, $hashedPassword)) {
            logAuth("Successful login for user ID: $Id_Uti", 'SUCCESS');

            echo $htmlMdpCorrespondRedirectionAccueil;
            $_SESSION['Mail_Uti'] = $Mail_Uti;
            $_SESSION['Id_Uti'] = $Id_Uti;

            // Check if user is a producer
            $queryIsProd = $bdd->prepare('SELECT COUNT(*) as count FROM PRODUCTEUR WHERE Id_Uti = :id');
            $queryIsProd->execute(['id' => $Id_Uti]);
            $returnIsProd = $queryIsProd->fetch(PDO::FETCH_ASSOC);
            $_SESSION["isProd"] = $returnIsProd['count'] > 0;

            if ($_SESSION["isProd"]) {
                logAuth("User ID: $Id_Uti has producer privileges", 'INFO');
            }

            // Check if user is an admin
            $queryIsAdmin = $bdd->prepare('SELECT COUNT(*) as count FROM ADMINISTRATEUR WHERE Id_Uti = :id');
            $queryIsAdmin->execute(['id' => $Id_Uti]);
            $returnIsAdmin = $queryIsAdmin->fetch(PDO::FETCH_ASSOC);
            $_SESSION["isAdmin"] = $returnIsAdmin['count'] > 0;

            if ($_SESSION["isAdmin"]) {
                logAuth("User ID: $Id_Uti has admin privileges", 'INFO');
            }

            $_SESSION['erreur'] = '';
        } else {
            $_SESSION['test_pwd']--;
            $_SESSION['erreur'] = $htmlMauvaisMdp . $_SESSION['test_pwd'] . $htmlTentatives;
            logAuth("Failed login attempt for user ID: $Id_Uti, remaining attempts: " . $_SESSION['test_pwd'], 'WARNING');
        }
    }

    if ($_SESSION['test_pwd'] <= -10) {
        $_SESSION['erreur'] = $htmlErreurMaxReponsesAtteintes;
        logAuth("Maximum login attempts exceeded for IP: " . $_SERVER['REMOTE_ADDR'], 'ERROR');
        // Consider adding additional security measures here like temporary IP ban
    }
} catch (Exception $e) {
    // Handle any exceptions
    $errorMessage = "Authentication error: " . $e->getMessage();
    echo "An error occurred: " . $e->getMessage();
    logAuth($errorMessage, 'ERROR');
}
?>