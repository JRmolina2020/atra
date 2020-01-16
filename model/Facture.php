<?php
require "../config/conexion.php";

class Facture
{
    public function __construct()
    {
    }
    public function index()
    {
        $sql = "SELECT FEC_COMPRA as fecha_documento, IDTIPO as tipo_documento,prefijo,V.ID as numero,NOFACTURA as nota,IDFORMPAGO 
        as metodo_pago,SUBTOTAL AS valor_bruto,V.IVA as valor_iva,RETEFUENTE as valor_retencion,DCTO 
        as valor_descuento,V.TOTAL as valor_neto,
        FEC_VENC as fecha_expiracion , FEC_COMPRA as fecha_pago
        FROM ventas v 
        INNER JOIN tipos_facturas TP
        ON V.IDTIPO = TP.ID
        WHERE FEC_COMPRA = '2013-03-19'";
        return ejecutarConsulta($sql);
    }
}
