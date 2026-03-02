(function ($) {
  'use strict';

  $(document).ready(function () {
    var $swatches = $('.philosofeet-swatch');
    var $selected = $('.philosofeet-swatch-selected');
    if (!$swatches.length) return;

    var $form = $('.variations_form');
    if (!$form.length) return;

    $swatches.on('click', function () {
      var $swatch    = $(this);
      var attributes = $swatch.data('attributes') || {};
      var label      = $swatch.data('label') || $swatch.text().trim();

      if (typeof attributes === 'string') {
        try { attributes = JSON.parse(attributes); } catch (e) { return; }
      }

      $.each(attributes, function (attrKey, attrVal) {
        var $select = $form.find('select[name="' + attrKey + '"]');
        if ($select.length && attrVal) {
          $select.val(attrVal).trigger('change');
        }
      });

      $swatches.removeClass('active');
      $swatch.addClass('active');
      $selected.text(label);
    });

    $form.on('found_variation', function (event, variation) {
      $swatches.each(function () {
        $(this).toggleClass('active', parseInt($(this).data('variation-id')) === variation.variation_id);
      });
    });

    $form.on('reset_data', function () {
      $swatches.removeClass('active');
      $selected.text('—');
    });
  });
})(jQuery);
