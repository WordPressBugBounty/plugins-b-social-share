<?php
if (!defined('ABSPATH')) exit;
$bssb_id = wp_unique_id('bssbSocialShare-');
$bssb_socials = is_array($attributes['socials'] ?? null) ? $attributes['socials'] : [];
$bssb_has_fa_icon = false;
if (is_array($bssb_socials)) {
	foreach ($bssb_socials as $bssb_social) {
		if (!empty($bssb_social['icon']['class'])) {
			$bssb_has_fa_icon = true;
			break;
		}
	}
}

if ($bssb_has_fa_icon) {
	wp_enqueue_style('bssb-font-awesome');
}

$bssb_frontend_keys = [
	'align',
	'socials',
	'alignment',
	'theme',
	'background',
	'size',
	'gap',
	'padding',
	'margin',
	'border',
	'shadow',
	'direction'
];
$bssb_allowed_attributes = array_intersect_key( $attributes, array_flip( $bssb_frontend_keys ) );
?>

<div <?php echo get_block_wrapper_attributes(); ?> id="<?php echo esc_attr( $bssb_id ); ?>" data-attributes="<?php echo esc_attr( wp_json_encode( $bssb_allowed_attributes ) ); ?>">
	<div class="bssbStyle"></div>

	<ul class="bssbSocialShare">
		<?php 
		$bssb_allowed_networks = [
			'facebook', 'twitter', 'linkedin', 'pinterest', 'blogger', 'wordpress', 'copy',
			'tumblr', 'reddit', 'digg', 'livejournal', 'evernote', 'flipboard', 'mix',
			'pocket', 'buffer', 'instapaper', 'meneame', 'baidu', 'weibo', 'xing', 'renren',
			'vkontakte', 'odnoklassniki', 'moimir', 'surfingbird', 'liveinternet',
			'sms', 'skype', 'telegram', 'viber', 'whatsapp', 'wechat', 'line'
		];
		foreach ($bssb_socials as $bssb_index => $bssb_social) {

			$bssb_network = $bssb_social['network'] ?? '';
			if (!in_array($bssb_network, $bssb_allowed_networks, true)) {
				continue;
			}
			$bssb_isUpIcon = $bssb_social['isUpIcon'] ?? false;
			$bssb_upIcon = $bssb_social['upIcon'] ?? [];
			$bssb_icon = $bssb_social['icon'] ?? [];

			$bssb_upIconUrl = $bssb_upIcon['url'] ?? '';
			$bssb_upIconAlt = $bssb_upIcon['alt'] ?? '';
			$bssb_iconClass = $bssb_icon['class'] ?? '';

			// classes
			$bssb_li_classes = [
				'icon',
				'icon-' . $bssb_index,
			];

			?>

			<li class="<?php echo esc_attr(implode(' ', $bssb_li_classes)); ?>" data-social="<?php echo esc_attr($bssb_network); ?>" 	<?php if ($bssb_network === 'copy'): ?> data-copy="true" <?php endif; ?>>
				<?php if ( $bssb_isUpIcon && $bssb_upIconUrl ) : ?>
					<img src="<?php echo esc_url( $bssb_upIconUrl ); ?>" alt="<?php echo esc_attr( $bssb_upIconAlt ); ?>" />
				<?php elseif ( $bssb_iconClass ) : ?>
					<i class="<?php echo esc_attr( $bssb_iconClass ); ?>"></i>
				<?php endif; ?>

			</li>

		<?php } ?>

	</ul>
</div>