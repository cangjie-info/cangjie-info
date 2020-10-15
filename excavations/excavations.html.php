<h1>All Excavations:</h1>
<div id="map"></div>
<?php foreach($excavations as $excavation) : ?>
<p><a href="../excavation/?id=<?php echo $excavation['id']; ?>"/>
<?php echo $excavation['name_en'] . ' ' . $excavation['name_zh']; ?>
</a>
</p>
<?php endforeach; ?>

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
<script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDy02I_mXcS9t8ND9iAlWIyo95zStY2x7U&callback=initMap">
</script>

