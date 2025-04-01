<?php 

include '../head.php';

	$id = $_GET['id'];

	$sql = $conexion->prepare("SELECT 
										productos.*, 
										medidas.* 
							 FROM 
							 			productos 
							 			INNER JOIN medidas ON productos.id_medida = medidas.id 
							 WHERE 
							 			idProducto = ? LIMIT 1 ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$datos = $sql->get_result();
	$sql->close();

	while ($mostrar1 = $datos->fetch_assoc()) {

?>

<!DOCTYPE html>
<html lang="es">
<title> Producto: Editar </title>
<body class="fondo">
	<div id="formEditar">

	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" >
		<h3 class="bloque"> Editar producto </h3>
		<input type="hidden" name="textid" value="<?= $mostrar1['id'] ?>" >

		<div >
			<label for="Articulo"> Articulo: </label>
			<input type="text" name="articulo" id="Articulo" required value="<?= $mostrar1['Articulo'] ?>">
		</div>	

			<div>
			<label for="select1"> Seleccione Unidad: </label>
			 <select id="select1" name="Opcion">
		        <option value="<?= $mostrar1['id'] ?>" > <?= $mostrar1['Medida'] ?> </option>
		        <?php
		          $query = ("SELECT 
		          								medidas.*, 
		          								productos.* 
		          					 FROM 
		          					 			medidas 
		          					 			INNER JOIN productos ON medidas.id = productos.id_medida 
		          					 GROUP BY 
		          					 			productos.id_medida ");

		          $query_respuesta = mysqli_query($conexion, $query);
		          while ($valores = mysqli_fetch_array($query_respuesta)) {

		         ?>
		            <option value="<?= $valores["id"] ?>" > <?= $valores["Medida"] ?> </option>

		         <?php }  ?>
		      </select>
		  </div>
			<div>
				<label for="tipo"> Tipo de unidad: </label>
				<select id="tipo" name="opciones">
				<?php	if($mostrar1['Tipo'] == "Si"){ ?>
						
						 <option value="<?= $mostrar1["Tipo"] ?>" > <?= "Unidad" ?> </option>
				<?php	} ?>

				<?php	if($mostrar1['Tipo'] == "No"){ ?>
						
						 <option value="<?= $mostrar1["Tipo"] ?>" > <?= "Unidades" ?> </option>
				<?php	} ?>
					<option  value="0">Unidad</option>
					<option  value="1">Unidades</option>

				</select>
		</div>

		<input type="hidden" name="textid" value="<?= $mostrar1['idProducto'] ?>"> 

		<div class="bloque">
			<button name="actualizar" class="agregar" > Actualizar </button>
			<button class="Volver" name="Volver"> Volver </button>
		</div>

	</form>

<?php } ?>

</div>

</body>
</html>
		<?php

			if(isset($_POST['actualizar'])){

				$idProducto = $_POST['textid'];
				$Articulo = $_POST['articulo'];
				$medida = $_POST['Opcion'];
				$Tipo = $_POST['opciones'];
			
				$sql2 = $conexion->prepare("UPDATE 
										productos 
								 SET 
								 		Articulo = ?, 
								 		id_medida = ?, 
								 		Tipo = ? 
								 WHERE  
								 		idProducto = ? ");
				$sql2->bind_param("sisi", $Articulo, $medida, $Tipo, $idProducto);
				$sql2->execute();
				$sql2->close();

			}
					
				echo "<script>
								alert('Actualizacion Exitosa');
								window.location = '../Productos.php';
							</script>";
		
			if(isset($_POST['Volver'])){
				header('location:../Productos.php');
			}


		?>
