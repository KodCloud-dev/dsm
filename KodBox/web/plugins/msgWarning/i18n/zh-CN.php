<?php
return array(
	'msgWarning.meta.name'			=> "消息预警",
	'msgWarning.meta.title'			=> "系统异常消息预警",
	'msgWarning.meta.desc'			=> "系统使用状态异常时,发送消息预警给管理员,以便及时处理,保障系统正常运行",
	
	'msgWarning.config.setDesc'		=> "<div class='info-alert info-alert-blue p-10 align-left can-select can-right-menu'>
	<li>本插件用于系统异常消息提醒,具体配置可在<a href='./#admin/tools/warning' target='_blank'>安全管控-消息预警</a>进行</li>
	</div>",
	'msgWarning.config.sysNtc'		=> '系统消息',
	'msgWarning.config.sysNtcDesc'	=> '<div class="desc mt-10 mb-10">此项检测管理员账号、存储空间等使用情况,如提示异常,请及时处理,以保障系统正常运行</div>',
	'msgWarning.config.setNtc'		=> '通知设置',
	'msgWarning.config.openNtc'		=> '开启预警',
	'msgWarning.config.openNtcDesc'	=> '此项检测CPU、内存使用情况,如出现异常,将连同系统异常消息(如果存在)发送给指定目标',
	'msgWarning.config.warnType'	=> '预警类型',
	'msgWarning.config.warnTypeCpu'	=> 'CPU使用',
	'msgWarning.config.warnTypeMem'	=> '内存使用',
	'msgWarning.config.useRatio'	=> '使用占比',
	'msgWarning.config.useRatioDesc'=> '使用占比超过M%',
	'msgWarning.config.useTime'		=> '持续时长',
	'msgWarning.config.useTimeTips' => '持续时长不能低于10分钟！',
	'msgWarning.config.useTimeDesc'	=> '分钟，使用占比超过M%，持续时间超过N分钟，则触发通知提醒',
	'msgWarning.config.sendType'	=> '发送方式',
	'msgWarning.config.dingTalk'	=> '钉钉',
	'msgWarning.config.weChat'		=> '企业微信',
	'msgWarning.config.email'		=> '邮件',
	'msgWarning.config.target'		=> '发送目标',
	'msgWarning.config.targetDesc'	=> "选择的目标用户,需绑定了有效的指定发送方式",

	'msgWarning.main.tipsTitle'		=> '运行警告',
	'msgWarning.main.msgSysOK'		=> '系统正常！',
	'msgWarning.main.msgPwdErr'		=> '您使用的是初始密码,为确保安全请尽快修改密码',
	'msgWarning.main.msgEmlErr'		=> '您尚未绑定邮箱,为确保通知或找回密码等功能正常,请尽快绑定邮箱',
	'msgWarning.main.msgSysPathErr'	=> '服务器系统路径异常(根目录"%s"需有可读权限,或启用exec函数)',
	'msgWarning.main.msgSysSizeErr'	=> '服务器系统盘剩余空间不足(%s)',
	'msgWarning.main.msgDefPathErr'	=> '系统 <a href="%s" style="padding:0px;text-decoration:none;">默认存储</a> 异常,请检查相关配置及读写权限',
	'msgWarning.main.msgDefSizeErr'	=> '系统 <a href="%s" style="padding:0px;text-decoration:none;">默认存储</a> 剩余空间不足(%s)',
	'msgWarning.main.setNow'		=> '立即设置',
	'msgWarning.main.msgSysErr'		=> '服务器在近%s分钟内,%s使用率持续超过%s(目前为%s),为避免影响系统的正常使用,请检查并优化相关配置',
	'msgWarning.main.msgEmpty'		=> '为空！',
	'msgWarning.main.msgFmtErr'		=> '格式错误！',
	'msgWarning.main.ignoreTips'	=> '暂不提醒',

	'msgWarning.main.taskTitle'		=> '消息预警',
	'msgWarning.main.taskDesc'		=> '系统用量消息预警,此任务来自于【消息预警】插件',
	'msgWarning.main.memory'		=> '内存',
	'msgWarning.main.ntcTitle'		=> '服务器异常提醒',
);