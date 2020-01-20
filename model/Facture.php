<?php
require "../config/conexion.php";

class Facture
{
    public function __construct()
    {
    }
    public function index()
    {
        $sql = "SELECT FEC_COMPRA as fecha_documento, IDTIPO as tipo_documento,prefijo,V.ID as numero,NOFACTURA 
        as nota,IDFORMPAGO 
        as metodo_pago,SUBTOTAL AS valor_bruto,V.IVA as valor_iva,RETEFUENTE as valor_retencion,DCTO 
        as valor_descuento,V.TOTAL as valor_neto,
        FEC_VENC as fecha_expiracion
        FROM ventas v 
        INNER JOIN tipos_facturas TP
        ON V.IDTIPO = TP.ID";
        return ejecutarConsulta($sql);
    }

    public function cabezera($fecha)
    {
        $sql = "SELECT FEC_COMPRA as fecha_documento, IDTIPO as tipo_documento,prefijo, 
        V.ID as numero,V.IDFORMPAGO as metodo_pago,SUBTOTAL as valor_bruto, 
        V.IVA as valor_iva,RETEFUENTE as valor_retencion,DCTO as valor_descuento, V.TOTAL as valor_neto,
        FEC_VENC as fecha_expiracion,C.CODIGO as codigo,C.REPRESENTANTE as nombres , 
        C.CIUDAD AS ciudad,B.nombre AS barrio,C.TELEFONOS as telefono,C.DIRECCION AS direccion,
        C.ID as documento, C.EMPRESA as punto_venta,C.DPTO as departamento,c.cliente as tipo_persona 
        FROM ventas V INNER JOIN clientes C 
        ON V.TERCERO = C.CODIGO 
        INNER JOIN barrios B 
        ON C.BARRIO = B.codigo 
        INNER JOIN tipos_facturas TP
        ON V.IDTIPO = TP.ID 
        WHERE V.FEC_COMPRA = '$fecha'";
        return ejecutarConsulta($sql);
    }
    public function detalle($id)
    {
        $sql = "SELECT P.IDMARCA as tipo,P.CODIGO as codigo,P.NOMBRE as nombre,VD.CANT_EMPAQ as cantidad ,
        VD.VRUNITARIO  as valor_referencial,VD.VRUNITARIO as valor_unitario_bruto, VD.TOTAL as subtotal,
        VD.IVA as iva,VD.DESCUENTOA as descuento
        FROM ventas_detalles VD
        INNER JOIN productos P 
        ON VD.IDPROD = P.REFERENCIA
        WHERE VD.IDVENTA ='$id'";
        return ejecutarConsulta($sql);
    }
}
