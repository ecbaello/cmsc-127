<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$show_category = isset($show_category)&&$show_category;

?>
<script type="text/javascript">
	app.constant('selectorUrl', '<?= isset($url)?$url:current_url() ?>');
	app.constant('selectorSelection', '<?= isset($current_tbl)?$current_tbl:"-1" ?>');
</script>
<div ng-controller="selector">
	<?php if ($show_category): ?>
	<md-content layout-padding>
	  <md-card class="p-0">
	    <div class="p-3">
			<h5 class="md-headline" flex><?= isset($title)?'<strong>'.$title.'</strong>&nbsp;':'' ?>Categories</h5>
		</div>
	    <div>
			<md-list>
				<md-list-item class="md-2-line align-items-center secondary-button-padding" ng-repeat-start="(key, item) in options" ng-click="redirect(item.link)">
					<h4>{{item.title}}</h4>
					<?php if ($permission >= PERMISSION_ALTER): ?>
					<md-button class="md-secondary md-raised md-warn" ng-click="removeCategory(item.title)">Delete</md-button>
					<?php endif ?>
				</md-list-item>
				<md-divider ng-repeat-end></md-divider>
			</md-list>
			<?php if ($permission >= PERMISSION_ALTER): ?>
			<form class="p-3" ng-submit="addCategory(category)">
				<h5 class="mb-4">
					New Category
				</h5>
				<md-input-container>
					<input placeholder="Title" ng-model="category" required>

				</md-input-container>
				<md-button class="md-raised md-primary" type="submit">
					Add Category
				</md-button>
			</form>
			<?php endif ?>
		</div>
	  </md-card>
	</md-content>
	<?php else: ?>
	<md-content layout-padding>
	  <md-card class="p-0">
	  	<md-content class="p-3">
	  		<h5 class="md-headline" flex><?= isset($title)?'<strong>'.$title.'</strong>&nbsp;':'' ?>Categories</h5>
	  		<span>
		  		<md-button class="md-2-line md-raised md-primary" ng-href="{{ menuUrl }}">Menu</md-button>
		  		|
				<md-button class="md-2-line md-accent" ng-repeat="(key, item) in options" class="secondary-button-padding" ng-click="redirect(item.link)" ng-disabled="selection==key">
					{{item.title}}
				</md-button>
	  		</span>
	   	</md-content>
	  </md-card>
	</md-content>
	<?php endif ?>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/selector.js' ?>"></script>