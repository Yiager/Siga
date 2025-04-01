<?php

include "conect.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$fechaActual = date("d/m/Y");

$id = $_GET['id'];

$solvencias = $conexion->prepare("SELECT * FROM solvencias WHERE id = ? ");
$solvencias->bind_param("i", $id);
$solvencias->execute();
$datos = $solvencias->get_result();
$solvencias->close();
while($traerSol = $datos->fetch_assoc()){

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloPer.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
	<title> Agregar correo </title>
</head>
<body>

<div class="formularioPerEdit" id="formulario">
		<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
			<h2 class="bloque"> Agregar correo </h2>

			<input type="hidden" name="id" value="<?= $traerSol['id']; ?>">

			<input type="hidden" name="nombres" value="<?= $traerSol['Nombres']; ?>">
			
<?php
}
?>
			<p>
				<label for="Correo"> Correo: </label>
				<input type="text" name="correo" id="Correo"  required>
			</p>
			
			<input type="submit" name="Agregar" value="Agregar" class="bloque">
			<div class="btn-volver">
				<a href="solvencias.php" class="btnVolver"> Volver </a>
			</div>
			
		</form>
	</div>

</body>
</html>

<?php

	
	if (isset($_POST['Agregar'])) {
		
		$id = $_POST['id'];
		$nombres = $_POST['nombres'];
		$correo = $_POST['correo'];

		$actualizarCorreo = $conexion->prepare("UPDATE correos SET correo = ? WHERE nombre = ? ");
		$actualizarCorreo->bind_param("ss", $correo, $nombres);
		$actualizarCorreo->execute();
		$actualizarCorreo->close();

		echo "<script> 
					alert('Agregado exitosamente!');
					window.location = 'solvencias.php'
			</script>";
	}

?>