<?php 

/**
 * 初始化项目基础数据
 * 需先创建数据库，建表，并配置好config/setting_user.php
 */
class installIndex extends Controller {
    public $roleID;
    public $admin = array();
    public $userSetting; 
    public $installLock; 
    public $installFastLock; 
    public $dbType;
    public $engine;
	public function __construct() {
        parent::__construct();
        $this->userSetting      = BASIC_PATH  . 'config/setting_user.php';
        $this->installLock      = USER_SYSTEM . 'install.lock';
        $this->installFastLock  = USER_SYSTEM . 'fastinstall.lock';
		$this->authCheck();
    }

    public function authCheck(){
        if(MOD.'.'.ST == 'install.index'){
            if(!ACT || !in_array(ACT, array('env', 'save', 'auto'))){
                show_json(LNG('common.illegalRequest'), false);
            }
            if($this->installCheck()) show_json(LNG('admin.install.errorRequest'), false);
            if(ACT == 'auto') $this->auto();    // 全自动安装
        }
    }

    public function check(){
        // 判断是否需要安装
        if($this->installCheck()) return;
		if(ACTION == 'user.view.call'){exit;}
        if(ACTION == 'user.view.options'){
            $options = array(
                "kod"	=> array(
                    'systemOS'		=> $this->config['systemOS'],
                    'phpVersion'	=> PHP_VERSION,
                    'appApi'		=> appHostGet(),
                    'APP_HOST'		=> APP_HOST,
                    'ENV_DEV'		=> !!STATIC_DEV,
                    'staticPath'	=> STATIC_PATH,
                    'version'		=> KOD_VERSION,
                    'build'			=> KOD_VERSION_BUILD,
                    'channel'		=> INSTALL_CHANNEL,
                ),
                "user"	=> array('config' => array()),
                "io"	=> array(),
                "lang"	=> I18n::getType(),
            );
			if($this->in['full'] == '1'){
				$options['_lang'] = array(
					"list"	=> I18n::getAll(),
					"lang"	=> I18n::getType(),
				);
			}
            show_json($options);
        }
		$actions = array(
			'install.index.env', 
			'install.index.save',
			'user.view.lang',
		);
        if( in_array(ACTION, $actions) ){
			ActionCall(ACTION);exit;
        }
		
		// 安装路径合法性检测; 不允许[, ;%][*][&=+%#?{}]; 允许(_!@$[]()-./); 需要转义([]@$)   
        $uri = str_replace(HOST,'',APP_HOST);$matchArr = array();
        if($uri && !preg_match("/^[a-zA-Z0-9_!@$\[\]\(\)\-\.\/]*$/",$uri,$matchArr)){
        	show_tips("App path cann't has special chars<br/>(程序路径不支持包含特殊符号,请尽量使用英文字母及数字)<pre>".$uri.'</pre>');
        }
		
        $this->tpl = CONTROLLER_DIR . 'install/static/';
        $value = array('installPath' => INSTALL_PATH);
        $this->values = array_merge($value, $this->installFast());
        $this->display('index.html');
        exit;
    }
    private function installCheck(){
        if(!defined('STATIC_PATH')){
            define('STATIC_PATH',$GLOBALS['config']['settings']['staticPath']);
        }
        if(!defined('INSTALL_PATH')){
            define('INSTALL_PATH', './app/controller/install/');
        }
        // 1.setting_user.php、install.lock、数据库配置都存在，已安装；
        // 2.setting_user.php、数据库配置存在，install.lock不存在，重置管理员密码
        if(@file_exists($this->userSetting)) {
            if($this->defDbConfig(true)) {
                if(@file_exists($this->installLock)) return true;
                @touch($this->installFastLock);
            }
        }
        if(!@file_exists($this->userSetting)) del_file($this->installLock);
        return false;
    }
    // 一键安装
    private function installFast(){
        $data = array(
            'installFast' => 0,     // 1:已配置数据库;2:已填写管理员账号
            'installAuto' => ''     // 管理员账号：admin|admin
        );
        if(!@file_exists($this->installFastLock) || !@file_exists($this->userSetting)) {
            return $data;
        }
        $data['installFast'] = 1;
        if($auto = $this->getFastAcc()){
            $data['installFast'] = 2;
            $data['installAuto'] = $auto;
        }
        return $data;
    }
    // 获取自动安装账号密码
    private function getFastAcc(){
        $file = $this->installFastLock;
        $content = trim(file_get_contents($file));
        if(empty($content)) return false;

        $content = array_filter(explode(PHP_EOL, $content));
        $data = array();
        foreach($content as $line) {
            $tmp = explode("=", trim($line));
            if(empty($tmp[1])) continue;
            $data[strtolower($tmp[0])] = $tmp[1];
        }
        if(isset($data['adm_name']) && isset($data['adm_pwd'])) {
            return $data['adm_name'] . '|' . $data['adm_pwd'];
        }
        return false;
    }
    // 全自动安装
    public function auto(){
        if (isset($this->in['action']) && $this->in['action'] == 'db') return;  // 避免自动安装死循环
        $data = $this->installFast();
        if (!$data['installFast']) return;

        // 1.根据setting_user.php初始化数据库——saveDb
        $config = $this->defDbConfig();
        if (empty($config)) return;
        $this->in = array_merge($config, array('action'=>'db'));
        $GLOBALS['NOT_SAVE_DB_SET'] = true;
        $res = ActionCallHook('install.index.save');
        unset($GLOBALS['NOT_SAVE_DB_SET']);
        if (!$res['code']) {
            // 数据库已存在，且有表，当做自行导入的kod库（暂不做对比检测）
            if (!isset($res['info']) || $res['info'] != 10001) {
                show_json($res['data'], false);
            }
        }

        // 2.没有管理员账号，返回
        if ($data['installFast'] == 1) return;

        // 3.有管理员账号，提交最后一步
        $data = explode('|', $data['installAuto']);
        $this->in = array(
            'name'      => $data[0],
            'password'  => $data[1],
            'action'    => 'user',
        );
        $this->save();
    }

