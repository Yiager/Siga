<?php

include "conect.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$fechaActual = date("d/m/Y");

$id = $_GET['id'];

$permisos = $conexion->prepare("SELECT * FROM permisos WHERE id = ? ");
$permisos->bind_param("i", $id);
$permisos->execute();
$datos = $permisos->get_result();
$permisos->close();
while($traerPermisos = $datos->fetch_assoc()){

$Hasta = $traerPermisos['Hasta'];

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
	<title> Renovar permiso </title>
</head>
<body>

<div class="formularioPerEdit" id="formulario">
		<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
			<h2 class="bloque"> Editar permiso </h2>

			<input type="hidden" name="id" value="<?= $traerPermisos['id']; ?>">

<?php
}
?>
			<p>
				<label for="Desde"> Desde: </label>
				<input type="date" name="desdeR" id="Desde" required onchange="fechas()">
			</p>
			<p>
				<label for="Hasta"> Hasta: </label>
				<input type="text" name="hastaR" id="Hasta" required>
			</p>

			<script>
						function fechas(){

							let desde = new Date(document.getElementById('Desde').value);
							let hasta = document.getElementById('Hasta');

							let dia = desde.getDate() + 1;
							let mes = desde.getMonth() + 4;
							let año = desde.getFullYear();

							if(mes == 2 && dia == 31){
								dia -= 3;
							}

							if(mes % 2 == 0 && dia == 31 && mes != 2){
								dia -= 1
							}else{
								dia += 1
							}

							hasta.value = año+'/'+mes+'/'+dia;

						}

			</script>

			<input type="hidden" name="emisionR" value="<?= $fechaActual; ?>" required>
			
			<input type="submit" name="Actualizar" value="Actualizar" class="bloque">
			<div class="btn-volver">
				<a href="permisos.php" class="btnVolver"> Volver </a>
			</div>
			
		</form>
	</div>

</body>
</html>

<?php
	
	if (isset($_POST['Actualizar'])) {
		
		$id = $_POST['id'];
		$emisionR = $_POST['emisionR'];
		$desdeR = $_POST['desdeR'];
		$hastaR = $_POST['hastaR'];
		$desdeRformat = date("Y-m-d", strtotime($desdeR));

		$verificarPermisos = $conexion->prepare("SELECT * FROM permisos WHERE id = ? ");
		$verificarPermisos->bind_param("i", $id);
		$verificarPermisos->execute();
		$datos = $verificarPermisos->get_result();
		$verificarPermisos->close();
		$traerFecha = $datos->fetch_assoc();

		$hastaViejo = $traerFecha['Hasta'];
		$hastaViejoF = date("Y-m-d", strtotime($hastaViejo));

		if ($desdeRformat > $hastaViejoF) {

			$actualizarPermisos =$conexion->prepare("UPDATE permisos SET Renovacion = 1, DesdeR = ?, HastaR = ?, EmisionR = ? WHERE id = ? ");
			$actualizarPermisos->bind_param("sssi", $desdeR, $hastaR, $emisionR, $id);
			$actualizarPermisos->execute();
			$actualizarPermisos->close();

			echo "<script> 
						alert('Renovacion exitosa!');
						window.location = 'permisos.php'
				  </script>";
		}else{

			echo "<script> 
						alert('ERROR: La nueva fecha ´desde´ debe ser mayor al periodo anterior! ');
				  </script>";

		}
	}

?>