<table>
	<tr valign="top">
		<th class="metabox_label_column"><label for="tf_featured">Featured</th>
		<td><input type="checkbox" name="tf_featured" id="tf_featured" value="1"<?php if(@get_post_meta($post->ID, 'tf_featured', true)) echo ' checked="checked"'; ?>></td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column"><label for="tf_lightbox">Display in lightbox</th>
		<td><input type="checkbox" name="tf_lightbox" id="tf_lightbox" value="1"<?php if(@get_post_meta($post->ID, 'tf_lightbox', true)) echo ' checked="checked"'; ?>></td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column"><label for="tf_start_date">Start Date/Time</label></th>
		<td><input class="showDateTime startdate" type="text" name="tf_start_date" id="tf_start_date" value="<?php echo @get_post_meta($post->ID, 'tf_start_date', true); ?>"></td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column"><label for="tf_end_date">End Date/Time</label></th>
		<td><input class="showDateTime enddate" type="text" name="tf_end_date" id="tf_end_date" value="<?php echo @get_post_meta($post->ID, 'tf_end_date', true); ?>"></td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column"><label for="tf_display_date">Display Date/Time</label></th>
		<?php 
		$disabled = @get_post_meta($post->ID, 'tf_display_date', true) == "" ? ' disabled="disabled"' : "";
		?>
		<td><input class="showDateTime displaydate" type="text" name="tf_display_date" id="tf_display_date" value="<?php echo @get_post_meta($post->ID, 'tf_display_date', true); ?>"<?php echo $disabled;?>></td>
	</tr>
	<tr valign="top">
		<th class="metabox_label_column"><label for="tf_complete">Complete</th>
		<td><input type="checkbox" name="tf_complete" id="tf_complete" value="1"<?php if(@get_post_meta($post->ID, 'tf_complete', true)) echo ' checked="checked"'; ?>></td>
	</tr>
</table>