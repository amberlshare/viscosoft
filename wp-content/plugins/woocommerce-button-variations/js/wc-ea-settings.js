jQuery( function( $ ){

  //Setup the color pickers to work with our text input field
  $(document).ready(function(){
    "use strict";
    
    //This if statement checks if the color picker widget exists within jQuery UI
    //If it does exist then we initialize the WordPress color picker on our text input field
    if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
      $( '.color-picker' ).wpColorPicker();
    }
    else {
      //We use farbtastic if the WordPress color picker widget doesn't exist
      $( '#colorpicker' ).farbtastic( '#color' );
    }
  });

});

