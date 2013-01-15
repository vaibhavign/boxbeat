<?php
	class Zend_View_Helper_datahelper extends Zend_View_Helper_Abstract 
	{ 
	function datahelper() { 
	?>
	<script type="text/javascript">
	
	jQuery.inboxdata=<?php echo json_encode($this->view->inboxdata);?>;
	</script>
	<?php
	} 
	}
?>