    /**
     * 获取数据库默认配置信息
     * @param boolean $return false:表单数据;true:db配置数据
     * @return void
     */
    public function defDbConfig($return=false){
        $data = array();
        // 1.获取文件数据
        if(!$dbConfig = $this->config['database']) return $data;
        if(!$dbConfig['DB_TYPE']) return $data;
        if($return) return $dbConfig;

        // 2.解析为form表单数据
        $database = array();
        unset($dbConfig['DB_SQL_LOG'],$dbConfig['DB_FIELDS_CACHE'],$dbConfig['DB_SQL_BUILD_CACHE']);
        foreach($dbConfig as $key => $value) {
            $keys = explode("_", strtolower($key));
            $key = $keys[0] . ucfirst($keys[1]);
            $database[$key] = $value;
        }
        // 2.1 pdo数据处理
        if($database['dbType'] == 'pdo') {
            $dsn = explode(":", $database['dbDsn']);
            $database['pdoType'] = $dsn[0];
            unset($database['dbDsn']);
            foreach($database as $key => $value) {
                if(in_array($key, array('dbType', 'pdoType'))) continue;
                $database['pdo'.ucfirst($key)] = $value;
                unset($database[$key]);
            }
        }
        if($database['dbType'] == 'mysqli') $database['dbType'] = 'mysql';
        if(in_array($database['dbType'], array('sqlite', 'sqlite3'))) {
            if($database['dbType'] == 'sqlite3') $database['dbType'] = 'sqlite';
            $database['dbName'] = '';
        }
        if(!$cache = $this->config['cache']) return $database;  // 无cache

        $cacheType = $cache['cacheType'];
        $database['cacheType'] = $cacheType;
        if($cacheType == 'file') return $database;  // 文件cache
        $database[$cacheType.'Host'] = $cache[$cacheType]['host'];
        $database[$cacheType.'Port'] = $cache[$cacheType]['port'];

        return $database;
    }

