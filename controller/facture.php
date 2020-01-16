<?php

require_once "../model/Facture.php";
$facture = new Facture();
switch ($_GET["op"]) {
    case 'listar':
        $rspta = $facture->index();
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => '<button class="btn btn-sm btn-primary" 
                onclick="mostrar(' . $reg->numero . ')"><i class="fa fa-sticky-note-o">
                </i></button> ' . '<button class="btn btn-sm btn-warning" 
                onclick="mostrar(' . $reg->numero . ')"><i class="fa fa-star">
                </i></button> ',
                "1" => $reg->fecha_documento,
                "2" => $reg->numero,
                "3" => $reg->tipo_documento,
                "4" => $reg->prefijo,
                "5" => $reg->nota,
                "6" => $reg->metodo_pago,
                "7" => $reg->valor_bruto,
                "8" => $reg->valor_iva,
                "9" => $reg->valor_retencion,
                "10" => $reg->valor_descuento,
                "11" => $reg->valor_neto,
                "12" => $reg->fecha_expiracion,
            );
        }
        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );
        echo json_encode($results);
        break;
}
