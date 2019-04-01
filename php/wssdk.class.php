<?php

define('WSSdkSource_PLATSOURCEPATH', dirname(dirname(dirname(__FILE__))).'/nmi/dbserver/PlatConnParam.ini');

class WSSdkSource
{
    const VERSION = 'v20180608001';
    
    const PLATSOURCEPATH = "../dbserver/PlatConnParam.ini";
    
	public static $G_EPID = 'system';
	
    public function __construct() {}
     
	public static function Set_G_EPID ($EPID = null)
	{
		if (isset($EPID))
		{
			self::$G_EPID = $EPID;
		}
	}	
	 
    public static function Read_Conn_Params (&$cp = null)
    {
        try {
            
            $ret = ErrorCode::FAILED;
            
            if (empty($cp))
            {
                $cp = new stdClass();
                $cp->SOAP_PATH = "";
                $cp->username = "";
                $cp->epid = "";
                $cp->password = "";
            }
            
            // 读配置文件
            if (file_exists(WSSdkSource_PLATSOURCEPATH))
            {
                $ret = ErrorCode::SUCCESS;
                
                $contents = file_get_contents(WSSdkSource_PLATSOURCEPATH);
            
                $ini_nodes = explode("\r\n", $contents);

                GLogger(__FILE__, __LINE__, __FUNCTION__, print_r($ini_nodes, true));
                
                foreach ($ini_nodes as $item)
                {
                    list($key, $value) = preg_split("/\\=/", $item);
                    	
                    if (isset($key))
                    {
                        switch ($key)
                        {
                            case 'SOAP_PATH':
                                $cp->SOAP_PATH = $value;
                                break;
                            case 'username':
                                $cp->username = $value;
                                break;
                            case 'epid':
                                $cp->epid = $value;
                                break;
                            case 'password':
                                $cp->password = strtolower($value);
                                break;
                        }
                    }
                }
				
				if (strcmp(self::$G_EPID, $cp->epid) != 0)
				{
					try 
					{
						// 查对应admin的接入密码
						global $db_link;
						
						$query = "
							select u.`AccreditedPassword` as `AccessPsw`
							from `UserInfo` u
							inner join `EPInfo` e on e.`Index`=u.`EPInfo_Index`
							where e.`EPID`='".self::$G_EPID."' 
								and u.`Identity`='admin'
							limit 1
						";
						
						GLogger(__FILE__, __LINE__, __FUNCTION__, "$query");
						
						$result = $db_link->Query($query);
						
						if ($result)
						{
							if (RowFetchObject($row, $result, $db_link))
							{
								$cp->epid = self::$G_EPID;
								$cp->password = $row->AccessPsw;
							}
						}
					}
					catch (Exception $ee) 
					{
						GLogger(__FILE__, __LINE__, __FUNCTION__, '$db link excep -> '.$ee->getMessage());
					}
				}
            }
            else 
            {
                GLogger(__FILE__, __LINE__, __FUNCTION__, 'PlatConnParam.ini not exists.');
                
                $ret = ErrorCode::PLAT_SOURCE_LOST;   
            }
            
            //GLogger(__FILE__, __LINE__, __FUNCTION__, print_r($cp, true));
            
            return $ret;
            
        } catch (Exception $e) {
            return ErrorCode::THREAD;
        }
    }
    
    public static function SoapClient ($SOAP_PATH, &$soap = null)
    {
        try {
            
            WSSdkSource::SynchSoapPath($SOAP_PATH);
            
			GLogger(__FILE__, __LINE__, __FUNCTION__, "Start new SoapClient...");
			
            $soap = new SoapClient($SOAP_PATH);
        
			GLogger(__FILE__, __LINE__, __FUNCTION__, "End SoapClient -> ".print_r($soap, true));
			
            return ErrorCode::SUCCESS;
        }
        catch(SoapFault $e) {
            GLogger(__FILE__, __LINE__, __FUNCTION__, "errorCode -> ".ErrorCode::SOAP_WSSDK_FAILED);
            return ErrorCode::SOAP_WSSDK_FAILED;
        }
        catch (Exception $e) {
            GLogger(__FILE__, __LINE__, __FUNCTION__, "errorCode -> ".ErrorCode::THREAD);
            return ErrorCode::THREAD;
        }
    }
    
