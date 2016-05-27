<?php



$uri = filter_input(INPUT_GET, "__req");

if(!$uri) {
	$uri = \SpringDvs\Config::$net['geosub'].'.'.\SpringDvs\Config::$net['geotop'];
}

$qs = $_GET;
unset($qs['__meta']);unset($qs['__req']);unset($qs['_']);unset($qs['callback']);

$qdata = count($qs) ? "?".http_build_query($qs) : "";

$request = "spring://".$uri."/bulletin/".$qdata;

$packet = SpringDvs\DvspPacket::ofType(\SpringDvs\DvspMsgType::gsn_resolution, $request);

$status = SpringDvs\HttpService::sendPacket($packet, \SpringDvs\Config::$net['master'],  SpringDvs\hostres_from_config());

if(!$status){ return ['status' => 'error', 'uri' => $request]; }
if($status->header()->type == SpringDvs\DvspMsgType::gsn_response){ return ['status' => 'error', 'uri' => $request]; }

$frame = $status->contentAs(\SpringDvs\FrameNodeInfo::contentType());
$node = \SpringDvs\Node::fromNoderegAddr($frame->name, $frame->address);
$rqpacket = SpringDvs\DvspPacket::ofType(\SpringDvs\DvspMsgType::gsn_request, $request);


$in = SpringDvs\HttpService::sendPacket($rqpacket, \SpringDvs\Node::addressToString($node->address()),  $node->hostname()."/spring/");
if(!$in){ return ['status' => 'error', 'uri' => $request]; }
if($in->header()->type == SpringDvs\DvspMsgType::gsn_response){ return ['status' => 'error', 'uri' => $request]; }
if($in->header()->type != SpringDvs\DvspMsgType::gsn_response_high){ return ['status' => 'error', 'uri' => $request]; }

$v = explode('|', $in->content());
$nodes = array();
foreach($v as $k => $val) {
	if($val == "") continue;
	$nodes[$k] = json_decode($val);
}

$dec = [
	"status" => 'ok',
	"content" => $nodes
	];

return $dec;

