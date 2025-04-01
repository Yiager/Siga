<?php

include('../login.php');
include('../fpdf/pdf_mc_table.php');

$conexion = conectar();

//*************************Identificador de la Salida*************************
$id = $_GET['id'];


$pdf = new PDF_MC_table();

$pdf->AddPage();
$pdf->SetFont('Arial', '', '12');

$pdf->Image('../img/logoNuevo.jpg' , 5 ,5, 50);
$pdf->Ln(20);
$pdf->Cell(90);
$pdf->SetFont('Arial', 'B', '14');
$pdf->Cell(20, 10, 'Salida Nro '.$id, 0, 1, 'C', 0);
$pdf->Ln(10);
$pdf->SetWidths(Array(20,25,40,35,35,25));
//Centrar contenido de celda
$pdf->SetAligns(Array('C','C','C','C','C','C'));
$pdf->SetLineHeight(5);


	$pdf->Cell(5);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(20, 10, 'Codigo', 1, 0, 'C', 0);
	$pdf->Cell(25, 10, 'Fecha', 1, 0, 'C', 0);
	$pdf->Cell(40, 10, 'Departamento', 1, 0, 'C', 0);
	$pdf->Cell(35, 10, 'Retiro', 1, 0, 'C', 0);
	$pdf->Cell(35, 10, 'Almacen', 1, 0, 'C', 0);
	$pdf->Cell(25, 10, 'Estado', 1, 1, 'C', 0);

	$pdf->Cell(5);

//*****************************Datos de la tabla salidas*****************************

	$ConexionSQL = $conexion->prepare("SELECT 
						salidas.*, 
						almacen.*, 
						departamentos.* 
					FROM 
						salidas 
						INNER JOIN almacen ON salidas.Deposito = almacen.id 
						INNER JOIN departamentos ON salidas.Dep = departamentos.id 
					WHERE 
						SalidaID = ? ");
	$ConexionSQL->bind_param("i", $id);
	$ConexionSQL->execute();
	$datos = $ConexionSQL->get_result();
	$ConexionSQL->close();

	$pdf->SetFont('Arial', '', '12');
while($traer = $datos->fetch_assoc()){

	$pdf->Row(Array(

		$traer['SalidaID'],
		$traer['Fecha'],
		$traer['departamento'],
		$traer['Persona'],
		$traer['Almacen'],
		$traer['Estado']

	));
}

$pdf->Ln(5);
$pdf->Cell(90);
$pdf->SetFont('Arial', 'B', '14');
$pdf->Cell(20, 10, 'Detalles', 0, 1, 'C', 0);

$pdf->SetWidths(Array(20,20,25,11,20,20,29));
//Centrar contenido de celda
$pdf->SetAligns(Array('C','C','C','C','C','C','C'));
$pdf->SetLineHeight(5);

	$pdf->Cell(20);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(20, 10, 'Detalle', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Salida', 1, 0, 'C', 0);
	$pdf->Cell(25, 10, 'Producto', 1, 0, 'C', 0);
	$pdf->Cell(11, 10, 'Cant', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Precio', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Total', 1, 0, 'C', 0);
	$pdf->Cell(29, 10, 'Observacion', 1, 1, 'C', 0);


//*******************************Traer los totales de las salidas con los montos de precios*******************************

$ConexionSQLDetalle = $conexion->prepare("SELECT 
							salidadetalle.*, 
							productos.*, 
							almacen.*,
							(
								SELECT 
									SUM(salidadetalle.Cantidad) 
								FROM 
									salidadetalle 
								WHERE  
									salidadetalle.CodigoSalida = ?
							) AS TotalSal, 

							(
								SELECT 
									SUM(salidadetalle.Precio) 
								FROM 
									salidadetalle 
								WHERE  
									salidadetalle.CodigoSalida = ?
							) AS TotalPrecio, 

							(
								SELECT 
									SUM(salidadetalle.Total) 
								FROM 
									salidadetalle 
								WHERE  
									salidadetalle.CodigoSalida = ?
							) AS TotalMonto 
						FROM 
							salidadetalle 
							INNER JOIN productos ON salidadetalle.Objeto = productos.idProducto 
							INNER JOIN almacen ON salidadetalle.Deposit = almacen.id 
						WHERE 
							salidadetalle.CodigoSalida = ? ");
$ConexionSQLDetalle->bind_param("iiii", $id, $id, $id, $id);
$ConexionSQLDetalle->execute();
$datos = $ConexionSQLDetalle->get_result();
$ConexionSQLDetalle->close();

	$pdf->SetFont('Arial', '', '12');
while($traerD = $datos->fetch_assoc()){
	$totalSal = $traerD['TotalSal'];
	$TotalPrecio = $traerD['TotalPrecio'];
	$TotalMonto = $traerD['TotalMonto'];

	$pdf->Cell(20);
	$pdf->Row(Array(

		$traerD['IdSalida'],
		$traerD['CodigoSalida'],
		$traerD['Articulo'],
		$traerD['Cantidad'],
		$traerD['Precio'],
		$traerD['Total'],
		$traerD['Observacion']

	));
}

	$pdf->Ln(5);
	$pdf->Cell(20);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(65, 10, 'Total', 1, 0, 'C', 0);
	$pdf->Cell(11, 10, $totalSal, 1, 0, 'C', 0);
	$pdf->Cell(20, 10, $TotalPrecio, 1, 0, 'C', 0);
	$pdf->Cell(20, 10, $TotalMonto, 1, 1, 'C', 0);

$pdf->Output();

?>