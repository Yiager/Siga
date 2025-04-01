<?php

include '../head.php';

	//identificador del proveedor
	$id = $_GET['id'];

	//Seleccionar proveedor con el identificador correspondiente
	$sql = $conexion->prepare("SELECT * FROM prove WHERE id = ? ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$datos $sql->get_result();
	$sql->close();

	//mostrar todos los datos del proveedor encontrado
	while ($mostrar = $datos->fetch_assoc()) {

?>

<!DOCTYPE html>
<html>
<title> Proveedores: Editar </title>
	<body class="fondo">
		<div id="formEditar">
			<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" >

				<h3 class="bloque">Editar proveedor </h3>
				<input type="hidden" name="id" value="<?= $mostrar['id'] ?>">
				<div>
					<label for="empresa"> Empresa: </label> 
					<input type="text" name="Empresa" id="empresa" value="<?= $mostrar['Empresa'] ?>" required >
				</div> 
				<div>
					<label for="Rif"> RIF: </label> 
					<input type="text" name="rif" id="Rif" value="<?= $mostrar['Rif'] ?>" required >
				</div>	
				<div>
					<label for="Email"> Correo Electronico: </label> 
					<input type="email" name="mail" id="Email"  required value="<?= $mostrar['Correo'] ?>" >
				</div>
				<div>
					<label for="Telefono"> Tlf Empresa: </label> 
					<input type="text" name="telefono" id="telefono" required  value="<?= $mostrar['TlfLocal'] ?>">
				</div>
				<div>
					<label for="contacto"> Persona de contacto: </label> 
					<input type="text" name="PContacto" id="contacto" required value="<?= $mostrar['Contacto'] ?>">
				</div>
				<div>
					<label for="tlfContacto"> Numero de contacto: </label> 
					<input type="text" name="TlfContacto" id="tlfContacto" required value="<?= $mostrar['Tlf'] ?>">
				</div>
				<div>
					<p> Servicio que ofrece:  </p> 

					<label for="casillas"> Articulos de oficina </label>
					<input type="checkbox" name="opcion[]"  value="Articulos de oficina" > <br>

					<label for="casillas"> Mantenimiento </label>
					<input type="checkbox" name="opcion[]"  value="Mantenimiento" > <br>

					<label for="casillas"> Computacion </label>
					<input type="checkbox" name="opcion[]"  value="Computacion" > <br>

					<label for="casillas"> Reparacion </label>
					<input type="checkbox" name="opcion[]"  value="Reparacion" > <br>

				</div>
				<div class="bloque">
					<button name="actualizar"class="agregar" > Guardar </button>
					<button class="Volver" name="volver"> Volver </button>
				</div>
			<?php } ?>

			</form>
		</div>
	</body>
</html>

<?php 

	if(isset($_POST['actualizar'])){

		$idProve = $_POST['id'];
		$Empresa = $_POST['Empresa'];
		$RIF = $_POST['rif'];
		$correo = $_POST['mail'];
		$tlfEmpresa = $_POST['telefono'];
		$Contacto = $_POST['PContacto'];
		$celularC = $_POST['TlfContacto'];
		$servicio = '';

		if(isset($_POST['opcion'])){

			$servicio = implode('\n', $_POST['opcion']);
		}

		//Verificar que no se repita el mismo rif y nombre del proveedor
		$sqlVerificar = $conexion->prepare("SELECT * FROM prove WHERE RIF = ? AND idProveedores != ? ");
		$sqlVerificar->bind_param("si", $RIF, $idProve);
		$sqlVerificar->execute();
		$sqlVerificar->store_result();
		$buscarRIF = $sqlVerificar->num_rows;
		$sqlVerificar->close();

		if ($buscarRIF > 0) {
			echo "<script>
					alert('Actualizacion fallida');
				</script>";
		}else{

			//actualizacion de datos a la base de datos
			$sql2 = $conexion->prepare("UPDATE 
						prove 
					 SET 
					 	empresa = ?, 
					 	RIF = ?, 
					 	CorreoElectronico = ?, 
					 	NumeroEmpresa =  ?, 
					 	PersonaContacto = ?, 
					 	NumeroContacto = ?, 
					 	Servicio = ?
					 WHERE 
					 	idProveedores = ? ");

			$sql2->bind_param("sssssssi", $Empresa, $RIF, $correo, $tlfEmpresa, $Contacto, $celularC, $servicio, $idProve);
			$sql2->execute();
			$sql2->close();
			
			echo "<script>
					alert('Actualizacion exitosa!');
					window.location ='../Proveedores.php';
				</script>";
		}
	}

	if(isset($_POST['volver'])){
		echo "<script>
				window.location ='../Proveedores.php';
			</script>";
	}

?>