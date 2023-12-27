<?php

/**
 * model函数用于实例化一个模型文件的Model [不存在的模型 or 存在的模型]
 * @param string $name Model名称;支持为空;支持表名(驼峰结构);支持模型名
 * @param string $tablePrefix 表前缀
 * @param mixed $connection 数据库连接信息
 * @return Model
 * 
 * eg:
 * Model("User")->getInfo(1); 	//实例化model中的model实现
 * Model("share_to")->find(1);	//实例化库，ModelBase-Model基类直接调用库
 */
function Model($name = '', $tablePrefix = '', $connection = '') {
    static $cache = array();
	if($name === '_destroy_'){$cache = array();return;} // 清除缓存;
    $guid = strtolower($tablePrefix . '_' . $name);
	if(is_string($connection)){
		$guid .= '_'.$connection;
	}else if($connection && is_array($connection)){
		return new ModelBase($name, $tablePrefix, $connection);
	}

	// 没有该类，则为表名或空model
	if(!$connection){$connection = $GLOBALS['config']['database'];}
	if(isset($cache[$guid])) return $cache[$guid];
	if($name){//有该类的情况(已经引入了类)
		$name = strtoupper($name[0]).substr($name,1);
		$modelName = $name.'Model';
		if( class_exists($modelName) ){
			$cache[$guid] = new $modelName('',$tablePrefix, $connection);
			return $cache[$guid];
		}
	}
	$cache[$guid] = new ModelBase($name, $tablePrefix, $connection);
    return $cache[$guid];
}

/**
 * action函数用于实例化一个控制器文件的Action
 * @param string $name 控制器名称
 * @return Object
 * 
 * eg: 控制器/model/ 插件方法/插件模块方法;
 * Action('user.index')->appConfig();
 * Action('UserModel')->count();
 * 
 * Action('chartPlugin')->view();	//chartPlugin; plugin/chart/app.php;
 * Action('chartPlugin.user.view')->listData();		// chartUserView;plugin/chart/controller/user/view.chass.php;
 * Action('chartPlugin.userModel')->listData();		// chartUserModel;
 */
function Action($name = '') {
	static $_cache = array();
	$nameBefore = $name;
	if(isset($_cache[$nameBefore])) return $_cache[$nameBefore];
	
	$name = trim(str_replace('/','.',$name),'/');
	$arr  = explode('.',$name);
	$mod  = strtolower($arr[0]);
	if( substr($mod,-6) == 'plugin'){
		$name = substr($arr[0],0,-6);
		$className = $name.'Plugin';
		$file = 'app.php';
		if(isset($arr[1]) && substr(strtolower($arr[1]),-5) == 'model'){
			$className = $name.$arr[1];// chartUserModel
			$file = "model/".$arr[1].".class.php";
		}else if(count($arr) == 3){
			$className = $name.$arr[1].$arr[2];//chartUserView
			$file = "controller/".$arr[1]."/".$arr[2].".class.php";
		}
		$file = PLUGIN_DIR.$name.'/'.$file;
	}else if( substr($mod,-5) == 'model'){
		$name = substr($arr[0],0,-5);
		return Model($name);
	}else{
		$className = $arr[0].$arr[1];
		$file = CONTROLLER_DIR.$arr[0].'/'.$arr[1].'.class.php';
	}
	$guid = strtolower($className);
	if (isset($_cache[$guid])) return $_cache[$guid];
	if (is_file($file)){
		include_once($file);
	}
	if (!class_exists($className)) {
		return actionCallError("[$name => $className] class not exists!");
	}
	$_cache[$guid] = new $className();
	$_cache[$nameBefore] = $_cache[$guid];
    return $_cache[$guid];
}

/**
 * 调用控制器,插件方法,或直接调用函数;
 * 
 * 参数合并为数组; 类似于js的 apply($object,$args);
 * ActionApply('user.index.appConfig',array("add",'123'));
 * 
 * 支持: 
 * 1. 直接调用:函数,类方法,静态类方法;eg: 'clear_html', array($this,'pathInfo'), array('IO','exist');
 * 2. 控制器方法; eg: 'user.index.appConfig';
 * 3. 模型方法;   eg: 'sourceModel.targetSpaceUpdate';
 * 
 * 4. 插件方法;   	  eg: 'testPlugin.echoJs';
 * 4. 插件模型方法;   eg: 'testPlugin.chartUserModel.getInfo';
 */
function ActionApply($action,$args=array()){
	static $_cache = array();
	if(is_array($action)){ //可调用方法; array($this,'log');
		return call_user_func_array($action,$args);
	}
	if(isset($_cache[$action])){
		return call_user_func_array($_cache[$action],$args);
	}
	
	if(function_exists($action)){ //全局函数;
		$_cache[$action] = $action;
	}else{
		$arrs  = explode('.',$action);
		$arrs  = is_array($arrs) ? $arrs : array();
		
		$className	= substr($action,0,strrpos($action,'.'));
		$method   	= $arrs[count($arrs) - 1];
		$obj 		= Action($className);
		if(!$method || !is_object($obj)){
			return actionCallError("$action method not exists!");
		}

		// 类查存在,最后一个不是方法时,默认将第三个参数作为参数; 支持多参数调用方式;
		// http://127.0.0.1/kod/kodbox/?test/test/index/page/1/limit/10
		if(!method_exists($obj,$method)){
			if(!$arrs[2] || !method_exists($obj,$arrs[2])){
				return actionCallError("$action method not exists!");
			}
			$method = $arrs[2];
		}
		$_cache[$action] = array($obj,$method);
	}
	return call_user_func_array($_cache[$action], $args);
}
function actionCallError($msg){
	return false;
	// think_exception($msg,false);
	write_log($msg."\n".get_caller_msg(),'error');
	return false;
}

