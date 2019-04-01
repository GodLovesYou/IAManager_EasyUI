<?php
/*************************************************************
/*							mysqli5.class.php
/*						----------------
/*		begin		:	Thursday,June 27,2015
/*		copyright	:	(C) 2015 The Crearo Group
/*		email		:	huzw@crearo.com
/*
**************************************************************/

class CR_DBLink
{
	const SUCCESS = "0x0000";
	const FAILED = "0x0001";
	const THREAD = "0x0002";
	const DB_NOT_EXISTS = "0x1001";
	const DB_SELECT_FAILED = "0x1002";
	const QUERY_PARAM_ERROR = "0x2001";		// 参数错误
	
	var $connect;
	var $user;
	var $password;
	var $host;
	var $port;
	var $db_name;
	
	var $character = "UTF8";
	var $result;
	var $error;
	
	// 构造函数
	function __construct($host = "127.0.0.1", $port = "3306", $user, $password, $db_name = "", $character = "UTF8") 
	{
		try
		{
			$this->host = $host;
			$this->port = $port;
			$this->user = $user;
			$this->password = $password;
			$this->db_name = $db_name;
			$this->character = $character;
			
			$this->connect = @mysqli_connect($this->host,
											$this->user,
											$this->password,
											$this->db_name,
											$this->port);
											
			if ($this->connect === FALSE)
			{
				$this->error = array(
					"code" => self::FAILED
				);
				return self::FAILED;
			}
			else
			{
				if (!empty($this->db_name))
				{
					// 选择数据库
					$rv = mysqli_select_db($this->connect, $this->db_name);
					if (!$rv)
					{
						$this->error = array(
							"code" => self::DB_SELECT_FAILED
						);
						// 选择失败，返回
						mysqli_close($this->connect);
						$this->connect = false;
						return self::DB_SELECT_FAILED;
					}
					else
					{
						// 设置字符集
						mysqli_query($this->connect, "SET NAMES ".$this->character); 
					}
				}
				
				return self::SUCCESS;	
			}
		}
		catch (Exception $e)
		{
			$this->error = array(
				"code" => self::THREAD
			);
			return self::THREAD;
		}
	}
	
	// 重新建立连接
	function ReLink()
	{
		try 
		{
			$this->Close();
			
			$this->connect = @mysqli_connect($this->host, 
											$this->user, 
											$this->password, 
											$this->db_name, 
											$this->port);
			
			if (!$this->connect || mysqli_connect_errno())
			{
				$this->error = array(
					"code" => self::FAILED
				);
				return false;
			}
			return $this->connect;
		}
		catch (Exception $e) {
			$this->error = array(
				"code" => self::THREAD
			);
			return false;
		}
	}
	
	// 选择打开数据库
	function SelectOpenDB($db_name = "")
	{
		try 
		{
			if (empty($db_name))
			{
				$db_name = $this->db_name;
			}
			
			$rv = mysqli_select_db($this->connect, $db_name);
			if (!$rv)
			{
				$this->error = array(
					"code" => self::DB_SELECT_FAILED
				);
				// 选择失败，返回
				mysqli_close($this->connect);
				$this->connect = false;
				return false;
			}
			else
			{
				// 设置字符集
				mysqli_query($this->connect, "SET NAMES ".$this->character); 
			}
			$this->db_name = $db_name;
			return $this->connect;
		}
		catch (Exception $e) {
			$this->error = array(
				"code" => self::THREAD
			);
			return false;
		}
	}
	
	// 单语句查询
	function Query($query = "")
	{
		try 
		{
			if (empty($query))
			{
				$this->error = array(
					"code" => self::QUERY_PARAM_ERROR
				);
				return false;
			}
			else
			{
				$this->result = mysqli_query($this->connect, $query);
				if (!$this->result)
				{
					$this->error = array(
						"code" => self::FAILED
					);
					return false;
				}
				else
				{
					return $this->result;
				}
			}
		}
		catch (Exception $e) {
			$this->error = array(
				"code" => self::THREAD
			);
			return false;
		}
	}
	