    // 环境检测
    public function env(){
        if(isset($this->in['db']) && $this->in['db'] == 1) {
            $data = $this->defDbConfig();
            show_json($data);
        }
        $env = array(
            'path_writable'     => array(),
            'php_version'       => phpversion(),
            'allow_url_fopen'   => ini_get('allow_url_fopen'),
            'php_bit'           => phpBuild64() ? 64 : 32,
            'iconv'             => function_exists('iconv'),
            'mb_string'         => function_exists('mb_convert_encoding'),
            'json'              => function_exists('json_encode'),
            'curl'              => function_exists('curl_init'),
			'xml'               => function_exists('xml_parser_create'),
            'shell_exec'        => (function_exists('shell_exec') && function_exists('exec')),
            'gd'                => true,
            'path_list'         => check_list_dir(),
        );
        if( !function_exists('imagecreatefromjpeg')||
            !function_exists('imagecreatefromgif')||
            !function_exists('imagecreatefrompng')||
            !function_exists('imagecolorallocate')){
            $env['gd'] = false;
        }
        $pathWrt = true;
		$pathList = array(BASIC_PATH, DATA_PATH, DATA_PATH.'system');
        foreach ($pathList as $value) {
            if(!path_writeable($value)) $pathWrt = false;
            break;
        }
        $env['path_writable'] = $pathWrt ? $pathWrt : rtrim(BASIC_PATH, '/');
        show_json($env);
    }

    /**
     * 数据库、管理员账号提交
     *
     * @return void
     */
    public function save(){
        $action = Input::get('action', 'in', null, array('db', 'user'));
        $func = 'save' . ucfirst($action);
        $this->$func();
    }

