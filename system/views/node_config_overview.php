<section>
	
<div class="pure-u-3-5">
	<div class="white-container raised">
		<h3>Updates</h3>
		
		<div id="update-list" data-bind="foreach: updates">
			<div class="update-block">
				<div class="update-type" style="font-weight: bold;" data-bind="text:mtype"></div>
				<div data-bind="foreach: modules">
					<div class="update-item">
						<code style="font-weight: bold; margin-right: 15px;" data-bind="text: module"></code> version  
						<span data-bind="text: details.version"></span>
					</div>
				</div>
			</div>
		</div>
		<button id="updater" onclick="ManOverviewController.performUpdate()">Update</button>
		<div id="update-msg"></div>
	</div>
	
	<div id="setup-file-warning" style="display: hidden;" class="white-container raised">
		<h3>Setup Files</h3>
		
		<strong>Setup files exist on this server -- please click button to remove them.</strong>
		<button onclick="remSetupFiles()" style="margin-top: 10px;">Remove Setup Files</button>
	</div>

</div> <!--  u-3-5 -->

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
				<td data-bind="text: service"></td>
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
				<td><strong>Primary Address:</strong></td>
				<td data-bind="text: primary_addr"></td>
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
	<div class="white-container raised">
		<table class="pure-table pure-table-bordered">
		<tbody>
			<tr>
				<td><strong>Remote API Token</strong></td>
				<td><?php echo \SpringDvs\Config::$sys['api_token']; ?></td>
			</tr>
		</tbody>
		</table>
		<br>
		<button href="javascript:void(0);" onclick="location.href='/node/logout/';">Logout</button></div>	
	</div>
</div><!--  u-2-5 -->
	
</section>

