<?php

require_once '../include/config.inc.php';
require_once('config.php');

//Access control
if(!$_COOKIE["zabdash_session"]) {
	header("location:index.php");
}

require_once 'lib/ZabbixApi.class.php';
use ZabbixApi\ZabbixApi;
$api = new ZabbixApi($zabURL.'api_jsonrpc.php', ''. $zabUser .'', ''. $zabPass .'');

//$dbHostsCount = DBselect( 'SELECT SUM(case when status = 0 then 1 else 0 end) AS active, SUM(case when status = 1 then 1 else 0 end) AS inactive, SUM(case when status = 3 then 1 else 0 end) AS template FROM hosts WHERE flags IN (0,4)');
//$hostsCount = DBFetch($dbHostsCount);
//$dbHosts = DBselect('SELECT h.hostid, h.name, h.status, h.snmp_available AS sa, h.snmp_disable_until AS sd, h.flags FROM hosts h, hosts_groups hg WHERE hg.groupid IN ("'.$groupsini.'") AND h.hostid = hg.hostid ORDER BY h.name ASC');

//$dbTrig = DBselect( 'SELECT COUNT(hostid) AS hc FROM hosts WHERE status = 1 AND flags = 0');
//$trigCount = DBFetch($dbTrig);	

//define if all hosts or groups
if(!isset($_REQUEST['grpids']) || $_REQUEST['grpids'] != 0 || $_REQUEST['grpids'] == "") {
	$todos = 0;
	if($initgroups != "") {		
		$arr_groups = array();
		$arr_groups = explode(",",$initgroups);
		$groupsini = implode(",", $arr_groups);	
		
		for($i=0;$i < count($arr_groups);$i++) {
			$grp[$i] = $arr_groups[$i]; 					
		}	
				
		//number of hosts groups
		$countHostsGroups = count($arr_groups);	
	
	//number of hosts
	$hostsCount = $api->hostGet(array(
		'output' => 'extend',
		'groupids' => [$grp[0],$grp[1],$grp[2],$grp[3],$grp[4]]
	));		
	
	$countHosts = count($hostsCount);	
	
	//active hosts
	$activeHosts =0;
	foreach($hostsCount as $a) {    			           
		$active = $a->status;
		if($active == 0) {
			$activeHosts += 1 ;
		}				            		
	}
	
	//inactive hosts
	$inactiveHosts = ($countHosts - $activeHosts);
	
	
	//get triggers
	$trigger = $api->triggerGet(array(
		'output' => 'extend',	
		'only_true' => '1',
		'active' => '1', // include trigger state active not active
		'selectHosts' => 1,
		'groupids' =>  [$grp[0],$grp[1],$grp[2],$grp[3],$grp[4]]							
	));	
	
	
	$triggerUnack = $api->triggerGet(array(
		'output' => 'extend',	
		'sortfield' => 'priority',
		'sortorder' => 'DESC',
		'only_true' => '1',
		'active' => '1', // include trigger state active not active
		'withUnacknowledgedEvents' => '1', 
		'expandDescription' => '1',
		'selectHosts' => 1,
		'groupids' =>  [$grp[0],$grp[1],$grp[2],$grp[3],$grp[4]]					
	));		
			
	}
}

else {
	$todos = 1;

	//number of hosts groups
	$hostsGroups = $api->hostgroupGet(array(
	'output' => 'extend'
	));	
	
	$countHostsGroups = count($hostsGroups);
//}

//number of hosts
$hostsCount = $api->hostGet(array(
	'output' => 'extend'
));		

$countHosts = count($hostsCount);	

//active hosts
$activeHosts =0;
foreach($hostsCount as $a) {    			           
	$active = $a->status;
	if($active == 0) {
		$activeHosts += 1 ;
	}				            		
}

//inactive hosts
$inactiveHosts = ($countHosts - $activeHosts);


//get triggers
$trigger = $api->triggerGet(array(
	'output' => 'extend',	
	'only_true' => '1',
	'active' => '1', // include trigger state active not active
	'selectHosts' => 1						
));	


$triggerUnack = $api->triggerGet(array(
	'output' => 'extend',	
	'sortfield' => 'priority',
	'sortorder' => 'DESC',
	'only_true' => '1',
	'active' => '1', // include trigger state active not active
	'withUnacknowledgedEvents' => '1', 
	'expandDescription' => '1',
	'selectHosts' => 1				
));

}


//number of users
$users = $api->userGet(array(
	'output' => 'extend'	
));

?>

