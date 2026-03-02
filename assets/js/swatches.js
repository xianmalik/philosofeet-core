(function ($) {
  'use strict';

  $(document).ready(function () {
    var $rows = $('.philosofeet-attr-row');
    if (!$rows.length) return;

    var $form = $('.variations_form');
    if (!$form.length) return;

    $rows.each(function () {
      var $row      = $(this);
      var $swatches = $row.find('.philosofeet-swatch');
      var $selected = $row.find('.philosofeet-swatch-selected');

      $swatches.on('click', function () {
        var $swatch  = $(this);
        var attrKey  = $swatch.data('attr-key');
        var attrVal  = $swatch.data('attr-value');
        var label    = $swatch.data('label') || attrVal;

        var $select = $form.find('select[name="' + attrKey + '"]');
        if ($select.length) {
          $select.val(attrVal).trigger('change');
        }

        $swatches.removeClass('active');
        $swatch.addClass('active');
        $selected.text(label);
      });
    });

    // Sync active state when WooCommerce resolves a full variation
    $form.on('found_variation', function (event, variation) {
      $rows.each(function () {
        var $row      = $(this);
        var attrKey   = $row.data('attr-key');
        var attrVal   = variation.attributes ? variation.attributes[attrKey] : null;

        $row.find('.philosofeet-swatch').each(function () {
          $(this).toggleClass('active', $(this).data('attr-value') === attrVal);
        });
      });
    });

    // Clear active states on form reset
    $form.on('reset_data', function () {
      $rows.find('.philosofeet-swatch').removeClass('active');
      $rows.find('.philosofeet-swatch-selected').text('—');
    });
  });
})(jQuery);
