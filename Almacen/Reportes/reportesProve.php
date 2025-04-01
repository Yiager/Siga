<?php

include('../login.php');
include('../fpdf/pdf_mc_table.php');

$conexion = conectar();
$Fecha = date("j/n/Y");
$pdf = new PDF_MC_table();
$pdf->AddPage("L40");
$pdf->SetFont('Arial', '', '12');

$pdf->Image('../img/logoNuevo.jpg' , 5 ,5, 50);
$pdf->SetFont('Arial', 'B', '14');
$pdf->Cell(190);
$pdf->Cell(80, 10, 'Fecha de reporte: '.$Fecha, 1, 1, 'C', 0);
$pdf->Ln(20);
$pdf->Cell(-5);
$pdf->SetFont('Arial', 'B', '14');
$pdf->Cell(285, 10, 'Proveedores', 1, 1, 'C', 0);

$pdf->SetWidths(Array(40,40,60,30,35,35,45));
//Centrar contenido de celda
$pdf->SetAligns(Array('C','C','C','C','C','C','C'));
$pdf->SetLineHeight(5);

	$pdf->Cell(-5);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(40, 10, 'Empresa', 1, 0, 'C', 0);
	$pdf->Cell(40, 10, 'RIF', 1, 0, 'C', 0);
	$pdf->Cell(60, 10, 'E-mail', 1, 0, 'C', 0);
	$pdf->Cell(30, 10, 'Nro local', 1, 0, 'C', 0);
	$pdf->Cell(35, 10, 'Contacto', 1, 0, 'C', 0);
	$pdf->Cell(35, 10, 'Nro Contacto', 1, 0, 'C', 0);
	$pdf->Cell(45, 10, 'Servicios', 1, 1, 'C', 0);

//*******************Traer todos los datos de los proveedores de la tabla proveedores*******************	

$ConexionSQLProvee = $conexion->prepare("SELECT * FROM prove ");
$ConexionSQLProvee->execute();
$datos = $ConexionSQLProvee->get_result();
$ConexionSQLProvee->close();

$pdf->SetFont('Arial', '', '12');
while($traerD = $datos->fetch_assoc()){

	$pdf->Cell(-5);
	$pdf->Row(Array(

		$traerD['Empresa'],
		$traerD['Rif'],
		$traerD['Correo'],
		$traerD['TlfLocal'],
		$traerD['Contacto'],
		$traerD['Tlf'],
		$traerD['Servicio']

	));
}

$pdf->Output();

?>