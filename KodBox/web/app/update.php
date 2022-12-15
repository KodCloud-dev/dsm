<?php

class updateKod{
	public function __construct(){
		del_file(__FILE__);
		$this->run('version114','1.14');
		$this->run('version130','1.30');
		$this->runAll();
	}
	
	// 更新前版本低于$version时执行更新脚本; 
	public function run($method,$version){
		Model('SystemOption')->cacheRemove('');
		if(!$this->beforeVersion){
			$this->beforeVersion = floatval(Model('SystemOption')->get('currentVersion'));
		}
		write_log(array('update start:'.$version,$GLOBALS['in'],get_caller_msg()),'task');
		Model('SystemOption')->set('currentVersion',$version);
		if($this->beforeVersion >= floatval($version)) return;

		$this->$method();
		write_log('update end:'.$version,'task');
	}
	public function runAll(){
		Action('explorer.lightApp')->initApp();
	}
	
	// 执行sql更新;
	public function runSql($version){
		del_dir(TEMP_PATH.'_fields');
		$dbType = getDatabaseType();
		$path  	= BASIC_PATH."app/controller/install/data/update$version/$dbType.sql";
		$exist 	= file_exists($path);
		write_log('update runSql:'.$path.';exist='.intval($exist),'task');
		if(!$exist) return;
		
		$sqlArr = sqlSplit(file_get_contents($path));
		write_log(array('update sql:',$sqlArr),'task');
		foreach($sqlArr as $sql){
			$result = Model()->db()->execute($sql);
			write_log('update sql: res='.$result.';sql='.$sql,'task');
		}
		del_dir(TEMP_PATH.'_fields');
	}

	// 升级到1.14; 不能退出;
	public function version114(){
		$this->runSql("1.14");
		Action('admin.repair')->resetShareTo();
	}
	
	// 升级到1.30以后, 自动启用第三方登录插件;
	public function version130(){
		Hook::trigger("globalRequest.before");
		Model('Plugin')->viewList();
		Model('Plugin')->changeStatus('oauth','1');
	}
}
new updateKod();