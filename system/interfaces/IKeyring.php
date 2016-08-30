<?php

interface IKeyring {
	public function getNodePublicKey();
	public function getNodePrivateKey();
	public function setNodePublicKey($key);
	public function setNodePrivateKey($key);
	public function getPublicKeyring();
}