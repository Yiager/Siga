<?php 

include '../head.php';
	// ************** Codigo de la Salida *********************
	$Codigo1 = $_GET['id'];

	//************************* Seleccionar datos de la fila de la Salida *****************************
	$sqlActua = $conexion->prepare("SELECT 
					salidas.*, 
					departamentos.*, 
					almacen.* 
				 FROM 
				 	salidas 
				 		INNER JOIN departamentos ON salidas.Dep = departamentos.id 
				 		INNER JOIN almacen ON salidas.Deposito = almacen.id 
				 WHERE 
				 	SalidaID = ? ");
	$sql->bind_param("i", $Codigo1);
	$sql->execute();
	$datos = $sql->get_result();
	$sql->close();

	while ($mostrar1 = $datos->fetch_assoc()) {

?>
<!DOCTYPE html>
<html>
<title> Salidas: Editar </title>
<body class="fondo">

<div id="formEditar">

	<form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>" >

		<h2 class="bloque"> Editar salida </h2>
		<input type="hidden" name="id" value="<?= $mostrar1['SalidaID'] ?>">
		<div>
			<label for="Fecha"> Fecha: </label>
			<input type="date" name="fecha" id="Fecha" value="<?= $mostrar1['Fecha'] ?>" required>
		</div>
		<div>
			<label for="dep"> Departamento: </label>
			<select id="dep" name="Dep" required>
					<option value="<?= $mostrar1['id'] ?>"> <?= $mostrar1['departamento'] ?> </option>
		        <?php
		          $query = ("SELECT * FROM departamentos");
		          $query_respuesta = mysqli_query($conexion, $query);
		          while ($valores = mysqli_fetch_array($query_respuesta)) {

		         ?>
		            <option value="<?= $valores["id"] ?>" > <?= $valores["departamento"] ?> </option>

		         <?php }  ?>
			</select>
		</div>
		<div>
			<label for="Persona"> Persona:</label>
			<input type="text" name="persona" id="Persona" required value="<?= $mostrar1['Persona'] ?>">
		</div>
		<div>
			<label for="Almacen"> Almacen: </label>
			<select name="almacen" id="Almacen" required>
				<option value="<?= $mostrar1['id'] ?>"> <?= $mostrar1['Almacen'] ?> </option>

				<?php 

				$queryProve = ("SELECT * FROM almacen");
				$respuestaProve = mysqli_query($conexion, $queryProve);

				while ($option = mysqli_fetch_array($respuestaProve)) {
					
				?>

				<option value="<?= $option['id']?> "> <?= $option["Almacen"] ?> </option>

			<?php } ?>

			</select>
		</div>
		<div class="bloque">
			<button class="agregar" name="Actualizar"> Actualizar </button>
			<button class="Volver" name="Volver"> Volver </button>
		</div>
	</form>

<?php } ?>

</div> 

<?php
			
	//Validar que se presione el boton de actualizar y que los valores del formulario no esten vacias

	if(isset($_POST['Actualizar'])){

		$idCodigo = $_POST['id'];
		$fecha = $_POST['fecha'];
		$Dep = $_POST['Dep'];
		$Persona = $_POST['persona'];
		$almacen = $_POST['almacen'];

		if(isset($_POST["status"])){
			$Estatus = "EnProceso";
		}

		//Actualizar datos de la salida
		$sql2 = $conexion->prepare("UPDATE 
					salidas 
				 SET 
				 	Fecha = ?, 
				 	Dep = ?, 
				 	Persona = ?, 
				 	Deposito = ?  
				 WHERE  
				 	SalidaID = ? ");
		$sql2->bind_param("sisii", $fecha, $Dep, $Persona, $almacen, $idCodigo);
		$sql2->execute();
		$sql2->close();
			
			echo "<script>
					alert('Actualizacion Exitosa');
					window.location = '../Salida.php';
				</script>";
	}

	if(isset($_POST['Volver'])){
		header('location:../Salida.php');
	}

?>