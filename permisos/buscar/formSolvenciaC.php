<?php
error_reporting (0);
include "../conect.php";
$archivo = fopen("../dbf/tbpropie.csv", "r");
$conexion = conectar();
$input = isset($_POST['inmuebles']) ? $_POST['inmuebles'] : null;
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
					<input type='hidden' name='tipoC' value='Catastro'>
					<input type='hidden' name='CodigoC' value='$valores[0]'>
					<p>
						<label for='NombresC'> Nombres y Apeliidos: </label>
						<input type='text' name='nombresC' id='NombresC' required value='$valores[2]' readonly>
					</p>
					<p>
						<label for='DireccionC'> Direccion: </label>
						<input type='text' name='direccionC' id='DireccionC' required value='$valores[7]' readonly> 
					</p>
					<p>
						<label for='usoC'> Uso: </label>
						<select id='usoC' name='UsoC' required>
							<option disabled selected hidden> Seleccione: </option>
							<option value='Comercial'> Comercial </option>
							<option value='Industrial'> Industrial </option>
							<option value='Residencial'> Residencial </option>
						</select>
					</p>
					<p>
						<label for='periodoC'> Periodo: </label>
						<select id='periodoC' name='PeriodoC' required >
							<option disabled selected hidden> Seleccione: </option>
							<option value='Anual'> Anual </option>
							<option value='Trimestral'> Trimestral </option>
							<option value='Mensual'> Mensual </option>
						</select>
					</p>
					<input type='hidden' name='fechaActualC' value='$fechaActual' >
					<p>
						<label for='DesdeC'> Desde: </label>
						<input type='date' name='desdeC' id='DesdeC'  required onchange=periodos()> 
					</p>
					<p>
						<label for='HastaC'> Hasta: </label>
						<input type='text' name='hastaC' id='HastaC' required >
					</p>

					<script>

						function periodos(){

							let periodo = document.getElementById('periodoC').value;
							let desde = new Date(document.getElementById('DesdeC').value);
							let hasta = document.getElementById('HastaC');

							let dia = desde.getDate() + 1;
							let mes = desde.getMonth() + 2;
							let año = desde.getFullYear();

							switch(periodo){

								case 'Anual':

									let añoA = desde.getFullYear();
									hasta.value = 31+'-'+12+'-'+añoA;
									break;

								case 'Trimestral':
									let diaT = desde.getDate() + 1;
									let mesT = desde.getMonth() + 4;
									let añoT = desde.getFullYear();

									if(mesT == 2 && diaT == 31){
										diaT -= 3;
									}

									if(mesT % 2 == 0 && diaT == 31 && mesT != 2){
										diaT -= 1
									}else{
										diaT += 1
									}

									hasta.value = diaT+'-'+mesT+'-'+añoT;
									break;

								case 'Mensual':
									let dia = desde.getDate() + 1;
									let mes = desde.getMonth() + 2;
									let año = desde.getFullYear();

									if(mes == 2 && dia == 31){
										dia -= 3;
									}

									if(mes % 2 == 0 && dia == 31 && mes != 2){
										dia -= 1
									}else{
										dia += 1
									}

									hasta.value = dia+'-'+mes+'-'+año;
									break;
							}
						}

					</script>
					
					<input type='submit' name='AgregarC' value='Agregar' class='bloque'>";

echo $form;

?>