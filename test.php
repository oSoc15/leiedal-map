<?php
if (!empty($_GET['location'])) {
    $maps_url = 'http://loc.geopunt.be/geolocation/location?q=' . urlencode($_GET['location']);

    $maps_json = file_get_contents($maps_url);
    $maps_array = json_decode($maps_json, true);

    $lat = $maps_array['LocationResult'][0]['Location']['Lat_WGS84'];
    $lon = $maps_array['LocationResult'][0]['Location']['Lon_WGS84'];
    $zoom = 18;

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
        <title>PLONN</title>
        <style>
            #map {
                width: 1400px;
                height: 1000px;
            }
        </style>
        <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-1.0.0-b1/leaflet.css" />
        <script src="http://cdn.leafletjs.com/leaflet-1.0.0-b1/leaflet.js"></script>
        <script src="leafletesri.js"></script>
        <script src="leafletwms.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/proj4js/2.0.0/proj4.js"></script>
        <script src="proj.js"></script>
    </head>
    <body>
        <a href="arcgis.php">energylabelatlas</a>
        <form action="">
            <input type="text" name="location" />
            <button type="submit">submit</button>
        </form>
        <div id="map"></div>
        <script>

            var crs = new L.Proj.CRS("EPSG:31370",
                    "+proj=lcc +lat_1=51.16666723333333 +lat_2=49.8333339 +lat_0=90 +lon_0=4.367486666666666 +x_0=150000.013 +y_0=5400088.438 +ellps=intl +towgs84=-106.868628,52.297783,-103.723893,0.336570,-0.456955,1.842183,-1.2747 +units=m +no_defs",
                    {
                        resolutions: [12000, 143000, 269000, 255000], // 3 example zoom level resolutions
                    }
            );

            var map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lon; ?>], <?php echo $zoom; ?>);
            var tiles = L.WMS.tileLayer("http://grb.agiv.be/geodiensten/raadpleegdiensten/GRB-basiskaart/wmsgr?", {
                'tileSize': 512,
                'layers': 'GRB_BASISKAART',
                'transparent': false,
                'crs': crs
            });
            tiles.addTo(map);
            var tiles2 = L.WMS.tileLayer("http://wms.agiv.be/ogc/wms/omkl?", {
                'tileSize': 512,
                'layers': 'Ortho',
                'transparent': true
            });
            tiles2.addTo(map);
            L.esri.dynamicMapLayer({
                url: 'http://www.govmaps.eu/arcgis/rest/services/ICL/ICL_Energielabelatlas/MapServer',
                opacity: 0.5,
                'crs': crs
            }).addTo(map);
        </script>
    </body>
</html>