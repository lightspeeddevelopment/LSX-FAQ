jQuery(document).ready(function( ) {
    jQuery(document).on('facetwp-loaded', function() {

        jQuery('.faq li .question .plus-minus-toggle').on( 'click', function() {
            jQuery(this).toggleClass('collapsed');
            jQuery(this).parents( 'li' ).toggleClass('active');
        });
    });
});