<!DOCTYPE html>
<html lang="es">
<title> Detalles de entrada </title>
<body >
<?php
	$id = $_GET['id'];
?>
<header>
	<h2> Detalle de entrada nro: <?php echo $id ?></h2>
</header>

<?php
	include("nav.php");

//identificador del usuario


//Registro de proveedor nuevo

if (isset($_POST["Enviar"])) {


	$Entrada = $_POST["entradas"];
	$Producto = $_POST["Producto"];
	$Producto = substr($Producto, 0, -2);
	$Cantidad = $_POST["cantidad"];
	
	if(empty($_POST["unidades"])){
		$Unidades = 0;
	}else{
		$Unidades = $_POST["unidades"];
	}

	if($Unidades == 0){
		$Unidades = $Cantidad;
	}

	$Precio = $_POST["precio"];
	$MontoPorcentaje = $_POST["montoIVA"] / 100;
	$MontoB = $Precio * $Cantidad;
	$MontoIVA = ($MontoPorcentaje * $MontoB); 
	$MontoTotal = $MontoB + $MontoIVA;


	$sqlDetalle = $conexion->prepare("SELECT * FROM entradadetalle WHERE CodigoProducto = ? AND CodigoEntrada = ? ");
	$sqlDetalle->bind_param("ii", $Producto, $Entrada);
	$sqlDetalle->execute();
	$sqlDetalle->store_result();
	$filas = $sqlDetalle->num_rows;
	$sqlDetalle->close();

	if($filas > 0){

		echo "<script>
				alert('Ya existe este producto');
				window.location('EntradaDetalle.php');
			 </script>";

	}else{

		//declaracion sql para insertar datos en la base de datos
		//De los proveedores

		$sqlNuevoDetalle = $conexion->prepare("INSERT INTO 
								entradadetalle (CodigoEntrada, CodigoProducto, Cantidad, Unidades, Existencia, Precio, MontoB, PorcentajeBase, MontoI, MontoT) 
							VALUES
							(
								?, 
								?, 
								?, 
								?, 
								?,
								?, 
								?, 
								?, 
								?, 
								?
							)");

		$sqlNuevoDetalle->bind_param("iissssssss", $Entrada, $Producto, $Cantidad, $Unidades, $Unidades, $Precio, $MontoB, $MontoPorcentaje, $MontoIVA, $MontoTotal);
		$sqlNuevoDetalle->execute();
		$sqlNuevoDetalle->close();

		$id = $_GET['id'];

		if($sqlNuevoDetalle == true){


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
										entradadetalle.CodigoEntrada = entradas.Codigo) 
							WHERE entradas.Codigo = ? ");

			$sqlSumaMontos->bind_param("i", $id);
			$sqlSumaMontos->execute();
			$sqlSumaMontos->close();

			echo "<script> 
					alert('Registro exitoso!');
					window.location('EntradaDetalle.php');
				</script>";

		}
	}
}

?>


<div id="table">

	<table>
		<div class="botones">
			 <input type="text" name="buscar" id="buscar" placeholder="Buscar" > 

			<?php 
				if($_GET['Estado'] == "Procesada"){
					echo "<button id='Agregar' style='display:none; '> Incluir </button>";
				}else{
					echo "<button id='Agregar'> Incluir </button>";
				}
			?>

		</div> 

			<thead>

				<th> Detalle </th>
				<th> Entrada </th>
				<th> Producto </th>
				<th> Cantidad </th>
				<th> Unidades </th>
				<th> Salidas </th>
				<th> Existencia </th>
				<th> Precio </th>
				<th> Monto Base </th>
				<th> % IVA </th>
				<th> Monto IVA </th>
				<th> Monto Total </th>
				<th> Acciones </th>
			
			</thead>

				<tbody id="tablaEntradaDetalle">
				
			</tbody>
		</table>		

		<div class="total">
			<label id="total"> </label>
		</div>

		<div id="paginacion"></div>

		<input type="hidden" id="pagina" value="1">
		<input type="hidden" id="Codigo" value="<?php echo $_GET['id'] ?>"> 
		<input type="hidden" id="Estado" value="<?php echo $_GET['Estado'] ?>"> 

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
			function getData(){

				let valorBusqueda = document.getElementById("buscar").value;
				let contenido = document.getElementById("tablaEntradaDetalle");
				let pagina = document.getElementById('pagina').value;
				let Codigo = document.getElementById('Codigo').value;
				let Estado = document.getElementById('Estado').value;


				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/BuscarDetalle.php";
				let formaData = new FormData();
				formaData.append("buscar", valorBusqueda);
				formaData.append("pagina", pagina);
				formaData.append("Codigo", Codigo);
				formaData.append("Estado", Estado);

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

	<div id="form">
		<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" id="formulario" >

			<h2 class="bloque"> Registrar detalle </h2>


			<?php 

				$id = $_GET['id'];

				$sqlActua = "SELECT * FROM entradas WHERE Codigo = '$id' ";

					$resultado2 = mysqli_query($conexion, $sqlActua);


					while ($mostrar1 = mysqli_fetch_assoc($resultado2)) {
	
				?>

				<input type="hidden" name="entradas" value="<?php echo $mostrar1['Codigo']?>">
		
			<?php } ?>

			<div >
				<label for="Productos"> Producto: </label>
				<select name="Producto" id="Productos" onchange="Unidad()"  required>
					<option value="" disabled selected hidden> Seleccione: </option>
					<?php 

				$queryProductos = ("SELECT * FROM productos");
				$respuestaProductos = mysqli_query($conexion, $queryProductos);

				while ($option2 = mysqli_fetch_array($respuestaProductos)) {

				?>
				
				<option value="<?php echo $option2['idProducto'], $option2['Tipo']?>"> <?php echo $option2["Articulo"] ?>  </option> 
			<?php } ?>

				</select>

			</div>

			<div>
				<label for="Cantidad"> Cantidad: </label>
				<input type="text" id="Cantidad" name="cantidad" required>
			</div>

			<div>
				<label for="Unidades"> Unidades: </label>
				<input type="text" name="unidades" id="Unidades" value="<?php echo 0 ?>" required>
			</div>

			<script>

				function Unidad(){
					let prod = document.getElementById("Productos");
					let produc = prod.value
					console.log(produc);
					let tipo = produc.includes('Si');
					let unidad = document.getElementById("Unidades");

					if(tipo == true){
						unidad.disabled = false;
					}
					if(tipo == false){
						unidad.disabled = true;
					}

				}

			</script>
			
			<div >
				<label for="Precio"> Precio: </label>
				<input type="text" id="Precio" name="precio" class="form-control" required>
			</div>

			<div >
				<label for="IVA"> % IVA: </label>
				<input type="text" id="IVA" name="montoIVA" value="16" required class="form-control" required>
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
