<?php
  class LogoutController extends Zend_Controller_Action
    {
       
        public function indexAction()
        {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            Zend_Session::destroy(TRUE);
            $this->_redirect(HTTPS_SECURE);
            exit;
        }

    }
