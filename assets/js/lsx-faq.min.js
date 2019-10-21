jQuery(document).ready(function( ) {
    jQuery(document).on('facetwp-loaded', function() {

        jQuery('.faq li .question').on( 'click', function() {
            console.log('testing1');
            jQuery(this).find('.plus-minus-toggle').toggleClass('collapsed');
            console.log('testing2');
            jQuery(this).parents( 'li' ).toggleClass('active');
        });
    });

    if ( jQuery( 'body' ).hasClass( 'single-product' ) ) {
        jQuery('.faq li .question').on( 'click', function() {
            console.log('testing3');
            jQuery(this).find('.plus-minus-toggle').toggleClass('collapsed');
            console.log('testing4');
            jQuery(this).parents( 'li' ).toggleClass('active');
        });
    }

});