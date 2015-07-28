<?php
if (!empty($_GET['location'])) {
    $maps_url = 'http://loc.geopunt.be/geolocation/location?q=' . urlencode($_GET['location']);

    $maps_json = file_get_contents($maps_url);
    $maps_array = json_decode($maps_json, true);

    $lat = $maps_array['LocationResult'][0]['Location']['Lat_WGS84'];
    $lon = $maps_array['LocationResult'][0]['Location']['Lon_WGS84'];
    $zoom = 20;
    
    //$google_maps_url = 'https://www.google.com/maps/@' . $lat . ',' . $lon . ',' . $zoom . 'z';

    //header("Location: $google_maps_url");
} else {
    $lat = 50.8086126;
    $lon = 3.2469894;
    $zoom = 10;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>MapTest</title>
        <style>
            #map-canvas {
                width: 1400px;
                height: 1000px;
            }
        </style>
        <script src="https://maps.googleapis.com/maps/api/js"></script>
        <script>
            function initialize() {
                var mapCanvas = document.getElementById('map-canvas');
                var mapOptions = {
                    center: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>),
                    zoom: <?php echo $zoom; ?>,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                var map = new google.maps.Map(mapCanvas, mapOptions)
            }
            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    </head>
    <body>
        <a href="test.php">energylabelatlas</a>
        <form action="">
            <input type="text" name="location" />
            <button type="submit">submit</button>
        </form>
        <div id="map-canvas"></div>
    </body>
</html>