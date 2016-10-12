<?php
use Flintstone\Flintstone;


/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */
if(!defined('SPRING_IF')) return [];

$options['dir'] = \SpringDvs\Config::$sys['store_live'];
$keyring = new Flintstone('keyring', $options);
$queries = [];

parse_str($url->query(), $queries);


if(isset($rpath[0]) && $rpath[0] == "pull") {
	if(!isset($rpath[1])) {
		return array('key' => 'error');
	}
	$keyid = $rpath[1];
	
	$cert = $keyring->get($keyid);
	if(!$cert)
		return array('key' => 'error');
	
	return array('key' => $cert['public']);
}

if(isset($rpath[0]) && $rpath[0] == "pullreq") {

	if(!isset($queries['from'])) {
		return array('result' => 'error');
	}
	$nodes = GatewayHandler::resolveUri("spring://{$queries['from']}");
	if($nodes === false) {
		return array('result' => 'error');
	}
	$node = $nodes[0];

	$keys = $keyring->get('this');

	if(!isset($keys['public']) || !isset($keys['public']['keyid'])) {
		return array('result' => 'error');
	}

	$req = 'service spring://'.$node->spring().'/cert/pull/'.$keys['public']['keyid'];
	$msg = \SpringDvs\Message::fromStr($req);
	$respmsg = \SpringDvs\HttpService::send($msg, $node->address(), $node->host());
	if($respmsg->cmd() != SpringDvs\CmdType::Response) {
		return array('result' => 'error');
	}
	
	if($respmsg->content()->code() != \SpringDvs\ProtocolResponse::Ok) {
		return array('result' => 'error');
	}
	
	if($respmsg->content()->type() != \SpringDvs\ContentResponse::ServiceText) {
		return array('result' => 'error');
	}
	$resparr = json_decode($respmsg->content()->content()->get(), true);
	$response = array_pop($resparr);
	if($response['key'] == "error") {
		return array('result' => 'error');
	}
	
	define('CERT_REQ', true);
	$handler = new KeyringHandler();
	$handler->importPublicKey($response['key']);
	
	return array('result' => 'ok');	
}

if(isset($rpath[0]) && $rpath[0] == "key") {
	$keys = $keyring->get('this');
	if(!$keys || !isset($keys['public'])) {
		return array('key' => 'error');
	}
	
	$cert = $keys['public'];
	
	return array('key' => $cert['armor']);
}

if(empty($queries)) {
	$keys = $keyring->get('this');
	if(!$keys || !isset($keys['public'])) {
		return array('certificate' => 'error');
	}
	
	$cert = $keys['public'];
	
	return array('certificate' => $cert);
}

if(isset($queries['keyonly'])) {
	$keys = $keyring->get('this');
	if(!$keys || !isset($keys['public'])) {
		return array('key' => 'error');
	}
	
	$cert = $keys['public'];
	
	return array('key' => $cert['armor']);
}

