<?php 
include("head.php");
?>
<!DOCTYPE html>
<html>
	<body>
		<nav id="Menu" > 
			<ul>
				<li> <a> Configuracion </a>
					<ul>
		    		<!--************************** Lista de tablas ****************-->
					     <li> <a href="/Almacen/Proveedores.php "> Proveedores </a> </li>
						<?php
							if($user == 'Administrador'){			   
							    echo "<li><a href='/Almacen/usuarios.php' > Usuarios  </a> </li>";
							}else{
							     echo "<li style = 'display:none;'><a  href='/Almacen/usuarios.php' > Usuarios </a> </li>";
							}
						?> 
					    <li><a  href="/Almacen/Productos.php " > Productos </a> </li>
					    <li><a  href="/Almacen/unidad_medida.php " > Unidades de medidas </a> </li>
					    <li><a  href="/Almacen/Departamento.php " > Departamentos </a> </li>
					    <li><a  href="/Almacen/Almacen.php " > Almacen </a> </li>
					</ul>		   
				</li>
				<!--********************************** Entradas *********************************************-->
				<li> <a href="/Almacen/Entradas.php " > Entradas </a> </li>
				<!--********************************** Salidas **********************************************-->
				<li> <a href="/Almacen/Salida.php" > Salidas </a> </li>
				<!--********************************** Inventario *******************************************-->
				<li> <a href="/Almacen/Inventario.php" > Inventario  </a> </li>
				<!--********************************** Salida del sistema ***********************************-->
				<li> <a href="/Almacen/salir.php" > Salir  </a>  </li>
			</ul> 
		</nav>
	</body>
</html>