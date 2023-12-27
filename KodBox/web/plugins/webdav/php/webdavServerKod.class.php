<?php

/**
 * webdav 文件管理处理;
 * 
 * kod自定义扩展支持:
 * 1. 文件属性数组追加 extendFileInfo; 数据:base64_encode(json_encode({}));//hasFile,fileInfoMore,children,fileOutLink...
 * 2. 文件列表数组追加 extendFileList; 数据:base64_encode(json_encode({}));//groupShow,pageInfo,targetSpace
 * 
 * 兼容sabre的文件patch追加协议: https://sabre.io/dav/http-patch/  
 */
class webdavServerKod extends webdavServer {
	public function __construct($DAV_PRE) {
		$this->davPre = $DAV_PRE;
		$this->plugin = Action('webdavPlugin');
		Hook::bind('show_json',array($this,'showErrorCheck'));
	}

	public function run(){
		$method = 'http'.HttpHeader::method();
		if(!method_exists($this,$method)){
			return HttpAuth::error();
		}
		if($method == 'httpOPTIONS'){
			return self::response($this->httpOPTIONS());
		}
		
		$this->checkUser();
		$this->initPath($this->davPre);
		$result = $this->$method();
		if(!$result) return;//文件下载;
		$this->response($result);
    }
	
	// head时一直返回200; 登录失败或无权限则直接返回; 登录检测等成功同理多了文件信息;
	// 兼容 win10下office打开异常情况;
	private function checkErrorHead(){		
		if(HttpHeader::method() != 'HEAD') return;
		self::response(array(
			'code' => 200,
			'headers' => array(
				'Content-Type: text/html; charset=utf8',
			)
		));exit;
	}
	
	// 错误处理;(空间不足,无权限等)
	public function showErrorCheck($json){
		if(!is_array($json)) return $json;
		if($json['code'] == true || $json['code'] == 1) return $json;
		
		$this->checkErrorHead();
		$this->lastError = is_string($json['data']) ?$json['data']:'';
		$this->response(array('code'=>404));exit;
	}
	public function getLastError(){
		$error = $this->lastError;
		if(!$error){$error = Action('explorer.auth')->getLastError();}
		if(!$error){$error = IO::getLastError();}
		return $error;
	}

	/**
	 * 用户登录校验;权限判断;
	 * 性能优化: 通过cookie处理为已登录; (避免ad域用户或用户集成每次进行登录验证;)
	 * 
	 */
	public function checkUser(){
		$userInfo = Session::get("kodUser");
	    if(!$userInfo || !is_array($userInfo)){
    	    $user = HttpAuth::get();
			// 兼容webdav挂载不支持中文用户名; 中文名用户名编解码处理;
			if(substr($user['user'],0,2) == '$$'){
				$user['user'] = rawurldecode(substr($user['user'],2));
			}
			// Windows下wps打开文件需要再次输入用户名密码情况; 用户名带入了电脑名称兼容(eg:'DESKTOP-E12RTST\admin:123')
			$startPose = strrpos($user['user'],"\\"); 
			if($startPose){$user['user'] = substr($user['user'],$startPose + 1);}
			
    		$find = ActionCall('user.index.userInfo', $user['user'],$user['pass']);
    		if ( !is_array($find) || !isset($find['userID']) ){
    			// $this->plugin->log(array($user,$find,$_SERVER['HTTP_AUTHORIZATION'],$GLOBALS['_SERVER']));
				$this->checkErrorHead();
    			return HttpAuth::error();
    		}
    		ActionCall('user.index.loginSuccess',$find);
			
			// 登录日志;
			$needLog = time() - intval($find['lastLogin']) >= 60; // 超过1分钟才记录
			if($needLog && HttpHeader::method() == 'OPTIONS'){
				Model('User')->userEdit($find['userID'],array("lastLogin"=>time()));
				ActionCall('admin.log.loginLog');
			}
	    }
		if(!$this->plugin->authCheck()){
			$this->checkErrorHead();
			$this->lastError = LNG('common.noPermission');
			$this->response(array('code'=>404));exit;
		}
	}
	public function parsePath($path){
		$options   = $this->plugin->getConfig();
		$rootBlock = '{block:files}/';
		$rootPath  = $options['pathAllow'] == 'self' ? MY_HOME:$rootBlock;
		if(!$path || $path == '/') return $rootPath;

		$pathArr = explode('/',KodIO::clear(trim($path,'/')));
		if($rootPath == $rootBlock){
			$rootList = $this->pathBlockRoot();
			$this->rootPathAutoLang($rootList,$pathArr);
		}else{
			$rootList = Action('explorer.list')->path($rootPath);
		}
		return $this->pathInfoDeep($rootList,$pathArr);
	}

