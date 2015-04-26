<?php
/**
 * Autocomplete Address input field.
 *
 * @package torindul-calendar
 * @subpackage forms
 */
class AddressTextField extends TextField {
	
	/**
	 * Includes the JavaScript neccesary for this field to work using the {@link Requirements} system.
	 */
	public static function include_js( $GoogleMapsKey = null ) {
		
		//Include Google Maps v3 API with Places Library (requires Javascript and Places APIs to be enabled)
		Requirements::javascript('https://maps.googleapis.com/maps/api/js?v=3&key=' . $GoogleMapsKey . '&sensor=false&libraries=places');
		
		//Include Field Javascript (attach Google functionality to the field)
		Requirements::javascript('torindul-calendar/js/googleautocomplete.js');		
		
	}
	
	// Include link switcher JavaScript
	public function __construct($name, $title = null, $GoogleMapsKey = null, $value = '', $form = null ) {
		
		//If we haven't been provided with a Google Maps API Key, throw an error.
		if( $GoogleMapsKey == null ) { 
			user_error("You neglected to provide a Google Maps API key in your use of AddressTextField", E_USER_WARNING);
			exit;
		}
		
		//Construct the form field
		parent::__construct($name, $title, $value, $form);
		
		//Include the Google Maps API Javascript
		self::include_js( $GoogleMapsKey );
		
	}
	
}
