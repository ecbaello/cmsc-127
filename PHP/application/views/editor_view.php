<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
?>
<script type="text/javascript">
	app.constant('editorUrl', '<?= isset($url)?$url:current_url() ?>');
	app.constant('editInitData', '<?= json_encode($item, JSON_NUMERIC_CHECK) ?>');
</script>
<div ng-controller="editor">
	<md-content layout-padding>
		<md-card class="p-0">
		    <div class="p-3">
		    	<h2 class="md-headline font-weight-bold" flex>Edit Item</h2>
				<form ng-cloak name="editform" ng-submit="update()">
					<div layout-padding>
						<div ng-repeat="(key, item) in headers" class="md-block">
							<div ng-if="!item.read_only">
								<?php echo $CI->load->view('input_switcher', 
								[
									'swtch' => 'item.type',
									'model' => 'editItem[key]',
									'label' => '{{item.title}}',
									'required' => 'item.required'
								]
							, true); ?>
							</div>
							<div ng-if="item.read_only">
								<md-input-container>
									<label>{{item.title}}</label>
									<div class="mt-4 md-input">
										{{
											<?= $CI->load->view('item_formatter', 
											[ 
												'item_type' => "item.type",
												'item_value' => 'editItem[key]'
											]
											, true); ?>
										}}
									</div>
								</md-input-container>
							</div>
						</div>
					</div>

					<md-button ng-disabled="!editform.$valid" type="submit" class="btn-confirm md-raised md-primary"><i class="fa fa-save"></i> Save</md-button>

					<md-button class="md-raised md-warn" ng-click="delete()"><i class="fa fa-trash"></i> Delete</md-button>
				</form>
				
			</div>
		</md-card>
	</md-content>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/editor.js' ?>"></script>