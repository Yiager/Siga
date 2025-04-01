<!DOCTYPE html>
<html lang="es">
<title> Detalles de salida </title>
<body>
<header>
<?php
$id = $_GET['id'];
$Almacen = $_GET['Almacen'];
?>
	<h2> Detalles de la salida nro: <?php echo $id ?> </h2>

</header>

<?php
	include("nav.php");

//Registro de detalle de salida nuevo

if (isset($_POST["Enviar"])) {

	$Salida = $_POST["salidas"];
	$Deposito = $_POST["Deposito"];
	$Producto = $_POST["Productos"];
	$Cantidad = $_POST["cantidad"];
	$Precio = 0;
	$Observacion = $_POST["Observacion"];

	$sqlDetalle = $conexion->prepare("SELECT 
						* 
					FROM 
						salidadetalle  
					WHERE 
						Objeto = ?
						AND CodigoSalida = ?  ");
	$sqlDetalle->bind_param("ii", $Producto, $Salida);
	$sqlDetalle->execute();
	$sqlDetalle->store_result();
	$filas = $sqlDetalle->num_rows;
	$sqlDetalle->close();

	//verificar que haya respuesta de conexion a la base de datos
	$sqlCantidad = $conexion->prepare("SELECT 
						* 
					FROM 
						inventario 
					WHERE 
						inventario.Almacen = ? 
						AND inventario.Producto = ? ");
	$sqlCantidad->bind_param("ii", $Deposito, $Producto);
	$sqlCantidad->execute();
	$cantidadBD = $sqlCantidad->get_result();
	$sqlCantidad->close();
	$CantidadSalida = $cantidadBD->fetch_assoc();

	$CantidadInv = $CantidadSalida['Total'];

	if($filas > 0 ){

		echo "<script>
				alert('Ya existe este producto / No existe cantidad suficiente');
				window.location('salidaDetalle.php');
			 </script>";

	}
	if($Cantidad > $CantidadInv){
			echo "<script>
					alert('No existe cantidad suficiente');
				 </script>";
	}
	if ($filas == 0 && $Cantidad <= $CantidadInv) {
	
		//**********Traer valores de la tabla inventyario del respectivo producto y almacen****************

		$sqlPrecio = $conexion->prepare("SELECT 
							inventario.*, 
							productos.* 
					  FROM 
					  		inventario 
					  		INNER JOIN productos ON inventario.Producto = productos.idProducto 
					  WHERE 
					  		Producto = ?
					  		AND Almacen = ? ");
		$sqlPrecio->bind_param("ii", $Producto, $Deposito);
		$sqlPrecio->execute();
		$PrecioBD = $sqlPrecio->get_result();
		$sqlPrecio->close();

		$traerPrecio = $PrecioBD->fetch_assoc();;

		//********* Traer tipo de unidad de medida, SI es Unidad, Unidad = (1) o NO es Unidad, Unidades = (muchos)*********************
		$Tipo = $traerPrecio['Tipo'];

		if($Tipo == 'Si'){
			$Precio = $traerPrecio['PrePon'];

			$sqlNuevoDetalle = $conexion->prepare("INSERT INTO 
										salidadetalle (CodigoSalida, Deposit, Objeto, Cantidad, Precio, Observacion) 
								VALUES
								(
									?, 
									?, 
									?, 
									?, 
									?,  
									?
								)");
			$sqlNuevoDetalle->bind_param("iiisss", $Salida, $Deposito, $Producto, $Cantidad, $Precio, $Observacion);
			$sqlNuevoDetalle->execute();
			$sqlNuevoDetalle->close();

			$id = $_GET['id'];

		}

		if($Tipo == 'No'){
			$Precio = $traerPrecio['PrePonUni'];

			$sqlNuevoDetalle = $conexion->prepare("INSERT INTO 
										salidadetalle (CodigoSalida, Deposit, Objeto, Cantidad, Precio, Observacion) 
								VALUES
								(
									?, 
									?, 
									?, 
									?, 
									?,  
									?
								)");
			$sqlNuevoDetalle->bind_param("iiisss", $Salida, $Deposito, $Producto, $Cantidad, $Precio, $Observacion);
			$sqlNuevoDetalle->execute();
			$sqlNuevoDetalle->close();

			$id = $_GET['id'];

		}

		//************** Actualizar el precio total de la tabla salida detalle (Cantidad * precio) *************************

		$TotalActualizar = "UPDATE salidadetalle SET Total = Precio * Cantidad";
		$RespuestaTotal = mysqli_query($conexion, $TotalActualizar);
		
			echo "<script> 
					alert('Registro Exitoso!');
					window.location('salidaDetalle.php');
				</script>";
		}
	}


?>

<div id="table">

	<table >

		<div class="botones">

			<input type="text" name="buscar"  id="buscar" placeholder="buscar" >

			<?php 
				if($_GET['Estado'] == "Procesada"){
					echo "<button id='Agregar' style='display:none; '> Incluir </button>";
				}else{
					echo "<button id='Agregar'> Incluir </button>";
				}
			?>

		</div> 

			<thead >

				<th> Detalle </th>
				<th> Salida </th>
				<th> Almacen </th>
				<th> Producto </th>
				<th> Cantidad </th>
				<th> Precio </th>
				<th> Total </th>
				<th> Observacion </th>
				<th> Acciones </th>
			
			</thead>

				<tbody id="tablaSalidaDetalle">
				
			</tbody>
		</table>		

		<div class="total">
			<label id="total"> </label>
		</div>

		<div id="paginacion"></div>

		<input type="hidden" id="pagina" value="1">
		<input type="hidden" id="Codigo" value="<?php echo $_GET['id'] ?> " >
		<input type="hidden" id="Estado" value="<?php echo $_GET['Estado'] ?> " >
		<input type="hidden" id="Almacen" value="<?php echo $_GET['Almacen'] ?> " >


		<script>
			
			getData();

			document.getElementById("buscar").addEventListener("keyup", function(){
				getData();
			});

			document.getElementById("Codigo").addEventListener("DOMContentLoaded", function(){
				getData();
			});

			document.getElementById("Estado").addEventListener("DOMContentLoaded", function(){
				
				getData();
			});

			document.getElementById("Almacen").addEventListener("DOMContentLoaded", function(){
				getData();
			});

			function getData(){

				let valorBusqueda = document.getElementById("buscar").value;
				let contenido = document.getElementById("tablaSalidaDetalle");
				let pagina = document.getElementById('pagina').value;
				let Codigo = document.getElementById('Codigo').value;
				let Estado = document.getElementById('Estado').value;
				let Almacen = document.getElementById('Almacen').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/BuscarDetalleS.php";
				let formaData = new FormData();
				formaData.append("buscar", valorBusqueda);
				formaData.append("pagina", pagina);
				formaData.append("Codigo", Codigo);
				formaData.append("Estado", Estado);
				formaData.append("Almacen", Almacen);

				fetch(url, {
						method: "POST",
						body: formaData
				}).then(response => response.json())
				.then(data => {
					contenido.innerHTML = data.data
					document.getElementById("total").innerHTML = "Mostrando "+data.totalFiltro +" de "+ data.total + " registros totales"
					document.getElementById("paginacion").innerHTML = data.paginacion
				}).catch(err => console.log(err))
			}

			function siguientePagina(pagina){
				document.getElementById('pagina').value = pagina;
				getData();
			}

		</script>

	</div>

	<!-- ///////////////////////////////////// FORMULARIO DETALLE DE SALIDA /////////////////////////////////////////////// -->

<div id="form">
	<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" id="formulario" >

			<h2 class="bloque"> Agregar detalle </h2>

			<!--************* Seleccionar el id de la tabla salidas para relacionarlo con el registro en salida detalle **************** -->

			<?php 

				$id = $_GET['id'];

				$sqlActual = "SELECT * FROM salidas WHERE SalidaID = '$id' ";

					$resultado2 = mysqli_query($conexion, $sqlActual);
					while ($mostrar1 = mysqli_fetch_assoc($resultado2)) {
					
				?>

				<input type="hidden" name="salidas" value="<?php echo $mostrar1['SalidaID']?>">
		
			<?php } ?>

			<!-- ********* Seleccionar el Deposito que este disponible en el inventario ***************** -->

			<div>
				<label for="deposito"> Deposito: </label>
				<select id="deposito" name="Deposito" required>
					<option value="" disabled selected hidden> Seleccione: </option>
					<?php 

				$queryProductos = ("SELECT 
										inventario.Almacen, 
										almacen.*, 
										salidas.Deposito 
									FROM 
										inventario 
										INNER JOIN almacen ON inventario.Almacen = almacen.id 
										INNER JOIN salidas ON inventario.Almacen = salidas.Deposito 
									WHERE 
										Salidas.Deposito = '$Almacen' 
									GROUP BY 
										almacen.id");

				$respuestaProductos = mysqli_query($conexion, $queryProductos);

				while ($option2 = mysqli_fetch_assoc($respuestaProductos)) {
					
				?>

				<option value="<?php echo $option2['id']?> "> <?php echo $option2["Almacen"] ?> </option>


			<?php } ?>

				</select>

			</div>

			<!-- ************* Seleccionar el producto que este disponible en el inventario ******************** -->

			<div>
				<label for="productos"> Producto: </label>
				<select id="productos" name="Productos" required>
					<option value="" disabled selected hidden> Seleccione: </option>
					<?php 

				$queryProductos = ("SELECT 
										inventario.Producto, 
										inventario.Almacen, 
										productos.*, 
										almacen.*, 
										salidas.Deposito 
									FROM 
										inventario 
										INNER JOIN productos ON inventario.Producto = productos.idProducto 
										INNER JOIN almacen ON inventario.Almacen = almacen.id 
										INNER JOIN salidas ON salidas.Deposito = almacen.id 
									WHERE 
										salidas.Deposito = '$Almacen' 
									GROUP BY 
									idProducto");

				$respuestaProductos = mysqli_query($conexion, $queryProductos);

				while ($option2 = mysqli_fetch_assoc($respuestaProductos)) {

				?>

				<option value="<?php echo $option2['idProducto']?> "> <?php echo $option2["Articulo"] ?> </option>


			<?php } ?>

				</select>

			</div>

			<div>
				<label for="Cantidad"> Cantidad: </label>
				<input type="text" id="Cantidad" name="cantidad" required>
			</div>
			<div>
				<label for="Obs"> Observacion: </label>
				<input type="text" id="Obs" name="Observacion" maxlength="50" required >
			</div>
			<div class="bloque">
				<button class="agregar" name="Enviar"> Agregar </button>
				<button class="Volver" id="Volver" > Volver </button>
			</div>
		</form>
</div>

<script src="js/events.js"></script>

</body>

</html>
