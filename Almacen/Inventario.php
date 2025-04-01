<!DOCTYPE html>
<html lang="es">
<title> Inventario </title>
<body>

<header>
	<h2> Inventario </h2>
</header>

<?php
	include("nav.php");


if (isset($_POST['enviarRes'])) {
	
	$Fecha = $_POST['fechaRes'];
	$Almacen = $_POST['almacenRes'];

	header("location: Reportes/resumen.php?fechaRes=$Fecha&almacenRes=$Almacen");

}

if (isset($_POST['enviarDet'])) {
	
	$Fecha = $_POST['fecha'];
	$Almacen = $_POST['almacen'];

	header("location: Reportes/detallado.php?fecha=$Fecha&almacen=$Almacen");

}


?>

<div id="table" >

	<table>
		<div class="botones" >
			<input type="text" name="buscar"  id="buscar" placeholder="Buscar" > 
			<button id="Resumen"> Resumen </button>
			<button id="Detallado"> Detallado </button>
		</div> 
		<thead>
			<th> Almacen </th>
			<th> Producto </th>
			<th> Cantidad </th>
			<th> Unidad de Medida </th>
			<th> inv inicial </th>
			<th> Entrada </th>
			<th> Salida </th>
			<th> Total </th>
			<th> Precio Ult Ent </th>
			<th> Ponderado </th>
			<th> Pond Uni </th>
			<th> Total Pond Uni </th>
		</thead>

		<tbody id="tablaInventario"></tbody>

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
				let contenido = document.getElementById("tablaInventario");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/BuscarInv.php";
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

<div id="formDetallado">
	<form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" id="formularioD" >

		<h2 class="bloque"> Detallado de inventario </h2> 

		<div >
			<label for="Fecha"> Fecha de factura: </label>
			<input type="date" id="Fecha" name="fecha" required>
		</div> 
		<div >
			<label for="AlmacenDet"> Almacen: </label>
			<select id="AlmacenDet" name="almacen" required>
					<option value="" disabled selected hidden> Seleccione: </option>
		        <?php
		          $query = ("SELECT inventario.*, almacen.* FROM inventario INNER JOIN almacen ON inventario.Almacen = almacen.id GROUP BY inventario.Almacen");
		          $query_respuesta = mysqli_query($conexion, $query);
		          while ($valores = mysqli_fetch_array($query_respuesta)) {

		         ?>
		            <option value="<?= $valores["id"] ?>" > <?= $valores["Almacen"] ?> </option>

		         <?php }  ?>
			</select>
		</div> 
		<div class="bloque">
			<button class="agregar" name="enviarDet"> Generar </button>
			<button class="Volver" id="VolverDet" > Volver </button>
		</div>
	</form>

</div>

<div id="formResumen">
	<form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" id="formularioR" >

		<h2 class="bloque"> Resumen de inventario </h2>
		<div >
			<label for="FechaRes"> Fecha de factura: </label>
			<input type="date" name="fechaRes" id="FechaRes" required >
		</div> 
		<div >
			<label for="AlmacenRes"> Almacen: </label>
			<select id="AlmacenRes" name="almacenRes" required >
					<option value="" disabled selected hidden> Seleccione: </option>
		        <?php
		          $query = ("SELECT inventario.*, almacen.* FROM inventario INNER JOIN almacen ON inventario.Almacen = almacen.id GROUP BY inventario.Almacen");
		          $query_respuesta = mysqli_query($conexion, $query);
		          while ($valores = mysqli_fetch_array($query_respuesta)) {

		         ?>
		            <option value="<?= $valores["id"] ?>" > <?= $valores["Almacen"] ?> </option>

		         <?php }  ?>
			</select>
		</div> 
		<div class="bloque">
			<button class="agregar" name="enviarRes"> Generar </button>
			<button class="Volver" id="VolverRes" > Volver </button>
		</div>
	</form>
</div>

<script src="js/eventsInv.js"></script>

</body>

</html>