	//获取{block:files}/下面的子文件夹;(从pathList直接获取较耗时(70ms),性能优化)  
	private function pathBlockRoot(){
		$list = array(
			array("path"=> KodIO::KOD_USER_FAV,'name'=>LNG('explorer.toolbar.fav')),
			array("path"=> KodIO::make(Session::get('kodUser.sourceInfo.sourceID')),'name'=>LNG('explorer.toolbar.rootPath')),
			array("path"=> KodIO::KOD_GROUP_ROOT_SELF,'name'=>LNG('explorer.toolbar.myGroup')),
			array("path"=> KodIO::KOD_USER_SHARE_TO_ME,'name'=> LNG('explorer.toolbar.shareToMe')),
		);
		// 企业网盘;
		$groupArray = Action('filter.userGroup')->userGroupRoot();
	    if (is_array($groupArray) && $groupArray[0]){
			$groupInfo = Model('Group')->getInfo($groupArray[0]);
			$list[] = array("path"=> KodIO::make($groupInfo['sourceInfo']['sourceID']),'name'=>$groupInfo['name']);
		}
		return array('folderList'=>$list,'fileList'=>array());
	}
	
	// 如果挂载全部路径; 第一层路径自适应多语言处理;
	private function rootPathAutoLang($rootList,&$pathArr){
		$rootPathName = array_to_keyvalue($rootList['folderList'],'','name');
		if(in_array($pathArr[0],$rootPathName)) return;

		$langKeys = $this->loadLangKeys();
		foreach($langKeys as $key=>$langValues){
			if(in_array($pathArr[0],$langValues)){
				$pathArr[0] = LNG($key);break;
			}
		}
	}
	
	// 获取key对应多个语言的值; [收藏夹,个人空间,我所在的部门,与我协作]; //企业网盘为部门名
	private function loadLangKeys(){
		$langKeys = Cache::get('webdav_lang_path_root');
		if(is_array($langKeys)) return $langKeys;
		
		$langKeys = array(
			'explorer.toolbar.fav'			=> array(),	// 收藏夹
			'explorer.toolbar.rootPath'		=> array(),	// 个人空间
			'explorer.toolbar.myGroup'		=> array(),	// 我所在的部门
			'explorer.toolbar.shareToMe'	=> array(),	// 与我协作
		);
		$languageList = $GLOBALS['config']['settingAll']['language'];
		foreach($languageList as $lang=>$info){
			$langFile = LANGUAGE_PATH.$lang.'/index.php';
			$langArr  = include($langFile);
			if(!is_array($langArr)) continue;
			foreach ($langKeys as $key=>$val){
				if(!$langArr[$key]) continue;
				$langKeys[$key][] = $langArr[$key];
			}
		}
		$langKeys['explorer.toolbar.rootPath'][] = 'my'; // 增加;
		Cache::set('webdav_lang_path_root',$langKeys,3600);
		return $langKeys;
	}
	
	/**
	 * 向下回溯路径;
	 */
	private function pathInfoDeep($parent,$pathArr){
		$list = $this->pathListMerge($parent);
		$itemArr = array_to_keyvalue($list,'name');
		$item = $itemArr[$pathArr[0]];
		if(!$item) return false;
		if(count($pathArr) == 1) return $item['path'];
		
		$pathAppend = implode('/',array_slice($pathArr,1));
		$newPath = KodIO::clear($item['path'].'/'.$pathAppend);
		$info = IO::infoFull($newPath);

		// 已存在回收站中处理;
		if($info && $info['isDelete'] == '1'){
			$resetName = $info['name'] .date('(H-i-s)');
			if($info['type'] == 'file'){
				$ext = '.'.get_path_ext($info['name']);
				$theName   = substr($info['name'],0,strlen($info['name']) - strlen($ext));
				$resetName = $theName.date('(H-i-s)').$ext;
			}
			IO::rename($info['path'],$resetName);
			$info = IO::infoFull($newPath);
		}
		// pr($newPath,$item,$pathArr,$info,count($parent['folderList']));
		if($info) return $info['path'];

		$parent = Action('explorer.list')->path($item['path']);
		$result  = $this->pathInfoDeep($parent,array_slice($pathArr,1));
		if(!$result){
			$result = $newPath;
			//虚拟目录追; 没找到字内容;则认为不存在;
			if(Action('explorer.auth')->pathOnlyShow($item['path']) ){
				$result = false;
			}
		}
		return $result;
	}
	
