<!DOCTYPE html>
<html lang="es">
	<title> Salidas </title>
<body>

<header>
	<h2> Salidas </h2>
</header>

<?php
	include("nav.php");

if(isset($_POST['enviar'])){

	$Fecha = $_POST['fecha'];
	$Dep = $_POST['Dep'];
	$Persona = $_POST['persona'];
	$Almacen = $_POST['almacen'];
	$Estado = $_POST['estado'];

	$sqlSalida = $conexion->prepare("SELECT 
						* 
				  FROM 
				  		salidas 
				  WHERE 
				  		Persona = ?");
	$sqlSalida->bind_param("s", $Persona);
	$sqlSalida->execute();
	$sqlSalida->store_result();
	$rows = $sqlSalida->num_rows;
	$sqlSalida->close();

	if($rows > 0){

		echo "<script>
					alert('Ya existe una persona que retiro en este departamento');
					window.location='Salida.php';
			 </script>";
	}else{

		$sqlNuevaSalida = $conexion->prepare("INSERT INTO 
									salidas (Fecha, Dep, Persona, Deposito, Estado) 
						   VALUES
						   (
						   	?, 
						   	?, 
						   	?, 
						   	?,
						   	?
						   )");
		$sqlNuevaSalida->bind_param("sisis", $Fecha, $Dep, $Persona, $Almacen, $Estado);
		$sqlNuevaSalida->execute();
		$sqlNuevaSalida->close();

		echo "<script> 
					alert('Registro creado exitosamente!');
			 </script>";
		
	}
}

if(isset($_POST["ProcesaSalida"]) && isset($_POST['check'])){

			$ID = $_POST["check"];

				$sqlNuevoProducto = $conexion->prepare("SELECT 
										*
									FROM 
										salidadetalle
									WHERE
										CodigoSalida = ?
									GROUP BY 
										Objeto ");

				$sqlNuevoProducto->bind_param("i", $ID);
				$sqlNuevoProducto->execute();
				$filas = $sqlNuevoProducto->get_result();
				$sqlNuevoProducto->close();

				while($traer = $filas->fetch_assoc()){

					$Salida = $traer['Cantidad'];
					$Producto = $traer['Objeto'];
					$Almacen = $traer['Deposit'];
					$Cantidad = $traer['Cantidad'];

					//********** Seleccionamos la fila del inventario del producto y entrada que se altera las unidades ************

					$sqlCantidades = $conexion->prepare("SELECT * FROM inventario WHERE Producto = ? AND Entrada >= ? AND Almacen = ? ");
					$sqlCantidades->bind_param("iii", $Producto, $Cantidad, $Almacen);
					$sqlCantidades->execute();
					$datos = $sqlCantidades->get_result();
					$sqlCantidades->close();

					$respuestaCant = $datos->fetch_assoc();

						//*****************Actualiza el estado de la salida********************************

						$Estado = "Procesada";
						$sqlProcesa = $conexion->prepare("UPDATE salidas SET Estado = ? WHERE SalidaID = ? ");
						$sqlProcesa->bind_param("si", $Estado, $ID);
						$sqlProcesa->execute();
						$sqlProcesa->close();

						//***************************Actualiza las salidas y el total del inventario****************************

						$sqlInsertar = $conexion->prepare("UPDATE 
											inventario 
										SET 
											Salida = $Salida + Salida, 
											Total = Entrada - Salida 
										WHERE 
											inventario.Almacen = ? 
											AND inventario.Producto = ? ");
						$sqlInsertar->bind_param("ii", $Almacen, $Producto);
						$sqlInsertar->execute();
						$sqlInsertar->close();
					}

							//*************Selecciona los datos de salida detalle**********************

							$sqlExistencia = "SELECT 
													salidadetalle.Objeto, 
													productos.idProducto, 
													almacen.id, 
													salidas.SalidaID, 
													salidadetalle.CodigoSalida, 
													salidadetalle.Deposit, 
													salidas.Estado, 
													salidas.Deposito, 
													salidadetalle.Cantidad,
													entradadetalle.Existencia, 
													entradas.Estado, 
													entradas.Codigo, 
													entradadetalle.CodigoEntrada, 
													entradadetalle.ID

											  FROM 
											  		salidas 
													RIGHT JOIN salidadetalle ON salidas.SalidaID = salidadetalle.CodigoSalida 
													INNER JOIN almacen ON salidas.Deposito = almacen.id 
													INNER JOIN productos ON salidadetalle.Objeto = productos.idProducto 
													INNER JOIN entradadetalle ON salidadetalle.Objeto = entradadetalle.CodigoProducto 
													INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo 
											  WHERE 
											  		salidas.Deposito = entradas.Almacen
											  		AND salidas.Estado = 'Procesada' 
											  		AND entradas.Estado = 'Procesada' 
											  		AND salidas.SalidaID = '$ID'
											  GROUP BY 
											  		salidadetalle.Objeto ";

							$sqlTraerExistencia = mysqli_query($conexion, $sqlExistencia);

							while($Datos = mysqli_fetch_assoc($sqlTraerExistencia)){

							//************Se traen los datos de la tabla salida detalle y se identifican******************

								$Salidas = $Datos['Cantidad'];
								$UnidadesED = $Datos['Existencia'];
								$Objeto = $Datos['Objeto'];
								$Detalle = $Datos['ID'];
								$AlmacenSalida = $Datos['Deposit'];

   						//***********************Contador de salida (la cantidad de la salida)**************************
   								$contador = $Salidas;

   								while($contador > 0){
   						//**********Se resta uno de la tabla entradadetaale y se actualiza cada vez que el contador da una vuelta********
   									$sqlDescontar = "UPDATE 
   														entradadetalle 
   														INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo 
   													 SET 
   													 	entradadetalle.Existencia = entradadetalle.Existencia - 1 
   													 WHERE 
   													 	entradadetalle.Existencia > 0 
   													 	AND entradadetalle.CodigoProducto = '$Objeto' 
   													 	AND entradas.Estado = 'Procesada' 
   													 	AND entradas.almacen = '$AlmacenSalida' 
   													 ORDER BY 
   													 	CodigoEntrada ASC 
   													 LIMIT 
   													 	1 ";

   									$RespuestaDescontar = mysqli_query($conexion, $sqlDescontar);

   									//****************Restar al contador****************
   									$contador--;

   								}
   								//**********************Actualizar las unidades en la tabla entrada detalle **********************
   								$sqlActualizarSalidas = "UPDATE entradadetalle SET Salidas = Unidades - Existencia";
   								$ResSalidasActualizadas = mysqli_query($conexion, $sqlActualizarSalidas);
   							
						}
							
					
		echo "<script>
					alert('Procesada exitosamente!');
			  </script>";	
}


if (isset($_POST['Generar'])) {

	$Almacen = $_POST['AlmacenEn'];
	$Producto = $_POST['productoEn'];
	$Inicial = $_POST['inicial'];
	$Final = $_POST['final'];
	$Dep = $_POST['Dep'];

	header("Location:entregas.php?pagina=1&AlmacenEn=$Almacen&productoEn=$Producto&inicial=$Inicial&final=$Final&Dep=$Dep");

}

?>

<div id="form"> 

	<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" id="formulario" > 

			<h2 class="bloque">  Nueva salida </h2>

			<div >
				<label for="Fecha"> Fecha: </label>
				<input type="date" id="Fecha" name="fecha" required>
			</div>

			<div >
				<label for="dep"> Departamento: </label>
				<select id="dep" name="Dep" required>
						<option value="" disabled selected hidden> Seleccione: </option>
			        <?php
			          $query = ("SELECT * FROM departamentos");
			          $query_respuesta = mysqli_query($conexion, $query);
			          while ($valores = mysqli_fetch_array($query_respuesta)) {
			         ?>
			            <option value="<?= $valores["id"] ?>" > <?= $valores["departamento"] ?> </option>

			         <?php }  ?>
				</select>
			</div>
			
			<div >
				<label for="Persona"> Persona:</label>
				<input type="text" id="Persona" name="persona" required>
			</div>

			<div >
				<label for="Almacen"> Almacen: </label>
				<select id="Almacen" name="almacen" required >

					<option value="" disabled selected hidden> Seleccione: </option>

					<?php 

					$queryProve = ("SELECT 
										almacen.*, 
										inventario.Almacen AS invAlmacen 
									FROM 
										almacen 
										INNER JOIN inventario ON almacen.id = inventario.Almacen 
									WHERE 
										almacen.id = inventario.Almacen 
									GROUP BY 
										almacen.id");

					$respuestaProve = mysqli_query($conexion, $queryProve);

					while ($option = mysqli_fetch_array($respuestaProve)) {
						
					?>

					<option value="<?= $option['id']?> "> <?= $option["Almacen"] ?> 
				</option>

				<?php	} ?>

				</select>

			</div>

			<input type="hidden" name="estado" value="EnProceso">
			<div class="bloque">
				<button class="agregar" name="enviar"> Agregar </button>
				<button class="Volver" id="Volver" > Volver </button>
			</div>
	</form>

</div>

<div id="table">
	
	<table>

		<div class="botones" >
				<input type="text" name="buscar" id="buscar" placeholder="Buscar"> 
				<button id="Agregar" > Incluir </button>
				<button id="Entregas"> Entregas</button>
		</div>
	
			<thead >
				<th> Proc. </th>
				<th> Codigo </th>
				<th> Fecha </th>
				<th> Departamento </th>
				<th> Retiró </th>
				<th> Almacen </th>
				<th> Estado </th>
				<th> Accion </th>
				<th>  </th>
				<th>  </th>
			</thead>
			<tbody id="tablaSalidas"></tbody>
		</table>			

		<div class="total">
			<label id="total"> </label>
		</div>

		<div id="paginacion"></div>

		<input type="hidden" id="pagina" value="1">

		<script>
			
			getData();

			document.getElementById("buscar").addEventListener("keyup", function(){
				getData();
			});

			function getData(){

				let valorBusqueda = document.getElementById("buscar").value;
				let contenido = document.getElementById("tablaSalidas");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/BuscarSalida.php";
				let formaData = new FormData();
				formaData.append("buscar", valorBusqueda);
				formaData.append("pagina", pagina);

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

<div id="formEntrega">

	<form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" id="formularioE" >
				<h2 class="bloque"> Entregas: </h2>

						<div >
							<label for="dep"> Departamento: </label>
							<select name="Dep"  id="dep"  required>

								<option value="" disabled selected hidden> Seleccione: </option>

					<!-- **************** Traer de la tabla salida detalle departamentos donde este procesada la Salida ********** -->

								<?php 

								$sqlDepSalidas = "SELECT 
														salidadetalle.*, 
														salidas.*, 
														departamentos.* 
												  FROM 
												  		salidadetalle 
												  		INNER JOIN salidas ON salidadetalle.CodigoSalida = salidas.SalidaID 
												  		INNER JOIN departamentos ON salidas.Dep = departamentos.id 
												  WHERE 
												  		salidas.Estado = 'Procesada' 
												  GROUP BY 
												  		departamentos.id ";

								$RespuestaDepa = mysqli_query($conexion, $sqlDepSalidas);
								while($traerDep = mysqli_fetch_assoc($RespuestaDepa)){

								?>
						
								<option value="<?= $traerDep['id']; ?>"> <?= $traerDep['departamento']; ?> </option>

							<?php } ?>

							</select>
						</div>
						
						<div >
							<label for="almacenEn"> Almacen: </label>
							<select name="AlmacenEn" id="almacenEn" required>

								<option value="" disabled selected hidden> Seleccione: </option>

								<!-- **************** Traer de la tabla salida detalle almacen donde este procesada la Salida ********** -->

								<?php 

								$sqlAlmacenSalidas = "SELECT 
															salidadetalle.*, 
															salidas.*, 
															almacen.* 
													  FROM 
													  		salidadetalle 
													  		INNER JOIN salidas ON salidadetalle.CodigoSalida = salidas.SalidaID 
													  		INNER JOIN almacen ON salidadetalle.Deposit = almacen.id 
													  WHERE 
													  		salidas.Estado = 'Procesada' 
													  GROUP BY 
													  		almacen.id ";

								$RespuestaAlmacen = mysqli_query($conexion, $sqlAlmacenSalidas);
								while($traerAlmacen = mysqli_fetch_assoc($RespuestaAlmacen)){

								?>
						
								<option value="<?= $traerAlmacen['id']; ?>"> <?= $traerAlmacen['Almacen']; ?> </option>

							<?php } ?>

							</select>
						</div>
						
					<div >
						<label for="ProductoEn"> Producto: </label>
						<select name="productoEn" id="ProductoEn" required>

							<option value="" disabled selected hidden> Seleccione: </option>

							<!-- **************** Traer de la tabla salida detalle productos donde este procesada la Salida ********** -->

							<?php 

							$sqlProductoSalidas = "SELECT 
														salidadetalle.*, 
														salidas.*, 
														productos.* 
													FROM 
														salidadetalle 
														INNER JOIN salidas ON salidadetalle.CodigoSalida = salidas.SalidaID 
														INNER JOIN productos ON salidadetalle.Objeto = productos.idProducto 
													WHERE 
														salidas.Estado = 'Procesada' 
													GROUP BY 
														productos.idProducto ";

							$RespuestaProductos = mysqli_query($conexion, $sqlProductoSalidas);
							while($traerProductos = mysqli_fetch_assoc($RespuestaProductos)){

							?>
						
							<option value="<?= $traerProductos['idProducto']; ?>"> <?= $traerProductos['Articulo']; ?> </option>

						<?php } ?>

						</select>
					</div>

					<div c> 
						<label for="Inicial"> Fecha inicial: </label>
						<input type="date" name="inicial" id="Inicial"  required>
					</div>
					<div >
						<label for="Final"> Fecha final: </label>
						<input type="date" name="final" id="Final" required>
					</div>
				<div class="bloque">
					<button class="agregar" name="Generar"> Generar </button>
					<button class="Volver" id="VolverEntrega" > Volver </button>
				</div>
	</form>
					
</div> 

<script src="js/eventsSalidas.js"></script>

</body>

</html>