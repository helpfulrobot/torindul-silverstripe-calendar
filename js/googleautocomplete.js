(function($) {
	
    $(document).ready(function(){
	    
	    /* The Google Maps Places API */
	    function initialize() {

			/* The field itself */
		    var input = document.getElementById('Form_ItemEditForm_Location');
		    
		    /* The variable to store the result */
		    var place;
		    
		    /* Autocompletion location */
		    var autocomplete = new google.maps.places.Autocomplete(input);
		    
		    /* Autocomplete Info Window */
		    var infowindow = new google.maps.InfoWindow();
		 
		    /* On selecting the field add a listener to catch input and return suggestions */
		    google.maps.event.addListener(autocomplete, 'place_changed', function () {
			    
			    /* Close the info window until we type */
			    infowindow.close();
			    
			    /* Store the result in the place variable */
				var place = autocomplete.getPlace();
				
		    });
	    
	    }
	    			    
		$('.addresstext').ready(function() {
			
			/* Link the Google Maps initialize function above to the DOM onLoad event */
		    google.maps.event.addDomListener(window, 'load', initialize);	    
		    
		});
        
    })
    
})(jQuery);