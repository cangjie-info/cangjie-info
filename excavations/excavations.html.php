<h1>All Excavations:</h1>
<!-- MAP -->
<div id="map"></div>
<!-- EXCAVATIONS LIST -->
<?php foreach($excavations as $excavation) : ?>
<p>
<a href="../excavation/?id=<?php echo $excavation['id']; ?>"/>
<?php 
echo $excavation['name_en'] . ' ' 
	. $excavation['name_zh'] . ' '
	. $excavation['year'] . ' ('
	. $excavation['location'] . ') '; ?>
</a>
</p>
<form action="." method="post">
	<input type="hidden" name="action" value="delete">
	<input type="hidden" name="id" value="<?php echo $excavation['id']; ?>">
	<input type="submit" value="delete">
</form>
<?php endforeach; ?>
<hr />
<!-- ADD NEW FORM -->
<form action="." method="post" id="add_form">
<input type="hidden" name="action" value="add">
<label>name (zh):</label>
<input type="text" name="name_zh">
</br>
<label>year:</label>
<input type="text" name="year">
</br>
<label>name (en):</label>
<input type="text" name="name_en">
</br>
<label>location:</label>
<input type="text" name="location">
</br>
<label>latitude:</label>
<input type="text" name="latitude">
</br>
<label>longitude:</label>
<input type="text" name="longitude">
</br>
<label>excavator:</label>
<input type="text" name="excavator">
</br>
<input type="submit" value="Add">
</form>
<!-- JS FOR MAP -->
<script>
function initMap() {
	var excavations = <?php echo $json_excavations; ?>;
	console.log(excavations);
	var guodian = {lat: 30.501053, lng: 112.177199};
	var map = new google.maps.Map(
		document.getElementById('map'), {zoom: 4, center: guodian});
	for(var i = 0; i < excavations.length; i++) {
		var place = {lat: 0, lng: 0};
		place.lat = parseInt(excavations[i]["latitude"]);
		place.lng = parseInt(excavations[i]["longitude"]);
		var marker = new google.maps.Marker({position: place, map: map});
	}
}
</script>
<script defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_key; ?>&callback=initMap">
</script>
