<?php
/**
 * @version     1.0.0
 * @package     mod_eiko_chart
 * @copyright   Copyright (C) 2013 by Ralf Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ralf Meyer <webmaster@feuerwehr-veenhusen.de> - http://einsatzkomponente.de
 */
defined('_JEXEC') or die;

//require_once( dirname(__FILE__).'/helper.php' ); 

    

$width            = $params->get( 'width', '0' );
$height           = $params->get( 'height', '0' );

require JModuleHelper::getLayoutPath('mod_eiko_chart', $params->get('layout', 'default'));
?>