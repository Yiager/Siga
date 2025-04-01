<!DOCTYPE html>
<html lang="es">
<title> Proveedores </title>
<link rel="icon" href="img/SIGA.png">
<body>

<header>
	<h2> Proveedores </h2>
</header>

<?php
include("nav.php");

//Registro de proveedor nuevo
if (isset($_POST["Enviar"])) {

	$Empresa = $_POST["empresa"];
	$Rif = $_POST["rif"];
	$Email = $_POST["mail"];
	$Telefono = $_POST["telefonoE"];
	$Contacto = $_POST["PContacto"];
	$tlfCon = $_POST["TlfContacto"];

	$Servicio = '';

	if(isset($_POST['opcion'])){
		$Servicio = implode('\n' , $_POST['opcion']);
	}

	$sqlproveedor = $conexion->prepare("SELECT 
						id 
					 FROM 
					 	prove 
					 WHERE 
					 	Empresa = ?
					 	AND Rif = ? ");
	$sqlproveedor->bind_param("ss", $Empresa, $Rif);
	$sqlproveedor->execute();
	$sqlproveedor->store_result();
	$filas = $sqlproveedor->num_rows;
	$sqlproveedor->close();

	//verificar que haya respuesta de conexion a la base de datos
	if($filas > 0){
		echo "<script>
				alert('Ya existe este proveedor');
				window.location('Proveedores.php');
			 </script>";
	}else{

		//declaracion sql para insertar datos en la base de datos
		//De los proveedores
		$sqlNuevoProvee = $conexion->prepare("INSERT INTO 
									prove (Empresa, Rif, Correo, TlfLocal, Contacto, Tlf, Servicio) 
							VALUES
							(
								?, 
								?, 
								?, 
								?, 
								?, 
								?, 
								? 
							)");
		$sqlNuevoProvee->bind_param("sssssss", $Empresa, $Rif, $Email, $Telefono, $Contacto, $tlfCon, $Servicio);
		$sqlNuevoProvee->execute();
		$sqlNuevoProvee->close();

		echo "<script> 
				alert('Registro exitoso!');
				window.location('Proveedores.php');
			</script>";

	}
}

?>

<div id="form">

		<!-- Formulario para proveedores -->

	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="formulario" >

		<h2 class="bloque"> Agregar proveedor </h2>

		<!-- Nombre y apellido del proovedor -->
		<div>
			<label for="Empresa"> Empresa: </label> 
			<input type="text" name="empresa" id="Empresa" required >
		</div> 

		<!-- Cedula de identidad del Proovedor -->
		<div >
			<label for="Rif"> RIF: </label> 
			<input type="text" name="rif" id="Rif" required >
		</div>	

		<!-- Correo electronico del proovedor -->
		<div >
			<label for="Email"> Correo Electronico: </label> 
			<input type="text" id="Email" name="mail"  required >
		</div>

		<!-- Numero telefonico de contacto del proovedor -->
		<div >
			<label for="TelefonoE"> Tlf Empresa: </label> 
			<input type="text" id="TelefonoE" name="telefonoE" required>
		</div>

		<!-- Persona de contacto de la empresa -->
		<div >
			<label for="PContacto"> Persona de contacto: </label> 
			<input type="text" id="PContacto" name="PContacto" required>
		</div>

		<!-- Numero telefonico de la persona de contacto -->
		<div >
			<label for="Telefono"> Numero de contacto: </label> 
			<input type="text" id="Telefono" name="TlfContacto"  required >
		</div>

		<!-- Tipo de producto que ofrece el proveedor -->
		<div class="servicios">
			<p> Servicio que ofrece: </p> 

			<label for="casillas1"> Articulos de oficina </label>
			<input type="checkbox" name="opcion[]" id="casillas1" value="Articulos de oficina" > 

			<label for="casillas2"> Mantenimiento </label>
			<input type="checkbox" name="opcion[]" id="casillas2" value="Mantenimiento" > 

			<label for="casillas3"> Computacion </label>
			<input type="checkbox" name="opcion[]" id="casillas3" value="Computacion" > 

			<label for="casillas4"> Reparacion </label>
			<input type="checkbox" name="opcion[]" id="casillas4" value="Reparacion" > 

		</div>
		<div class="bloque">
			<button class="agregar" name="Enviar"> Agregar </button>
			<button class="Volver" id="Volver" > Volver </button>
		</div>
	</form>

</div>

<div id="table" >

		<table >
			<div class="botones" >
					<input type="text" name="buscar" id="buscar" placeholder="Buscar"> 
					<button id="Agregar"> Incluir </button>
					<a class="listado" href="/Almacen/Reportes/reportesProve.php?" target="_blank"> Listado </a> 
			</div>
				<thead >
					<tr>
						<th> Empresa </th>
						<th> Nº de Rif </th>
						<th> Correo electronico </th>
						<th> Telefono </th>
						<th> Contacto </th>
						<th> Tlf personal </th>
						<th> Servicio </th>
						<th> Acciones </th>
					</tr>
				</thead>

				<tbody id="tablaProvee"></tbody>
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
				let contenido = document.getElementById("tablaProvee");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/buscar_prove.php";
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

<script src="js/events.js"></script>

</body>

</html>