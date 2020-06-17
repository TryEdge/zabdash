<?php

if($todos == 0) {
	if($initgroups != "") {
		$arr_groups = array();
		$arr_groups = explode(",",$initgroups);
		$groupsini = implode(',', $arr_groups);
		
		$triggerUnack1 = $api->triggerGet(array(
			'groupids' => $groupsini,
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
}

else {

	$triggerUnack1 = $api->triggerGet(array(
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
	
echo "				
		<table id='triggersUnack' class='box table table-striped table-hover table-condensed' border='0' style='background:#fff; padding-left: 5px; padding-right: 5px;'>
			<thead>
				<tr>
					<th style='text-align:center; width:15%;'>". _('Last change')."</th>
					<th style='text-align:center;'>". _('Severity')."</th>
					<th style='text-align:center;'>". _('Status')."</th>				
					<th style='text-align:center;'>". _('Host')."</th>
					<th style='text-align:center;'>". _('Description')."</th>
				</tr>								
			</thead>
		<tbody> ";
	
	
 foreach($triggerUnack1 as $tu) {
 	
  	if($tu->value == 0) { $priority = 9; $statColor = '#34AA63';} 	
  	else { $priority = $tu->priority; $statColor = '#E33734'; } 	 
	    

	echo "<tr>";			            
		echo "<td style='text-align:center; vertical-align:middle !important;' data-order=".$tu->lastchange.">".from_epoch($tu->lastchange)."</td>";				            
		echo "<td style='text-align:left; vertical-align: middle !important;'>
					<div class='hostdiv nok". $priority ." hostevent trig_radius truncate' style='height:21px !important; margin-top:0px; !important;' onclick=\"window.open('".$zabURL."zabbix.php?action=problem.view&page=1&filter_hostids[]=". $tu->hosts[0]->hostid. "&filter_show=1&filter_application=&filter_name=&filter_severity=0&filter_inventory[0][field]=type&filter_inventory[0][value]=&filter_evaltype=0&filter_tags[0][tag]=&filter_tags[0][operator]=0&filter_tags[0][value]=&filter_show_tags=3&filter_tag_name_format=0&filter_tag_priority=&filter_unacknowledged=1&filter_set=1')\">
						<p class='severity'>". _(get_severity($tu->priority)) ."</p>									
					</div>
				</td>";				            
		echo "<td style='text-align:center; vertical-align: middle !important; color:".$statColor." !important;'>"._(set_status($tu->value))."</td>";				            
		echo "<td style='text-align:left; vertical-align: middle !important;'><a href='../zabdash/host_detail.php?hostid=".$tu->hosts[0]->hostid."' target='_blank'>". get_hostname($tu->hosts[0]->hostid)."</a></td>";
		echo "<td style='text-align:left; vertical-align: middle !important;'>".$tu->description."</td>";				            
	echo "</tr>";			            
		
 }

echo "</tbody>
			</table>\n";		

?>