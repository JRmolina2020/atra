<?php
require 'header.php';
?>
<div class="alert alert-primary" role="alert">
    HOLA <strong><?php echo $_SESSION['nombre'] ?></strong> has iniciado sección.
</div>

<div class="row mt-5">
    <div class="col-lg-3 col-md-12">
        <!-- Contenido -->
        <form method="post" action="../controller/facture.php">
            <input class="btn btn-danger" type="submit" value="Generar">
        </form>

    </div>
</div><!-- Fin row -->
<?php
require 'footer.php';
?>