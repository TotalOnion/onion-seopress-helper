<label for="<?php echo $field_id; ?>">
	<input
		type="checkbox"
		id="<?php echo $field_id; ?>"
		name="<?php echo $field_name; ?>"
		value="1"
		<?php if ( $field_enabled ) : ?>
			checked="checked"
		<?php endif; ?>
	/>
	<?php echo $label; ?>
</label>
<br />