	public function pathInfo($path){
		return IO::info($path);
	}
	
	public function can($path,$action){
		$result = Action('explorer.auth')->fileCan($path,$action);
		// 编辑;则检测当前存储空间使用情况;
		if($result && $action == 'edit'){
			$result = Action('explorer.auth')->spaceAllow($path);
		}
		return $result;
	}
	public function pathExists($path,$allowInRecycle = false){
		$info = IO::infoFull($path);
		if(!$info) return false;
		if(!$allowInRecycle && $info['isDelete'] == '1') return false;
		return true;
	}
	
	/**
	 * 文档属性及列表;
	 * 不存在:404;存在207;  文件--该文件属性item; 文件夹--该文件属性item + 多个子内容属性
	 */
	public function pathList($path){
		if(!$path) return false;
		$info  = IO::infoFull($path);
		if(!$info && !Action('explorer.auth')->pathOnlyShow($path) ){
			return false;
		}
		
		// if($info && $info['isDelete'] == '1') return false;//回收站中; 允许复制下载等操作;
		if(!$this->can($path,'show')) return false;
		if($info && $info['type'] == 'file'){ //单个文件;
			return array('fileList'=>array($info),'current'=>$info);
		}
		
		$pathParse = KodIO::parse($path);
		// 分页大小处理--不分页; 搜索结果除外;
		if($pathParse['type'] != KodIO::KOD_SEARCH){
			$GLOBALS['in']['pageNum'] = -1;
		}
		// write_log([$path,$pathParse,$GLOBALS['in']],'test');		
		return Action('explorer.list')->path($path);
	}
	
	public function pathMkdir($pathBefore){
		$path = $this->pathCreateParent($pathBefore);
		if(!$path || !$this->can($path,'edit')) return false;
		return IO::mkdir($path);
	}
	public function pathOut($path){
		if(!$this->pathExists($path) || !$this->can($path,'view')){
			$this->response(array('code' => 404));exit;
		}
		if(IO::size($path)<=0) return;//空文件处理;
		//部分webdav客户端不支持301跳转;
		if($this->notSupportHeader()){
			IO::fileOutServer($path); 
		}else{
			IO::fileOut($path); 
		}
	}
	// GET 下载文件;是否支持301跳转;对象存储下载走直连;
	private function notSupportHeader(){
		$software = array(
			'ReaddleDAV Documents',	// ios Documents 不支持;
			'GstpClient',			// goodsync 同步到对象存储问题
		);
		$ua = $_SERVER['HTTP_USER_AGENT'];
		foreach ($software as $type){
			if(stristr($ua,$type)) return true;
		}
		return false;
	}
	
	// 收藏夹下文件夹处理;(新建,上传)
	private function pathCreateParent($path){
		if($path) return $path;
		$inPath  = $this->pathGet();
		if(IO::pathFather($inPath) == '.recycle') return false;
		$pathFather = rtrim($this->parsePath(IO::pathFather($inPath)),'/');
		return $pathFather.'/'.IO::pathThis($inPath);
	}
	
	public function pathPut($path,$localFile=''){
		$pathBefore = $path;
		$path = $this->pathCreateParent($path);
		if(!$path || !$this->can($path,'edit')) return false;
		$name = IO::pathThis($this->pathGet());
		$info = IO::infoFull($path);
		if($info){	// 文件已存在; 则使用文件父目录追加文件名;
			$uploadPath = rtrim(IO::pathFather($info['path']),'/').'/'.$name; //构建上层目录追加文件名;
		}else{
			// 首次请求创建,文件不存在; 则使用{source:xx}/newfile.txt; 自动创建文件夹: /src/aa/s.txt => / [文件夹不存在时]
			$pathFatherStr = get_path_father($path);
			$pathFather    = IO::mkdir($pathFatherStr); 
			$uploadPath    = rtrim($pathFather,'/').'/'.$name;
			$this->plugin->log("pathPut-mkdir:pathFatherStr=$pathFatherStr;pathFather=$pathFather;uploadPath=$uploadPath");
			//$uploadPath = $path;
		}
		$this->pathPutCheckKod($uploadPath);

		// 传入了文件; wscp等直接一次上传处理的情况;  windows/mac等会调用锁定,解锁,判断是否存在等之后再上传;
		// 文件夹下已存在,或在回收站中处理;
		// 删除临时文件; mac系统生成两次 ._file.txt;
		$size = 0;
		if($localFile){
			$size = filesize($localFile);
			$result = IO::upload($uploadPath,$localFile,true,REPEAT_REPLACE);
			// $result = IO::move($localFile,$uploadPath,REPEAT_REPLACE);
			$this->pathPutRemoveTemp($uploadPath);
		}else{
			if(!$info){ // 不存在,创建;
				$result = IO::mkfile($uploadPath,'',REPEAT_REPLACE);
			}
			$result = true;	
		}
		$this->plugin->log("upload=$uploadPath;path=$path,$pathBefore;res=$result;local=$localFile;size=".$size);
		return $result;
	}
	private function pathPutRemoveTemp($path){
		$pathArr = explode('/',$path);
		$pathArr[count($pathArr) - 1] = '._'.$pathArr[count($pathArr) - 1];
		$tempPath = implode('/',$pathArr);
		
		$tempInfo = IO::infoFull($tempPath);
		if($tempInfo && $tempInfo['type'] == 'file'){
			IO::remove($tempInfo['path'],false);
		}
	}
	
