<?php
error_reporting (0);
include "../conect.php";
$archivo = fopen("../dbf/tblicen02.csv", "r");
$conexion = conectar();
$input = isset($_POST['licencias']) ? $_POST['licencias'] : 1;
$q = $conexion->real_escape_string($input);
$fechaActual = date("d/m/Y");

$valores = [];

//Recorremos el archivo CSV
while (($datos = fgetcsv($archivo)) == true){
	$num = count($datos); 
	for ($i = 0; $i < 1; $i++){
		$datos[$i];

		if ($datos[$i] == $q) {

				//Recorremos la fila donde haya valores que concidan
			for ($columna=0; $columna < 15 ; $columna++) { 
				$valores[] = $datos[$columna];
			}

		}else{	
		
		}
	}
}

$form = "
					<input type='hidden' name='tipoP' value='Patente'>
					<input type='hidden' name='CodigoP' value='$valores[0]'>
					<p>
						<label for='NombresP'> Nombres y Apeliidos: </label>
						<input type='text' name='nombresP' id='NombresP' required value='$valores[5]' readonly>
					</p>
					<p>
						<label for='DireccionP'> Direccion: </label>
						<input type='text' name='direccionP' id='DireccionP' required value='$valores[6]' readonly> 
					</p>
					<p>
						<label for='usoP'> Uso: </label>
						<select id='usoP' name='UsoP' required>
							<option disabled selected hidden> Seleccione: </option>
							<option value='Comercial'> Comercial </option>
							<option value='Industrial'> Industrial </option>
							<option value='Residencial'> Residencial </option>
						</select>
					</p>
					<p>
						<label for='periodoP'> Periodo: </label>
						<select id='periodoP' name='PeriodoP' required>
							<option disabled selected hidden> Seleccione: </option>
							<option value='Mensual'> Mensual </option>
						</select>
					</p>
					<input type='hidden' name='fechaActualP' value='$fechaActual' >
					<p>
						<label for='DesdeP'> Desde: </label>
						<input type='date' name='desdeP' id='DesdeP' onchange=fechasP() required > 
					</p>
					<p>
						<label for='HastaP'> Hasta: </label>
						<input type='text' name='hastaP' id='HastaP' required >
					</p>

					<script>

						function fechasP(){

							let desdeP = new Date(document.getElementById('DesdeP').value);
							let hastaP = document.getElementById('HastaP');

							let diaP = desdeP.getDate() + 1;
							let mesP = desdeP.getMonth() + 2;
							let añoP = desdeP.getFullYear();

							if(mesP == 2 && diaP == 31){
								diaP -= 3;
							}

							if(mesP % 2 == 0 && diaP == 31 && mesP != 2){
								diaP -= 1
							}else{
								diaP += 1
							}

							hastaP.value = diaP+'-'+mesP+'-'+añoP;

						}

					</script>
					
					<input type='submit' name='AgregarP' value='Agregar' class='bloque'>";

echo $form;