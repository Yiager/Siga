<?php

include("conect.php");

$conexion = conectar();

session_start();

if(!isset($_SESSION['idUser'])){
	header("Location: index.php");
}

$idSesion = $_SESSION['idUser'];

$usuarioInfo = "SELECT Nombre, Usuario, tipo FROM usuarios WHERE id = '$idSesion'";
$respuestaInfo = mysqli_query($conexion, $usuarioInfo);

$info = mysqli_fetch_assoc($respuestaInfo);

$nombreUser = $info['Nombre'];
$UserName = $info['Usuario'];

$fechaActual = date("d/m/Y");



if(isset($_POST['Registrar'])){

	$nombre = $_POST['name'];
	$usuario = $_POST['user'];
	$contraseña = $_POST['pass'];
	$contraseña_encrip = sha1($contraseña);
	$correo = $_POST['mail'];
	$tlf = $_POST['tlf'];
	$tipo = $_POST['tipo'];


	$verificarUsuario = "SELECT Usuario FROM usuario WHERE Usuario = '$usuario' ";
	$verificar = mysqli_query($conexion, $verificarUsuario);


	if($verificar > 0){

		echo "<script> 

				alert('El nombre de usuario ya existe!');
				back();

		</script>";


	}else{


		$sqlInsertarUsuario = "INSERT INTO 
										usuarios (Nombre, Usuario, pass, telefono, correo, tipo) 
								VALUES 
									(
										'$nombre', 
										'$usuario', 
										'$contraseña_encrip', 
										'$tlf', 
										'$correo', 
										'$tipo' 
									)";

		$respuestaConexion = mysqli_query($conexion, $sqlInsertarUsuario);

		echo "<script> 

				alert('Usuario registrado de manera exitosa!');
				window.location = 'User.php';


		</script>";

	}
}

$registros_por_pagina = 2;

//Datos para la paginacion de los registros de usuario
$sqlUsers = "SELECT * FROM usuarios";
$respuestaRegistros = mysqli_query($conexion, $sqlUsers);
$TotalUsers = $respuestaRegistros->num_rows;

$paginas = ceil($TotalUsers/$registros_por_pagina);

$Iniciar_desde = ($_GET['pagina']-1)*$registros_por_pagina;

if(!$_GET){
	header("Location: User.php?pagina=1");
}

if($_GET['pagina']>$paginas || $_GET['pagina']<=0){
	header("Location:User.php?pagina=1");
}



?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloMenu.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
	<title> Usuarios </title>
</head>
<body>

	<header>
		<div class="fecha">   
			<p> <?php echo $fechaActual ?> </p>
		</div>
		<div class="information">
			<h3> <?php echo $nombreUser ?> </h3>
			<h4> <?php echo $UserName ?> </h4>
		</div>
	</header>

	<div id="sidebar" >

		<div class="toggle-btn">
			
			<span>&#9776;</span>

		</div>

		<ul>
			<li>
				<img src="img/user.png" alt="logo" class="logo">
			</li>
			<li> <a href="menu.php"> Menu principal </a> </li>
			<li> <a href="ConcejoC.php"> Registros </a> </li>
			<?php
				if($info['tipo'] != 3){
					echo "<li style='display:none;'> <a  href='User.php'> Usuarios </a> </li>";
				}else{
					echo "<li> <a href='User.php'> Usuarios </a> </li>";
				}
			?>
			<li> <a href="mensajeria.php"> Mensajes </a> </li>
			<li> <a href="ayuda.php"> Ayuda </a> </li>
			<li> <a href="salir.php"> Salir </a> </li>

		</ul>


	</div>

