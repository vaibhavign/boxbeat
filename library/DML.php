<?php

#==========================DML: Data Manupulation Language==========================
#	PURPOSE:	THIS CLASS WILL ACT AS AN ACTIVE RECORD SET, DATA CAN BE MANIPULATED
#			EASILY WITH THE HELP OF THIS CLASS.	
#	AUTHOR:		ANKIT VISHWAKARMA
#	CONTACT:	ankitvishwakarma@sify.com
#       Date Created:   22-Apr-2011
#       Date Modified   01-May-2011
#====================================================================================				

class DML {

	protected $db;
	private $str = '';
	private $lastQuery = '';
	public $joinImplemented = false;
	public $joinTypeImplemented = false;
	private static $instance = NULL;

	function __construct($str = '') {
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}

	function __destruct() {
		return $this->db = NULL;
	}

	public function select($fields='*') {
		$statement = "SELECT $fields ";
		$this->str = $statement;

		return $this;
	}

	public function selectMax($field, $as = NULL) {
		$statement = "SELECT MAX($field)" . ($as != NULL ? " as $as" : "");
		$this->str = $statement;
		return $this;
	}

	public function selectMin($field) {
		$statement = "SELECT MIN($field)";
		$this->str = $statement;
		return $this;
	}

	public function selectAvg($field) {
		$statement = "SELECT AVG($field)";
		$this->str = $statement;
		return $this;
	}

	public function selectSum($field) {
		$statement = "SELECT SUM($field)";
		$this->str = $statement;
		return $this;
	}

	public function from($table) {
		$this->str .= ' FROM ' . $table . ' ';
		return $this;
	}

	public function between($field, $from, $to, $afterwhere=true) {

		$this->str.= ( strpos($this->str, 'WHERE') ? ' AND ' : ' WHERE ') . "$field BETWEEN $from and $to";
		return $this;
	}

	public function where($where, $myvalue = NULL, $orwhere=NULL, $orvalue=NULL) {

		if (is_array($where)) {

			foreach ($where as $key => $value) {
				if (preg_match("/(=)/", $key)) {
					preg_replace("/(=)/", ' ', $key);
				} elseif (preg_match("/(>=)/", $key)) {
					preg_replace("/(>=)/", ' ', $key);
				} elseif (preg_match("/(<=)/", $key)) {
					preg_replace("/(<=)/", ' ', $key);
				} elseif (preg_match("/(!=)/", $key)) {
					preg_replace("/(!=)/", ' ', $key);
				} elseif (preg_match("/(<>)/", $key)) {
					preg_replace("/(<>)/", ' ', $key);
				} else {
					$key = $key . '=';
				}
				$condition .= $key . "'" . $value . "'" . " &";
			}

			$statement .= ( strpos($this->str, 'WHERE') ? ' AND ' : ' WHERE ') . str_replace('&', ' AND ', substr($condition, 0, - 1));
		} else {

			if ($myvalue != NULL) {
				if ($orwhere != NULL) {
					$statement = (strpos($this->str, 'WHERE') ? ' AND' : ' WHERE ') . "($where='" . $myvalue . "' OR $orwhere='" . $orvalue . "')";
				} else {
					$statement = (strpos($this->str, 'WHERE') ? ' AND ' : ' WHERE ') . "$where='" . $myvalue . "'";
				}
			}
		}
		$this->str .= $statement;






		return $this;
	}

	public function whereAfter($where, $myvalue = NULL, $keepwhere=true) {

		if (is_array($where)) {
			foreach ($where as $key => $value) {
				if (preg_match("/(=)/", $key)) {
					preg_replace("/(=)/", ' ', $key);
				} elseif (preg_match("/(>=)/", $key)) {
					preg_replace("/(>=)/", ' ', $key);
				} elseif (preg_match("/(<=)/", $key)) {
					preg_replace("/(<=)/", ' ', $key);
				} elseif (preg_match("/(!=)/", $key)) {
					preg_replace("/(!=)/", ' ', $key);
				} elseif (preg_match("/(<>)/", $key)) {
					preg_replace("/(<>)/", ' ', $key);
				} else {
					$key = $key . '=';
				}
				$condition .= $key . "'" . $value . "'" . " &";
			}

			$statement .= ( $keepwhere == true) ? ' WHERE ' : ' AND ' . str_replace('&', ' AND ', substr($condition, 0, - 1));
		} else {
			if ($myvalue != NULL) {
				if (!$this->joinImplemented)
					$statement .= " WHERE $where='" . $myvalue . "'";
				else {
					$statement .= " AND $where='" . $myvalue . "'";
				}
			} else {
				if (!$this->joinImplemented)
					$statement .= " WHERE $where";

				else {
					if (!$this->joinTypeImplemented)
						$statement .= " AND $where";
					else
						$statement .= " WHERE $where";
				}
			}
		}

		$this->str .= $statement;




		return $this;
	}

