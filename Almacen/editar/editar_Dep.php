<?php 

include '../head.php';

	//identificador del departamento
	$id = $_GET['id'];
	//sentencia y respuesta de buscqueda del departamento
	$sql = $conexion->prepare("SELECT * FROM departamentos WHERE id = ? LIMIT 1 ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$datos = $sql->get_result();
	$sql->close();

	while ($mostrar1 = $datos->fetch_assoc()) {

?>
<!DOCTYPE html>
<html>
<title> Departamentos: Editar </title>
<body class="fondo">

<div id="formEditar">

	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

	<h3 class="bloque"> Editar Departamento </h3>

	<input type="hidden" name="textid" value="<?= $mostrar1['id'] ?>">
	<div>
		<label for="Dep"> Departamento: </label> 
		<input type="text" name="dep" id="Dep" value="<?= $mostrar1['departamento'] ?>" required >
	</div> 
	<div class="bloque">
		<button name="Actualizar"class="agregar" > Guardar </button>
		<button class="Volver" name="Volver"> Volver </button>
	</div>

<?php } ?>

</form>

</div>

<?php

			if(isset($_POST['Actualizar'])){

				$idDep = $_POST['textid'];
				$Dep = $_POST['dep'];

			// ****************** Verificar que no se duplica el departamento al editarlo ******************
					$sqlVerificar = $conexion->prepare("SELECT 
										departamento 
									FROM 
										departamentos 
									WHERE 
										departamento = ? ");
					$sqlVerificar->bind_param("i", $Dep);
					$sqlVerificar->execute();
					$buscarDep = $sqlVerificar->num_rows;
					$sqlVerificar->close();

				if ($buscarDep > 0) {

					echo "<script>
								alert('Actualizacion fallida: Ya existe un departamento con este nombre');
						</script>";
					
				}else{

					$sql2 = $conexion->prepare("UPDATE 
								departamentos 
							 SET 
							 	departamento = ?
							 WHERE  
							 	id = ?");
					$sql2->bind_param("ii", $Dep, $idDep);
					$sql2->execute();

					
					echo "<script>
							alert('Actualizacion Exitosa');
							window.location = '../Departamento.php';
						</script>";
				}
			}

			if(isset($_POST['Volver'])){

				header('location:../Departamento.php');
			}


		?>