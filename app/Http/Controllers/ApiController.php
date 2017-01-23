<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Unifi;

class ApiController extends Controller {
	
	public function authorize_guest(Request $request) {
 
		$unifi = new Unifi('', '', '', $request->input('site'));
          $unifi->login();
          $data = $unifi->authorize_guest($request->input('mac'), $request->input('global_timeout'), $request->input('up'), $request->input('down'), $request->input('bytes'), $request->input('ap_mac'));
          $unifi->logout();
 
		return response()->json($data);
	}
	
	public function status_aps(Request $request) {
		
		$unifi = new Unifi('', '', '', $request->input('site'));
          $unifi->login();
          $data = $unifi->list_aps();
          $unifi->logout();
		
		return response()->json($data);
	}
	
	public function ap_reboot(Request $request) {
	
		$login = 'admin';
		$password = 'wMxzS5w';
		
		$connection = ssh2_connect($request->input('ip_address'), $request->input('port'));

          $auth = @ssh2_auth_password($connection, $login, $password);

          if ($auth === false) {
               return $auth;
          }

          if (!ssh2_exec($connection, 'reboot')) {
               return false;
          }

          return true;
		
	}
	
	public function ap_provising (Request $request) {
		
		$login = 'admin';
		$password = 'wMxzS5w';
		
		$prepair = [
               'wget -O /tmp/uap.sh http://'.$request->input('server_ip_address').'/uap/config/uap.sh',
               'wget -O /tmp/mac.sh http://'.$request->input('server_ip_address').'/uap/config/rm_mac.sh',
               'chmod +x /tmp/uap.sh',
               'chmod +x /tmp/mac.sh',
          ];
		
		$connection = ssh2_connect($request->input('ip_address'), $request->input('port'));

          $auth = @ssh2_auth_password($connection, $login, $password);

          if ($auth === false) {
               return $auth;
          }

          if (!ssh2_exec($connection, implode(';' , $prepair))) {
               return false;
          } 
		 
		sleep(1);
		
		if (!ssh2_exec($connection,'sh /tmp/uap.sh http://'.$request->input('server_ip_address').'/uap/get_mac/'.$request->input('id').' &')) {
			return false;
		}
          
		sleep(1);
		
		if (!ssh2_exec($connection,'sh /tmp/mac.sh &')) {
			return false;
		}

          return true;
		
	}

}
