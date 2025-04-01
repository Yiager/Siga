<?php 

include "../conect.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$id = $_GET['id'];
$Registros = $conexion->prepare("SELECT * FROM usuarios WHERE id = ? ");
$Registros->bind_param("i", $id);
$Registros->execute();
$fila = $Registros->get_result();
$Registros->close();

while ($datos = $fila->fetch_assoc()) {
	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/estiloUser.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
	<title> Editar usuario </title>
</head>
<body>

	<div class="formularioUserEdit" id="formulario">
		<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
			<h2> Editar usuario </h2>

			<input type="hidden" name="id" value="<?= $datos['id']; ?>">

			<p>
				<label for="Nombre"> Nombre: </label>
				<input type="text" name="nombre" id="Nombre" required value="<?= $datos['Nombre']; ?>">
			</p>
			<p>
				<label for="Apellido"> Apellido: </label>
				<input type="text" name="apellido" id="Apellido" required value="<?= $datos['Apellido']; ?>">
			</p>
			<p>
				<label for="Usuario"> Usuario: </label>
				<input type="text" name="usuario" id="Usuario" required value="<?= $datos['Usuario']; ?>">
			</p>

		<?php
	}
		?>
			<p>
				<label for="tipo"> Tipo de usuario: </label>
				<select id="tipo" name="Tipo" required>
					<option value="" disabled selected hidden> Seleccione: </option>
					<option value="0"> Estandar </option>
					<option value="1"> Administrador </option>
				</select>
			</p>
			<p>
				<input type="submit" name="Actualizar" value="Actualizar">
			</p>
			
		</form>
	</div>

	<div class="btn-volver">
		<a href="../usuarios.php" class="btnVolver"> Volver </a>
	</div>

</body>
</html>

<?php

	if (isset($_POST['Actualizar'])) {
		
		$idUsuario = $_POST['id'];
		$nombre = $_POST['nombre'];
		$apellido = $_POST['apellido'];
		$usuario = $_POST['usuario'];
		$tipo = $_POST['Tipo'];

		$verificarUsuario = $conexion->prepare("SELECT id,Usuario FROM usuarios WHERE Usuario = ? AND id != ? ");
		$verificarUsuario->bind_param("si",$usuario, $id);
		$verificarUsuario->execute();
		$verificarUsuario->store_result();
		$contarRegistros = $verificarUsuario->num_rows;
		$verificarUsuario->close();

		if ($contarRegistros > 0) {
			
			echo "<script> alert('Error: Ya existe un usuario con ese nombre'); </script>";

		}else{

			$actualizarUsuario = $conexion->prepare("UPDATE usuarios SET Nombre = ?, Apellido = ?, Usuario = ?, Tipo = ? WHERE id = ? ");
			$actualizarUsuario->bind_param("ssssi", $nombre, $apellido, $usuario, $tipo, $id);
			$actualizarUsuario->execute();
			$actualizarUsuario->close();

			echo "<script> alert('Se ha actualuzado correctamente el usuario ". $usuario ." ');
						   window.location = '../usuarios.php';
				 </script>";
		}
	}

?>