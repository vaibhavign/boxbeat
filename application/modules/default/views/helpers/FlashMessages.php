<?php
class Zend_View_Helper_FlashMessages extends Zend_View_Helper_Abstract
{
    public function flashMessages()
    {
        $messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        $output = '';
       
        if (!empty($messages)) {
            $output .= '<div class="welldone_message_container" style="position:absolute;top:0;right:0">';
            foreach ($messages as $message) {
                $output .= '<div class="welldone_content">' . current($message) . '</div>';
            }
            $output .= '</div>';
        }
       
        return $output;
    }
}
?>
