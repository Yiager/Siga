<?php

include("../conect.php");

$conexion = conectar();

session_start();

if(!isset($_SESSION['idUser'])){
	header("Location: index.php");
}

$idUsuario = $_GET['id'];

$sqlUsuario = "SELECT * FROM usuarios WHERE id = '$idUsuario' ";
$respuestaUser = mysqli_query($conexion, $sqlUsuario);

//Inicio de ciclo para iterar los valores existentes en la base de datos y mostrarlos en los respectivos campos del formulario
while($traerUser = mysqli_fetch_assoc($respuestaUser)){

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/estiloMenu.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
	<title> Editar usuarios </title>
</head>
<body style="background-color: black;">

		<div class="contenedor">

		<div class="Form FormEdit">

			<h2 class="tituloForm"> Editar usuario: <?php echo $traerUser['Usuario'] ?> </h2>
				
			<form method="POST" action="<?php $_SERVER["PHP_SELF"] ?>">
				
			
				<input type="hidden" name="id" class="input-camp" id="Cuaderno" value="<?php echo $traerUser['id'] ?>">

			<p>	
				<label for="NombreC"> Nombre y apellido: </label>
				<input type="text" name="nombre" class="input-camp" id="NombreC" value="<?php echo $traerUser['Nombre'] ?>">
			</p>
			<p>
				<label for="User"> Usuario: </label>
				<input type="text" name="usuario" class="input-camp" id="User" value="<?php echo $traerUser['Usuario'] ?>">
			</p>
			<p>
				<label for="Tlf"> Telefono: </label>
				<input type="text" name="telefono" class="input-camp" id="Tlf" value="<?php echo $traerUser['telefono'] ?>">
			</p>
			<p>
				<label for="Correo"> Correo electronico: </label>
				<input type="text" name="correo" class="input-camp" id="Correo" value="<?php echo $traerUser['correo'] ?>">
			</p>
			<p>
				<label for="Tipo"> Tipo de usuario: </label>
				<input type="text" name="tipo" class="input-camp" id="Tipo" value="<?php echo $traerUser['tipo'] ?>">
			</p>
			
			<button type="submit" class="btn-Enviar bloque" name="Actualizar">
				Actualizar
			</button>

			<a href="../User.php" class="btn-Volver bloque">
				Volver
			</a>

			</form>

			<?php

				}

			?>



		</div>

	</div>


	<?php

		if(isset($_POST['Actualizar'])){

				$id = $_POST['id'];
				$nombre = $_POST['nombre'];
				$usuario = $_POST['usuario'];
				$correo = $_POST['correo'];
				$tlf = $_POST['telefono'];
				$tipo = $_POST['tipo'];

				$VerificarUsuario = 'SELECT id, Nombre, Usuario FROM Usuarios WHERE Nombre = "$nombre" OR Usuario = "$usuario" AND  id != "$id" ';
				$verificar = mysqli_query($conexion, $VerificarUsuario);
				$buscarNombre = $verificar->num_rows;

				if($buscarNombre > 0){

					echo "<script>

							alert('Error, no se pudo actualizar el usuario!');

						</script>
					";

				}else{

					$sqlActualizarUser = "UPDATE 
												usuarios 
										  SET 
										  	Nombre = '$nombre', 
										  	Usuario = '$usuario', 
										  	telefono = '$tlf', 
										  	correo = '$correo', 
										  	tipo = '$tipo' 
										  WHERE 
										  	id = '$id' ";

					$respuestaActualizar = mysqli_query($conexion, $sqlActualizarUser);


					echo "<script>

							alert('Usuario actualizado exitosamente!');
							window.location='../User.php';

						</script>
					";
				}
		}
	?>

</body>
</html>