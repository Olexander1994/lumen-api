<?php

$app->group(['prefix' => 'api'], function($app) {
	
	$app->post('authorize_guest','ApiController@authorize_guest');

	$app->post('status_aps','ApiController@status_aps');
	 
	$app->post('ap_reboot', 'ApiController@ap_reboot');

	$app->post('ap_provising', 'ApiController@ap_provising');
});