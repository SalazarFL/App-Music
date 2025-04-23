<?php
// Iniciar sesión
session_start();

require_once("global.php");
require_once(__CLS_PATH . "cls_html.php");
require_once(__CTR_PATH . "ctr_auth.php");

$HTML = new cls_Html();
$ctr_Auth = new ctr_Auth();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MusicApp - Comparte tus canciones favoritas">
    <?php
        // Incluir jQuery y jQuery UI
        echo $HTML->html_js_header("https://code.jquery.com/jquery-3.6.0.min.js");
        echo $HTML->html_js_header("https://code.jquery.com/ui/1.13.2/jquery-ui.min.js");

        // Incluir iconos Font Awesome
        echo $HTML->html_css_header("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css", "screen");

        // Incluir archivo de funciones JS propio
        echo $HTML->html_js_header(__JS_PATH . "functions.js");

        // Incluir CSS
        echo $HTML->html_css_header(__CSS_PATH . "style.css", "screen");
    ?>
    <title>MusicApp - Comparte tus canciones favoritas</title>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo __IMG_PATH; ?>favicon.ico" type="image/x-icon">
</head>
<body id="main_page">
    <div id="main_box">
        <header>
            <h1 class="site-title">Music<span class="hub-text">App</span></h1>
            <p class="site-description">Comparte tus canciones favoritas</p>

            <?php if ($ctr_Auth->is_authenticated()): ?>
                <div class="user-info">
                    <span>Bienvenido, <?php echo $ctr_Auth->get_full_name(); ?></span>
                    <form method="post" action="" class="logout-form">
                        <button type="submit" name="btn_logout" class="link-button">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </header>

        <?php
        // Procesar el cierre de sesión
        if (isset($_POST['btn_logout'])) {
            $ctr_Auth->btn_logout_click();
            header("Location: " . __SITE_PATH);
            exit;
        }

        // Cargar la vista apropiada según el estado de autenticación
        if ($ctr_Auth->is_authenticated()) {
            include_once(__VWS_PATH . "music.php");
        } else {
            include_once(__VWS_PATH . "login.php");
        }
        ?>

        <footer>
            <p>MusicHub &copy; <?php echo date('Y'); ?> - Todos los derechos reservados</p>
        </footer>
    </div>
</body>
</html>
