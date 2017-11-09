<div ng-controller="expBuilder">
	<md-chips ng-model="expression" class="chip-hide-removable">
		<md-chip-template>
			<span ng-switch="$chip.type">
				<span ng-switch-when="field">
					{{ $chip.title }} <em>(field)</em>
				</span>
				<span ng-switch-when="operand">
					{{ $chip.value }}
				</span>
			</span>

		</md-chip-template>
		<span md-chip-remove>
		</span>
		<input type="hidden" disabled>
	</md-chips>
	<div>
		<md-switch ng-model="newIsField">
			Field
		</md-switch>
		<md-input-container ng-if="!newIsField">
			<input type="text" ng-model="newItem.value">
		</md-input-container>
		<md-select ng-model="newItem.key" ng-if="newIsField">
			<md-option value="key">
				Hello
			</md-option>
		</md-select>
	</div>
</div>