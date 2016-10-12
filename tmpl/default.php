<?php

/**
 * @version     1.0.0
 * @package     mod_eiko_chart
 * @copyright   Copyright (C) 2015 by Ralf Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ralf Meyer <webmaster@feuerwehr-veenhusen.de> - http://einsatzkomponente.de
 */


defined('_JEXEC') or die;


$document =	JFactory::getDocument();
$document->addScript('https://www.google.com/jsapi'); 

$app          = JFactory::getApplication();
$filter = date("Y");

$selectedYear_old = $app->getUserStateFromRequest( "com_einsatzkomponente.selectedYear", 'year', $filter);


//echo $selectedYear;

		$database			= JFactory::getDBO();
		$query = 'SELECT Year(date1) as id, Year(date1) as title FROM `#__eiko_einsatzberichte` GROUP BY title';
		$database->setQuery( $query );
		$totalyears = $database->loadObjectList();
		$totalyears_count = count($totalyears);
		$firstyear = $totalyears[0]->title;
		$lastyear = $totalyears[$totalyears_count-1]->title;

if ($params->get( 'selectedYear', '2016' ) == '-- alle Jahre --' or $selectedYear_old == '9999') :

		$database			= JFactory::getDBO();
		$query = 'SELECT COUNT(r.data1) as total,r.data1,rd.marker,rd.title as einsatzart FROM #__eiko_einsatzberichte r ';
		$query.='JOIN #__eiko_einsatzarten rd ON r.data1 = rd.id WHERE r.date1 LIKE "2%" AND (r.state = "1" OR r.state = "2") AND rd.state = "1"';
	          $query.=' GROUP BY r.data1 ' ;
		$database->setQuery( $query );
		$total = $database->loadObjectList();
		$title = 'Einsatzstatistik für die Jahre von '.$firstyear.' bis '.$lastyear.'';
	
	else:
		$selectedYear = $params->get( 'selectedYear', '2016' );
		
		if ($params->get( 'curyear', '1' )) :
		$selectedYear = date("Y");
		endif;
		
		if ($params->get( 'filteryear', '0' )) :
		$filter = ($app->getUserStateFromRequest('com_einsatzkomponente.filter', 'filter', array(), 'array')); 
		if (isset($filter['year'])) :
		$selectedYear = $filter['year'];
		endif;
		endif;

		if ($selectedYear_old AND !$filter) : 		
		$selectedYear = $selectedYear_old;
		if ($params->get( 'curyear', '1' )) :
		$selectedYear = date("Y");
		endif;
		endif;

		if ($selectedYear == '') : 		
		$selectedYear = date("Y");
		endif;
		
		
		$database			= JFactory::getDBO();
		$query = 'SELECT COUNT(r.data1) as total,r.data1,rd.marker,rd.title as einsatzart FROM #__eiko_einsatzberichte r ';
		$query.='JOIN #__eiko_einsatzarten rd ON r.data1 = rd.id WHERE Year(r.date1) LIKE "'.$selectedYear.'" AND (r.state = "1" OR r.state = "2") AND rd.state = "1"';
	          $query.=' GROUP BY r.data1 ' ;
		$database->setQuery( $query );
		$total = $database->loadObjectList();
		$title = 'Einsatzstatistik für das Jahr '.$selectedYear.''; 

endif;		

	
	//echo $selectedYear.'</br>';
	//echo $selectedYear_old.'</br>';
		
$zufall = rand(1,100000);

//print_r ($total);

$Column = '';
$Colors = '';
for($i=0; $i < count($total); $i++)
   {
   $Column.='["'.$total[$i]->total.' x '.$total[$i]->einsatzart.'",'.$total[$i]->total.'],';
   $Colors.='"'.$total[$i]->marker.'",';
   }
   $Column=substr($Column,0,strlen($Column)-1);
   $Colors=substr($Colors,0,strlen($Colors)-1);
  

   

?>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]}); 
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
		["Einsatzart","Anzahl"],
         <?php echo $Column;?>
        ]);
		var options = {
          title: '<?php echo $title;?>',
          is3D: <?php echo $params->get( 'chart', true );?>,
		  width:<?php echo $params->get( 'pwidth', '630' );?>,
		  height:<?php echo $params->get( 'pheight', '300' );?>,
		  backgroundColor: 'transparent',
		  legend : { position : '<?php echo $params->get( 'legend_pos', 'right' );?>', textStyle: {color: '<?php echo $params->get( 'legend_color', '#ffffff' );?>', fontSize: <?php echo $params->get( 'legend_size', 10 );?>} },
		  colors: [<?php echo $Colors;?>],
		  titleTextStyle: {color: '<?php echo $params->get( 'title_color', '#ffffff' );?>',fontSize:<?php echo $params->get( 'title_size', 16 );?>},
		  pieSliceText: 'percentage',
		  pieSliceBorderColor : "#eeeeee",
		  pieSliceTextStyle: {color: '<?php echo $params->get( 'slice_text_color', '#ffffff' );?>'}

		  };

        var chart = new google.visualization.PieChart(document.getElementById('chart_div_<?php echo $zufall;?>'));

        chart.draw(data,options);
      }
    </script>
	
	<style type="text/css">
	<?php echo $params->get('css');?>
	</style>

	<div class="eiko_chart_<?php echo $params->get( 'moduleclass_sfx', '');?>" id="chart_div_<?php echo $zufall;?>" style="margin-left: auto;margin-right: auto;width: <?php echo $params->get( 'width', '600px' );?>px; height: <?php echo $params->get( 'pheight', '300px' );?>;"></div>

	<?php
	
	
	
