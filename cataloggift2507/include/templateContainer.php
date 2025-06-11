<?php
require_once(dirname(__FILE__).'/config.php');
require_once(DIR_INCLUDE_LIB.'/patTemplate/patErrorManager.php');
require_once(DIR_INCLUDE_LIB.'/patTemplate/patTemplate.php');

//テンプレートコントローラクラス
//　テンプレートエンジンの読み込みと基本のページ表示処理を行う
class templateContainer{
	public $contents;
	public $contentsName;
	private $headerCharset = 'UTF-8';

	const TMPL_ROOT_NEME = '_contents';//ルートのテンプレートタグの名前

	//共通テンプレートのファイル
	const FILE_TMPL_COMMON_HEADER = '_common.header.tmpl.html';//ヘッダ
	const FILE_TMPL_COMMON_FOOTER = '_common.footer.tmpl.html';//フッタ
	const FILE_TMPL_COMMON_UTIL = '_common.util.tmpl.html';//定型文
	const FILE_TMPL_GTM_HEAD = '_gtm.head.tmpl.html';//ヘッダ
	const FILE_TMPL_GTM_BODY = '_gtm.body.tmpl.html';//ボディ

	//パス置換の定数(現状のディレクトリ構造を変える際は注意)
	const REF_COMMON_PATH_LOCAL = '../../../common/';
	const REF_COMMON_PATH_SERVER = '../common/';
	const REF_IMAGE_PATH_LOCAL = '../../images/';
	const REF_IMAGE_PATH_SERVER = 'images/';
	const REF_CSS_PATH_LOCAL = '../../css/';
	const REF_CSS_PATH_SERVER = 'css/';
	const REF_SCRIPTS_PATH_LOCAL = '../../js/';
	const REF_SCRIPTS_PATH_SERVER = 'js/';

	//コンストラクタ
	function __construct($contentsName){

		$this->contentsName = $contentsName;//コンテンツ名取得

		//親ファイル名の接頭辞はコンテンツ名
		$rootTmplName = $this->contentsName.'.tmpl.html';

		//親テンプレートをロード
		$this->contents = new patTemplate();
		$this->contents->setBasedir($this->getTemplatePath());
		$this->contents->readTemplatesFromFile($rootTmplName);

		//共通テンプレートをロード
		//:ヘッダ用
		$tmplCommonHeader = new patTemplate();
		$tmplCommonHeader->setBasedir($this->getTemplatePath());
		$tmplCommonHeader->readTemplatesFromFile(self::FILE_TMPL_COMMON_HEADER);
		//:フッタ用
		$tmplCommonFooter = new patTemplate();
		$tmplCommonFooter->setBasedir($this->getTemplatePath());
		$tmplCommonFooter->readTemplatesFromFile(self::FILE_TMPL_COMMON_FOOTER);

		//定型文用
		$tmplCommonUtil = new patTemplate();
		$tmplCommonUtil->setBasedir($this->getTemplatePath());
		$tmplCommonUtil->readTemplatesFromFile(self::FILE_TMPL_COMMON_UTIL);

		//GTM　head
		$tmplGtmHead = new patTemplate();
		$tmplGtmHead->setBasedir($this->getTemplatePath());
		$tmplGtmHead->readTemplatesFromFile(self::FILE_TMPL_GTM_HEAD);
		//GTM　body
		$tmplGtmBody = new patTemplate();
		$tmplGtmBody->setBasedir($this->getTemplatePath());
		$tmplGtmBody->readTemplatesFromFile(self::FILE_TMPL_GTM_BODY);

		//アプリのルートURLを取得(表示されているページの2階層上)
		$appRootDir = $_SERVER['SERVER_NAME'].dirname(dirname($_SERVER['PHP_SELF']));

		//共通変数の値
		$arrCommonItemVers =  array(
			'APP_ROOT_URL' => $appRootDir
			,'APP_NAME' => APP_NAME
			,'SITE_HOME_URL' => SITE_HOME_URL
			,'HTTP_HOST' => $_SERVER['HTTP_HOST']
			,'HEADER_TITLE' => HEADER_TITLE
		);

		$tmplCommonUtil->addVars(self::TMPL_ROOT_NEME, array(
			'HTTP_HOST' => $_SERVER['HTTP_HOST']
			,'HEADER_TITLE' => HEADER_TITLE
		));

		//定型文の値
		$arrCommonItemUtilVers = array(
			 'COMMON_UTIL_OGP' => $tmplCommonUtil->getParsedTemplate('_ogp')
			,'COMMON_UTIL_TITLE' => trim($tmplCommonUtil->getParsedTemplate('_title'))
			,'COMMON_UTIL_MSG_TXT' => trim($tmplCommonUtil->getParsedTemplate('_msg_txt'))
			,'COMMON_UTIL_ANALYTICS_LOGGER' => $tmplCommonUtil->getParsedTemplate('_analytics_logger')
		);

		$arrCommonItemVers = $arrCommonItemVers + $arrCommonItemUtilVers;//ヘッダとフッタ共通変数もマージ

		$tmplCommonHeader->addVars(self::TMPL_ROOT_NEME, $arrCommonItemVers);
		$tmplCommonFooter->addVars(self::TMPL_ROOT_NEME, $arrCommonItemVers);

		//gtm on/off flg //add fuki
		$gtmTmpName = GTM_FLG ? self::TMPL_ROOT_NEME : '_off';

		//テンプレート共通変数の値
		$arrContentsVers = array(
			'TEML_CHARSET' => $this->headerCharset
			,'COMMON_HEADER' => $tmplCommonHeader->getParsedTemplate(self::TMPL_ROOT_NEME)
			,'COMMON_FOOTER' => $tmplCommonFooter->getParsedTemplate(self::TMPL_ROOT_NEME)
			,'GTM_HEAD' => $tmplGtmHead->getParsedTemplate($gtmTmpName)
			,'GTM_BODY' => $tmplGtmBody->getParsedTemplate($gtmTmpName)
		);

		$arrContentsVers = $arrContentsVers + $arrCommonItemVers;//ヘッダとフッタ共通変数もマージ

		//値挿入
		$this->contents->addVars(self::TMPL_ROOT_NEME, $arrContentsVers);

	}

	//テンプレートのパス取得
	function getTemplatePath(){
		return dirname(__FILE__).'/template';
	}

	//メールテンプレートのパス取得
	function getTemplateMailPath(){
		return dirname(__FILE__).'/template_mail';
	}

	//テンプレート編集用パスをサーバ表示用のパスに変換
	function covServerPath(&$strHtml){
		$strHtml = str_replace(self::REF_COMMON_PATH_LOCAL, self::REF_COMMON_PATH_SERVER, $strHtml);
		$strHtml = str_replace(self::REF_IMAGE_PATH_LOCAL, self::REF_IMAGE_PATH_SERVER, $strHtml);
		$strHtml = str_replace(self::REF_CSS_PATH_LOCAL, self::REF_CSS_PATH_SERVER, $strHtml);
		$strHtml = str_replace(self::REF_SCRIPTS_PATH_LOCAL, self::REF_SCRIPTS_PATH_SERVER, $strHtml);
	}

	//親テンプレートの表示
	function displayContent(){
		$tmplContentsHtml = $this->contents->getParsedTemplate(self::TMPL_ROOT_NEME);
		$this->covServerPath($tmplContentsHtml);
		echo $tmplContentsHtml;//表示出力
	}

}

?>
