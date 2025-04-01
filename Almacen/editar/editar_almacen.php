<?php 

include '../head.php';

	$id = $_GET['id'];

	$sql = $conexion->prepare("SELECT * FROM almacen WHERE id = ? LIMIT 1");
	$sql->bind_param("i", $id);
	$sql->execute();
	$datos = $sql->get_result();
	$sql->close();

while ($mostrar1 = $datos->fetch_assoc()) {

?>

<!DOCTYPE html>
<html lang="es">
	<title> Almacen: Editar </title>
	<body class="fondo">
		<div id="formEditar">
			<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
			<h3 class="bloque"> Editar Almacen </h3> 

					<input type="hidden" name="id" value="<?php echo $mostrar1['id'] ?>">
					<div>
						<label for="Almacen"> Nombre Almacen: </label> 
						<input type="text" name="almacen" id="Almacen" value="<?= $mostrar1['Almacen'] ?>">
					</div>
					<div>
						<label for="Persona"> Responsable almacen:  </label> 
						<input type="text" name="persona" id="Persona" value="<?= $mostrar1['PersonaR'] ?>"> 
					</div>
					<div>
						<label for="Telefono"> Telefono: </label> 
						<input type="text" name="telefono" id="Telefono" value="<?= $mostrar1['Telefono'] ?>"> 
					</div>
					<div>
						<label for="Email"> Correo electronico: </label>
						<input type="email" name="email" id="Email" value="<?= $mostrar1['Correo'] ?>"> 
					</div>
					<div>
						<label for="Ubicacion"> Ubicacion: </label>
						<input type="text" name="ubicacion" id="Ubicacion" value="<?= $mostrar1['Ubicacion'] ?>" >
					</div>
					<div class="bloque" >
						<button class="agregar" name="Actualizar" > Actualizar </button>
						<button class="Volver" name="Volver"> Volver </button>
					</div>
		<?php } ?>

			</form>
		</div>
	</body>
</html>

<?php

			if(isset($_POST['Actualizar'])){

				$id = $_POST['id'];
				$Almacen = $_POST['almacen'];
				$Resp = $_POST['persona'];
				$Tlf = $_POST['telefono'];
				$email = $_POST['email'];
				$ubicacion = $_POST['ubicacion'];

			
				$sql2 = $conexion->prepare("UPDATE 
								almacen 
						 SET 
						 	Almacen = ?, 
						 	PersonaR = ?, 
						 	Telefono = ?, 
						 	Correo = ?, 
						 	Ubicacion = ?
						 WHERE  
						 	id = ? ");

				$sql2->bind_param("issssi", $Almacen, $Resp, $Tlf, $email, $ubicacion, $id);
				$sql2->execute();
				$sql2->close();

				echo "<script>
						alert('Actualizacion fallida');
					</script>";
				
			}

			if(isset($_POST['Volver'])){
				header('location:../Almacen.php');
			}

?>


