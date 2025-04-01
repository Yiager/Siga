<?php
include('../login.php');
include('../fpdf/pdf_mc_table.php');

$conexion = conectar();

$Fecha = $_GET['fechaRes'];
$Almacen = $_GET['almacenRes'];
$FechaReporte = date("j/n/Y");

//***********************************Seleccionar los totales de las entradas y las salidas***********************************

$ConexionSQL = $conexion->prepare("SELECT 
					entradadetalle.*, 
					entradas.*, 
					almacen.*, 
					IFNULL(
						(
							SELECT 
								SUM(Precio) 
							FROM 
								entradadetalle 
								INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo 
							WHERE 
								entradas.Almacen = ?
								AND entradas.Estado = 'Procesada' 
								AND DATE_FORMAT(entradas.FechaFactura, '%Y-%m') <  DATE_FORMAT(?, '%Y- %m')
						), 0) AS ExisAnt, 

					IFNULL(
						(
							SELECT 
								SUM(Precio) 
							FROM 
								entradadetalle 
								INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo 
							WHERE 
								entradas.Almacen = ?
								AND entradas.Estado = 'Procesada' 
								AND DATE_FORMAT(entradas.FechaFactura, '%Y-%m') = DATE_FORMAT(?, '%Y-%m') 
						), 0) AS Entrada, 

					IFNULL(
						(
							SELECT 
								SUM(Precio) 
							FROM 
								salidadetalle 
								INNER JOIN salidas ON salidadetalle.CodigoSalida = salidas.SalidaID 
							WHERE 
								salidadetalle.Deposit = ?
								AND salidas.Estado = 'Procesada' 
								AND DATE_FORMAT(salidas.Fecha, '%Y-%m') = DATE_FORMAT(?, '%Y-%m' )
						), 0) AS Salida 
				FROM 
					entradadetalle 
					INNER JOIN entradas ON entradadetalle.CodigoEntrada = entradas.Codigo 
					INNER JOIN almacen ON entradas.Almacen = almacen.id 
					INNER JOIN salidas ON salidas.Deposito = almacen.id 
				WHERE 
					entradas.Almacen = ?
					AND salidas.Deposito = ?
					AND entradas.Estado = 'Procesada' 
					AND salidas.Estado = 'Procesada' ");

$ConexionSQL->bind_param("isisisii", $Almacen, $Fecha, $Almacen, $Fecha, $Almacen, $Fecha, $Almacen, $Almacen);
$ConexionSQL->execute();
$datos = $ConexionSQL->get_result();
$ConexionSQL->close();

$pdf = new pdf_mc_table();
$pdf->AddPage();
$pdf->SetFont('Arial', '', '12');
$pdf->Image('../img/logoNuevo.jpg' , 10 ,5, 50);

	$pdf->SetWidths(Array(45,25,30,30,20,20,30));
	//Centrar contenido de celda
	$pdf->SetAligns(Array('C','C','C','C','C','C','C'));
	

while($traer = $datos->fetch_assoc()){

	$AlmacenRes = $traer['Almacen'];
	$ExistenciaAnterior = $traer['ExisAnt'];
	$Entradas = $traer['Entrada'];
	$Salidas = $traer['Salida'];
	$Existencia = $Entradas - $Salidas;
	
}
	
	$pdf->SetFont('Arial', '', '12');
	$pdf->Cell(110);
	$pdf->Cell(70, 10, 'Fecha de reporte: '.$FechaReporte, 1, 1, 'C', 0);
	$pdf->Ln(10);
	$pdf->Cell(60);
	$pdf->SetFont('Arial', 'B', '14');
	$pdf->Cell(70, 10, $AlmacenRes,0, 1, 'C', 0);
	$pdf->Cell(90);
	$pdf->SetFont('Arial', 'B', '12');
	$pdf->Cell(10, 10, 'Resumen inventario', 0, 1, 'C', 0);
	$pdf->Ln(5);
	
	$pdf->SetLineHeight(5);
	$pdf->Cell(5);
	$pdf->Cell(90, 10, 'Exis anterior', 1, 0, 'C', 0);
	$pdf->Cell(90, 10, $ExistenciaAnterior, 1, 1, 'C', 0);
	$pdf->Ln(5);
	$pdf->Cell(5);
	$pdf->Cell(90, 10, 'Entrada', 1, 0, 'C', 0);
	$pdf->Cell(90, 10, $Entradas, 1, 1, 'C', 0);
	$pdf->Ln(5);
	$pdf->Cell(5);
	$pdf->Cell(90, 10, 'Salida', 1, 0, 'C', 0);
	$pdf->Cell(90, 10, $Salidas, 1, 1, 'C', 0);
	$pdf->Ln(5);
	$pdf->Cell(5);
	$pdf->Cell(90, 10, 'Existencia', 1, 0, 'C', 0);
	$pdf->Cell(90, 10, $Existencia, 1, 1, 'C', 0);

$pdf->Output();


?>