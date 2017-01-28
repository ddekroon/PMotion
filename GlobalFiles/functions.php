<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getGetVariableWithDefault($name = '', $default = 0) {
	$toReturn = filter_input(INPUT_GET, $name, FILTER_SANITIZE_STRING);
	
	if(!empty($toReturn) && $toReturn != '') {
		return $toReturn;
	}

	return $default;
}
?>
