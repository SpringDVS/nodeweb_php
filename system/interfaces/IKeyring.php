<?php

interface IKeyring {
	public function getNodePublicKey();
	public function getNodePrivateKey();
	public function setNodePublicKey();
	public function setNodePrivateKey();
	public function getPublicKeyring();
}