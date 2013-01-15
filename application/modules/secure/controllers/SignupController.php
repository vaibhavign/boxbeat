<?php
class Secure_SignupController extends Zend_Controller_Action
{
	private $cityList;
    public function init()
    {
		//$dataname = new Secure_Model_SignupMapper();
		//$cityData = $dataname->listCity();
		$cityMappers = new Secure_Model_CityMapper();
		//echo "<pre>";
		$cityList = $cityMappers->getCityList();
		$this->view->cityList = $cityMappers->getCityList();
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		if($_GET['action']=='process'){
       		$zendGetData = file_get_contents("http://v5.com/secure/register/xml?fullname=".$_GET['fullname']."&username=".$_GET['username']."&emailaddress=".$_GET['email']."&passwords=".$_GET['password']."&location=".$_GET['city']);
			$this->_redirect('/home');
		}
		$view = new Zend_View();
		$this->view->citiesList=$this->cityList;
	}
	
}
