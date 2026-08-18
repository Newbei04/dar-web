(function($) {
  "use strict";

  function getOpts($el) {
    var opts = {};
    var $modal = $el.closest('.modal');
    if ($modal.length) {
      opts.dropdownParent = $modal;
    }
    return opts;
  }

  $(document).ready(function() {

    $(".single-select, .default-select, .select2").each(function() {
      if ($(this).hasClass('select2-hidden-accessible')) return;
      $(this).select2(getOpts($(this)));
    });

    window.initSelect2 = function(context) {
      var $scope = context ? $(context) : $(document);
      $scope.find(".single-select, .default-select, .select2").each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) return;
        $(this).select2(getOpts($(this)));
      });
    };

    window.reinitSelect2 = function(el) {
      var $el = $(el);
      $el.select2('destroy').select2(getOpts($el));
    };

  });

})(jQuery);
