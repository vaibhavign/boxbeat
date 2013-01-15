<?php 
require_once APPLICATION_PATH.'/includes/DataRender.php';
require_once APPLICATION_PATH.'/includes/Utility.php';
class PluginsController extends Zend_Controller_Action
{
	protected $current_user;
    public function init()
    {
		$this->current_user = (int)$_SESSION['USER']['userId'];
	}
	public function indexAction()
    {
    }
	public function shareAction()
	{
		$user = new Default_Model_Classes_Share($this->current_user);
		$user->setSharePluginsData($_POST);
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/plugins/');
		$html->share_array = $user->__get("shareArray");
		echo $html->render("share.phtml");
		exit;
	}
	public function shareEventAction()
	{
		$user = new Default_Model_Classes_Share($this->current_user);
		$user->shareActionPerform($_POST);
		exit;
	}
	public function callbackAction()
	{
		$user = new Default_Model_Classes_Share($this->current_user);
		$user->twitterCallbackAction();
		exit;
	}
	public function redirectAction()
	{
		$user = new Default_Model_Classes_Share($this->current_user);
		$user->twitterRedirectAction();
		exit;
	}
	public function reportabuseAction(){
		$reportabuseuser = new Default_Model_Classes_ReportAbuse($this->current_user);
		extract($_POST);
		if($part == "reportData")
		{
			if($report_text_id!='undefined')
				$reportabuseuser->setReportData($report_text_id,$report_to);
			$sql = "INSERT INTO user_block_history(blocked_by,blocked_user,block_time) values($this->current_user,$blocked_user,UNIX_TIMESTAMP())";
			if($blocked_user != 0 && $blocked_option == 'true')
				Zend_Db_Table::getDefaultAdapter()->query($sql);
			exit;
		}
		$type = array('profile'=>0,'deal'=>1,'need'=>2,'comments'=>3);
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/plugins/');
		$reportabuseuser->reportabuse($type['profile']);
		$html->report_type = "profile";
		$html->report_to = $_POST['report_to'];
		$userData = $reportabuseuser->__get('reportabuseArray');
		$html->showOptions = $userData;
		echo $this->view->bodyText = $html->render('reportabuse.phtml');
		exit;
	}
}