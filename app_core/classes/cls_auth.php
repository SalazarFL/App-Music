<?php
require_once(__CLS_PATH . "cls_mysql.php");

class cls_Auth {
    private cls_Mysql $data_provider;
    private bool $is_authenticated = false;
    private ?array $user_data = null;

    public function __construct() {
        $this->data_provider = new cls_Mysql();
        $this->check_session();
    }

    /**
     * Verifica si hay una sesión activa
     */
    private function check_session(): void {
        if (isset($_SESSION['AUTH']) && $_SESSION['AUTH'] === true && isset($_SESSION['USER_ID'])) {
            $this->is_authenticated = true;
            $this->load_user_data($_SESSION['USER_ID']);
        }
    }

    /**
     * Carga los datos del usuario desde la base de datos
     * @param int $user_id ID del usuario
     */
    private function load_user_data(int $user_id): void {
        $result = $this->data_provider->sql_execute_prepared(
            "SELECT id, username, full_name, email, profile_image
             FROM tbl_users
             WHERE id = ?",
            "i",
            [$user_id]
        );

        if ($result !== false) {
            $this->user_data = $this->data_provider->sql_get_fetchassoc($result);
        }
    }

    /**
     * Intenta autenticar al usuario con las credenciales proporcionadas
     * @param string $username Nombre de usuario
     * @param string $password Contraseña sin encriptar
     * @return bool True si la autenticación fue exitosa, false en caso contrario
     */
    public function login(string $username, string $password): bool {
        $hashed_password = md5($password);

        $result = $this->data_provider->sql_execute_prepared(
            "SELECT id, username, full_name, email, profile_image
             FROM tbl_users
             WHERE username = ? AND password = ?",
            "ss",
            [$username, $hashed_password]
        );

        if ($result === false) {
            return false;
        }

        $user = $this->data_provider->sql_get_fetchassoc($result);

        if ($user) {
            // Establecer variables de sesión
            $_SESSION['AUTH'] = true;
            $_SESSION['USER_ID'] = $user['id'];
            $_SESSION['USERNAME'] = $user['username'];
            $_SESSION['FULLNAME'] = $user['full_name'];
            $_SESSION['PROFILE_IMAGE'] = $user['profile_image'];

            $this->is_authenticated = true;
            $this->user_data = $user;

            return true;
        }

        return false;
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(): void {
        // Destruir todas las variables de sesión
        $_SESSION = array();

        // Si se desea destruir completamente la sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        $this->is_authenticated = false;
        $this->user_data = null;
    }

    /**
     * Verifica si el usuario está autenticado
     * @return bool True si el usuario está autenticado, false en caso contrario
     */
    public function is_authenticated(): bool {
        return $this->is_authenticated;
    }

    /**
     * Obtiene los datos del usuario
     * @return array|null Datos del usuario o null si no está autenticado
     */
    public function get_user_data(): ?array {
        return $this->user_data;
    }

    /**
     * Obtiene el ID del usuario
     * @return int|null ID del usuario o null si no está autenticado
     */
    public function get_user_id(): ?int {
        return $this->user_data ? $this->user_data['id'] : null;
    }

    /**
     * Obtiene el nombre de usuario
     * @return string|null Nombre de usuario o null si no está autenticado
     */
    public function get_username(): ?string {
        return $this->user_data ? $this->user_data['username'] : null;
    }

    /**
     * Obtiene el nombre completo del usuario
     * @return string|null Nombre completo o null si no está autenticado
     */
    public function get_full_name(): ?string {
        return $this->user_data ? $this->user_data['full_name'] : null;
    }

    /**
     * Obtiene la imagen de perfil del usuario
     * @return string|null Imagen de perfil o null si no está autenticado
     */
    public function get_profile_image(): ?string {
        return $this->user_data ? $this->user_data['profile_image'] : null;
    }
}
?>
