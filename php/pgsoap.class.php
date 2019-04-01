<?php

header('Content-Type:text/html;charset=utf-8');

/**
 * PHP调用C++ GSoap错误码 操作成功
 **/
define('PGSOAP_SUCCESS', '0x0000');
/**
 * PHP调用C++ GSoap错误码 操作失败
 **/
define('PGSOAP_FAILED', '0x0001');
/**
 * PHP调用C++ GSoap错误码 抛出异常
 **/
define('PGSOAP_THREAD', '0x0003');
/**
 * PHP调用C++ GSoap错误码  参数验证出错
 **/
define('PGSOAP_ERROR_PARAM_NOT_VALID', '0x100A');

/**
 * PHP调用C++ GSoap错误码 Gsoap对象未实例化
 **/
define('PGSOAP_ERROR_GSOAP_NOT_INSTANCE', '0x100B');

include_once('../utility/ecb_des.class.php');

/**
 * @desc PHP调用C++ GSoap功能封装类
 * @author huzw
 * @param
 *      <p>@time 2017.11.20</p>
 *      <p>@remark
 *          - 所有请求响应报文采用ECB模式Des加密
 *      </p>
 **/
class PGSoap
{
    // 版本号
    const VERSION = '1.0.17.1120';
  
    // GSoap地址
    var $G_SOAP_ADDR = null;
    
    // GSoap实例对象
    var $G_SOAP_INSTANCE = null;
    
    // ECB模式Des加密加密密钥
    var $G_DES_KEY = null;
    
    // 是否开启调试功能
    var $G_DEBUG = true;
    
    // 调试输出对象
    var $G_LOG = null;
    
    // 平台用户企业域
    var $G_UEPID = 'system';
    
    // 平台用户名
    var $G_UID = '';
    
    // 全局返回值类型 stdClass（默认） | JSON
    var $G_RV_TYPE = 'stdClass';
    
    // 全局日志标记
    const G_TAG = 'PGSoap';
    
    // 每次日志内容最大长度，超过截断
    const G_MAX_LOGSTR_LEN = 10240; 
    
    /**
     * @desc PGSoap类构造函数
     * @author huzw
     * @param
     *      <p>_GSOAP_ADDR * C++ Gsoap服务地址</p>
     *      <p>_DES_KEY * ECB模式DES加密密钥，一般8个有效字符 </p>
     *      <p>_DEBUG 是否开启调试日志记录，默认true </p>
     *      <p>_LOG 调试日志记录对象 </p>
     **/
    public function __construct($_GSOAP_ADDR, $_DES_KEY = null, $_DEBUG = true, $_LOG = null)
    {
        try {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "PGSoap Service Starting...");
            
            if (empty($_GSOAP_ADDR))
            {
                PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Gsoap address empty.");
                return false;
            }
            
            $this->G_SOAP_ADDR = $_GSOAP_ADDR;
            
            $this->G_SOAP_INSTANCE = new SoapClient($this->G_SOAP_ADDR);
            
            $this->G_DES_KEY = $_DES_KEY;
            
            $this->G_DEBUG = empty($_DEBUG) ? false : true;
            
            $this->G_LOG = empty($_LOG) ? null : $_LOG;
            
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "PGSoap Service Started.");
            
