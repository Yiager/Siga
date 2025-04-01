<!DOCTYPE html>
<html lang="es">
	<title> Unidad de medida </title>
<body>

<header>

	<h2> Medidas </h2>

</header>

<?php
	include("nav.php");

//Agregar Producto nuevo

if (isset($_POST["Enviar"])) {

	$Medida = $_POST['medida'];

	$sqlUnidad = $conexion->prepare("SELECT id FROM medidas WHERE Medida = ? ");
	$sqlUnidad->bind_param("s", $Medida);
	$sqlUnidad->execute();
	$sqlUnidad->store_result();
	$filas = $sqlUnidad->num_rows;
	$sqlUnidad->close();

	//verificar que haya respuesta de conexion a la base de datos
	if($filas > 0){

		echo "<script>
				alert('Ya existe esa medida!');
				window.location('unidad_medida.php');
			 </script>";

	}else{

		//declaracion sql para insertar datos en la base de datos
		//De los proveedores

		$sqlNuevaMedida = $conexion->prepare("INSERT INTO 
									medidas (Medida) 
							VALUES
							( 
								?
							)");

		$sqlNuevaMedida->bind_param("s", $Medida);
		$sqlNuevaMedida->execute();
		$sqlNuevaMedida->close();

		echo "<script> 
				alert('Medida agregada de manera exitosa');
				window.location('unidad_medida.php');
			</script>";

	}
}

?>

<div id="table" >

		<table >
			<div class="botones" >
			 	<input type="text" name="buscar" id="buscar" placeholder="Buscar" > 
				<button id="Agregar"> Incluir</button> 
			</div>
				<thead >
					<tr>
						<th> Nº Codigo </th>
						<th> Unidad de medida </th>
						<th> Acciones </th>
					</tr>
				</thead>
					<tbody id="tablaMedida"></tbody>
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
				let contenido = document.getElementById("tablaMedida");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/buscar_unidad.php";
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

		<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="formulario" >

			<h2 class="bloque"> Agregar Unidad de Medida </h2>

			<!-- Añadir medida nueva -->
			<div >
				<label for="Medida"> Unidad de medida: </label> 
				<input type="text" id="Medida" name="medida"  required >
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