/**
 * 调用控制器,插件方法,或直接调用函数;
 * 参数合并为数组; 类似于js的 call($object,$arg1,$arg2,...);
 * 
 * ActionCall('user.index.appConfig',"add",'123');
 * ActionCall('UserModel.count');
 * 
 * ActionCall('chartPlugin.view','5');
 * ActionCall('chartPlugin.user.view.list');
 * ActionCall('chartPlugin.userModel.list');
 */
function ActionCall($action){
	$args = array_slice(func_get_args(),1);
	return ActionApply($action,$args);
}

function ActionCallApi($uri,$param='',$allowExec=true){
	$paramStr = $param;
	if(is_array($param)){
		$paramStr = '';
		foreach($param as $key => $value){
			$value = is_array($value) ? json_encode($value) : $value;
			$value = is_bool($value) ? intval($value) : $value;
			$paramStr .= '&'.$key.'='.rawurlencode($value);
		}
	}
	$token = Action('user.index')->accessToken();
	$uri = str_replace('.','/',$uri).'&accessToken='.$token.$paramStr;
	$res = '';
	$phpBin = phpBinCommand();
	if($allowExec && $phpBin && function_exists('shell_exec')){
		$command = $phpBin.' '.BASIC_PATH.'index.php '.escapeshellarg($uri);
		$res = shell_exec($command);
	}
	if(!$res){
		$streamContext = stream_context_create(array('http'=>array('timeout'=>20,'method'=>"GET")));
		$res = file_get_contents(APP_HOST.'index.php?'.$uri,false,$streamContext);
	}
	$json 	= json_decode($res,true);
	$result = is_array($json) ? $json:array('code'=>null,'data'=>$res);
	if(!$json){echo $res;}
	
	return $result;
}

/**
 * 调用控制器,插件方法,或直接调用函数; 拦截show_json退出;返回show_json的内容数组;
 * 
 * 调用方法有多个show_json;默认返回第一个调用的结果; 
 * 注: 为避免后续继续执行引起其他问题,需要在前面show_json前加上return (容易出bug,逐步废弃,用ActionCallResult替代)
 */
function ActionCallHook($action){
	ob_start();
	$args = array_slice(func_get_args(),1);
	$GLOBALS['SHOW_JSON_NOT_EXIT'] = 1;
	$result = ActionApply($action,$args);
	$echo   = ob_get_clean();
	$result = $echo ? json_decode($echo,true) : $result;// 优先使用输出内容;
	$GLOBALS['SHOW_JSON_NOT_EXIT_DONE'] = 0;
	$GLOBALS['SHOW_JSON_NOT_EXIT'] = 0;
	return $result;
}

/**
 * 调用控制器,插件方法,或直接调用函数; 并对show_json输出进行处理;
 */
function ActionCallResult($action,$resultParse){
	$args = array_slice(func_get_args(),2);
	$GLOBALS['SHOW_JSON_RESULT_PARSE'] = $resultParse;
	ActionApply($action,$args);
}

function beforeShutdown(){
	Hook::trigger('beforeShutdown');
}

$_SERVER['BASIC_PATH'] 	= BASIC_PATH;
$_SERVER['LIB_DIR'] 	= LIB_DIR;
// https://www.jianshu.com/p/1a443d542219;
function beforeShutdownError($code=false,$msg='',$file='',$line=0){
	switch ($code) {
        case E_PARSE:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
		case E_USER_ERROR:$errorType = 'Fatal Error';break;
		
        case E_WARNING:
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
		case E_RECOVERABLE_ERROR:$errorType = 'Warning';break;
		case E_STRICT:$errorType = 'Strict';break;
		
		case E_NOTICE:
		case E_USER_NOTICE:$errorType = 'Notice';break;
        case E_DEPRECATED:
        case E_USER_DEPRECATED:$errorType = 'Deprecated';break;
        default :break;
	}
	if(!$errorType || $errorType == 'Notice' || $errorType=='Deprecated') return;
	$file  = '/'.str_replace($_SERVER['BASIC_PATH'],'',$file);
	$error = $errorType.','.$msg.','.$file.','.$line;
	write_log($error."\n".get_caller_msg(1)."\n".json_encode($GLOBALS['in']),'error');
}
function beforeShutdownFatalError($e){
	think_exception($e);
}

$errorLevel = E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED;
register_shutdown_function('beforeShutdown');			// 结束时调用;有exit则不会走到该处;
set_error_handler('beforeShutdownError',$errorLevel); 	// 错误处理记录;notice,warning,error等都会进入;


if(function_exists('set_exception_handler')){
	set_exception_handler('beforeShutdownFatalError');	// 
}
stream_wrapper_register('kodio','StreamWrapperIO');