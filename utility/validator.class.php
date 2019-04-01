<?php

class Validator
{
	private function __construct() {}
	
	// 检验整型数据（不能是负数）。
	public static function checkInteger($value, $allowEmpty = false)
	{
		if (!isset($value))
		{
			return $allowEmpty == true;
		}
		
		if(!preg_match('/^[1-9]\d*|0$/', $value))
		{
			return false;
		}
		
		return true;
	}
	
	// 检验浮点型数据（不能是负数）。
	public static function checkFloat($value, $allowEmpty = false)
	{
		if (!isset($value))
		{
			return $allowEmpty == true;
		}
		
		if(!preg_match('/^[1-9]\d*|[1-9]\d*\.\d+|0?\.\d+|0$/', $value))
		{
			return false;
		}
		
		return true;
	}
	
	// 检验整型数据（不能是负数）及其范围。$min为null时，不检验下限值；$max为null时，不检验上限值。
	public static function checkIntegerRange($value, $min, $max, $allowEmpty = false)
	{
		if (!isset($value))
		{
			return $allowEmpty == true;
		}
		
		if(!preg_match('/^[1-9]\d*|0$/', $value))
		{
			return false;
		}
		
		if ($min !== null && intval($value) < $min)
		{
			return false;
		}
		
		if ($max !== null && intval($value) > $max)
		{
			return false;
		}
		
		return true;
	}
	
	// 检验浮点型数据（不能是负数）及其范围。$min为null时，不检验下限值；$max为null时，不检验上限值。
	public static function checkFloatRange($value, $min, $max, $allowEmpty = false)
	{
		if (!isset($value))
		{
			return $allowEmpty == true;
		}
		
		if(!preg_match('/^[1-9]\d*|[1-9]\d*\.\d+|0?\.\d+|0$/', $value))
		{
			return false;
		}
		
		if ($min !== null && floatval($value) < $min)
		{
			return false;
		}
		
		if ($max !== null && floatval($value) > $max)
		{
			return false;
		}
		
		return true;
	}
	
	// 检验字符串长度范围。$min为null时，不检验下限值；$max为null时，不检验上限值。
	public static function checkStringLength($value, $min, $max, $allowEmpty = false)
	{
		if (empty($value))
		{
			return $allowEmpty == true;
		}
		
		$len = mb_strlen($value, 'UTF8');
		if ($min !== null && $len < $min)
		{
			return false;
		}
		
		if ($max !== null && $len > $max)
		{
			return false;
		}
		
		return true;
	}
	
	// 检验会话ID合法性。$min为null时，不检验下限值；$max为null时，不检验上限值。
	public static function checkSessionID($value, $min, $max, $allowEmpty = false)
	{
		return self::checkStringLength($value, $min, $max, $allowEmpty);
	}
	
	// 检验公司邮箱合法性。
	public static function checkCrearoEmail($value, $allowEmpty = false)
	{
		if (empty($value))
		{
			return $allowEmpty == true;
		}
		
		if(!preg_match('/^[_a-z0-9\-]+(\.[_a-z0-9\-]*)*@crearo.com$/i', $value))
		{
			return false;
		}
		
		return true;
	}
	
	// 检验通用邮箱合法性。
	public static function checkEmail($value, $allowEmpty = false)
	{
		if (empty($value))
		{
			return $allowEmpty == true;
		}
		
		if(!preg_match('/^[_a-z0-9\-]+(\.[_a-z0-9\-]*)*@([a-z0-9]*[-_]?[a-z0-9]+)+\.[a-z]{2,3}(\.[a-z]{2})?$/i', $value))
		{
			return false;
		}
		
		return true;
	}
	
	// 检验日期合法性。
	public static function checkDate($value, $allowEmpty = false)
	{
		if (empty($value))
		{
			return $allowEmpty == true;
		}
		
		if(!preg_match('/^\d{4}-(0?[1-9]|[1][012])-(0?[1-9]|[12][0-9]|[3][01])$/', $value))
		{
			return false;
		}
		
		return true;
	}
	
	// 检验时间合法性。
	public static function checkTime($value, $allowEmpty = false)
	{
		if (empty($value))
		{
			return $allowEmpty == true;
		}
		
		if(!preg_match('/^(0?[0-9]|[1][0-9]|[2][0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9])$/', $value))
		{
			return false;
		}
		
		return true;
	}
	
	// 检验日期时间合法性。
	public static function checkDateTime($value, $allowEmpty = false)
	{
		if (empty($value))
		{
			return $allowEmpty == true;
		}
		
		if(!preg_match('/^\d{4}-(0?[1-9]|[1][012])-(0?[1-9]|[12][0-9]|[3][01]) ' .
				'(0?[0-9]|[1][0-9]|[2][0-3]):(0?[0-9]|[1-5][0-9]):(0?[0-9]|[1-5][0-9])$/', $value))
		{
			return false;
		}
		
		return true;
	}
	
	// 检验域名
	public static function checkDomain($url)
	{
		try {
			if ($url)
			{
				$domain = '/^(https|http|ftp|rtsp|mms):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';
				return preg_match($domain, $url) ? true : false; 
			}
			return false;
		}
		catch(Exception $e) {
			return false;
		}
	}
	
	// 检验目录
	public static function checkFolderFormat ($folderPath, $allowEmpty = false)
	{
	    try {
	        
	        if ($allowEmpty === true)
	        {
	            if (empty($folderPath))
	            {
	                return true;
	            }
	        }
	        
	        if ($folderPath)
	        {
	            return preg_match('/^[A-Za-z]{1}:(\/|\\\\).*$/', $folderPath) ? true : false;
	        }
	        return false;
	    }
	    catch(Exception $e) {
	        return false;
	    }
	}
	
	// 验证IP
	public static function checkIP ($IP, $allowEmpty = false)
	{
	    try {
	        
	        if ($allowEmpty === true)
	        {
	            if (empty($IP))
	            {
	                return true;
	            }
	        }
	        
	        $IPReg = '/^([0-9]|[0-9][0-9]|[1-3][0-9][0-9]|[2][0-5][0-5])[\.]([0-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-5])[\.]([0-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-5])[\.]([0-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-5])$|^([^\f\n\r\t\v]){0,}$/';
	        
	        return preg_match($IPReg, $IP) ? true : false;
	        
	    } catch (Exception $e) {
	        return false;
	    }
	}
}

?>
