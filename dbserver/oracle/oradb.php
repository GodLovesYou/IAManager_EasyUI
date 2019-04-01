<?php
/*
---
fn: oradb.php
desc: some initial parameters for oracle db & php(5.0+)
author: 
	- huzw
time: 2013.03.11 add 2017.06.08 mdf
...
*/

// - load oraoci.php ---
include_once("oraoci.php");


// - test connect ---
/*
define('ORACLE_DB_SOURCEFILE_PATH', 'DBSource.ini');

InitOracleDBConfig(); 
// print_r($GLOBALS["g_OraDB_Link"]);

$oradb_link = $GLOBALS["g_OraDB_Link"]["instance"];

$result = $oradb_link->Query(" select count(*) TC from SGZY.T_YJ_DWYJ_LP_GZPJL plan ");

if ($result)
{
	$row = $oradb_link->FetchObject($result);
	
	if ($row)
	{
		echo $row->TC;
	}
	else
	{
		print_r($oradb_link->GetError());
	}
}
else
{
	echo 'execute error';
}
*/

// - conn management ---
function InitOracleDBConfig() {
	// - 获取Oracle数据库配置参数
	FetchOracleDBParams();
	 
	// - 获取Oracle的一个连接
	$oradbconn = GetOracleDBConnect(); 
} 

function GetOracleDBConnect($isNewConn = FALSE) {
	
	$GLOBALS["g_OraDB_Link"] = array(
		"instance" => OracleDBConnect($_SESSION["oraDB"]["Host"], $_SESSION["oraDB"]["Port"], $_SESSION["oraDB"]["User"], $_SESSION["oraDB"]["Password"], $isNewConn, $_SESSION["oraDB"]["SID"], $_SESSION["oraDB"]["Character"], $_SESSION["oraDB"]["SessionMode"]),
		"dbhost" => $_SESSION["oraDB"]["Host"],
		"dbport" => $_SESSION["oraDB"]["Port"],
		"dbname" => $_SESSION["oraDB"]["SID"],
		"dbsid" => $_SESSION["oraDB"]["SID"],
		"dbuser" => $_SESSION["oraDB"]["User"],
		"dbauser" => $_SESSION["oraDB"]["DBAuser"],
		"dbpwd" => $_SESSION["oraDB"]["Password"],
		"dbcharacter" => $_SESSION["oraDB"]["Character"],
		"dbsessionmode" => $_SESSION["oraDB"]["SessionMode"]
	);  
	
	return $GLOBALS["g_OraDB_Link"]["instance"]; 
}

function OracleDBConnect($Host, $Port, $User, $Password, $NewConn, $Database, $Charset, $SessionMode) { 
	return new OCIConn($Host.":".$Port, $User, $Password, $NewConn, $Database, $Charset, $SessionMode);
}

function FetchOracleDBParams() {
	$path = ORACLE_DB_SOURCEFILE_PATH;
	 
	if(file_exists($path)) { 
		$handle = fopen($path, "rb");
		$content = fread($handle, filesize($path));  
		fclose($handle);
		
		if($content != "") {
			$paramsArr = explode("\r\n", $content);
			$found = false;
			if(is_array($paramsArr) && count($paramsArr) > 0) {
				foreach($paramsArr as $row) {
					if($row == "[database]") {
						$found = true;
					} 
					if($found == true) {
						list($key, $val) = split("=", $row);
				  
						switch($key) {
							case "host":
								$_SESSION["oraDB"]["Host"] = $val;
								break;
							case "port":
								$_SESSION["oraDB"]["Port"] = $val;
								break;
							case "SID":
								$_SESSION["oraDB"]["SID"] = $val;
								break;
							case "character":
								$_SESSION["oraDB"]["Character"] = $val;
								break;
							case "user":
								$_SESSION["oraDB"]["User"] = $val;
								break;
							case "password":
								$_SESSION["oraDB"]["Password"] = $val;
								break;
							case "dbauser":
								$_SESSION["oraDB"]["DBAuser"] = $val;
								break;
							default:
								break;
						}
					}
				}
			}
		}
	} 
}	

// - conn management ...
	
?>