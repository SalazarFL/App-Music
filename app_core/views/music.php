<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/music-app/global.php");
require_once(__CLS_PATH . "cls_html.php");
require_once(__CTR_PATH . "ctr_music.php");

$HTML = new cls_Html();
$ctr_Music = new ctr_Music();

// Inicialización de variables
$editMode = false;
$song = null;
$searchResults = [];
$isSearching = false;

// Procesar eliminación
if (isset($_POST['btn_delete'])) {
    $ctr_Music->btn_delete_click();
}

// Procesar búsqueda
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8');
    $searchResults = $ctr_Music->search_songs($searchTerm);
    $isSearching = true;
}

// Procesar actualización
if (isset($_POST['btn_update'])) {
    if ($ctr_Music->btn_update_click()) {
        header("Location: " . __SITE_PATH . "?updated=true");
        exit;
    } else {
        // Si falla la actualización, mantener en modo de edición
        $editMode = true;
        $song = [
            'id' => $_POST['hdn_id'],
            'title' => $_POST['txt_title'],
            'artist' => $_POST['txt_artist'],
            'genre' => $_POST['sel_genre'],
            'review' => $_POST['txt_review'],
            'rating' => $_POST['rating']
        ];
    }
}

// Procesar guardado de nueva canción
if (isset($_POST['btn_save'])) {
    if ($ctr_Music->btn_save_click()) {
        header("Location: " . __SITE_PATH . "?added=true");
        exit;
    }
}

// Verificar si estamos en modo edición
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $songId = (int)$_GET['edit'];
    $song = $ctr_Music->get_song_by_id($songId);
    if ($song) {
        $editMode = true;
    }
}

// Géneros musicales para el select
$genres = [
    "" => "Seleccione un género",
    "pop" => "Pop",
    "rock" => "Rock",
    "rap" => "Rap",
    "hiphop" => "Hip-Hop",
    "reggae" => "Reggae",
    "electronic" => "Electrónica",
    "classical" => "Clásica",
    "folk" => "Folk",
    "country" => "Country",
    "reggaeton" => "Reggaetón",
    "alternative" => "Alternativa",
    "metal" => "Metal",
    "blues" => "Blues",
    "indie" => "Indie"
];
?>

