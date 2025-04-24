<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/music-app/global.php");
require_once(__CLS_PATH . "cls_html.php");
require_once(__CTR_PATH . "ctr_auth.php");

$HTML = new cls_Html();
$ctr_Auth = new ctr_Auth();

// Si el usuario ya está autenticado, redirigir a la página principal
if ($ctr_Auth->is_authenticated()) {
    header("Location: " . __SITE_PATH);
    exit;
}

// Procesar el inicio de sesión
if (isset($_POST['btn_login'])) {
    if ($ctr_Auth->btn_login_click()) {
        header("Location: " . __SITE_PATH);
        exit;
    }
}


?>

<div id="login_container">
    <div id="login_form">
        <h2 class="section-title">Iniciar Sesión</h2>

        <?php echo $HTML->html_form_tag("frm_login", "login-form", "", "post"); ?>
            <div class="form-group">
                <label for="txt_username">Nombre de Usuario:</label>
                <?php echo $HTML->html_input_text("text", "txt_username", "txt_username", "form-control", "", "Ingrese su nombre de usuario", "required"); ?>
            </div>

            <div class="form-group">
                <label for="txt_password">Contraseña:</label>
                <?php echo $HTML->html_input_text("password", "txt_password", "txt_password", "form-control", "", "Ingrese su contraseña", "required"); ?>
            </div>

            <div class="button-container">
                <?php echo $HTML->html_button("submit", "btn_login", "btn_login", "button", "Iniciar Sesión", "fas fa-sign-in-alt"); ?>
            </div>
        <?php echo $HTML->html_form_end(); ?>
    </div>
</div>
