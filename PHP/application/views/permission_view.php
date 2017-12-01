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
				<div id="accordion" class="pb-2" role="tablist">
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
									<div flex>
										<h5>
											{{ group.name }}
										</h5>
										<p>
											{{ group.description }}
										</p>
									</div>
									<div>
										<div layout="row" layout-align="center center" class="buttonGroup">
											<md-button class="left {{permissionTable[model.table_name][group.id]==undefined||permissionTable[model.table_name][group.id]==0?'group-focused':''}}" ng-click="changed(model.table_name, group.id, 0)" value="0">View</md-button>
											<md-button class="middle {{permissionTable[model.table_name][group.id]==1?'group-focused':''}}" ng-click="changed(model.table_name, group.id, 1)" value="1">Add</md-button>
											<md-button class="middle {{permissionTable[model.table_name][group.id]==2?'group-focused':''}}" ng-click="changed(model.table_name, group.id, 2)" value="2">Change</md-button>
											<md-button class="right {{permissionTable[model.table_name][group.id]==3?'group-focused':''}}" ng-click="changed(model.table_name, group.id, 3)" value="3">Alter</md-button>
										</div>
									</div>
								</md-list-item>
							</md-list>
						</div>
					</div>
					
				</div>
				<em ng-if="models.length != 0">Open a table in the database to register them here.</em>
				<div ng-if="models.length == 0" class="text-center">
					<i class="fa fa-folder-open fa-3x pb-3" style="color: {{ colors('primary-400'); }}"></i>
					<h3>No tables loaded</h3>
					<p>You will need to open a table in the database for them to register here.</p>
					<md-button class="md-raised md-primary" ng-href="<?=base_url()?>database">
			          <i class="fa fa-database fa-fw fa-lg"> </i> Go to the database
			        </md-button>
				</div>
			</md-card-content>
		</md-card>
	</md-content>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/permissions.js' ?>"></script>