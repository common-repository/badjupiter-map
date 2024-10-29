<?php

/**
 *
* Plugin Name: BadJupiter Map
* Plugin URI: https://util.badjupiter.com/plugin
* Description: Embed a BadJupiter Collection directly into your post as a map of places and a QR for mobile on the go.
* Version: 1.0.10
* Author URI: https://badjupiter.com/
**/

//
function bj_col_map_init()
{

	// Register the script like this for a plugin:
    wp_register_script( 'mapbox-js', 'https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.js');
 	wp_register_style( 'mapbox-css', 'https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.css' );

    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'mapbox-js' );
	wp_enqueue_style( 'mapbox-css' );

}

function bj_col_draw_map( $atts = array() ){

//  Have we been passed a collection slug?
	$slug = $atts['collection'];
	if( empty($slug) )
		return "<p>Sorry But we need a BadJupiter Collection slug";

	else {
		$mapid = sprintf("map%s", $slug);

		// 	// 	Lets get the collection
		$body = bj_col_get_collection( $slug );
		if ($body == "Fail")
			return "<p>Looks like there was something wrong with the BadJupiter collection, please check the collection ID.</p>";
		if ($body == "Null")
			return "<p>That Collection is Empty</p>";

		// 	Draw the Dom placeholders
		$output = "<div id='".$mapid."' style='width: 100%%; height: 300px; margin-bottom:25px;'>".bj_col_add_QR( $slug )."</div>";
// 		$output .= bj_col_add_QR( $slug );
		$output .= "<script>";

		//  Build the list of map features from the collection
		$features = bj_col_get_features( $body );

		// 	Get coords
		$coords = bj_col_get_coords( $body );

		// 	Add the map to the DOM
		$output .= bj_col_add_map( $mapid );

		//  Add the hover
		// 	$output .= bj_col_add_hover( $mapid );

		// 	Add the features to the map
		$output .= bj_col_add_points( $mapid, $features, $body, $coords);

		$output .= "</script>";

		$output .= bj_col_get_qr( $slug );
	}

	return $output;
}

