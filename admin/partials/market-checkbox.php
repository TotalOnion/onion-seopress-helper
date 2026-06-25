<label>
	<input
		type="checkbox"
		name="<?php echo $field_name; ?>'"
		value="1"
		<?php if ( $market['is_hidden'] ) : ?>
			disabled
		<?php endif; ?>
		<?php if ( $is_suppressed ) : ?>
			checked="checked"
		<?php endif; ?>
	/>
	<?php echo $market['name']; ?>
	<?php if ( $market['is_hidden'] ) : ?>
		(<?php _e( 'hidden in WPML', 'PR SEOPress Helper' ); ?>)
	<?php endif; ?>
</label>
<br />
