<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

?>
<script type="text/javascript">
	app.constant('uploadURL', "<?= isset($url)?$url:current_url() ?>");
</script>
<div ng-controller="importer" ng-init="loadTables()">
	<md-content layout-padding>
		<md-card class="p-0">
			<md-card-title>
				<span class="md-headline" flex>CSV Importer</span>
			</md-card-title>
			<md-card-content>
				<div>
					<form enctype=multipart/form-data ng-attr-action="<?= isset($url)?$url:current_url() ?>/importcsv" 
					ng-upload="completed(content)" 
					ng-upload-loading="loading()">
						<input type="hidden" name="{{ csrf }}" value="{{ csrfHash }}">
						<div>
							<span>
								Step 1. Add fields that are not existing in the table then come back here.
							</span>
						</div>
						<div>
							<label>
								Step 2. Select Table to Import to.
							</label>
							<md-select ng-model="tableselect" ng-change="loadHeaders()">
								<md-option ng-repeat="map in tableMaps" ng-value="map">
									{{ map.mdl_name }}
								</md-option>
							</md-select>
						</div>
						<div>
							<div>
								Step 3. Set your header to the following keys
							</div>
							<table class="table table-striped p-4">
								<tr>
									<th>Set this</th>
									<th ng-repeat="(key, item) in headers" ng-if="key!=tableselect.table_pk">
										{{ item.title }}
									</th>
								</tr>
								<tr>
									<th>To this</th>
									<td ng-repeat="(key, item) in headers" ng-if="key!=tableselect.table_pk">
										{{ key }}
									</td>
								</tr>
							</table>
						</div>
						
						<div>
							<div>
								<label>
									Step 4. Upload the CSV file.
								</label>
							</div>
							<input type="hidden" name="table" value="{{ tableselect.table_name }}"></input>
							<input type="file" name="csv_db"></input>
							<input type="submit" value="Upload" 
							ng-disabled="$isUploading"></input>
						</div>
					</form>
				</div>
			</md-card-content>
		</md-card>
	</md-content>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/importer.js' ?>"></script>