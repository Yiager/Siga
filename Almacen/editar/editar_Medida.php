<?php 

include '../head.php';

	$id = $_GET['id'];

	$sql = $conexion->prepare("SELECT * FROM medidas WHERE id = '$id' ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$datos = $sql->get_result();
	$sql->close();

	while ($mostrar1 = $datos->fetch_assoc()) {


?>
<!DOCTYPE html>
<html>
<title> Medidas: Editar </title>
<body class="fondo">

<div id="formEditar">
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
		<h3 class="bloque"> Editar medida </h3>
		<input type="hidden" name="textid" value="<?=$mostrar1['id'] ?>">
		<div>
			<label for="Medida"> Unidad de medida: </label> 
			<input type="text" name="medida" id="Medida" value="<?= $mostrar1['Medida'] ?>" required >
		</div> 
		<div class="bloque">
			<button name="Actualizar" class="agregar"> Actualizar </button>
			<button class="Volver" name="Volver" > Volver </button>
		</div>
		<?php } ?>
	</form>
</div>

<?php
			if(isset($_POST['Actualizar'])){

				$idMedida = $_POST['textid'];
				$Unidad = $_POST['medida'];

				$sql2 = $conexion->prepare("UPDATE 
							medidas 
						SET 
							Medida = ?
						WHERE  
							id = ? ");
				$sql2->bind_param("is", $Unidad, $idMedida);
				$sql2->execute();
				$sql2->close();

				echo "<script>
							alert('Actualizacion fallida');
					</script>";
				
			}

			if(isset($_POST['Volver'])){
				header('location:../unidad_medida.php');
			}

		?>