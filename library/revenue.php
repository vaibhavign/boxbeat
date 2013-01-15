<?php
// Class revenue
class revenue{
	private $data;
	private $method;
	private $returnType;
	private $page;
	private $error = false;
	
	function __construct($data,$method){
		
		$this->data = $data;
		$this->method = $method;
		$this->analyseInput();
	}
	
	protected function analyseInput(){
		// function to return set of server errors
		switch($this->method){
			case "ORDER" :
			
			$mapper  = new Api_Model_RevenueMapper();
			$orderDetails = $mapper->getOrderDetails($this->data);
			
			
			break;
			
			default :
			
			break;
			
			
		}

	}
	
	
	protected function getRevenueType(){
		$revenueTypeString = $this->data;
		$revenueTypeArray = explode("");
	
	}
	
	
}




?>