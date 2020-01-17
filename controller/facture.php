<?php
require_once "../model/Facture.php";

class App
{
    function Consultas()
    {
        $fac = new Facture();
        $id = 56857;
        $rspta = $fac->cabezera($id);
        $reg = $rspta->fetch_object();
        $data = array(
            'nota' => "ATRATO",
            "numero" => intval($reg->numero),
            "codigo_empresa" => 80,
            "tipo_documento" => $reg->tipo_documento,
            "prefijo" => $reg->prefijo,
            'fecha_documento' => $reg->fecha_documento,
            "valor_descuento" =>  $reg->valor_descuento,
            "anticipos" => null,
            "valor_ico" => 0.0,
            "valor_iva" => $reg->valor_iva,
            "valor_bruto" => $reg->valor_bruto,
            "valor_neto" => $reg->valor_neto,
            "metodo_pago" => intval($reg->metodo_pago),
            "valor_retencion" => $reg->valor_retencion,
            "factura_afectada" => 0,
            "fecha_expiracion" => $reg->fecha_expiracion,
            //CLIENTES ARRAY
            'cliente'     => array(
                "codigo" => $reg->codigo,
                "nombres" => $reg->nombres,
                "apellidos" => $reg->nombres,
                "departamento" => $reg->departamento,
                "ciudad" => "47960",
                "barrio" => $reg->barrio,
                "correo" => "",
                "telefono" => $reg->telefono,
                "direccion" => $reg->direccion,
                "documento" => $reg->documento,
                "punto_venta" => $reg->punto_venta,
                "obligaciones" => ["ZZ"],
                "razon_social" => $reg->nombres,
                "punto_venta_nombre" => $reg->punto_venta,
                "tipo_persona" => 1,
                "codigo_postal" => "000000",
                "nombre_comercial" => $reg->punto_venta,
                "numero_mercantil" => 0,
                "informacion_tributaria" => "ZZ",
                //criticos
                "tipo_regimen" => "48",
                "es_responsable_iva" => false,
                "tipo_identificacion" => 13,
            ),
            'factura'     => array(
                "moneda" => null,
                "subtipo_factura" => "10",
                "intercambio_acordado" => 0.0
            ),
            'pagos'     => array(
                array(
                    "fecha" => $reg->fecha_documento,
                    "valor" => 0.0,
                    "metodo_pago" => 1,
                    "detalle_pago" => "ZZZ"
                )
            ),
            'descuentos'     => array(
                array(
                    "razon" => null,
                    "valor" => $reg->valor_descuento,
                    "codigo" => null,
                    "porcentaje" => 0.0
                )
            ),
            'extensibles'     => array(
                "peso" => 0.0,
                "zona" => "",
                "orden" => 0,
                "asesor" => "",
                "pedido" => $reg->numero,
                "canastas" => 0,
                "planilla" => "",
                "logistica" => "",
                "recibo_caja" => 0.0,
                "distribucion" => "",
                "asesor_numero" => 0,
                "logistica_numero" => 0,
                "cantidad_productos" => 0,
                "distribucion_numero" => 0
            ),
            'nota_debito'     => array(
                "razon" => 0,
                "factura" => null,
                "id_felam" => 0,
                "tipo_documento" => "",
                "descripcion_razon" => null
            ),
            'nota_credito'     => array(
                "razon" => 0,
                "factura" => null,
                "id_felam" => 0,
                "tipo_documento" => "",
                "descripcion_razon" => null
            ),
        ); //end
        echo json_encode([$data]);
    }
}

$app = new App();
$app->Consultas();
