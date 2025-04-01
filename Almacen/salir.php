<?php

//Funcion de cierre de sesion
session_start();
session_destroy();

//Redireccionamiento a la pagina inicial
header("location: index.php");

?>