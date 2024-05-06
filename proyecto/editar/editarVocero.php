<?php

include("../conect.php");

$conexion = conectar();

session_start();

if(!isset($_SESSION['idUser'])){
	header("Location: index.php");
}

$idCC = $_GET['id'];
$CI = $_GET['CI'];
$id_vocero = $_GET['V'];

$sqlVocero = "SELECT * FROM voceros WHERE Cedula = '$CI' ";
$respuestaVocero = mysqli_query($conexion, $sqlVocero);

//Inicio de ciclo para iterar los valores existentes en la base de datos y mostrarlos en los respectivos campos del formulario
while($traerVocero = mysqli_fetch_assoc($respuestaVocero)){
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/estiloMenu.css">
	<link rel="stylesheet" type="text/css" href="../css/estiloDatos.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
	<title> Editar vocero </title>
</head>
<body style="background-color: black;">

	<div class="contenedor" style="margin-top: 45px;">
		<div class="formVoceros FormEdit">
		<form  method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
			<h2 class="tituloFormV"> Editar vocero </h2>

			<input type="hidden" name="id_cc" value="<?php echo $idCC ?>">
			<input type="hidden" name="id_vocero" value="<?php echo $id_vocero ?>">

			<p>
				<label for="Nombre"> Nombres: </label>
				<input type="text" name="name" id="Nombre" value="<?php echo $traerVocero['Nombre']; ?>" required>
			</p>
			<p>
				<label for="Apellido"> Apellidos: </label>
				<input type="text" name="apellido" id="Apellido" value="<?php echo $traerVocero['Apellido']; ?>" required>
			</p>
			<p>
				<label for="CI"> Cedula: </label>
				<input type="text" name="cedula" id="CI" value="<?php echo $traerVocero['Cedula']; ?>" required>
			</p>
			<p>
				<label for="Tlf"> Telefono: </label>
				<input type="text" name="tlf" id="Tlf" value="<?php echo $traerVocero['Tlf']; ?>" required>
			</p>
			<p>
				Unidad: 
				<select name="unidad" id="Unidades" required> 
					<option disabled selected hidden> Seleccione: </option>
					<option value="Unidad Ejecutiva"> Unidad Ejecutiva </option>
					<option value="Unidad Administrativa y financiera"> Unidad Administrativa y financiera </option>
					<option value="Unidad de Contraloria Social Comunal"> Unidad de Contraloria Social Comunal </option>
					<option value="Comision Electoral"> Comision Electoral </option>
				</select>
			</p>
			<p>
				<label for="Votos"> Votos: </label>
				<input type="text" name="votos" id="Votos" value="<?php echo $traerVocero['Votos']; ?>" required>
			</p>
			<p id="Campo-comites">
				Comite: 
				<select name="comite" id="comites" > 
					<option disabled selected hidden> Seleccione: </option>
					<option value="Alimentacion y Defensa al consumidor"> Alimentacion y Defensa al consumidor </option>
					<option value="Comunitario de personas con discapacidad"> Comunitario de personas con discapacidad </option>
					<option value="Economia Comunal"> Economia Comunal </option>
					<option value="Ecosocialismo"> Ecosocialismo </option>
					<option value="Educacion, Cultura y formacion ciudadana"> Educacion, Cultura y formacion ciudadana </option>
					<option value="Familia e igualdad de genero"> Familia e igualdad de genero </option>
					<option value="Mesa tecnica de agua"> Mesa tecnica de agua </option>
					<option value="Medios alternativos comunitarios"> Medios alternativos comunitarios </option>
					<option value="Mesa tecnica de energia y gas"> Mesa tecnica de energia y gas </option>
					<option value="Planificacion comunal"> Planificacion comunal </option>
					<option value="Proteccion social de niños, niños y adolescentes"> Proteccion social de niños, niños y adolescentes</option>
					<option value="Recreacion y deporte"> Recreacion y deporte </option>
					<option value="Salud integral"> Salud integral </option>
					<option value="Seguridad y defensa integral"> Seguridad y defensa integral </option>
					<option value="Tierra urbana"> Tierra urbana </option>
					<option value="Transporte"> Transporte </option>
					<option value="Turismo"> Turismo </option>
					<option value="Vivienda y habitad"> Vivienda y habitad </option>
					<option value="Ambiente y demarcacion de tierra en los habitad indigenas"> Ambiente y demarcacion de tierra en los habitad indigenas </option>
					<option value="Medicina tradicional indigena"> Medicina tradicional indigena </option>
					<option value="Educacion propia, educacion intercultural bilingue e idiomas indigenas"> Educacion propia, educacion intercultural bilingue e idiomas indigenas </option>
					<option value="Otro"> Otro </option>
				</select>
			</p>

			<p id="NuevoComite">
				<label for="NuevoC"> Nuevo comite: </label>
				<input type="text" name="Cnuevo" id="NuevoC">
			</p>

				<script type="text/javascript">
					
					document.getElementById('comites').addEventListener('change', function(){

						if(this.value == 'Otro'){
							document.getElementById('NuevoComite').style.display = 'block' ;
						}else{
							document.getElementById('NuevoComite').style.display = 'none' ;
						}

					});

					document.getElementById('Unidades').addEventListener('change', function(){

						if(this.value == 'Unidad Ejecutiva'){
							document.getElementById('Campo-comites').style.display = 'block' ;
						}else{
							document.getElementById('Campo-comites').style.display = 'none' ;
						}

					});

				</script>
			<p>
				Tipo: 
				<select name="tipo" required> 
					<option disabled selected hidden> Seleccione: </option>
					<option value="Principal"> Principal </option>
					<option value="Suplente"> Suplente </option>
				</select>
			</p>

			<button class="RegistrarVocero" name="Actualizar"> Registrar </button>
		</form>

		<?php
			}
		?>

		<a id="btnVolver" href="../DatosConcejo.php?id=<?php echo $idCC ?>" > Volver </a>
	</div>

