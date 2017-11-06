<script type="text/javascript">
var map;
var geocoder;
var mouse;
var coords = new Object();
var markersArray = [];
coords.lat = <?php echo empty($coords) ? 0 : $coords[0];?>;
coords.lng = <?php echo empty($coords) ? 0 : $coords[1];?>;

// dom绘制完成后执行
jQuery(function($) {
	
	loadScript();

	/* 显示地图按钮的点击事件 */
    $( "#show-map" ).click(function() {
        $( "#dialog-<?php echo $id;?>" ).dialog("open");
    });
});

// 页面所有内容加载完成后（包括图片）执行
//jQuery(window).load(function(){
//})

function addMarkerInfo(latLng) {
	geocoder.geocode({'latLng': latLng}, function(results, status){
		var content = '';
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[1]) {
				content = results[1].formatted_address + '<br />' + latLng.lat() + '<br />' + latLng.lng();
			}
		} else {
			alert("Geocoder failed due to: " + status);
		}
		addMarker(latLng.lat(), latLng.lng(), '', content);
	});
}
function addMarker(srcLat,srcLon,title,popUpContent,markerIcon) {
        var latlng = new google.maps.LatLng(srcLat, srcLon);
        var marker = new google.maps.Marker({
              position: latlng,
              map: map,
              title:title,
              icon: markerIcon
          });
        markersArray.push(marker);
        var infowindow = new google.maps.InfoWindow({
            content: popUpContent
        });
        infowindow.open(map,marker);
        
}
//Removes the overlays from the map, but keeps them in the array
function clearOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(null);
    }
  }
}
// Shows any overlays currently in the array
function showOverlays() {
	  if (markersArray) {
	    for (i in markersArray) {
	      markersArray[i].setMap(map);
	    }
	  }
	}

// Deletes all markers in the array by removing references to them
function deleteOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(null);
    }
    markersArray.length = 0;
  }
}
function initialize() {   
	geocoder = new google.maps.Geocoder();

   if (coords.lat == 0 || coords.lng == 0) {
		// Try W3C Geolocation (Preferred)
		if(navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
				LabPHP.debug(initialLocation);
			    var myOptions = {
					zoom: 15,
					center: initialLocation,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map = new google.maps.Map(document.getElementById("map-canvas"),  myOptions);
				addMarkerInfo(initialLocation);
				map.setCenter(initialLocation);
				//coords.lat = position.coords.latitude;
				//coords.lng = position.coords.longitude;
			});
		// Try Google Gears Geolocation
		} else if (google.gears) {
			var geo = google.gears.factory.create('beta.geolocation');
			geo.getCurrentPosition(function(position) {
				initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
			    var myOptions = {
					zoom: 15,
					center: initialLocation,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map = new google.maps.Map(document.getElementById("map-canvas"),  myOptions);
				addMarkerInfo(initialLocation);
				map.setCenter(initialLocation);
				//coords.lat = position.latitude;
				//coords.lng = position.longitude;
		    });
		// Browser doesn't support Geolocation
		}
	}
	else {
	    latlng = new google.maps.LatLng(coords.lat, coords.lng);
	    var myOptions = {
			zoom: 15,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("map-canvas"),  myOptions);
		addMarkerInfo(latlng);
		map.setCenter(latlng);
   }
   
	google.maps.event.addListener(map, 'click', function(event) {
		deleteOverlays();
		addMarkerInfo(event.latLng);
		document.getElementById("<?php echo $id;?>").value = event.latLng.lat() + ', ' + event.latLng.lng();
	});
}
// 构造加载google js代码
function loadScript() {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=initialize";
	document.body.appendChild(script);
}
	  
</script>

<input type="text" id="<?php echo $id;?>" name="<?php echo $id;?>"
	size="<?php echo $size;?>" value="<?php echo $value;?>" />
<a id="show-map" class="easyui-linkbutton">打开地图</a>
<div id="dialog-<?php echo $id;?>" title="选择地点" class="easyui-dialog"
	data-options="iconCls:'icon-save',shadow:true,closed:false,onOpen:function() {}"
	style="width: 500px; height: 350px;">
	<div id="map-canvas" style="width: 100%; height: 100%;"></div>
</div>
