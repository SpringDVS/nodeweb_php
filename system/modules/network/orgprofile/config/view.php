<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */
if(!defined('NODE_ADMIN')) die("Platform Error");
?>

<div class="pure-u-3-5">
	<div class="white-container raised">
		<h3>Organisation Mini-Profile</h3>
		This is your organisation's profile that is broadcast to the network
		<div>
		<table class="pure-table pure-table-horizontal" style="margin-top: 10px;">
			<tr>
				<td><strong>Name:</strong></td>
				<td data-bind="text: name"></td>
			</tr>
			
			<tr>
				<td><strong>Website:</strong></td>
				<td data-bind="text: website">Unknown</td>
			</tr>

			<tr>
				<td><strong>tags:</strong></td>
				<td data-bind="text: tags">Unknown</td>
			</tr>
		</table>
		
		</div>
	</div>
</div>

<div class="pure-u-2-5">
	<div class="white-container raised">
		<h3>Update Profile</h3>
		<form class="pure-form pure-form-stacked">
			<fieldset>
				<label for="bf-name">Organisation Name</label>
				<input id="bf-name" type="text"  data-bind="value: name" style="width: 100%;">
				
				<label for="bf-website">Web Address</label>
				<input id="bf-website" type="text" data-bind="value: website"  style="width: 100%;">
				
				<label for="bf-tags">Tags</label>
				<input id="bf-tags" type="text" data-bind="value: tags" style="width: 100%;">

			</fieldset>
		</form>
		<button onclick="prForm.send()">Update</button>
	</div>
</div>