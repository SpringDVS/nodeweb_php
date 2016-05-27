<?php

interface IVersionHandler {
	public function info($pkg);
	public function needsUpdate($strLocalVersion, $strRemoteVersion);
}