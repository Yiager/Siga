<?php

include "../conect.php";
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

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/estiloPer.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
	<title> Editar permiso </title>
</head>
<body>

<div class="formularioPerEdit" id="formulario">
		<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
			<h2 class="bloque"> Editar permiso </h2>

			<input type="hidden" name="id" value="<?= $traerPermisos['id']; ?>">

			<p>
				<label for="Cedula"> Cedula: </label>
				<input type="text" name="ci" id="Cedula" required value="<?= $traerPermisos['Cedula']; ?>">
			</p>
			<p>
				<label for="Nombre"> Nombre: </label>
				<input type="text" name="nombre" id="Nombre" required value="<?= $traerPermisos['Nombre']; ?>">
			</p>
			<p>
				<label for="Correo"> Correo: </label>
				<input type="text" name="correo" id="Correo" required value="<?= $traerPermisos['Correo'] ?>">
			</p>
			<p>
				<label for="Empresa"> Empresa: </label>
				<input type="text" name="empresa" id="Empresa" required value="<?= $traerPermisos['Empresa']; ?>">
			</p>
			<p>
				<label for="Direccion"> Direccion: </label>
				<input type="text" name="direccion" id="Direccion" required value="<?= $traerPermisos['Direccion']; ?>">
			</p>
			<p>
				<label for="Actividad"> Actividad: </label>
				<input type="text" name="actividad" id="Actividad" required value="<?=$traerPermisos['Actividad']; ?>">
			</p>
<?php
}
?>
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

							hasta.value = dia+'/'+mes+'/'+año;
						}

					</script>

			<input type="hidden" name="emision" value="<?= $fechaActual; ?>" required>
			
			<input type="submit" name="Actualizar" value="Actualizar" class="bloque">
			<div class="btn-volver">
				<a href="../permisos.php" class="btnVolver"> Volver </a>
			</div>
			
		</form>
	</div>

</body>
</html>

<?php

	if (isset($_POST['Actualizar'])) {
		
		$id = $_POST['id'];
		$cedula = $_POST['ci'];
		$nombre = $_POST['nombre'];
		$correo = $_POST["correo"];
		$empresa = $_POST['empresa'];
		$direccion = $_POST['direccion'];
		$actividad = $_POST['actividad'];

		$verificarPermisos = $conexion->prepare("SELECT * FROM permisos WHERE Cedula = ? AND id != ? ");
		$verificarPermisos->bind_param("si", $cedula, $id);
		$verificarPermisos->execute();
		$contarPermisos = $verificarPermisos->num_rows;
		$verificarPermisos->close();

		if ($contarPermisos > 0) {
			echo "<script> alert('Ya existe un permisos con ese numero de cedula') </script>";
		}else{

			$actualizarPermisos = $conexion->prepare("UPDATE permisos SET Cedula = ?, Correo = ?, Nombre = ?, Empresa = ?, Direccion = ?, Actividad = ? WHERE id = ? ");
			$actualizarPermisos->bind_param("ssssssi", $cedula, $correo, $nombre, $empresa, $direccion, $actividad, $id);
			$actualizarPermisos->execute();
			$actualizarPermisos->close();

			echo "<script> 
						alert('Actualizacion exitosa!') 
						window.location = '../permisos.php'
			</script>";

		}
	}

?>