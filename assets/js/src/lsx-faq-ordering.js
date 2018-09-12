var LSX_FAQ = {

    init: function () {
        this.current_term = this.getUrlParameter( 'faq-category' );

        this.watchPosts();
        this.watchCategories();
    },

    watchPosts: function () {
        var $this = this;
        jQuery('table.posts #the-list, table.pages #the-list').sortable({
            'items': 'tr',
            'axis': 'y',
            'helper': LSX_FAQ.fixHelper,
            'update' : function(e, ui) {
                jQuery.post( ajaxurl, {
                    action: 'update-menu-order',
                    order: jQuery('#the-list').sortable('serialize'),
                    term: $this.current_term
                });
            }
        });
    },

    watchCategories: function () {
        jQuery('table.tags #the-list').sortable({
            'items': 'tr',
            'axis': 'y',
            'helper': LSX_FAQ.fixHelper,
            'update' : function(e, ui) {
                jQuery.post( ajaxurl, {
                    action: 'update-menu-order-tags',
                    order: jQuery('#the-list').sortable('serialize'),
                });
            }
        });
    },

    fixHelper: function(e, ui) {
        ui.children().children().each(function() {
            jQuery(this).width($(this).width());
        });
        return ui;
    },

    getUrlParameter: function (sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }

        return false;
    },
};

jQuery(document).ready(function() {
    LSX_FAQ.init();
});