	// kodbox 挂载链接
	private function pathPutCheckKod($uploadFile){
		if($_SERVER['HTTP_X_DAV_UPLOAD'] != 'kodbox') return;
		if(!$_SERVER['HTTP_X_DAV_ARGS']) return;
		
		$args = json_decode(base64_decode($_SERVER['HTTP_X_DAV_ARGS']),true);
		if(!is_array($args)) return false;
		$io   = IO::init('/');
		$info = array(
			'name' => $io->pathThis($uploadFile),
			'path' => $io->pathFather($uploadFile)
		);
		if($args['uploadWeb'] && $args['checkType'] == 'checkHash'){
			// 前端上传文件夹,层级处理; eg: /self/a1/a2/a3.txt ; fullPath: /a1/a2/a3.txt ===> /self/
			$fullPath = $args['fullPath'] ? $args['fullPath']:'';
			$fullArr  = explode('/', trim($fullPath,'/'));
			if(count($fullArr) > 1){
				$uriArr = explode('/', trim($this->pathGet(),'/'));
				$uriArr = array_slice($uriArr,0,count($uriArr) - count($fullArr));
				$info['path'] = $this->parsePath('/'.implode('/',$uriArr).'/');
			}
			
			$argsCheck = array('path'=>$info['path']);//,'size'=>$args['size']
			$link = Action('user.index')->apiSignMake('explorer/upload/fileUpload',$argsCheck,false,false,true);
			$info['addUploadParam'] = $link;
		}
		$GLOBALS['in'] = array_merge($GLOBALS['in'],$args,$info);
		Action('explorer.upload')->fileUpload();exit;
	}
	
