<?php
header('Content-type: application/json');

$result = array();
if(isset($_GET['twitchUser']) && isset($_GET['shirtName'])){
	$result = getNumberSold($_GET['twitchUser'], $_GET['shirtName']);
}
else{
	$result = array('error' => true,'message' => 'The name parameters twitchUser and shirtName were  not set');
}

echo json_encode($result);

function getNumberSold($twitchUser,$shirtName){
	$r = array();
	$url = "http://xnf09ccdo4-3.algolia.io/1/indexes/searchable_campaigns_production/query";//The url to contact the api 
	$apiId = "XNF09CCDO4";//El id de la api
	$apiKey = "5cf4b4f788d542e9e1661cb977480f0dcb5acfdae52786e3bf9593ba8da3ddd4";//La llave de la api
	$data = '{"params":"query='.$twitchUser.'&hitsPerPage=12&restrictSearchableAttributes=%5B%22name%22%2C%22url%22%2C%22description%22%2C%22tag_names%22%2C%22front_text%22%2C%22back_text%22%2C%22id%22%5D&attributesToRetrieve=%5B%22name%22%2C%22url%22%2C%22tippingpoint%22%2C%22amount_ordered%22%2C%22primary_pic_url%22%2C%22secondary_pic_url%22%2C%22endcost%22%2C%22enddate%22%5D&numericFilters=%5B%22id%3E924650%22%2C%22publicly_searchable%3D1%22%5D&page=0",
			"apiKey":"'.$apiKey.'",
			"appID":"'.$apiId.'"}
			';//Los datos que se le piden a la api
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	//echo curl_exec($ch);
	$result = curl_exec($ch);
	curl_close ($ch);
	$jsonArray = json_decode($result,true);//se decodifica el resultado en json
	if(isset($jsonArray['hits'])){
		if(count($jsonArray['hits']) > 0){//Se checa si regreso resultados
			foreach($jsonArray['hits'] as $hit){//Se checan los resultados para buscar el que concuerde con nuestra busqueda
				if($hit['url'] == $twitchUser){
					$r = array("twitchName" => $twitchUser,"shirtName" => $shirtName,"objective" => $hit["tippingpoint"], "ordered" => $hit["amount_ordered"]);
					break;
				}
				else{
					$r = array('error' => true,'message' => 'The shirt associated with the user doesnt exist');
				}
			}
		}
		else{
			$r = array('error' => true,'message' => 'The shirt associated with the user doesnt exist');
		}
	}
	else{
		$r = array('error' => true,'message' => 'Something went wrong with the Api');
	}
	return $r;
}