    // @huzw 2016.08.03 add -> 2018.06.08 mdf
    // 同步Soap地址
    public static function SynchSoapPath (&$SESSION_SOAP_PATH)
    {
        try {
        
            $ret = WSSdkSource::Read_Conn_Params($cp);
            if ($ret == ErrorCode::SUCCESS && !empty($cp))
            {
                if ($cp->SOAP_PATH != $SESSION_SOAP_PATH)
                {
                    $SESSION_SOAP_PATH = $cp->SOAP_PATH;
                    
                    if (isset($_SESSION['NMI'], 
							$_SESSION['NMI']['WSSdk'], 
							$_SESSION['NMI']['WSSdk'][self::$G_EPID]))
                    {
                        $_SESSION['NMI']['WSSdk'][self::$G_EPID]['SOAP_PATH'] = $cp->SOAP_PATH;
                    }
                }
            }
            	
            return ErrorCode::SUCCESS;
        }
        catch (Exception $e) {
            GLogger(__FILE__, __LINE__, __FUNCTION__, "errorCode -> ".ErrorCode::THREAD.", excp msg -> ".$e->getMessage());
            return ErrorCode::THREAD;
        }
    }

    // 建立连接
    public static function Connect()
    {
        try {
            $aRet = array('errorCode' => ErrorCode::SUCCESS);

            if (isset($_SESSION['NMI'], 
					$_SESSION['NMI']['WSSdk'], 
					$_SESSION['NMI']['WSSdk'][self::$G_EPID])
                && !empty($_SESSION['NMI']['WSSdk'][self::$G_EPID]['sessionID']))
            {
                $aRet['errorCode'] = ErrorCode::PLAT_ALREADY_LOGIN;
                return $aRet;
            }
            
            $cp = null;
            
            $ret = WSSdkSource::Read_Conn_Params($cp);
            
            if ($ret != ErrorCode::SUCCESS)
            {
                $aRet['errorCode'] = $ret;
                return $aRet;
            }
             
            $soap = null;
            $ret = WSSdkSource::SoapClient($cp->SOAP_PATH, $soap);
            if ($ret != ErrorCode::SUCCESS)
            {
                $aRet['errorCode'] = $ret;
                return $aRet;
            }
			
			GLogger(__FILE__, __LINE__, __FUNCTION__, "Start Connecting...");
           
            $rv = $soap->getRequest(array(
                'request' => json_encode(array(
                    'action' => 'Connect',
                    'content' => array(
                        'userID' => "$cp->username@$cp->epid",
                        'password' => CR_Utility::GetRandCode(6)."{$cp->password}",
                        'bMD5AccreditedPsw' => 1
                    )
                ))
            ));
			
            GLogger(__FILE__, __LINE__, __FUNCTION__, $rv->response);
            
            $response = json_decode($rv->response);
            
            if ($response->errorCode != WSSdkErrorCode::SUCCESS)
            {
                switch ($response->errorCode)
                {
                    case WSSdkErrorCode::LOGIN_USERID_NOTEXISTS:
                        $aRet['errorCode'] = ErrorCode::PLAT_ID_ERROR;
                        break;
                    case WSSdkErrorCode::LOGIN_PASSWORD_ERROR:
                        $aRet['errorCode'] = ErrorCode::PLAT_PSW_ERROR;
                        break;
                    default:
                        $aRet['errorCode'] = ErrorCode::FAILED;
                        break;
                }
        
                return $aRet;
            }
            else 
            {
                // 连接成功，保存Sessions信息
                $content = $response->content;
                
				// session_start();
				
				if (!isset($_SESSION['NMI'], $_SESSION['NMI']['WSSdk']))
				{
					$_SESSION['NMI']['WSSdk'] = array();
				}
				
				$_SESSION['NMI']['WSSdk'][$cp->epid] = array(
                    'SOAP_PATH' => $cp->SOAP_PATH,
                    'username' => $cp->username,
                    'epid' => $cp->epid,
                    'password' => $cp->password,
                    'sessionID' => $content->sessionID
                );
				
				// session_commit();
            }
            
            return $aRet;
            
        } catch (Exception $e) {
            GLogger(__FILE__, __LINE__, __FUNCTION__, "errorCode -> ".ErrorCode::THREAD.", excep msg -> ".$e->getMessage());
            return array('errorCode' => ErrorCode::THREAD);
        }
    }
    
    // 断开连接
    public static function DisConnect() 
    {
        try
		{
			$aRet = array('errorCode' => ErrorCode::SUCCESS);
			 	
			if (isset($_SESSION['NMI'], $_SESSION['NMI']['WSSdk']))
			{
				foreach ($_SESSION['NMI']['WSSdk'] as $item)
				{
					if (isset($item[self::$G_EPID]) 
						&& !empty($item[self::$G_EPID]['sessionID']))
					{
						$soap = null;
						$ret = WSSdkSource::SoapClient($item[self::$G_EPID]['SOAP_PATH'], $soap);
						if ($ret != ErrorCode::SUCCESS)
						{
							$aRet['errorCode'] = $ret;
							return $aRet;
						}
						
						$rv = $soap->getRequest(array(
							'request' => json_encode(array(
								'action' => 'DisConnect',
								'content' => array(
									'sessionID' => $item[self::$G_EPID]['sessionID']
								)
							))
						));	
						
						unset($item[self::$G_EPID]);
					}					
				}	
			}		
			
			return $aRet;
		}
		catch (Exception $e)
		{
			return array('errorCode' => ErrorCode::THREAD);
		}
    }