function bj_col_add_map ( $mapid ){
	return sprintf("mapboxgl.accessToken = 'pk.eyJ1IjoiZGFucGVuIiwiYSI6ImNpb2JyNXUzaTA0ODN2NGtxZGVoamtqOW4ifQ.ElYXumVabnlbsthBexRhOA';

			var %s = new mapboxgl.Map({
			container: '%s',
			attributionControl: false,
			style: 'mapbox://styles/mapbox/light-v10'
			});", $mapid, $mapid);
}


function bj_col_add_QR( $slug ){

	$output = "";

	if (!wp_is_mobile()){
		$output .= "<div class='bj-text-center' style='float: left; position: absolute; bottom: 5px; min-width: 1cm; min-height: 1cm; z-index: 10; margin-left: 5px;  width: 100px;  height: 115px;  border-radius: 4px;  box-shadow: 0 2px 14px 0 rgba(132, 132, 132, 0.25);  background-color: white;'>
  				  <img id='qr". $slug ."' src='' style='max-width: 100px!important; margin: 0px;padding: 8px; background-color: #fff; border: 1px solid #dee2e6; border-radius: .25rem; max-width: 100%; height: auto;vertical-align: middle; border-style: none;padding-bottom:0px;' >
				  <span style='  width: 100px;  height: 21px;  font-size: 12px;  font-weight: normal;  font-stretch: normal;  font-style: normal;  line-height: 1.75;  text-align: center;  color: #8f8e94;  margin-left:8px;'>Scan for mobile</span>
			  </div>";
	} else {
		$tag = '?ref=wp&type=click&src='. urlencode( get_bloginfo( 'name' ) );
		$output .= "<div class='bj-text-center' style='float: left;position: absolute;bottom: 5px;z-index: 10;margin-left: 5px;width: 100px;height: 40px;border-radius: 4px;box-shadow: 0 2px 14px 0 rgba(132, 132, 132, 0.25);background-color: white;'>
  				  <a href='https://jupiter.link/". $slug . "/" . $tag ."' target='_blank' style='text-decoration: none;'><img src='". plugins_url( 'badjupiter-map/assets/img/logo-small.jpg', _FILE_ ) ."' style='width: 39px!important;margin: 1px;height: 38px;vertical-align: middle;float: left;border: 0px !important;padding: 0px !important;box-shadow: 0 0 0 0px #fff;' >
				  <p style='width: 90px;height: 50px;font-size: 12px;font-weight: normal;font-stretch: normal;font-style: normal;line-height: 1.50;text-align: center;color: #8f8e94;margin-left: 8px;margin-top: 3px;text-align: center;vertical-align: middle;'>Tap for Mobile</p></a>
			  </div>";
              // $output .= "<div class='bj-text-center' style='float: left;position: absolute;bottom: 5px;z-index: 10;margin-left: 5px;width: 100px;height: 40px;border-radius: 4px;box-shadow: 0 2px 14px 0 rgba(132, 132, 132, 0.25);background-color: white;'>
              //               <a href='https://jupiter.link/". $slug ."' target='_blank' style='text-decoration: none;'><img src='https://util.badjupiter.com/static/images/logo-small.jpg' style='width: 39px!important;margin: 1px;height: 38px;vertical-align: middle;float: left;border: 0px !important;padding: 0px !important;box-shadow: 0 0 0 0px #fff;' >
              //           <p style='width: 90px;height: 50px;font-size: 12px;font-weight: normal;font-stretch: normal;font-style: normal;line-height: 1.50;text-align: center;color: #8f8e94;margin-left: 8px;margin-top: 3px;text-align: center;vertical-align: middle;'>Tap for Mobile</p></a>
              //       </div>";
	}

	return $output;

}

function bj_col_add_points( $mapid, $features, $body, $coords ){
	$output = sprintf("%s.on('load', function() {
				%s.addSource('points', {
					'type': 'geojson',
					'data': {
						'type': 'FeatureCollection',",$mapid,$mapid);

	$output .= $features;

	$output .= "}
				})";

    $output .= bj_col_add_paths( $mapid, $body );

	$output .= sprintf("
				%s.addLayer({
					'id': 'labels',
					'type': 'symbol',
					'source': 'points',
					'layout': {
						'text-field': ['get', 'title'],
						'text-variable-anchor': ['top', 'bottom', 'left', 'right'],
						'text-radial-offset': 0.5,
						'text-justify': 'auto'
					}

				});",$mapid);

	$output .= sprintf("
				%s.addLayer({
					'id': 'points',
					'type': 'circle',
					'source': 'points',
					paint: {
						'circle-color': '#c74440',
						'circle-radius': 4,
						'circle-stroke-width': 1,
						'circle-stroke-color': '#fff'
					}
				});",$mapid);



	// 	Fit to the bounds of the collections coordinates
	$output .= "
		// ". $coords . "
		var coordinates = " . json_encode( $coords ) ."
		";

	$output .= sprintf("
		var bounds = coordinates.reduce(function(bounds, coord) {
			return bounds.extend(coord);
		}, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));

		%s.fitBounds(bounds, {
 			padding: {top: 30, bottom:30, left: 110, right: 30}
 		});", $mapid, $mapid);


// 	$output .= sprintf("
// 		var coordinates = %s;
// 		var bounds = coordinates.reduce(function(bounds, coord) {
// 			return bounds.extend(coord);
// 		}, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));

// 		%s.fitBounds(bounds, {
//  			padding: {top: 30, bottom:30, left: 110, right: 30}
//  		});", json_encode(array_values($coords)), $mapid, $mapid);



// // Uncomment to add hover over or disable zoom on the map
// 	$output .= $mapid.".scrollZoom.disable()";
// 	$output .= bj_col_add_hover( $mapid );

	// $output .= bj_col_add_paths( $mapid, $body );

	$output .= "
			});";

	return $output;
}


function bj_col_add_paths( $mapid, $body ){
	$r = "
	";
    $numItems = count($body->result->map_overlays);
	$type="";

	$maps = $body->result->map_overlays;


    if ( $numItems>0 ){
        // Add the source
        forEach( $maps as $map ) {

            if ($map->type=="path"){
                $type1="LineString";
            } elseif ( $map->type=="poly" ){
                $type1="LineString";
            }

            // $path = "[" + $map.coords.map(a => "[" +a.join(",")).join("], ") + "] ]"
            $path = sprintf("[%f]", $map->coords);

            $r .= "".$mapid.".addSource('p". $map->id. "', {
                            'type': 'geojson',
                            'data': {
                                'type': 'Feature',
                                 'geometry': {
                                    'type': '".$type1."',
                                    'coordinates': ".json_encode($map->coords)."
                                }";

            // if we are not on the last item we should add a ,
            if(++$i < $numItems) {
                $r .= "
                ";
            }

            $r .= "}});
            ";

        };

    	// r = r + "]";

        // Add the Layers for Paths
        forEach( $maps as $map ) {

			if ( $map->type == "path") {
                $r .= $mapid.".addLayer({
                            'id': '". $map->id ."',
                            'type': 'line',
                            'source': 'p". $map->id ."',
                            'layout': {
                                'line-join': 'round',
                                'line-cap': 'round'
                            },
                            'paint': {
                                'line-color': '". $map->stroke_color ."',
                                'line-width': ". $map->stroke_width."
                            }
        	       });
                 ";
            } elseif ($map->type == "poly") {
                $opacityStr = 0;
                $fillStr = $map->fill_color;

				if (strlen($map->fill_color)==9){
					$opacityStr = substr($map->fill_color, -2);
                    $fillStr = substr($map->fill_color, 0, -2);
					$opacity = hexdec($opacityStr) / 255;

				}
				$r .= $mapid.".addLayer({
                            'id': 'l". $map->id ."',
                            'type': 'line',
                            'source': 'p". $map->id ."',
                            'layout': {
                                'line-join': 'round',
                                'line-cap': 'round'
                            },
                            'paint': {
                                'line-color': '". $map->stroke_color ."',
                                'line-width': ". $map->stroke_width."
                            }
        	       });
                 ";
				$r .= $mapid.".addLayer({
                            'id': '". $map->id ."',
                            'type': 'fill',
                            'source': 'p". $map->id ."',
                            'layout': {},
                            'paint': {
                                'fill-outline-color': '". $map->stroke_color ."',
                                'fill-color': '". $fillStr ."',
                                'fill-opacity': $opacity
                            }
        	       });
                 ";

            }
        };
    }

	return $r;
}

