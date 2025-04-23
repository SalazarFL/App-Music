<?php
class cls_Mysql {
    private ?mysqli $conn = null;

    private string $serverName = "sql211.infinityfree.com";
    private string $dataBase   = "if0_38812344_bd_musichub";
    private string $userName   = "if0_38812344";
    private string $password   = "PVZGkFHRJ2qxS2";

    public function __construct() {}

    /**
     * Establece la conexión con la base de datos
     * @return mysqli Objeto de conexión
     */
    public function db_connect(): mysqli {
        if ($this->conn === null) {
            try {
                $this->conn = new mysqli($this->serverName, $this->userName, $this->password, $this->dataBase);

                if ($this->conn->connect_error) {
                    throw new Exception("Error de conexión: " . $this->conn->connect_error);
                }

                $this->conn->set_charset("utf8mb4");
            } catch (Exception $e) {
                cls_Message::show_message($e->getMessage(), "error", "");
                error_log($e->getMessage());
            }
        }

        return $this->conn;
    }

    /**
     * Ejecuta una consulta SQL
     */
    public function sql_execute(string $sql) {
        try {
            $result = $this->db_connect()->query($sql);

            if ($result === false) {
                throw new Exception("Error en la consulta: " . $this->conn->error);
            }

            return $result;
        } catch (Exception $e) {
            cls_Message::show_message($e->getMessage(), "error", "");
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Ejecuta una consulta preparada SQL
     */
    public function sql_execute_prepared(string $sql, string $types, array $params) {
        try {
            $stmt = $this->db_connect()->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conn->error);
            }

            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            $result = $stmt->get_result();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            cls_Message::show_message($e->getMessage(), "error", "");
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Ejecuta una consulta preparada SQL para insertar, actualizar o eliminar datos
     */
    public function sql_execute_prepared_dml(string $sql, string $types, array $params): bool {
        try {
            $stmt = $this->db_connect()->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Error en la preparación de la consulta: " . $this->conn->error);
            }

            $stmt->bind_param($types, ...$params);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            cls_Message::show_message($e->getMessage(), "error", "");
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los resultados como un array indexado numéricamente
     */
    public function sql_get_rows($result): array {
        try {
            $array = [];

            while ($row = $result->fetch_row()) {
                $array[] = $row;
            }

            return $array;
        } catch (Exception $e) {
            cls_Message::show_message($e->getMessage(), "error", "");
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los resultados como un array asociativo
     */
    public function sql_get_rows_assoc($result): array {
        try {
            $array = [];

            while ($row = $result->fetch_assoc()) {
                $array[] = $row;
            }

            return $array;
        } catch (Exception $e) {
            cls_Message::show_message($e->getMessage(), "error", "");
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el resultado como un array asociativo
     */
    public function sql_get_fetchassoc($result): ?array {
        try {
            return $result->fetch_assoc();
        } catch (Exception $e) {
            cls_Message::show_message($e->getMessage(), "error", "");
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene el número de filas afectadas por una consulta
     */
    public function sql_get_affected_rows(): int {
        return $this->conn->affected_rows;
    }

    /**
     * Obtiene el último ID insertado
     */
    public function sql_get_last_id(): int {
        return $this->conn->insert_id;
    }

    /**
     * Cierra la conexión a la base de datos
     */
    public function close(): void {
        if ($this->conn !== null) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    /**
     * Destructor para cerrar la conexión
     */
    public function __destruct() {
        $this->close();
    }
}
?>
