/* Dutch (Belgium) initialisation for the jQuery UI date picker plugin. */
/* David De Sloovere @DavidDeSloovere */
( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {

datepicker.regional[ "nl-BE" ] = {
	closeText: "Sluiten",
	prevText: "←",
	nextText: "→",
	currentText: "Va