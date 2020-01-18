<?php
require_once "../model/Facture.php";
require("comprimidos/zipfile.php");

class App
{
    public $fac;
    public $id;
    public $rspta;
    public $reg;
    public $detalle = array();
    public function __construct()
    {
        $this->fac = new Facture();
        $this->id = 56857;
        $this->rspta = $this->fac->cabezera($this->id);
        $this->rsptad = $this->fac->detalle($this->id);
        $this->reg = $this->rspta->fetch_object();
    }
    function detalle()
    {
        while ($this->reg = $this->rsptad->fetch_object()) {
            $this->detalle[] = array(
                "tipo" => $this->reg->tipo,
                "marca" => "",
                "codigo" => $this->reg->codigo,
                "nombre" => $this->reg->nombre,
                "cantidad" => $this->reg->cantidad,
                "impuestos" => array(
                    array(
                        "tipo" => "01",
                        "porcentaje" => 0.0
                    )
                ),
                "descuentos" => array(
                    array(
                        "razon" => "Descuento",
                        "valor" => 0.0,
                        "codigo" => "00",
                        "porcentaje" => 0.0
                    )
                ),
                "tipo_gravado" => 1,
                "valor_referencial" => 0.0,
                "valor_unitario_bruto" => $this->reg->valor_unitario_bruto,
                "valor_unitario_sugerido" => 0.0
            );
        }
        return  $this->detalle;
    }
    function Consultas()
    {
        $data = array(
            'nota' => "ATRATO",
            "numero" => intval($this->reg->numero),
            "codigo_empresa" => 80,
            "tipo_documento" => $this->reg->tipo_documento,
            "prefijo" => $this->reg->prefijo,
            'fecha_documento' => $this->reg->fecha_documento,
            "valor_descuento" =>  $this->reg->valor_descuento,
            "anticipos" => null,
            "valor_ico" => 0.0,
            "valor_iva" => $this->reg->valor_iva,
            "valor_bruto" => $this->reg->valor_bruto,
            "valor_neto" => $this->reg->valor_neto,
            "metodo_pago" => intval($this->reg->metodo_pago),
            "valor_retencion" => $this->reg->valor_retencion,
            "factura_afectada" => 0,
            "fecha_expiracion" => $this->reg->fecha_expiracion,
            //CLIENTES ARRAY
            'cliente'     => array(
                "codigo" => $this->reg->codigo,
                "nombres" => $this->reg->nombres,
                "apellidos" => $this->reg->nombres,
                "departamento" => $this->reg->departamento,
                "ciudad" => "47960",
                "barrio" => $this->reg->barrio,
                "correo" => "",
                "telefono" => $this->reg->telefono,
                "direccion" => $this->reg->direccion,
                "documento" => $this->reg->documento,
                "punto_venta" => $this->reg->punto_venta,
                "obligaciones" => ["ZZ"],
                "razon_social" => $this->reg->nombres,
                "punto_venta_nombre" => $this->reg->punto_venta,
                "tipo_persona" => 1,
                "codigo_postal" => "000000",
                "nombre_comercial" => $this->reg->punto_venta,
                "numero_mercantil" => 0,
                "informacion_tributaria" => "ZZ",
                //criticos
                "tipo_thisimreg->en" => "48",
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
                    "fecha" => $this->reg->fecha_documento,
                    "valor" => 0.0,
                    "metodo_pago" => 1,
                    "detalle_pago" => "ZZZ"
                )
            ),
            'descuentos'     => array(
                array(
                    "razon" => null,
                    "valor" => $this->reg->valor_descuento,
                    "codigo" => null,
                    "porcentaje" => 0.0
                )
            ),
            'extensibles'     => array(
                "peso" => 0.0,
                "zona" => "",
                "orden" => 0,
                "asesor" => "",
                "pedido" => $this->reg->numero,
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
            //productos
            'productos'     =>  $this->detalle()

            //end productos
        ); //end
        echo json_encode([$data]);
    }
}

$app = new App();
$app->Consultas();