    /**
     * 1. 数据库配置
     */
    private function saveDb(){
        // 1.1 获取db配置信息
        $data = $this->dbConfig();
        if (isset($dat['code']) && !$data['code']) return $data;
        $dbName = $data['DB_NAME'];
        $cacheType = Input::get('cacheType', 'in', null, array('file', 'redis', 'memcached'));

        // 1.2 连接数据库
        // 如果用include引入配置文件，$config的值会是上次请求的（？），所以直接用$data赋值
        $GLOBALS['config']['database'] = $data;
        
        // $GLOBALS['config']['cache']['sessionType'] = $cacheType;
        // $GLOBALS['config']['cache']['cacheType'] = $cacheType;
        think_config($GLOBALS['config']['databaseDefault']);
        think_config($GLOBALS['config']['database']);

        if($this->dbType == 'mysql'){
            // mysql连接，先不指定数据库，配置错误时会报错
            $GLOBALS['config']['database']['DB_NAME'] = '';
            think_config($GLOBALS['config']['database']);
            $db = Model()->db();
            $dbexist = $db->execute("show databases like '{$dbName}'");
        }
        $GLOBALS['config']['database']['DB_NAME'] = $dbName;    // 避免auto调用时后续取该值为空（同一进程）

        // 1.3 检测缓存配置
        // 判断所需缓存配置是否有效——redis、memcached
        if(in_array($cacheType, array('redis', 'memcached'))){
            if(!extension_loaded($cacheType)){
                return show_json(sprintf(LNG('common.env.invalidExt'), "[php-{$cacheType}]"), false);
            }
            $host = Input::get("{$cacheType}Host", 'require');
            $port = Input::get("{$cacheType}Port", 'require');

            $type = ucfirst($cacheType);
            $handle = new $type();
            try{
                if($cacheType == 'redis') {
                    $handle->connect($host, $port, 1);
                    $auth = Input::get('redisAuth');
                    if ($auth) $handle->auth($auth);
                    $conn = $handle->ping();
                }else{
                    $conn = $handle->addServer($host, $port);
                    if($conn && !$handle->getStats()) $conn = false;
                }
                if(!$conn) return show_json(sprintf(LNG('admin.install.cacheError'),"[{$cacheType}]"), false);
            }catch(Exception $e){
                $msg = sprintf(LNG('admin.install.cacheConnectError'),"[{$cacheType}]");
                $msg .= '<br/>'.$e->getMessage();
                return show_json($msg, false);
            }
        }

        // 1.4 创建数据库
        if($this->dbType == 'mysql'){
            if(!$dbexist){
                // host=localhost时，root空密码默认没有权限，会创建失败
                $db->execute("create database `{$dbName}`");
            }
            $db->execute("use `{$dbName}`");
            $tables = $db->getTables($dbName);
            if (!empty($tables)) {
                if(empty($this->in['del_table'])){
                    return show_json(LNG('admin.install.ifDelDb'), false, 10001);
                }
                // 删除数据表
                foreach($tables as $table) {
                    if ($table) {
                        $table = strtolower($table);
                    } else {continue;}
                    $db->execute("drop table if exists `{$table}`");
                }
            }
        }else{
            $db = Model()->db();
        }

        // 1.5 写入配置文件：数据库、缓存——自动安装时配置已存在，无需保存
        if (!$GLOBALS['NOT_SAVE_DB_SET']) {
            $data['DB_NAME'] = $dbName;
            if($data['DB_TYPE'] == 'pdo'){
                $dbDsn = explode(':', $data['DB_DSN']);
                if($dbDsn[0] == 'mysql'){
                    $data['DB_DSN'] .= ';dbname=' . $data['DB_NAME'];
                }
            }
            $database = var_export($data, true);
            $text = array(
                "<?php ",
                "\$config['database'] = {$database};",
                "\$config['cache']['sessionType'] = '{$cacheType}';",
                "\$config['cache']['cacheType'] = '{$cacheType}';"
            );
            if(isset($host) && isset($port)){
                $text[] = "\$config['cache']['{$cacheType}']['host'] = '{$host}';";
                $text[] = "\$config['cache']['{$cacheType}']['port'] = '{$port}';";
                if ($cacheType == 'redis' && $auth) {
                    $text[] = "\$config['cache']['{$cacheType}']['auth'] = '{$auth}';";
                }
            }
            $file = $this->userSetting;
            if(!@file_exists($file)) @touch($file);
            $content = file_get_contents($file);
            $pre = '';
            if(stripos(trim($content),"<?php") !== false) {
                $pre = PHP_EOL;
                unset($text[0]);
            }
            $content = implode(PHP_EOL, $text);
            if($this->dbType == 'sqlite') {
                $content = $this->sqliteFilter($content);
            }
            if(!file_put_contents($file,$pre.$content, FILE_APPEND)) {
                $msg = LNG('admin.install.dbSetError');
                $tmp = explode('<br/>', LNG('common.env.pathPermissionError'));
                $msg .= "<br/>" . $tmp[0];
                return show_json($msg, false, 10000);
            }
        }

        // 1.6 创建数据表
        $res = $this->createTable($db);
        if (isset($res['code']) && !$res['code']) return $res;
        return show_json(LNG('explorer.success'));
    }
    private function sqliteFilter($content) {
		$replaceFrom = "'DB_NAME' => '".USER_SYSTEM;
		$replaceTo   = "'DB_NAME' => USER_SYSTEM.'";
		$replaceFrom2= "'DB_DSN' => 'sqlite:".USER_SYSTEM;
		$replaceTo2  = "'DB_DSN' => 'sqlite:'.USER_SYSTEM.'";
		$content = str_replace($replaceFrom,$replaceTo,$content);
		$content = str_replace($replaceFrom2,$replaceTo2,$content);
		return $content;
	}

    /**
     * 创建数据表
     * @param [type] $db
     * @return void
     */
    private function createTable($db){
        $dbFile = INSTALL_PATH . "data/{$this->dbType}.sql";
		if (!@file_exists($dbFile)) {
			return show_json(LNG('admin.install.dbFileError'), false);
		}
		$content = file_get_contents($dbFile);
		if($this->dbType == 'mysql') {
		    if(!empty($this->engine) && $this->engine == 'innodb') {
		        $content = str_ireplace('MyISAM', 'InnoDB', $content);
		    }
            // fulltext索引兼容
            $res = $db->query('select VERSION() as v');
            $mysqlVersion = floatval(($res[0] && isset($res[0]['v'])) ? $res[0]['v'] : 0);
			if($mysqlVersion && $mysqlVersion >= 5.7){
				// 删除索引不存在时报错; 需手动处理;
				//$content .= "\n".file_get_contents(INSTALL_PATH."data/fulltext.sql");
			}else{
				$content = str_ireplace('FULLTEXT ','', $content);
			}
		}
		$sqlArr = sqlSplit($content);
		foreach($sqlArr as $sql){
			$db->execute($sql);
		}
    }