            return PGSOAP_SUCCESS;
            
        } catch(SoapFault $e) {
            
            $this->G_SOAP_INSTANCE = null;
            
            if (is_callable(array('PGSoap', 'Logger')))
            {
                PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Soap fault -> {$e->getMessage()}");
            }
            return PGSOAP_THREAD;
        } catch (Exception $e) {
            $this->G_SOAP_INSTANCE = null;
            
            if (is_callable(array('PGSoap', 'Logger')))
            {
                PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Excep error -> {$e->getMessage()}");
            }
            return PGSOAP_THREAD;
        }
    }
    
    /**
     * @desc 内部日志输出
     * @author huzw
     * @param
     *      <p>F * 文件名，一般传入__FILE__ </p>
     *      <p>L * 代码行数，一般传入__LINE__ </p>
     *      <p>Func * 函数方法名，一般传入__FUNCTION__ </p>
     *      <p>logStr * 日志内容 </p>
     *      <p>level 日志记录级别，默认null</p>
     * @return
     *      PGSOAP_XXX
     **/
    private function Logger($F, $L, $Func, $logStr = "", $level = null)
    {
        try
        {
            if ($this->G_DEBUG)
            {
                if (mb_strlen($logStr) >= PGSoap::G_MAX_LOGSTR_LEN)
                {
                    $logStr = mb_substr($logStr, 0, PGSoap::G_MAX_LOGSTR_LEN).'...[BIGDATA]...';
                }
                
                if ($this->G_LOG && property_exists($this->G_LOG, 'Writer'))
                {
                    $this->G_LOG->Writer(PGSoap::G_TAG, basename($F), $L, $Func, $logStr, $level);
                }
                else
                {
                    $_log_dir = '../Log';
                    
                    if (!file_exists($_log_dir))
                    {
                        mkdir($_log_dir, 0777, true);
                    }
                    
                    $_log_file = $_log_dir.'/'.PGSoap::G_TAG.'.log';
                    
                    $_log_fill_start = false;
                    
                    if (file_exists($_log_file))
                    {
                        if (filesize($_log_file) > 10 * 1024 * 1024)
                        {
                            unlink($_log_file);
                            
                            $_log_fill_start = true;
                        }
                    }
                    else
                    {
                        $_log_fill_start = true;
                    }
                    
                    if ($_log_fill_start)
                    {
                        file_put_contents($_log_file, "[".PGSoap::G_TAG." LOG]\r\n", FILE_APPEND);
                    }
                    
                    $_log_data = sprintf(
                        "[%s][%s][%s][%s]%s\r\n",
                        date('Ymd_His', time()),
                        basename($F), 
                        $L, 
                        $Func, 
                        $logStr
                    );
                    
                    file_put_contents($_log_file, $_log_data, FILE_APPEND);
                }
            }
        
            return PGSOAP_SUCCESS;
        }
        catch (Exception $e)
        {
            return PGSOAP_FAILED;
        }
    }
    
    /**
     * @desc 设置全局认证平台用户信息
     * @author huzw
     * @param
     *      <p>_UID * 平台用户账户</p>
     *      <p>_UEPID 平台用户企业域，默认system</p>
     * @return
     *      PGSOAP_XXX
     **/
    public function SetGlobalValidUser($_UID, $_UEPID = 'system')
    {
        try {
            
            if (!empty($_UID))
            {
                $this->G_UID = $_UID;
            }
            
            if (!empty($_UEPID))
            {
                $this->G_UEPID = $_UEPID;
            }
            
            return PGSOAP_SUCCESS;
            
        } catch (Exception $e) {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Excep error -> {$e->getMessage()}");
            return PGSOAP_FAILED;
        }
    }
    
    /**
     * @desc 内部验证Gsoap合法性
     * @author huzw
     * @return
     *      PGSOAP_XXX
     **/
    private function ValidGSoap()
    {
        try {
            
            if (empty($this->G_SOAP_INSTANCE))
            {
                return PGSOAP_ERROR_GSOAP_NOT_INSTANCE;
            }
            
            return PGSOAP_SUCCESS;
            
        } catch (Exception $e) {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Excep error -> {$e->getMessage()}");
            return PGSOAP_FAILED;
        }
    }
    
    /**
     * @desc 附加整体请求报文
     * @author huzw
     * @param 
     *      <p>[IN] requestXML结构为</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;Msg ...&gt;...&lt;/Msg&gt;</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;Dst Type="" ...&gt;...&lt;/Dst&gt;</p>
     *      <p>[OUT] requestXML结构如下</p> 
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;?xml ...?&gt;</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;Request&gt;</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;Auth UserName="" EPID="" /&gt;</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;Msg ...&gt;...&lt;/Msg&gt;</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;Dst Type="" ...&gt;...&lt;/Dst&gt;</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;/Request&gt;</p>
     *      <p></p>
     * @return
     *      PGSOAP_XXX
     **/
    private function MakeFullRequestXMLData(&$requestXML = '')
    {
        try {

            $xmlTpl = <<<XML
                <Request>
                    <Auth UserName="%s" EPID="%s" />
                    %s
                </Request>
XML;
            $requestXML = sprintf($xmlTpl, $this->G_UID, $this->G_UEPID, $requestXML);
        
            return PGSOAP_SUCCESS;
        
        } catch (Exception $e) {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Excep error -> {$e->getMessage()}");
            return PGSOAP_FAILED;
        }
    }
    
    /**
     * @desc 内部发送Gsoap请求
     * @author huzw
     * @return
     *      PGSOAP_XXX
     **/
    private function SendRequest($requestXML, &$responseXML = '')
    {
        try {
            $ret = $this->ValidGSoap();
            
            if ($ret != PGSOAP_SUCCESS)
            {
                return $ret;
            }
            
            // 加壳
            $this->MakeFullRequestXMLData($requestXML);
            
            $this->Logger(__FILE__, __LINE__, __FUNCTION__, "\$requestXML -> $requestXML");
            
            $request = ECB_Des::encrypt($requestXML, $this->G_DES_KEY);

            $this->Logger(__FILE__, __LINE__, __FUNCTION__, "\$request -> $request");
            
            $rv = $this->G_SOAP_INSTANCE->__call('getRequestXML', array(array('requestXML' => $request)));
            
            $response = empty($rv->responseXML) ? '' : $rv->responseXML;

            $this->Logger(__FILE__, __LINE__, __FUNCTION__, "\$response -> $response");
            
            $responseXML = ECB_Des::decrypt($response, $this->G_DES_KEY);

            $this->Logger(__FILE__, __LINE__, __FUNCTION__, "\$responseXML -> $responseXML");
            
            return PGSOAP_SUCCESS;
        
        } catch (SoapFault $e) {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Soap fault -> {$e->getMessage()}");
            return PGSOAP_FAILED;
        } catch (Exception $e) {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Excep error -> {$e->getMessage()}");
            return PGSOAP_FAILED;
        }
    }
    /**
     * @desc 处理函数响应
     * @author huzw
     * @return
     *      <p>$this->G_RV_TYPE = 'stdClass' PGSoap_ReturnValue</p>  
     *      <p>$this->G_RV_TYPE = 'JSON' json_encode(PGSoap_ReturnValue)</p> 
     **/
    private function Response($rv = PGSOAP_SUCCESS, $response = null)
    {
        try {
        
            $g_RV = new PGSoap_ReturnValue($rv, $response);
            
            switch ($this->G_RV_TYPE)
            {
                case 'JSON':
                    return json_encode($g_RV);
                    break;
                default:
                    return $g_RV;
                    break;
            }
        
        } catch (Exception $e) {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Excep error -> {$e->getMessage()}");
            
            $g_RV = new PGSoap_ReturnValue(PGSOAP_THREAD);
            
            switch ($this->G_RV_TYPE)
            {
                case 'JSON':
                    return json_encode($g_RV);
                    break;
                default:
                    return $g_RV;
                    break;
            }
        }
    }
    
    /**
     * @desc 向平台发送命令万能接口
     * @author huzw
     * @param
     *      <p>requestMsgXML * 请求报文，结构如下</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;?xml ... ?&gt; ！可有可无，不必要带入</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;Msg ...&gt;...&lt;/Msg&gt; ！原请求报文主体</p>
     * @return
     *      PGSoap_ReturnValue { response => $responseXML }
     **/
    public function TransCustomMessage($requestMsgXML)
    {
        try {
            
            if (!empty($requestMsgXML))
            {
                PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "\$requestMsgXML -> $requestMsgXML");

                $requestMsgXML = preg_replace('/\<\?(x|X)(ml|ML|mL|Ml).*\?\>/', '', $requestMsgXML);
                
                $requestXML = <<<XML
                    $requestMsgXML
                    <Dst Type="PF" />
XML;
                
                $ret = $this->SendRequest($requestXML, $responseXML);
                
                if ($ret != PGSOAP_SUCCESS)
                {
                    return $this->Response($ret);
                } 
                
                return $this->Response(null, $responseXML);
            }
            else
            {
                PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "\$requestMsgXML is empty.");
                return $this->Response(PGSOAP_FAILED);
            }
        
        } catch (Exception $e) {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Excep error -> {$e->getMessage()}");
            
            return $this->Response(PGSOAP_THREAD);
        }
    }
    
    /**
     * @desc 向设备发送命令万能接口
     * @author huzw
     * @param
     *      <p>strObjID * 对象标识ID，比如设备PUID</p>
     *      <p>requestMsgXML * 请求报文，结构如下</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;?xml ... ?&gt; ！可有可无，不必要带入</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;Msg ...&gt;...&lt;/Msg&gt; ！原请求报文主体</p>
     * @return
     *      PGSoap_ReturnValue { response => $responseXML }
     **/
    public function TransCommonMessage($strObjID, $requestMsgXML)
    {
        try {
            
            if (!empty($strObjID) && !empty($requestMsgXML))
            {
                PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "\$requestMsgXML -> $requestMsgXML");
    
                $requestMsgXML = preg_replace('/\<\?(x|X)(ml|ML|mL|Ml).*\?\>/', '', $requestMsgXML);
    
                $requestXML = <<<XML
                    $requestMsgXML
                    <Dst Type="DEV" ObjID="{$strObjID}" />
XML;
    
                $ret = $this->SendRequest($requestXML, $responseXML);
    
                if ($ret != PGSOAP_SUCCESS)
                {
                    return $this->Response($ret);
                }
    
                return $this->Response(null, $responseXML);
            }
            else
            {
                PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "\$objID or \$requestMsgXML is empty.");
                return $this->Response(PGSOAP_FAILED);
            }
    
        } catch (Exception $e) {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Excep error -> {$e->getMessage()}");
    
            return $this->Response(PGSOAP_THREAD);
        }
    }
    
    /**
     * @desc 向网元发送命令万能接口
     * @author huzw
     * @param
     *      <p>intNUType * 网元类型，比如CUI网元为5</p>
     *      <p>strNUID * 网元ID，比如CUI网元为005000100000000001</p>
     *      <p>requestMsgXML * 请求报文，结构如下</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;?xml ... ?&gt; ！可有可无，不必要带入</p>
     *      <p>&nbsp;&nbsp;&nbsp;&nbsp;&lt;Msg ...&gt;...&lt;/Msg&gt; ！原请求报文主体</p>
     * @return
     *      PGSoap_ReturnValue { response => $responseXML }
     **/
    public function TransNUCommonMessage($intNUType, $strNUID, $requestMsgXML)
    {
        try {
    
            if (isset($intNUType) && !empty($strNUID) && !empty($requestMsgXML))
            {
                PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "\$requestMsgXML -> $requestMsgXML");
    
                $requestMsgXML = preg_replace('/\<\?(x|X)(ml|ML|mL|Ml).*\?\>/', '', $requestMsgXML);
    
                $requestXML = <<<XML
                    $requestMsgXML
                    <Dst Type="NU" NUType="{$intNUType}" NUID="{$strNUID}" />
XML;
    
                $ret = $this->SendRequest($requestXML, $responseXML);

                if ($ret != PGSOAP_SUCCESS)
                {
                    return $this->Response($ret);
                }

                return $this->Response(null, $responseXML);
            }
            else
            {
                PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "\$intNUType or \$strNUID or \$requestMsgXML is empty.");
                return $this->Response(PGSOAP_FAILED);
            }
    
        } catch (Exception $e) {
            PGSoap::Logger(__FILE__, __LINE__, __FUNCTION__, "Excep error -> {$e->getMessage()}");
    
            return $this->Response(PGSOAP_THREAD);
        }
    }
    
}

/**
 * @desc PGSoap返回值对象类
 * @author huzw
 * @return
 *      <p>rv PGSOAP_XXX</p>
 *      <p>response 具体值</p>
 **/
class PGSoap_ReturnValue
{
    var $rv = PGSOAP_SUCCESS;
    var $response = null;
    
    public function __construct($rv = PGSOAP_SUCCESS, $response = null) 
    {
        $this->rv = empty($rv) ? PGSOAP_SUCCESS : $rv;
        $this->response = empty($response) ? null : $response;
    }
}

?>