<?php
/* ------ Power By Crearo. ------ */
/// 默认时区
ini_set ( "date.timezone", "Asia/Shanghai" );
header ( 'content-Type:text/html;charset=utf-8' );

include_once(dirname(dirname(__FILE__)).'/utility/global.var.php');
include_once(PROJECT_DOMAIN_ROOT."/utility/validator.class.php");
include_once(PROJECT_DOMAIN_ROOT."/php/action.class.php");
include_once(PROJECT_DOMAIN_ROOT."/php/errorcode.class.php");
include_once(PROJECT_DOMAIN_ROOT."/php/myobject.class.php");

GLogger(__FILE__, __LINE__, __FUNCTION__, "DataExchange Started...");

define("MAX_LOGSTR_LEN",102400);
define("VERSION",'20190311.001');
define("SEND_NU_TYPE","71");
define("SEND_NU_ID","071000100000000001");

/// 创建返回值对象别名
$g_RV = $g_ReturnValue;

/// 快捷响应函数
function g_Response($errorCode = ErrorCode::SUCCESS, $_action = null, $_content = null)
{
    global $g_Log, $g_Utility, $g_RV, $action;

    $g_RV->errorCode = !isset($errorCode) ? ErrorCode::SUCCESS : $errorCode;
    $g_RV->time = time();
    $g_RV->action = isset($_action) ? $_action : $action;
    $g_RV->content = $_content;

    $g_Resp = array(
        "response" => $g_RV
    );
    
    if (strcasecmp(PHP_VERSION, '5.4.0') >= 0)
    {
        $g_RV_JSON = json_encode($g_Resp, JSON_UNESCAPED_UNICODE);
    }
    else
    {
        $g_RV_JSON = json_encode($g_Resp);
         
        if (defined('RETURN_JSON_CODETYPE') && RETURN_JSON_CODETYPE == 'UTF-8')
        {
            $g_RV_JSON = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function( '$matches', 'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'), $g_RV_JSON);
        }
    }

    GLogger(__FILE__, __LINE__, __FUNCTION__, "DataExchange Ended, Responsing($g_RV_JSON)");
    $g_Utility->Response($g_RV_JSON);
    exit();
}
// 页面错误响应函数
function g_CustomError($errno, $errstr, $errfile, $errline)
{
    global $action;
    g_Response(ErrorCode::FAILED, $action, array("errno"=>$errno,"errstr"=>$errstr,"errfile"=>$errfile,"errline"=>$errline));
    exit();
}

/// 连接数据库
$db_link = null;
if (array_key_exists('g_CR_Utility', $GLOBALS) && $GLOBALS['g_CR_Utility']['g_CR_DBLink'])
{
    $db_link = $GLOBALS['g_CR_Utility']['g_CR_DBLink'];

    $g_Log->Writer(G_TAG, __FILE__, __LINE__, __FUNCTION__, "g_CR_Utility(".print_r($GLOBALS['g_CR_Utility'], true).")");
}
if (!isset($db_link) || !$db_link->connect)
{
    GLogger(__FILE__, __LINE__, __FUNCTION__, "创建连接TGAccess数据库实例对象失败");

    g_Response(ErrorCode::DB_MYSQL_LINK_FAILED);
    exit();
}

/// 连接ORACLE

// 日志输出
function GLogger($F, $L, $Func, $logStr = "", $level = null)
{
    try {
		global $g_Log;
		if (G_DEBUG) {
			if (mb_strlen($logStr) >= MAX_LOGSTR_LEN)
			{
				$logStr = mb_substr($logStr, 0, MAX_LOGSTR_LEN).'...[BIGDATA]...';
			}
			
			$g_Log -> Writer(G_TAG, $F, $L, $Func, $logStr, $level);
			
		}
		return true;
	} catch (Exception $e) {
		return false;
	}
}

$reqJson = "";

if (array_key_exists('QUERY_STRING', $_SERVER))
{
    $reqJson = trim(rawurldecode($_SERVER['QUERY_STRING']));
}

if (empty($reqJson))
{
    $reqJson = trim(rawurldecode(file_get_contents("php://input")));
}

