<?php
$id = wp_unique_id('bssbSocialShare-');
extract($attributes);

$is_theme_three = isset($theme) && $theme === 'theme4';
$is_theme_four = isset($theme) && $theme === 'theme5';

if (isset($icon['class']) && !empty($icon['class'])) {
	wp_enqueue_style('font-awesome-7');
}

wp_enqueue_script('goodshare');
wp_set_script_translations(
	'bssb-social-share-view-script',
	'social-share',
	BSSB_DIR_PATH . 'languages'
);
?>

<div <?php echo get_block_wrapper_attributes(); ?> id="<?php echo esc_attr($id); ?>" data-attributes="<?php echo esc_attr(wp_json_encode($attributes)); ?>">
	<div class="bssbStyle"></div>

	<ul class="bssbSocialShare">
		<?php foreach ($socials as $index => $social) {

			$network = $social['network'] ?? '';
			$isUpIcon = $social['isUpIcon'] ?? false;
			$upIcon = $social['upIcon'] ?? [];
			$icon = $social['icon'] ?? [];

			$upIconUrl = $upIcon['url'] ?? '';
			$upIconAlt = $upIcon['alt'] ?? '';
			$iconClass = $icon['class'] ?? '';

			$upIconEl = $upIconUrl ? '<img src="' . esc_url($upIconUrl) . '" alt="' . esc_attr($upIconAlt) . '" />' : '';
			$iconEl = $iconClass ? '<i class="' . esc_attr($iconClass) . '"></i>' : '';

			$filterIconEl = $isUpIcon ? $upIconEl : $iconEl;
			// classes
			$li_classes = [
				'icon',
				'icon-' . $index,
			];

			if ($is_theme_three) {
				$li_classes[] = 'bss-animation';
			}

			$style_attr = '';
			if ($is_theme_three) {
				$style_attr = 'style="animation-delay:' . esc_attr($index * 0.3) . 's"';
			}
			?>

			<li class="<?php echo esc_attr(implode(' ', $li_classes)); ?>" data-social="<?php echo esc_attr($network); ?>" <?php echo $style_attr; ?> 	<?php if ($network === 'copy'): ?> data-copy="true" <?php endif; ?>>
				<?php echo wp_kses_post($filterIconEl); ?>
				<?php if ($is_theme_four): ?>
					<p><?php echo esc_attr($network); ?></p>
				<?php endif; ?>
			</li>

		<?php } ?>
		<?php if (!empty($isCounter)): ?>
			<h4 class="bss-counter-title">
				<?php echo esc_html($counterText); ?>:
				<span><?php echo esc_html($counterNumber); ?></span>
			</h4>
		<?php endif; ?>
	</ul>
</div>