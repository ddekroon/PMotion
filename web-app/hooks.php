<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$app->hook('slim.before.dispatch', function () use ($app) {
	$app->render('views/header.php');
});
  
$app->hook('slim.after.dispatch', function () use ($app) {
	$app->render('views/footer.php');
});