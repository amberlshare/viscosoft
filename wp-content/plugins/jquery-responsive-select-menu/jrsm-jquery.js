// Add jquery class to body for jQuery dependent CSS
(function() {
    jQuery('html').addClass('jquery');
})();

// Main functionality
jQuery(document).ready(function() {

    // Menu containers array
    var menuContainers = jrsm_params.containers.replace(/, /g,',').split(',');

    // Only proceed if some menuContainer is specified
    if ( '' == menuContainers )
        return false;
    
    // 1. Loop through menu containers
    jQuery.each(menuContainers, function( index, container ) {

        // Find first <ul> in container
        var ul = jQuery(container).find('ul').first();

        // Add dropdown <select>
        jQuery('<select />', {
            'class': 'jquery-responsive-select-menu jrsm-' + index,
            'name': 'jrsm-' + index
        }).insertAfter(ul);

        if ( !jrsm_params.firstItem )
            var label = 'Navigation';
        
        // Add <label> for select
        jQuery('<label />', {
            'text': label,
            'class': 'jrsm-label',
            'for': 'jrsm-' + index
        }).insertAfter(ul);

        // Get jQuery object of <select> to append to
        select = jQuery(container).find('.jquery-responsive-select-menu');

        // Create first, default <option>
        if ( jrsm_params.firstItem ) {
            var firstOption = jQuery('<option />', {
                'class': 'first-option',
                'value'   : '',
                'text'    : jrsm_params.firstItem
            }).appendTo(select);
        }

        // Loop through menu item <li>'s in container
        get_child_menu_items( ul, 1 );

        // Choose <option> to be selected
        if ( 1 == jrsm_params.showCurrentPage )
            select.find('.current-page').prop('selected',true);
        else
            select.find('option').first().prop('selected',true);

    }); // End 1. Main loop through menu containers

    // Select functionality
    jQuery('.jquery-responsive-select-menu').change(function() {
        var option = jQuery(this).find('option:selected').is('[value]');

        // Check if option has actual value
        if ( jQuery(this).find('option:selected').is('[value]') && "#" != jQuery(this).find('option:selected').val() ) {
            window.location = jQuery(this).find('option:selected').val();
        }
    });

});

function get_child_menu_items( ul, depth ) {

    // 2. Loop through menu item <li>'s
    jQuery.each( ul.children('li'), function( index, li ) {
        
        // Get jQuery object of <li>
        var li = jQuery(li);

        // Get depth prefix
        var prefix = jrsm_params.indent;
        prefix = Array(depth).join(prefix);

        // Get <li> value & text
        var value = li.children('a').attr('href'); 
        var text = li.children('a').text();

        // Ouput <option>
        if ( ! jrsm_params.hideEmptyLinks || ( jrsm_params.hideEmptyLinks && value && "#" != value ) ) {
            var option = jQuery('<option />', {
                'value'   : value,
                'text'    : prefix + ' ' + text
            }).appendTo(select);

            // Add "disabled" property if needed
            if ( jrsm_params.disableEmptyLinks && ( ! value || "#" == value ) ) {
                option.prop('disabled', true);
            }
        }

        // Add current class to current page item
        if ( li.hasClass('current_page_item') || li.hasClass('current-menu-item') ) {
            option.addClass('current-page');
        }

        // Only do something if this <li> contains a child <ul>
        var ul = li.children('ul');

        // Repeat this loop for child <ul>'s
        if ( ul.length > 0 ) {
            get_child_menu_items( ul, depth + 1 );
        }

    }); // End 2. Loop through menu item <li>'s

}
