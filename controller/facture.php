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
    //descuentos
    public $descuento;
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
        date_default_timezone_set("America/Bogota");
        $this->fecha = date("Y-m-d");

        //inicializaciones
        $this->tipo = 1;
        $this->descuento = 0;
    }
    function detalle($id)
    {
        $id = $id;
        $this->detalle = array();
        $this->rsptad = $this->fac->detalle($id);
        while ($this->reg = $this->rsptad->fetch_object()) {

            //Valindando la cantidad de productos si es en caja o si es por unidad
            if ($this->reg->cantidad == 0) { //si la cantidad und es 0 es por que es una caja
                $cantidad = $this->reg->caja; //le asignamos a la cantidad el total de cajas
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
                    $this->descuento = 0; //descuento del producto regalo
                } else {
                    $valor_unitario_bruto = $this->reg->valor_unitario_bruto;
                    $this->valor_unit = $valor_unitario_bruto;
                    $this->tipo = 1;
                    $this->descuento = $this->reg->descuentoB;
                }
            }
            //validando descuentos Base A Y B
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
            //END descuentos BASE A Y B 
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
        //VALIDACIONES
        while ($this->reg = $this->rspta->fetch_object()) {
            //Validando la ciudad del cliente.
            if ($this->reg->ciudad == "") {
                $ciudad = 20001;
            } else {
                $ciudad = $this->reg->ciudad;
            }
            //validando el barrio del cliente
            if ($this->reg->barrio == "DIVINO NIÃ‘O" || $this->reg->barrio == "divino niÃ±o") {
                $barrio = "divino nino";
            } else if ($this->reg->barrio == "450 AÃ‘OS") {
                $barrio = "450 ";
            } else {
                $barrio = $this->reg->barrio;
            }
            //Valindado el telefono del cliente
            if ($this->reg->telefono == "" || $this->reg->telefono == 0 || $this->reg->telefono == 1) {
                $telefono = 11111111;
            } else {
                $telefono = substr($this->reg->telefono, 0, 10); //recortando telefonos a 10 digitos
            }
            //validando el metodo de pago
            if (
                $this->reg->metodo_pago == 1 || $this->reg->metodo_pago == 13
                || $this->reg->metodo_pago == 8
            ) {
                $metodo_pago = 1; //contado
            } else {
                $metodo_pago = 2; //credito
            }
            //Vvalidando el tipo de regimen
            if ($this->reg->tipo_regimen == null || $this->reg->tipo_regimen == 0) {
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
            //quintando prefijo al numero de la factura EJEM: B123 -> 123
            $numero = preg_replace('/[^0-9]/', '', $this->reg->numero);
            //validando nit
            $nit  = str_replace('.', '', $this->reg->nit);
            $nit = preg_replace('/-/', '', $nit);
            $nit = substr($nit, 0, 10);
            //Quitando las letras del pedido EJEM : APP123 -> 123
            $pedido = preg_replace('/[^0-9]/', '', $this->reg->pedido);
            //end
            //validar el mensaje de resolucion 
            if ($this->reg->prefijo == "B") {
                $resolucion = "RESOLUCION DIAN 18762009353951 FECHA: 2018/07/25 DEL No. b109728 AL No. b200000 prefijo[B] habilita.";
            } elseif ($this->reg->prefijo == "C") {
                $resolucion = "RESOLUCION DIAN 18762009353951 FECHA: 2018/07/25 DEL No. c17612 AL No. c30000 PREFIJO [C] habilita.";
            } elseif ($this->reg->prefijo == "TAT") {
                $resolucion = "Res. Dian No. 18762010933894 Fecha : 2018-10-25 Del TAT 19229 al tat 30000 habilita FACTURA POR COMPUTADOR.";
            } elseif ($this->reg->prefijo == "F") {
                $resolucion = "RESOLUCION DIAN 240000035883 FECHA: 2015/09/21 DEL No. 776 AL No. 10000 PREFIJO [F] HABILITA.";
            } elseif ($this->reg->prefijo == "V") {
                $resolucion = "Res. Dian No. 240000018505 Fecha : 2009-07-10 Del V-1 al 4000 HABILITA FACTURA POR COMPUTADOR.";
            } elseif ($this->reg->prefijo == "FF") {
                $resolucion = "RESOLUCION DIAN 18762015697813 FECHA: 2019/07/15 DEL No. 30001 AL No. 50000 PREFIJO [FF] habilita.";
            }
            //end resoluciones 


            $pre = "SETT";
            $numeri = $pre . $numero;

            //ARRAYS
            $data[] = array(
                "nota" => $this->reg->manera_pago,
                "numero" => $numero,
                "codigo_empresa" => 80,
                "tipo_documento" => '01',
                "prefijo" => 'SETT', //this->reg->prefijo
                'fecha_documento' => '2019-12-30',
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
                    "barrio" => $barrio,
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
                    "zona" => $this->reg->zona,
                    "orden" => 0,
                    "asesor" => $this->reg->asesor,
                    "pedido" => $pedido,
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
                    "tipo_documento" => "",
                    "descripcion_razon" => ""
                ),
                'nota_credito'     => array(
                    "razon" => 5,
                    "factura" => $numeri, //reg->facturap tiene el prefijo de la factura
                    "id_felam" => 0,
                    "tipo_documento" => "23",
                    "descripcion_razon" => "En este apartado se genera la nota credito con fines internos entre la empresa y el cliente referente"
                ),
                //productos
                'productos'     =>  $this->detalle($this->reg->IDF)
            );
        }
        //END
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