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
use WHMCS\Database\Capsule;
if(!function_exists('gofasautologin_hook')){function gofasautologin_hook($vars){
	$secret_key = 'AS9WNMS9829W922SDTY9MS9M9M290FJNGIO9DJKJK';  // Altere aqui
	$merge_fields = array();
	foreach( Capsule::table('tbladdonmodules') -> where('module', '=', 'gofasautologin') -> get( array( 'setting', 'value') ) as $params_ ){
		$params[$params_->setting] = $params_->value;
	}
	// Invoice
	if((strpos($vars['messagename'], "Invoice") !== false || strpos($vars['messagename'], "Credit Card") !== false) and (strpos($vars['messagename'], "Credit Card Expiring Soon") === false)){
	//if(strpos($vars['messagename'], "Invoice") !== false){
		$invoice = localAPI( 'GetInvoice', array('invoiceid'=>$vars['relid']),false);
		foreach ( Capsule::table('tblclients')->where('id',$invoice['userid'])->get() as $client_){
			$client_email = $client_->email;
			$client_id = $client_->id;
			$client_firstname = $client_->firstname;
			$client_lastname = $client_->lastname;
			$client_uuid = $client_->uuid;
		}
		foreach( Capsule::table('tblconfiguration')->where('setting','=','SystemURL')->get(array('value')) as $system_url_){
			$system_url = $system_url_->value;
		}
		$hash_comp = $client_email.$client_id.$client_firstname.$client_lastname.$client_uuid.$secret_key;
		$hash = sha1($hash_comp);
		if(strpos($params['invoice_link'],'invoice_link') !== false){
			$merge_fields['invoice_link'] = $system_url.'modules/addons/gofasautologin/auth.php?client='.$client_id.'&hash='.$hash.'&invoice='.$vars['relid'];
		}
		if(strpos($params['invoice_link'],'login_link') !== false){
			$merge_fields['login_link'] = $system_url.'modules/addons/gofasautologin/auth.php?client='.$client_id.'&hash='.$hash.'&invoice='.$vars['relid'];
		}
	}
	// Ticket
	if( strpos( $vars['messagename'], "Ticket") !== false){
		$ticket = localAPI( 'GetTicket', array('ticketid'=>$vars['relid']), false );	
		foreach ( Capsule::table('tblclients')->where('id',$ticket['userid'])->get() as $client_){
			$client_email = $client_->email;
			$client_id = $client_->id;
			$client_firstname = $client_->firstname;
			$client_lastname = $client_->lastname;
			$client_uuid = $client_->uuid;
		}
		foreach( Capsule::table('tblconfiguration')->where('setting','=','SystemURL')->get(array('value')) as $system_url_){
			$system_url = $system_url_->value;
		}
		$hash_comp = $client_email.$client_id.$client_firstname.$client_lastname.$client_uuid.$secret_key;
		$hash = sha1($hash_comp);
		if(strpos($params['ticket_link'],'ticket_link') !== false){
			$merge_fields['ticket_link'] = $system_url.'modules/addons/gofasautologin/auth.php?client='.$client_id.'&hash='.$hash.'&ticket='.$vars['relid'];
		}
		if(strpos($params['ticket_link'],'login_link') !== false){
			$merge_fields['login_link'] = $system_url.'modules/addons/gofasautologin/auth.php?client='.$client_id.'&hash='.$hash.'&ticket='.$vars['relid'];
		}
	}
	if($params['debug']){
		logModuleCall( 'gofasautologin', 'generate_link', array('module_version'=>'4.0.0','hash'=>$hash,'invoice'=>$invoice,'client_'=>$client_,'ticket'=>$ticket ), false, $merge_fields, false);
	}
	return $merge_fields;
}}
if(!function_exists('gofasautologin_magic_link')){
	function gofasautologin_magic_link($vars) {
		echo '<pre>',print_r($vars),'</pre>';
		echo '<script>
			//document.getElementById("login").insertAdjacentHTML("afterend", "<a href="#">Link Mágico</a>");
			var btn = document.getElementById("login");
			btn.insertAdjacentHTML("afterend", "<p>My new paragraph</p>");
		</script>';
    	return;
	}
}
add_hook('EmailPreSend',1,'gofasautologin_hook');