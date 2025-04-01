<!DOCTYPE html>
<html lang="es">
	<title> Usuarios </title>
<body>

<header>
	<h2> Usuarios </h2>
</header>

<?php
	include("nav.php");
	
//Registrar ususario
if(isset($_POST["Enviar"])) {
	$nombre = $_POST['nombre'];
	$correo = $_POST['correo'];
	$usuario = $_POST['user'];
	$password = $_POST['contraseña'];
	$tipoUsuario = $_POST['tipo'];
	$password_encriptada = sha1($password);

	$sqluser = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ? ");
	$sqluser->bind_param("s", $usuario);
	$sqluser->execute();
	$sqluser->store_result();
	$rows = $sqluser->num_rows;
	$sqluser->close();

	if($rows > 0){
		echo "<script >
				alert('El nombre de usuario ya existe');
				window.location = 'usuarios.php';
			</script>";
	}else{

		//Insertar informacion del usuario
		$sqlusuario = $conexion->prepare("INSERT INTO 
								usuarios (nombre, correo, usuario, password, Tipo) 
					   VALUES
					   (
					   	?, 
					   	?, 
					   	?, 
					   	?, 
					   	?
					   )");
		$sqlusuario->bind_param("sssss", $nombre, $correo, $usuario, $password_encriptada, $tipoUsuario);
		$sqlusuario->execute();
		$sqlusuario->close();

		echo "<script>
				alert('Registro exitoso');
				window.location = 'usuarios.php';
			</script>";

	}
}

?>

<div id="table">

	<!-- Tabla de usuarios -->
		<table>
			<div class="botones" >
				<input type="text" name="buscar" id="buscar" placeholder="Buscar" > 
				<button id="Agregar" > Incluir </button>
			</div>
				<thead>
					<tr>
						<th> Nombre y Apellido </th>
						<th> Correo electronico </th>
						<th> Nombre de usuario </th>
						<th> Tipo de usuario </th>
						<th> Acciones </th>
					</tr>
				</thead>
				<tbody id="tablaUsuarios"></tbody>
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
				let contenido = document.getElementById("tablaUsuarios");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/buscar_user.php";
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

	<!-- Formulario de registro de usuario nuevo-->
	<div id="form" >
		<form  action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="formulario" >
			
			<h2 class="bloque"> Agregar Usuario: </h2> 
			
			<div >
				<label for="Nombre"> Nombre y Apellido: </label> 
				<input type="text" id="Nombre" name="nombre" required> 
			</div>  
			<div >
				<label for="Contraseña"> Contraseña: </label> 
				<input type="password" id="Contraseña" name="contraseña" required> 
			</div> 
			<div>
				<label for="User"> Nombre de usuario: </label> 
				<input type="text" id="User" name="user" required> 
			</div> 
			<div >
				<label for="Correo"> Correo electronico </label>
				<input type="email" id="Correo" name="correo" required> 
			</div>
			<div>
				<label for="Tipo"> Tipo de usuario: </label> 
				<input type="text" id="Tipo" name="tipo" required> 
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