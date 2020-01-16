var tabla;
function init() {
  cabezera();
}
//Función listarcategoria
function cabezera() {
  tabla = $("#example")
    .dataTable({
      dom: "Bfrtip",
      lengthChange: false,
      buttons: ["excel", "pdf"],
      ajax: {
        url: "../controller/facture.php?op=listar",
        type: "get",
        dataType: "json",
        error: function(e) {
          console.log(e.responseText);
        }
      },
      bDestroy: true,
      iDisplayLength: 3,
      order: [[0, "asc"]]
    })
    .dataTable();
}

init();