<!DOCTYPE html>
<html>
<head>
 <title>ZabDash - Home </title>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="Pragma" content="public">           

 <link rel="icon" href="img/favicon.ico" type="image/x-icon" />
 <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />    
 <link href="css/bootstrap.css" rel="stylesheet">        		   
 <link rel="stylesheet" type="text/css" href="css/layout.css">
 <meta http-equiv="refresh" content= "600"/>
 
  <!-- this page specific styles 
 <link rel="stylesheet" href="css/compiled/index.css" type="text/css" media="screen" /> -->    

 <!--[if lt IE 9]>
   <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
 <![endif]-->
 	<script src="js/jquery.min.js"></script> 
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<!--   <link href="css/style-dash.css" rel="stylesheet" type="text/css" />    -->
 
 <!-- odometer -->
<link href="css/odometer.css" rel="stylesheet">
<script src="js/odometer.js"></script>		

<!-- Datatables -->	
<script src="js/media/js/jquery.dataTables.min.js"></script>
<link href="js/media/css/dataTables.bootstrap.css" type="text/css" rel="stylesheet" />
<script src="js/media/js/dataTables.bootstrap.js"></script>
<script src="js/extensions/Buttons/js/dataTables.buttons.min.js"></script>
<link href="js/extensions/Select/css/select.bootstrap.css" type="text/css" rel="stylesheet" />
<script src="js/extensions/Select/js/dataTables.select.min.js"></script>
 
<!-- <link href="less/style.less" rel="stylesheet"  title="lessCss" id="lessCss"> -->
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->         
<!-- <link href="fonts/fonts.css" rel="stylesheet" type="text/css" /> -->
  
<?php 
echo '<link rel="stylesheet" type="text/css" href="css/skin-material.css">'; 
echo '<link rel="stylesheet" type="text/css" href="css/style-material.css">';				
?>  	

<style type="text/css">
	@media screen and (min-width: 1201px) and (max-width: 2200px) {
  		#footer-bar {
    		margin-top: 5px;
    		height: 20px;
  	 	}
	}
	.triggersUnack_filter { display:none !important; }
</style>	

<link href="css/loader.css" type="text/css" rel="stylesheet" />

<script type="text/javascript">
	jQuery(window).load(function () {
		$(".loader").fadeOut("slow"); //retire o delay quando for copiar!  delay(1500).
		$("#container-fluid").toggle("fast");    
	});          
</script>	
</head>

<body>	 
<?php    
	$ano = date("Y");
	$month = date("Y-m");
	$hoje = date("Y-m-d");
?>
<div id="loader" class="loader"></div>    
<div class="container-fluid">  
<div class="site-holder">
<!-- top -->
<!-- .box-holder -->
<!-- .content -->
<div class="content animated fadeInBig corpo col-md-12 col-sm-12 align">
<div id='content-main' class="container-fluid1 align col-md-12 col-sm-12 row">      
    <!-- main-content 
   <div class="main-content masked-relative masked"> -->      
	<div id="panels" class="row" style="margin-top: 1%; margin-left: 0%; margin-right:-1%;">
		<!-- COLUMN 1 -->															
			  <div class="col-sm-3 col-md-3">
				  <div class="dashbox shad panel panel-default db-redx row stat">
								<div class="panel-left db-red row">					   	
						   			<i  style="vertical-align:middle; margin-top:18px;" class="fa fa-desktop fa-3x"></i>
						   	</div>
						   <div class="panel-right right" style='cursor:pointer;' onclick="window.open('../hosts.php');">
	         				<span class="chamado"><?php echo _('Hosts'); ?></span><br>
						     	<div id="odometer1" class="odometer" style="font-size: 25px;">   </div><p></p>
	            			<span class="date" title="Active/Inactive"><span style="font-weightx:bold; color:#e33734;"><?php echo "<span style='color:#429e47;'>".$activeHosts."</span><span class='date'> / </span> ". $inactiveHosts; ?></span></span>												
						   </div>
					 </div>
			  </div>
			  
			  <div class="col-sm-3 col-md-3">
				 <div class="dashbox shad panel panel-default db-bluex row stat">
							<div class="panel-left db-blue">					   	
					   			<i  style="vertical-align:middle; margin-top:18px;" class="fa fa-warning fa-3x"></i>
					   	</div>										 
					   <div class="panel-right right " style='cursor:pointer;' onclick="window.open('../zabbix.php?action=problem.view');">	
         				<span class="chamado"><?php echo _('Triggers'); ?></span><br>
							<div id="odometer2" class="odometer" style="font-size: 25px;">   </div><p></p>
         				<span class="date" title="Ack/Unack"><span style="font-weight:boldx; color:#e33734;"><?php echo "<span style='color:#429e47;'>".(count($trigger) - count($triggerUnack))."</span><span class='date'> / </span> ".count($triggerUnack); ?></span></span>
					   </div>
				 </div>
			  </div>																		
      								
			  <div class="col-sm-3 col-md-3">
				 <div class="dashbox shad panel panel-default db-purplex row stat">

			   		<div class="panel-left db-purple">					   	
			   			<i  style="vertical-align:middle; margin-top:18px;" class="fa fa-sitemap fa-3x"></i>
				   	</div>	
					   <div class="panel-right right" style='cursor:pointer;' onclick="window.open('../hostgroups.php');">
         				<span class="chamado"><?php echo _('Hosts groups'); ?></span><br>
							<div id="odometer3" class="odometer" style="font-size: 25px;">   </div><p></p>
         				<span class="date"><b>&nbsp;</b></span>
					   </div>										   
				 </div>
			  </div>
			  <div class="col-sm-3 col-md-3">
				 <div class="dashbox shad panel panel-default db-orangex row stat">
			   		<div class="panel-left db-orange">					   	
			   			<i  style="vertical-align:middle; margin-top:18px;" class="fa fa-users fa-3x"></i>
					   </div>	
			   		<div class="panel-right right" style='cursor:pointer;' onclick="window.open('../zabbix.php?action=user.list&ddreset=1');">	
         				<span class="chamado"><?php echo _('Users'); ?></span><br>                        				
							<div id="odometer4" class="odometer" style="font-size: 25px;">   </div><p></p>
         				<span class="date"><b>&nbsp;</b></span>
					   </div>
				 </div>
			  </div>																	                          				                           							
	</div>        
                

