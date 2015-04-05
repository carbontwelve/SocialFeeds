(function($) {

    $(document).ready(function ($) {
        $(document).on('change', '.additionalFieldSelector', function(){
            $('.additionalFields').hide();
            $('.additionalFields.' + $(this).val()).show();
        });
    });

})(jQuery);