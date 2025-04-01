<?php 

include '../head.php';

	$id = $_GET['id'];
	$Codigo = $_GET['Codigo'];
	$estado = $_GET['Estado'];

	$sql = $conexion->prepare("SELECT 
					entradadetalle.*, 
					entradas.Codigo, 
					productos.idProducto, 
					productos.Articulo 
				 FROM 
				 	entradadetalle 
				 	INNER JOIN entradas ON entradadetalle.CodigoEntrada =  entradas.Codigo 
				 	INNER JOIN productos ON entradadetalle.CodigoProducto = productos.idProducto
	 			 WHERE 
	 			 	ID = ? LIMIT 1");
	$sql->bind_param("i", $id);
	$sql->execute();
	$datos = $sql->get_result();
	$sql->close();

	while ($mostrar2 = $datos->fetch_assoc()) {

?>
<!DOCTYPE html>
<html>
<title> Entradas: Editar detalle </title>
<body class="fondo">

<div id="formEditar">
	<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
			<h3 class="bloque"> Editar detalle Nro : <?= $id ?> </h3>
			<input type="hidden" name="idDetalle" value="<?= $mostrar2['ID']?>"> 

			<?php 

				$id = $_GET['Codigo'];
				$sqlActual = "SELECT * FROM entradas WHERE Codigo = '$id' ";
				$resultado3 = mysqli_query($conexion, $sqlActual);
				while ($mostrar1 = mysqli_fetch_assoc($resultado3)) {

				?>

				<input type="hidden" name="ID" value="<?= $mostrar1['Codigo']?>">
		
			<?php } ?>

			<div >
				<label for="productos"> Producto: </label>
				<select name="Productos" id="productos" required>
					<option value="<?= $mostrar2['idProducto'] ?>"> <?= $mostrar2['Articulo'] ?> </option>
					<?php 

				$queryProductos = ("SELECT * FROM productos");
				$respuestaProductos = mysqli_query($conexion, $queryProductos);

				while ($option2 = mysqli_fetch_array($respuestaProductos)) {
					
				?>

				<option value="<?= $option2['idProducto']?> "> <?= $option2["Articulo"] ?> </option>

			<?php } ?>

				</select>
			</div>
			<div>
				<label for="Cantidad"> Cantidad: </label>
				<input type="number" name="cantidad" id="Cantidad" required value="<?= $mostrar2['Cantidad'] ?>">
			</div>
			<div>
				<label for="Unidades"> Unidades: </label>
				<input type="number" name="unidades" id="Unidades" required value="<?= $mostrar2['Unidades'] ?>">
			</div>
			<div>
				<label for="precio"> Precio: </label>
				<input type="text" name="Precio" id="precio" required value="<?= $mostrar2['Precio'] ?>">
			</div>
			<div>
				<label for="MontoB"> Monto Base: </label>
				<input type="text" name="montoB" id="MontoB" required value="<?= $mostrar2['MontoB'] ?>">
			</div>
			<div>
				<label for="MontoIVA"> Monto IVA: </label>
				<input type="text" name="montoIVA" id="MontoIVA" required value="<?= $mostrar2['MontoI'] ?>">
			</div>
			<div class="bloque">
				<button class="agregar" name="Actualizar"> Enviar </button>
				<button class="Volver" name="Volver"> 
					<a href="../EntradaDetalle.php?pagina=1&id=<?= $Codigo ?>&Estado=<?= $estado ?>"> Volver </a> 
				</button>
			</div>
		</form>

<?php } ?>

</div> 

<?php

			if(isset($_POST['Actualizar'])){

				$idDetalle = $_POST['idDetalle'];
				$idCodigo = $_POST['ID'];
				$Articulo = $_POST['Productos'];
				$Cantidad = $_POST['cantidad'];
				$Unidades = $_POST['unidades']; 
				$precio = $_POST['Precio'];
				$montoB = $_POST['montoB'];
				$montoIVA = $_POST['montoIVA'];
				$PorcentajeB = (100 * $montoIVA) / 100;
				$MontoTotal = ($montoB + $montoIVA) * $Cantidad;

						$sqlVerificar = $conexion->prepare("SELECT 
											* 
										 FROM 
										 	entradadetalle 
										 WHERE 
										 	CodigoProducto = ? 
										 	AND CodigoEntrada = ?
										 	AND ID != ? ");
						$sqlVerificar->bind_param("sii", $Articulo, $idCodigo, $idDetalle);
						$sqlVerificar->execute();
						$sqlVerificar->store_result();
						$buscarProducto = $sqlVerificar->num_rows;
						$sqlVerificar->close();
				
				if ($buscarProducto > 0) {

					echo "<script>
							alert('Actualizacion fallida');
						</script>";
				}else{

					$sql2 = $conexion->prepare("UPDATE 
								entradadetalle 
							 SET 
							 	CodigoEntrada = ?, 
							 	CodigoProducto = ?, 
							 	Cantidad = ?, 
							 	Unidades = ?, 
							 	Precio = ?, 
							 	MontoB = ?, 
							 	PorcentajeBase = ?, 
							 	MontoI = ?, 
							 	MontoT = ? 
							 WHERE  
							 	ID = ?");
					$sql2->bind_param("iisssssssi", $idCodigo, $Articulo,$Cantidad, $Unidades, $precio, $montoB, $PorcentajeB, $montoIVA, $MontoTotalm, $idDetalle);
					$sql2->execute();
					$sql2->close();

					$sqlSumaMontos = $conexion->prepare("UPDATE 
										entradas 
									  SET 
									  	MontoBase = (
									  					SELECT 
									  						SUM(MontoB) 
									  					FROM 
									  						entradadetalle 
									  					WHERE 
									  						entradadetalle.CodigoEntrada = entradas.Codigo
									  				), 
									  	MontoIVA = (
									  					SELECT 
									  						SUM(MontoI) 
									  					FROM 
									  						entradadetalle 
									  					WHERE 
									  						entradadetalle.CodigoEntrada = entradas.Codigo
									  				), 
									  	MontoTotal = (
									  					SELECT 
									  						SUM(MontoT) 
									  					FROM 
									  						entradadetalle 
									  					WHERE 
									  						entradadetalle.CodigoEntrada = entradas.Codigo
									  				) 
									  WHERE 
									  	entradas.Codigo = ? ");

					$sqlSumaMontos->bind_param("i", $Codigo);
					$sqlSumaMontos->execute();
					$sqlSumaMontos->close();

					$url = "../EntradaDetalle.php?pagina=1&id=$Codigo&Estado=$status";

					echo "<script>
							alert('Actualizacion Exitosa');
							window.location.href='$url';
						</script>";
					
				}
			}

		?>
