<?php

class UploadController extends Zend_Controller_Action
{

	private $form;

	public function init()
	{
	$this->form = new Default_Form_UploadForm();
	}
	
    function indexAction()
    {
			 $this->view->form = $this->form;
			 $formData = $this->_request->getPost();

	 if ($this->_request->isPost()) {
	
	 $formData = $this->_request->getPost();
	 if ($this->form->isValid($formData)) {
	 $upload = new Zend_File_Transfer_Adapter_Http();
	 $upload->setDestination("/uploads/files/");
	 try {
	 // upload received file(s)
	 $upload->receive();
	 } catch (Zend_File_Transfer_Exception $e) {
	 $e->getMessage();
	 }
	
	 // so, Finally lets See the Data that we received on Form Submit
	 $uploadedData = $form->getValues();
	 Zend_Debug::dump($uploadedData, 'Form Data:');

 // you MUST use following functions for knowing about uploaded file
 # Returns the file name for 'doc_path' named file element
 $name = $upload->getFileName('doc_path');

 # Returns the size for 'doc_path' named file element
 # Switches of the SI notation to return plain numbers
 $upload->setOption(array('useByteString' => false));
 $size = $upload->getFileSize('doc_path');

 # Returns the mimetype for the 'doc_path' form element
 $mimeType = $upload->getMimeType('doc_path');

 // following lines are just for being sure that we got data
 print "Name of uploaded file: $name";
 print "File Size: $size";
 print "File's Mime Type: $mimeType";

 // New Code For Zend Framework :: Rename Uploaded File
 $renameFile = 'newName.jpg';

 $fullFilePath = '/images/'.$renameFile;

 // Rename uploaded file using Zend Framework
 $filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));

 $filterFileRename -> filter($name);

 exit;
 } 

 } else {

 // this line will be called if data was not submited
 $this->form->populate($formData);
 }
    }

	
}
?>