    /**
     * 获取数据库配置信息
     */
    private function dbConfig(){
        $this->dbType = $dbType = Input::get('dbType', 'in', null, array('sqlite', 'mysql', 'pdo'));
        $dbList = $this->dbList();

        $init = array('db_type' => $dbType);
        if($dbType == 'mysql'){
            $data = Input::getArray(array(
                'dbHost'    => array('aliasKey' => 'db_host', 'check'   => 'require'),
                'db_port'   => array('default'  => 3306),
                'dbUser'    => array('aliasKey' => 'db_user', 'check'   => 'require'),
                'dbPwd'     => array('aliasKey' => 'db_pwd', 'check'    => 'require', 'default' => ''),
                'dbName'    => array('aliasKey' => 'db_name', 'check'   => 'require'),
            ));
            $this->execPort($data);
            $dbType = in_array('mysqli', $dbList) ? 'mysqli' : 'mysql';
            $this->engine = Input::get('dbEngine', null, 'myisam');
        }else if($dbType == 'pdo'){
            $this->dbType = $pdoType = Input::get('pdoType', 'in', null, array('sqlite', 'mysql'));
            if($pdoType == 'mysql'){
                $data = Input::getArray(array(
                    'db_dsn'    => array('default'  => ''),
                    'pdoDbHost' => array('aliasKey' => 'db_host', 'check'   => 'require'),
                    'db_port'   => array('default'  => 3306),
                    'pdoDbUser' => array('aliasKey' => 'db_user', 'check'   => 'require'),
                    'pdoDbPwd'  => array('aliasKey' => 'db_pwd', 'check'    => 'require', 'default' => ''),
                    'pdoDbName' => array('aliasKey' => 'db_name', 'check'   => 'require'),
                ));
                $this->execPort($data);
                $data['db_dsn'] = "mysql:host={$data['db_host']}";
                $this->engine = Input::get('pdoDbEngine', null, 'myisam');
            }else{
                $data['db_dsn'] = "sqlite:" . $this->sqliteDbFile();
            }
            $dbType .= '_' . $pdoType;
        }else{
            $dbType = in_array('sqlite3', $dbList) ? 'sqlite3' : 'sqlite';
            $data = array('db_name' => $this->sqliteDbFile());
        }
        if(!in_array($dbType, $dbList)){
            if(stripos($dbType, 'sqlite') === 0 || 
            (isset($data['db_dsn']) && stripos($data['db_dsn'], 'sqlite') === 0)) {
                del_file($data['db_name']);
            }
            return show_json(sprintf(LNG('admin.install.dbTypeError'),$dbType), false);
        }
        if($init['db_type'] != 'pdo') $init['db_type'] = $dbType;

        return array_change_key_case(array_merge($init, $data, array(
            'DB_SQL_LOG'            => true,		// SQL执行错误日志记录	
            'DB_FIELDS_CACHE'       => true,		// 启用字段缓存
            'DB_SQL_BUILD_CACHE'	=> false,		// sql生成build缓存
        )), CASE_UPPER);
    }
    private function execPort(&$data) {
        $host = explode(':', $data['db_host']);
        if(isset($host[1])) {
            $data['db_host'] = $host[0];
            $data['db_port'] = $host[1];
        }
    }

    /**
     * sqlite database文件
     */
    private function sqliteDbFile(){
        @chmod(DATA_PATH, 0755);
        $dbFile = USER_SYSTEM . rand_string(12) . '.php';
        @touch($dbFile);
        return $dbFile;
    }

    /**
     * 支持的数据库类型列表
     * @return void
     */
    private function dbList(){
        $db_exts = array('sqlite', 'sqlite3', 'mysql', 'mysqli', 'pdo_sqlite', 'pdo_mysql');
        $dblist = array_map(function($ext){
            if (extension_loaded($ext)){
                return $ext;
            }
        }, $db_exts);
        return array_filter($dblist);
    }

