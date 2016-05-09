<section>
	
<div class="pure-u-3-5">
	
	<div class="white-container raised">
		<h3>Network Services</h3>
		Various services that your node exposes on the Spring network.
		<table class="pure-table pure-table-striped" style="margin-top: 10px;">
			<tbody data-bind="foreach: nwservices">
				<tr>
					<td style="font-weight: bold;" data-bind="text: title"></td>
					<td data-bind="text: description"></td>
					<td> <a style="text-decoration: none;" data-bind="attr: {'href':'/node/service/network/'+module+'/'}">Config</a></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="white-container raised">
		<h3>Gateway Services</h3>
		Providing public facing services through the node.
		<table class="pure-table pure-table-striped" style="margin-top: 10px;">
			<tbody data-bind="foreach: gwservices">
				<tr>
					<td style="font-weight: bold;" data-bind="text: title"></td>
					<td data-bind="text: description"></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="pure-u-2-5">
	<div class="pure-1-1">
	
	<div class="white-container raised">
		<h3>Node Details</h3>
		<table class="pure-table pure-table-bordered">
		<tbody>
			<tr>
				<td><strong>Springname:</strong></td>
				<td data-bind="text: springname"></td>
			</tr>
			<tr>
				<td><strong>Hostname:</strong></td>
				<td data-bind="text: hostname"></td>
			</tr>
			<tr>
				<td><strong>Service:</strong></td>
				<td data-bind="text: service"<>/td>
			</tr>
			<tr>
				<td><strong>Address:</strong></td>
				<td data-bind="text: address"></td>
			</tr>
		</tbody>
		</table>
	</div>
		
	<div class="white-container raised">
		<h3>Network Details</h3>
		<table class="pure-table pure-table-bordered">
		<tbody>
			<tr>
				<td><strong>GSN:</strong></td>
				<td data-bind="text: geosub"></td>
			</tr>
			<tr>
				<td><strong>Master Address:</strong></td>
				<td data-bind="text: master_addr"></td>
			</tr>
			<tr>
				<td><strong>Status:</strong></td>
				<td data-bind="text: status" id="bind-status">offline</td>
			</tr>
			<tr>
				<td><strong>Registered:</strong></td>
				<td data-bind="text: register" id="bind-register">Unknown</td>
			</tr>
		</tbody>
		</table>
		
		<button id="action-status-update" style="margin-top: 10px;">Bring Online</button>
		<button id="action-register" style="margin-top: 10px;">Register</button>
		<div id="action-error" style="color: #e9322d; font-weight: bold; margin-top:10px;"></div>
	</div>
	<div class="white-container raised"><button onclick="location.href='/node/logout/';">Logout</button></div>	
	</div>
</div>
	
</section>