<div class="contenedor">

	<div id="FormU" class="FormUser">
		
		<form method="POST"  action="<?php $_SERVER['PHP_SELF'] ?>" id="formUser"> 

			<h2 class="tituloFormU"> Usuario nuevo</h2>
			
			<p>
				<label for="Nombre"> Nombre y Apellido: </label>
				<input type="text" name="name" id="Nombre" placeholder="Ej: Juan Perez..." required>
			</p>

			<p>
				<label for="Usuario"> Usuario: </label>
				<input type="text" name="user" id="Usuario" placeholder="Ej: Juan4, JuanP..." required>
			</p>

			<p>
				<label for="Pass"> Contraseña: </label>
				<input type="password" name="pass" id="Pass" required>
			</p>

			<p>
				<label for="Correo"> Correo Electronico: </label>
				<input type="text" name="mail" id="Correo" placeholder="Ej: correo@.gmail.com..." required>
			</p>

			<p>
				<label for="Tlf"> Telefono: </label>
				<input type="text" name="tlf" id="Tlf" placeholder="Ej: 0414 123 4567" required>
			</p>

			<p>
				Tipo de usuario: 
				<select name="tipo" required> 

					<option disabled selected hidden> Seleccione: </option>
					<option value="1"> Invitado </option>
					<option value="2"> Estandar </option>
					<option value="3"> Administrador </option>


				</select>
			</p>


			<p>
				<button class="btn-Enviar" name="Registrar"> Registrar </button>
			</p>

			<p>
				<button class="btn-Volver" id="botonFormU"> Volver </button>
			</p>

		</form>

	</div>

</div>

	<div class="tablaUsers" id="TablaU">
		
		<div class="cabecera">
			<h2 class="tituloUsers"> Usuarios </h2>

			<button class="btn-incluirU" id="botonTablaU"> Incluir </button>

		</div>
		
		<table>

			<thead>
				<th> Nombre y apellido </th>
				<th> Usuario </th>
				<th> Telefono </th>
				<th> Correo </th>
				<th> Tipo </th>
				<th> Acciones </th>
			</thead>

			<tbody>
				<tr>

					<?php

						$sqlUsuarios = "SELECT * FROM usuarios LIMIT  $Iniciar_desde, $registros_por_pagina";
						$sqlRespuesta = mysqli_query($conexion, $sqlUsuarios);

						while($traerUsers = mysqli_fetch_assoc($sqlRespuesta)){

						$idUsuario = $traerUsers['id'];
						$tipoUsuario = $traerUsers['tipo'];

					?>


					<td> <?php echo $traerUsers['Nombre']; ?> </td>
					<td> <?php echo $traerUsers['Usuario']; ?> </td>
					<td> <?php echo $traerUsers['telefono']; ?> </td>
					<td> <?php echo $traerUsers['correo']; ?> </td>

					<?php

						switch ($tipoUsuario) {
							case '2':
								echo "<td> Estandar </td>";
								break;

							case '3':
								echo "<td> Administrador </td>";
								break;

							default:
								echo "<td> Invitado </td>";
								break;
						}

					?>

					<td> <a class="acciones" href="editar/editarUser.php?id=<?php echo $idUsuario ?>"> Editar </a> 
						<button onclick="Eliminar()" class="acciones" > Eliminar </button> </td>

					<?php

							echo "
									<script>

										function Eliminar(){
											let pregunta = confirm('¿Estas seguro que deseas eliminar este registro?');
											if(pregunta == true){
												window.location.href = 'eliminar/eliminarUser.php?id=$idUsuario'
											}
										}

									</script>

								";

					?>

				</tr>
			</tbody>
					<?php
						}
					?>
		</table>

		<ul class="paginador">
			
			<li id="<?php echo $_GET['pagina']==1 ? 'disable' : '' ?>"> <a href="User.php?pagina=<?php echo $_GET['pagina']-1 ?>"> Anterios </a></li>

			<?php for ($i=0; $i < $paginas ; $i++) { ?>
				<li id="<?php echo $_GET['pagina']==$i+1 ? 'active' : '' ?>"> 
					<a href="User.php?pagina=<?php echo $i+1 ?>"> <?php echo $i+1 ?> </a></li>
			<?php } ?>
			<li id="<?php echo $_GET['pagina']>=$paginas ? 'disable' : '' ?>"> <a href="User.php?pagina=<?php echo $_GET['pagina']+1 ?>"> Siguiente </a></li>


		</ul>


	</div>




	<script type="text/javascript" src="js/sidebar.js"></script>
	<script type="text/javascript" src="js/eventsU.js"></script>

</body>
</html>