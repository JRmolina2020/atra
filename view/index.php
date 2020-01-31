<?php
require 'header.php';
?>

<H1>ANOTACIONES</H1>
<p> HOLA <strong><?php echo $_SESSION['nombre'] ?></strong> Es importante saber las siguientes
    recomendaciones:</p>
<p class="mt-5"><strong>1:</strong> ENVIAR LAS <strong>FACTURAS</strong> Y <strong>NOTAS</strong>
    DIARIAMENTE.</p>
<p><strong>2:</strong> PRIMERO SE ENVIA <strong>LA FACTURA , </strong> DE SEGUNDO <strong>LA NOTA CREDITO , </strong> DE
    TERCERO LAS
    <strong>NOTAS DEBITOS</strong> SI LAS HAY.
    <p><strong class="text-danger">El envio debe realizar por separado.</strong></p>
</p>
<p><strong>3:</strong> UNA VEZ ENVIADA LA FACTURA O LAS NOTAS, SE DEBE ESPERAR EL MENSAJE DE CONFIRMACION Y</p>
<p>Revisar en la plataforma FELAM.</p>

<?php
require 'footer.php';
?>