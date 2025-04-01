<!DOCTYPE html>
<html>
<body>

<title> Almacen </title>

<header>
	<h2> Almacen </h2>
</header>

<?php
include('nav.php');

//Registro de proveedor nuevo

if (isset($_POST["Enviar"])) {

	$almacen = $_POST["almacen"];
	$Responsable = $_POST["persona"];
	$Email = $_POST["email"];
	$Telefono = $_POST["telefono"];
	$ubicacion = $_POST["ubicacion"];


	$sql = $conexion->prepare("SELECT 
				id, Almacen, PersonaR 
			FROM 
				almacen 
			WHERE 
				Almacen = ?
				AND PersonaR = ? ");

	$sql->bind_param("ss", $almacen, $Responsable);
	$sql->execute();
	$sql->store_result();
	$filas = $sql->num_rows;
	$sql->close();

	//verificar que haya respuesta de conexion a la base de datos

	if($filas > 0){

		echo "<script>
			alert('Ya existe este almacen');
			window.location.href = 'Almacen.php';

		 </script>";

	}else{

		//declaracion sql para insertar datos en la base de datos
		//De los proveedores

		//Preparar sentencia sql
		$sqlAlmacen = $conexion->prepare("INSERT INTO 
							almacen (Almacen, PersonaR, Correo, Telefono, Ubicacion) 
						VALUES
						(
							?, 
							?, 
							?, 
							?, 
							?
						)");

		//Limpiar valores ingresados por el usuario
		$sqlAlmacen->bind_param("sssss", $almacen, $Responsable, $Email, $Telefono, $ubicacion);
		$sqlAlmacen->execute();
		$sqlAlmacen->close();

		echo "<script> 
			alert('Registro exitoso!');
			window.location.href = 'Almacen.php';

		</script>";
		
	}
}


?>

<!DOCTYPE html>
<html lang="es">
<head>

	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0" >
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" media="only screen and (max-width: 1080px)" href="css/adaptable.css">
	<link rel="stylesheet" media="only screen and (min-width: 1080px) and (max-width:1288px)" href="css/adaptableMed.css">
	<link rel="stylesheet" type="text/css" href="css/estiloMenu.css">
	<link rel="stylesheet" type="text/css" href="css/Estilo.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">

</head>

	<div id="table">
		<table >

				<div class="botones">
					<input id="buscar" type="text" name="buscar" placeholder="Buscar"> 
					<button id="Agregar" > Incluir </button> 
				</div>

				<thead >
					<tr>
						<th> Codigo </th>
						<th> Almacen </th>
						<th> Responsable </th>
						<th> Telefono </th>
						<th> Correo </th>
						<th> Ubicacion </th>
						<th> Acciones </th>
					</tr>
				</thead>
				
					<tbody id="tablaAlmacen">
				
			</tbody>
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
				let contenido = document.getElementById("tablaAlmacen");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/buscar_almacen.php";
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
		<h2 class="bloque"> Agregar Almacen </h2> 
				<div >
					<label for="Almacen"> Nombre:  </label> 
					<input type="text" id="Almacen" name="almacen" required>
				</div>
				<div>
					<label for="Persona"> Responsable almacen:  </label> 
					<input type="text" id="Persona" name="persona" required> 
				</div>   
				<div>
					<label for="Telefono"> Telefono: </label> 
					<input type="text" id="Telefono" name="telefono" required> 
				</div>   
				<div>
					<label for="Email"> Correo electronico: </label> 
					<input type="email" id="Email" name="email" required> 
				</div>   
				<div>
					<label for="Ubicacion"> Ubicacion: </label>
					<input type="text" id="Ubicacion" name="ubicacion" required>
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