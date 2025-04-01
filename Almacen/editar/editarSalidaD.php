
<?php 

include '../head.php';

	//Recibir codigo de la Salida, el estado de la Salida, Codigo del detalle de la salida y del alamcen en el que esta
	$id = $_GET['id'];
	$Codigo2 = $_GET['Codigo'];
	$status = $_GET['Estado'];
	$Almacen = $_GET['Almacen'];

	$sql = $conexion->prepare("SELECT 
					salidadetalle.*, 
					salidas.SalidaID, 
					productos.idProducto, 
					productos.Articulo, 
					almacen.id, 
					almacen.Almacen 
				 FROM 
				 	salidadetalle 
				 	INNER JOIN salidas ON salidadetalle.CodigoSalida =  salidas.SalidaID 
				 	INNER JOIN productos ON salidadetalle.Objeto = productos.idProducto 
				 	INNER JOIN almacen ON salidadetalle.Deposit = almacen.id 
				 WHERE 
				 	IdSalida = ? 
				 	AND salidadetalle.Deposit = ? ");
	$sql->bind_param("ii", $Codigo2, $Almacen);
	$sql->execute();
	$datos = $sql->get_result();
	$sql->close();

	while ($mostrar1 = $datos->fetch_assoc()) {

?>
<!DOCTYPE html>
<html>
<title> Salidas: Editar detalle </title>
<body class="fondo">

<div id="formEditar">

	<form  method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
			<h3 class="bloque"> Detalle Salida Nro: <?= $Codigo2 ?> </h3>
			<input type="hidden" name="idDetalle" value="<?= $mostrar1['IdSalida']?>">

			<?php 

				$id = $_GET['id'];

				$sqlActua = "SELECT * FROM salidas WHERE SalidaID = '$id' ";

					$resultado2 = mysqli_query($conexion, $sqlActua);

					while ($mostrar2 = mysqli_fetch_assoc($resultado2)) {
					
				?>

				<input type="hidden" name="salidas" value="<?= $mostrar2['SalidaID']; ?>">
		
			<?php } ?>

			<div>
				<label for="deposito"> Deposito: </label>
				<select id="deposito" name="Deposito">
					<option value="<?= $mostrar1['id']; ?>" > <?= $mostrar1['Almacen']; ?> </option>
					<?php 

				$queryProductos = ("SELECT 
										almacen.id, 
										almacen.Almacen, 
										inventario.Almacen AS Almacen2 
									FROM 
										almacen 
										INNER JOIN inventario ON almacen.id = inventario.Almacen 
									WHERE 
										inventario.Almacen = '$Almacen' 
									GROUP BY 
										almacen.id 
								");
				$respuestaProductos = mysqli_query($conexion, $queryProductos);
				while ($option2 = mysqli_fetch_array($respuestaProductos)) {

				?>

				<option value="<?= $option2['id']; ?> "> <?= $option2["Almacen"]; ?> </option>

			<?php } ?>

				</select>
			</div>
			<div>
				<label for="productos"> Producto: </label>
				<select id="productos" name="Productos">
					<option value="<?= $mostrar1['idProducto']; ?>" > <?= $mostrar1['Articulo']; ?> </option>
					<?php 

				$queryProductos = ("SELECT 
										productos.*, 
										inventario.*, 
										almacen.* 
									FROM 
										productos 
										INNER JOIN inventario ON productos.idProducto = inventario.Producto 
										INNER JOIN inventario.Almacen = almacen.id 
									WHERE 
										inventario.Almacen = '$Almacen'  
									");
				$respuestaProductos = mysqli_query($conexion, $queryProductos);

				while ($option2 = mysqli_fetch_array($respuestaProductos)) {

				?>

				<option value="<?= $option2['idProducto']; ?> "> <?= $option2["Articulo"]; ?> </option>

			<?php } ?>

				</select>
			</div>
			<div>
				<label for="Cantidad"> Cantidad: </label>
				<input type="text" name="cantidad" id="Cantidad" value="<?= $mostrar1['Cantidad']; ?>">
			</div>
			<div>
				<label for="Obs"> Observacion: </label>
				<input type="text" name="Observacion" id="Obs" maxlength="50" required value="<?= $mostrar1['Observacion'];?>">
			</div>
			<div class="bloque">
				<button class="agregar" name="Actualizar"> Enviar </button>
				<button class="Volver" name="Volver" > <a href="../salidaDetalle.php?pagina=1&id=<?= $id; ?>&Estado=<?= $status; ?>&Almacen=<?= $Almacen ?> " > Volver </a>  </button>
			</div>
		</form>

<?php } ?>

</div> 

<?php
if(isset($_POST['Actualizar'])){

	$ID = $_POST['idDetalle'];
	$Codigo = $_POST['salidas'];
	$Deposito = $_POST['Deposito'];
	$Producto = $_POST['Productos'];
	$Cantidad = $_POST['cantidad'];
	$Observacion = $_POST['Observacion'];

	//Evitar que se duplique la informacion del objeto en el mismo detalle de la salida

		$sqlVerificar = $conexion->prepare("SELECT 
							* 
						 FROM 
						 	salidadetalle 
						 WHERE 
						 	Objeto = ? 
						 	AND CodigoSalida = ?
						 	AND IdSalida != ? ");

		$sqlVerificar->bind_param("iii", $Producto, $Codigo, $ID);
		$sqlVerificar->execute();
		$datos = $sqlVerificar->store_result();
		$buscarProducto = $sqlVerificar->num_rows;
		$sqlVerificar->close();
	
		$sqlCantidad = $conexion->prepare("SELECT * FROM inventario WHERE inventario.Almacen = ? AND inventario.Producto = ?");
		$sqlCantidad->bind_param("ii", $Deposito, $Producto);
		$sqlCantidad->execute();
		$datos = $sqlCantidad->get_result();
		$sqlCantidad->close();

		$CantidadSalida = $datos->fetch_assoc();;

		$CantidadInv = $CantidadSalida['Total'];

	if ($buscarProducto > 0 || $Cantidad > $CantidadInv) {

		echo "<script>
				alert('Ya existe este producto / No existe cantidad suficiente');
			</script>";
		
	}
	elseif ($Cantidad <= $CantidadInv){

		$sql2 = $conexion->prepare("UPDATE 
					salidadetalle 
				 SET 
				 	Deposit = ?, 
				 	Objeto = ?, 
				 	Cantidad = ?, 
				 	Observacion = ?
				 WHERE  
				 	IdSalida = ? ");
		$sql2->bind_param("iissi", $Deposito, $Producto, $Cantidad, $Observacion, $Codigo2);
		$sql2->execute();
		$sql2->close();

		$url = "../salidaDetalle.php?pagina=1&id=$id&Estado=$status&Almacen=$Almacen";
		
		echo "<script>
				alert('Actualizacion Exitosa');
				window.location = '$url';
			</script>";
	}
}

?>