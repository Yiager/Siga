<?php
include("conect.php");

$conexion = conectar();

session_start();
session_destroy();

header("Location: index.php");

?>