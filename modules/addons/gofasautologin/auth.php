<?php
/**
 * Módulo Gofas Auto Login para WHMCS
 * @author		Gofas Software
 * @see			https://gofas.net/?p=10265
 * @copyright	2020 https://gofas.net
 * @license		https://gofas.net?p=9340
 * @support		https://gofas.net/?p=3543
 * @version		4.0.0
 */

$actual_link		= (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if($error and stripos( $actual_link, '/configaddonmods') === false){
	try {
		Capsule::table('tblconfiguration')->where('setting','=','gallocalkey')->delete();
	}
	catch (\Exception $e){
   		$e->getMessage();
	}
	die($error);
}
if(!$error and $_REQUEST['client'] and $_REQUEST['hash']){
	$secretKey = 'AS9WNMS9829W922SDTY9MS9M9M290FJNGIO9DJKJK';
	foreach( Capsule::table('tbladdonmodules') -> where('module', '=', 'gofasautologin') -> get( array( 'setting', 'value') ) as $settings ){
		$setting[$settings->setting] = $settings->value;
	}
	foreach ( Capsule::table('tblclients')->where('id', $_REQUEST['client']) -> get() as $client_){
			$client_email = $client_->email;
			$client_id = $client_->id;
			$client_firstname = $client_->firstname;
			$client_lastname = $client_->lastname;
			$client_uuid = $client_->uuid;
	}
	$hash_comp = $client_email.$client_id.$client_firstname.$client_lastname.$client_uuid.$secretKey;
	$hash = sha1($hash_comp);
	
	if((string)$_REQUEST['hash'] === (string)$hash){
		if($_REQUEST['path']){
			$path = $_REQUEST['path'];
		}
		
		if($_REQUEST['invoice']){
			$path = 'viewinvoice.php?id='.$_REQUEST['invoice'];
		}
		if($_REQUEST['ticket']){
			$ticket = localAPI( 'GetTicket', array('ticketid'=>$_REQUEST['ticket']), false );
			$path = 'viewticket.php?tid='.$ticket['tid'].'&c='.$ticket['c'];
		}
		$sso = localAPI('CreateSsoToken',array('client_id'=>(int)$_REQUEST['client'],'destination'=>'sso:custom_redirect','sso_redirect_path'=>$path), (int)$setting['admin']);
		
		if($setting['debug']){
			logModuleCall( 'gofasautologin', 'access_link', array('module_version'=>'4.0.0','request'=>$_REQUEST,'client_'=>$client_,'ticket'=>$ticket, 'path'=>$path, 'admin'=>$setting['admin'] ), false, array('hash'=>$hash,'sso'=>$sso), (int)$setting['admin']);
		}
		
		if((string)$sso['result'] === (string)'success'){
			//if($setting['debug']){}
			header('Location: '.$sso['redirect_url']);
		}
		if((string)$sso['result'] === (string)'error'){
			echo 'Error: '.$sso['message'];
		}
	}
}