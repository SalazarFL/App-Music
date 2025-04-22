<?php
class cls_Message {
    /**
     * Muestra un mensaje al usuario
     * @param string $message Mensaje a mostrar
     * @param string $type Tipo de mensaje (success, error, warning, info)
     * @param string $action Acción adicional
     * @return void
     */
    public static function show_message(string $message, string $type, string $action): void {
        $messageText = $message;

        if (empty($message)) {
            switch ($action) {
                case 'success_insert':
                    $messageText = "La información ha sido ingresada correctamente";
                    break;
                case 'success_update':
                    $messageText = "La información ha sido actualizada correctamente";
                    break;
                case 'success_delete':
                    $messageText = "La información ha sido eliminada correctamente";
                    break;
                default:
                    $messageText = "Operación completada";
            }
        }

        $cssClass = match($type) {
            'success' => 'alert alert-success',
            'error' => 'alert alert-danger',
            'warning' => 'alert alert-warning',
            'info' => 'alert alert-info',
            default => 'alert alert-info'
        };

        echo "<div class='{$cssClass}'>{$messageText}</div>";
    }
}
?>
