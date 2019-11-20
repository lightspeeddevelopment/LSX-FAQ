jQuery(document).ready(function( ) {
    // https://www.bugherd.com/projects/128771/tasks/476
    let already_toggled = false;

    jQuery('.faq li .question').on( 'click', function() {
        // console.log('testing1');
        jQuery(this).find('.plus-minus-toggle').toggleClass('collapsed');
        // console.log('testing2');
        jQuery(this).parents( 'li' ).toggleClass('active');
        already_toggled = true;
    });

    if ( jQuery( 'body' ).hasClass( 'single-product' ) ) {
        jQuery('.faq li .question').on( 'click', function() {
            if (!already_toggled) {
                // console.log('testing3');
                jQuery(this).find('.plus-minus-toggle').toggleClass('collapsed');
                // console.log('testing4');
                jQuery(this).parents( 'li' ).toggleClass('active');
            }
        });
    }

});