<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

?>
<script type="text/javascript">
	app.constant('permissionsURL', '<?= isset($url)?$url:current_url() ?>');
</script>
<div ng-controller="permissions" ng-init="loadPermissions()">
	<md-content layout-padding>
		<md-card class="p-0">
			<md-card-title>
				<span class="md-headline" flex>Permissions</span>
			</md-card-title>
			<md-card-content>
				<div id="accordion" role="tablist">
					<div class="card" ng-repeat="model in models">
						<div class="card-header" role="tab" id="headingOne">
							<h5 class="mb-0">
								<a data-toggle="collapse" href="#collapse-{{ model.table_name }}" aria-expanded="true">
									{{ model.mdl_name }}
								</a>
							</h5>
						</div>

						<div id="collapse-{{ model.table_name }}" class="collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion">
							<md-list flex>
								<md-list-item ng-repeat="group in groups">
									<h5 flex>{{ group.description }}</h5>
									<md-select ng-model="permissionTable[model.table_name][group.id]" ng-change="changed(model.table_name, group.id)">
										<md-option value="0">View</md-option>
										<md-option value="1">Add</md-option>
										<md-option value="2">Change</md-option>
										<md-option value="3">Alter</md-option>
									</md-select>
								</md-list-item>
							</md-list>
						</div>
					</div>
				</div>
			</md-card-content>
		</md-card>
	</md-content>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/permissions.js' ?>"></script>