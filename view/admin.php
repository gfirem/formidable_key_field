<div id="tab-tables" class="lkg-card card pressthis">
	<h2><?= FormidableKeyFieldManager::t( "Generate Key into a form" ) ?></h2>
	
	<p><?= FormidableKeyFieldManager::t( "Select a form target and how many key you want to generate." ) ?></p>
	
	<form enctype="multipart/form-data" method="post" name="lkg_commands" id="lkg_commands">
		<table class="form-table">
			<tbody>
			<tr class="form-field form-required">
				<td>
					<select name="form_target" id="form_target">
						<option value=""></option>
						<?php echo "$form_options"; ?>
					</select>
				
				</td>
				<td>
					<input type="number" name="cycle_target" id="cycle_target">
				</td>
			</tr>
			
			<tr class="form-field">
				<td colspan="2" style="text-align:left">
					<input type="submit" style="text-align:left" value="<?= FormidableKeyFieldManager::t( "Generate" ) ?>" class="button button-primary" id="lkg_data_submit" name="lkg_data_submit">
				</td>
			</tr>
			</tbody>
		</table>
		<input type="hidden" id="lkg_action" name="lkg_action" value="generate_keys">
	</form>

</div>
