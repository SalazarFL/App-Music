<?php
/* Archivo controlador que contiene los llamados a las acciones de la vista
   (ADD, EDIT, DELETE, SEARCH) */

require_once($_SERVER["DOCUMENT_ROOT"] . "/music-app/global.php");
require_once(__CLS_PATH . "cls_music.php");

class ctr_Music {
    private cls_Music $songdata;

    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->songdata = new cls_Music();
    }

    /**
     * Obtiene todas las canciones
     * @return array Array con las canciones
     */
    public function get_songs(): array {
        return $this->songdata->get_songs();
    }

    /**
     * Obtiene una canción por su ID
     * @param int $id ID de la canción
     * @return array|null Datos de la canción o null si no existe
     */
    public function get_song_by_id(int $id): ?array {
        return $this->songdata->get_song_by_id($id);
    }

    /**
     * Maneja el evento de click en el botón de guardar (para añadir nueva canción)
     * @return bool True si se guardó correctamente, false en caso contrario
     */
    public function btn_save_click(): bool {
        if (!isset($_POST['txt_title']) || !isset($_POST['txt_artist']) ||
            !isset($_POST['sel_genre']) || !isset($_POST['txt_review']) ||
            !isset($_POST['rating']) || !isset($_SESSION['USER_ID'])) {
            cls_Message::show_message("Por favor, complete todos los campos", "error", "");
            return false;
        }

        // Escapar y validar los datos ingresados
        $songdata = [
            'title' => htmlspecialchars($_POST['txt_title'], ENT_QUOTES, 'UTF-8'),
            'artist' => htmlspecialchars($_POST['txt_artist'], ENT_QUOTES, 'UTF-8'),
            'genre' => htmlspecialchars($_POST['sel_genre'], ENT_QUOTES, 'UTF-8'),
            'review' => htmlspecialchars($_POST['txt_review'], ENT_QUOTES, 'UTF-8'),
            'rating' => (int)$_POST['rating'],
            'user_id' => (int)$_SESSION['USER_ID']
        ];

        // Validar datos
        if (empty($songdata['title']) || empty($songdata['artist']) ||
            empty($songdata['genre']) || empty($songdata['review']) ||
            $songdata['rating'] < 1 || $songdata['rating'] > 5 ||
            $songdata['user_id'] < 0) {
            cls_Message::show_message("Datos inválidos. Por favor, verifique la información ingresada.", "error", "");
            return false;
        }

        if ($this->songdata->insert_song($songdata)) {
            cls_Message::show_message("Canción añadida correctamente", "success", "success_insert");
            return true;
        }

        return false;
    }

    /**
     * Maneja el evento de click en el botón de actualizar
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function btn_update_click(): bool {
        if (!isset($_POST['hdn_id']) || !isset($_POST['txt_title']) || !isset($_POST['txt_artist']) ||
            !isset($_POST['sel_genre']) || !isset($_POST['txt_review']) ||
            !isset($_POST['rating']) || !isset($_SESSION['USER_ID'])) {
            cls_Message::show_message("Por favor, complete todos los campos", "error", "");
            return false;
        }

                // Escapar y validar los datos ingresados
                $songdata = [
                    'id' => (int)$_POST['hdn_id'],
                    'title' => htmlspecialchars($_POST['txt_title'], ENT_QUOTES, 'UTF-8'),
                    'artist' => htmlspecialchars($_POST['txt_artist'], ENT_QUOTES, 'UTF-8'),
                    'genre' => htmlspecialchars($_POST['sel_genre'], ENT_QUOTES, 'UTF-8'),
                    'review' => htmlspecialchars($_POST['txt_review'], ENT_QUOTES, 'UTF-8'),
                    'rating' => (int)$_POST['rating'],
                    'user_id' => (int)$_SESSION['USER_ID']
                ];
        
                // Validar datos
                if ($songdata['id'] < 0 || empty($songdata['title']) || empty($songdata['artist']) ||
                    empty($songdata['genre']) || empty($songdata['review']) ||
                    $songdata['rating'] < 1 || $songdata['rating'] > 5 || $songdata['user_id'] < 0) {
                    cls_Message::show_message("Datos inválidos. Por favor, verifique la información ingresada.", "error", "");
                    return false;
                }
        
                if ($this->songdata->update_song($songdata)) {
                    cls_Message::show_message("Canción actualizada correctamente", "success", "success_update");
                    return true;
                } else {
                    cls_Message::show_message("No tienes permisos para editar esta canción", "error", "");
                    return false;
                }
        
                return false;
            }
        
            /**
             * Maneja el evento de click en el botón de eliminar
             * @return bool True si se eliminó correctamente, false en caso contrario
             */
            public function btn_delete_click(): bool {
                if (!isset($_POST['hdn_id']) || !isset($_SESSION['USER_ID'])) {
                    cls_Message::show_message("ID de canción no válido", "error", "");
                    return false;
                }
        
                $id = (int)$_POST['hdn_id'];
                $user_id = (int)$_SESSION['USER_ID'];
        
                if ($id < 0 || $user_id < 0) {
                    cls_Message::show_message("ID de canción no válido", "error", "");
                    return false;
                }
        
                if ($this->songdata->delete_song($id, $user_id)) {
                    cls_Message::show_message("Canción eliminada correctamente", "success", "success_delete");
                    return true;
                } else {
                    cls_Message::show_message("No tienes permisos para eliminar esta canción", "error", "");
                    return false;
                }
            }
        
            /**
             * Maneja el evento de búsqueda
             * @param string $searchTerm Término de búsqueda
             * @return array Resultados de la búsqueda
             */
            public function search_songs(string $searchTerm): array {
                if (empty($searchTerm)) {
                    return $this->get_songs();
                }
        
                return $this->songdata->search_songs($searchTerm);
            }
        }
        ?>
        