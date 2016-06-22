<?php

$uri = filter_input(INPUT_GET, "__req");

if(!$uri) {
	$uri = \SpringDvs\Config::$net['geosub'].'.'.\SpringDvs\Config::$net['geotop'];
}

$qs = $_GET;
unset($qs['__meta']);unset($qs['__req']);unset($qs['_']);unset($qs['callback']);

$qdata = count($qs) ? "?".http_build_query($qs) : "";

$request = "spring://".$uri.$qdata;


$nodes = GatewayHandler::resolveUri($request);

if($nodes === false){ return ['status' => 'error', 'uri' => $request,'reason' => 'Resolution failed']; }

try {
	$message = \SpringDvs\Message::fromStr("service $request");
} catch(\Exception $e) {
	return ['status' => 'error', 'uri' => $request];
}

$response = GatewayHandler::outboundFirstResponse($message, $nodes);

if($response === null) {
	return ['status' => 'error', 'uri' => $request, 'reason' => 'Request failed'];
}

if($response->content()->type() != \SpringDvs\ContentResponse::ServiceText) {
	return ['status' => 'error', 'uri' => $request, 'reason' => 'Invalid service response type'];
}

$v = explode('|', $response->content()->content()->get());
$serviced = array();
foreach($v as $k => $val) {
	if($val == "") continue;
	$serviced[$k] = json_decode($val);
}

$dec = [
	"status" => 'ok',
	"content" => $serviced
	];

return $dec;

