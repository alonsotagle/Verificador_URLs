<!DOCTYPE html>
<html>
<head>
	<title>Analizar Links</title>
	<meta charset="utf-8"/>
	<meta name="description" content="Analizador Links"/>
	<meta name="author" content="Alonso Tagle"/>
	<link rel="stylesheet" type="text/css" href="estilos.css">
</head>
<body>
	<ul id="menu">
		<li>Inicio</li>
		<li>Ordenar</li>
		<li>Ocultar</li>
		<li>Eliminar seleccionados</li>
		<li>Actualizar seleccionados</li>
		<li>Desactivar seleccionados</li>
	</ul>

	<div>No. de Links analizados:</div>

	<table id="tabla">
		<thead>
			<tr>
				<th>Título</th>
				<th>Entidad</th>
				<th>Estatus del recurso</th>
				<th>URL</th>
				<th>Estatus del link</th>
			</tr>
		</thead>
		<tbody>
<?php

require "conexion_bd.php";

//Función que verifica la url
function verifica($url){
	//Valido si es una URL valida
	if (!filter_var($url, FILTER_VALIDATE_URL)){
		return 200;
	}

	//Inicializo CURL
	$curl = curl_init();
	//Se manda la página
    curl_setopt($curl, CURLOPT_URL, $url);
    //Tiempo para conexión
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
	//Peticiones HEAD
	curl_setopt($curl, CURLOPT_HEADER, true);
	//Sólo petición HEAD
	curl_setopt($curl, CURLOPT_NOBODY, true);
	//No imprime e navegador
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	//Incluir páginas https
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	//Obtengo una respuesta
	$response = curl_exec($curl);
	//print_r($response);

	//Mostrar información de la respuesta
	$info = curl_getinfo($curl);

	if ($info['http_code'] > 302) {
		return $info['http_code'];
		//echo $info['http_code'] .'<a href="'. $info['url'] .'"> ' . $info['url'] .'</a><br>';
	}
	
	// Comprobar si occurió algún error
	if(curl_errno($curl)){
		//Imprimir el número del error
	    return curl_errno($curl);
	}

	//Cerrar petición
	curl_close($curl);

	if ($response) return 100;

	return 6;
}
	
//Se ingresa directo
if ( $recursos ) {
	//Iterar por el arreglo de resultados
	for ($i = 0; $i < sizeof($recursos); $i++) {
		$url = $recursos[$i]["url"];
		//Reiniciar tiempo de espera
		set_time_limit(0);
		//Mandamos a la función
		$resultado = verifica($url);
		//Si está activo
		if ($resultado == 100 || $resultado == 60) {
			//echo "$key <a href='$value'>$value</a> <div style='color: green'>Existe</div><br>";
		//Si está desactivo
		}else{
			//Imprimimos id y página
			/*echo "<pre>";
			var_dump($recursos[$i]);
			echo "</pre>";*/
			echo "<tr>
					<td>".$recursos[$i]['titulo']."</td>
					<td>".$recursos[$i]['entidad']."</td>
					<td>".$recursos[$i]['estatus']."</td>
					<td>".$recursos[$i]['url']."</td>
					<td>Error</td>
				</tr>";
			
			//Revisamos por el tipo de error
			// switch ($resultado) {
			// 	case 200:
			// 		echo $resultado . " Direccion no v&aacute;lida";
			// 		break;

			// 	case 6:
			// 		echo $resultado . " Fallo al resolver Host";
			// 		break;

			// 	case 28:
			// 		echo $resultado . " Tiempo de respuesta excedido";
			// 		break;

			// 	case 7:
			// 		echo $resultado . " Fallo al conectar al Host";
			// 		break;
				
			// 	default:
			// 		echo $resultado;
			// 		break;
			// }
			// echo "</div><br>";
		}
	}
}
?>
		</tbody>
	</table>
<?php ?>
</body>
</html>