<script type="text/javascript" >
window.odometerOptions = {
   format: '( ddd).dd'
};

setTimeout(function(){
    //odometer1.innerHTML = <?php echo ($hostsCount['active'] + $hostsCount['inactive']); ?>;
    odometer1.innerHTML = <?php echo $countHosts; ?>;
    odometer2.innerHTML = <?php echo count($trigger); ?>;
    odometer3.innerHTML = <?php echo $countHostsGroups; ?>;
    odometer4.innerHTML = <?php echo count($users); ?>;
}, 1000);

</script> 
  
<div id="widgets2" class="row" style="margin-bottom: -20px !important;">

	<div class="col-sm-12 col-md-12 align" style="float:left; margin-left: 0px; display: none;"> 
		<?php
			//include ("charts/triggers_severity_bar.inc.php");
		?> 
	</div>	
	 				              
	<div class="col-sm-6 col-md-6 align" style="float:left; margin-left: 0px;"> 	 				              
	   <div id="tickets_status" class="widget2 widget-table action-table striped card1" >
	      <div class="widget-header">                 
	      	<h3><i class="fa fa-list" style="margin-left:7px;">&nbsp;&nbsp;&nbsp;</i><?php echo $labels['Triggers by host - Top 10']; ?></h3>
	      	 <span class=""></span>               
	      </div> 
	      <!-- /widget-header -->      
	      <div id="graflinhas1" class="col-md-12 col-sm-12" style='height:400px !important; background:#fff;'>	 			
					<?php						
						include ("charts/triggers_hosts.inc.php");
					?> 	 						            
			</div> 
		</div>
	</div>
	
	<div class="col-sm-6 col-md-6 align" style="float:left; margin-left: 0px; height:400px !important; "> 	 				              
	   <div id="triggers_severity" class="widget2 widget-table action-table striped card1" >
			<div class="widget-header">                 
	      	<h3><i class="fa fa-pie-chart" style="margin-left:7px;">&nbsp;&nbsp;&nbsp;</i><?php echo $labels['Triggers by Severity']; ?></h3>
	      	 <span  class=""></span>               
	      </div>
	      <!-- /widget-header -->      
	      <div id="severity1" style="height:400px !important; background:#fff;">
	      	<div id="severity" class="align" style="height:280px !important; background:#fff;">	      	 			
					<?php
						include ("charts/triggers_severity_tab.inc.php");
					?> 	 						            
				</div>
				<div id="legend" class="col-sm-12 col-md-12 align" style="margin-top:35px; text-align:center; display:none;">
		  		  <span class="label label-primary" style="background: #97AAB3 !important; border: 1px solid #97AAB3;"><?php echo _('Not classified'); ?></span>
		  		  <span class="label label-success" style="background: #7499FF !important; border: 1px solid #7499FF;"><?php echo _('Information'); ?></span>
	           <span class="label label-success" style="background: #FFC859 !important; border: 1px solid #FFC859; width:70px;"><?php echo _('Warning'); ?></span>
	           <span class="label label-danger"  style="background: #FFA059 !important; border: 1px solid #FFA059; width:70px;"><?php echo _('Average'); ?></span>
	           <span class="label label-warning" style="background: #E97659 !important; border: 1px solid #E97659; width:70px;"><?php echo _('High'); ?></span>
	           <span class="label label-warning" style="background: #B10505 !important; border: 1px solid #B10505; width:70px;"><?php echo _('Disaster'); ?></span>	           		  		  
				</div>  
			</div>  
		</div>
	</div>

