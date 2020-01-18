function enviar(e) {
  $("#form").bootstrapValidator({
    message: "El archivo es de valor invalido",
    fields: {
      file: {
        message: "Archivo invalido",
        validators: {
          file: {
            extension: "zip",
            type: "application/zip",
            maxSize: 2097152, // 2048 * 1024
            message: "Archivo denegado,Inserte una imagen valida"
          }
        }
      }
    }
  });
}
function init() {
  enviar();
}
init();
