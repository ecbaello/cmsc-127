<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

?>

<script type="text/javascript">
	app.constant('tablemanagerURL', '<?= isset($url)?$url:current_url() ?>');
</script>
<div ng-controller="tablemanager" ng-init="loadTables()">
	<md-content layout-padding>
		<md-card class="p-0">
			<md-card-title>
				<span class="md-headline" flex>Table Manager</span>
			</md-card-title>
			<md-card-content>
				<table class="table table-striped">
					<tr>
						<th>
							Custom Table Name
						</th>
						<th>
							Custom Table Mapping
						</th>
						<th>
							Custom Table Prefix
						</th>
						<th>
							Custom Table Type
						</th>
						<th>
							
						</th>
					</tr>
					<tr ng-repeat="table in customTables">
						<td>
							{{ table.mdl_name }}
						</td>
						<td>
							{{ table.table_name }}
						</td>
						<td>
							{{ table.table_prefix }}
						</td>
						<td>
							{{ table.is_array==1?'Array':'Basic' }}
						</td>
						<th>
							<md-button class="md-raised md-warn" ng-click="delete(table.table_name)">
								<i class="fa fa-trash"></i>
								Delete
							</md-button>
						</th>
					</tr>
				</table>
			</md-card-content>
		</md-card>
	</md-content>

	<md-content layout-padding>
		<md-card class="p-0">
			<md-toolbar>
				<h5 class="md-toolbar-tools">New Custom Table</h5>
			</md-toolbar>
			<form name="newcustomtableform" class="p-4" ng-submit="new()">
				<md-input-container>
					<input placeholder="Title" name="title" ng-model="newItem.title" required>
					<div ng-messages style="color: {{ colors('accent') }}">
						Once set, the custom table's title can not be renamed. You will need to backup the table and reimport it again.
					</div>
				</md-input-container>
				
				<md-input-container>
					<input placeholder="Shortname" ng-model="newItem.prefix">
					<div ng-messages style="color: {{ colors('primary') }}">
						Example for 'Petty Cash Fund' write 'pcf'.
					</div>
				</md-input-container>
				<md-input-container>
					<p>Table Type</p>
					<md-radio-group ng-model="newItem.array" required>
				      <md-radio-button ng-value="0" class="md-primary">Basic</md-radio-button>
				      <md-radio-button ng-value="1">Array</md-radio-button>
				    </md-radio-group>
				</md-input-container>
				<md-button ng-disabled="!newcustomtableform.$valid" class="md-raised md-primary" type="submit">
					Add New
				</md-button>


			</form>
		</md-card>
	</md-content>

	
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/tablemanager.js' ?>"></script>