<?php

include "conect.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$id = $_SESSION['id_usuario'];

$UsuarioTipo = $conexion->prepare("SELECT id, Tipo FROM usuarios WHERe id = ? ");
$UsuarioTipo->bind_param("s", $id);
$UsuarioTipo->execute();
$datosUsuario = $UsuarioTipo->get_result();
$UsuarioTipo->close();
$tipo = $datosUsuario->fetch_assoc();

if ($tipo['Tipo'] == 0) {
	header("Location: menu.php");
}

$fechaActual = date("d/m/Y");

if(isset($_POST['Agregar'])){

	$Nombre = $_POST['nombre'];
	$Apellido = $_POST['apellido'];
	$Usuario = $_POST['usuario'];
	$Contraseña = $_POST['contraseña'];
	$contraseña_encriptada = sha1($Contraseña);
	$Tipo = $_POST['Tipo'];

	$verificar_bd = $conexion->prepare("SELECT id FROM usuarios WHERE Usuario = ? ");
	$verificar_bd->bind_param("s", $Usuario);
	$verificar_bd->execute();
	$verificar_bd->store_result();
	$filas = $respuesta->num_rows;
	$verificar_bd->close();

	if($filas > 0){
		echo 
		"<script>
			alert('Ya existe ese nombre de usuario!');
		</script>";
	}else{

		$insertar_datos = $conexion->prepare("INSERT INTO usuarios (Nombre, Apellido, Usuario, Contraseña, Tipo) VALUES (?,?,?,?,?)");
		$insertar_datos->bind_param("sssss", $Nombrem, $Apellido, $Usuario, $contraseña_encriptada, $Tipo);
		$insertar_datos->execute();
		$insertar_datos->close();

		if($respuesta_insertar == True){
			echo "<script> 
						alert('¡Usuario agregado exitosamente!')
						window.location = 'usuarios.php'
			 	  </script>";
		}else{
				echo "<script> 
						alert('¡Error al insertar datos!')
						window.location = 'usuarios.php'
			 	  </script>";
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloUser.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	
	<title> Usuarios </title>
</head>
<body>

	<header>
		<div class="titulo"> <h3> Usuarios </h3> </div>
		<div class="fecha"> <p> <?= $fechaActual; ?> </p> </div>
	</header>
	
	<div class="barraNav">
		<ul>
			<li> <button id="agregar"> Agregar </button></li>
			<li> <button id="ver"> Ver </button></li>
		</ul>
	</div>
	
	<div class="formularioUser" id="formulario">
		<form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>" >
			<h2> Agregar usuario </h2>
			<p>
				<label for="Nombre"> Nombre: </label>
				<input type="text" name="nombre" id="Nombre" required>
			</p>
			<p>
				<label for="Apellido"> Apellido: </label>
				<input type="text" name="apellido" id="Apellido" required>
			</p>
			<p>
				<label for="Usuario"> Usuario: </label>
				<input type="text" name="usuario" id="Usuario" required>
			</p>
			<p>
				<label for="Contraseña"> Contraseña: </label>
				<input type="password" name="contraseña" id="Contraseña" required>
			</p>
			<p>
				<label for="tipo"> Tipo de usuario: </label>
				<select id="tipo" name="Tipo" required>
					<option value="" disabled selected hidden> Seleccione: </option>
					<option value="0"> Estandar </option>
					<option value="1"> Administrador </option>
				</select>
			</p>
			<p>
				<input type="submit" name="Agregar" value="Agregar">
			</p>
		</form>
	</div>

	<div class="tablaUser" id="tabla">

		<input type="text" name="buscar" placeholder="Buscar..." class="buscador" id="buscar"> 
		
		<table>
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Apellido</th>
					<th>Usuario</th>
					<th>Tipo</th>
					<th>Acciones</th>
				</tr>
			</thead>

			<tbody id="tablaUsuarios">
				
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
				let contenido = document.getElementById("tablaUsuarios");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "buscar/consultaUser.php";
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

	<a href="menu.php" class="btnVolver"> Volver </a>

<script  type="text/javascript" src="js/Events.js"></script>
</body>
</html>
