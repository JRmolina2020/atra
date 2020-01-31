<?php
require 'header.php';
?>

<H1>ENVIAR FACTURAS</H1>
<div class="row mt-5">
    <div class="col-lg-3 col-md-12">
        <!-- Contenido -->
        <form method="post" action="../controller/facture.php">
            <div class="row">
                <div class="col-lg-10">
                    <div class="form-group">
                        <input class="form-control" placeholder="digitar fecha" type="text" readonly name="fecha"
                            id="datepicker">
                    </div>
                </div>
                <div class="col-lg-12">
                    <input class="btn btn-danger" type="submit" value="Enviar facturas">
                </div>
            </div>
        </form>
    </div>
</div><!-- Fin row -->
<h1>ENVIAR NOTAS CREDITOS</h1>
<div class="row mt-5">
    <div class="col-lg-3 col-md-12">
        <!-- Contenido -->
        <form method="post" action="../controller/note_credit.php">
            <div class="row">
                <div class="col-lg-10">
                    <div class="form-group">
                        <input class="form-control" placeholder="digitar fecha" type="text" readonly name="fecha"
                            id="datepickernota">
                    </div>
                </div>
                <div class="col-lg-12">
                    <input class="btn btn-warning" type="submit" value="Enviar nota credito">
                </div>
            </div>
        </form>
    </div>
</div><!-- Fin row -->
<?php
require 'footer.php';
?>
<script>
$(function() {
    $("#datepicker").datepicker({
        format: 'yyyy-mm-dd',
        locale: 'es-es',
    });
    $("#datepickernota").datepicker({
        format: 'yyyy-mm-dd',
        locale: 'es-es',
    });
});
</script>