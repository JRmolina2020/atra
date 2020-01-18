<?php
require 'header.php';
?>
<div class="alert alert-primary" role="alert">
    HOLA <strong><?php echo $_SESSION['nombre'] ?></strong> has iniciado sección.
</div>

<div class="row mt-5">
    <div class="col-12 col-md-12">
        <!-- Contenido -->
        <form method="post" action="../controller/authapi.php" id="form" name="form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="usuario">Seleccione archivo para enviar</label>
                <input required type="file" class="form-control" id="file" name="file">
            </div>
            <input name="cargar_archivo" class="btn btn-primary" type="submit" value="Enviar">
        </form>
        <br>
        <!-- Fin Contenido -->
    </div>
</div><!-- Fin row -->
<?php
require 'footer.php';
?>
<script type="text/javascript" src="scripts/facture.js"></script>