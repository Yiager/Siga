<!DOCTYPE html>
<html lang="es">
<title> Departamento </title>

<header>
	<h2>Departamentos </h2>
</header>
<body>

<?php
include("nav.php");

if(isset($_POST["Enviar"])) {
	$Dep = $_POST['Dep'];
	

	$sqlDep = $conexion->prepare("SELECT 
					id 
				FROM 
					departamentos 
				WHERE 
					departamento = ? ");

	$sqlDep->bind_param("s", $Dep);
	$sqlDep->execute();
	$sqlDep->store_result();
	$rows = $sqlDep->num_rows;
	$sqlDep->close();

	if($rows > 0){
		echo "<script >
			
				alert('El departamento ya existe');
				window.location = 'Departamento.php';
			</script>";

		}else{

		$sql = $conexion->prepare("INSERT INTO departamentos (departamento) VALUES(?)");
		$sql->bind_param("s", $Dep);
		$sql->execute();
		$sql->close();

		echo "<script>
				alert('Registro exitoso');
				window.location = 'Departamento.php';
			</script>";
		
	}
}

?>

<div id="table">

		<table>
			<div class="botones">
					 <input type="text" id="buscar" name="buscar"  placeholder="Buscar"> 
					<button id="Agregar" > Incluir </button> 
			</div>

				<thead>
					<tr>
						<th> Departamento </th>
						<th> Acciones </th>
					</tr>
				</thead>
				<tbody id="tablaDep"></tbody>
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
				let contenido = document.getElementById("tablaDep");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "busqueda/buscar_dep.php";
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
		<h2 class="bloque">Agregar Departamento </h2>
		<div>
			<label for="dep"> Departamento: </label> 
			<input type="text" id="dep" name="Dep"  required >
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