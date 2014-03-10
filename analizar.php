<?php

require "conexion_bd.php";

echo "<link rel='stylesheet' type='text/css' href='estilos.css'>";
echo "No. de Links analizados: ";
echo $elementos[0];

//Función que verifica la URL
function multi($links){

	//Para manejar llamados curl paralelos
	$curl_multiple = curl_multi_init();

	//Iterar arreglo configurando curl's individuales
	foreach ($links as $i => $url) {
		//Iniciando curl individual
		$curl_individual[$i] = curl_init($url);

		//Configurando curl individual
		//Se manda la página
	    curl_setopt($curl_individual[$i], CURLOPT_URL, $url);
	    //Tiempo para conexión
		curl_setopt($curl_individual[$i], CURLOPT_CONNECTTIMEOUT, 30);
		//Para regresar el HEAD
		curl_setopt($curl_individual[$i], CURLOPT_HEADER, true);
		//Sólo petición HEAD sin BODY
		curl_setopt($curl_individual[$i], CURLOPT_NOBODY, true);
		//Para que curl_exec() pueda ser asignado a una variable
		curl_setopt($curl_individual[$i], CURLOPT_RETURNTRANSFER, true);
		//Incluir páginas https
		curl_setopt($curl_individual[$i], CURLOPT_SSL_VERIFYPEER, false);

		curl_multi_add_handle($curl_multiple,$curl_individual[$i]);
	}

	do{
		set_time_limit(0);
		$estado = curl_multi_exec($curl_multiple,$active);
	} while($active);

	foreach ($links as $i => $url) {
			$info = curl_getinfo($curl_individual[$i]);
			$res[$i] = $info["http_code"];
			curl_multi_remove_handle($curl_multiple,$curl_individual[$i]);
			curl_close($curl_individual[$i]);
	}
	curl_multi_close($curl_multiple);

	return $res;

}

echo "<table class='tables'>
			<thead>
				<tr>
					<th>T&iacute;tulo</th>
					<th>Entidad</th>
					<th>Estatus del recurso</th>
					<th>URL</th>
					<th>Estatus del link</th>
				</tr>
			</thead>
			<tbody>";


foreach ($links as $key => $value) {
	$respuesta = multi($links[$key]);
	imprime($links, $respuesta, $recursos);
}


echo "</tbody>
	</table>";


function imprime($links, $respuesta, $recursos){

	foreach ($links as $i => $valor) {
		if ($respuesta[$i] < 200 || $respuesta[$i] > 302) {
			switch ($respuesta[$i]) {
				case 0:
			 		$error = "Tiempo excedido";
			 		break;

			 	case 404:
			 		$error = "No encontrado";
			 		break;

			 	case 403:
			 		$error = "Solicitud prohibida";
			 		break;

			 	case 500:
			 		$error = "Error interno del servidor";
			 		break;
					
			 	default:
			 		$error = "";
			 		echo $respuesta[$i]."<br>";
			 		break;
			 }

			if ($recursos[$i]['estado'] == 1) {
				$recursos[$i]['estado'] = "Activo";
			}else{
				$recursos[$i]['estado'] = "Oculto";
			}
			echo "<tr>
						<td>".$recursos[$i]['titulo']."</td>
						<td>".$recursos[$i]['entidad']."</td>
						<td>".$recursos[$i]['estado']."</td>
						<td><a href='".$recursos[$i]['url']."'>".$recursos[$i]['url']."</td>
						<td>".$error."</td>
					</tr>";
		}
	}

}


?>