	// 多语句查询
	function MultiQuery($query = "", &$result)
	{
		try 
		{
			if (!isset($result))
			{
				$result = array();
			}
			
			if ($this->connect)
			{
				if (strlen(trim($query)) > 0)
				{
					$result = array();
					
					$rv = mysqli_multi_query($this->connect, $query);
					if ($rv)
					{
						// 把结果集记录到$result
						do 
						{
							if (($item = mysqli_store_result($this->connect)) != NULL)
							{
								$result[] = $item;	
							} 
						}
						while(mysqli_next_result($this->connect));
						
						return $result;
					}
				}
			}
			return false;
		}	
		catch (Exception $e) 
		{
			$this->error = array(
				"code" => self::THREAD
			);
			return false;
		}
	} 	
	// 释放查询结果
	function FreeResult()
	{
		try 
		{
			if ($this->connect)
			{
				do 
				{
					if (($result = mysqli_store_result($this->connect)) != NULL)
					{
						mysqli_free_result($result);
					}
				}
				while(mysqli_next_result($this->connect));
				
				$this->result = null;	
			}
			
			return true;
		}
		catch(Exception $e) 
		{
			$this->error = array(
				"code" => self::THREAD
			);
			return false;
		}
	}
	
	// 通过事务方式处理语句
	function Trans_Query($querys = array())
	{
		try 
		{
			if (count($querys) > 0)
			{
				$operator = true;
			
				// 启动事务，关闭自动提交
				mysqli_autocommit($this->connect, false);
				
				foreach($querys as $query)
				{
					$result = mysqli_query($this->connect, $query);
					if (!$result || mysqli_errno($this->connect))
					{
						$operator = false;
						break;
					}
				}
				
				if ($operator == true)
				{
					mysqli_commit($this->connect);
				}
				else
				{
					mysqli_rollback($this->connect);
				}
				mysqli_autocommit($this->connect, true);
				
				return $operator;
			}
			$this->error = array(
				"code" => self::QUERY_PARAM_ERROR
			);
			return false;
		}
		catch(Exception $e) 
		{
			mysqli_autocommit($this->connect, true);	
			$this->error = array(
				"code" => self::THREAD
			);
			return false;
		}
	}
	
	// 以数组形式返回查询结果
	function FetchArray($result)
	{
		try 
		{
			if ($result)
			{
				if ($this->connect)
				{
					return mysqli_fetch_array($result, MYSQLI_BOTH);
				}
			}
			
			return false;
		}
		catch(Exception $e) 
		{
			$this->error = array(
				"code" => self::THREAD
			);
			return false;
		}
	}
	
	// 以对象形式返回查询结果
	function FetchObject($result)
	{
		try 
		{
			if ($result)
			{
				if ($this->connect)
				{
					return mysqli_fetch_object($result);
				}
			}
			
			return false;
		}
		catch(Exception $e) 
		{
			$this->error = array(
				"code" => self::THREAD
			);
			return false;
		}
	}
	
	// 查询结果行数
	function QueryRows($result = 0)
	{
		if (!$result)
		{
			$result = $this->result;
		}
		if ($result)
		{
			return mysqli_num_rows($result);
		}
		return false;
	}
	
	function Num_Fields($result = 0)
	{
		if (!$result)
		{
			$result = $this->result;
		}
		if ($result)
		{
			return mysqli_num_fields($result);
		}
		return false;
	}
	
	// 返回操作结果影响的行数
	function QueryAffectRows()
	{
		if ($this->connect)
		{
			return mysqli_affected_rows($this->connect);
		}
		return 0;
	}
	
	// 关闭连接
	function Close()
	{
		if ($this->connect)
		{
			return mysqli_close($this->connect);
		}
		return false;
	}
	
	// 获取最新插入的行号
	function GetLastInsertID()
	{
		if ($this->connect)
		{
			return mysqli_insert_id($this->connect);
		}
		return 0;
	}
	
	// 查询表数据总条数
	function QueryRecordCount($afterFromSqlStr)
	{
		try 
		{
			if ($this->connect)
			{
				$row = $this->FetchObject($this->Query("select count(*) as RecordCount from ".$afterFromSqlStr));
				if ($row && isset($row->RecordCount) && $row->RecordCount > 0)
				{
					return intval($row->RecordCount);
				}
			}
			return 0;
		}
		catch(Exception $e) 
		{
			$this->error = array(
				"code" => self::THREAD
			);
			return 0;
		}
	}
	// 提示错误
	function GetError($returnMode = "array")
	{
		try 
		{ 
			$error = $errno = "";
			if ($this->connect)
			{
				$error = mysqli_error($this->connect);
				$errno = mysqli_errno($this->connect);
			}
			switch ($returnMode)
			{
				case "string":
					return $errno."[".$error."]";
					break;
				default:
					return array(
						"code" => self::SUCCESS,
						"error" => $error,
						"errno" => $errno
					);
					break;
			}
		}
		catch(Exception $e) 
		{
			$this->error = array(
				"code" => self::THREAD
			);
			switch ($returnMode)
			{
				case "string":
					return "";
					break;
				default:
					return array(
						"code" => self::FAILED,
						"error" => "",
						"errno" => ""
					);
					break;
			}
		}
	}
}

?>