	public function pathRemove($path){
		if(!$this->can($path,'remove')) return false;
		$tempInfo = IO::infoFull($path);
		if(!$tempInfo) return true;
		
		$toRecycle = Model('UserOption')->get('recycleOpen');
		if($tempInfo['isDelete'] == '1'){$toRecycle = false;}
		return IO::remove($tempInfo['path'], $toRecycle);
	}
	public function pathMove($path,$dest){
		$pathUrl = $this->pathGet();
		$destURL = $this->pathGet(true);		
		$path 	= $this->parsePath($pathUrl);
		$dest   = $this->parsePath(IO::pathFather($destURL)); //多出一层-来源文件(夹)名
		$this->plugin->log("from=$path;to=$dest;$pathUrl;$destURL");

		// 目录不变,重命名,(编辑文件)
		$io = IO::init('/');
		if($io->pathFather($pathUrl) == $io->pathFather($destURL)){
			if(!$this->can($path,'edit')) return false;
			$destFile = rtrim($dest,'/').'/'.$io->pathThis($destURL);
			$this->plugin->log("edit=$destFile;exists=".intval($this->pathExists($destFile)));

			/**
			 * office 编辑保存最后落地时处理（导致历史记录丢失）
			 * window下文件保存处理(office文件保存时 file=>file.tmp 不做该操作,避免历史版本丢失)
			 * 
			 * 0. 上传~tmp1601041332501525796.TMP //锁定,上传,解锁;
			 * 1. 移动 test.docx => test~388C66.tmp 				// 改造,识别到之后不进行移动重命名;
			 * 2. 移动 ~tmp1601041332501525796.TMP => test.docx; 	// 改造;目标文件已存在则更新文件;删除原文件;
			 * 3. 删除 test~388C66.tmp  
			 * 
			 * window + raidrive + wps编辑
			 *      delete ~$file.docx
             *      put    ~$file.docx
             *      put    ~tmpxxx.TMP
             *      delete ~$file.docx
             *      move   file.docx   file~xxx.tmp
             *      move   ~tmpxxx.TMP file.docx
             *      delete file~xxx.tmp
			 */
			$fromFile 	= $io->pathThis($pathUrl);
			$toFile 	= $io->pathThis($destURL);
			$fromExt 	= get_path_ext($pathUrl);
			$toExt   	= get_path_ext($destURL);// 误判情况: 将xx/aa.docx 移动到xx/aa~xxx.tmp会失败;
			$officeExt 	= array('doc','docx','xls','xlsx','ppt','pptx');
			if( $toExt == 'tmp' && in_array($fromExt,$officeExt) && strstr($toFile,'~')){
				$result =  IO::mkfile($destFile);
			    $this->plugin->log("move mkfile=$path;$pathUrl;$destURL;result=".$result);
			    return $result;
			}
			// 都存在则覆盖；
			if( $this->pathExists($path,true) && $this->pathExists($destFile) ){
				$destFileInfo = IO::infoFull($destFile);

				// $content = IO::getContent($path);
				// IO::setContent($destFileInfo['path'],$content);
				// IO::remove($path);$result = $destFileInfo['path'];
				$result  = IO::saveFile($path,$destFileInfo['path']);//覆盖保存;
				$this->plugin->log("move saveFile; to=$path;toFile=".$destFileInfo['path'].';result='.$result);
				return $result;
			}
			return IO::rename($path,$io->pathThis($destURL));
		}
		
		if(!$this->can($path,'remove')) return false;
		if(!$this->can($dest,'edit')) return false;
		
		// 名称不同先重命名;
		if( $io->pathThis($destURL) != $io->pathThis($pathUrl) ){
			$path = IO::rename($path,$io->pathThis($destURL));
		}
		return IO::move($path,$dest);
	}
	public function pathCopy($path,$dest){
		$pathUrl = $this->pathGet();
		$destURL = $this->pathGet(true);		
		$path 	= $this->parsePath($pathUrl);
		$dest   = $this->parsePath(IO::pathFather($destURL)); //多出一层-来源文件(夹)名
		$this->plugin->log("from=$path;to=$dest;$pathUrl;$destURL");

		if(!$this->can($path,'download')) return false;
		if(!$this->can($dest,'edit')) return false;
		
		$fromName = get_path_this($pathUrl); 
		$destName = get_path_this($destURL);
		$destName = $fromName != $destName ? $destName : '';
		return IO::copy($path,$dest,false,$destName);
	}
	
	// 上传临时目录; 优化: 默认存储io为本地时,临时目录切换到对应目录的temp/下;(减少从头temp读取->写入到存储i)
	public function uploadFileTemp(){
		$tempPath = TEMP_FILES;
		$path = $this->pathCreateParent();// 上传到目录转换; /dav/test/1.txt=> {source:23}/1.txt;
		$driverInfo = KodIO::pathDriverType($path);
		if($driverInfo && $driverInfo['type'] == 'local'){
			$truePath = rtrim($driverInfo['path'],'/').'/';
			$isSame = KodIO::isSameDisk($truePath,TEMP_FILES);
			if(!$isSame && file_exists($truePath)){$tempPath = $truePath;}
		}
		
		if(!file_exists($tempPath)){
			@mk_dir($tempPath);
			touch($tempPath.'index.html');
		}
		return $tempPath;
	}
	
	// 文件编辑锁添加或移除;(office/wps: 打开编辑时会添加; 保存时会添加/解除; 关闭文件时会解锁)
	public function fileLock($path){
		$info = $this->fileLockCheck($path);if(!$info) return;
		Model("Source")->metaSet($info['sourceID'],'systemLock',USER_ID);
		Model("Source")->metaSet($info['sourceID'],'systemLockTime',time());		
	}
	public function fileUnLock($path){
		$info = $this->fileLockCheck($path);if(!$info) return;
		Model("Source")->metaSet($info['sourceID'],'systemLock',null);
		Model("Source")->metaSet($info['sourceID'],'systemLockTime',null);
	}
	private function fileLockCheck($path){
		$info = IO::infoFull($path);
		if(!$info || !$info['sourceID'] || !USER_ID) return;
		if(!$this->can($path,'edit')) return;
		return $info;
	}
}