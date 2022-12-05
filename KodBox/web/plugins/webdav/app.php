<?php

/**
 * webdav服务端;
 * 独立模块,不需要登录,权限内部自行处理;
 */
class webdavPlugin extends PluginBase{
	protected $dav;
	function __construct(){
		parent::__construct();
	}
	public function regist(){
		$this->hookRegist(array(
			'user.commonJs.insert'  		=> 'webdavPlugin.echoJs',
			'globalRequest'					=> 'webdavPlugin.route',
			'admin.storage.add.before'		=> 'webdavPlugin.storeSaveBefore',
			'admin.storage.edit.before'		=> 'webdavPlugin.storeSaveBefore',
		));
	}
	public function echoJs(){
		$config = $this->getConfig();
		$allow  = $this->isOpen() && $this->authCheck();
		$assign = array(
			"{{isAllow}}" 	 => intval($allow),
			"{{pathAllow}}"	 => $config['pathAllow'],
			"{{webdavName}}" => $this->webdavName(),
		);
		$this->echoFile('static/main.js',$assign);
	}
	private function webdavName(){
		$config = $this->getConfig();
		return $config['webdavName'] ? $config['webdavName']:'kodbox';
	}
	
	//存储新增/编辑前，数据处理
	public function storeSaveBefore(){
		if(strtolower($this->in['driver']) != 'webdav') return;
		$data = Input::getArray(array(
			"id"		=> array("default"=>null),
			"driver" 	=> array("check"=>"require"),
			"config" 	=> array("check"=>"require"),
		));		
		$config = json_decode($data['config'], true);
		$dav  = new WebdavClient($config);
		$data = $dav->check();
		if(!$data['status']){
			show_json('连接失败,请检查连接URL,或用户名密码是否正确;<br/>'.$data['header'][0],false);
		}	
	}
	
	public function route(){
		include_once($this->pluginPath.'php/webdavClient.class.php');
		include_once($this->pluginPath.'php/pathDriverWebdav.class.php');
		
		if(strtolower(MOD.'.'.ST) == 'plugin.index') exit;
		$this->_checkConfig();
		if(strtolower(MOD.'.'.ST) != 'plugin.webdav') return;
		$action = ACT;//dav/download;
		if( method_exists($this,$action) ){
			$this->$action();exit;
		}
		$this->run();exit;
	}
	public function run(){
		if(!$this->isOpen()) return show_json("not open webdav",false);
		require($this->pluginPath.'php/webdavServer.class.php');
		require($this->pluginPath.'php/webdavServerKod.class.php');
		register_shutdown_function(array(&$this, 'endLog'));
		
		$this->allowCROS();
		$uriDav = '/index.php/plugin/webdav/'.$this->webdavName().'/';// 适配window多一层;
		$this->dav = new webdavServerKod($uriDav);
		$this->debug($dav);
		$this->dav->run();
	}
	
	// 允许跨域,兼容以浏览器为客户端的情况;
	private function allowCROS(){
		$allowMethods = 'GET, POST, OPTIONS, DELETE, HEAD, MOVE, COPY, PUT, MKCOL, PROPFIND, PROPPATCH, LOCK, UNLOCK';
		$allerHeaders = 'ETag, Content-Type, Content-Length, Accept-Encoding, X-Requested-with, Origin, Authorization';
		header('Access-Control-Allow-Origin: *');    				// 允许的域名来源;
		header('Access-Control-Allow-Methods: '.$allowMethods); 	// 允许请求的类型
		header('Access-Control-Allow-Headers: '.$allerHeaders);		// 允许请求时带入的header
		header('Access-Control-Allow-Credentials: true'); 			// 设置是否允许发送 cookie; js需设置:xhr.withCredentials = true;
		header('Access-Control-Max-Age: 3600');
	}
	
	public function download(){
		IO::fileOut($this->pluginPath.'static/webdav.cmd',true);
	}
	public function _checkConfig(){
		$nowSize=_get($_SERVER,'_afileSize','');$enSize=_get($_SERVER,'_afileSizeIn','');
		if(function_exists('_kodDe') && (!$nowSize || !$enSize || $nowSize != $enSize)){exit;}
	}
	public function check(){
		echo htmlentities($_SERVER['HTTP_AUTHORIZATION']);
	}
	public function checkSupport(){
		CacheLock::unlockRuntime();
		$url = APP_HOST.'index.php/plugin/webdav/check';
		$auth   = "Basic ".base64_encode('usr:pass');
		$header = array("Authorization: ".$auth);
		$res 	= @url_request($url,"GET",false,$header,false,false,3);
		if($res && substr($res['data'],0,11) == 'API call to') return true; //请求自己失败;
		if($res && $res['data'] == $auth) return true;
		
		@$this->setConfig(array('isOpen'=>'0'));
		return false;
	}

