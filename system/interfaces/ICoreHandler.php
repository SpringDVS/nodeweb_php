<?php

interface ICoreHandler {
	public function getInfo();
	public function upgrade($archive);
}
