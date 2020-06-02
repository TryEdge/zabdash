<?php

//Access control
if(!$_COOKIE["zabdash_session"]) {
	header("location:index.php");
}

require_once '../include/config.inc.php';
require_once '../include/hosts.inc.php';
require_once '../include/actions.inc.php';

include('config.php');

require_once 'lib/ZabbixApi.class.php';
use ZabbixApi\ZabbixApi;

$api = new ZabbixApi($zabURL.'api_jsonrpc.php', ''. $zabUser .'', ''. $zabPass .'');

//check version
if(ZABBIX_EXPORT_VERSION >= '4.0'){
	$grps = 'hstgrp';
}
else {
	$grps = 'groups';
}
	
$dbGroups = DBselect( 'SELECT * FROM '.$grps.' WHERE groupid <> 1 ORDER BY name ASC');


if(isset($_REQUEST['sel']) && $_REQUEST['sel'] != '' && $_REQUEST['sel'] == 1) {
		
	$group = $_POST['groupid'];
	$groupID = explode(",",$group);
	
	if(in_array(-1, $groupID)) {		
		$include = 0;				
	}
	
	else {		
		$include = 1;
	}
}	

else {
	$include = 2;	
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Language" content="pt-br">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--<meta http-equiv='refresh' content='120'>-->	
	<title>Zabbix Hosts</title>	
	
	<link rel="icon" href="img/favicon.ico" type="image/x-icon" />
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/font-awesome.css" rel="stylesheet">
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	
	<link href="inc/select2/select2.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="inc/select2/select2.js" language="javascript"></script>	
	
	<script src="js/media/js/jquery.dataTables.min.js"></script>
	<link href="js/media/css/dataTables.bootstrap.css" type="text/css" rel="stylesheet" />
	<script src="js/media/js/dataTables.bootstrap.js"></script>
	<script src="js/extensions/Buttons/js/dataTables.buttons.min.js"></script>
	
<!--	<script src="js/extensions/Select/js/dataTables.select.min.js"></script>
	<link href="js/extensions/Select/css/select.bootstrap.css" type="text/css" rel="stylesheet" />-->
	
	<link href="css/loader.css" type="text/css" rel="stylesheet" />
	
	<script type="text/javascript">
	 jQuery(window).load(function () {
		$(".loader").fadeOut("slow"); //retire o delay quando for copiar!  delay(1500).
		$("#container-fluid").toggle("fast");    
	});          
	</script>
</head>

<body>
<div id="loader" class="loader"></div>
 <div class='container-fluid col-md-11 col-sm-11' style='margin-right: 30px;'>
	<div class="row" style="margin-top:0px; margin-bottom: 70px; float:none; margin-right:auto; margin-left:auto; text-align:center;">

	<form id="form1" name="form1" class="form_rel" method="post" action="hosts_disp.php?sel=1" style="margin-top:30px; margin-bottom: -10px;" onchange='javascript:form1.submit();'>
	<!-- <label>Selecione um ou mais Grupos:</label><br> -->
		<select id='groupid' name='groupid' style='width: 300px; height: 27px;' autofocus data-placeholder="">
			<option value='-2'> <?php echo $labels['Select group']; ?> </option>
			<option value='-1'> <?php echo _('All'); ?> </option>
			<?php
				while ($groups = DBFetch($dbGroups)) {
					echo "<option value='".$groups['groupid']."'>".$groups['name']."</option>\n";									
				}											
			?>
		</select><br><br><p>	
	</form>
	<?php 		
	
		if($include == 0) {				
			//include('disp.php');							
		}
					
		if($include == 1) {
			include('disp.php');
		}					
	?>
	</div>
</div>
		
	<script type="text/javascript">
		$("#groupid").select2({
			placeholder: "<?php echo $labels['Select group']; ?>",
			allowClear: false	  
		});
	</script>

</body>
</html>