</div>

<div id="widgets" class="row" style="margin-top: -25px;">	

	<div class="col-sm-12 col-md-12 align" style="margin-left: 0px;"> 	 				              
	   <div id="time" class="widget2 widget-table action-table striped card1" >
	      <div class="widget-header">                 
	      	<h3><i class="fa fa-calendar" style="margin-left:7px;">&nbsp;&nbsp;&nbsp;</i><?php echo $labels['Triggers last 7 days']; ?></h3>
	      	 <span  class=""></span>               
	      </div> 
	      <!-- /widget-header -->      
	      <div id="triggers_time" style='height:305px !important; background:#fff; '>	 			
					<?php
						include ("charts/triggers_time.inc.php");
					?> 	 						            
			</div> 
		</div>
	</div>
	
	<div class="col-sm-12 col-md-12 align" style="margin-left: 0px;"> 	 				              
	   <div id="tickets_status" class="widget2 widget-table action-table striped card1" style="background:#fff !important;" >
	      <div class="widget-header">                 
	      	<h3><i class="fa fa-list" style="margin-left:7px;">&nbsp;&nbsp;&nbsp;</i><?php echo $labels['Unacknowledged Triggers'];// ."   (". count($triggerUnack) .")"; ?></h3>
	      	 <span  class=""></span>               
	      </div> 
	      <!-- /widget-header -->      
	      <div id="unack" style='height:350px !important; background:#fff !important; '>	 			
					<?php
						include ("charts/triggers_unack.inc.php");
					?> 	 						            
			</div> 
		</div>
	</div>		

</div> <!-- end widgets -->                   		

</div> 
 
<script type="text/javascript">

	function scrollWin()
	{
		$('html, body').animate({ scrollTop: 0 }, 'slow');
	}

	
	$(document).ready(function() {
		
	    $('#triggersUnack').DataTable({
			  "select": false,
			  "paging":   true,
	        "ordering": true,
	        "info":     true,
	        "filter":	false,	 
	        "lengthChange":	false,	 	               
	        "order": [[ 0, "desc" ]],
	        pagingType: "full_numbers",        
			  displayLength: 20,
	        lengthMenu: [[15,25, 50, 100, -1], [15,25, 50, 100, "All"]],	    	    	   
	    
	    });
	});
</script>	

<div id="go-top" class="go-top" onclick="scrollWin()">
   <i class="fa fa-chevron-up"></i>&nbsp; Top     							    
</div> 
 	
</div> <!-- end content -->   

</div>
</div>
<!-- /.site-holder -->

<script src="js/jquery-ui.min.js" type="text/javascript"></script> 
<!--<script src="js/jquery.accordion.js"></script>            
<script src="js/bootstrap-dropdown.js"></script>
<script src="js/jquery.easy-pie-chart.js"></script> 
<script src="js/jquery.address-1.6.min.js"></script>-->

<!--<script src="js/bootstrap-switch.js"></script> -->
<script src="js/highcharts.js" type="text/javascript" ></script>
<script src="js/highcharts-3d.js" type="text/javascript" ></script>
<script src="js/modules/exporting.js" type="text/javascript" ></script>
<script src="js/modules/no-data-to-display.js" type="text/javascript" ></script>

<script src="js/theme.js"></script>         
<script src="js/jquery.jclock.js"></script>
<!--<script src="js/widgets.js"></script>

<script src="js/raphael.2.1.0.min.js"></script>
<script src="js/morris.min.js"></script>-->

<!-- Highcharts export xls, csv 
<script src="js/export-csv.js"></script>
-->
<!-- Remove below two lines in production -->  
<!--<script src="js/theme-options.js"></script>       
<script src="js/core.js"></script>-->
</body>
</html>