if (!empty($reqJson))
{
    GLogger(__FILE__, __LINE__, __FUNCTION__, "Request message body -> {$reqJson}");
    
    // 解析请求消息体，转为stdClass对象
    $de_reqJson = json_decode($reqJson);
    if (is_null($de_reqJson) 
       || (strcasecmp(PHP_VERSION, '5.3.0') >= 0 && json_last_error() != JSON_ERROR_NONE)
       || empty($de_reqJson->request)
       || empty($de_reqJson->request->action) ) 
    {
        $g_Log->Writer(G_TAG, __FILE__, __LINE__, __FUNCTION__, "Request message body unlegal.");
        
        g_Response(ErrorCode::REQUEST_MSG_UNLEGAL);
        exit();
    }
    
    GLogger(__FILE__, __LINE__, __FUNCTION__, "Parsed request message body  -> ".print_r($de_reqJson, true));
    
    $request = $de_reqJson->request;
    
    if (empty($request->action))
    {
        Glogger(__FILE__, __LINE__, __FUNCTION__, "Request Parameters Error: Not Found 'action' property.");
        
        g_Response(ErrorCode::PARAM_ACTION_NOTEXISTS);
        exit();
    }
    
    if (!property_exists($request, 'content'))
    {
        Glogger(__FILE__, __LINE__, __FUNCTION__, "Request Parameters Error: Not Found 'content' property.");
        
        g_Response(ErrorCode::PARAM_CONTENT_NOTEXISTS);
        exit();
    }
}
else
{
    GLogger(__FILE__, __LINE__, __FUNCTION__, "请求消息体为空");
    
    g_Response(ErrorCode::REQUEST_MSG_NULL);
    exit();
}

// 解析结果值，转换为对象
function RowFetchObject (&$row, $result, $db_link)
{
    if (!isset($db_link))
    {
        global $db_link;
    }

    $row = $db_link->FetchObject($result);
    if ($row)
    {
        return true;
    }
    else
    {
        $row = NULL;
        return false;
    }
}
// 解析结果值，转换为数组
function RowFetchArray (&$row, $result, $db_link)
{
    if (!isset($db_link))
    {
        global $db_link;
    }

    $row = $db_link->FetchArray($result);
    if ($row)
    {
        return true;
    }
    else
    {
        $row = NULL;
        return false;
    }
}

// 创建新的数据库连接
function CreateNewDBLink($dbInfo, &$db_newlink = null)
{
    try {

        global $g_Utility;

        // 创建新的数据库连接
        $db_newlink = $g_Utility->GetCR_DBLink
        (
            $dbInfo->IP,
			$dbInfo->DB_Port,
			$dbInfo->DB_UID,
			$dbInfo->DB_PSW,
			(!empty($dbInfo->DB_Name) ? $dbInfo->DB_Name : 'mysql'),
            $_SESSION["DB"]["Character"]
            );

        if (isset($db_newlink) && $db_newlink->connect != false)
        {
            return true;
        }
        else
        {
            try {
                if (defined('Glogger'))
                {
                    GLogger(__FILE__, __LINE__, __FUNCTION__, "create new dblink error -> ".$db_newlink->GetError("string"));
                }
            } catch (Exception $e) {
            }
			
			return false;
        }

        return true;

    } catch (Exception $e) {
        return false;
    }
}

// 商汤请求
function HTTP_CURL_CallInterfaceCommon_ST($URL, $type, $params, &$httpCode = 200) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: text/html, application/xhtml+xml, image/jxr, */*', 'Content-Type: application/x-www-form-urlencoded', 'charset=utf-8', 'Accept-Encoding: gzip, deflate'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    switch ($type) {
        case 'GET' :
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            break;
        case 'POST' :
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            break;
        case 'PUT' :
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            break;
        case 'DELETE' :
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            break;
    }

    $file_contents = curl_exec($ch);
    //获得返回值

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    return $file_contents;
}

// 图麟科技算法通用http请求接口
// d47bf894-4b3c-4766-bd72-91eb1c1aaf0e
//
function HTTP_CURL_CallInterfaceCommon_TL($appkey, $appsecret, $URL, $type, $params, &$httpCode = 200)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: text/html, application/xhtml+xml, image/jxr, */*',
        'Content-Type: multipart/form-data',
        'App-Key:'.$appkey,
        'App-Secret:'.$appsecret,
        'charset=utf-8',
        'Accept-Encoding: gzip, deflate'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    switch ($type) {
        case 'GET' :
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            break;
        case 'POST' :
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            break;
        case 'PUT' :
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            break;
        case 'DELETE' :
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            break;
    }

    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    return $file_contents;
}

/**
 * 获取图片的Base64编码(不支持url)
 * @date 2017-02-20 19:41:22
 *
 * @param $img_file 传入本地图片地址
 *
 * @return string
 */
function imgToBase64($img_file) {

    $img_base64 = '';
    if (file_exists($img_file)) {
        $app_img_file = $img_file; // 图片路径
        $img_info = getimagesize($app_img_file); // 取得图片的大小，类型等

        $fp = fopen($app_img_file, "r"); // 图片是否可读权限

        if ($fp) {
            $filesize = filesize($app_img_file);
            $content = fread($fp, $filesize);
            $file_content = chunk_split(base64_encode($content)); // base64编码
            switch ($img_info[2]) {           //判读图片类型
                case 1: $img_type = "gif";
                    break;
                case 2: $img_type = "jpg";
                    break;
                case 3: $img_type = "png";
                    break;
            }

            //$img_base64 = 'data:image/' . $img_type . ';base64,' . $file_content;//合成图片的base64编码
            $img_base64 = $file_content;
        }
        fclose($fp);
    }

    return $img_base64; //返回图片的base64
}


?>