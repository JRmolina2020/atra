<?php include '../config/conexion.php';
if (!isset($_SESSION['nombre'])) {
  header('location:../');
}
?>
<!doctype html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="../public/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="https://rawgit.com/nghuuphuoc/bootstrapvalidator/v0.5.0/vendor/bootstrap/css/bootstrap.css" />
  <link rel="stylesheet" href="https://rawgit.com/nghuuphuoc/bootstrapvalidator/v0.5.0/dist/css/bootstrapValidator.css" />
  <title>Atrato</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-item nav-link active" href="home.php">Inicio<span class="sr-only">(current)</span></a>
        <a class="nav-item nav-link" href="../controller/auth.php?op=exit">Salir</a>
      </div>
    </div>
  </nav>
  <div class="container mt-5">