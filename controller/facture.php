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
        $this->rspta = $this->fac->cabezera($this->fechac);

        date_default_timezone_set("America/Bogota");
        $this->fecha = date("Y-m-d");
    }
    function detalle($id)
    {
        $id = $id;
        $this->detalle = array();
        $this->rsptad = $this->fac->detalle($id);
        while ($this->reg = $this->rsptad->fetch_object()) {

            //VALIDANDO CANTIDAD
            if ($this->reg->cantidad == 0) {
                $cantidad = $this->reg->caja;
                $valor_unitario_bruto = $this->reg->valor_caja;
            } else {
                $cantidad = $this->reg->cantidad;
                if (
                    $this->reg->valor_unitario_bruto < 0.01 || $this->reg->valor_unitario_bruto == ""
                    || $this->reg->valor_unitario_bruto == 0
                ) {
                    $valor_unitario_bruto = 100;
                } else {
                    $valor_unitario_bruto = $this->reg->valor_unitario_bruto;
                }
            }

            //END
            $this->detalle[] = array(
                "tipo" => "1",
                "marca" => "",
                "codigo" => $this->reg->codigo,
                "nombre" => $this->reg->nombre,
                "cantidad" => $cantidad,
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

            //validando metodo de pago

            if (
                $this->reg->metodo_pago == 1 || $this->reg->metodo_pago == 13
                || $this->reg->metodo_pago == 8
            ) {
                $metodo_pago = 1;
            } else {
                $metodo_pago = 2;
            }
            //validando tipo de regimen
            if ($this->reg->tipo_regimen == null) {
                $tipo_regimen = 49;
            } else {
                $tipo_regimen = $this->reg->tipo_regimen;
            }
            //validando departamento
            if ($this->reg->departamento == null) {
                $departamento = 20;
            } else {
                $departamento = $this->reg->departamento;
            }

            //quintando prefijo al numero de la factura
            $numero = preg_replace('/[^0-9]/', '', $this->reg->numero);
            //end validaciones
            $data[] = array(
                'nota' => "ATRATO",
                "numero" => $numero,
                "codigo_empresa" => 80,
                "tipo_documento" => '01',
                "prefijo" => $this->reg->prefijo,
                'fecha_documento' => '2019-12-11',
                "valor_descuento" =>  $this->reg->valor_descuento,
                "anticipos" => null,
                "valor_ico" => 0.0,
                "valor_iva" => $this->reg->valor_iva,
                "valor_bruto" => $this->reg->valor_bruto,
                "valor_neto" => $this->reg->valor_neto,
                "metodo_pago" => $metodo_pago,
                "valor_retencion" => $this->reg->valor_retencion,
                "factura_afectada" => 0,
                "fecha_expiracion" =>  $this->reg->fecha_expiracion,
                //CLIENTES ARRAY
                'cliente'     => array(
                    "codigo" => $this->reg->nit,
                    "nombres" => $this->reg->nombres,
                    "apellidos" => $this->reg->nombres,
                    "departamento" => $departamento,
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
                    "tipo_regimen" => $tipo_regimen,
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
                        "metodo_pago" => $metodo_pago,
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
            header("Location: ../view/errfacture.php");;
            die();
        } else {
            //echo json_encode($data);
            $jstring =  json_encode($data, true);
            $zip = new ZipArchive();
            $filename = "archivo-" . $this->fecha . ".zip";
            if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
                exit("cannot open <$filename>\n");
            }
            $zip->addFromString("archivo-" . $this->fecha . ".txt", $jstring);
            $zip->close();
            $api = new Login();
            $api->Uploader($filename);
        }
    }
}

$app = new App();
$app->Consultas();