<section>
	
<div class="pure-u-3-5">
	
	<div class="white-container raised">
		<h3>Tasks</h3>
		<button id="action-status-update">Bring Online</button>
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
		</tbody>
		</table>
	</div>
		
	</div>
</div>
	
</section>