<div id="search_box">
    <form id="fm_search" method="get" action="">
        <div class="search-container">
            <input type="text" name="search" id="txt_search" class="search-input" placeholder="Buscar por título, artista o género ..." 
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : ''; ?>" />
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
            <?php if ($isSearching): ?>
                <a href="<?php echo __SITE_PATH; ?>" class="clear-search"><i class="fas fa-times"></i> Limpiar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div id="form_box">
    <h2 class="section-title"><?php echo $editMode ? 'Editar Canción' : '¿Qué canción estás escuchando?'; ?></h2>

    <form id="fm_song" method="post" action="">
        <?php if ($editMode): ?>
            <?php echo $HTML->html_hidden("hdn_id", "hdn_id", $song['id']); ?>
        <?php endif; ?>

        <div class="form-group">
            <label for="txt_title">Título</label>
            <?php echo $HTML->html_input_text("text", "txt_title", "txt_title", "form-control", $editMode && $song ? $song['title'] : '', "Título de la canción", "required"); ?>
        </div>

        <div class="form-group">
            <label for="txt_artist">Artista</label>
            <?php echo $HTML->html_input_text("text", "txt_artist", "txt_artist", "form-control", $editMode && $song ? $song['artist'] : '', "Nombre del artista o banda", "required"); ?>
        </div>

        <div class="form-group">
            <label for="sel_genre">Género</label>
            <?php echo $HTML->html_select("sel_genre", "sel_genre", "form-control", $genres, $editMode && $song ? $song['genre'] : '', "required"); ?>
        </div>

        <div class="form-group">
            <label for="txt_review">Reseña</label>
            <?php echo $HTML->html_textarea("txt_review", "txt_review", "form-control", $editMode && $song ? $song['review'] : '', "Escribe una breve reseña de la canción", 4, "required"); ?>
        </div>

        <div class="form-group">
            <label>Calificación</label>
            <?php echo $HTML->html_rating_stars("rating", $editMode && $song ? $song['rating'] : 5, "required"); ?>
        </div>

        <div class="button-container">
            <?php if ($editMode): ?>
                <button type="submit" name="btn_update" id="btn_update" class="button update-button">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="<?php echo __SITE_PATH; ?>" class="button cancel-button"><i class="fas fa-times"></i> Cancelar</a>
            <?php else: ?>
                <button type="submit" name="btn_save" id="btn_save" class="button">
                    <i class="fas fa-plus"></i> Añadir Canción
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div id="songs_panel">
    <h2 class="panel-title">Canciones Compartidas</h2>

    <?php
    $songs = $isSearching ? $searchResults : $ctr_Music->get_songs();

    if (empty($songs)) {
        echo "<div class='no-songs'><p>" .
             ($isSearching ? "No se encontraron resultados para tu búsqueda." : "No hay canciones compartidas aún. ¡Sé el primero en compartir!") .
             "</p><div class='empty-icon'><i class='fa-music'></i></div></div>";
    } else {
        foreach ($songs as $song) {
            $songId = (int)$song['id'];
            $title = htmlspecialchars($song['title'], ENT_QUOTES, 'UTF-8');
            $artist = htmlspecialchars($song['artist'], ENT_QUOTES, 'UTF-8');
            $genre = htmlspecialchars($song['genre'], ENT_QUOTES, 'UTF-8');
            $review = htmlspecialchars($song['review'], ENT_QUOTES, 'UTF-8');
            $rating = (int)$song['rating'];
            $createdAt = new DateTime($song['created_at']);
            $formattedDate = $createdAt->format("d/m/Y H:i");
            $genreText = $genres[$genre] ?? $genre;

            $stars = "";
            for ($i = 1; $i <= 5; $i++) {
                $stars .= "<i class='far fa-star" . ($i <= $rating ? " filled" : "") . "'></i>";
            }

            echo "
            <div class='songs-block'>
                <div class='song-header'>
                    <h3 class='song-title'>{$title}</h3>
                    <div class='song-actions'>
                        <a href='" . __SITE_PATH . "?edit={$songId}' class='edit-button' title='Editar'><i class='fas fa-edit'></i></a>
                        <form method='post' action='' class='delete-form' onsubmit='return confirm(\"¿Estás seguro de que deseas eliminar esta canción?\")'>
                            <input type='hidden' name='hdn_id' value='{$songId}' />
                            <button type='submit' name='btn_delete' class='delete-button' title='Eliminar'><i class='fas fa-trash'></i></button>
                        </form>
                    </div>
                </div>
                <div class='song-artist'><i class='fas fa-user'></i> {$artist}</div>
                <div class='song-info'>
                    <span class='song-genre'><i class='fas fa-music'></i> {$genreText}</span>
                    <span class='song-rating'>{$stars}</span>
                </div>
                <div class='song-review'>{$review}</div>
                <div class='song-date'>Compartido: {$formattedDate}</div>
            </div>";
        }
    }
    ?>
</div>

<!-- Mensajes de alerta según la operación -->
<?php if (isset($_GET['added'])): ?>
<script>
    $(document).ready(function() {
        $("<div class='alert alert-success'>Canción añadida correctamente</div>").insertBefore("#songs_panel").fadeIn('slow').delay(5000).fadeOut('slow');
    });
</script>
<?php endif; ?>

<?php if (isset($_GET['updated'])): ?>
<script>
    $(document).ready(function() {
        $("<div class='alert alert-success'>Canción actualizada correctamente</div>").insertBefore("#songs_panel").fadeIn('slow').delay(5000).fadeOut('slow');
    });
</script>
<?php endif; ?>
