
<?php 

include '../head.php';

	$Codigo1 = $_GET['id'];

	$sql = $conexion->prepare("SELECT 
					entradas.*, 
					prove.Empresa, 
					almacen.id, 
					almacen.Almacen AS Almacen2 
				 FROM 
				 	entradas 
				 	INNER JOIN prove ON entradas.Proveedor = prove.id
				 	INNER JOIN almacen ON entradas.Almacen = almacen.id 
				 WHERE 
				 	Codigo = ?  LIMIT 1");
	$sql->bind_param("i", $Codigo1);
	$sql->execute();
	$datos = $sql->get_result();
	$sql->close();

	while ($mostrar1 = $datos->fetch_assoc()) {

?>
<!DOCTYPE html>
<html>
<title> Entradas: Editar </title>
<body class="fondo">

<div id="formEditar">

	<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >

		<h2 class="bloque"> Editar entradas </h2>

		<input type="hidden" name="id" value="<?= $mostrar1['Codigo'] ?>">

		<div >
			<label for="Fecha"> Fecha: </label>
			<input type="date" name="fecha" id="Fecha" value="<?= $mostrar1['Fecha'] ?>" >
		</div>

		<div >
			<label for="Almacen"> Almacen: </label>
			<select id="Almacen" name="almacen">

				<option value="<?= $mostrar1['Almacen'] ?>"> <?= $mostrar1['Almacen2'] ?> 
		</option>
					
		        <?php
		          $query = ("SELECT * FROM almacen");
		          $query_respuesta = mysqli_query($conexion, $query);
		          while ($valores = mysqli_fetch_array($query_respuesta)) {

		         ?>

		         
		            <option value="<?= $valores["id"] ?>" > <?= $valores["Almacen"] ?> 
		        </option>

		         <?php }  ?>
			</select>
		</div>

		<div>
			<label for="proveedor"> Proveedor: </label>
			<select id="proveedor" name="Prove" >

			<option value="<?= $mostrar1['Proveedor'] ?>"> <?= $mostrar1['Empresa'] ?> 
		</option>

				<?php 

				$queryProve = ("SELECT * FROM prove");
				$respuestaProve = mysqli_query($conexion, $queryProve);

				while ($option = mysqli_fetch_array($respuestaProve)) {
					
				?>

				<option value="<?= $option['id']?> "> <?= $option["Empresa"] ?> 
			</option>


			<?php	} ?>

			</select>

		</div>

		<div>
			<label for="OCompra"> Nº orden de compra:</label>
			<input type="text" name="ordenCompra" id="OCompra" value="<?= $mostrar1['NroCompra'] ?>">
		</div>

		<div>
			<label for="fechaOrden"> Fecha orden de compra: </label>
			<input type="date" name="fechaOrdenCompra" id="fechaOrden" value="<?= $mostrar1['FechaCompra'] ?>">
		</div>

		<div >
			<label for="FacturaNro"> Nº Factura: </label>
			<input type="text" name="nroFactura" id="FacturaNro" value="<?= $mostrar1['NroFactura'] ?>">
		</div>

		<div>
			<label for="fechaF"> Fecha factura: </label>
			<input type="date" name="fechaFactura" id="fechaF" value="<?= $mostrar1['FechaFactura'] ?>">
		</div>
		<div>
			<label> Tipo de entrada:</label>
			<select  name="opciones" >
				
				<option value="<?= $mostrar1['TipoEntrada'] ?>" ><?= $mostrar1['TipoEntrada'] ?></option>
				<option  value="Parcial">Parcial</option>
				<option  value="Total">Total</option>

			</select>
		</div>

		<input type="hidden" name="status" value="0">

		<input type="hidden" name="InvInicial" value="<?= $mostrar1['InvInicial'] ?>">

		<div class="bloque">
			<button class="agregar" name="Actualizar"> Actualizar </button>
			<button class="Volver" > <a href="../Entradas.php?pagina=1"> Volver </a> </button>
		</div>

</form>

<?php } ?>

</div> 

<?php

			if(isset($_POST['Actualizar'])){

				$idCodigo = $_POST['id'];
				$fecha = $_POST['fecha'];
				$almacen = $_POST['almacen'];
				$proveedor = $_POST['Prove'];
				$OCompra = $_POST['ordenCompra'];
				$FOCompra = $_POST['fechaOrdenCompra'];
				$NroFactura = $_POST['nroFactura'];
				$FFactura = $_POST['fechaFactura'];
				$opciones = $_POST['opciones'];
				$Estado = "";
				$InventarioIni = $_POST['InvInicial'];

				if(isset($_POST["status"])){
					$Estado = "EnProceso";
				}

				$sql2 = $conexion->prepare("UPDATE 
							entradas 
						 SET 
						 	Fecha = ?, 
						 	Almacen = ?, 
						 	Proveedor = ?, 
						 	NroCompra = ?, 
						 	FechaCompra = ?, 
						 	NroFactura = ?, 
						 	FechaFactura = ?, 
						 	TipoEntrada = ?, 
						 	Estatus = ?, 
						 	InvInicial = ? 
						 WHERE  
						 	Codigo = ? ");
				$sql2->bind_param("siisssssssi", $fecha, $almacen, $proveedor, $OCompra, $FOCompra, $NroFactura, $FFactura, $opciones, $Estado, $InventarioIni, $idCodigo);
				$sql2->execute();
				$sql2->close();
					
				echo "<script>
						alert('Actualizacion Exitosa');
						window.location = '../Entradas.php';
					</script>";
					
			
			}

		?>