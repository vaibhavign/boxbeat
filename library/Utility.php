<?php
class Utility
{
	public function getArray($arr)
	{
		$new_arr = array();
		foreach($arr as $arr_split)
		{
			if(is_array($arr_split))
			{
				foreach($arr_split as $val)
					$new_arr[] = $val;
			}
			else
				$new_arr[] = $arr_split;
		}
		return $new_arr;
	}
	
	public function get_string_between($string, $start, $end)
	{
		$string = " ". $string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
	public function countInArray($arr,$field,$value)
	{
		//print_r($arr);
		$count = 0;
		foreach($arr as $data)
		{
			if($data[$field] == $value)
				$count++;
		}
		return $count;
	}
	public function convertPxToInt($value)
	{
		return intval(substr($value,0,strlen($value)-2));
	}
}