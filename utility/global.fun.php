<?php

class CR_Log
{
	var $logPath = "log"; // 默认日志存放目录
	var $debug = true; // 是否开启调试日志
	var $logFileName = "IAManager"; // 日志文件名
	var $logFileMaxSize = 10485760; // 单文件最高大小（单位字节）
	var $logFileReserveDays = 10; // 日志文件最大保存天数
	
	var $logLevel = 0; // 日志级别，以下取值
	const LOG_LEVEL_FILE = 0; // 仅文件记录
	const LOG_LEVEL_DB = 1; // 仅数据库记录
	const LOG_LEVEL_BOTH = 2; // 文件以及数据库记录
	
	function CR_Log($logPath = "", $logFileName = "IAManager", $debug = true, $logLevel = null, $logFileMaxSize = 10485760)
	{
		try
		{
			$this->logPath = $logPath;
			$this->logFileName = $logFileName;
			$this->logFileMaxSize = $logFileMaxSize;
			
			if (!isset($logLevel))
			{
				$this->logLevel = $logLevel;
			}
			else
			{
				$this->logLevel = CR_Log::LOG_LEVEL_FILE;
			}
			
			if (isset($logPath))
			{
				$this->logPath = preg_replace("/((\/*)|(\\*))$/", "", $this->logPath);
			}
			
			$this->RemoveExpiredLogFile();
			
			if (is_dir($this->logPath))
			{
				return true;
			}
			else
			{
				// 创建目录
				if (mkdir($this->logPath, 0777))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		     
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	/*
	 * @desc: 移除过期的日志文件
	 * @time: 2015.11.30
	 * @author: huzw
	 */ 
	private function RemoveExpiredLogFile()
	{
	    try {
	        
	        if (is_dir($this->logPath))
	        {
	            $logFiles = array();
	            $expiredFiles = array();
	            
	            $rsdays = intval($this->logFileReserveDays);
	            if ($rsdays < 1)
	            {
	                $rsdays = 1;
	            }
	            
	            $curTime = CR_Utility::GetDateTime('Y-m-d H-i-s');
	            
	            $expiredTime = CR_Utility::GetDateTime('Y-m-d H-i-s', (time() - $rsdays * 24 * 60 * 60));
	            
	            if (($fh = opendir($this->logPath)) !== FALSE)
	            {
	                while (($logfile = readdir($fh)) !== FALSE)
	                {
	                    if (!in_array($logfile, array('.', '..')))
	                    {
	                        array_push($logFiles, $logfile);
	                    
	                        $lf_name = str_replace(".".pathinfo($logfile, PATHINFO_EXTENSION), '', basename($logfile));
	                    
	                        $lf_arr = preg_split('/\_/', $lf_name);
	                    
	                        array_splice($lf_arr, 0, 1);
	                    
	                        if (count($lf_arr) == 7 && implode("-", $lf_arr) < $expiredTime)
	                        {
	                            array_push($expiredFiles, $logfile);
	                        }
	                    }
	                }
	            }
	            
	            if (!empty($expiredFiles))
	            {
	                $this->Writer($this->logLevel, __FILE__, __LINE__, __FUNCTION__, "Need removing expiredFiles ->　".print_r($expiredFiles, true)." ");
	               
	                foreach ($expiredFiles as $logfile)
	                {
	                    $lf_path = "{$this->logPath}/$logfile";
	                    
	                    if (file_exists($lf_path))
	                    {
	                       unlink($lf_path);
	                    }
	                }
	            }
	        }
	        
	        return true;
	    } catch (Exception $e) {
	        return false;
	    }
	}
	
	function Writer($type_id = "UNKNOWN", $_F_, $_L_, $_Func_, $content = "", $datetime = null, $logLevel = null)
	{
		try
		{
			if (!$this->debug) return false;
			
			if (!isset($datetime))
			{
				$datetime = CR_Utility::GetDateTime();
			}
			if (!isset($logLevel))
			{
				$logLevel = $this->logLevel;
			}
			
			switch ($logLevel)
			{
				case CR_Log::LOG_LEVEL_FILE:
					$this->WriterToFile($type_id, $_F_, $_L_, $_Func_, $content, $datetime);
					break;
				case CR_Log::LOG_LEVEL_DB:
					$this->WriterToDB($type_id, $_F_, $_L_, $_Func_, $content, $datetime);
					break;
				case CR_Log::LOG_LEVEL_BOTH:
					$this->WriterToFile($type_id, $_F_, $_L_, $_Func_, $content, $datetime);
					$this->WriterToDB($type_id, $_F_, $_L_, $_Func_, $content, $datetime);
					break;
			}
			return true;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	
	public function WriterToFile($type_id = "UNKNOWN", $_F_, $_L_, $_Func_, $content = "", $datetime = null)
	{
		try
		{
			$logFilePath = "";
			if (is_dir($this->logPath))
			{
				$logFilePath = $this->logPath.'/'.$this->logFileName.".log";
			}
			else
			{
				$logFilePath = $this->logFileName.".log";
			}
			
			$rv = $this->CreateLogFile($logFilePath); 
			
			if ($rv == true)
			{
				$clientAddr = CR_Utility::GetClientAddress();
				
				$fp = fopen($logFilePath, "a+");
				if (!$fp)
				{
					return false;
				}
				else
				{
					$fContent = "[$type_id][$clientAddr][$datetime][$_F_][$_L_][$_Func_] $content \r\n";
					fwrite($fp, $fContent);
					fclose($fp);
				}
				$fp = null;
			}
			else
			{
				return false;
			}
			
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	public function WriterToDB($type_id = "UNKNOWN", $_F_, $_L_, $_Func_, $content = "", $datetime = null)
	{
        
	}
	
	public function CreateLogFile($logFilePath = "")
	{
		try
		{
			if (file_exists($logFilePath))
			{
				if (filesize($logFilePath) > $this->logFileMaxSize)
				{
					$bak_logFilePath = $this->logFileName.'_'.CR_Utility::GetDateTime('Y_m_d_H_i_s').'_'.floor(microtime()*1000).'.log';
					if (!empty($bak_logFilePath))
					{
						$bak_logFilePath = dirname($logFilePath).'/'.$bak_logFilePath;
					}
					rename($logFilePath, $bak_logFilePath);
					
					$fp = fopen($bak_logFilePath, 'a+');
					if (!$fp)
					{
						return false;
					}
					else
					{
						fwrite($fp, "========================================================\r\n");
						fwrite($fp, "\tRunning Information Continue-Time:".CR_Utility::GetDateTime()."\r\n");
						fwrite($fp, "========================================================\r\n");
						
						fclose($fp);
					} 
				}
			}
			else
			{
				$fp = fopen($logFilePath, 'w');
				if (!$fp)
				{
					return false;
				}
				else
				{
					fwrite($fp, "========================================================\r\n");
					fwrite($fp, "\tCrearo ".strtoupper($this->logFileName)." log file \r\n");
					fwrite($fp, "\tSubject : Running Information \r\n");
					fwrite($fp, "\tContinued-Time:".CR_Utility::GetDateTime()."\r\n");
					fwrite($fp, "========================================================\r\n");
				}
			}
		
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
}

// 公共类库
class CR_Utility
{
	function CR_Utility($DBSourceIniPath = null)
	{
		try {
			self::GetDBConfigParams($DBSourceIniPath);
			
			// 建立一个全局性的数据库连接对象
			$GLOBALS["g_CR_Utility"] = array(
				"g_CR_DBLink" => $this->GetCR_DBLink($_SESSION["DB"]["Host"], 
												$_SESSION["DB"]["Port"],
												$_SESSION["DB"]["User"],
												$_SESSION["DB"]["Password"],
												$_SESSION["DB"]["Name"],
												$_SESSION["DB"]["Character"]),
				"g_DB_Host" => $_SESSION["DB"]["Host"],
				"g_DB_Port" => $_SESSION["DB"]["Port"],
				"g_DB_User" => $_SESSION["DB"]["User"],
				"g_DB_Password" => $_SESSION["DB"]["Password"],
				"g_DB_Name" => $_SESSION["DB"]["Name"],
				"g_DB_Character" => $_SESSION["DB"]["Character"]
			);		
		
		} catch (Exception $e) {
		}
	}
	
	public function GetCR_DBLink($host = '127.0.0.1', $port = '3306', $user = '', $password = '', $db_name = '', $character = 'UTF8')
	{
		return new CR_DBLink($host, $port, $user, $password, $db_name, $character);
	}
	
	// 测试连接数据库参数是否正确
	public static function TestCR_DBLink($host, $port, $user, $password, $db_name, $character)
	{
		try
  		{
			$rv = true;
			
			$db_link = CR_Utility::GetCR_DBLink($host, $port, $user, $password, $db_name, $character);
			
			if (!isset($db_link) || !$db_link->connect)
			{
				$rv = false;
			}
			else
			{
				$db_link->Close();
			}
		
			return $rv;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public static function GetDBConfigParams($DBSourceIniPath = null)
	{
		try
		{
			$dbparams = self::ReadDBSourceIniFile($DBSourceIniPath);
			if ($dbparams)
			{
				foreach ($dbparams as $key=>$value)
				{
					if (isset($key))
					{
						switch ($key)
						{
							case 'host':
								$_SESSION["DB"]["Host"] = $value;
								break;
							case 'port':
								$_SESSION["DB"]["Port"] = $value;
								break;
							case 'name':
								$_SESSION["DB"]["Name"] = $value;
								break;
							case 'character':
								$_SESSION["DB"]["Character"] = $value;
								break;
							case 'user':
								$_SESSION["DB"]["User"] = $value;
								break;
							case 'password':
								$_SESSION["DB"]["Password"] = $value;
								break;
						}
					} 
				}
				
				return true;
			}
			
			return false;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public static function WriteDBSourceIniFile($DBSourceIniPath = null, $host = '127.0.0.1', $port = '3306', $user = '', $password = '', $db_name = '', $character = 'UTF8')
	{
		try
		{
			if (!isset($DBSourceIniPath))
			{
				$DBSourceIniPath = MYSQL_DB_SOURCEFILE_PATH;
			}
		
			$contents = "[database]\r\nhost=$host\r\nport=$port\r\nname=$db_name\r\ncharacter=$character\r\nuser=$user\r\npassword=$password\r\n";
			$fp = fopen($DBSourceIniPath, "w+");
			fwrite($fp, $contents);
			fclose($fp);
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}	
	
	public static function ReadDBSourceIniFile($DBSourceIniPath = null)
	{
		try
		{
			if (!isset($DBSourceIniPath))
			{
				$DBSourceIniPath = MYSQL_DB_SOURCEFILE_PATH;
			}
			$dbparams = array(
				"host" => "",
				"port" => "",
				"name" => "",
				"user" => "",
				"password" => "",
				"character" => ""
			);
			
			// 读配置文件
			if (file_exists($DBSourceIniPath))
			{ 
				$contents = file_get_contents($DBSourceIniPath);
				
				$ini_nodes = explode("\r\n", $contents);
				
				foreach ($ini_nodes as $item)
				{
					list($key, $value) = preg_split("/\\=/", $item);
					
					if (isset($key))
					{
						switch ($key)
						{
							case 'host':
								$dbparams["host"] = $value;
								break;
							case 'port':
								$dbparams["port"] = $value;
								break;
							case 'name':
								$dbparams["name"] = $value;
								break;
							case 'character':
								$dbparams["character"] = $value;
								break;
							case 'user':
								$dbparams["user"] = $value;
								break;
							case 'password':
								$dbparams["password"] = $value;
								break;
						}
					} 
				}
				
				return $dbparams;
			}
			else
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			return false;
		}
	}	
	
	// 计算执行时间
	public static function Runtime($mode = 0)
	{
		static $start;
		
		if (empty($mode))
		{
			$start = microtime(true);
			return false;
		}
		$end = microtime(true);
		
		$r = ($end - $start) * 1000;
		return number_format($r, 4, '.', '').'';
	}
	
	// 输出响应
	public static function Response($responseText = '')
	{
		$callback = "";
			
		header("Content-Type:text/html;charset=utf-8");
			
		if (array_key_exists("callback", $_REQUEST))
		{
			$callback = $_REQUEST["callback"];
		}
		else
		{
			// header("Content-Type:application/x-json;charset=utf-8");
		}
		
		if (empty($callback))
		{
			echo $responseText;
		}
		else
		{
			echo $callback."($responseText)";
		}
		
		exit();
	}
	
	public static function createSessionId()
	{
		$IP = CR_Utility::GetClientAddress();
		// 获取当前时间的微秒
		list($u, $s) = explode(' ', microtime());
		$time = (float)$u + (float)$s;
		// 产生一个随机数
		$rand_num = rand(100000, 999999);
		$rand_num = rand($rand_num, $time);
		mt_srand($rand_num);
		$rand_num = mt_rand();
		// 产生SessionID
		$sess_id = md5($IP.$time.$rand_num);
		// 截取指定长度的SessionID
		$sess_id = substr($sess_id, 0, 32);
		return $sess_id;
	}
	public static function createGuid()
	{
		if (function_exists('com_create_guid'))
		{
			return preg_replace('/(\{?)|(\}?)/', '', com_create_guid());
		}
		else
		{
			mt_srand((double)microtime()*10000); // optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = "-";
			$uuid = substr($charid, 0, 8).$hyphen
				.substr($charid, 8, 4).$hyphen
				.substr($charid,12, 4).$hyphen
				.substr($charid,16, 4).$hyphen
				.substr($charid,20,12);
					
			return $uuid;
		}
	}
	
	public static function do_mencrypt($input, $key)
	{ 
		global $log;
	    $key = substr(md5($key), 0, 8);	
	    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
	    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	    mcrypt_generic_init($td, $key, $iv);
	    $encrypted_data = mcrypt_generic($td, $input);
	    $encrypted_data = mcrypt_generic($td, $encrypted_data);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	    return base64_encode($encrypted_data);
	}
	   
	public static function do_mdecrypt($input, $key)
	{  
	    $input = base64_decode($input);
	    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
	    $key = substr(md5($key), 0, 8);
	    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	    mcrypt_generic_init($td, $key, $iv);
	    $decrypted_data = mdecrypt_generic($td, $input);
	    $decrypted_data = mdecrypt_generic($td, $decrypted_data);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	    return $decrypted_data;
	} 
	
	/* *
	 format character Description Example returned values 
		Day  
		--- --- 
		d Day of the month, 2 digits with leading zeros 01 to 31 
		D A textual representation of a day, three letters Mon through Sun 
		j Day of the month without leading zeros 1 to 31 
		l (lowercase 'L') A full textual representation of the day of the week Sunday through Saturday 
		N ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0) 1 (for Monday) through 7 (for Sunday) 
		S English ordinal suffix for the day of the month, 2 characters st, nd, rd or th. Works well with j  
		w Numeric representation of the day of the week 0 (for Sunday) through 6 (for Saturday) 
		z The day of the year (starting from 0) 0 through 365 
		Week  
		--- --- 
		W ISO-8601 week number of year, weeks starting on Monday (added in PHP 4.1.0) Example: 42 (the 42nd week in the year) 
		Month  
		--- --- 
		F A full textual representation of a month, such as January or March January through December 
		m Numeric representation of a month, with leading zeros 01 through 12 
		M A short textual representation of a month, three letters Jan through Dec 
		n Numeric representation of a month, without leading zeros 1 through 12 
		t Number of days in the given month 28 through 31 
		Year  
		--- --- 
		L Whether it's a leap year 1 if it is a leap year, 0 otherwise. 
		o ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead. (added in PHP 5.1.0) Examples: 1999 or 2003 
		Y A full numeric representation of a year, 4 digits Examples: 1999 or 2003 
		y A two digit representation of a year Examples: 99 or 03 
		Time  
		--- --- 
		a Lowercase Ante meridiem and Post meridiem am or pm 
		A Uppercase Ante meridiem and Post meridiem AM or PM 
		B Swatch Internet time 000 through 999 
		g 12-hour format of an hour without leading zeros 1 through 12 
		G 24-hour format of an hour without leading zeros 0 through 23 
		h 12-hour format of an hour with leading zeros 01 through 12 
		H 24-hour format of an hour with leading zeros 00 through 23 
		i Minutes with leading zeros 00 to 59 
		s Seconds, with leading zeros 00 through 59 
		u Microseconds (added in PHP 5.2.2) Example: 654321 
		Timezone  
		--- --- 
		e Timezone identifier (added in PHP 5.1.0) Examples: UTC, GMT, Atlantic/Azores 
		I (capital i) Whether or not the date is in daylight saving time 1 if Daylight Saving Time, 0 otherwise. 
		O Difference to Greenwich time (GMT) in hours Example: +0200 
		P Difference to Greenwich time (GMT) with colon between hours and minutes (added in PHP 5.1.3) Example: +02:00 
		T Timezone abbreviation Examples: EST, MDT ... 
		Z Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. -43200 through 50400 
		Full Date/Time 
		--- --- 
		c ISO 8601 date (added in PHP 5) 2004-02-12T15:19:21+00:00 
		r RFC 2822 formatted date Example: Thu, 21 Dec 2000 16:01:07 +0200 
		U Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
	 * */
	public static function GetDateTime($format = 'Y-m-d H:i:s', $time = NULL)
	{
		return date($format, isset($time)?$time:time());
	}
	public static function GetStrftime($format = '%Y-%m-%d %H:%M:%S', $time = NULL)
	{
		return strftime($format, isset($time)?$time:time());
	}
	// 获取网络地址内容，php.ini中打开php_curl.dll，执行效率比file_get_contents快很多
	public static function url_get_contents($strUrl, $boolUseCookie = false)
	{
		$ch = curl_init($strUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPGET, true); 
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		if ($boolUseCookie && is_array($_COOKIE) && count($_COOKIE) > 0) {
			$cookie_str = '';
			foreach($_COOKIE as $key => $value) {
				$cookie_str .= "$key=$value; "; 
			}
			curl_setopt($ch, CURLOPT_COOKIE, $cookie_str);
		}
		$response = curl_exec($ch);
		if (curl_errno($ch) != 0) {
			return false;
		}
		curl_close($ch);
		return $response;	
	}
	
	// 获取客户端地址
	public static function GetClientAddress()
	{
		try
		{
			$IP = "";
			if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
			{
				$IP = getenv('HTTP_CLIENT_IP');
			}
			else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
			{
				$IP = getenv('HTTP_X_FORWARDED_FOR');
			}
			else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
			{
				$IP = getenv('REMOTE_ADDR');
			}
			else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
			{
				$IP = $_SERVER['REMOTE_ADDR'];
			}
			else 
			{
				$IP = "unknown";
			}
			
			$PORT = "$0";
			if (array_key_exists('REMOTE_PORT', $_SERVER))
			{
				$PORT = $_SERVER['REMOTE_PORT'];
			}	
			
			return $IP.":".$PORT;
		}
		catch (Exception $e)
		{
			return "uncatched";
		}
	}
	
	// @huzw 2015.11.04 add
	// 获取随机码
	public static function GetRandCode($times = 6, $onlyNums = FALSE)
	{
	    try
	    {
	        if (empty($times))
	        {
	            $times = 6;
	        }
	        $times = intval($times);
	        
	        $randVal = '';
	        
	        $letters = "DBCHITUYERMOGZVJNQSPWFXKLAahidbfkpgstojwnzceyulmxqrv0123456789";
	        
	        if ($onlyNums === true)
	        {
	            $letters = "0123456789";
	        }
	        
	        for($i=0; $i< $times; $i++)
	        {
	            $index = rand(0, 999999) % (strlen($letters));
	            $randVal .= substr($letters, $index, 1);
	        }
	        
	        return $randVal;
	    }
	    catch (Exception $e)
	    {
	        return rand(100000, 999999);
	    }
	}
	
	// @huzw 2016.10.11 add
	// 过滤特殊字符，防止SQL脚本注入
	public static function FilterSpecialChars($val)
	{
	    try
	    {
	        // $strTemp = "^&h\\/!@#$%^&*()+|/jgfj&%fgd''$#$@!)(}|";
	        	
	        $r = "/(^|\\&)|(\\|)|(\\;)|(\\$)|(\\%)|(\\@)|(\\')|(\\\")|(\\>)|(\\<)|(\\<\\>)|(\\))|(\\()|(\\+)|(\\,)|(\\.)|(script)|(document)|(eval)|(SELECT)|(FROM)|(UPDATE)|(DELETE)|(UNION)|(WHERE)|(\\\\)|(\\#|$)/m";
	        	
	        $val = preg_replace($r, '', $val);
	        return $val;
	    }
	    catch (Exception $e)
	    {
	        return '';
	    }
	}
}

// 返回值结构定义
class CR_ReturnValue
{
	var $errorCode;
	var $time;
	var $action;
	var $content;
	
	function __construct($errorCode = null, $time = null, $action = null, $content = null) 
	{
		if (!isset($errorCode))
		{
			$errorCode = "0x0000";
		}
		$this->errorCode = $errorCode;
		if (!isset($time))
		{
			$time = CR_Utility::GetDateTime();
		}
		$this->time = $time;
		$this->time_utc = time();
		$this->action = $action;
		$this->content = $content;
	}
}

?>