	public function whereIn($field, $values) {
		//print_r($values);
		for ($i = 0; $i < count($values); $i++) {
			$myvalues.= "'" . $values[$i] . "'" . ',';
		}
		$myvalues = substr($myvalues, 0, -1);

		$this->str .= ( strpos($this->str, 'WHERE') ? 'AND ' : " WHERE ") . $field . " IN (" . $myvalues . ")";

		return $this;
	}

	public function orWhereIn($field, $values) {
		for ($i = 0; $i <= count($values); $i++) {
			$myvalues = $values[$i] . ',';
		}
		$myvalues = substr($myvalues, 0, - 1);
		$this->str .= " OR WHERE $field IN (" . $myvalues . ")";
		return $this;
	}

	public function whereNotIn($field, $values, $beforeWhere=false) {

		for ($i = 0; $i < count($values); $i++) {
			$myvalues.= "'" . $values[$i] . "'" . ',';
		}
		$myvalues = substr($myvalues, 0, -1);

		$this->str .= ( strpos($this->str, 'WHERE') ? 'AND ' : " WHERE ") . $field . " NOT IN (" . $myvalues . ")";


		// return $this;
//        if (is_array($values)) {
//            for ($i = 0; $i <= count($values); $i++) {
//                $myvalues = $values[$i] . ',';
//            }
//            $myvalues = substr($myvalues, 0, - 1);
//        } else {
//            $myvalues = "'" . $values . "'";
//        }
//         $this->str .= ( $beforeWhere) ? " WHERE " : " AND " . $field . " NOT IN (" . $myvalues . ")";

		return $this;
	}

	public function whereNotIn1($field, $values, $beforeWhere=false) {

		if (is_array($values)) {
			for ($i = 0; $i <= count($values); $i++) {
				$myvalues = $values[$i] . ',';
			}
			$myvalues = substr($myvalues, 0, - 1);
		} else {
			$myvalues = $values;
		}
		$this->str .= ( $beforeWhere) ? " WHERE " : " AND " . $field . " NOT IN (" . $myvalues . ")";

		return $this;
	}

	public function like($title, $match, $type = NULL) {
		if ($type == 'before') {
			$statement = (strpos($this->str, 'WHERE') ? ' AND ' : ' WHERE ') . "$title LIKE '%$match'";
		} else
		if ($type == 'after') {
			$statement = (strpos($this->str, 'WHERE') ? ' AND ' : ' WHERE ') . "$title LIKE '$match%'";
		} elseif ($type == 'both' || $type == NULL) {
			$statement = (strpos($this->str, 'WHERE') ? ' AND ' : ' WHERE ') . "$title LIKE '%$match%'";
		}

		$this->str .= $statement;



		return $this;
	}

	public function likeOrLike($arr, $join=' AND ', $type=NULL) {
		if (is_array($arr)) {
			//print_r($arr);
			$queryString = $join . ' ( ';
			foreach ($arr as $key => $value) {
				$title = $key;
				$match = $value;
				switch ($type) {
					case 'before':
						$queryString.="$title  LIKE '%$match' OR ";
						break;
					case 'after':
						$queryString.="$title  LIKE '$match%' OR ";
						break;
					case 'both':

					case NULL:
						$queryString.="$title  LIKE '%$match%' OR ";
						break;
				}
			}

			$queryString = substr($queryString, 0, strrpos($queryString, 'OR'));
			$queryString.=')';
			$this->str.= $queryString;


			return $this;
		}
	}

	public function orLike($title, $match, $type='both') {
		if ($type == 'before') {
			$statement = " OR $title LIKE '%$match'";
		} else
		if ($type == 'after') {
			$statement = " OR $title LIKE '$match%'";
		} elseif ($type == 'both' || $type == NULL) {
			$statement = " OR $title LIKE '%$match%'";
		}
		$this->str .= $statement;

		return $this;
	}

