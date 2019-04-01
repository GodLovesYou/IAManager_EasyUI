<?php
/*
---
fn: oraoci.php
desc: connect oracle db for php(5.0+)
author: 
	- huzw
time: 2013.03.09
version: v1.0.1
...
*/
class OCIConn {
	var $Connect;
	var $User;
	var $result;
	var $error = array();
	var $realDB;
	 
	/*
	---
	desc: ���캯�������Ӵ����ݿ�
	parems: 
		- $HostPort(string) Orable db ������ַ�˿�
		- $User(string) Orable db �û���
		- $Password(string) Orable db �û�����
		- $NewConn(boolean) �Ƿ���һ���µ����Ӿ��
		- $Charset(string) �����ַ���
		- $SessionMode(int) 
	remark:
		- $HostPort
			=> 127.0.0.1:1521
		- $NewConn 
			=> true, it will call oci_new_connect
			=> false(default), it will call oci_connect 
		- $SessionMode 
			=> OCI_DEFAULT��OCI_SYSOPER �� OCI_SYSDBA
	...
	*/
	function OCIConn($HostPort = "", $User, $Password = "", $NewConn = false, $Database = "", $Charset = "GB2312", $SessionMode = "OCI_DEFAULT") {
		
		$this->Connect = null;
		$this->HostPort = trim(ereg_replace("\/+$", " ", $HostPort));
		$this->User = $User;
		$this->Password = $Password;
		$this->NewConn = $NewConn;
		$this->Database = $Database;
		$this->Charset = $Charset;
		$this->SessionMode = $SessionMode; 
		
		if($User) {
			if($this->HostPort && $this->Database) { 
				$this->realDB = $this->HostPort."/".$this->Database;
			}
			else {
				$this->realDB = $this->Database ? $this->Database : "";
			}
			 
			if(!$this->NewConn) {
			
				// �ȳ��Ե�һ�ַ�ʽ
				
				if($this->HostPort && $this->Database) { 
					list($DB_IP, $DB_Port) = preg_split('/:/', $this->HostPort);
					
					$db_temp = "(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = %s)(PORT = %s)))(CONNECT_DATA = (SERVICE_NAME = %s) ))";
					
					$db = sprintf($db_temp,	$DB_IP, $DB_Port, $this->Database);
					
					$this->Connect = oci_connect($this->User, $this->Password, $db, $this->Charset, $this->SessionMode);
				}
				
				if (empty($this->Connect))
				{
					$this->Connect = $this->realDB ? 
									oci_connect($this->User, $this->Password, $this->realDB, $this->Charset, $this->SessionMode)
									: oci_connect($this->User, $this->Password);
				}
			}
			else {
				$this->Connect = $this->realDB ? 
									oci_new_connect($this->User, $this->Password, $this->realDB, $this->Charset, $this->SessionMode)
									: oci_new_connect($this->User, $this->Password);
				
			} 
			
			if(!$this->Connect) {
				$e = $this->GetError();
				$this->error["Connect"] = $e ? $e : "create connect error";
				return false;
			} 
			
			return $this->Connect;
		} 
		else { 
			$this->error["Connect"] = "exception error";
			return false; 
		} 
	}
	
	// - Query�Ļ�������
	function Query($query = "") {
		try {
			$errorFlag = true;
			$stmt = "";
			// - �Զ��ύ����
			$mode = OCI_COMMIT_ON_SUCCESS; 
			
			if($this->Connect) {  
				if($query != "") {
					$errorFlag = false;
					
					$stmt = oci_parse($this->Connect, $query);
					$flag = oci_execute($stmt, $mode);
					
					if(!$flag) {
						$errorFlag = true;
					}
				} 
				
				if($errorFlag) {
					$e = $this->GetError();  
					$this->error["Query"] = $e ? $e : "query error";
					return false;
				}
			}
			else {
				$this->error["Query"] = "connect unknown";
				return false;
			}   
			
			return $stmt; 
		}
		catch(Exception $e) {
			$this->error["Query"] = "exception error"; 
			return false;
		} 
	} 
	
	// - ��Ҫͨ��������Ĳ�ѯ
	function T_Query($query = "", $transection = false) {
		try {
			// - ͨ��oci_commit()�ύ��ѯ
			$mode = OCI_DEFAULT;
			
			unset($this->result);
			
			if($this->Connect) { 
 				if($query != "") { 
					$stmt = oci_parse($this->Connect, $query); 
					$flag = oci_execute($stmt, $mode);
					
					if($flag != false) {
						$this->result = $stmt; 
						if($transection != "END_TRANSECTION" && $transection !== true) {
							$transection != "BEGIN_TRANSECTION";
						}
					}
					else {
						$transection = "END_TRANSECTION"; 
					}
				}
				else { 
					$transection = "END_TRANSECTION";
				}
				
				if($transection === "END_TRANSECTION" || $transection === true) {
					// - ��ʼ�ύ��ѯ
					$commit = oci_commit($this->Connect); 
					
					if(!$commit) {
						// - crror
						$e = $this->GetError();
						$this->error["T_Query"] = $e ? $e : "commit failure error"; 
						
						// - �ع�
						$rv = oci_rollback($this->Connect);
						
						return false;
					}
				}
			}
			else {
				$this->error["T_Query"] = "connect error"; 
				return false;
			}
			
			return $this->result;
			
		}
		catch(Exception $e) {
			$this->error["T_Query"] = "exception error"; 
			return false;
		}
	}
	 
