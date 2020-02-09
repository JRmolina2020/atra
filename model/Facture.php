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
        $sql = "SELECT 
        V.ID as IDF, V.NOFACTURA as numero,V.FEC_COMPRA as fecha_documento,TP.prefijo as prefijo,
        V.NOFACTURA as facturap,V.PEDIDO as pedido, 
        V.IDFORMPAGO as metodo_pago, FO.DESCRIPCION AS manera_pago,
        V.SUBTOTAL as valor_bruto, V.IVA as valor_iva,
        V.RETEFUENTE as valor_retencion,DCTO as valor_descuento, 
        V.TOTAL as valor_neto,V.FEC_VENC as fecha_expiracion,C.CODIGO as codigo,
        C.NIT as nit ,C.REPRESENTANTE as nombres ,C.tipo_regimen as tipo_regimen,
        CI.CODDIAN as ciudad,B.nombre as barrio,
        C.TELEFONOS as telefono,C.DIRECCION as direccion,
        C.ID as documento, C.EMPRESA as punto_venta,
        C.DPTO as departamento,c.cliente as tipo_persona,U.NOMBRE as asesor,U.DOC as zona
        FROM ventas V 
        INNER JOIN clientes C 
        ON V.TERCERO = C.CODIGO 
        LEFT JOIN barrios B 
        ON C.BARRIO = B.codigo 
        INNER JOIN tipos_facturas TP
        ON V.IDTIPO = TP.ID 
        INNER JOIN usuarios U
        ON U.USUARIO = V.VENDEDOR
        INNER JOIN ciudades CI
        ON CI.CODIGO = C.CIUDAD
        INNER JOIN formas_pagos FO
        ON FO.ID = V.IDFORMPAGO
        WHERE   V.FEC_COMPRA = '$fecha' and TP.ID NOT IN(7)";
        return ejecutarConsulta($sql);
    }
    public function notacredito($fecha)
    {
        $sql = "SELECT 
        V.ID as IDF,CO.CONSECUTIVO as consecutivo,CO.FEC_COMPRA as fecha_documento, 
        CO.IDTIPO as tipo_documento,TP.prefijo as prefijo,FO.DESCRIPCION as manera_pago,
        CO.NOFACTURA as facturap,V.nofactura as vnot,V.PEDIDO as pedido, 
        CO.SUBTOTAL as valor_bruto, CO.IVA as valor_iva,
        CO.RETEFUENTE as valor_retencion,CO.observacion as observacion,
        CO.TOTAL as valor_neto,CO.FEC_VENC as fecha_expiracion,
        C.CODIGO as codigo,C.NIT as nit ,C.REPRESENTANTE as nombres ,C.tipo_regimen as tipo_regimen,
        CI.CODDIAN as ciudad,B.nombre as barrio,
        C.TELEFONOS as telefono,C.DIRECCION as direccion,
        C.ID as documento, C.EMPRESA as punto_venta,
        C.DPTO as departamento,c.cliente as tipo_persona,U.NOMBRE as asesor,U.DOC as zona
        FROM ventas V 
        INNER JOIN clientes C 
        ON V.TERCERO = C.CODIGO 
        LEFT JOIN barrios B 
        ON C.BARRIO = B.codigo 
        LEFT JOIN compras CO
        ON V.NOFACTURA = CO.NOFACTURA
        INNER JOIN tipos_facturas TP
        ON V.IDTIPO = TP.ID 
        INNER JOIN usuarios U
        ON U.USUARIO = V.VENDEDOR
        INNER JOIN ciudades CI
        ON CI.CODIGO = C.CIUDAD
        INNER JOIN formas_pagos FO
        ON FO.ID = V.IDFORMPAGO
        WHERE CO.FEC_COMPRA  = '$fecha'";
        return ejecutarConsulta($sql);
    }
    public function detalle($id)
    {
        $sql = "SELECT P.IDMARCA as tipo,P.CODIGO as codigo,P.NOMBRE as nombre,VD.UNID as cantidad ,VD.CAJA as caja,
        VD.VRCAJA as valor_caja,
        VD.IDBODEGA as bodega,
        VD.VRUNITARIO  as valor_referencial,VD.VRUNITARIO as valor_unitario_bruto, VD.TOTAL as subtotal,
        VD.IVA as iva,VD.DESCUENTOA AS descuentoA,VD.DESCUENTOB as descuentoB,VD.TOTAL as totalvd
        FROM ventas_detalles VD
        INNER JOIN productos P 
        ON VD.IDPROD = P.REFERENCIA
        WHERE VD.IDVENTA ='$id'";
        return ejecutarConsulta($sql);
    }
}