	public function orNotLike($title, $match) {
		$this->str .= " OR $title NOT LIKE '%$match'";
		return $this;
	}

	public function groupBy($title) {
		$this->str .= "  GROUP BY $title";
		return $this;
	}

	public function orderBy($title, $order='ASC') {


		$this->str .= "  ORDER BY $title $order";
		if (substr_count($this->str, "ORDER BY") > 1) {
			$myarr = explode('ORDER BY', $this->str);
			$size = sizeof($myarr);
			$statement = $myarr[0] . 'ORDER BY';
			$order = '';
			for ($i = 1; $i < $size; $i++) {
				$order .= $myarr[$i] . ',';
			}
			$this->str = '';
			$this->str.= $statement . substr($order, 0, - 1);
			return $this;
		}

		return $this;
		//die;
	}

	public function orWhere($where, $myvalue=NULL) {
		if (is_array($where)) {
			foreach ($where as $key => $value) {
				if (preg_match("/(=)/", $key)) {
					preg_replace("/(=)/", ' ', $key);
				} elseif (preg_match("/(>=)/", $key)) {
					preg_replace("/(>=)/", ' ', $key);
				} elseif (preg_match("/(<=)/", $key)) {
					preg_replace("/(<=)/", ' ', $key);
				} elseif (preg_match("/(!=)/", $key)) {
					preg_replace("/(!=)/", ' ', $key);
				} elseif (preg_match("/(<>)/", $key)) {
					preg_replace("/(<>)/", ' ', $key);
				} else {
					$key = $key . '=';
				}
				$condition .= " & " . $key . "'" . $value . "'";
			}
			$statement = str_replace('&', ' OR ', $condition);
		}
		$this->str.= $statement;

		return $this;
	}

	public function limit($limit, $offset = NULL) {
		$statement = " LIMIT $limit" . ($offset != NULL ? ",$offset" : "");
		$this->str .= $statement;
		return $this;
	}

	public function getWhere($table, $where, $order=NULL, $limit=NULL) {
		$statement = "SELECT *";
		$statement .= " FROM $table";
		foreach ($where as $key => $value) {
			if (preg_match("/(=)/", $key)) {
				preg_replace("/(=)/", ' ', $key);
			} elseif (preg_match("/(>=)/", $key)) {
				preg_replace("/(>=)/", ' ', $key);
			} elseif (preg_match("/(<=)/", $key)) {
				preg_replace("/(<=)/", ' ', $key);
			} elseif (preg_match("/(!=)/", $key)) {
				preg_replace("/(!=)/", ' ', $key);
			} elseif (preg_match("/(<>)/", $key)) {
				preg_replace("/(<>)/", ' ', $key);
			} else {
				$key = $key . '=';
			}
			$condition .= $key . "'" . $value . "'" . " &";
		}
		$statement .= ' WHERE ' . str_replace('&', ' AND ', substr($condition, 0, - 1));
		$statement .= ( $order != NULL) ? 'ORDER BY ' . ((is_string($order) ? $order . ' ASC' : array_keys($order) . ' ' . array_values($order))) : '';

		$this->lastQuery = $statement;
		$this->str = $statement;
		return $this;
	}

	public function get() {

		return $this;
	}

	public function rowArray() {
		try {
			$this->lastQuery = $this->str;
			return $this->str = $this->db->fetchRow($this->str);
		} catch (Exception $e) {
			echo 'Error:' . $e->getMessage() . 'Generated on File' . __FILE__ . ' in line number ' . __LINE__;
		}
	}

