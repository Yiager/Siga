<?php

include "conect.php";
$conexion = conectar();
$archivo = fopen("dbf/tblicen02.csv", "r");

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$idUsuario = $_SESSION['id_usuario'];

$fechaActual = date("d/m/Y");

$sqlUsuarios = $conexion->prepare("SELECT id, Tipo FROM usuarios WHERE id = ? ");
$sqlUsuarios->bind_param("i", $idUsuario);
$sqlUsuarios->execute();
$filas = $sqlUsuarios->get_result();
$sqlUsuarios->close();
$datos = $filas->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloMenu.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
	<title> Menu </title>
</head>
<body>

	<header>
		<div class="titulo"> <h3> Menu principal </h3> </div>
		<div class="fecha"> <p> <?= $fechaActual; ?> </p> </div>
	</header>
	
		<div class="nav">
			<a href="solvencias.php"> Solvencias </a>
			<a href="permisos.php"> Permisos </a>

			<?php

			if ($datos['Tipo'] == 1) {
				echo "<a href='usuarios.php'> Usuarios </a>";
			}else{
				echo "<a style='display:none;' href='usuarios.php'> Usuarios </a>";
			}

			?>
			<a href="salir.php"> Salir </a>
		</div>

	

		
</body>
</html>
