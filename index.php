<?php
if (!empty($_GET['location'])) {
    $maps_url = 'http://loc.geopunt.be/geolocation/location?q=' . urlencode($_GET['location']);

    $maps_json = file_get_contents($maps_url);
    $maps_array = json_decode($maps_json, true);

    if (!empty($maps_array['LocationResult'][0]['Location']['Lat_WGS84'])) {
        $lat = $maps_array['LocationResult'][0]['Location']['Lat_WGS84'];
        $lon = $maps_array['LocationResult'][0]['Location']['Lon_WGS84'];
        $zoom = 18;


        $formattedAdress = $maps_array['LocationResult'][0]['FormattedAddress'];
        $keywords = preg_split("/[,]+/", $formattedAdress);
        $length = count($keywords);
        $adres = $keywords[0];

        if (ctype_alpha(substr($adres, -1))) {
            $adres = substr($adres, 0, strlen($adres) - 1) . '  ' . substr($adres, -1);
        }

        $obid_json = file_get_contents('http://www.govmaps.eu/arcgis/rest/services/ICL/ICL_Energielabelatlas/MapServer/0/query?where=ADRES' . urlencode('=\'' . $adres . '\'') . '&returnIdsOnly=true&f=pjson');

        $obid_array = json_decode($obid_json, true);

        $objectId = $obid_array['objectIds'][0];

        $residence_info_json = file_get_contents("http://www.govmaps.eu/arcgis/rest/services/ICL/ICL_Energielabelatlas/MapServer/2/" . $objectId . "?f=pjson");
        $residence_info_array = json_decode($residence_info_json, true);

        if (!empty($residence_info_array['feature']['attributes'])) {
            //echo var_dump($residence_info_array['feature']['attributes']);
        } else {
            echo 'no info over this house';
        }
    } else {
        echo 'this house is not found';
        $lat = 50.8086126;
        $lon = 3.2469894;
        $zoom = 10;
    }
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
        <script src="js/leafletesri.js"></script>
        <script src="js/leafletwms.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/proj4js/2.0.0/proj4.js"></script>
        <script src="js/proj.js"></script>
    </head>
    <body>
        <a href="#">bereken label</a> <!-- gaat naar de questionnaire -->
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
            <?php if(!empty($residence_info_array['feature']['attributes'])){ ?>
            var residence =  <?php echo json_encode($residence_info_array['feature']['attributes']) ?>;
            localStorage.setItem('residence', JSON.stringify(residence));
            //var retrievedResidence = localStorage.getItem('residence');
            //console.log('retrievedResidence: ', JSON.parse(retrievedResidence));
            <?php  } ?>
        </script>
    </body>
</html>
