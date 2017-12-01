<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<script type="text/javascript">
	app.constant('recognizerUrl', '<?= isset($url)?$url:current_url() ?>');
</script>
<div ng-controller="selector">
	<md-content layout-padding>
	  <md-card class="p-0">
	    <div class="p-3">
			<h5 class="md-headline" flex><i class="fa fa-search"> &nbsp;</i>Recognize foreign tables in the database</h5>
		</div>
	    <div>
			<md-list>
				<md-list-item ng-repeat-start="item in data" class="md-2-line align-items-center secondary-button-padding">
					<h5>{{ item }}</h5>
					<md-button class="md-secondary md-raised md-primary" ng-click="recognize(item)">Recognize</md-button>
				</md-list-item>
				<md-divider ng-repeat-end></md-divider>

			</md-list>
			<md-content class="p-3">
				<em>This only works for indexable tables in your database.</em>
			</md-content>
	  </md-card>
	</md-content>
	<div style="visibility: hidden">
		<div class="md-dialog-container" id="recognizerDialog">
			<md-dialog>
				<md-toolbar>
					<div class="md-toolbar-tools">
						<h2>Recognize Foreign Table</h2>
						<span flex></span>
						<md-button class="md-icon-button" ng-click="closeDialog()">
							<i class="fa fa-times fa-lg"></i>
						</md-button>
					</div>
				</md-toolbar>
				<form ng-cloak name="recognizerForm" ng-submit="recognizeTable(recognizing, title)">
				<md-dialog-content>
						<div class="pt-3" layout-padding>
							<md-input-container class="w-100">
								<input placeholder="Title" type="text" ng-model="title" required>
							</md-input-container>
						</div>
				</md-dialog-content>
				<md-dialog-actions layout="row">
					<md-button type="submit" ng-disabled="!recognizerForm.$valid" class="md-primary md-raised">
						Recognize {{ recognizing }}
					</md-button>
				</md-dialog-actions>
				</form>
			</md-dialog>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/recognizer.js' ?>"></script>