	/*
	desc: ��������ʽ�������н�� 
	time: 2013.03.09
	returns:
		- succ outputResults lines
		- error false
	remark:
		- $flag 
			=> OCI_FETCHSTATEMENT_BY_ROW 
			=> OCI_FETCHSTATEMENT_BY_COLUMN��Ĭ��ֵ�� 
			=> OCI_NUM 
			=> OCI_ASSOC  
	*/
	function FetchArrayAll($dbStmt, &$outputResults, $skipStartrow = 0, $maxrows = -1, $flag = OCI_FETCHSTATEMENT_BY_COLUMN) {
		try {
			if($this->Connect && $dbStmt) {
				$rows = oci_fetch_all($dbStmt, &$outputResults, $skipStartrow, $maxrows, $flag);
				if($rows != false) {
					return $outputResults; 
				}
				else { 
					$e = $this->GetError();
					$this->error["FetchArrayAll"] = $e ? $e : "fetch array all error";
					return false;
				} 
			}
			else {
				$this->error["FetchArrayAll"] = "connect or params error";
				return false;
			}
		}
		catch(Exception $e) { 
			$this->error["FetchArrayAll"] = "exception error";
			return false;
		}
	}
	 
	/*
	desc: ��������ʽ������һ�н�� 
	time: 2013.03.09
	returns:
		- succ the next one row
		- error false
	remark:
		- $mode 
			=> OCI_BOTH(default) - return an array with both associative and numeric indices (the same as OCI_ASSOC + OCI_NUM). 
			=> OCI_ASSOC - return an associative array (as oci_fetch_assoc() works).  
			=> OCI_NUM - return a numeric array, (as oci_fetch_row() works).  
			=> OCI_RETURN_NULLS - create empty elements for the NULL fields.  
			=> OCI_RETURN_LOBS - return the value of a LOB of the descriptor.  
 
	*/
	function FetchArray($dbStmt, $mode = OCI_BOTH) {
		try {
			if($this->Connect && $dbStmt) {
				$row = oci_fetch_array($dbStmt, $mode);
				if($row != false) {
					return $row; 
				}
				else { 
					$e = $this->GetError();
					$this->error["FetchArray"] = $e ? $e : "fetch array error";
					return false;
				} 
			}
			else {
				$this->error["FetchArray"] = "connect or params error";
				return false;
			}
		}
		catch(Exception $e) { 
			$this->error["FetchArray"] = "exception error";
			return false;
		}
	}
	 
	/*
	---
	desc: Returns the next row from a query as an object
	remark:
		- returns value as like 
			=> $row->OneFieldName?
	...
	*/
 	function FetchObject($dbStmt) {
		try {
		if($this->Connect && $dbStmt) {
				$row = oci_fetch_object($dbStmt);
				if($row != false) {
					return $row; 
				}
				else { 
					$e = $this->GetError();
					$this->error["FetchObject"] = $e ? $e : "fetch object error";
					return false;
				} 
			}
			else {
				$this->error["FetchObject"] = "connect or params error";
				return false;
			}
		}
		catch(Exception $e) {
			$this->error["FetchObject"] = "exception error";
			return false;
		}
	}

		
	// - ��ѯ�������е���Ŀ
	function QueryFields($dbStmt) {
		try {
			if($this->Connect && $dbStmt) {
				$nums = oci_num_fields($dbStmt);
				if($nums != false) {
					return $nums; 
				}
				else { 
					$e = $this->GetError();
					$this->error["QueryFields"] = $e ? $e : "query fields num error";
					return false;
				} 
			}
			else {
				$this->error["QueryFields"] = "connect or params error";
				return false;
			}
		}
		catch(Exception $e) { 
			$this->error["QueryFields"] = "exception error";
			return false;
		}
	}
	
	// - ��ѯ���Ӱ�������
	function QueryRows($dbStmt) {
		try {
			if($this->Connect && $dbStmt) {
				$nums = oci_num_rows($dbStmt);
				if($nums != false) {
					return $nums; 
				}
				else { 
					$e = $this->GetError();
					$this->error["QueryRows"] = $e ? $e : "query rows num error";
					return false;
				} 
			}
			else {
				$this->error["QueryRows"] = "connect or params error";
				return false;
			}
		}
		catch(Exception $e) { 
			$this->error["QueryRows"] = "exception error";
			return false;
		}
	}
	
