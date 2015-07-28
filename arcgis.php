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
        <title>PLONN</title>
        <style>
            #map {
                width: 1400px;
                height: 1000px;
            }
        </style>
        <script src="http://js.arcgis.com/3.13/"></script>
        <script>
            var map;
            require([
                "esri/layers/ArcGISDynamicMapServiceLayer",
                "esri/layers/WMSLayer",
                "esri/layers/WMSLayerInfo",
                "esri/geometry/Extent",
                "esri/map",
                "esri/geometry/webMercatorUtils",
                "esri/geometry/Point",
                "dojo/domReady!"
            ], function (ArcGISDynamicMapServiceLayer, WMSLayer, WMSLayerInfo, Extent, Map, webMercatorUtils, Point) {

                map = new Map("map", {
                    //basemap: "topo",
                    zoom: 14,
                    center: [3.265028, 50.827991],
                    sliderStyle: "small",
                    logo: false
                });

                var grbLayerInfo = new WMSLayerInfo({
                    name: 'GRB_BASISKAART',
                    title: 'GRB'
                });

                var resourceInfo = {
                    extent: new Extent(12000, 143000, 269000, 255000, {
                        wkid: 31370
                    }),
                    layerInfos: [grbLayerInfo]
                };
                var grbLayer = new WMSLayer('http://grb.agiv.be/geodiensten/raadpleegdiensten/GRB-basiskaart/wmsgr?', {
                    resourceInfo: resourceInfo,
                    visibleLayers: ['GRB_BASISKAART']
                });
                map.addLayers([grbLayer]);

                var orthoLayerInfo = new WMSLayerInfo({
                    name: 'Ortho',
                    title: 'Ortho'
                });

                var resourceInfo = {
                    extent: new Extent(22000, 150000, 259000, 245000, {
                        wkid: 31370
                    }),
                    layerInfos: [orthoLayerInfo]
                };
                var orthoLayer = new WMSLayer('http://wms.agiv.be/ogc/wms/omkl?', {
                    resourceInfo: resourceInfo,
                    visibleLayers: ['Ortho']
                });
                map.addLayers([orthoLayer]);

                var energylabelLayerURL = "http://www.govmaps.eu/arcgis/rest/services/ICL/ICL_Energielabelatlas/MapServer";
                var energylabelLayerOptions = {
                    "id": "energylabelLayer",
                    "opacity": 0.8,
                    "showAttribution": false
                };
                var energylabelLayer = new ArcGISDynamicMapServiceLayer(energylabelLayerURL, energylabelLayerOptions);
                /*
                 var layerDefinitions = [];
                 layerDefinitions[2] = "LABEL = 'E'";
                 energylabelLayer.setLayerDefinitions(layerDefinitions);
                 */
                map.addLayer(energylabelLayer);
                function zoom() {
                    var pt = webMercatorUtils.geographicToWebMercator(new Point(51, 3));
                    map.centerAndZoom(pt, 6);
                }
            });
        </script>
    </head>
    <body>
        <a href="arcgis.php">energylabelatlas</a>
        <form action="">
            <input type="text" name="location" />
            <button type="submit">submit</button>
        </form>
        <div id="map"></div>
    </body>
</html>