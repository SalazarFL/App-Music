<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/music-app/global.php");
require_once(__CLS_PATH . "cls_auth.php");
require_once(__CLS_PATH . "cls_message.php");

class ctr_Auth {
    private cls_Auth $auth;

    public function __construct() {
        $this->auth = new cls_Auth();
    }

    /**
     * Maneja el evento de inicio de sesión
     * @return bool True si el inicio de sesión fue exitoso, false en caso contrario
     */
    public function btn_login_click(): bool {
        if (!isset($_POST['txt_username']) || !isset($_POST['txt_password'])) {
            cls_Message::show_message("Por favor, complete todos los campos", "error", "");
            return false;
        }

        $username = trim($_POST['txt_username']);
        $password = trim($_POST['txt_password']);

        if (empty($username) || empty($password)) {
            cls_Message::show_message("Por favor, complete todos los campos", "error", "");
            return false;
        }

        if ($this->auth->login($username, $password)) {
            return true;
        } else {
            cls_Message::show_message("Nombre de usuario o contraseña incorrectos", "error", "");
            return false;
        }
    }

    /**
     * Maneja el evento de cierre de sesión
     */
    public function btn_logout_click(): void {
        $this->auth->logout();
    }

    /**
     * Verifica si el usuario está autenticado
     * @return bool True si el usuario está autenticado, false en caso contrario
     */
    public function is_authenticated(): bool {
        return $this->auth->is_authenticated();
    }

    /**
     * Obtiene el nombre de usuario actual
     * @return string|null Nombre de usuario o null si no está autenticado
     */
    public function get_username(): ?string {
        return $this->auth->get_username();
    }

    /**
     * Obtiene el nombre completo del usuario actual
     * @return string|null Nombre completo o null si no está autenticado
     */
    public function get_full_name(): ?string {
        return $this->auth->get_full_name();
    }

    /**
     * Obtiene la imagen de perfil del usuario actual
     * @return string|null Imagen de perfil o null si no está autenticado
     */
    public function get_profile_image(): ?string {
        return $this->auth->get_profile_image();
    }
}
?>
