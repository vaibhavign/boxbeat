<?php
class cssparser {
  var $css;
  var $html;
  
  function cssparser($html = true) {
    // Register "destructor"
    register_shutdown_function(array(&$this, "finalize"));
    $this->html = ($html != false);
    $this->Clear();
  }
  
  function finalize() {
    unset($this->css);
  }
  
  function Clear() {
    unset($this->css);
    $this->css = array();
   
  }
  
  function SetHTML($html) {
    $this->html = ($html != false);
  }
  
  function Add($key, $codestr) {
    $key = strtolower($key);
    $codestr = strtolower($codestr);
    if(!isset($this->css[$key])) {
      $this->css[$key] = array();
    }
    $codes = explode(";",$codestr);
    if(count($codes) > 0) {
      foreach($codes as $code) {
        $code = trim($code);
        list($codekey, $codevalue) = explode(":",$code);
        if(strlen($codekey) > 0) {
          $this->css[$key][trim($codekey)] = trim($codevalue);
        }
      }
    }
  }
  
  function Get($key, $property) {
    $key = strtolower($key);
    $property = strtolower($property);
    
    list($tag, $subtag) = explode(":",$key);
    list($tag, $class) = explode(".",$tag);
    list($tag, $id) = explode("#",$tag);
    $result = "";
    foreach($this->css as $_tag => $value) {
      list($_tag, $_subtag) = explode(":",$_tag);
      list($_tag, $_class) = explode(".",$_tag);
      list($_tag, $_id) = explode("#",$_tag);
      
      $tagmatch = (strcmp($tag, $_tag) == 0) | (strlen($_tag) == 0);
      $subtagmatch = (strcmp($subtag, $_subtag) == 0) | (strlen($_subtag) == 0);
      $classmatch = (strcmp($class, $_class) == 0) | (strlen($_class) == 0);
      $idmatch = (strcmp($id, $_id) == 0);
      
      if($tagmatch & $subtagmatch & $classmatch & $idmatch) {
        $temp = $_tag;
        if((strlen($temp) > 0) & (strlen($_class) > 0)) {
          $temp .= ".".$_class;
        } elseif(strlen($temp) == 0) {
          $temp = ".".$_class;
        }
        if((strlen($temp) > 0) & (strlen($_subtag) > 0)) {
          $temp .= ":".$_subtag;
        } elseif(strlen($temp) == 0) {
          $temp = ":".$_subtag;
        }
        if(isset($this->css[$temp][$property])) {
          $result = $this->css[$temp][$property];
        }
      }
    }
    return $result;
  }
  
  function GetSection($key) {
    $key = strtolower($key);
    
    list($tag, $subtag) = explode(":",$key);
    list($tag, $class) = explode(".",$tag);
    list($tag, $id) = explode("#",$tag);
    $result = array();
    foreach($this->css as $_tag => $value) {
      list($_tag, $_subtag) = explode(":",$_tag);
      list($_tag, $_class) = explode(".",$_tag);
      list($_tag, $_id) = explode("#",$_tag);
      
      $tagmatch = (strcmp($tag, $_tag) == 0) | (strlen($_tag) == 0);
      $subtagmatch = (strcmp($subtag, $_subtag) == 0) | (strlen($_subtag) == 0);
      $classmatch = (strcmp($class, $_class) == 0) | (strlen($_class) == 0);
      $idmatch = (strcmp($id, $_id) == 0);
      
      if($tagmatch & $subtagmatch & $classmatch & $idmatch) {
        $temp = $_tag;
        if((strlen($temp) > 0) & (strlen($_class) > 0)) {
          $temp .= ".".$_class;
        } elseif(strlen($temp) == 0) {
          $temp = ".".$_class;
        }
        if((strlen($temp) > 0) & (strlen($_subtag) > 0)) {
          $temp .= ":".$_subtag;
        } elseif(strlen($temp) == 0) {
          $temp = ":".$_subtag;
        }
        foreach($this->css[$temp] as $property => $value) {
          $result[$property] = $value;
        }
      }
    }
    return $result;
  }
  
  function ParseStr($str) {
    $this->Clear();
    // Remove comments
    $str = preg_replace("/\/\*(.*)?\*\//Usi", "", $str);
    // Parse this damn csscode
    $parts = explode("}",$str);
    if(count($parts) > 0) {
      foreach($parts as $part) {
        list($keystr,$codestr) = explode("{",$part);
        $keys = explode(",",trim($keystr));
        if(count($keys) > 0) {
          foreach($keys as $key) {
            if(strlen($key) > 0) {
              $key = str_replace("\n", "", $key);
              $key = str_replace("\\", "", $key);
              $this->Add($key, trim($codestr));
            }
          }
        }
      }
    }
    //
    return (count($this->css) > 0);
  }
  
  function Parse($filename) {
    $this->Clear();
    if(file_exists($filename)) {
      return $this->ParseStr(file_get_contents($filename));
    } else {
      return false;
    }
  }
  
  function GetCSS() {
    $result = "";
    foreach($this->css as $key => $values) {
      $result .= $key." {\n";
      foreach($values as $key => $value) {
        $result .= "  $key: $value;\n";
      }
      $result .= "}\n\n";
    }
    return $result;
  }
  function setAttribute($tag,$attr,$value)
  {
	$this->css[$tag][$attr] = $value;
  }

  function getAttribute($tag,$attr)
  {
	return $this->css[$tag][$attr];
  }
  
  function saveCss($filename)
  {
  	$result = '';
	foreach($this->css as $key => $values) {
	  $result .= $key." {\n";
	  foreach($this->css[$key] as $key => $value) {
		$result .= "  $key: $value;\n";
	  }
	  $result .= "}\n\n";
	}
	file_put_contents($filename,$result);
  }
  
  //Returns a css string
  public function getCssOfElement($css_tag)
	{
		$result = '';
		$tags = $this->css[$css_tag];
		if($tags){
		 foreach($tags as $key => $value) {
			$result .= "  $key: $value;";
		  }
		}
		return $result;
	}
	
	//Converts css string into array and assign it to css object
	public function setCssOfElement($tag,$mycss)
	{
		$arr = explode(';',trim($mycss));
		$new_attribute_arr = array();
		for($i=0; $i<sizeof($arr) - 1; $i++)
		{
			$values_arr = explode(':',$arr[$i]);
			$new_attribute_arr[trim($values_arr[0])] = trim($values_arr[1]);
		}
		$this->css[$tag] = $new_attribute_arr;
	}
}
?>
