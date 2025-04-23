<?php
require_once(__CLS_PATH . "cls_mysql.php");

class cls_Music {
    private cls_Mysql $data_provider;

    public function __construct() {
        $this->data_provider = new cls_Mysql();
    }

    // Obtener todas las canciones con detalles del usuario
    public function get_songs(): array {
        $result = $this->data_provider->sql_execute(
            "SELECT s.id, s.title, s.artist, s.genre, s.review, s.rating, s.created_at,
                    u.id as user_id, u.username, u.full_name, u.profile_image
             FROM tbl_songs s
             JOIN tbl_users u ON s.user_id = u.id
             ORDER BY s.id DESC"
        );

        if ($result === false) return [];
        return $this->data_provider->sql_get_rows_assoc($result);
    }

    // Obtener canción por ID
    public function get_song_by_id(int $id): ?array {
        $result = $this->data_provider->sql_execute_prepared(
            "SELECT s.id, s.title, s.artist, s.genre, s.review, s.rating, s.created_at,
                    s.user_id, u.username, u.full_name, u.profile_image
             FROM tbl_songs s
             JOIN tbl_users u ON s.user_id = u.id
             WHERE s.id = ?",
            "i",
            [$id]
        );

        if ($result === false) return null;
        return $this->data_provider->sql_get_fetchassoc($result);
    }

    // Insertar canción
    public function insert_song(array $songdata): bool {
        if (empty($songdata['title']) || empty($songdata['artist']) ||
            empty($songdata['genre']) || empty($songdata['review']) ||
            empty($songdata['rating']) || empty($songdata['user_id'])) {
            return false;
        }

        return $this->data_provider->sql_execute_prepared_dml(
            "INSERT INTO tbl_songs (title, artist, genre, review, rating, user_id, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            "sssssis",
            [
                $songdata['title'],
                $songdata['artist'],
                $songdata['genre'],
                $songdata['review'],
                $songdata['rating'],
                $songdata['user_id'],
                date("Y-m-d H:i:s")
            ]
        );
    }

    // Actualizar canción (solo si el usuario es dueño)
    public function update_song(array $songdata): bool {
        if (empty($songdata['id']) || empty($songdata['title']) || empty($songdata['artist']) ||
            empty($songdata['genre']) || empty($songdata['review']) ||
            empty($songdata['rating']) || empty($songdata['user_id'])) {
            return false;
        }

        if (!$this->can_user_edit_song($songdata['id'], $songdata['user_id'])) {
            return false;
        }

        return $this->data_provider->sql_execute_prepared_dml(
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

    // Eliminar canción (solo si el usuario es dueño)
    public function delete_song(int $id, int $user_id): bool {
        if (!$this->can_user_edit_song($id, $user_id)) {
            return false;
        }

        return $this->data_provider->sql_execute_prepared_dml(
            "DELETE FROM tbl_songs WHERE id = ?",
            "i",
            [$id]
        );
    }

    // Verificar si el usuario puede editar la canción
    public function can_user_edit_song(int $song_id, int $user_id): bool {
        $result = $this->data_provider->sql_execute_prepared(
            "SELECT 1 FROM tbl_songs WHERE id = ? AND user_id = ?",
            "ii",
            [$song_id, $user_id]
        );

        if ($result === false) return false;

        $row = $this->data_provider->sql_get_fetchassoc($result);
        return $row !== null;
    }

    // Buscar canciones
    public function search_songs(string $searchTerm): array {
        $searchTerm = "%{$searchTerm}%";

        $result = $this->data_provider->sql_execute_prepared(
            "SELECT s.id, s.title, s.artist, s.genre, s.review, s.rating, s.created_at,
                    u.id as user_id, u.username, u.full_name, u.profile_image
             FROM tbl_songs s
             JOIN tbl_users u ON s.user_id = u.id
             WHERE s.title LIKE ? OR s.artist LIKE ? OR s.genre LIKE ?
             ORDER BY s.id DESC",
            "sss",
            [$searchTerm, $searchTerm, $searchTerm]
        );

        if ($result === false) return [];
        return $this->data_provider->sql_get_rows_assoc($result);
    }
}
?>
