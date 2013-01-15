<?php 
class Default_Model_Classes_ReportAbuse extends Default_Model_Classes_UserProfile
{
	protected $reportabuseArray;
	public function __construct($user_id)
	{
		parent::__construct($user_id);
		$this->reportabuseArray = array();
	}
	public function __get($request)
	{
		return $this->$request;
	}
	public function reportabuse($type){
		$db = Zend_db_Table::getDefaultAdapter();
		$getReportOption = $db->select()->from('report_abuse_option',array('*'))
										->where('parent_option_id = 0')
										->where ('option_type = "'.$type.'"');
		$reportOption = $db->query($getReportOption)->fetchAll();
		$new_arr = array();
		foreach($reportOption as $report)
		{
			$arr = array();
			$getSubReportOption = $db->select()->from('report_abuse_option',array('*'))
										->where('parent_option_id = ?',$report['id']);
			$subReportOption = $db->query($getSubReportOption)->fetchAll();
			if(count($subReportOption) > 0)
				$arr = array('main'=>$report,'suboptions'=>$subReportOption);
			else
				$arr = array('main'=>$report);
			$new_arr[] = $arr;
		}
		$this->reportabuseArray = $new_arr;
	} 
	public function setReportData($report_id,$report_to){
		$db = Zend_db_Table::getDefaultAdapter();
		$getReportOption = $db->select()->from('report_abuse_option',array('option_text'))
										->where ('id = "'.$report_id.'"');
		$reportOption = call_user_func_array('array_merge',$db->query($getReportOption)->fetchAll());
		$report_data = "INSERT INTO report_abuse(reported_from,report_to,report_time,report_text) values('".$this->user_id."','".$report_to."',UNIX_TIMESTAMP(CURRENT_TIMESTAMP),'".$reportOption['option_text']."')";
		$db->query($report_data);
	}
}