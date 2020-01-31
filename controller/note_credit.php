<?php

require_once "../model/Facture.php";
require "inc/zipfile.inc.php";
require "authapi.php";
class App
{
    public $fac;
    public $id;
    public $rspta;
    public $reg;
    public $fecha; //fecha actual.zip
    public $fechac; //fecha consulta parametro
    public $detalle;
    public function __construct()
    {
        $this->fac = new Facture();
        //parametro para la consulta por fecha
        $this->fechac = isset($_POST["fecha"]) ? ($_POST["fecha"]) : "";
        $this->rspta = $this->fac->notacredito($this->fechac);

        date_default_timezone_set("America/Bogota");
        $this->fecha = date("Y-m-d");
    }
    function detalle($id)
    {
        $id = $id;
        $this->detalle = array();
        $this->rsptad = $this->fac->detalle($id);
        while ($this->reg = $this->rsptad->fetch_object()) {
            if (
                $this->reg->valor_unitario_bruto < 0.01 || $this->reg->valor_unitario_bruto == ""
                || $this->reg->valor_unitario_bruto == 0
            ) {
                $valor_unitario_bruto = 0.01;
            } else {
                $valor_unitario_bruto = $this->reg->valor_unitario_bruto;
            }
            $this->detalle[] = array(
                "tipo" => "1",
                "marca" => "",
                "codigo" => $this->reg->codigo,
                "nombre" => $this->reg->nombre,
                "cantidad" => $this->reg->cantidad,
                "impuestos" => array(
                    array(
                        "tipo" => "01",
                        "porcentaje" => $this->reg->iva
                    )
                ),
                "descuentos" => array(
                    array(
                        "razon" => "Descuento",
                        "valor" => $this->reg->descuento,
                        "codigo" => "00",
                        "porcentaje" => 0.0
                    )
                ),
                "tipo_gravado" => 1,
                "valor_referencial" => 0.0,
                "valor_unitario_bruto" => $valor_unitario_bruto,
                "valor_unitario_sugerido" => 0.0
            );
        }
        return ($this->detalle);
    }
    function Consultas()
    {
        //validaciones
        while ($this->reg = $this->rspta->fetch_object()) {
            if ($this->reg->ciudad == "") {
                $ciudad = 20001;
            } else {
                $ciudad = $this->reg->ciudad;
            }
            //VALIDANDO TELEFONO
            if ($this->reg->telefono == "" || $this->reg->telefono == 0 || $this->reg->telefono == 1) {
                $telefono = 11111111;
            } else {
                $telefono = substr($this->reg->telefono, 0, 10);
            }

            if ($this->reg->tipo_documento == 3 || $this->reg->tipo_documento == 6) {
                $tipo_documento = 91;
            }
            //end validaciones
            $data[] = array(
                'nota' => "ATRATO",
                "numero" => $this->reg->consecutivo,
                "codigo_empresa" => 80,
                "tipo_documento" => $tipo_documento,
                "prefijo" => $this->reg->prefijo,
                'fecha_documento' =>  "2019-12-06",
                "valor_descuento" =>  0,
                "anticipos" => null,
                "valor_ico" => 0.0,
                "valor_iva" => $this->reg->valor_iva,
                "valor_bruto" => $this->reg->valor_bruto,
                "valor_neto" => $this->reg->valor_neto,
                "metodo_pago" => 1,
                "valor_retencion" => $this->reg->valor_retencion,
                "factura_afectada" => 0,
                "fecha_expiracion" =>  $this->reg->fecha_expiracion,
                //CLIENTES ARRAY
                'cliente'     => array(
                    "codigo" => $this->reg->nit,
                    "nombres" => $this->reg->nombres,
                    "apellidos" => $this->reg->nombres,
                    "departamento" => $this->reg->departamento,
                    "ciudad" => $ciudad,
                    "barrio" => $this->reg->barrio,
                    "correo" => "",
                    "telefono" => intval($telefono),
                    "direccion" => $this->reg->direccion,
                    "documento" => $this->reg->documento,
                    "punto_venta" =>  $this->reg->codigo,
                    "obligaciones" => ["ZZ"],
                    "razon_social" => $this->reg->nombres,
                    "punto_venta_nombre" => $this->reg->punto_venta,
                    "codigo_postal" => "000000",
                    "nombre_comercial" => $this->reg->punto_venta,
                    "numero_mercantil" => 0,
                    "informacion_tributaria" => "ZZ",
                    "tipo_persona" => 1,
                    "tipo_regimen" => $this->reg->tipo_regimen,
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
                        "fecha" =>  $this->reg->fecha_documento,
                        "valor" => 0.0,
                        "metodo_pago" => 1,
                        "detalle_pago" => "ZZZ"
                    )
                ),
                'descuentos'     => array(
                    array(
                        "razon" => null,
                        "valor" => 0,
                        "codigo" => null,
                        "porcentaje" => 0.0
                    )
                ),
                'extensibles'     => array(
                    "peso" => 0.0,
                    "zona" => "",
                    "orden" => 0,
                    "asesor" => $this->reg->asesor,
                    "pedido" => $this->reg->pedido,
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
                    "razon" => 4,
                    "factura" => $this->reg->facturap,
                    "id_felam" => 0,
                    "tipo_documento" => "33",
                    "descripcion_razon" => "En este apartado se genera la nota debito con fines internos
                    entre la empresa y el cliente referente"
                ),
                'nota_credito'     => array(
                    "razon" => 5,
                    "factura" => $this->reg->facturap,
                    "id_felam" => 0,
                    "tipo_documento" => "23",
                    "descripcion_razon" => "En este apartado se genera la nota credito con fines internos
                    entre la empresa y el cliente referente"
                ),
                //productos
                'productos'     =>  $this->detalle($this->reg->IDF)
            );
        }
        //end productos

        if (empty($data)) {
            header("Location: ../view/errnote.php");;
            die();
        } else {
            echo json_encode($data);
            // $jstring =  json_encode($data, true);
            // $zip = new ZipArchive();
            // $filename = "archivo-" . $this->fecha . ".zip";
            // if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
            //     exit("cannot open <$filename>\n");
            // }
            // $zip->addFromString("archivo-" . $this->fecha . ".txt", $jstring);
            // $zip->close();
            // $api = new Login();
            // $api->Uploader($filename);
        }
    }
}

$app = new App();
$app->Consultas();