</div>

	<?php

		if(isset($_POST['Actualizar'])){

				$idConcejo = $_POST['id_cc'];
				$nombre = $_POST['name'];
				$apellido = $_POST['apellido'];
				$cedula = $_POST['cedula'];
				$tlf = $_POST['tlf'];
				$votos = $_POST['votos'];
				$comite = $_POST['comite'];
				$comiteNuevo = $_POST['Cnuevo'];
				$Unidad = $_POST['unidad'];	
				$tipo = $_POST['tipo'];
				$id_V = $_POST['id_vocero'];

				if($Unidad != 'Unidad Ejecutiva'){
					$comite = 'N/A';

				}

				if($comite == 'Otro'){
					$comite = $comiteNuevo;

				}

				$ActualizarVocero = 'SELECT Cedula, Comite FROM voceros WHERE Cedula = "$cedula" AND Comite = "$comite" AND Unidad = "$Unidad" ';
				$verificar = mysqli_query($conexion, $ActualizarVocero);
				$buscarNombre = $verificar->num_rows;

				if($buscarNombre > 0){

					echo "<script>

							alert('Error, no se pudo actualizar el vocero, ya existe!');

						</script>
					";

				}else{

					$sqlActualizarVocero = "UPDATE 
												voceros 
										  SET 
										  	Nombre = '$nombre', 
										  	Apellido = '$apellido', 
										  	Cedula = '$cedula', 
										  	Tlf = '$tlf', 
										  	Votos = '$votos',
										  	Comite = '$comite',
										  	Tipo = '$tipo',
										  	Unidad = '$Unidad'
										  WHERE 
										  	id_cc = '$idConcejo' AND id_vocero = '$id_V' ";

					$respuestaActualizar = mysqli_query($conexion, $sqlActualizarVocero);

					echo "<script>

							alert('Vocero actualizado exitosamente!');
							window.location='../DatosConcejo.php?id=$idCC';

						</script>
					";

				}

		}

	?>

</body>
</html>