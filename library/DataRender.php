<?php
class DataRender
{
	public function getRecordCount($table,$condition="1=1")
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from(array($table),array('total'=>'count(*)'))
							  ->where($condition);
		$count = Utility::getArray($db->query($select)->fetchAll());
		return $count[0];
	}
	public function getFieldsVal($table,$field,$condition="1=1")
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from(array($table),array($field))
							  ->where($condition);
		$val = Utility::getArray($db->query($select)->fetchAll());
		return $val[0];
	} 
	public function getRecord($table,$fields,$condition="1=1")
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from(array($table),array($fields))
							  ->where($condition);
		$row = $db->query($select)->fetchAll();
		return $row[0];
	}
	public function getRecordsArray($table,$fields,$condition="1=1")
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from(array($table),array($fields))
							   ->where($condition);
		$rows = $db->query($select)->fetchAll();
		return $rows;
	}
	public function getQuery($sql)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$rows = $db->query($sql)->fetchAll();
		return $rows;
	}
}