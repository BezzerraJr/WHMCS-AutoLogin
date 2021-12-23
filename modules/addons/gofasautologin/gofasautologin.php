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
if(!defined("WHMCS")){die();}
use WHMCS\Database\Capsule;
if(!function_exists('gofasautologin_config') ){
	function gofasautologin_config(){
		$debug = false;
		require_once __DIR__.'/auth.php';
		
	$module_version = '4.0.0';
	$module_version_int = (int)preg_replace('/[^0-9]/', '', $module_version);
	foreach( Capsule::table('tblconfiguration')->where('setting','=','SystemURL')->get(array('value')) as $system_url_){
		$system_url = $system_url_->value;
	}
	$tbladmins = array();
	foreach( Capsule::table('tbladmins') -> get() as $tbladmins_ ){
		$tbladmins[$tbladmins_->id] = $tbladmins_->id.' - '.$tbladmins_->firstname.' '.$tbladmins_->lastname.' ('.$tbladmins_->username.')';
	}
	// Verify available updates
	if( !function_exists('gal_verify_module_updates') ){
		function gal_verify_module_updates($referer,$module_version){
   			$query = 'https://gofas.net/br/updates/?software=10265&referer='.$referer.'&version='.$module_version;
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_USERAGENT,'Módulo Gofas Auto Login para WHMCS v'.$module_version.' instalado em '.$referer);
    		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
    		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
    		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    		curl_setopt($curl, CURLOPT_URL, $query);
			$result = curl_exec($curl);
    		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			return array(
				'http_status' => $http_status,
				'result' => $result,
			);
		}
	}
	$available_update_ = gal_verify_module_updates($system_url,$module_version);
	if( (int)$available_update_['http_status'] === 200 ){
		$available_update = $available_update_['result'];
		$available_update_int = (int)preg_replace("/[^0-9]/", "", $available_update);
	}
	else {
		$available_update_int = 000;
	}
	if( $available_update_int === $module_version_int ){
		$available_update_message = '<p style="font-size: 14px;color:green;"><i class="fas fa-check-square"></i> Você está executando a versão mais recente do módulo.</p>';
	}
	if( $available_update_int > $module_version_int ){
		$available_update_message = '<p style="font-size: 14px;color:red;"><i class="fas fa-exclamation-triangle"></i> Atualização disponível, verifique a <a style="color:#CC0000;text-decoration:underline;" href="https://gofas.net/?p=10265" target="_blank">versão '.$available_update.'</a></p>';
	}
	if( $available_update_int < $module_version_int ){
		$available_update_message = '<p style="font-size: 14px;color:red;"><i class="fas fa-exclamation-triangle"></i> Você está executando uma versão Beta desse módulo.<br>Não recomendamos o uso dessa versão em produção.<br>Baixar versão estável: <a style="color:#CC0000;text-decoration:underline;" href="https://gofas.net/?p=10265" target="_blank">v'.$available_update.'</a></p>';
	}
	if( $available_update_int === 000 ){
		$available_update_message = '<p style="font-size: 14px;color:green;"><i class="fas fa-check-square"></i> Você está executando a versão mais recente do módulo.</p>';
	}
	
	$intro = array('intro' => array(
				'FriendlyName' => '',
				'Description' => '<h4 style="padding-top: 5px;">Módulo Gofas Auto Login para WHMCS v'.$module_version.'</h4>'.$available_update_message,
	));
	
	// Notificar admin sobre erros
	$admin = array('admin' => array(
			'FriendlyName' => 'Administrador do WHMCS',
			'Type'          => 'dropdown',
			'Default' 		=> key(reset($tbladmins)),
            'Options'       => $tbladmins,
			'Description' => 'Defina o administrador com permissões para utilizar a API interna do WHMCS.',
		),
	);
	$invoice_link = array('invoice_link' => array(
		'FriendlyName' => 'Tag de email para Faturas',
		'Type' => 'radio',
		'Options' => 'Usar {$invoice_link},Usar {$login_link}',
		'Default' => 'Usar {$invoice_link}',
		'Description' => 'Usar nos templates de email a tag de mesclagem padrão do WHMCS <i>{$invoice_link}</i>, ou usar a tag do módulo <i>{$login_link}</i>:',
	));
	$ticket_link = array('ticket_link' => array(
		'FriendlyName' => 'Tag de email para Tickets',
		'Type' => 'radio',
		'Options' => 'Usar {$ticket_link},Usar {$login_link}',
		'Default' => 'Usar {$ticket_link}',
		'Description' => 'Usar nos templates de email a tag de mesclagem padrão do WHMCS <i>{$invoice_link}</i>, ou usar a tag do módulo <i>{$login_link}</i>:',
	));
	$debug = array('debug' => array(
				'FriendlyName' => 'Debug',
				'Type' => 'yesno',
                'Default' => '',
                'Description' => 'Salvar informações de diagnóstico no <a target="_blank" style="text-decoration:underline;" href="https://s3.amazonaws.com/uploads.gofas.me/wp-content/uploads/2020/10/13003919/WHMCS_-_Log_de_Debug_dos_Mo%CC%81dulos_do_Sistema.png">Log de Módulo</a> quando links são gerados e acessados.',
	));
	$footer = array('footer' => array(
				'FriendlyName' => '',
				'Description' => '&copy; '.date('Y').' <a target="_blank" title="↗ Gofas Software" href="https://gofas.net">Gofas Software</a>',
	));
	$fields = array_merge($intro,$admin,$invoice_link,$ticket_link,$debug,$footer);
    $configarray = array(
    "name" => "Gofas Auto Login",
    "description" => "Módulo Gofas Auto Login para WHMCS",
    "version" => $module_version,
    "author" => '<a title="Gofas Software" href="https://gofas.net/" target="_blank" alt="Gofas"><img src="'.$system_url.'modules/addons/gofasautologin/lib/logo.png"></a>',
	 "fields" => $fields,
	 );
    return $configarray;
}
}
if( !function_exists('gofasautologin_output') ){
	function gofasautologin_output($vars){
		echo '<p>&copy;'.date('Y').' <a target="_blank" title="↗ Gofas Software" href="https://gofas.net">Gofas Software</a> | <a target="_blank" title="↗ Documentação" href="https://gofas.net/?p=10265">Documentação</a> | <a target="_blank" title="↗ Suporte" href="https://gofas.net/?p=3543">Suporte</a></p>';
	}
}