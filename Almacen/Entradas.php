<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" >
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" media="only screen and (max-width: 1080px)" href="css/adaptable.css">
	<link rel="stylesheet" media="only screen and (min-width: 1080px) and (max-width:1288px)" href="css/adaptableMed.css">
	<link rel="stylesheet" type="text/css" href="/Almacen/css/estiloMenu.css">
	<link rel="stylesheet" type="text/css" href="/Almacen/css/Estilo.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
</head>
<title> Entradas </title>
<body>

<header>
	<!-- ************************** Titulo principal ******************************* -->
	<h2> Entradas </h2>

</header>

<?php
	include("nav.php");

//Crear entrada nueva

if (isset($_POST["enviar"])) {

	$Fecha = $_POST["fecha"];
	$Almacen = $_POST["almacen"];
	$Proveedor = $_POST["Prove"];
	$OrdenCompra = $_POST["ordenCompra"];
	$fechaOrden = $_POST["fechaOrdenCompra"];
	$Factura = $_POST["nroFactura"];
	$fechaFactura = $_POST["fechaFactura"];
	$nombreArchivoFactura = $_FILES["archivoFactura"]['name'];
	$guardadoFactura = $_FILES['archivoFactura']['tmp_name'];
	
	if(file_exists('./Facturas/')){
		move_uploaded_file($guardadoFactura, './Facturas/'.$nombreArchivoFactura);

	}
	$nombreArchivoOrden = $_FILES["archivoOrden"]['name'];
	$guardadoOrden = $_FILES['archivoOrden']['tmp_name'];

	if(file_exists('./OrdenCompra/')){
		move_uploaded_file($guardadoOrden, './OrdenCompra/'.$nombreArchivoOrden);

	}
	$rutaFactura = '/Facturas/'.$nombreArchivoFactura;
	$rutaOrden = '/OrdenCompra/'.$nombreArchivoOrden;
	//$montoBase = $_POST["montoB"];
	//$montoIVA = $_POST["montoIVA"];
	//$montoTotal = $montoBase + $montoIVA;

	$EntradaTipo = $_POST['opciones'];
	$Estado = "";

	if(isset($_POST["estado"])){
			$Estado = "EnProceso";
		
		}

	$invInicial = $_POST["InvInicial"];	

	$sqlEntrada = $conexion->prepare("SELECT 
						Codigo 
					FROM 
						entradas 
					WHERE 
						NroCompra = ? 
						AND NroFactura = ? ");
	$sqlEntrada->bind_param("ss", $OrdenCompra, $Factura);
	$sqlEntrada->execute();
	$sqlEntrada->store_result();
	$filas = $sqlEntrada->num_rows;
	$sqlEntrada->close();

	//verificar que no este duplicado el nro de compra y el nro de factura

	if($filas > 0){

			echo "<script>
					alert('Ya existe una entrada con este Nro de orden de compra / factura');
					window.location('Entradas.php');
				 </script>";
	}else{

		//declaracion sql para insertar datos en la base de datos
		//De los proveedores

		if($fechaOrden < $fechaFactura){

			$sqlNuevaEntrada = $conexion->prepare("INSERT INTO 
										entradas (Fecha, Almacen, Proveedor, NroCompra, FechaCompra, NroFactura, FechaFactura, 
														TipoEntrada, Estado, InvInicial, rutaFactura, rutaOrden) 
								VALUES(
									?, 
									?, 
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
			$sqlNuevaEntrada->bind_param("ssssssssssss", $Fecha, $Almacen, $Proveedor, $OrdenCompra, $fechaOrden, $Factura, $fechaFactura, $EntradaTipo, $Estado, $invInicial, $rutaFactura, $rutaOrden);
			$sqlNuevaEntrada->execute();
			$sqlNuevaEntrada->close();

			echo "<script> 
				alert('Registro exitoso!');
				window.location('Entradas.php');
			</script>";

		}else{

			echo "<script> 
					alert('Fecha de orden es mayor a fecha de factura');
					window.location('Entradas.php');
				</script>";

		}
	}
}



//*********************Procesar entrada************************

if(isset($_POST["ProcesaEntrada"]) && isset($_POST['check'])){

			//*************************identificador de la entrada*****************************
			$ID = $_POST["check"];

			$Estado = "Procesada";

			$sqlProcesa = $conexion->prepare("UPDATE entradas SET Estado = ? WHERE Codigo = ? ");
			$sqlProcesa->bind_param("si", $Estado, $ID);
			$sqlProcesa->execute();
			$sqlProcesa->close();

				$sqlNuevoProducto = $conexion->prepare("SELECT 
										entradadetalle.CodigoProducto, 
										entradas.Almacen, 
										almacen.id, 
										productos.Articulo, 
										entradadetalle.Cantidad, 
										entradadetalle.Unidades, 
										entradadetalle.Precio, 
										entradas.InvInicial, 
										inventario.Producto, 
										inventario.Almacen AS invAlmacen 
									FROM 
										inventario 
										RIGHT JOIN entradadetalle ON inventario.Producto = entradadetalle.CodigoProducto 
										INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo 
										INNER JOIN almacen ON almacen.id = entradas.Almacen 
										INNER JOIN productos ON productos.idProducto = entradadetalle.CodigoProducto  
									WHERE 
										entradadetalle.CodigoEntrada = ?
										AND entradas.Almacen = almacen.id 
									GROUP BY 
										productos.idProducto 
									ORDER BY 
										entradas.Codigo");

				$sqlNuevoProducto->bind_param("i", $ID);
				$sqlNuevoProducto->execute();
				$productos = $sqlNuevoProducto->get_result();
				$sqlNuevoProducto->close();

				while($traer = $productos->fetch_assoc() ){

					$ProductoNuevo = $traer['Producto'];
					$Almacen = $traer['id'];
					$AlmacenNuevo = $traer['invAlmacen'];
					$Producto = $traer['CodigoProducto'];
					$InvInicial = $traer['InvInicial'];
					$Entrada = $traer['Cantidad'];
					$Unidades = $traer['Unidades'];
					$Salida = "0";
					$UnidadesTotal = $Unidades - $Salida;
					$PrecioUE = $traer['Precio'];
					$PrePon = $traer['Precio'];
					$PrePonUni = $PrePon / $Unidades;

					//if($AlmacenNuevo == null){

						$sqlInsertar = "INSERT INTO 
												inventario (Almacen, Producto, Cant,  InvIni,  Entrada,  
															Salida, Total, PrecioUE, PrePon, PrePonUni)

										VALUES 
											(
												'$Almacen', 
												'$Producto', 
												'$Entrada', 
												'$InvInicial', 
												'$Unidades', 
												'$Salida', 
												'$UnidadesTotal', 
												'$PrecioUE', 
												'$PrePon', 
												'$PrePonUni' 
											) ON DUPLICATE KEY 
										UPDATE 
											PrecioUE = $PrecioUE, 
											Cant = Cant + $Entrada, 
											Entrada = Entrada + $Unidades, 
											Total = Entrada - Salida, 
											PrePon = (
														SELECT 
															AVG(Precio) 
														FROM 
															entradadetalle  
															INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo 
														WHERE 
															entradas.Estado = 'Procesada'  
															AND entradadetalle.CodigoProducto = $Producto 
															AND entradadetalle.Existencia > 0 
															AND entradas.Almacen = '$Almacen' 
													), 
											PrePonUni = (
														SELECT 
															CAST(
																(AVG(Precio) * CAST(
																				(
																					SELECT 
																						SUM(Cantidad) 
																					FROM 
																						entradadetalle 
																						INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo 
																					WHERE  
																						entradas.Estado = 'Procesada' 
																						AND entradadetalle.CodigoProducto = '$Producto' 
																						AND entradas.Almacen = '$Almacen' 
																						AND entradadetalle.Existencia > 0
																				) AS DECIMAL(10,2) 
																				   ) 
																) AS DECIMAL(10,2)
																) / CAST(
																			(
																				SELECT 
																					SUM(Unidades) 
																				FROM 
																					entradadetalle 
																					INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo 
																				WHERE  
																					entradas.Estado = 'Procesada' 
																					AND entradadetalle.CodigoProducto = '$Producto' 
																					AND entradas.Almacen = '$Almacen' 
																					AND entradadetalle.Existencia > 0
																			) AS DECIMAL(10,2) 
																		) 
																FROM 
																	entradadetalle 
																	INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo
																WHERE 
																	entradadetalle.CodigoProducto = '$Producto' 
																	AND entradas.Estado = 'Procesada' 
																	AND entradas.Almacen = '$Almacen' 
																	AND entradadetalle.Existencia > 0
														) ";

						$sqlResInv = mysqli_query($conexion, $sqlInsertar);

			}

			echo "<script>
					alert('Agregado exitosamente!');
				</script>";

}

if(isset($_POST['Inv-Ini']) && isset($_POST['check1'])){

	$ID = $_POST["check1"];

	$Inv = "Si";

	$sqlInicial = $conexion->prepare("UPDATE entradas SET InvInicial = ? WHERE Codigo = ? ");
	$sqlInicial->bind_param("si", $Inv, $ID);
	$sqlInicial->execute();
	$sqlInicial->close();

	echo "<script>
			alert('Agregado al inventario inicial');
		</script>";

}

?>

<div id="table" >

	<table >

		<div class="botones" >	
			<input type="text" name="buscar" id="buscar" placeholder="Buscar" > 			
			<button id="Agregar" > Incluir </button>
		</div>

		<thead >
			<th> Proc. </th>
			<th> Inv Ini. </th>
			<th> Ver entrada </th>
			<th> Codigo </th>
			<th> Fecha </th>
			<th> Proveedor </th>
			<th> Almacen </th>
			<th> Tipo de Entrada </th>
			<th> Estado </th>
			<th> Inv Inicial </th>
			<th> Archivos </th>
			<th> Acciones </th>
			<th> </th>
			<th> </th>
		</thead>

		<tbody id="tablaEntradas"></tbody>

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
				let contenido = document.getElementById("tablaEntradas");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/BuscarEntrada.php";
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

<div id="form">

<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" id="formulario"> 

		<h2 class="bloque">  Nueva entrada </h2>
		<div >
			<label for="Fecha"> Fecha: </label>
			<input type="date" id="Fecha" name="fecha" required>
		</div>
		<div >
			<label for="Proveedor"> Proveedor: </label>
			<select  name="Prove" id="Proveedor" required>

				<option value="#" selected hidden disabled> Seleccione: </option>

				<?php 

				$queryProve = $conexion->prepare("SELECT * FROM prove");
				$queryProve->execute();
				$opcion = $queryProve->get_result();
				$queryProve->close();

				while ($filaProv = $opcion->fetch_assoc()) {
					
				?>

				<option value="<?= $filaProv['id']?> "> <?= $filaProv["Empresa"] ?> </option>

			<?php } ?>

			</select>

		</div>
		<div >
			<label for="Almacen"> Almacen: </label>
			<select  name="almacen" id="Almacen" required>
					<option value="#" disabled selected hidden> Seleccione: </option>
		        <?php
		          $query = $conexion->prepare("SELECT * FROM almacen");
		          $query->execute();
		          $almacen = $query->get_result();
		          $query->close();
		          while ($fila = $almacen->fetch_assoc()) {

		         ?>
		            <option value="<?= $fila["id"] ?>" > <?= $fila["Almacen"] ?> </option>

		         <?php }  ?>
			</select>
		</div>
		<div >
			<label for="OrdenCompra"> Nº orden de compra:</label>
			<input type="text" id="OrdenCompra" name="ordenCompra" required  >
		</div>
		<div >
			<label for="FechaOrdenCompra"> Fecha orden de compra: </label>
			<input type="date" id="FechaOrdenCompra" name="fechaOrdenCompra"  required>
		</div>
		<div >
			<label for="fechaF"> Fecha factura: </label>
			<input type="date" id="fechaF" name="fechaFactura" required>
		</div>
		<div >
			<label for="NroFactura"> Nº Factura: </label>
			<input type="text" id="NroFactura" name="nroFactura" required>
		</div>
		<div >
			<label for="Tipo"> Tipo de entrada: </label>
			<select  name="opciones" id="Tipo" required>
				<option value="N/A">Seleccione:</option>
				<option  value="Parcial">Parcial</option>
				<option  value="Total">Total</option>
			</select>
		</div>
		<input type="hidden" name="estado" value="0">
		<input type="hidden" name="InvInicial" value="No">
		<div >
      		<span > Subir orden de compra:</span>
     		<input type="file" name="archivoOrden"  required>
    	</div>
		<div >
      		<span > Subir factura:</span>
     		<input type="file" name="archivoFactura"  required>
    	</div>
		<div class="bloque">
			<button class="agregar" name="enviar"> Agregar </button>
			<button class="Volver" id="Volver" > Volver </button>
		</div>
</form>

</div>

<script src="js/events.js"></script>

</body>

</html>