    public static function __checkLogin()
    {
        try {
			
			$ret = ErrorCode::PLAT_NOT_LOGIN;
			
			$need_try = false;
			
			if (isset($_SESSION['NMI'], 
					$_SESSION['NMI']['WSSdk']))
			{
			    // 删除会话信息（为了兼容旧程序）
			    if (isset($_SESSION['NMI']['WSSdk']['SOAP_PATH']))
			    {
			        unset($_SESSION['NMI']['WSSdk']);
			     
			        $need_try = true;
			    }
			    else
			    {
			        $found = false;
			        
			        foreach ($_SESSION['NMI']['WSSdk'] as $key=>$value)
			        {
			            if (strcmp($key, self::$G_EPID) == 0)
			            {
			                $found = true;
			                
			                if (empty($value['sessionID']))
			                {
			                    $need_try = true;
			                }
			            }
			        }
			        if (!$found)
			        {
			            $need_try = true;
			        }
			    }
			}
			else 
			{
                $need_try = true;
			}
			
			if ($need_try)
			{
			    // 连接一次
			    $__aRet = WSSdkSource::Connect();
			    if ($__aRet['errorCode'] == ErrorCode::SUCCESS)
			    {
			        $ret = ErrorCode::SUCCESS;
			    }
			}
			
			if ($ret !== ErrorCode::SUCCESS)
			{
				if (!isset($_SESSION['NMI'], 
						$_SESSION['NMI']['WSSdk'],
						$_SESSION['NMI']['WSSdk'][self::$G_EPID])
					|| empty($_SESSION['NMI']['WSSdk'][self::$G_EPID]['sessionID']))
				{
					$ret = ErrorCode::PLAT_NOT_LOGIN;
				}
				else 
				{
					$ret = ErrorCode::PLAT_ALREADY_LOGIN;
				}
			}
			
			return $ret;
        
        } catch (Exception $e) {
            return ErrorCode::THREAD;
        }
    }
    
    public static function __getsoap (&$soap = null)
    {
        try {
            $ret = WSSdkSource::__checkLogin();
            
            if ($ret != ErrorCode::SUCCESS && $ret != ErrorCode::PLAT_ALREADY_LOGIN)
            {
                return $ret;
            }
            
            $cp = $_SESSION['NMI']['WSSdk'][self::$G_EPID];
			
            $soap = null;
            $ret = WSSdkSource::SoapClient($cp['SOAP_PATH'], $soap);
            if ($ret != ErrorCode::SUCCESS)
            {
                return $ret;
            }
    
            return ErrorCode::SUCCESS;
    
        } catch (Exception $e) {
            return ErrorCode::THREAD;
        }
    }
    
	// 向WebService发生请求
	public static function __getRequest ($action='', $content='', &$response)
	{
		$ret = ErrorCode::SUCCESS;
		try
		{ 
            $soap = null;
            $ret = WSSdkSource::__getsoap($soap);
            if ($ret != ErrorCode::SUCCESS)
            {
                return $ret;
            }

            GLogger(__FILE__, __LINE__, __FUNCTION__, print_r( $_SESSION['NMI']['WSSdk'][self::$G_EPID], true));
            
            if (empty($content)) 
            {
                $content = array();
            }
            
            $content['sessionID'] = $_SESSION['NMI']['WSSdk'][self::$G_EPID]['sessionID'];
            
            GLogger(__FILE__, __LINE__, __FUNCTION__, print_r($content, true));
            
            $rv = $soap->getRequest(array(
                'request' => json_encode(array(
                    'action' => $action,
                    'content' => $content
                ))
            ));
			
			
            GLogger(__FILE__, __LINE__, __FUNCTION__, print_r($rv, true));
			
            GLogger(__FILE__, __LINE__, __FUNCTION__, $rv->response);
            
            $response = json_decode($rv->response);

            WSSdkSource::__validResponse($response->errorCode);
            
            if ($response->errorCode == WSSdkErrorCode::SESSION_UNVALID_OR_NOTEXISTS)
            {
                return ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            }
            
            return $ret;
    
        } catch (Exception $e) {
            GLogger(__FILE__, __LINE__, __FUNCTION__, "errorCode -> ".ErrorCode::THREAD.", excep msg -> ".$e->getMessage());
            return ErrorCode::THREAD;
        }
	}

