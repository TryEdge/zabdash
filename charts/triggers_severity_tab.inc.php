<?php

$triggersPr = $api->triggerGet(array(
	'output' => 'extend',	
	'sortfield' => 'priority',
	'sortorder' => 'DESC',
	'only_true' => '1',
	'active' => '1'		
));	

foreach($triggersPr as $t) {    			           
	$valores[] = $t->priority;			            		
 }

$contagem = array_count_values($valores);
	
foreach($contagem AS $numero => $vezes) {
		
	$priori[get_severity($numero)] = $vezes;
}	

$conta = count($valores);

# trigger severities ids - inverted
$severities = array(5,4,3,2,1,0);

$severity = array('Disaster' => 0,'High' => 0,'Average' => 0,'Warning' => 0,'Information' => 0,'Not classified' => 0);
$colors = array('Disaster' => '#E45959','High' => '#E97659','Average' => '#FFA059','Warning' => '#FFC859','Information' => '#7499FF','Not classified' => '#97AAB3');

$arrDiff = array_diff_key($severity,$priori);

$priority = array_merge($priori, $arrDiff);

//replace keys in triggers order
$keys = array_fill_keys(array('Disaster','High','Average','Warning','Information','Not classified'), ''); // wanted array with empty value
$allkeys = array_replace($keys, array_intersect_key($priority, $keys));    // replace only the wanted keys

$priority = $allkeys;

$names = array_keys($priority);
$values = array_values($priority);

$arrCor = array_intersect_key($colors,$priori);


$colorsCod = array_values($colors);

echo "<div style='height: 25px;'></div>\n";
echo "<table id='tablestat' class='tablestat1 col-md-8 col-sm-8' style='table-layout:fixed;'>\n";

for($i=0; $i < count($names); $i++) {      
	echo "<tr>\n";
	echo "<td style='text-align:center; height: 52px; background-color:".$colorsCod[$i].";'><span class='statbar' ><a href='../zabbix.php?action=problem.view&page=1&filter_show=1&filter_application=&filter_name=&filter_severity=".$severities[$i]."&filter_inventory%5B0%5D%5Bfield%5D=type&filter_inventory%5B0%5D%5Bvalue%5D=&filter_evaltype=0&filter_tags%5B0%5D%5Btag%5D=&filter_tags%5B0%5D%5Boperator%5D=0&filter_tags%5B0%5D%5Bvalue%5D=&filter_show_tags=3&filter_tag_name_format=0&filter_tag_priority=&filter_show_opdata=0&filter_show_timeline=1&filter_set=1' target='_blank'>".$values[$i]."</a></span></td>";
	echo "<td style='text-align:left; background-color:".$colorsCod[$i].";'> <span class='barsev'>&nbsp; &nbsp;". _($names[$i])."</span></td>\n";
	echo "</tr>\n";
}

echo "</table>\n";

/*
$keys = array_fill_keys(array('Article','Wattage','Dimmable','Type','Foobar'), ''); // wanted array with empty value
$allkeys = array_replace($keys, array_intersect_key($values, $keys));    // replace only the wanted keys

desastre
https://servidor/zabbix/zabbix.php?action=problem.view&page=1&filter_show=1&filter_application=&filter_name=&filter_severity=5&filter_inventory%5B0%5D%5Bfield%5D=type&filter_inventory%5B0%5D%5Bvalue%5D=&filter_evaltype=0&filter_tags%5B0%5D%5Btag%5D=&filter_tags%5B0%5D%5Boperator%5D=0&filter_tags%5B0%5D%5Bvalue%5D=&filter_show_tags=3&filter_tag_name_format=0&filter_tag_priority=&filter_show_opdata=0&filter_show_timeline=1&filter_set=1

*/

//disaster #B10505   - df4e4c  - E45959
//high    #E97659
//average #FFA059
//warn #FFC859
//info #59DB8F
//not classified #97AAB3
//ok #4BAC64

?>


    
