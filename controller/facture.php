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

    //variables globales para producto
    public $tipo; //tipo_producto
    public $descuento;
    //descuentos
    public $cantidadunit;
    public $cantidadcajaunit;
    public $valor_unit;
    public $valor_unitcaja;


    public function __construct()
    {
        $this->fac = new Facture();
        //parametro para la consulta por fecha
        $this->fechac = isset($_POST["fecha"]) ? ($_POST["fecha"]) : "";
        $this->rspta = $this->fac->cabezera($this->fechac);
        $this->tipo = 1;
        $this->descuento = 0;
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
                $this->cantidadcajaunit = $cantidad; //obteniedo cantidad para el descuento;
                $valor_unitario_bruto = $this->reg->valor_caja;
                $this->valor_unitcaja = $valor_unitario_bruto;
                $embalaje = 'caja';
            } else {
                $cantidad = $this->reg->cantidad;
                $this->cantidadunit = $cantidad;
                $embalaje = 'und';
                if (
                    $this->reg->valor_unitario_bruto < 0.01 || $this->reg->valor_unitario_bruto == ""
                    || $this->reg->valor_unitario_bruto == 0
                ) {
                    $valor_unitario_bruto = 0.01;
                    $this->tipo = 4; //tipo de producto
                    $this->descuento = 0; //descuento del producto
                } else {
                    $valor_unitario_bruto = $this->reg->valor_unitario_bruto;
                    $this->valor_unit = $valor_unitario_bruto;
                    $this->tipo = 1;
                    $this->descuento = $this->reg->descuentoB;
                }
            }
            if ($this->reg->descuentoA == 0) {
                $base = 0;
            } else if ($this->reg->descuentoA == 0) {
                $base = 0;
            } else if ($this->reg->descuentoB == 0) {
                $base1 = $this->valor_unitcaja * $this->cantidadcajaunit;
                $db = $base1 * $this->reg->descuentoA;
                $base = $base1 - $db / 100;
            } else {
                $base1 = $this->valor_unit * $this->cantidadunit;
                $db = $base1 * $this->reg->descuentoB;
                $base1 = $base1 - $db / 100;
                //obteniendo descuentoB
                $base2 = $base1;
                $da = $base2 * $this->reg->descuentoA;
                $base2 = $base2 - $da / 100;
                $base = $base2;
            }

            //END
            $this->detalle[] = array(
                "tipo" => $this->tipo,
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
                        "razon" => "DescuentoB",
                        "valor" => 0.0,
                        "codigo" => "00",
                        "porcentaje" =>  $this->reg->descuentoB
                    ),
                    array(
                        "razon" => "DescuentoA",
                        "valor" => 0.0,
                        "codigo" => "00",
                        "base" => round($base, 2),
                        "porcentaje" =>  $this->reg->descuentoA
                    ),
                ),
                "extensibles" =>
                array(
                    "tipo_embalaje" => "",
                    "tipo_empaque" => $embalaje,
                    "bodega" => $this->reg->bodega
                ),

                "tipo_gravado" => 1,
                "valor_referencial" => 0.0,
                "valor_unitario_bruto" => $valor_unitario_bruto,
                "valor_unitario_sugerido" => $this->reg->valor_caja
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
                $telefono = substr($this->reg->telefono, 0, 10); //recortando telefonos a 10 digitos
            }
            //validando metodo de pago
            if (
                $this->reg->metodo_pago == 1 || $this->reg->metodo_pago == 13
                || $this->reg->metodo_pago == 8
            ) {
                $metodo_pago = 1; //contado
            } else {
                $metodo_pago = 2; //credito
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
            //validando nit
            $nit  = str_replace('.', '', $this->reg->nit);
            $nit = preg_replace('/-/', '', $nit);
            $nit = substr($nit, 0, 10);
            //end validaciones

            $data[] = array(
                "nota" => $this->reg->manera_pago,
                "numero" => $numero,
                "codigo_empresa" => 80,
                "tipo_documento" => '01',
                "prefijo" => 'SETT',
                'fecha_documento' => '2020-02-02',
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
                    "codigo" => $this->reg->codigo,
                    "nombres" => $this->reg->nombres,
                    "apellidos" => $this->reg->nombres,
                    "departamento" => $departamento,
                    "ciudad" => $ciudad,
                    "barrio" => $this->reg->barrio,
                    "correo" => "",
                    "telefono" => intval($telefono),
                    "direccion" => $this->reg->direccion,
                    "documento" => $nit,
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
                        "detalle_pago" => "ZZZ",
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
                    "zona" => '',
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
        if (empty($data)) {
            header("Location: ../view/errfacture.php");;
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