	// 检测会话信息是否失效
	public static function __validResponse($errorCode)
	{
	    try {
	        switch ($errorCode)
	        {
	             case WSSdkErrorCode::SESSION_UNVALID_OR_NOTEXISTS:
					// 会话失效就删除
					 WSSdkSource::DisConnect();
	                 
	                 unset($_SESSION['NMI']['WSSdk'][self::$G_EPID]);
	                 break;
	             default:
	                 break;
	        }
	        
			return ErrorCode::SUCCESS;
	    } catch (Exception $e) {
	        return ErrorCode::THREAD;
	    }
	}
	
	// 获取优先级
	public static function __getUserPriority(&$priority=1)
	{
	    try {
            $ret = WSSdkSource::__getRequest('GetUserPriority', array(), $response);
	        
            if ($ret == ErrorCode::SUCCESS && !empty($response->content))
            {
                $priority = $response->content;
            }
            else
            {
                $priority = 1;
            }
            
	        return ErrorCode::SUCCESS;
	    } catch (Exception $e) {
	        return ErrorCode::THREAD;
	    }
	}
	
	///
	/// 万能接口
	///
	// 向设备发送请求命令
	// - puid 
	// - cmdType GET(default) | SET | PREV | CTL
    // - xmlDstRes
	public static function TransCommonMessage($param)
	{
	    try {
            GLogger(__FILE__, __LINE__, __FUNCTION__, "wssdk xml param=".$param);
	        $aRet = array('errorCode' => ErrorCode::SUCCESS);
	        
	        $param->cmdType = empty($param->cmdType) ? 'GET' : $param->cmdType;
	        
	        $param->xmlDstRes = rawurldecode($param->xmlDstRes);
	        
            //WSSdkSource::__getUserPriority($priority);
            $priority = 1;
	        
	        $xmlRequest = sprintf('<?xml version="1.0" encoding="UTF-8"?>'.
	                      '<Msg Name="CUCommonMsgReq" DomainRoad="">'.
                          '<Cmd Type="%s" Prio="%s">%s</Cmd>'.
	                      '</Msg>', $param->cmdType, $priority, $param->xmlDstRes);
	        
	        $ret = WSSdkSource::__getRequest('TransCommonMessage', array(
                'puid' => $param->PUID,
	            'xmlstr' => $xmlRequest
	        ), $response);
	        	
	        if ($ret != ErrorCode::SUCCESS)
	        {
	            $aRet['errorCode'] = $ret;
	            return $aRet;
	        }
	        	
	        if ($response->errorCode != WSSdkErrorCode::SUCCESS)
	        {
	            $aRet['errorCode'] = ErrorCode::FAILED;
	        }
	        else
	        {
	            $content_arr = array();
	            
	            $xml_content = $response->content;
	            
	            $xmldoc = new DOMDocument('1.0', 'utf-8');
	            if ($xmldoc->loadXML($xml_content))
	            {
                    $xpath = new DOMXPath($xmldoc);
                    
                    $cmd = $xpath->query('/Msg/Cmd')->item(0);
                    
                    if ($cmd->getAttribute('NUErrorCode') != 0)
                    {
                        if ($cmd->getAttribute('NUErrorCode') == 8714) {
                            $aRet['errorCode'] = ErrorCode::DEVICE_NOT_ONLINE;
                        }
                    }
                    else 
                    {
                        $dstRes_nodes = $cmd->getElementsByTagName('DstRes');
                        
                        foreach ($dstRes_nodes as $dstRes)
                        {
                            $_param = array();

                            if ($dstRes->getAttribute('ErrorCode') == 0)
                            {
                                $params_node = $dstRes->getElementsByTagName('Param');
                                
                                if ($params_node && !empty($params_node->length))
	                            {
	                                $param_node = $params_node->item(0);
	                               
	                                $found_attr = false;
	                               
	                                if ($param_node->hasAttributes())
	                                {
                                        $found_attr = true;

	                                    foreach ($param_node->attributes as $attr)
	                                    {
                                            $_param[$attr->nodeName] = $attr->nodeValue;
	                                    }
	                                }
	                                
	                                if (!$found_attr)
	                                {
	                                    foreach ($param_node->childNodes as $node)
	                                    {
                                            if ($node->nodeName == '#text')
                                            {
                                                continue;
                                            }
                                            if (!array_key_exists($node->nodeName, $_param))
                                            {
                                                $_param[$node->nodeName] = array();
                                            }
                                
                                            $normal = true;
                                            $node_childs = array();

                                            if ($node->hasAttributes())
                                            {
                                                $normal = false;

                                                foreach ($node->attributes as $attr)
                                                {
                                                    $node_childs[$attr->nodeName] = $attr->nodeValue;
                                                }
                                            }
                                            else 
                                            {
                                                array_push($_param[$node->nodeName], $node->nodeValue);
                                            }

                                            if (!empty($node->childNodes->length))
                                            {
                                                $normal = false;
                                                
                                                foreach ($node->childNodes as $node_child)
                                                {
                                                    if ($node_child->nodeName == '#text')
                                                    {
                                                        continue;
                                                    }

                                                    if (!array_key_exists($node_child->nodeName, $node_childs))
                                                    {
                                                        $node_childs[$node_child->nodeName] = array();
                                                    }

                                                    if ($node_child->hasAttributes())
                                                    {
                                                        foreach ($node_child->attributes as $attr)
                                                        {
                                                            $node_childs[$node_child->nodeName][$attr->nodeName] = $attr->nodeValue;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        array_push($node_childs[$node_child->nodeName], $node_child->nodeValue);
                                                    }
                                                }
                                            }
                                            
                                            GLogger(__FILE__, __LINE__, __FUNCTION__, print_r($node_childs, true));

                                            if ($normal)
                                            {
                                                array_push($_param[$node->nodeName], $node->nodeValue);
                                            }
                                            else
                                            {
                                                if (!empty($node_childs)) array_push($_param[$node->nodeName], $node_childs);
                                            }
	                                    }
	                                }
	                            }
	                        }
	                       
	                        array_push($content_arr, array(
                                'Type' => $dstRes->getAttribute('Type'),
                                'Idx' => intval($dstRes->getAttribute('Idx')),
                                'OptID' => $dstRes->getAttribute('OptID'),
                                'Setable' => intval($dstRes->getAttribute('Setable')), 
                                'ErrorCode' => intval($dstRes->getAttribute('ErrorCode')),
                                'Param' => $_param
	                        ));
	                   }
	               }
	            } 
	            
                $aRet['object_content'] = json_decode(json_encode($content_arr));
                
                $aRet['content'] = rawurlencode(json_encode($content_arr));

                $aRet['xml_content'] = $xml_content;
	        }
	        
	        return $aRet;
	        
        } catch (Exception $e) {
            return array('errorCode' => ErrorCode::THREAD);
        }
	}
    
    // 向网元发送请求命令
    // @params: $param => stdClass of
    // {
    //    cmdType GET(default) | SET | PREV | CTL  
    //    NUType * 网元类型
    //    NUID * 网元ID
    //    xmlDstRes * 请求XML报文dstRes节点
    //    xmlExtra 其他报文节点，比如ObjSets、SrcSets等等，放在Cmd标签之后
    // }
    public static function TransNUCommonMessage ($param)
    {
        try {
	        $aRet = array('errorCode' => ErrorCode::SUCCESS);
	        
	        $param->cmdType = empty($param->cmdType) ? 'GET' : $param->cmdType;
	        
	        $param->xmlDstRes = rawurldecode($param->xmlDstRes);
	        
            //WSSdkSource::__getUserPriority($priority);
            $priority = 1;
	        
	        $xmlRequest = sprintf('<?xml version="1.0" encoding="UTF-8"?>'.
	                      '<Msg Name="CUCommonMsgReq" DomainRoad="">'.
                          '<Cmd Type="%s" Prio="%s" EPID="%s">%s</Cmd>'.
                          '%s'.
	                      '</Msg>', $param->cmdType, $priority, self::$G_EPID, $param->xmlDstRes, $param->xmlExtra);
            
            GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".$xmlRequest);
	        $ret = WSSdkSource::__getRequest('TransNUCommonMessage', array(
                'NUType' => $param->nuType,
                'NUID' => $param->NUID,
	            'xmlstr' => $xmlRequest
	        ), $response);
			
	        GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml request=".print_r($ret,true));	
	        if ($ret != ErrorCode::SUCCESS)
	        {
	            $aRet['errorCode'] = $ret;
	            return $aRet;
	        }
	        	
	        if ($response->errorCode != WSSdkErrorCode::SUCCESS)
	        {
				GLogger(__FILE__, __LINE__, __FUNCTION__, 'errorCode='.$response->errorCode);	
	            $aRet['errorCode'] = ErrorCode::FAILED;
	        }
	        else
	        {
                $content_arr = array();
	            
	            $xml_content = $response->content;
	            
	            $xmldoc = new DOMDocument();
	            if ($xmldoc->loadXML($xml_content))
	            {
                    $xpath = new DOMXPath($xmldoc);
                    
                    $cmd = $xpath->query('/Msg/Cmd')->item(0);
                    
                    if ($cmd->getAttribute('NUErrorCode') != 0)
                    {
                        if ($cmd->getAttribute('NUErrorCode') == 8714) {
                            $aRet['errorCode'] = ErrorCode::DEVICE_NOT_ONLINE;
                        }
                        else if ($cmd->getAttributes('NUErrorCode') == 17409){
                            $aRet['errorCode'] = ErrorCode::XML_RESPONSE_NU_ERROR;
                        }
                        else if ($cmd->getAttributes('NUErrorCode') == 17410){
                            $aRet['errorCode'] = ErrorCode::XML_RESPONSE_NU_TIMEOUT;
                        }
						else{
							$aRet['errorCode'] = ErrorCode::XML_RESPONSE_NU_ERROR;
						}
                    }
                    else 
                    {
                        $dstRes_nodes = $cmd->getElementsByTagName('DstRes');
                        
                        foreach ($dstRes_nodes as $dstRes)
                        {
                            $_param = array();

                            if ($dstRes->getAttribute('ErrorCode') == 0)
                            {
                                $param_node = $dstRes->getElementsByTagName('Param');
                                
                                if ($param_node && !empty($param_node->length))
                                {
                                    $param_node = $param_node->item(0);
                                    
                                    $found_attr = false;
                                    
                                    if ($param_node->hasAttributes())
                                    {
                                        foreach ($param_node->attributes as $attr)
                                        {
                                            $found_attr = true;
                                            
                                            $_param[$attr->nodeName] = $attr->nodeValue;
                                        }
                                    }
	                               
                                    if (!$found_attr)
                                    {
                                        foreach ($param_node->childNodes as $node)
                                        {
                                            if ($node->nodeName == '#text')
                                            {
                                                continue;
                                            }
                                            
                                            if (!array_key_exists($node->nodeName, $_param))
                                            {
                                                $_param[$node->nodeName] = array();
                                            }

                                            $normal = true;
                                            $node_childs = array();

                                            if ($node->hasAttributes())
                                            {
                                                $normal = false;

                                                foreach ($node->attributes as $attr)
                                                {
                                                    $node_childs[$attr->nodeName] = $attr->nodeValue;
                                                }
                                            }
                                            if (!empty($node->childNodes->length))
                                            {
                                                $normal = false;
                                                
                                                foreach ($node->childNodes as $node_child)
                                                {
                                                    if ($node_child->nodeName == '#text')
                                                    {
                                                        continue;
                                                    }

                                                    if (!array_key_exists($node_child->nodeName, $node_childs))
                                                    {
                                                        $node_childs[$node_child->nodeName] = array();
                                                    }

                                                    if ($node_child->hasAttributes())
                                                    {
                                                        foreach ($node_child->attributes as $attr)
                                                        {
                                                            $node_childs[$node_child->nodeName][$attr->nodeName] = $attr->nodeValue;
                                                        }
                                                    }
                                                }
                                            }

                                            if ($normal)
                                            {
                                                array_push($_param[$node->nodeName], $node->nodeValue);
                                            }
                                            else
                                            {
                                                switch ($dstRes->getAttribute('OptID'))
                                                {
                                                case 'CTL_LA_GetPlanAction':
                                                    if (!empty($node->childNodes->length))
                                                    {
                                                        $_objSets = array();

                                                        foreach ($node->childNodes as $node_child)
                                                        {
                                                            if ($node_child->nodeName == '#text')
                                                            {
                                                                continue;
                                                            }

                                                            // Res > Param
                                                            if ($node_child->nodeName == 'Param')
                                                            {
                                                                if ($node_child->hasAttributes())
                                                                {
                                                                    foreach ($node_child->attributes as $attr)
                                                                    {
                                                                        $_objSets[$attr->nodeName] = $attr->nodeValue;
                                                                    }

                                                                    // Res > Param > [Param]/[Description]
                                                                    if (!empty($node_child->childNodes->length))
                                                                    {
                                                                        foreach ($node_child->childNodes as $pparam)
                                                                        {
                                                                            if ($pparam->nodeName == '#text')
                                                                            {
                                                                                continue;
                                                                            } 
                                                                            
                                                                            if ($pparam->hasAttributes())
                                                                            {
                                                                                foreach ($pparam->attributes as $attr)
                                                                                {
                                                                                    $_objSets[$attr->nodeName] = $attr->nodeValue;
                                                                                }
                                                                            }
                                                                        }
                                                                    }

                                                                    // Res > Param > [Param] > ObjSets > Res ...
                                                                    $_objSet_Res = $node_child->getElementsByTagName('Res');

                                                                    if (!empty($_objSet_Res->length))
                                                                    {
                                                                        foreach ($_objSet_Res as $res)
                                                                        {
                                                                            if ($res->hasAttributes())
                                                                            {
                                                                                foreach ($res->attributes as $attr)
                                                                                {
                                                                                    $_objSets[$attr->nodeName] = $attr->nodeValue;
                                                                                }
                                                                            }
    
                                                                            if (!empty($res->childNodes->length))
                                                                            {
                                                                                foreach ($res->childNodes as $res_child)
                                                                                {
                                                                                    if ($res_child->nodeName == '#text')
                                                                                    {
                                                                                        continue;
                                                                                    }

                                                                                    if ($res_child->nodeName == 'Param')
                                                                                    {
                                                                                        if ($res_child->hasAttributes())
                                                                                        {
                                                                                            foreach ($res_child->attributes as $attr)
                                                                                            {
                                                                                                $_objSets[$attr->nodeName] = $attr->nodeValue;
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        $node_childs['objSets'] = $_objSets;
                                                    }
                                                    break;
                                                }

                                                array_push($_param[$node->nodeName], $node_childs);
                                            }
                                        }
	                                }
	                            }
	                        }
							else{
								$aRet['errorCode'] = ErrorCode::XML_REQUEST_ERROR;
							}
	                       
	                        array_push($content_arr, array(
	                           'Type' => $dstRes->getAttribute('Type'),
	                           'Idx' => intval($dstRes->getAttribute('Idx')),
	                           'OptID' => $dstRes->getAttribute('OptID'),
	                           'Setable' => intval($dstRes->getAttribute('Setable')), 
	                           'ErrorCode' => intval($dstRes->getAttribute('ErrorCode')),
	                           'Param' => $_param
	                        ));
	                    }
	                }
	            } 
	            
                $aRet['content'] = json_decode(json_encode($content_arr));
                
                $aRet['json_content'] = rawurlencode(json_encode($content_arr));

                $aRet['xml_content'] = $xml_content;
	        }
	        
            return $aRet;
	        
        } catch (Exception $e) {
            return array('errorCode' => ErrorCode::THREAD);
        }
    }
    
    // 获取设备列表
    // $param stdClass
    public static function FetchPUList($param)
    {
        try {
            $aRet = array('errorCode' => ErrorCode::SUCCESS);
            
			$response = null;
			$ret = WSSdkSource::__getRequest('ForkPUResource', array(
						'offset' => $param->offset,
						'count' => $param->count
					), $response);
			
			GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml response=".$ret);
			if ($ret != ErrorCode::SUCCESS)
			{
				$aRet['errorCode'] = $ret;
				return $aRet;
			}
            if ($response->errorCode == WSSdkErrorCode::SUCCESS)
            {
                $aRet['errorCode'] = ErrorCode::FAILED;
            }
            else
            {
                $aRet['content'] = $response->content;
            }
            
            return $aRet;
        
        } catch (Exception $e) {
            return array('errorCode' => ErrorCode::THREAD);
        }
    }
    
    // 获取设备资源模块列表
    // $param stdClass
    public static function FetchPUResourceList($param)
    {
        try {
            $aRet = array('errorCode' => ErrorCode::SUCCESS);
            
			$response = null;
			$ret = WSSdkSource::__getRequest('ForkPUResourceNodes', array(
						'puid' => $param->PUID
					), $response);
			
			if ($ret != ErrorCode::SUCCESS)
			{
				$aRet['errorCode'] = $ret;
				return $aRet;
			}
			
			$aRet['content'] = $response->content;
			
			return $aRet;
        
        } catch (Exception $e) {
            return array('errorCode' => ErrorCode::THREAD);
        }
    }
    
    // 获取流TOKEN
    public static function GetStreamIPToken($param)
    {
        try {
            $aRet = array('errorCode' => ErrorCode::SUCCESS);
        
            $response = null;
            $ret = WSSdkSource::__getRequest('GetStreamIPToken', array(
                'puid' => $param->PUID,
                'resType' => $param->resType,
                'idx' => $param->resIdx,
                'streamType' => $param->streamType,
                'domainRoad' => isset($param->domainRoad) ? $param->domainRoad : ''
            ), $response);
            	
            if ($ret != ErrorCode::SUCCESS)
            {
                if ($response->errorCode != WSSdkErrorCode::SUCCESS)
                {
                    $aRet['errorCode'] = ErrorCode::GET_STREAM_IPTOKEN_FAILED;
                }
                else
                {
                    $aRet['errorCode'] = $ret;
                }
                return $aRet;
            }
            	
            $aRet['content'] = $response->content;
            	
            return $aRet;
        
        } catch (Exception $e) {
            return array('errorCode' => ErrorCode::THREAD);
        }
    }
    
    // 获取RTMP直播流地址
    public static function GetRealtimeRtmpStreamURL($param)
    {
        try {
            $aRet = array('errorCode' => ErrorCode::SUCCESS);
        
            $response = null;
            $ret = WSSdkSource::__getRequest('GetRtmpStreamURL', array(
                'puid' => $param->PUID,
                'idx' => $param->resIdx
            ), $response);
            	
            if ($ret != ErrorCode::SUCCESS || empty($response->content) || empty($response->content->rtmpURL))
            {
                $aRet['errorCode'] = ErrorCode::GET_REALTIME_RTMP_FAILED;
                return $aRet;
            }
            
            if ($param->autoPlay === 1)
            {
                $rtmpURL = $response->content->rtmpURL;
                
                // 开启播放
                $__aRet = self::OpenPlayRtmpStream(json_decode(json_encode(array(
                    'rtmpURL' => $rtmpURL
                ))));
                
                if ($__aRet['errorCode'] != ErrorCode::SUCCESS)
                {
                    $aRet['errorCode'] = $__aRet['errorCode'];
                    return $aRet;
                }
                else 
                {
                    $aRet['content'] = $rtmpURL;
                }
            }
            else
            {
                $aRet['content'] = $response->content->rtmpURL;
            }
            
            return $aRet;
        
        } catch (Exception $e) {
            return array('errorCode' => ErrorCode::THREAD);
        }
    }
    
    // 开始推送RTMP流
    public static function OpenPlayRtmpStream ($param)
    {
        try {
            $aRet = array('errorCode' => ErrorCode::SUCCESS);
        
            $response = null;
            $ret = WSSdkSource::__getRequest('PlayRtmpStream', array(
                'rtmpURL' => $param->rtmpURL
            ), $response);
             
            if ($ret != ErrorCode::SUCCESS)
            {
                $aRet['errorCode'] = ErrorCode::PLAY_RTMP_STREAM_FAILED;
                return $aRet;
            }
        
            $aRet['content'] = $response->content;
            
            return $aRet;
        
        } catch (Exception $e) {
            return array('errorCode' => ErrorCode::THREAD);
        }
    }
    
    // 关闭推送RTMP流
    public static function StopPlayRtmpStream ($param)
    {
        try {
            $aRet = array('errorCode' => ErrorCode::SUCCESS);
        
            $response = null;
            $ret = WSSdkSource::__getRequest('StopRtmpStream', array(
                'rtmpURL' => $param->rtmpURL
            ), $response);
             
            if ($ret != ErrorCode::SUCCESS)
            {
                $aRet['errorCode'] = ErrorCode::STOP_RTMP_STREAM_FAILED;
                return $aRet;
            }
        
            $aRet['content'] = $response->content;
            
            return $aRet;
        
        } catch (Exception $e) {
            return array('errorCode' => ErrorCode::THREAD);
        }
    }
    
}

class WSSdkErrorCode
{
    private function __construct() {}

    CONST SUCCESS = '0x0000'; 											// 成功
    CONST FAILED = '0x0001';											// 失败
    CONST THREAD = '0x0002';											// 抛出异常
    CONST PLUGIN_CONTROL_FAILED = '0x0003';								// 插件操作失败
    CONST ARGUMENTS_ERROR = "0x0004";									// 参数错误
    CONST XML_ANALYSIS_FAILED = "0x0005";								// XML报文解析失败
    CONST ACTION_ERROR = "0x0006";										// action错误
    CONST XML_REQUEST_ERROR = "0x0007";									// XML返回错误
    CONST XML_OPTID_ERROR = "0x0008";									// XML发送OPTID错误

    CONST DATABASE_LINK_FAILED = '0x0010';      						// 初始化数据库连接对象失败
    CONST DATABASE_DBCONTROL_FAILED = '0x0011';							// 数据库操作失败

    CONST LOGIN_USERID_FORMAT_ERROR = '0x0020';							// 用户名格式错误
    CONST LOGIN_USERID_NOTEXISTS = '0x0021';							// 用户名不存在
    CONST LOGIN_PASSWORD_ERROR = '0x0022';								// 登陆密码错误

    CONST LOGIN_SESSIONID_MAXALLOWED_ERROR = '0x0024';					// 登陆连接操作超过最大连接数

    CONST SESSION_ERROR = "0x0030";										// SESSION错误
    CONST SESSION_UNVALID_OR_NOTEXISTS = "0x0031";						// SESSION失效
}

?>