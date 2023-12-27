<?php 
/**
 * office文档前端解析
 * 
 * doc：https://github.com/SheetJS/js-word  // x
 * docx：https://github.com/mwilliamson/mammoth.js
 * xls/xlsx：https://github.com/SheetJS/sheetjs
 * xlsx：https://github.com/mengshukeji/Luckysheet
 *       https://mengshukeji.github.io/LuckysheetDocs/zh/guide
 *       https://madewith.cn/709
 * ppt：https://github.com/SheetJS/js-ppt   // x
 * pptx：https://github.com/meshesha/PPTXjs
 * 
 */
class officeViewerWebOfficeIndex extends Controller {
    protected $pluginName;
	public function __construct() {
		parent::__construct();
		$this->pluginName = 'officeViewerPlugin';
        $this->appName = 'WebOffice';
    }

    public function index(){
        $plugin = Action($this->pluginName);
        if(!$plugin->allowExt('wb')) {
            $plugin->showTips(LNG('officeViewer.main.invalidExt'), $this->appName);
		}
        $extList = array(
            'docx'  => 'mammothjs', 
            'doc'   => 'mammothjs',
            'xlsx'  => 'luckysheet', // sheetjs
            'xls'   => 'luckysheet',
            'csv'   => 'luckysheet',
            'pptx'  => 'pptxjs',
            'ppt'   => 'pptxjs',
        );
        // doc、ppt不支持，此处为兼容某些旧格式命名的新结构（zip）文件
        $ext = $this->in['ext'];
        if(!isset($extList[$ext])) {
            $plugin->showTips(LNG('officeViewer.main.invalidExt'), $this->appName);
        }
        $app = $extList[$ext];
		$plugin->showWebOffice($app);
    }
}