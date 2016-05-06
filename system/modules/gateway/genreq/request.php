<?php
$request = filter_input(INPUT_POST, "req");
if(!$request) return "error";

$packet = SpringDvs\DvspPacket::ofType(\SpringDvs\DvspMsgType::gsn_resolution, $request);

$resp = SpringDvs\HttpService::sendPacket($packet, \SpringDvs\Config::$net['master'],  SpringDvs\hostres_from_config());
if(!$resp) return "error";
if($resp->header()->type == SpringDvs\DvspMsgType::gsn_response) {
	$frame = $resp->contentAs(SpringDvs\FrameResponse::contentType());
	
	switch($frame->code) {
		case \SpringDvs\DvspRcode::netspace_error : return "NetspaceError";
		case \SpringDvs\DvspRcode::network_error : return "NetworkError";
	} 
}

if($resp->header()->type != SpringDvs\DvspMsgType::gsn_response_node_info) {
	
	return "Unexpected result";
}

$frame = $resp->contentAs(\SpringDvs\FrameNodeInfo::contentType());
$node = \SpringDvs\Node::fromNoderegAddr($frame->name, $frame->address);
$rqpacket = SpringDvs\DvspPacket::ofType(\SpringDvs\DvspMsgType::gsn_request, $request);


$in = SpringDvs\HttpService::sendPacket($rqpacket, \SpringDvs\Node::addressToString($node->address()),  $node->hostname()."/spring/");
if($in->header()->type == SpringDvs\DvspMsgType::gsn_response) {
	$f = $in->contentAs(SpringDvs\FrameResponse::contentType());
	
	switch($f->code) {
		case \SpringDvs\DvspRcode::netspace_error : return "NetspaceError";
		case \SpringDvs\DvspRcode::network_error : return "NetworkError";
		case \SpringDvs\DvspRcode::malformed_content : return "MalformedContent";
	} 
}
//echo $in->header()->type . "\n";
if($in->header()->type != SpringDvs\DvspMsgType::gsn_response_high) {
	return "Unexpected result";
}
$dec = json_decode($in->content());

return $dec;