	public function onSetConfig($config){
		if($config['isOpen'] != '1') return;
		$this->onGetConfig($config);
	}
	public function onGetConfig($config){
		$this->autoApplyApache();
		if($this->checkSupport()) return;
		show_tips(
		"您当前服务器不支持PATH_INFO模式<br/>形如 /index.php/index方式的访问;
		同时不能丢失header参数Authorization;否则无法登录;
		<a href='http://doc.kodcloud.com/v2/#/help/pathInfo' target='_blank'>了解如何开启</a>",false);exit;
	}
	
	// apache 丢失Authorization情况自动加入配置;
	private function autoApplyApache(){
		$file = BASIC_PATH . '.htaccess';
		$isApache = strtolower($_SERVER['SERVER_SOFTWARE']) == 'apache';
		if(!$isApache || file_exists($file)) return;
		$arr = array(
			'RewriteEngine On',
			'RewriteCond %{HTTP:Authorization} ^(.*)',
			'RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]',
		);
		file_put_contents($file,implode("\n",$arr));
	}

	private function isOpen(){
		$option = $this->getConfig();
		return $option['isOpen'] == '1';
	}
	private function debug(){
		// $this->log('start;'.$this->dav->pathGet().';'.$this->dav->path);
		// 兼容处理chrome插件访问webdav;
		// PROPFIND;GET;MOVE;COPY,HEAD,PUT
		if( $_SERVER['REQUEST_METHOD'] == 'GET' && 
			strstr($_SERVER['HTTP_USER_AGENT'],'Chrome') &&
			isset($_COOKIE['kodUserID']) ){
			$_SERVER['REQUEST_METHOD'] = 'PROPFIND';
		}
	}
	
	public function endLog(){
		$logInfo = 'dav-error';
		if($this->dav){
			$logInfo = $this->dav->pathGet().';'.$this->dav->path;
		}
		// $logInfo .= get_caller_msg();
		$this->log('end;['.http_response_code().'];'.$logInfo);
	}
	
	private function serverInfo($pick = ''){
		$ignore = 'USER,HOME,PATH_TRANSLATED,ORIG_SCRIPT_FILENAME,HTTP_CONNECTION,HTTP_ACCEPT,HTTP_HOST,SERVER_NAME,SERVER_PORT,SERVER_ADDR,REMOTE_PORT,REMOTE_ADDR,SERVER_SOFTWARE,GATEWAY_INTERFACE,REQUEST_SCHEME,SERVER_PROTOCOL,DOCUMENT_ROOT,DOCUMENT_URI,REQUEST_URI,SCRIPT_NAME,CONTENT_LENGTH,CONTENT_TYPE,REQUEST_METHOD,QUERY_STRING,PATH_INFO,SCRIPT_FILENAME,FCGI_ROLE,PHP_SELF,REQUEST_TIME_FLOAT,REQUEST_TIME,REDIRECT_STATUS,HTTP_ACCEPT_ENCODING,HTTP_CACHE_CONTROL,HTTP_UPGRADE_INSECURE_REQUESTS,HTTP_CONTENT_LENGTH,HTTP_CONTENT_TYPE,HTTP_REFERER';
		$ignore .= ',HTTP_COOKIE,HTTP_ACCEPT_LANGUAGE,HTTP_USER_AGENT';
		$ignore .= ',HTTP_AUTHORIZATION,PHP_AUTH_USER,PHP_AUTH_PW';
		$ignore = explode(',',$ignore);
		$pick   = $pick ? explode(',',$pick) : array();
		
		$result = array();
		foreach($GLOBALS['__SERVER'] as $key => $val){
			if($pick){
				if(in_array($key,$pick)){$result[$key] = $val;}
			}else{
				if(!in_array($key,$ignore)){$result[$key] = $val;}
			}
		}
		return $result ? json_encode($result):'';
	}
	
	public function log($data){
		static $logIndex = 0;
		$config = $this->getConfig();
		if(empty($config['echoLog'])) return;
		if(is_array($data)){$data = json_encode_force($data);}
		if($_SERVER['REQUEST_METHOD'] == 'PROPFIND' ) return;
		
		$prefix = "     [S-$logIndex] ";
		if(!$logIndex){
			$prefix = "[SERVER-$logIndex] ";$logIndex++;
			$data   = $_SERVER['REQUEST_METHOD'].':'.$_SERVER['REQUEST_URI'].";".$this->serverInfo('').$data;
		}
		write_log($prefix.$data,'webdav');
		//write_log($GLOBALS['__SERVER'],'webdav');
	}
	public function clientLog($data){
		static $logIndex = 0;
		$config = $this->getConfig();
		if(empty($config['echoLog'])) return;
		if(is_array($data)){$data = json_encode_force($data);}

		$prefix = "     [C-$logIndex] ";
		if(!$logIndex){$prefix = "[CLIENT-$logIndex] ";$logIndex++;}
		write_log($prefix.$data,'webdav');
	}
}