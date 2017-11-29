<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<script type="text/javascript" src="<?= base_url().'js/controllers/bookmarks.js' ?>"></script>
<md-content layout-padding>
	<md-card>
		<md-card-title>
			<span class="md-headline">Bookmarks</span>
		</md-card-title>
		<md-card-content>
			<div ng-controller="bookmarks" ng-init="loadBookmarks()">
				<md-list>
					<md-list-item class="md-2-line align-items-center" ng-repeat-start="item in bookmarks" class="secondary-button-padding">
						<p class="d-inline">{{item.title}}</p>
						<md-button class="md-secondary" ng-href="{{item.link}}">Go</md-button>
						<md-button class="md-secondary md-raised md-warn" ng-click="removeBookmark(item.title)">Delete</md-button>
					</md-list-item>
					<md-divider ng-repeat-end></md-divider>
				</md-list>
				<div ng-if="bookmarks.length == 0">
					Bookmark a page by clicking on the bookmark icon above.
				</div>
			</div>
		</md-card-content>
	</md-card>
</md-content>