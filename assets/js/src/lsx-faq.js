jQuery(document).ready(function( ) {
    jQuery(document).on('facetwp-loaded', function() {

        jQuery('.faq li .question').on( 'click', function() {
            jQuery(this).find('.plus-minus-toggle').toggleClass('collapsed');
            jQuery(this).parents( 'li' ).toggleClass('active');
        });
    });
});