	public function resultArray() {
		try {
			$this->lastQuery = $this->str;
			return $this->str = $this->db->fetchAll($this->str);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function getArray($table = '') {
		if ($table != '') {
			$statement = "SELECT *";
			$statement .= " FROM $table";
			$this->lastQuery = $statement;
			return $this->str = $this->db->fetchAll($statement);
		} else {
			try {
				$this->lastQuery = $this->str;
				return $this->str = $this->db->fetchAll($this->str);
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}

	public function getRow($table = '', $where=NULL) {
		if ($table != '') {
			$statement = "SELECT *";
			$statement .= " FROM $table";
			$this->str = $statement;
			$condition = $this->where($where);
			$this->lastQuery = $this->str;
			return $this->db->fetchRow($this->str);
		} else {
			try {
				$this->lastQuery = $this->str;
				return $this->str = $this->db->fetchRow($this->str);
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}

	public function join($table, $condition, $jointype=NULL) {
		/**
		 *
		 * @jointype : 'left' or 'right'
		 */
		$this->joinImplemented = true;
		if ($jointype == 'left') {
			$type = " LEFT JOIN ";
			$this->joinTypeImplemented = true;
		}
		if ($jointype == 'right') {
			$type = " RIGHT JOIN ";
			$this->joinTypeImplemented = true;
		}
		if ($jointype == 'inner') {
			$type = " INNER JOIN ";
			$this->joinTypeImplemented = true;
		}
		$comma = ' , ';
		if ($jointype != NULL) {
			$statement.=" ON ";
			$comma = '';
		}
		$statement = $type . $comma . $table;


		$this->str.=$statement . (($jointype == NULL) ? " WHERE " : " ON ") . $condition;
		$this->lastQuery = $this->str;


		return $this;
	}

	public function countResult() {
		$statement = $this->lastQuery;
		$count = $this->db->fetchAll($statement);
		return count($count);
	}

	public function insertRecord($table, $records) {
		try {
			$this->db->insert($table, $records);
		} catch (Exception $e) {

			echo $e->getMessage();
		}
		return $this->db->lastInsertId();
	}

	public function deleteRecord($table, $where=NULL) {

		foreach ($where as $key => $value) {
			if (preg_match("/(=)/", $key)) {
				preg_replace("/(=)/", ' ', $key);
			} elseif (preg_match("/(>=)/", $key)) {
				preg_replace("/(>=)/", ' ', $key);
			} elseif (preg_match("/(<=)/", $key)) {
				preg_replace("/(<=)/", ' ', $key);
			} elseif (preg_match("/(!=)/", $key)) {
				preg_replace("/(!=)/", ' ', $key);
			} elseif (preg_match("/(<>)/", $key)) {
				preg_replace("/(<>)/", ' ', $key);
			} else {
				$key = $key . '=';
			}
			$condition.= $key . "'" . $value . "'" . " &";
		}
		$condition = str_replace('&', ' AND ', substr($condition, 0, - 1));


		try {
			$this->db->delete($table, $condition);
		} catch (Exception $e) {

			echo $e->getMessage();
		}
	}

	public function updateRecord($table, $setdata, $where) {

		/**
		 * 
		 * This function will be used to Update any particular record from the Table
		 * @table
		 * 		The name of the table to be updated; must be a String.  
		 * @setdata
		 * 		The collection of data to be updated ; must be an associative array [eg: array('key'=>'value')] 
		 * @where 
		 * 		The collection of conditions to be passed in where clause; must be an array [eg: array('key'=>'value')]
		 */
		$statement = "UPDATE $table SET ";
		foreach ($setdata as $key => $value) {
			$statement.="$key='$value',";
		}

		$statement = substr($statement, 0, -1);
		foreach ($where as $key => $value) {
			if (preg_match("/(=)/", $key)) {
				preg_replace("/(=)/", ' ', $key);
			} elseif (preg_match("/(>=)/", $key)) {
				preg_replace("/(>=)/", ' ', $key);
			} elseif (preg_match("/(<=)/", $key)) {
				preg_replace("/(<=)/", ' ', $key);
			} elseif (preg_match("/(!=)/", $key)) {
				preg_replace("/(!=)/", ' ', $key);
			} elseif (preg_match("/(<>)/", $key)) {
				preg_replace("/(<>)/", ' ', $key);
			} else {
				$key = $key . '=';
			}
			$condition .= $key . "'" . $value . "'" . " &";
		}
		$condition = str_replace('&', ' AND ', substr($condition, 0, - 1));
		$statement.=" WHERE $condition";
		$this->lastQuery = $statement;


		try {
			$this->db->query($statement);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function lastQuery() {
		if (SHOW_LAST_QUERY)
			return $this->lastQuery;
	}

	public function simpleQuery($queryString) {
		try {
			return $this->db->fetchAll($queryString);;
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$this->lastQuery = $queryString;
	}

}