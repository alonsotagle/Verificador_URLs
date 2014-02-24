<?php 

//Accesos a mysql
$conexion = mysql_connect("localhost","root","");  
    
//Error de conexión
if (!$conexion) {  
    die('No pudo conectarse: ' . mysql_error() );  
}  

//echo 'Conectado satisfactoriamente <br><br>'; 
  
//Escoger la BD
$base_datos = "tultest";
$bd_seleccionada = mysql_select_db($base_datos,$conexion);

//Error al escoger la BD
if (!$bd_seleccionada) {
    die ('No se puede usar la base de datos '. $base_datos . ':' . mysql_error());  
} 

//Sentencia SQL
$sql = "SELECT * FROM  `recurso` ";
$resultado = mysql_query($sql);

//Error en la consulta
if (!$resultado) {
    echo "No se pudo ejecutar con exito la consulta ($sql) en la BD: " . mysql_error();
    exit;
}

// echo "<pre>";
// var_dump(mysql_fetch_assoc($resultado));
// echo "</pre>";

//Arreglo con los recursos
$recursos = array();
$contador = 0;

//Iteramos para llenar arreglo con valores deseados
while ($recurso = mysql_fetch_assoc($resultado)) {
	$recursos[$contador]["id"] = $recurso["rec_id"];
	$recursos[$contador]["titulo"] = $recurso["rec_titulo_largo"];
	$recursos[$contador]["entidad"] = $recurso["rec_urlEntidad_rs"];
	$recursos[$contador]["estado"] = $recurso["rec_estatus"];
	$recursos[$contador]["url"] = $recurso["rec_url"];
	$contador++;
}

// echo "<pre>";
// var_dump($recursos);
// echo "</pre>";

//Liberamos memoria de la consulta
mysql_free_result($resultado);
  
//Cerramos la conexión
mysql_close($conexion);