<?php
require_once(__CLS_PATH . "cls_mysql.php");

class cls_Music {
    private cls_Mysql $data_provide;

    public function __construct() {
        $this->data_provide = new cls_Mysql();
    }

    /**
     * Obtiene todas las canciones ordenadas por ID descendente
     * @return array Array con las canciones
     */
    public function get_songs(): array {
        $result = $this->data_provide->sql_execute(
            "SELECT id, title, artist, genre, review, rating, created_at
             FROM tbl_songs
             ORDER BY id DESC"
        );

        if ($result === false) {
            return [];
        }

        return $this->data_provide->sql_get_rows_assoc($result);
    }

    /**
     * Obtiene una canción por su ID
     * @param int $id ID de la canción
     * @return array|null Datos de la canción o null si no existe
     */
    public function get_song_by_id(int $id): ?array {
        $result = $this->data_provide->sql_execute_prepared(
            "SELECT id, title, artist, genre, review, rating, created_at
             FROM tbl_songs
             WHERE id = ?",
            "i",
            [$id]
        );

        if ($result === false) {
            return null;
        }

        return $this->data_provide->sql_get_fetchassoc($result);
    }

    /**
     * Inserta una nueva canción en la base de datos
     * @param array $songdata Datos de la canción a insertar
     * @return bool True si se insertó correctamente, false en caso contrario
     */
    public function insert_song(array $songdata): bool {
        // Verificar que tengamos todos los datos necesarios
        if (empty($songdata['title']) || empty($songdata['artist']) ||
            empty($songdata['genre']) || empty($songdata['review']) ||
            empty($songdata['rating'])) {
            return false;
        }

        // Usando consulta preparada para mayor seguridad
        return $this->data_provide->sql_execute_prepared_dml(
            "INSERT INTO tbl_songs (title, artist, genre, review, rating, created_at)
             VALUES (?, ?, ?, ?, ?, ?)",
            "ssssis",
            [
                $songdata['title'],
                $songdata['artist'],
                $songdata['genre'],
                $songdata['review'],
                $songdata['rating'],
                date("Y-m-d H:i:s")
            ]
        );
    }

    /**
     * Actualiza una canción existente
     * @param array $songdata Datos de la canción a actualizar
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function update_song(array $songdata): bool {
        if (empty($songdata['id']) || empty($songdata['title']) ||
            empty($songdata['artist']) || empty($songdata['genre']) ||
            empty($songdata['review']) || empty($songdata['rating'])) {
            return false;
        }

        return $this->data_provide->sql_execute_prepared_dml(
            "UPDATE tbl_songs
             SET title = ?, artist = ?, genre = ?, review = ?, rating = ?
             WHERE id = ?",
            "sssssi",
            [
                $songdata['title'],
                $songdata['artist'],
                $songdata['genre'],
                $songdata['review'],
                $songdata['rating'],
                $songdata['id']
            ]
        );
    }

    /**
     * Elimina una canción por su ID
     * @param int $id ID de la canción a eliminar
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function delete_song(int $id): bool {
        return $this->data_provide->sql_execute_prepared_dml(
            "DELETE FROM tbl_songs WHERE id = ?",
            "i",
            [$id]
        );
    }

    /**
     * Busca canciones que coincidan con un término de búsqueda
     * @param string $searchTerm Término de búsqueda
     * @return array Array con las canciones encontradas
     */
    public function search_songs(string $searchTerm): array {
        $searchTerm = "%{$searchTerm}%";

        $result = $this->data_provide->sql_execute_prepared(
            "SELECT id, title, artist, genre, review, rating, created_at
             FROM tbl_songs
             WHERE title LIKE ? OR artist LIKE ? OR genre LIKE ?
             ORDER BY id DESC",
            "sss",
            [$searchTerm, $searchTerm, $searchTerm]
        );

        if ($result === false) {
            return [];
        }

        return $this->data_provide->sql_get_rows_assoc($result);
    }
}
?>
