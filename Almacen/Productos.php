<!DOCTYPE html>
<html lang="es">
<title> Productos </title>
<body>

<header>
	<h2> Productos </h2>
</header>

<?php
	include("nav.php");

//Agregar Producto nuevo
if (isset($_POST["Enviar"])) {

	$Articulo = $_POST["articulo"];
	$id_medida = $_POST["medida"];
	$Tipo = $_POST['tipoUnidad'];
	$nombreImagen = $_FILES["Imagen"]['name'];
	$guardadoImagen = $_FILES['Imagen']['tmp_name'];
	
	if(file_exists('./Productos/')){
		move_uploaded_file($guardadoImagen, './Productos/'.$nombreImagen);

	}

	$rutaImagen = 'Productos/'.$nombreImagen;

	if($Tipo == 0){
		$Tipo = "No";
	}

	if($Tipo == 1){
		$Tipo = "Si";
	}

	$sqlproducto = $conexion->prepare("SELECT 
							* 
					FROM 
							productos 
					WHERE 
							Articulo = ? ");
	$sqlproducto->bind_param("s", $Articulo);
	$sqlproducto->execute();
	$sqlproducto->store_result();
	$filas = $sqlproducto->num_rows;
	$sqlproducto->close();

	//verificar que haya respuesta de conexion a la base de datos
	if($filas > 0){

		echo "<script>
				alert('Ya existe un articulo con este codigo');
				window.location('Productos.php');
			 </script>";

	}else{

		//declaracion sql para insertar datos en la base de datos
		$sqlNuevoProducto = $conexion->prepare("INSERT INTO 
										productos (Articulo, id_medida, Tipo, rutaImagen) 
												VALUES
												(
													?, 
													?,
													?,
													?
												)");
		
		$sqlNuevoProducto->bind_param("siss", $Articulo, $id_medida, $Tipo, $rutaImagen);
		$sqlNuevoProducto->execute();
		$sqlNuevoProducto->close();

		echo "<script> 
				alert('Producto agregado de manera exitosa');
				window.location('Productos.php');
			</script>";
		
	}
}

?>

<div id="table">

		<table>
			<div class="botones">
				<input type="text" name="buscar" id="buscar" placeholder="Buscar" > 
				<button id="Agregar"> Incluir </button>
			</div>
				<thead >
					<tr>
						<th> Articulo </th>
						<th> Unidad de medida </th>
						<th> Tipo de unidad </th>
						<th> Imagen </th>

						<th> Acciones </th>
					</tr>
				</thead>
				<tbody id="tablaProducto"></tbody>
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
				let contenido = document.getElementById("tablaProducto");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/buscar_producto.php";
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

		<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="formulario" enctype="multipart/form-data">

			<h2 class="bloque"> Agregar producto </h2>

			<!-- Articulos -->
			<div>
				<label for="Articulo"> Articulo: </label> 
				<input type="text" id="Articulo" name="articulo" >
			</div>	

			<div>
				<label for="select1"> Seleccione Unidad: </label>

				 <select id="select1" name="medida" required>
			        <option value="" disabled selected hidden> Seleccione: </option>

			        <!--*************** Seleccionar todas las unidades de medida de la tabla de medidas ****************** -->
			        <?php
			          $query = ("SELECT * FROM medidas");
			          $query_respuesta = mysqli_query($conexion, $query);
			          while ($valores = mysqli_fetch_array($query_respuesta)) {

			         ?>
			        <option value="<?= $valores["id"] ?>" > <?= $valores["Medida"] ?> </option>

			         <?php }  ?>
			   </select>
			</div>
			<div>
				<label for="Unidad" > Tipo de unidad: </label>
				<select name="tipoUnidad" id="Unidad" required>
					<option value="" disabled selected hidden> Seleccione: </option>
					<option  value="0">Unidad</option>
					<option  value="1">Unidades</option>
				</select>
			</div>
			<div>
				<label for="foto" > Imagen del producto: </label>
				<input type="file" id="foto" name="Imagen" >
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