    /**
     * 2. 账号设置
     * @return void
     */
    private function saveUser(){
        $data = Input::getArray(array(
            'name' => array('check' => 'require'),
            'password' => array('check' => 'require')
        ));
        $this->checkDbInit();

        $userID = 1;
        if(Model('User')->find($userID)) {
            del_file($this->installFastLock);
            if(!Model('User')->userEdit($userID, $data)) {
                show_json(LNG('user.bindUpdateError'), false);
            }
            @touch($this->installLock);
            show_json(LNG('admin.install.updateSuccess'), true, $userID);
        }
        $this->admin = $data;
        $this->init();
    }
    // （检查）数据库初始化
    private function checkDbInit(){
        think_config($GLOBALS['config']['databaseDefault']);
        // think_config($GLOBALS['config']['database']);
        $data = $GLOBALS['config']['database'];
        // 获取数据库类型，配置数据库信息（不指定数据库）
        $type = $data['DB_TYPE'];
        $dbName = $data['DB_NAME'];
        switch ($data['DB_TYPE']) {
            case 'pdo':
                $dsn = explode(':', $data['DB_DSN']);
                $type = $dsn[0];
                $dsn = explode(';', $data['DB_DSN']);
                $GLOBALS['config']['database']['DB_DSN'] = $dsn[0];  // 去掉数据库名
                break;
            case 'mysql':
            case 'mysqli':
                $type = 'mysql';
                $GLOBALS['config']['database']['DB_NAME'] = '';
                break;
            case 'sqlite3':
                $type = 'sqlite';
                break;
        }
        think_config($GLOBALS['config']['database']);
        // 判断数据库（表）是否存在
        $db = Model()->db();
        if ($type != 'sqlite') {
            $exist = $db->execute("show databases like '{$dbName}'");
            if (!$exist) show_json(LNG('ERROR_DB_NOT_EXIST'), false);
            $db->execute("use `{$dbName}`");
        }
        // sqlite可直接调用该方法，无论库文件是否存在
        $tables = $db->getTables();
        if (empty($tables) || !in_array('user', $tables)) {
            $msg = $type == 'sqlite' ? 'dbError' : 'dbTableError';
            show_json(LNG('admin.install.'.$msg), false);
        }
        // 重新配置数据库信息
        $GLOBALS['config']['database'] = $data;
        think_config($GLOBALS['config']['database']);
    }

    /**
     * 初始化数据
     */
    public function init(){
        define('USER_ID',1);
        Cache::deleteAll();
        $this->systemDefault();
		$this->storageDefault();
        $this->initLightApp();
        $this->initPluginList();

        $this->addGroup();
        $this->addAuth();
        $this->roleID = $this->getRoleID();
		$this->addUser();
		KodIO::initSystemPath();

        @touch($this->installLock);
        del_file($this->installFastLock);
        show_json(LNG('admin.install.createSuccess'), true);
    }

    /**
     * 系统默认设置
     */
    public function systemDefault(){
        $default = $this->config['settingSystemDefault'];
        $res = Model('SystemOption')->set($default);
        
        if(!$res) show_json(LNG('admin.install.defSetError'), false);
    }
    /**
     * 默认存储配置
     */
    public function storageDefault(){
        $driver = KodIO::defaultDriver();
        if($driver) return $driver['id'];

        $dataPath = './data/files/';
        if(!is_dir($dataPath)) @mk_dir($dataPath);
        $freeSize = @disk_free_space($dataPath);
        $freeSize = $freeSize ? floor($freeSize / 1024 / 1024 / 1024) : 0;
        $freeSize = $freeSize > 10 ? floor($freeSize / 10) * 10 : 10;
        $data = array (
            'name' => LNG('admin.storage.localStore'),
            'sizeMax' => $freeSize,
            'driver' => 'Local',
            'default' => '1',
            'system' => '1',
            'config' => json_encode(array(
                "basePath" => $dataPath
            )),
        );
        $res = Model('Storage')->add($data);
        if(!$res) show_json(LNG('admin.install.defStoreError'), false);
	}
	
    /**
     * 轻应用列表初始化
     */
    public function initLightApp(){
		Action('explorer.lightApp')->initApp();
    }

