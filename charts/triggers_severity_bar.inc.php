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

$severity = array('Disaster' => 0,'High' => 0,'Average' => 0,'Warning' => 0,'Information' => 0,'Not classified' => 0);
$colors = array('Disaster' => '#df4e4c','High' => '#E97659','Average' => '#FFA059','Warning' => '#FFC859','Information' => '#7499FF','Not classified' => '#97AAB3');

//krsort($priori);

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

echo "<table class='tablestat col-md-12 col-sm-12' style='table-layout:fixed;'>\n";
echo "<tr>\n";

for($i=0; $i < count($names); $i++) {      
	echo "<td style='text-align:center; height: 50px; background-color:".$colorsCod[$i].";'><span class='statbar' >".$values[$i]."</span><p><span class='barsev'>". _($names[$i])."</span></td>\n";
}

echo "</tr>\n";
echo "</table>\n";

/*
$keys = array_fill_keys(array('Article','Wattage','Dimmable','Type','Foobar'), ''); // wanted array with empty value
$allkeys = array_replace($keys, array_intersect_key($values, $keys));    // replace only the wanted keys


for($i=0; $i < count($names); $i++) {      
	echo "{value: ". percent($values[$i],$conta).", label: '". _($names[$i])."'},";
}*/

//disaster #B10505 
//high    #E97659
//average #FFA059
//warn #FFC859
//info #59DB8F
//not classified #97AAB3
//ok #4BAC64

?>


    