	// - some tools funcs
	/*
	---
	desc: Returns the next row from a query as an associative array
	remark:
		- Calling this func is identical to calling FetchArray width $mode as OCI_ASSOC + OCI_RETURN_NULLS
	...
	*/
	function FetchAssoc($dbStmt) {
		try { 
			return oci_fetch_assoc($dbStmt);
		}
		catch(Exception $e) {
			return false;
		}
	}
	/*
	---
	desc: Returns the next row from a query as a numeric array
	remark:
		- Calling this func is identical to calling FetchArray width $mode as OCI_NUM + OCI_RETURN_NULLS
	...
	*/
	function FetchRow($dbStmt) {
		try { 
			return oci_fetch_row($dbStmt);
		}
		catch(Exception $e) {
			return false;
		}
	}
	/*
	---
	desc: Fetches the next row into result-buffer
	remark:
		- ��ȡ��һ�У����� SELECT ��䣩���ڲ������������ 
	...
	*/
	function FetchIntoBuffer($dbStmt) {
		try { 
			return oci_fetch($dbStmt);
		}
		catch(Exception $e) {
			return false;
		}
	}
	/*
	---
	desc: ������ȡ�������ֶε�ֵ
	remark:
		-  ������ oci_fetch() ��ȡ�õĵ�ǰ���� field �ֶε����ݡ�oci_result() ���������Ͷ������ַ���ֻ���˳������ͣ�ROWID��LOB �� FILE����
	...
	*/
	function FieldResultFromBuffer($dbStmt, $field) {
		try { 
			return oci_result($dbStmt, $field);
		}
		catch(Exception $e) {
			return false;
		}
	}
	
	// - Binds a PHP array to an Oracle PL/SQL array parameter
	function BindArrayByName($dbStmt, $name, &$var_array, $max_table_length, $max_item_length, $type) {
		try { 
			return oci_bind_array_by_name($dbStmt, $name, &$var_array, $max_table_length, $max_item_length, $type);
		}
		catch(Exception $e) {
			return false;
		}
	}
	// - ��һ�� PHP ������һ�� Oracle λ�ñ�־��
	function BindByName($dbStmt, $ph_name, &$variable, $maxlength, $type) {
		try { 
			return oci_bind_by_name($dbStmt, $ph_name, &$variable, $maxlength, $type);
		}
		catch(Exception $e) {
			return false;
		}
	}
	
	// - ����ֶ��Ƿ�Ϊ NULL
	function FieldIsNull($dbStmt, $field) {
		try { 
			return oci_field_is_null($dbStmt, $field);
		}
		catch(Exception $e) {
			return false;
		}
	}
	function GetFieldName($dbStmt, $field) {
		try { 
			return oci_field_name($dbStmt, $field);
		}
		catch(Exception $e) {
			return false;
		}
	} 
	// - �����ֶξ���
	function GetFieldPrecision($dbStmt, $field) {
		try { 
			return oci_field_precision($dbStmt, $field);
		}
		catch(Exception $e) {
			return false;
		}
	} 
	// - �����ֶη�Χ
	function GetFieldScale($dbStmt, $field) {
		try { 
			return oci_field_scale($dbStmt, $field);
		}
		catch(Exception $e) {
			return false;
		}
	}
	// - �����ֶδ�С
	function GetFieldSize($dbStmt, $field) {
		try { 
			return oci_field_size($dbStmt, $field);
		}
		catch(Exception $e) {
			return false;
		}
	} 
	// - �����ֶε�ԭʼ Oracle ��������
	function GetFieldTypeRaw($dbStmt, $field) {
		try { 
			return oci_field_type_raw($dbStmt, $field);
		}
		catch(Exception $e) {
			return false;
		}
	} 
	// - �����ֶε���������
	function GetFieldType($dbStmt, $field) {
		try { 
			return oci_field_type($dbStmt, $field);
		}
		catch(Exception $e) {
			return false;
		}
	} 
	/* 
	--- 
	desc: ���� OCI ��������	
	returns:
		- succ
			-> SELECT
			-> UPDATE
			-> DELETE
			-> INSERT
			-> CREATE
			-> DROP
			-> ALTER
			-> BEGIN
			-> DECLARE
			-> UNKNOWN 
	...
	*/ 
	function GetStatementType($dbStmt) {
		try { 
			return oci_statement_type($dbStmt);
		}
		catch(Exception $e) {
			return false;
		}
	}
	// - �ͷŹ����������α��������Դ
	function FreeStatement($dbStmt) {
		try { 
			return oci_free_statement($dbStmt);
		}
		catch(Exception $e) {
			return false;
		}
	}
	// - ȡ�����α��ȡ����
	function Cancel($dbStmt) {
		try {
			return oci_cancel($dbStmt); 
		}
		catch(Exception $e) {
			return false;
		}
	}
	
	/*
	---
	desc: �Ͽ�����
	...
	*/
	function Close() {
		try {
			if($this->Connect) {
				$rv = oci_close($this->Connect);
				if(!$rv) {
					$e = $this->GetError();
					$this->error["Close"] = $e ? $e : "close error";
				}
				return $rv;
			}
			else {
				$this->error["Close"] = "connect error";
				return false; 
			}
		}
		catch(Exception $e) {
			$this->error["Close"] = "exception error";
			return false;
		}
	}
	
	// - ��ȡ������Ϣ
	function GetError() {
		$e = oci_error();
		
		if(is_array($e)) { 
			$error["code"] = $e["code"];
			$error["message"] = $e["message"];
		}
		
		return $error ? $error : "";
	}
	
} 

?>