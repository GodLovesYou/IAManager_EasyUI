<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING);

set_time_limit(1000000);

if (!isset($g_Debug))
{
	$g_Debug = true; // 开启全局调试
}
define('G_DEBUG', $g_Debug);

define('ENABLE_VERIFYCODE', FALSE); // 是否开启验证码

define('RETURN_JSON_CODETYPE', 'UNICODE'); // UNICODE | UTF-8 

define('PROJECT_DOMAIN_ROOT', dirname(dirname(__FILE__)));

define('MYSQL_DB_SOURCEFILE_PATH', PROJECT_DOMAIN_ROOT."/dbserver/mysql/DBSource.ini");
 
require_once(PROJECT_DOMAIN_ROOT.'/dbserver/mysql/mysqli5.class.php');
require_once(PROJECT_DOMAIN_ROOT.'/utility/global.fun.php');

if (!isset($g_Log_Level))
{
	$g_Log_Level = CR_Log::LOG_LEVEL_FILE;
}
define('G_LOG_LEVEL', $g_Log_Level);

define('G_TAG', 'IAManager');

if (!isset($g_LogFile_MaxSize))
{
	$g_LogFile_MaxSize = 10485760;
}
define('G_LOGFILE_MAXSIZE', $g_LogFile_MaxSize);

$g_Log = new CR_Log(PROJECT_DOMAIN_ROOT.'/Log', G_TAG, G_DEBUG, G_LOG_LEVEL, G_LOGFILE_MAXSIZE);
$g_Utility = new CR_Utility();
$g_ReturnValue = new CR_ReturnValue();

?>