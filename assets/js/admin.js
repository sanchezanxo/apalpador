/**
 * Apalpador Admin Scripts
 *
 * @package Apalpador
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		var mediaFrame;
		var $container = $('.apalpador-image-selector');
		var $previewImg = $container.find('.apalpador-preview-img');
		var $customUpload = $container.find('.apalpador-custom-upload');
		var $customPlaceholder = $container.find('.apalpador-custom-placeholder');

		// Preset gallery selection.
		$('.apalpador-preset-radio').on('change', function() {
			var $item = $(this).closest('.apalpador-preset-item');
			var value = $(this).val();

			// Update selected state.
			$('.apalpador-preset-item').removeClass('selected');
			$item.addClass('selected');

			// Show/hide custom upload section.
			if (value === 'custom') {
				$customUpload.show();
				// Update preview with custom image if available.
				var customImgSrc = $customPlaceholder.find('img').attr('src');
				if (customImgSrc) {
					$previewImg.attr('src', customImgSrc);
				}
			} else {
				$customUpload.hide();
				// Update preview with preset image.
				var presetImgSrc = $item.find('img').attr('src');
				if (presetImgSrc) {
					$previewImg.attr('src', presetImgSrc);
				}
			}
		});

		// Custom image selector from media library.
		$('.apalpador-select-image').on('click', function(e) {
			e.preventDefault();

			var $input = $container.find('.apalpador-image-id');
			var $removeBtn = $container.find('.apalpador-remove-image');

			// Create media frame if it doesn't exist.
			if (!mediaFrame) {
				mediaFrame = wp.media({
					title: apalpadorAdmin.mediaTitle,
					button: {
						text: apalpadorAdmin.mediaButton
					},
					multiple: false
				});

				mediaFrame.on('select', function() {
					var attachment = mediaFrame.state().get('selection').first().toJSON();
					var imageUrl = attachment.sizes && attachment.sizes.medium
						? attachment.sizes.medium.url
						: attachment.url;

					// Update hidden input.
					$input.val(attachment.id);
					$removeBtn.show();

					// Update custom placeholder (use jQuery methods to avoid XSS).
					var $img = $('<img>').attr('src', imageUrl).attr('alt', '');
					$customPlaceholder
						.addClass('has-image')
						.empty()
						.append($img);

					// Update preview.
					$previewImg.attr('src', imageUrl);
				});
			}

			mediaFrame.open();
		});

		// Remove custom image.
		$('.apalpador-remove-image').on('click', function(e) {
			e.preventDefault();

			var $input = $container.find('.apalpador-image-id');

			$input.val('0');
			$(this).hide();

			// Reset custom placeholder.
			$customPlaceholder
				.removeClass('has-image')
				.html('<span class="dashicons dashicons-plus-alt2"></span>');

			// Reset preview to default or first preset.
			var $defaultPreset = $('.apalpador-preset-item').not('.apalpador-preset-custom').first();
			var defaultSrc = $defaultPreset.find('img').attr('src');
			if (defaultSrc) {
				$previewImg.attr('src', defaultSrc);
			}
		});

		// Size selector - show/hide custom input.
		$('.apalpador-size-select').on('change', function() {
			var $customSize = $(this).closest('td').find('.apalpador-custom-size');
			if ($(this).val() === 'custom') {
				$customSize.show();
			} else {
				$customSize.hide();
			}
		});

		// Snow toggle - show/hide density.
		$('.apalpador-snow-toggle').on('change', function() {
			var $density = $(this).closest('td').find('.apalpador-snow-density');
			if ($(this).is(':checked')) {
				$density.show();
			} else {
				$density.hide();
			}
		});

		// Star toggle - show/hide frequency.
		$('.apalpador-star-toggle').on('change', function() {
			var $frequency = $(this).closest('td').find('.apalpador-star-frequency');
			if ($(this).is(':checked')) {
				$frequency.show();
			} else {
				$frequency.hide();
			}
		});

		// Bubble toggle - show/hide options.
		$('.apalpador-bubble-toggle').on('change', function() {
			var $options = $(this).closest('td').find('.apalpador-bubble-options');
			if ($(this).is(':checked')) {
				$options.show();
			} else {
				$options.hide();
			}
		});
	});

})(jQuery);
