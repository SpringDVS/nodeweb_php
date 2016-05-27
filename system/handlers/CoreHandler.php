<?php

class CoreHandler extends ICoreHandler {
	public function getInfo() {
		if(!file_exists(__DIR__.'/../coreinfo.hp')) return;
		
		return include __DIR__.'/../coreinfo.hp';
	}

	public function upgrade($archive) {
	}
}