function bj_col_add_hover( $mapid ){
	$output = sprintf("
	var popup = new mapboxgl.Popup({
		closeButton: false,
		closeOnClick: false
	});

	%s.on('mouseenter', 'points', function(e) {
		// Change the cursor style as a UI indicator.
		%s.getCanvas().style.cursor = 'pointer';

		var coordinates = e.features[0].geometry.coordinates.slice();
		var title = e.features[0].properties.title;

		// Ensure that if the map is zoomed out such that multiple
		// copies of the feature are visible, the popup appears
		// over the copy being pointed to.
		while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
			coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
		}

		// Populate the popup and set its coordinates
		// based on the feature found.
		popup
			.setLngLat(coordinates)
			.setHTML(title)
			.addTo(%s);
	});

	%s.on('mouseleave', 'points', function() {
		%s.getCanvas().style.cursor = '';
		popup.remove();
	});",$mapid, $mapid, $mapid, $mapid, $mapid  );
;

	return $output;
}

function bj_col_get_features( $body ){

	$output = "'features': [";
	$coords = "[";
	$numItems = count($body->result->items);
	$i = 0;

	foreach( $body->result->items as $item ) {

		$output .= "{
				'type': 'Feature',
				'geometry': {
				'type': 'Point',
				'coordinates': [
					" . $item->location->coords->lng . ",
					" . $item->location->coords->lat . "
				]
				},
				'properties': {
					'title': '" . htmlspecialchars_decode( esc_js( $item->name ) ) . "',
					'icon': 'music'
				}
			}";
		$coords .= sprintf("[%f,%f]", $item->location->coords->lng,$item->location->coords->lat);
		// if we are not on the last item we should add a ,
		if(++$i < $numItems) {
			$output .= ",
			";
			$coords .= ",
			";
		}

	}

	$output .= "]
	";
	$coords .= "]";

	$r->features = $output;
	$r->coords = $coords;
// 	return json_encode($r);
	return $output;
}

function bj_col_get_coords( $body ){

	$coordArr = array();
	$allpointsArr = array();
	$numItems = count($body->result->items);

	foreach( $body->result->items as $item ) {
		array_push($coordArr, array($item->location->coords->lng, $item->location->coords->lat));
	};
	$allpointsArr = bj_col_get_plot_coords( $body );
// 	array_merge_recursive($coordArr, $allpointsArr);

// 	return bj_col_get_plot_coords( $body );
	return array_merge_recursive($coordArr, $allpointsArr);
}

function bj_col_get_plot_coords( $body ){

    $numItems = count($body->result->map_overlays);
	$arr = array();

	if ( $numItems && $numItems>0 ){

    	forEach( $body->result->map_overlays as $map) {
			forEach( $map->coords as $c) {
				array_push($arr, array_values($c));
			}
// 				array_push($arr, $map->coords );
    	};
    }
	return $arr;
}


function bj_col_get_collection( $slug ){

	$request_uri = sprintf('https://api.badjupiter.com/biz10/collection/%s/', $slug);
	$request = wp_remote_get( $request_uri );

	if( is_wp_error( $request ) || '200' != wp_remote_retrieve_response_code( $request ) )
		return "Fail";

	$body = json_decode( wp_remote_retrieve_body( $request ) );
	$numItems = count($body->result->items);

	if($numItems = 0 )
		return "Null";

	if( empty( $body ) )
		return "Null";

	return $body;

}

function bj_col_get_qr( $slug ){
	$baseURL = 'https://api.qrserver.com/v1/create-qr-code/?data=';
	$config = '&size=120x120';
	$bj = 'https://jupiter.link/';
	$tag = '?ref=wp&type=qr&src='. get_bloginfo( 'name' );

	$output = "<script>
		qr". $slug .".src = '". $baseURL . urlencode( $bj.$slug.$tag ) . $config . "';
	</script>";

	return $output;

}


add_action( 'wp_enqueue_scripts', 'bj_col_map_init' );

function bj_col_shortcodes_init(){
 add_shortcode( 'badjupiter-map', 'bj_col_draw_map' );
}

add_action('init', 'bj_col_shortcodes_init');

?>
