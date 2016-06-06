<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */
if(!defined('NODE_ADMIN')) die("Platform Error");
?>

<div class="pure-u-3-5">
	<div class="white-container raised">
		<h3>Bulletin Service</h3>
		These bulletins are broadcast to the public network
		<table style="width: 100%; margin-top:10px;" class="pure-table">
			<tr>

			</tr>
			<tbody data-bind="foreach: bulletins">
				<tr>
					<td style="border-bottom: 1px solid #D3D5D5;">
					<span style="font-weight: bold;" data-bind="text: title"></span>
					<span style="float: right;"><a href="#" data-bind="click: $root.removeBulletin">remove</a></span>
					</td>
				</tr>
				<tr>
					<td data-bind="text: content" style="background-color: white;"></td>
				</tr>
				
				<tr>
					<td style="font-size: 12px; border-top: 1px solid #D3D5D5;border-bottom: 1px solid #D3D5D5;">
						<span data-bind="text: tags"></span>
						<span style="font-size: 12px; float: right;" colspan="2" data-bind="text: uid"></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="pure-u-2-5">
	<div class="white-container raised">
		<h3>New Bulletin</h3>
		<form class="pure-form pure-form-stacked">
			<fieldset>
				<label for="bf-title">Title</label>
				<input id="bf-title" type="text" style="width:100%;">
				<label for="bf-content">Content</label>
				<textarea id="bf-content" style="width: 100%;"></textarea>
				<label for="bf-tags">Tags</label>
				<input id="bf-tags" type="text" style="width:60%;" placeholder="tag1,tag2,tag3">
				<small>Separate with commas</small>

			</fieldset>
		</form>
		<button onclick="buForm.send()">Add</button>
	</div>
</div>
