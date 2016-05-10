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
		<table style="width: 100%; margin-top:10px;" class="pure-table pure-table-bordered pure-table-horizontal">
			<tr>
				<th style="text-align: left; width: 15%;">Type</th>
				<th style="text-align: left;">Title</th>
				<th>&nbsp;</th>
			</tr>
			<tbody data-bind="foreach: bulletins">
				<tr>
					<td data-bind="text: type"></td>
					<td data-bind="text: title"></td>
					<td style="text-align: right;"><a href="#" data-bind="click: $root.removeBulletin">remove</a></td>
				</tr>
				<tr>
					<td style="font-size: 12px;" colspan="3" data-bind="text: tags"></td>
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
				<input id="bf-title" type="text">
				<label for="bf-type">Type</label>
				<select id="bf-type">
					<option value="event">Event</option>
					<option value="notice">Notice</option>
					<option value="service">Service</option>
				</select>
				<label for="bf-tags">Tags</label>
				<input id="bf-tags" type="text">
			</fieldset>
		</form>
		<button onclick="buForm.send()">Add</button>
	</div>
</div>