    /**
     * 初始化插件列表
     */
    public function initPluginList(){
        Model('Plugin')->viewList();
        $list = Model('Plugin')->loadList();
        foreach($list as $app => $item) {
            Model('Plugin')->changeStatus($app, 1);
        }
    }

    /**
     * 添加根部门
     */
    public function addGroup(){
        $this->in = array(
            "groupID"   => 1,
            "name" 		=> $this->config['settingSystemDefault']['groupRootName'],
			"sizeMax" 	=> 0,
			"parentID"	=> 0,
        );
        $res = ActionCallHook('admin.group.add');
        if(!$res['code']){
            show_json(LNG('admin.install.defGroupError'), false);
        }
    }

    /**
     * 文档权限
     */
    public function addAuth(){
        Model('Auth')->initData();
    }

    /**
     * 系统内置角色
     */
    public function getRoleID(){
        $list = Model('SystemRole')->listData();
        $roleList = array_to_keyvalue($list, 'administrator', 'id');

        $administrator = 1;
        if(!isset($roleList[$administrator])){
            $roleID = $this->roleDefault();
            if(!$roleID) show_json(LNG('admin.install.defRoleError'), false);
        }else{
            $roleID = $roleList[$administrator];
        }
        return $roleID;
    }

    /**
     * 添加角色——Administrator、default
     */
    private function roleDefault(){
        $administrator = array (
            'name' => LNG('admin.role.administrator'),
            'display' => 1,
            'system' => 1,
            'administrator' => 1,
            'auth' => 'explorer.add,explorer.upload,explorer.view,explorer.download,explorer.share,explorer.shareLink,explorer.remove,explorer.edit,explorer.move,explorer.serverDownload,explorer.search,explorer.unzip,explorer.zip,user.edit,user.fav,admin.index.dashboard,admin.index.setting,admin.index.loginLog,admin.index.log,admin.index.server,admin.role.list,admin.role.edit,admin.job.list,admin.job.edit,admin.member.list,admin.member.userEdit,admin.member.userAuth,admin.member.groupEdit,admin.auth.list,admin.auth.edit,admin.plugin.list,admin.plugin.edit,admin.storage.list,admin.storage.edit,admin.autoTask.list,admin.autoTask.edit',
            'label' => 'label-green-deep',
            'sort' => 2,
        );
        $groupOwner = array (
            'name' => LNG('admin.role.group'),
            'display' => 1,
            'system' => 1,
            'auth' => 'explorer.add,explorer.upload,explorer.view,explorer.download,explorer.share,explorer.shareLink,explorer.remove,explorer.edit,explorer.move,explorer.serverDownload,explorer.search,explorer.unzip,explorer.zip,user.edit,user.fav,admin.index.loginLog,admin.index.log,admin.member.list,admin.member.userEdit,admin.member.userAuth,admin.member.groupEdit,admin.auth.list',
            'label' => 'label-blue-deep',
            'sort' => 1,
        );
        $defaultUser = array (
            'name' => LNG('admin.role.default'),
            'display' => 1,
            'system' => 1,
            'auth' => 'explorer.add,explorer.upload,explorer.view,explorer.download,explorer.share,explorer.shareLink,explorer.remove,explorer.edit,explorer.move,explorer.serverDownload,explorer.search,explorer.unzip,explorer.zip,user.edit,user.fav',
            'label' => 'label-blue-normal',
            'sort' => 0,
        );
		Model('SystemRole')->add($defaultUser);
		Model('SystemRole')->add($groupOwner);
        $administrator = Model('SystemRole')->add($administrator);
        return $administrator;
    }

    /**
     * 新增用户-管理员
     */
    public function addUser(){
        $this->in = array(
            "userID"    => 1,
			"name" 		=> !empty($this->admin['name']) ? $this->admin['name'] : 'admin',
			"nickName" 	=> LNG('admin.role.administrator'),
			"password" 	=> !empty($this->admin['password']) ? $this->admin['password'] : 'admin',
            "roleID"	=> $this->roleID,
            "groupInfo" => json_encode(array('1'=>'1')),
			"sizeMax" 	=> 0,
        );
        $res = ActionCallHook('admin.member.add');
        if(!$res) return;
        if(!$res['code']){
            show_json($res['data'], false);
        }
    }
}