<?php

require_once '../../include/config.inc.php';
require_once '../../include/hosts.inc.php';
require_once '../../include/actions.inc.php';
require_once '../../include/items.inc.php';
include('../config.php');

require_once '../lib/ZabbixApi.class.php';
use ZabbixApi\ZabbixApi;
$api = new ZabbixApi($zabURL.'api_jsonrpc.php', ''. $zabUser .'', ''. $zabPass .'');

if(isset($_GET['off'])) {
	$off = $_GET['off'];
}

$groupID = array();

if(isset($_REQUEST['groupid']) && $_REQUEST['groupid'] != '' && $_REQUEST['groupid'] != 0) {

		$groupID = explode(",",$_REQUEST['groupid']);		
		
		if(in_array(-1, $groupID)) {		
			$dbLoc = DBselect( 'SELECT hi.hostid, h.host, hi.name, hi.location,
							hi.location_lat AS lat, hi.location_lon AS lon , h.snmp_disable_until AS sd, 
							h.status AS status, h.flags, h.description, h.maintenance_status AS maint							
							FROM host_inventory hi, hosts h 
							WHERE hi.location_lat <> 0 
							AND hi.hostid = h.hostid
							ORDER BY name ASC');	
		}
		
		else {					
			$dbLoc = DBselect( 'SELECT hi.hostid, h.host, hi.name, hi.location, hi.location_lat AS lat, hi.location_lon AS lon ,
							h.snmp_disable_until AS sd, h.status AS status, h.flags, h.description, h.maintenance_status AS maint							
							FROM host_inventory hi, hosts h, hosts_groups hg 
							WHERE hi.location_lat <> 0 
							AND hi.hostid = h.hostid
							AND hg.groupid IN ('. implode(",",$groupID).')
							AND h.hostid = hg.hostid
							ORDER BY name ASC');
		}
}
?>

<html> 
<head>
<title>ZabDash - <?php echo _('Hosts Map'); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="content-language" content="en-us" />
<meta http-equiv='refresh' content='300'>

<link rel="icon" href="../img/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon" />
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="../css/font-awesome.css" type="text/css" rel="stylesheet" />
<script src="../js/jquery.min.js" type="text/javascript" ></script>

<link href="css/map.css" rel="stylesheet" type="text/css" />
<script src="../js/reload_param.js" type="text/javascript" ></script>

<link rel="stylesheet" href="css/leaflet.css" />
<script src="js/leaflet.js"></script>
<link rel="stylesheet" href="css/MarkerCluster.css" />
<link rel="stylesheet" href="css/MarkerCluster.Default.css" />
<script src="js/leaflet.markercluster-src.js"></script>
<link rel="stylesheet" href="css/leaflet-beautify-marker-icon.css">
<script src="js/leaflet-beautify-marker-icon.js"></script>	

<style type="text/css">
	html { margin-top: 35px;}
	a, a:visited, a:focus, a:hover { color: #0776cc;}	
	
	#map_canvas {
		margin-left: auto;
		margin-right: auto;
		float: none;
		margin-top: 15px;
		width: 98%;
		height: 92%;
	}
	.mycluster-green {
		width: 32px;
		height: 32px;
		line-height: 32px;
		background-image: url('images/0-32.png');
		text-align: center;		
	}
	
	.mycluster-red {
		width: 32px;
		height: 32px;
		line-height: 32px;
		background-image: url('images/3-32.png');
		text-align: center;		
	}
</style> 
 
</head>

<script type="text/javascript">
               
var locations = 

<?php

$locations = [];

$icon_red = "./images/red-marker.png";
$icon_green = "./images/green-marker.png";

while ($row = DBFetch($dbLoc)) {

  $id = $row['hostid'];
  $title = $row['host'];  
  $url = "../../zabbix.php?action=problem.view&filter_hostids%5B%5D=".$id ."&filter_set=1";
  $url_host = "../../hosts.php?form=update&hostid=".$id;
  //$link = "<a href=". $url ." target=_blank >" . $title . "</a>";  
  $status = $row['status'];  
  $local = $row['location']; 
  $lat = $row['lat']; 
  $lon = $row['lon']; 
  $quant = $row['sd'];     
  $desc = str_replace(["\r\n", "\r", "\n"], "<br/>",$row['description']);	
  $maint = $row['maint']; 
	  
//if($row['status'] == 0 && $row['flags'] == 0) {	

	if ($status != 0) {

		#$color = "./images/prio5.png";
		$color = 6;				
		$num_up = 0;	
		$num_down = 1;	
		$conta[] = $id;
		$prio = 6;	
		$num_trig = 0;	
		$url = $url_host;	
	}
	
	if ($status == 0) {
	
		$trigger = $api->triggerGet(array(
			'output' => 'extend',
			'hostids' => $id,
			'sortfield' => 'priority',
			'sortorder' => 'DESC',
			'only_true' => '1',
			'active' => '1', 
			//'withUnacknowledgedEvents' => '1',
			'withLastEventUnacknowledged' => '1'			
		));				
	
		if ($trigger) {
	
			// Highest Priority error			
			if($trigger[0]->value == 0) { $prio = 9; $num_up = 1; $num_down = 0;} 	
	  		else { $prio = $trigger[0]->priority; $num_up = 0; $num_down = 1; } 			
			#$color = "./images/prio".$prio.".png";
			$color = $prio;	
			$num_trig = count($trigger);
		}
		
		else {							
			#$color = "./images/prio9.png";
			$color = 9;							
			$num_up = 1;	
			$num_down = 0;
		   $prio = 0;	
			$num_trig = 0;	
			$url = $url_host;			
		}			
	}
//	}	
	
	$showAlert[] += $id;
	$ups[] += $num_ups;
	$downs[] += $num_down;
	$link = "<a href=". $url ." target=_blank >" . $title . "</a>";
	
	$locations[] = [
	     $title,
	     $lat,
	     $lon,
	     $local,
	     $color,
	     $link,
	     $id,
	     $quant,
	     array_sum($ups),
	     array_sum($downs),
	     $url,
	     $prio,
	     $num_trig,
	     $status,
	     $maint
	 ];
}
echo json_encode($locations);
?>
;
    
function initialize() {
   
	latlng = L.latLng(-9.95126,-63.9059);
	var map = L.map('map_canvas').setView([-9.95126,-63.9059], 12);
	    
		var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);
		
		for(var i = 0; i < locations.length; i++) {
			var a = locations[i];			
			var markers = new L.MarkerClusterGroup({
        		iconCreateFunction: function(cl) {     
            var layer = a[9];
            var cor = layer !== 0 ? 'red' : 'green';            
            return L.divIcon({ html: '<b>' + cl.getChildCount() + '</b>', className: 'mycluster-' + cor, iconSize: L.point(32, 32) });
        	},        			
			maxClusterRadius: 20, spiderfyOnMaxZoom: true, showCoverageOnHover: true, zoomToBoundsOnClick: false 
			});
		}			
		
	  // individual markers			
		var arr_markers = [];
		
		for (var i = 0; i < locations.length; i++) {				 

		   var a = locations[i];
			var cor;
	
			// avoid markers with same location
			var min = .999999;
			var max = 1.00001;    
			var offsetLat = a[1] * (Math.random() * (max - min) + min);
			var offsetLng = a[2] * (Math.random() * (max - min) + min);  			
			//var offsetLat = a[1];  			
			//var offsetLng = a[2];  			
		   
			if (a[4] == 0) { cor = '#97AAB3' };
			if (a[4] == 1) { cor = '#7499FF' };
			if (a[4] == 2) { cor = '#FFC859' };
			if (a[4] == 3) { cor = '#FFA059' };
			if (a[4] == 4) { cor = '#e97659' };
			if (a[4] == 5) { cor = '#e45959' };
			if (a[4] == 6) { cor = '#e33734' }; // host offline
			if (a[4] == 9) { cor = '#43B53C' }; // no trigger
			if (a[4] != 6 && a[14] == 1) { cor = '#f24f1d' }; // maintenance
			
			// host offline
			if (a[4] == 6 && a[13] == 1) {
				var options = { isAlphaNumericIcon: true, text:'<i style="margin-left: -4px;" class="fa fa-times-circle"></i>', iconShape: 'marker', borderColor: cor, backgroundColor: cor, textColor: '#fff'};				
			}	
			// maintenance
			else if (a[4] != 6 && a[14] == 1) {
				var options = { isAlphaNumericIcon: true, text:'<i style="margin-left: -4px;" class="fa fa-wrench"></i>', iconShape: 'marker', borderColor: cor, backgroundColor: cor, textColor: '#fff'};								
			}
			// no trigger
			else if (a[4] == 9) {										 	 		 	 			
				var options = { isAlphaNumericIcon: true, text:'<i style="margin-left: -4px;" class="fa fa-check-circle"></i>', iconShape: 'marker', borderColor: cor, backgroundColor: cor, textColor: '#fff'};
			}	
				
			else {
				var options = { isAlphaNumericIcon: true, text:a[12], iconShape: 'marker', borderColor: cor, backgroundColor: cor, textColor: '#fff'};				
			}		
			
		   var marker = L.marker([offsetLat, offsetLng], {icon: L.BeautifyIcon.icon(options), draggable: false}, {title: a[3]});
		   marker.l = a[8];  
		   

			if (a[11] != 0 && a[11] != 6 && a[11] != 9) {
				marker.bindPopup(a[5] + '<br>Triggers: '  + a[12]);								
			}
			else if (a[11] == 6 && a[13] == 1 ) {				
				marker.bindPopup(a[5] + '<br> Host Offline');								
			}
			else if (a[4] != 6 && a[14] == 1) {
				marker.bindPopup(a[5] + '<br> Maintenance');												
			}
			else {
				marker.bindPopup(a[5]);								
			}
			
			markers.addLayer(marker);
			
			//array to center
			arr_markers.push([offsetLat, offsetLng]);
		}

		map.addLayer(markers);
		
		//center map		
		var bounds = L.latLngBounds(arr_markers);
		map.fitBounds(bounds);
		//var group = new L.featureGroup(arr_markers);
		//map.fitBounds(group.getBounds());		
		
}
</script> 

<?php

	 //offline hosts 	 	 
	 if($contaRed != 0) {
		$sound = "../sound/Alarm1.wav";	 	
	 }
	 else { 
	 	$sound = "../sound/no_sound.wav";
	 }	
	 	
	 $offAtual = count($conta);
	 
?>

<script type="text/javascript">

function reloadPage() {
	
		$("#reload_page").click(function() {			
			window.location.href='map.php?groupid=<?php echo implode(",",$groupID); ?>';
		});		
		
		var reloadTimer = function(flag, interval) {
		if (flag === true) {
			clearInterval(metric.reloadId);
			var counter = interval;
			$("#countDownTimer").text(interval);
	
			metric.reloadId = setInterval(function() {
				counter--;
				$("#countDownTimer").text(counter);
	
				if (counter === 0) {					
					window.location.href='map.php?groupid=<?php echo implode(",",$groupID); ?>';
					counter = interval;
				}
	
			}, 1000);
		} else {
			clearInterval(metric.reloadId);
			$("#countDownTimer").text("");
		}
   	};			


	$(function($){
		
		var inter = localStorage.getItem('relInt');
		document.getElementById('reload_selecter').value = inter;
		
		if (inter > 0) {
			$("#reload_page").attr({
				"disabled" : "disabled"
			});

			reloadTimer(true, inter);

		} else {
			$("#reload_page").removeAttr("disabled");

			reloadTimer(false);
		}							
});
		
		$(function($) {
			$('#reload_selecter').change(function() {
								
				var selectVal = $(this).val();
				
				localStorage.setItem('relInt',selectVal);
				var inter = localStorage.getItem('relInt');
						
				window.location.href='map.php?groupid=<?php echo implode(",",$groupID); ?>';						

				if (selectVal != 0) {
					$("#reload_page").attr({
						"disabled" : "disabled"
					});

					reloadTimer(true, selectVal);

				} else {
					$("#reload_page").removeAttr("disabled");

					reloadTimer(false);
					window.location.href='map.php?groupid=<?php echo implode(",",$groupID); ?>';
				}
			});
		});			
}		 	  
</script>

<?php      	 	 	 
	 
	 if($off > 0 && $offAtual > $off) {
		 echo '<!--[if IE]>';
		 echo '<embed src="'.$sound.'" autostart="true" width="0" height="0" type="application/x-mplayer2"></embed>';
		 echo "<![endif]-->\n";   
		 // Browser HTML5    
		 echo '<audio preload="auto" autoplay>';
		 echo '<source src="'.$sound.'" type="audio/ogg"><source src="'.$sound.'" type="audio/mpeg">';
		 echo "</audio>\n";
	 }
?>

	<body onload="initialize(); reloadPage();" style="background:#e5e5e5;">
	
		<div id='container-fluid' class="col-md-12 col-sm-12"  style="margin-top: -40px; margin-bottom:-15px;" > 
			<div style="margin-top:2px;margin-bottom:1px;">
			<div style="float:left;"><a href="<?php echo $zabURL; ?>" target="_blank"><img src="../img/zabbix.png" alt="Zabbix" style="height:28px;"></img></a></div> 	
		   <div class="" id="date" style="color:#000; float:right; "><?php echo date("d F Y", time())." - "; echo date("H:i:s", time()); ?></div>	  
		   <?php echo $sel_grp; ?>  
			</div>
		</div>
		<?php
			if(empty($showAlert) OR count($showAlert) == 0) {
				echo '<div id="help" class="col-md-12 col-sm-12" style="display:block;">
							<div class="alert alert-danger" role="alert">
								<span>' .$labels['To view a host on the map enter your latitude and longitude in "Host inventory".'].'</span>
							</div>
						</div>';
			}										
		?>				
		<div id="map_canvas"></div>
		
		<!-- interval selector -->
		<div class="col-xs-3 col-sm-4 col-md-4 col-lg-1 form-group pull-right" style="float: right; width:125px; margin-top:8px;">
			<select id="reload_selecter" class="form-control pull-right">
				<option value="0"><?php echo _('Disabled'); ?></option>						
				<option value="30">30s</option>						
				<option value="45">45s</option>			
				<option value="60">60s</option>
				<option value="90">90s</option>
				<option value="120">120s</option>
				<option value="180">180s</option>
				<option value="300">300s</option>
			</select>
		</div>	
		<div>
			<button id="reload_page" type="button" class="btn btn-default pull-right" style="margin-top:8px;">
				<i class="glyphicon glyphicon-refresh"></i><text id="countDownTimer"></text>
			</button>
		</div>		
		<!-- interval selector -->								
	</body>
</html>

