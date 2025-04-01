<?php 

include 'head.php';

	$Codigo1 = $_GET['id'];

	$sqlActua = "SELECT 
					entradas.*, 
					prove.Empresa, 
					almacen.id, 
					almacen.Almacen AS Almacen2 
				 FROM 
				 	entradas 
				 	INNER JOIN prove ON entradas.Proveedor = prove.id
				 	INNER JOIN almacen ON entradas.Almacen = almacen.id 
				 WHERE 
				 	Codigo = '$Codigo1' ";

	$resultado2 = mysqli_query($conexion, $sqlActua);

	while ($mostrar1 = mysqli_fetch_assoc($resultado2)) {


?>
<!DOCTYPE html>
<html>

<body class="fondo">

 <!-- ***************************** DATOS DE LA ENTRADA ************************************** -->

<div id="formVer" >

	<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
		<h2 class="bloque"> Entrada nro <?php echo $Codigo1 ?>  </h2>
		<div>
			<label > Proveedor: </label>
			<select >

			<option value="<?php echo $mostrar1['Proveedor'] ?>"> <?php echo $mostrar1['Empresa'] ?> 
		</option>

				<?php 

				$queryProve = ("SELECT * FROM prove");
				$respuestaProve = mysqli_query($conexion, $queryProve);

				while ($option = mysqli_fetch_array($respuestaProve)) {
					
				?>

				<option value="<?php echo $option['id']?> "> <?php echo $option["Empresa"] ?> 
			</option>

			<?php	} ?>

			</select>

		</div>
		<div>
			<label> Nº orden de compra:</label>
			<input type="text" value="<?php echo $mostrar1['NroCompra'] ?>">
		</div>
		<div>
			<label > Fecha orden de compra: </label>
			<input type="date" value="<?php echo $mostrar1['FechaCompra'] ?>">
		</div>

		<div>
			<label > Nº Factura: </label>
			<input type="text" value="<?php echo $mostrar1['NroFactura'] ?>">
		</div>
		<div>
			<label > Fecha factura: </label>
			<input type="date" value="<?php echo $mostrar1['FechaFactura'] ?>">
		</div>
		<div>
			<label > Monto base: </label>
			<input type="text" value="<?php echo $mostrar1['MontoBase'] ?>">
		</div>
		<div>
			<label > Monto IVA: </label>
			<input type="text" value="<?php echo $mostrar1['MontoIVA'] ?>">
		</div>
		<div>
			<label > Monto total: </label>
			<input type="text" value="<?php echo $mostrar1['MontoTotal'] ?>">
		</div>
		<div class="bloque">
			<a href="/Almacen/Entradas.php?pagina=1"> Volver </a> 
		</div>

</form>

<?php } ?>

</div> 

</body>


</html>