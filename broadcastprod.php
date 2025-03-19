<?php
// Start session at the very beginning
if (!isset($_SESSION)) {
    session_start();
}

/*
if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] !== true) {
    header("Location: index.php");
    exit;
}
*/

// Include French language file
require_once 'language_fr.php';

spl_autoload_register(function ($class) {
    // Convert namespace separator to directory separator
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if (file_exists($path)) {
        require_once $path;
        return true;
    }
    return false;
});

//Use database classes namespace
use DBConfig\Database;

// Database connection function
function dbConnect(): PDO {
    return Database::getConnection();
}

// Handle popup session management
if(isset($_SESSION, $_SESSION['tempPopup'])) {
    $_POST['popup'] = $_SESSION['tempPopup'];
    unset($_SESSION['tempPopup']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $htmlMarque; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="css/style_general.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
</head>

<body class="d-flex" style="background-color: #EBF4EC;">
    <!-- Sidebar -->
    <div class="d-flex flex-column flex-shrink-0 p-3 position-sticky top-0"
        style="width: 280px; height: 100vh; overflow-y: auto; background-color: #659D31;">
        <span class="fs-4"><a href="index.php"><img class="img-fluid mw-100" src="img/logo.png" alt="Logo"></a></span>
        <hr>
        <div class="sidebar-content text-white">
            <h5 class="text-center mb-3"><?php echo $htmlMessageAdmin; ?></h5>
            <p class="mb-3">
                <?php echo $htmlMessageInfoProds; ?>
            </p>
            <a href="panel_admin.php" class="btn btn-light w-100"><?php echo $htmlRetour; ?></a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-grow-1">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-xl navbar-dark bg-white">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation"
                    style="background-color: #659D31;">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-xl-0 d-flex align-items-center">
                        <!-- Always visible -->
                        <li class="nav-item">
                            <a class="nav-link text-black" href="index.php"><?php echo $htmlAccueil; ?></a>
                        </li>

                        <?php if (isset($_SESSION["Id_Uti"])): ?>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="messagerie.php"><?php echo $htmlMessagerie; ?></a>
                        </li>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="achats.php"><?php echo $htmlAchats; ?></a>
                        </li>

                        <?php if (isset($_SESSION["isProd"]) && $_SESSION["isProd"] == true): ?>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="produits.php"><?php echo $htmlProduits; ?></a>
                        </li>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="delivery.php"><?php echo $htmlCommandes; ?></a>
                        </li>
                        <?php endif; ?>

                        <?php if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == true): ?>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black active" href="panel_admin.php"
                                aria-current="page"><?php echo $htmlPanelAdmin; ?></a>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav d-flex align-items-center">
                        <!-- Language selector form -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-black" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php 
                                    switch($_SESSION["language"] ?? "fr") {
                                        case "fr": echo "🇫🇷 Français"; break;
                                        case "en": echo "🇬🇧 English"; break;
                                        case "es": echo "🇪🇸 Español"; break;
                                        case "al": echo "🇩🇪 Deutsch"; break;
                                        case "ru": echo "🇷🇺 русский"; break;
                                        case "ch": echo "🇨🇳 中國人"; break;
                                        default: echo "🇫🇷 Français";
                                    }
                                ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <form action="language.php" method="post" id="languageForm">
                                        <input type="hidden" id="languageValue" name="language" value="fr">
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('fr')">🇫🇷
                                            Français</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('en')">🇬🇧
                                            English</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('es')">🇪🇸
                                            Español</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('al')">🇩🇪
                                            Deutsch</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('ru')">🇷🇺
                                            русский</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('ch')">🇨🇳
                                            中國人</button>
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <?php if (isset($_SESSION['Mail_Uti'])): ?>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <form method="post">
                                <input type="hidden" name="popup" value="info_perso">
                                <button type="submit" class="nav-link text-black border-0 bg-transparent">
                                    <?php echo $_SESSION['Mail_Uti']; ?>
                                </button>
                            </form>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="p-3">
            <div class="container">
                <h1 class="my-3"><?php echo $htmlDiffusionProducteurs; ?></h1>

                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><?php echo $htmlEnvoiMessageProducteurs; ?></h5>
                            </div>
                            <div class="card-body">
                                <!-- Add CSRF token for security -->
                                <form action="traitements/traitement_broadcast_prod.php" method="post"
                                    class="needs-validation">
                                    <input type="hidden" name="csrf_token"
                                        value="<?php echo htmlspecialchars(bin2hex(random_bytes(32))); ?>">

                                    <div class="mb-3">
                                        <label for="message" class="form-label"><?php echo $htmlVotreMessage; ?></label>
                                        <textarea class="form-control" id="message" name="message" rows="8"
                                            maxlength="5000" required></textarea>
                                        <div class="form-text"><?php echo $htmlMaxCaracteres; ?></div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success">
                                            <?php echo $htmlEnvoyerMessageATousProducteurs; ?>
                                        </button>
                                    </div>
                                </form>

                                <?php
                                // Show success message if applicable
                                if(isset($_SESSION['broadcast_success'])) {
                                    echo '<div class="alert alert-success mt-3">' . htmlspecialchars($_SESSION['broadcast_success']) . '</div>';
                                    unset($_SESSION['broadcast_success']);
                                }
                                
                                // Show error message if applicable
                                if(isset($_SESSION['broadcast_error'])) {
                                    echo '<div class="alert alert-danger mt-3">' . htmlspecialchars($_SESSION['broadcast_error']) . '</div>';
                                    unset($_SESSION['broadcast_error']);
                                }
                                ?>
                            </div>
                        </div>

                        <?php
                        // Get count of producers for information
                        $bdd = dbConnect();
                        $queryProducerCount = $bdd->query('SELECT COUNT(*) as count FROM PRODUCTEUR');
                        $producerCount = $queryProducerCount->fetch(PDO::FETCH_ASSOC)['count'];
                        ?>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <?php echo $htmlMessageEnvoyeA . ' ' . $producerCount . ' ' . $htmlProducteurs; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer links -->
            <footer class="container-fluid mt-5 py-3 bg-light">
                <div class="row justify-content-center">
                    <div class="col-auto mx-3">
                        <form method="post">
                            <input type="hidden" name="popup" value="contact_admin">
                            <button type="submit"
                                class="btn btn-outline-secondary btn-sm"><?php echo $htmlSignalerDys; ?></button>
                        </form>
                    </div>
                    <div class="col-auto mx-3">
                        <form method="post">
                            <input type="hidden" name="popup" value="cgu">
                            <button type="submit"
                                class="btn btn-outline-secondary btn-sm"><?php echo $htmlCGU; ?></button>
                        </form>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    <script>
    function setLanguage(lang) {
        document.getElementById('languageValue').value = lang;
    }
    </script>

    <?php require "popups/gestion_popups.php"; ?>
</body>

</html>