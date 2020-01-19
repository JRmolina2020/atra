function enviar(e) {
  $("#form").bootstrapValidator({
    message: "El archivo es de valor invalido",
    fields: {
      file: {
        message: "Archivo invalido",
        validators: {
          file: {}
        }
      }
    }
  });
}
function init() {
  enviar();
}
init();
