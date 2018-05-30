

jQuery(document).ready(function ($) {
	
	var attend_button = $('#event-attend-button').is(':checked');  
	if( attend_button == false )
		$("#event-attend-options").hide();
	
	var attendees_list = $('#event-attendees-list').is(':checked'); 
	if( attendees_list == false )	
		$("#event-attendees-display-options").hide();


	$('#event-attend-button').change(function () {
		$('#event-attend-options').fadeToggle();
	});	

	$('#event-attendees-list').change(function () {
		$('#event-attendees-display-options').fadeToggle();
	});
	

	$('#event-date').datetimepicker({
		controlType: 'select',
		oneLine: true,
		timeFormat: 'h:mm tt',
		dateFormat: 'DD, MM d, yy',
		firstDay: 0
	});

	$('#event-date-end').datetimepicker({
		controlType: 'select',
		oneLine: true,
		timeFormat: 'h:mm tt',
		dateFormat: 'DD, MM d, yy',
		firstDay: 0	
	});
	
});

function initialize() {
	var input = document.getElementById('event-location');
	var autocomplete = new google.maps.places.Autocomplete(input);
	google.maps.event.addListener(autocomplete, 'place_changed', function () {
		var place = autocomplete.getPlace();
		//console.log(place);
		var lat = place.geometry.location.lat();
		var lng = place.geometry.location.lng();
		var latlng = lat + ',' + lng;
		//document.getElementById('event-place').value = JSON.stringify(place);
		if( place.formatted_address.indexOf( place.name ) > -1 )
			document.getElementById('event-address').value = place.formatted_address;
		else
			document.getElementById('event-address').value = place.name + ', ' + place.formatted_address;
		document.getElementById('event-latlng').value = latlng;
	});
}

google.maps.event.addDomListener(window, 'load', initialize);

function clearFileInput(finput) {
    var oldInput = document.getElementById(finput);

    var newInput = document.createElement("input");

    newInput.type = "file";
    newInput.id = oldInput.id;
    newInput.name = oldInput.name;
    newInput.className = oldInput.className;
    newInput.style.cssText = oldInput.style.cssText;

    oldInput.parentNode.replaceChild(newInput, oldInput);
}
