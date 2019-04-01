<?php
/* ------ Power By Crearo. ------ */
ob_start ();
session_set_cookie_params (5 * 24 * 60 * 60);
session_start ();
/// 默认时区
ini_set ( "date.timezone", "Asia/Shanghai" );
header ( 'content-Type:text/html;charset=utf-8' );

include_once('entrydoor.php');
include_once('wssdk.class.php');

GLogger(__FILE__, __LINE__, __FUNCTION__, "引用地址=".WSSdkSource_PLATSOURCEPATH);

switch ($request->action)
{
    // 登录
    case Action::Login:
		$arr_RV = Login($request->content);
        break;
    
    // 检测是否登录
    case Action::CheckLogin:
        $arr_RV = CheckLogin($request->content);
        break;

    // 保活
    case Action::KeepAlive:
        $arr_RV = KeepAlive($request->content);
        break;

    // 登出
    case Action::Logout:
        $arr_RV = Logout($request->content);
        break;

    // 获取IA服务器
    case Action::QueryIAServerInfo:
		$arr_RV = QueryIAServerInfo($request->content);
        break;

    // 添加IA服务器
    case Action::AddIAServerInfo:
		$arr_RV = AddIAServerInfo($request->content);
        break;
    
    // 删除IA服务器
    case Action::DeleteIAServerInfo:
        $arr_RV = DeleteIAServerInfo($request->content);
        break;

    // 获取人脸集人脸
    case Action::QueryFaceInfo:
        $arr_RV = QueryFaceInfo($request->content);
        break;

    // 获取所有人脸
    case Action::QueryAllFaceInfo:
        $arr_RV = QueryAllFaceInfo($request->content);
        break;

    // 获取NU信息
    case Action::GetNuInfo:
        $arr_RV = GetNuInfo($request->content);
        break;

    // 添加人脸
    case Action::AddFace:
        $arr_RV = AddFace($request->content);
        break;


    // 删除人脸
    case Action::DeleteFace:
        $arr_RV = DeleteFace($request->content);
        break;

    // 添加人脸基本信息
    case Action::AddFaceBaseInfo:
        $arr_RV = AddFaceBaseInfo($request->content);
        break;


    // 修改人脸基本信息
    case Action::ModifyFaceBaseInfo:
		$arr_RV = ModifyFaceBaseInfo($request->content);
        break;

    // 删除人脸基本信息
    case Action::DeleteFaceBaseInfo:
        $arr_RV = DeleteFaceBaseInfo($request->content);
        break;

    // 添加人脸补录信息
    case Action::AddFaceAdditionalInfo:
        $arr_RV = AddFaceAdditionalInfo($request->content);
        break;

    // 修改人脸补录信息
    case Action::ModifyFaceAdditionalInfo:
        $arr_RV = ModifyFaceAdditionalInfo($request->content);
        break;

    // 删除人脸补录信息
    case Action::DeleteFaceAdditionalInfo:
        $arr_RV = DeleteFaceAdditionalInfo($request->content);
        break;

    // 添加上传附件
    case Action::AddUploadFiles:
        $arr_RV = AddUploadFiles($request->content);
        break;

    // 删除文件上传
    case Action::DeleteUploadFiles:
        $arr_RV = DeleteUploadFiles($request->content);
        break;

    // 检查属性
    case Action::CheckAttributes:
        $arr_RV = CheckAttributes($request->content);
        break;

    // 获取组信息
    case Action::QueryGroupInfo:
        $arr_RV = QueryGroupInfo($request->content);
        break;

    // 添加组信息
    case Action::AddGroupInfo:
        $arr_RV = AddGroupInfo($request->content);
        break;

    // 修改组信息
    case Action::ModifyGroupInfo:
        $arr_RV = ModifyGroupInfo($request->content);
        break;
    // 删除组信息
    case Action::DeleteGroupInfo:
        $arr_RV = DeleteGroupInfo($request->content);
        break;

    // 获取车牌信息
    case Action::QueryCarIDInfo:
        $arr_RV = QueryCarIDInfo($request->content);
        break;

    // 添加车牌信息
    case Action::AddCarIDInfo:
        $arr_RV = AddCarIDInfo($request->content);
        break;

    // 修改车牌信息
    case Action::ModifyCarIDInfo:
        $arr_RV = ModifyCarIDInfo($request->content);
        break;

    // 删除车牌信息
    case Action::DeleteCarIDInfo:
        $arr_RV = DeleteCarIDInfo($request->content);
        break;

    // 获取组下资源信息
    case Action::QueryGroupResourceInfo:
        $arr_RV = QueryGroupResourceInfo($request->content);
        break;

    // 添加组下资源信息
    case Action::AddGroupResourceInfo:
        $arr_RV = AddGroupResourceInfo($request->content);
        break;

    // 删除组下资源
    case Action::DeleteGroupResourceInfo:
        $arr_RV = DeleteGroupResourceInfo($request->content);
        break;

    // 查询人脸识别历史记录
    case Action::QueryFaceRecognitionHistroyInfo:
        $arr_RV = QueryFaceRecognitionHistroyInfo($request->content);
        break;
        
     // 查询人脸检测历史记录
    case Action::QueryFaceDetectHistoryInfo:
        $arr_RV = QueryFaceDetectHistoryInfo($request->content);
        break;

    // 查询车牌历史记录
    case Action::QueryPlateNumberHistroyInfo:
        $arr_RV = QueryPlateNumberHistroyInfo($request->content);
        break;


    default:
        GLogger(__FILE__, __LINE__, __FUNCTION__, "请求action动作未知");
        g_Response(ErrorCode::PARAM_ACTION_UNKNOWN, Action::UnknownAction);
        exit();
        break;
}

if ($arr_RV['errorCode'] == ErrorCode::DB_MYSQL_OPERATE_FAILED)
{
    try {

        $errstr = $db_link->GetError("string");

        GLogger(__FILE__, __LINE__, __FUNCTION__, "数据库操作失败，详细错误：$errstr");

    } catch (Exception $e) {
    }
}

g_Response($arr_RV['errorCode'], $request->action, $arr_RV['content']);




/**
 * 管理员用户登录
 * @param
 *          userID
 *          password
 * @author
 *          zenghx
 * @return array
 */
function Login($param){
    try{
		global $db_link;
		$aRet = array(
		    'errorCode' => ErrorCode::SUCCESS
        );

		if(!isset($param->userID))
		{
		    $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
		    return $aRet;
        }

        if(!isset($param->password))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $psw = md5($param->password);

        $query = "SELECT COUNT(*) as Count FROM CNRMS_CES_v8.`userInfo` WHERE Identity='$param->userID' AND Password='$psw'";

		GLogger(__FILE__, __LINE__, __FUNCTION__, "$query");
		$result = $db_link->Query($query);

		if (!$result)
		{
			$aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
			return $aRet;
        }
        else{
            $content = array();
            while(RowFetchObject($row, $result, $db_link)){
                $count = $row->Count;
            }
            if($count > 0){
                if(!__checkSession())
                {
                    $_SESSION['IA']['USERID'] = $param->userID;
                    $_SESSION['IA']['PASSWORD'] = $param->password;
                    $_SESSION['IA']['TIME'] = time() + 300;
                }
                $aRet['content'] = array();
            }
            else{
                $aRet['errorCode'] = ErrorCode::LOGIN_USERID_NOTEXISTS;
                return $aRet;
            }
        }

		return $aRet;
	}catch (Exception $e) {
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 检测是否登陆
 * @author zenghx
 * @date 2018年8月21日
 * @param
 */
function CheckLogin($param){
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['content'] = '登录断开连接';
            return $aRet;
        }
        else{
            $content = array(
                'userID' => $_SESSION['IA']['USERID'],
                'password'=>$_SESSION['IA']['PASSWORD'],
                'keepAliveTime'=> $_SESSION['IA']['TIME']
            );
        }
        $aRet['content'] = $content;

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 保活
 * @author zenghx
 * @date 2018年8月21日
 * @param
 *      userID          用户名
 *      password        密码
 */
function KeepAlive($param){
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['content'] = '登录断开连接';
            return $aRet;
        }

        $interval = 90000;

        $_SESSION['IA']['TIME'] = time() + $interval;

        $aRet['content'] =array();
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 退出系统
 * @author zenghx
 * @date 2018年8月21日
 * @param null
 */
function Logout($param){
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        session_unset("IA");

        $aRet['content'] =array();
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

function QueryIAServerInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        /*if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            return $aRet;
        }*/

        if(!isset($param->dbIndex)){
            $param->dbIndex = '';
        }
        
        if(!isset($param->IAType)){
            $param->IAType = '';
		}

        $DstXML = <<<XML
		<DstRes Type="SC" Idx="0" OptID="CTL_IA_QueryFaceGroup">
			<Param IAType="{$param->IAType}" Index="{$param->dbIndex}" ></Param>
		</DstRes>
XML;
		GLogger(__FILE__, __LINE__, __FUNCTION__, "XML：$DstXML");

		$param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);
		
		$param = json_decode(json_encode($param));
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));

		$ret = WSSdkSource::TransNUCommonMessage($param);
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($ret,true));
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] = ErrorCode::XML_REQUEST_ERROR;
            return $aRet;
        }
        else{
            $XMLContent = $ret['xml_content'];

            $xmldoc = new DOMDocument();
            $xmldoc->loadXML($XMLContent);

            if(!$xmldoc->loadXML($XMLContent)){
                $aRet['errorCode'] = ErrorCode::XML_ANALYSIS_FAILED;
				GLogger(__FILE__, __LINE__, __FUNCTION__, "XML读取失败,XML=".$XMLContent);
				return $aRet;
            }

            $xpath = new DOMXPath($xmldoc);
            $cmd = $xpath->query("/Msg/Cmd")->item(0);
			$nuErrorCode = $cmd->getAttribute("NUErrorCode");
            GLogger(__FILE__, __LINE__, __FUNCTION__, 'NUErrorCode='.$nuErrorCode);
            
            if($nuErrorCode != 0)
			{
				$aRet['errorCode'] = ErrorCode::XML_RESPONSE_NU_ERROR;
				GLogger(__FILE__, __LINE__, __FUNCTION__, 'NU错误,ErrorCode='.$nuErrorCode);
				return $aRet;
			}

            $GroupInfoList = $cmd->getElementsByTagName("GroupInfo");

            $GroupInfoArr = array();
            if(count($GroupInfoList) > 0){
                foreach($GroupInfoList as $row){
                    $GroupInfo = new FaceDBStructInfo();
                    $GroupInfo->index = $row->getAttribute('Index');
                    $iaType = $row->getAttribute('IAType');
                    $GroupInfo->iaType = $iaType;
                    if(intval($iaType) == 0){
                        $GroupInfo->name = $row->getAttribute('Name');
                        $GroupInfo->description = $row->getAttribute('Description');
                    }else{
                        $GroupInfo->name = $row->getAttribute('Description');
                        $GroupInfo->description = $row->getAttribute('Name');
                    }
                    $GroupInfo->ip = $row->getAttribute('IP');
                    $GroupInfo->port = $row->getAttribute('Port');
                    $GroupInfo->size = $row->getAttribute('Size');
                    $GroupInfo->count = $row->getAttribute('Count');
                    $GroupInfo->appKey = $row->getAttribute('APPKey');
                    $GroupInfo->appSecret = $row->getAttribute('APPSecret');
                    $GroupInfo->remark = $row->getAttribute('Remark');
                    $GroupInfo->deleteFlag = $row->getAttribute('DeleteFlag');

                    array_push($GroupInfoArr, $GroupInfo);
                }
            }
            $aRet['content'] = $GroupInfoArr;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加IA服务器（算法集合）
 * @param
 *          iaType          算法库类型
 *          dbname          算法库名称
 *          ip              算法服务器目标
 *          port            算法服务器端口
 *          appkey          图麟连接密钥
 *          appsecret       图麟连接密钥
 *          description     描述
 *          remark          备注
 * @author
 *          zenghx
 * @return array
 */
function AddIAServerInfo($param){
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->iaType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->dbname))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->ip))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->port))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(intval($param->iaType) == 1)
        {
            if(!isset($param->appkey))
            {
                $param->appkey = 'd47bf894-4b3c-4766-bd72-91eb1c1aaf0e';
            }

            if(!isset($param->appsecret))
            {
                $param->appsecret = 'c39fe532-7cd4-4f6c-8a69-2b557ecfe202';
            }
        }

        if(!isset($param->description))
        {
            $param->description = '';
        }

        if(!isset($param->remark))
        {
            $param->remark = '';
        }

        if(!isset($param->size))
        {
            $param->size = 10000;
        }

        if(!isset($param->count))
        {
            $param->count = 0;
        }

        $DstXML = <<<XML
		<DstRes Type="SC" Idx="0" OptID="CTL_IA_CreateFaceGroup">
			<Param FaceGroupName="{$param->dbname}" IP="{$param->ip}" Port="{$param->port}" IAType="{$param->iaType}" APPKey="{$param->appkey}" APPSecret="{$param->appsecret}" Size="{$param->size}" Count="{$param->count}" ></Param>
		</DstRes>
XML;
		GLogger(__FILE__, __LINE__, __FUNCTION__, "XML：$DstXML");

		$param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);
		
		$param = json_decode(json_encode($param));
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));

		$ret = WSSdkSource::TransNUCommonMessage($param);
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($ret,true));
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] = ErrorCode::XML_REQUEST_ERROR;
            return $aRet;
        }

        
        $aRet['content'] =array();
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}


function DeleteIAServerInfo($param)
{
    try{
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );
        if(!isset($param->dbname)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;  
        }

        if(!isset($param->desc)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;  
        }
        
        if(!isset($param->ip)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;  
        }

        if(!isset($param->port)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;  
        }

        if(!isset($param->iaType)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;  
        }

        if(intval($param->iaType) == 1)
        {
            if(!isset($param->appkey))
            {
                $param->appkey = 'd47bf894-4b3c-4766-bd72-91eb1c1aaf0e';
            }

            if(!isset($param->appsecret))
            {
                $param->appsecret = 'c39fe532-7cd4-4f6c-8a69-2b557ecfe202';
            }
        }

        $DstXML = <<<XML
		<DstRes Type="SC" Idx="0" OptID="CTL_IA_DeleteFaceGroup">
			<Param FaceGroupName="{$param->dbname}" FaceGroupToken="{$param->desc}"  IP="{$param->ip}" Port="{$param->port}" IAType="{$param->iaType}" APPKey="{$param->appkey}" APPSecret="{$param->appsecret}" ></Param>
		</DstRes>
XML;
		GLogger(__FILE__, __LINE__, __FUNCTION__, "XML：$DstXML");

		$param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);
		
		$param = json_decode(json_encode($param));
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));

		$ret = WSSdkSource::TransNUCommonMessage($param);
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($ret,true));
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] = ErrorCode::XML_REQUEST_ERROR;
            return $aRet;
        }

    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}


/**
 * 获取人脸集人脸
 * @param
 *          dbIndex         算法库索引（必传）
 * @author
 *          zenghx
 * @return array
 */
function QueryFaceInfo($param)
{
    try{
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->DBIndex)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;  
        }

        $DstXML = <<<XML
		<DstRes Type="SC" Idx="0" OptID="CTL_IA_GetFaceInfo_WEB">
			<Param DBIndex="{$param->DBIndex}"></Param>
		</DstRes>
XML;
		GLogger(__FILE__, __LINE__, __FUNCTION__, "XML：$DstXML");

		$param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);
		
		$param = json_decode(json_encode($param));
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));

		$ret = WSSdkSource::TransNUCommonMessage($param);
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($ret,true));
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] = ErrorCode::XML_REQUEST_ERROR;
            return $aRet;
        }
        else{
            $XMLContent = $ret['xml_content'];

            $XMLContent = str_replace(array("\r\n\r\n\r\n","\r\n\r\n", "\r\n","\r", "\n"), "", $XMLContent);

            $xmldoc = new DOMDocument();
            $XMLContent = str_replace(PHP_EOL, '', $XMLContent);
            $xmldoc->loadXML($XMLContent);

            if(!$xmldoc->loadXML($XMLContent)){
                $aRet['errorCode'] = ErrorCode::XML_ANALYSIS_FAILED;
				GLogger(__FILE__, __LINE__, __FUNCTION__, "XML读取失败,XML=".$XMLContent);
				return $aRet;
            }

            $xpath = new DOMXPath($xmldoc);
            $cmd = $xpath->query("/Msg/Cmd")->item(0);
			$nuErrorCode = $cmd->getAttribute("NUErrorCode");
            GLogger(__FILE__, __LINE__, __FUNCTION__, 'NUErrorCode='.$nuErrorCode);
            
            if($nuErrorCode != 0)
			{
				$aRet['errorCode'] = ErrorCode::XML_RESPONSE_NU_ERROR;
				GLogger(__FILE__, __LINE__, __FUNCTION__, 'NU错误,ErrorCode='.$nuErrorCode);
				return $aRet;
			}

            $ManInfoList = $cmd->getElementsByTagName("QueryManInfoWeb");

            $previewURL = 'http://'.$_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT']."/nmi/php/nmi_ia_server.php";
            
            $ManInfoArr = array();
            if(count($ManInfoList) > 0){
                foreach($ManInfoList as $row){                    
                    $dbManInfo = new DBManStructInfo();
                    $dbManInfo->db_index  = $row->getAttribute('Index');
                    $dbManInfo->db_name  = $row->getAttribute('DBName');
                    $dbManInfo->db_ip  = $row->getAttribute('IP');
                    $dbManInfo->db_port  = $row->getAttribute('Port');
                    $dbManInfo->db_size  = $row->getAttribute('Size');
                    $dbManInfo->db_count  = $row->getAttribute('Count');
                    $dbManInfo->db_type  = $row->getAttribute('IAType');
                    $dbManInfo->db_key  = $row->getAttribute('APPKey');
                    $dbManInfo->db_secret  = $row->getAttribute('APPSecret');
                    $dbManInfo->db_description  = $row->getAttribute('Description');
                    $dbManInfo->db_remark  = $row->getAttribute('Remark');
                    $NUID = $row->getAttribute('NUID');
                    $dbManInfo->NUID = $NUID;
                    $dbManInfo->NUType = $row->getAttribute('NUType');
                    $dbManInfo->user_manInfoEx_Index = $row->getAttribute('ManInfoEx_Index');
                    $dbManInfo->user_index = $row->getAttribute('UserIndex');
                    $dbManInfo->user_number  = $row->getAttribute('Number');
                    $dbManInfo->user_function  = $row->getAttribute('Function');
                    $dbManInfo->user_projectDepartment  = $row->getAttribute('ProjectDepartment');
                    $dbManInfo->user_name  = $row->getAttribute('UserName');
                    $dbManInfo->user_sex  = $row->getAttribute('Sex');
                    $dbManInfo->user_idNumber  = $row->getAttribute('IDNumber');
                    $dbManInfo->user_groupName  = $row->getAttribute('GroupName');
                    $dbManInfo->user_groupIndex  = $row->getAttribute('GroupIndex');
                    $dbManInfo->user_company  = $row->getAttribute('Company');
                    $dbManInfo->user_jobNumber  = $row->getAttribute('JobNumber');
                    $dbManInfo->user_subcontractTeam  = $row->getAttribute('SubcontractTeam');
                    $dbManInfo->user_dept  = $row->getAttribute('Dept');
                    $dbManInfo->user_birthDate  = $row->getAttribute('BirthDate');
                    $dbManInfo->user_personalFileIndex  = $row->getAttribute('PersonalFile_Index');
                    $dbManInfo->user_personalFileName  = $row->getAttribute('PersonalFileName');
                    $dbManInfo->user_PersonalPath  = $row->getAttribute('PersonalPath');
                    $dbManInfo->user_ID_A_Index  = $row->getAttribute('ID_A_Index');
                    $dbManInfo->user_ID_A_FileName  = $row->getAttribute('ID_A_FileName');
                    $dbManInfo->user_ID_A_Path  = $row->getAttribute('ID_A_Path');
                    $dbManInfo->user_ID_B_Index  = $row->getAttribute('ID_B_Index');
                    $dbManInfo->user_ID_B_FileName  = $row->getAttribute('ID_B_FileName');
                    $dbManInfo->user_ID_B_Path  = $row->getAttribute('ID_B_Path');
                    $dbManInfo->user_paperPhoto_Index  = $row->getAttribute('PaperPhoto_Index');
                    $dbManInfo->user_paperPhotoFileName  = $row->getAttribute('PaperPhotoFileName');
                    $dbManInfo->user_paperPhotoPath  = $row->getAttribute('PaperPhotoPath');
                    $dbManInfo->user_examResult  = $row->getAttribute('ExamResult');
                    $dbManInfo->user_medicalReportIndex  = $row->getAttribute('MedicalReport_Index');
                    $dbManInfo->user_medicalReportFileName  = $row->getAttribute('MedicalReportFileName');
                    $dbManInfo->user_medicalReportPath  = $row->getAttribute('MedicalReportPath');
                    $dbManInfo->user_specialWorkPermit_Index  = $row->getAttribute('SpecialWorkPermit_Index');
                    $dbManInfo->user_specialWorkPermitFileName  = $row->getAttribute('SpecialWorkPermitFileName');
                    $dbManInfo->user_specialWorkPermitPath  = $row->getAttribute('SpecialWorkPermitPath');
                    $dbManInfo->user_auditStatus  = $row->getAttribute('AuditStatus');
                    $dbManInfo->user_auditBeginTime  = $row->getAttribute('AuditBeginTime');
                    $dbManInfo->user_auditEndTime  = $row->getAttribute('AuditEndTime');
                    $dbManInfo->user_auditor  = $row->getAttribute('Auditor');
                    $dbManInfo->user_auditTime  = $row->getAttribute('AuditTime');
                    $dbManInfo->user_IllegalInformation  = $row->getAttribute('IllegalInformation');
                    $dbManInfo->user_sourcePicturePath = $row->getAttribute('sourcePicturePath');
                    $dbManInfo->user_sourcePicturePath_sl = $row->getAttribute('sourcePicturePath_sl');

                    $dbManInfo->HttpPicUrl_Source = $previewURL."?NUID=".$NUID."&fname=".$row->getAttribute('sourcePicturePath');
                    $dbManInfo->HttpPicUrl_Preview = $previewURL."?NUID=".$NUID."&fname=".$row->getAttribute('sourcePicturePath_sl');

                    // Name重复了
                    array_push($ManInfoArr, $dbManInfo);
                }
            }
            $aRet['content'] = $ManInfoArr;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

function QueryAllFaceInfo($param){
    try{
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->count)){
            $param->count = 0;
        }

        if(!isset($param->offset)){
            $param->offset = 1000;
        }

        $DstXML = <<<XML
		<DstRes Type="SC" Idx="0" OptID="CTL_IA_GetFaceInfos_WEB">
			<Param Offset="{$param->offset}" Cnt="{$param->count}"></Param>
		</DstRes>
XML;
		GLogger(__FILE__, __LINE__, __FUNCTION__, "XML：$DstXML");

		$param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);
		
		$param = json_decode(json_encode($param));
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));

		$ret = WSSdkSource::TransNUCommonMessage($param);
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($ret,true));
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] = ErrorCode::XML_REQUEST_ERROR;
            return $aRet;
        }
        else{
            $XMLContent = $ret['xml_content'];
            $XMLContent = str_replace(PHP_EOL, '', $XMLContent);
            
            $xmldoc = new DOMDocument();
            $xmldoc->loadXML($XMLContent);

            if(!$xmldoc->loadXML($XMLContent)){
                $aRet['errorCode'] = ErrorCode::XML_ANALYSIS_FAILED;
				GLogger(__FILE__, __LINE__, __FUNCTION__, "XML读取失败,XML=".$XMLContent);
				return $aRet;
            }

            $xpath = new DOMXPath($xmldoc);
            $cmd = $xpath->query("/Msg/Cmd")->item(0);
			$nuErrorCode = $cmd->getAttribute("NUErrorCode");
            GLogger(__FILE__, __LINE__, __FUNCTION__, 'NUErrorCode='.$nuErrorCode);
            
            if($nuErrorCode != 0)
			{
				$aRet['errorCode'] = ErrorCode::XML_RESPONSE_NU_ERROR;
				GLogger(__FILE__, __LINE__, __FUNCTION__, 'NU错误,ErrorCode='.$nuErrorCode);
				return $aRet;
			}

            $ManInfoList = $cmd->getElementsByTagName("QueryAllManInfoWeb");

            $ManInfoArr = array();
            if(count($ManInfoList) > 0){
                foreach($ManInfoList as $row){                    
                    $dbManInfo = new DBManStructInfo();
                    $dbManInfo->db_index  = $row->getAttribute('Index');
                    $dbManInfo->db_name  = $row->getAttribute('DBName');
                    $dbManInfo->db_ip  = $row->getAttribute('IP');
                    $dbManInfo->db_port  = $row->getAttribute('Port');
                    $dbManInfo->db_size  = $row->getAttribute('Size');
                    $dbManInfo->db_count  = $row->getAttribute('Count');
                    $dbManInfo->db_type  = $row->getAttribute('IAType');
                    $dbManInfo->db_key  = $row->getAttribute('APPKey');
                    $dbManInfo->db_secret  = $row->getAttribute('APPSecret');
                    $dbManInfo->db_description  = $row->getAttribute('Description');
                    $dbManInfo->db_remark  = $row->getAttribute('Remark');
                    $dbManInfo->NUID = $row->getAttribute('NUID');
                    $dbManInfo->NUType = $row->getAttribute('NUType');
                    $dbManInfo->user_manInfoEx_Index = $row->getAttribute('ManInfoEx_Index');
                    $dbManInfo->user_index = $row->getAttribute('UserIndex');
                    $dbManInfo->user_number  = $row->getAttribute('Number');
                    $dbManInfo->user_function  = $row->getAttribute('Function');
                    $dbManInfo->user_projectDepartment  = $row->getAttribute('ProjectDepartment');
                    $dbManInfo->user_name  = $row->getAttribute('UserName');
                    $dbManInfo->user_sex  = $row->getAttribute('Sex');
                    $dbManInfo->user_idNumber  = $row->getAttribute('IDNumber');
                    $dbManInfo->user_groupName  = $row->getAttribute('GroupName');
                    $dbManInfo->user_groupIndex  = $row->getAttribute('GroupIndex');
                    $dbManInfo->user_company  = $row->getAttribute('Company');
                    $dbManInfo->user_jobNumber  = $row->getAttribute('JobNumber');
                    $dbManInfo->user_subcontractTeam  = $row->getAttribute('SubcontractTeam');
                    $dbManInfo->user_dept  = $row->getAttribute('Dept');
                    $dbManInfo->user_birthDate  = $row->getAttribute('BirthDate');
                    $dbManInfo->user_personalFileIndex  = $row->getAttribute('PersonalFile_Index');
                    $dbManInfo->user_personalFileName  = $row->getAttribute('PersonalFileName');
                    $dbManInfo->user_PersonalPath  = $row->getAttribute('PersonalPath');
                    $dbManInfo->user_ID_A_Index  = $row->getAttribute('ID_A_Index');
                    $dbManInfo->user_ID_A_FileName  = $row->getAttribute('ID_A_FileName');
                    $dbManInfo->user_ID_A_Path  = $row->getAttribute('ID_A_Path');
                    $dbManInfo->user_ID_B_Index  = $row->getAttribute('ID_B_Index');
                    $dbManInfo->user_ID_B_FileName  = $row->getAttribute('ID_B_FileName');
                    $dbManInfo->user_ID_B_Path  = $row->getAttribute('ID_B_Path');
                    $dbManInfo->user_paperPhoto_Index  = $row->getAttribute('PaperPhoto_Index');
                    $dbManInfo->user_paperPhotoFileName  = $row->getAttribute('PaperPhotoFileName');
                    $dbManInfo->user_paperPhotoPath  = $row->getAttribute('PaperPhotoPath');
                    $dbManInfo->user_examResult  = $row->getAttribute('ExamResult');
                    $dbManInfo->user_medicalReportIndex  = $row->getAttribute('MedicalReport_Index');
                    $dbManInfo->user_medicalReportFileName  = $row->getAttribute('MedicalReportFileName');
                    $dbManInfo->user_medicalReportPath  = $row->getAttribute('MedicalReportPath');
                    $dbManInfo->user_specialWorkPermit_Index  = $row->getAttribute('SpecialWorkPermit_Index');
                    $dbManInfo->user_specialWorkPermitFileName  = $row->getAttribute('SpecialWorkPermitFileName');
                    $dbManInfo->user_specialWorkPermitPath  = $row->getAttribute('SpecialWorkPermitPath');
                    $dbManInfo->user_auditStatus  = $row->getAttribute('AuditStatus');
                    $dbManInfo->user_auditBeginTime  = $row->getAttribute('AuditBeginTime');
                    $dbManInfo->user_auditEndTime  = $row->getAttribute('AuditEndTime');
                    $dbManInfo->user_auditor  = $row->getAttribute('Auditor');
                    $dbManInfo->user_auditTime  = $row->getAttribute('AuditTime');
                    $dbManInfo->user_IllegalInformation  = $row->getAttribute('IllegalInformation');
                    $dbManInfo->user_sourcePicturePath = $row->getAttribute('sourcePicturePath');
                    $dbManInfo->user_sourcePicturePath_sl = $row->getAttribute('sourcePicturePath_sl');
                    // Name重复了
                    array_push($ManInfoArr, $dbManInfo);
                }
            }
            $aRet['content'] = $ManInfoArr;
        }


        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 用户添加人脸
 * @param
 *          ID         身份证
 * @author
 *          zenghx
 * @return array
 */
function AddFace($param)
{
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->ID)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->ObjSets)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $NUListXML = "";

        if(count($param->ObjSets) > 0){
            foreach($param->ObjSets as $row){
                $NUID = $row->NUID;
                $NUType = $row->NUType;

                $NUListXML.= '<NU NUID="'.$NUID.'"  NUType="'.$NUType.'"></NU>';
            }
        }

        $imageBase64 = "";

        if(!empty($_FILES)){
            $photo = $_FILES['addface'];

            $file_ext = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));

            if ($file_ext !== 'jpg' || !in_array($file_ext, array('jpg','jpeg', 'bmp', 'png', 'gif'))){
                GLogger(__FILE__, __LINE__, __FUNCTION__, "extension($file_ext) not allowed.");
                $aRet['errorCode'] = ErrorCode::UPLOAD_PHOTO_EXT_ERROR;
                
                return $aRet;
            }

            $d = date('Y-m-d');
            $dirName = dirname(__FILE__).'/EmployeeImages/'.$d;

            if(!file_exists($dirName))
            {
                mkdir($dirName, 0777, true);
            }

            if(file_exists($dirName))
            {
                // 随机数
                $r = rand(100000, 999999);

                $saveName = "{$r}.{$file_ext}";
                $savePath = "$dirName/$saveName";
                GLogger(__FILE__,__LINE__,__FUNCTION__,'savePath='.$savePath);

                if(move_uploaded_file($photo['tmp_name'], $savePath))
                {
                    $imageBase64 = imgToBase64($savePath);
                }
            }
        }
        else{
            $aRet['errorCode'] = ErrorCode::UPLOAD_ERROR;
            return $aRet;
        }
    

        $formatBase64 = "<![CDATA[".$imageBase64."]]>";

        $formatBase64 = str_replace(array("\r\n", "\r", "\n"), "", $formatBase64);
                
        $DstXML = <<<XML
        <DstRes Type="SC" Idx="0" OptID="CTL_IA_AddFacePic">
            <Param IDNumber="$param->ID" Force="1" >
                <Pic>$formatBase64</Pic>
                <NUList>
                    $NUListXML
                </NUList>
            </Param>
        </DstRes>
XML;
        
        GLogger(__FILE__, __LINE__, __FUNCTION__, "XML=".$DstXML);
        $param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);

        $param = json_decode(json_encode($param));

        GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));
        
        $ret = WSSdkSource::TransNUCommonMessage($param);

        
        GLogger(__FILE__, __LINE__, __FUNCTION__, "send request param=".print_r($ret,true));
		
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] =$ret['errorCode'] ;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 删除人脸
 * @param
 *          ID          身份证
 *          imageID     图片ID
 * @author
 *          zenghx
 * @return array
 */
function DeleteFace($param){
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }
        
        if(!isset($param->ID)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->imageID)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }
        
        $DstXML = <<<XML
        <DstRes Type="SC" Idx="0" OptID="CTL_IA_DeleteFacePic">
            <Param IDNumber="$param->id" ImageID="$param->imageID"></Param>
        </DstRes>
XML;
        
        GLogger(__FILE__, __LINE__, __FUNCTION__, "XML=".$DstXML);
        $param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);

        $param = json_decode(json_encode($param));

        GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));
        
        $ret = WSSdkSource::TransNUCommonMessage($param);

        if($ret !== "0x0000"){
            $aRet['errorCode'] = ErrorCode::REMOTE_SERVER_REQUEST_ERROR;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加人脸基本信息
 * @param
 *          number
 *          function
 *          projectDepartment
 *          name
 *          sex
 *          ID
 * @return array
 */
function AddFaceBaseInfo($param){
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->number))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->function))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->projectDepartment))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->name))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

       
        if(!isset($param->sex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->ID))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->description)){
            $param->description = NULL;
        }

        if(!isset($param->ObjSets)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $NUListXML = "";

        if(count($param->ObjSets) > 0){
            foreach($param->ObjSets as $row){
                $NUID = $row->NUID;
                $NUType = $row->NUType;

                $NUListXML.= '<NU NUID="{$NUID}"  NUType="{$NUType}"></NU>';
            }
        }


        $DstXML = <<<XML
        <DstRes Type="SC" Idx="0" OptID="CTL_IA_AddManInfo">
            <Param Number="$param->number" Name="$param->name" Function="$param->function" ProjectDepartment="$param->projectDepartment" Group="" Sex="$param->sex" IDNumber="$param->ID" Description="$param->description">
                <NUList>
                    $NUListXML
                </NUList>
            </Param>
        </DstRes>
XML;
        
        GLogger(__FILE__, __LINE__, __FUNCTION__, "XML=".$DstXML);
        $param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);

        $param = json_decode(json_encode($param));

        GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));
        
        $ret = WSSdkSource::TransNUCommonMessage($param);

        GLogger(__FILE__, __LINE__, __FUNCTION__, "send request param=".print_r($ret,true));
		
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] =$ret['errorCode'];
            return $aRet;
        }
        return $aRet;
    }
    catch(Exception $e){
        GLogger(__FILE__, __LINE__, __FUNCTION__, "XML=".print_r($e,true));
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 修改人脸基本信息
 * @param
 *          number
 *          function
 *          projectDepartment
 *          name
 *          group
 *          sex
 *          id
 * @return array
 */
function ModifyFaceBaseInfo($param){
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->number))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->function))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->projectDepartment))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->name))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->sex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->ID))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->description)){
            $param->description = NULL;
        }

        $DstXML = <<<XML
        <DstRes Type="SC" Idx="0" OptID="CTL_IA_UpdateManInfo">
            <Param Number="$param->number" Name="$param->name" Function="$param->function" ProjectDepartment="$param->projectDepartment" Group="" Sex="$param->sex" IDNumber="$param->ID" Description="$param->description"></Param>
        </DstRes>
XML;
        
        GLogger(__FILE__, __LINE__, __FUNCTION__, "XML=".$DstXML);
        $param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);

        $param = json_decode(json_encode($param));

        GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));
        
        $ret = WSSdkSource::TransNUCommonMessage($param);
       

        GLogger(__FILE__, __LINE__, __FUNCTION__, "send request param=".print_r($ret,true));
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] =$ret['errorCode'];
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 删除人脸基本信息
 * @param
 *          ID          身份证
 * @author
 *          zenghx
 * @return array
 */
function DeleteFaceBaseInfo($param){
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }
    
        if(!isset($param->ID))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $DstXML = <<<XML
        <DstRes Type="SC" Idx="0" OptID="CTL_IA_DeleteFaceInfo">
            <Param IDNumber="$param->ID" ></Param>
        </DstRes>
XML;
        
        GLogger(__FILE__, __LINE__, __FUNCTION__, "XML=".$DstXML);
        $param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);

        $param = json_decode(json_encode($param));

        GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));
        
        $ret = WSSdkSource::TransNUCommonMessage($param);


        GLogger(__FILE__, __LINE__, __FUNCTION__, "send request param=".print_r($ret,true));
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] =$ret['errorCode'];
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加人脸补录信息
 * @param
 *          userIndex       用户索引
 *          jobNumber       工号（新增）
 *          SubcontractTeam     所属分包队伍（新增）
 *          Dept            部门（新增）
 *          Company         公司（新增）
 *          BirthDate       出生年月（新增）
 *          personalFile_Index  个人档案索引
 *          id_a_index      身份证索引正面
 *          id_b_index      身份证索引背面
 *          paperPhoto_Index    试卷照片
 *          examResult      考试分数
 *          medicalReport_Index 体检报告索引
 *          SpecialWorkPermit_Index 特种作业索引
 *          AuditStatus     审核状态
 *          AuditBeginTime  审核开始时间
 *          AuditEndTime    审核结束时间
 *          Auditor         审核人
 *          AuditTime       审核时间
 *          illegalInformation  违章信息
 *          description     描述
 *          remark          备注
 * @author
 *          zenghx
 * @return array
 */
function AddFaceAdditionalInfo($param){
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->userIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $filedColumn = "";
        $filedValue = "";

        if(isset($param->examResult))
        {
            $filedColumn .= "`ExamResult`, ";
            $filedValue .= " $param->examResult, ";
        }
        
        $updateSQL = "";

        if(isset($param->jobNumber)){
            $filedColumn .= "`JobNumber`, ";
            $filedValue .= " '$param->jobNumber', ";
        }

        if(isset($param->SubcontractTeam)){
            $filedColumn .= "`SubcontractTeam`, ";
            $filedValue .= " '$param->SubcontractTeam', ";
        }

        if(isset($param->Dept)){
            $filedColumn .= "`Dept`, ";
            $filedValue .= " '$param->Dept', ";
        }

        if(isset($param->Company)){
            $filedColumn .= "`Company`, ";
            $filedValue .= " '$param->Company', ";
        }

        if(isset($param->BirthDate)){
            $filedColumn .= "`BirthDate`, ";
            $filedValue .= " '$param->BirthDate', ";
        }

        if(isset($param->personalFile_Index))
        {
            $filedColumn .= "`PersonalFile_Index`, ";
            $filedValue .= " '$param->personalFile_Index', ";
        }

        if(isset($param->id_a_index))
        {
            $filedColumn .= "`ID_A_Index`, ";
            $filedValue .= " '$param->id_a_index', ";
        }

        if(isset($param->id_b_index))
        {   
            $filedColumn .= "`ID_B_Index`, ";
            $filedValue .= " '$param->id_b_index', ";
        }

        if(isset($param->paperPhoto_Index))
        {   
            $filedColumn .= "`PaperPhoto_Index`, ";
            $filedValue .= " '$param->paperPhoto_Index', ";
        }

        if(isset($param->medicalReport_Index))
        {
           $filedColumn .= "`MedicalReport_Index`, ";
           $filedValue .= " '$param->medicalReport_Index', ";
        }

        if(isset($param->SpecialWorkPermit_Index))
        {
           $filedColumn .= "`SpecialWorkPermit_Index`, ";
           $filedValue .= " '$param->SpecialWorkPermit_Index', ";
        }

        if(isset($param->AuditStatus))
        {
           $filedColumn .= "`AuditStatus`, ";
           $filedValue .= " '$param->AuditStatus', ";
        }

        if(isset($param->AuditBeginTime))
        {
           $filedColumn .= "`AuditBeginTime`, ";
           $filedValue .= " '$param->AuditBeginTime', ";
        }

        if(isset($param->AuditEndTime))
        {
           $filedColumn .= "`AuditEndTime`, ";
           $filedValue .= " '$param->AuditEndTime', ";
        }

        if(isset($param->Auditor))
        {
           $filedColumn .= "`Auditor`, ";
           $filedValue .= " '$param->Auditor', ";
        }

        if(isset($param->AuditTime))
        {
           $filedColumn .= "`AuditTime`, ";
           $filedValue .= " '$param->AuditTime', ";
        }

        if(isset($param->illegalInformation))
        {
           $filedColumn .= "`IllegalInformation`, ";
           $filedValue .= " '$param->illegalInformation', ";
        }

        if(isset($param->description))
        {
            $filedColumn .= "`Description`, ";
            $filedValue .= " '$param->description', ";
        }
        
        $filedColumn .= "`ModifyDateTime`";
        $filedValue .= " now()";


        $query = "INSERT INTO IA_ManInfoEx(".$filedColumn.")VALUE(".$filedValue.")" ;

        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询语句='.$query);

        $result = $db_link->Query($query);

        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{
            $lastQuery = "Select LAST_INSERT_ID() AS LastIndex";
            $lastResult = $db_link->Query($lastQuery);
            if(!$lastResult){
                $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
                return $aRet;
            }
            else{
                $lastIndex = "";
                while(RowFetchObject($row, $lastResult, $db_link)){
                    $lastIndex = $row->LastIndex;
                }

                $update = "UPDATE IA_ManInfo SET `ManInfoEx_Index` = '$lastIndex' WHERE `Index` = '$param->userIndex' ";
                $updateResult = $db_link->Query($update);
                if(!$updateResult){
                    $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
                    return $aRet;  
                }
            }
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

// 修改人脸补录信息
function ModifyFaceAdditionalInfo($param){
    try{
        global $db_link;

        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->userIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->examResult))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }
        
        $updateSQL = "";

        if(isset($param->jobNumber)){
            $updateSQL .= " `JobNumber` = '$param->jobNumber', ";
        }

        if(isset($param->SubcontractTeam)){
            $updateSQL .= " `SubcontractTeam` = '$param->SubcontractTeam', ";
        }

        if(isset($param->Dept)){
            $updateSQL .= " `Dept` = '$param->Dept', ";
        }

        if(isset($param->Company)){
            $updateSQL .= " `Company` = '$param->Company', ";
        }

        if(isset($param->BirthDate)){
            $updateSQL .= " `BirthDate` = '$param->BirthDate', ";
        }

        if(isset($param->personalFile_Index))
        {
            $updateSQL .= " `PersonalFile_Index` = '$param->personalFile_Index', ";
        }

        if(isset($param->id_a_index))
        {
            $updateSQL .= " `ID_A_Index` = '$param->id_a_index', ";
        }

        if(isset($param->id_b_index))
        {   
            $updateSQL .= " `ID_B_Index` = '$param->id_b_index', ";
        }

        if(isset($param->paperPhoto_Index))
        {   
            $updateSQL .= " `PaperPhoto_Index` = '$param->paperPhoto_Index', ";
        }

        if(isset($param->medicalReport_Index))
        {
           $updateSQL .= " `MedicalReport_Index` = '$param->medicalReport_Index', ";
        }

        if(isset($param->SpecialWorkPermit_Index))
        {
           $updateSQL .= " `SpecialWorkPermit_Index` = '$param->SpecialWorkPermit_Index', ";
        }

        if(isset($param->AuditStatus))
        {
           $updateSQL .= " `AuditStatus` = '$param->AuditStatus', ";
        }

        if(isset($param->AuditBeginTime))
        {
           $updateSQL .= " `AuditBeginTime` = '$param->AuditBeginTime', ";
        }

        if(isset($param->AuditEndTime))
        {
           $updateSQL .= " `AuditEndTime` = '$param->AuditEndTime', ";
        }

        if(isset($param->Auditor))
        {
           $updateSQL .= " `Auditor` = '$param->Auditor', ";
        }

        if(isset($param->AuditTime))
        {
           $updateSQL .= " `AuditTime` = '$param->AuditTime', ";
        }

        if(isset($param->illegalInformation))
        {
           $updateSQL .= " `IllegalInformation`='$param->illegalInformation', ";    
        }

        if(isset($param->description))
        {
            $updateSQL .= " `Description` = '$param->description', ";
        }

        if(isset($param->remark))
        {
            $updateSQL .= " `Remark` = '$param->remark', ";
        }

        $query = "UPDATE IA_ManInfoEx SET ".$updateSQL." `ExamResult` = '$param->examResult' WHERE `Index`='$param->userIndex'";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询语句='.$query);

        $result = $db_link->Query($query);

        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }


        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

// 删除人脸补录信息
function DeleteFaceAdditionalInfo($param){}

// 添加上传附件
function AddUploadFiles($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );
        if(!empty($_FILES)){

            $photo = $_FILES['UploadImage'];
            GLogger(__FILE__, __LINE__, __FUNCTION__, "阿发达".print_r($photo,true));

            $file_ext = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));

            if (!in_array($file_ext, array('jpg','jpeg', 'bmp', 'png', 'gif')))
            {
                GLogger(__FILE__, __LINE__, __FUNCTION__, "extension($file_ext) not allowed.");
                return $aRet['errorCode'] = ErrorCode::UPLOAD_PHOTO_EXT_ERROR;
            }

            $d = date('Y-m-d');
            $dirName = dirname(__FILE__).'/UploadImages/'.$d;

            if(!file_exists($dirName))
            {
                mkdir($dirName, 0777, true);
            }

            if(file_exists($dirName))
            {
                // 随机数
                $r = rand(100000, 999999);

                $saveName = "{$r}.{$file_ext}";
                $savePath = "$dirName/$saveName";
                $savePath=str_replace("\\","/",$savePath);
                GLogger(__FILE__,__LINE__,__FUNCTION__,'savePath='.$savePath);

                if(move_uploaded_file($photo['tmp_name'], $savePath))
                {
                    $query = "INSERT INTO IA_UploadPicInfo(`PicName`,`PicType`,`PicPath`,`Description`,`DeleteFlag`,`ModifyDateTime`,`Remark`) VALUES('$saveName','0','$savePath','','0',now(),'')";
                    GLogger(__FILE__,__LINE__,__FUNCTION__,'查询语句='.$query);

                    $result = $db_link->Query($query);
                    if(!$result){
                        $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
                        return false;
                    }
                    else{
                        $inserIndex = $db_link->GetLastInsertID();
                        $aRet['content'] = $inserIndex;
                    }
                }
                else{
                    $aRet['errorCode'] = ErrorCode::UPLOAD_ERROR;
                    return $aRet;
                }
            }
        }
        else{
            $aRet['errorCode'] = ErrorCode::UPLOAD_ERROR;
            return false;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

// 删除文件上传
function DeleteUploadFiles($param){
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->file_Index)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = " UPDATE IA_UploadPicInfo SET `DeleteFlag` = '1' WHERE `Index` = '$param->file_Index' ";
        
        $result = $db_link-> Query($query);

        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

// 检查属性
function CheckAttributes($param){
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->checkID))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->checkNumber))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "SELECT COUNT(*) AS SUM FROM IA_ManInfo WHERE (`IDNumber`='$param->checkID' OR `Number`='$param->checkNumber') AND `DeleteFlag` = '0' ";
        GLogger(__FILE__, __LINE__, __FUNCTION__, "查询".$query);

        $result = $db_link->Query($query);
        $count = 0;
        if(!$result)
        {
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{
            while(RowFetchObject($row, $result, $db_link))
            {
                $count = $row->SUM;
            }
        }

        $aRet['content'] = array(
            'count'=>$count
        );
        return $aRet;


        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 获取组信息
 * @param
 *      groupType
 */
function QueryGroupInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->groupType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "SELECT * FROM IA_GroupInfo WHERE GroupType='$param->groupType' ";
        
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{
            $groupInfoArr = array();
            while(RowFetchObject($row, $result, $db_link))
            {
                $dbInfo = new GroupInfo();
                $dbInfo->index = $row->Index;
                $dbInfo->group = $row->Group;
                $dbInfo->parent_index = $row->Parent_Index;
                $dbInfo->remark = $row->Remark;
                array_push($groupInfoArr, $dbInfo);
            }
            $aRet['content'] = $groupInfoArr;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加组信息
 * @param
 *      group
 *      groupType
 *      parent_index
 *      remark
 */
function AddGroupInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->group))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->groupType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->parent_index))
        {
            $parent_index = 0;
        }else{
            $parent_index = $param->parent_index;
        }

        if(!isset($param->remark))
        {
            $remark = "";
        }else{
            $remark = $param->remark;
        }

        $query = "INSERT INTO IA_GroupInfo (`Group`, `Parent_Index`, `GroupType`, `Remark`) VALUES ('$param->group','$parent_index' ,'$param->groupType','$remark')";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'添加'.$query);

        $result = $db_link->Query($query);

        if(!$result)
        {
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 修改组信息
 * @param
 *      groupType
 *      groupIndex
 *      groupName
 *      parent_index
 */
function ModifyGroupInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->groupName))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }
        if(!isset($param->groupIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->groupType)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "UPDATE IA_GroupInfo SET `Group` = '$param->groupName' WHERE `Index` = '$param->groupIndex' AND `GroupType` = '$param->groupType'";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'修改组别信息'.$query);
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 删除组信息
 * @param
 *      groupIndex
 */
function DeleteGroupInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->groupIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "DELETE from IA_GroupInfo WHERE `Index` = '$param->groupIndex' ";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'删除组别信息'.$query);
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 获取车牌信息
 * @param
 *      
 */
function QueryCarIDInfo($param){
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        $query = "SELECT * FROM IA_CarInfo  WHERE `DeleteFlag` = '0' ";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'删除组别信息'.$query);
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{
            $content = array();
            while(RowFetchObject($row, $result, $db_link)){
                $plateInfo = new PlateInfoStruct();

                $plateInfo->Index = $row->Index;
                $plateInfo->PicturePath = $row->PicturePath;
                $plateInfo->PicturePath_sl = $row->PicturePath_sl;
                $plateInfo->PlateColor = $row->PlateColor;
                $plateInfo->PlateNo = $row->PlateNo;
                $plateInfo->PlateNoAttach = $row->PlateNoAttach;
                $plateInfo->VehicleClass = $row->VehicleClass;
                $plateInfo->VehicleBrand = $row->VehicleBrand;
                $plateInfo->VehicleModel = $row->VehicleModel;
                $plateInfo->VehicleColor = $row->VehicleColor;
                $plateInfo->DeleteFlag = $row->DeleteFlag;
                $plateInfo->ModifyDateTime = $row->ModifyDateTime;
                $plateInfo->Remark = $row->Remark;

                array_push($content, $plateInfo);
            }
            $aRet['content'] = $content;
        }
        
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加车牌信息
 * @param
 *          PlateColor
 *          PlateNo
 *          PlateNoAttach
 *          VehicleClass
 *          VehicleBrand
 *          VehicleModel
 *          VehicleColor
 */
function AddCarIDInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->PlateColor)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->PlateNo)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->PlateNoAttach)){
            $param->PlateNoAttach = '';
        }

        if(!isset($param->VehicleClass)){
            $param->VehicleClass = '';
        }

        if(!isset($param->VehicleBrand)){
            $param->VehicleBrand = '';
        }

        if(!isset($param->VehicleModel)){
            $param->VehicleModel = '';
        }

        if(!isset($param->VehicleColor)){
            $param->VehicleColor = '';
        }

        $query = "INSERT INTO IA_CarInfo(`PlateColor`, `PlateNo`, `PlateNoAttach`, `VehicleClass`,`VehicleBrand`,`VehicleModel`,`VehicleColor`,`DeleteFlag`,`ModifyDateTime`) VALUE('$param->PlateColor','$param->PlateNo','$param->PlateNoAttach','$param->VehicleClass','$param->VehicleBrand','$param->VehicleModel','$param->VehicleColor','0',now())";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'添加车牌信息'.$query);
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 修改车牌信息
 * @param
 *      plateIndex
 *      plateNumber
 *      color
 *      description
 *      remark
 */
function ModifyCarIDInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->plateIndex)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->PlateColor)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->PlateNo)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }
        
        $update = "";

        if(isset($param->PlateNoAttach)){
            $update .= " `PlateNoAttach` = '$param->PlateNoAttach', ";
        }

        if(isset($param->VehicleClass)){
            $update .= " `VehicleClass` = '$param->VehicleClass', ";
        }

        if(isset($param->VehicleBrand)){
            $update .= " `VehicleBrand` = '$param->VehicleBrand', ";
        }

        if(isset($param->VehicleModel)){
            $update .= " `VehicleModel` = '$param->VehicleModel', ";
        }

        if(isset($param->VehicleColor)){
            $update .= " `VehicleColor` = '$param->VehicleColor', ";
        }

        $query = "UPDATE IA_CarInfo SET ".$update." `PlateColor` = '$param->PlateColor',`PlateNo`='$param->PlateNo' WHERE `Index` = '$param->plateIndex' ";
        GLogger(__FILE__,__LINE__,__FUNCTION__,'修改车牌信息'.$query);
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 删除车牌信息
 * @param
 *      plateIndex
 */
function DeleteCarIDInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->plateIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "UPDATE IA_CarInfo SET `DeleteFlag` = '1' WHERE `Index` = '$param->plateIndex' ";
        GLogger(__FILE__,__LINE__,__FUNCTION__,'删除车牌信息'.$query);
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 获取组下资源信息
 * @param
 *      groupIndex
 *      groupType
 */
function QueryGroupResourceInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->groupIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->groupType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if($param->groupType == '1'){
            $query = "SELECT ci.`Index`, ci.`PlateNo` AS resName FROM IA_CarGroupMap ipm  INNER JOIN IA_CarInfo ci ON ci.`Index` = ipm.CarInfo_Index WHERE ipm.GroupInfo_Index = '$param->groupIndex'  ";
        }
        else{
            $query = "SELECT imi.`Index`, imi.`Name` AS resName FROM IA_ManGroupMap imm  INNER JOIN IA_ManInfo imi ON imi.`Index` = imm.ManInfo_Index WHERE imm.GroupInfo_Index = '$param->groupIndex'  ";
        }
        
        GLogger(__FILE__,__LINE__,__FUNCTION__,'获取分组下信息'.$query);
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{
            $content = array();
            while(RowFetchObject($row, $result, $db_link)){
                array_push($content, array(
                    'Index' => $row->Index,
                    'resName' => $row->resName
                ));
            }

            $aRet['content'] = $content;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加组下资源信息
 * @param
 *      groupIndex
 *      groupType
 *      resList
 */
function AddGroupResourceInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->groupIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->groupType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->resList))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $valueSQL = "";
        if(is_array($param->resList)){
            if(count($param->resList) > 0){
                foreach($param->resList as $res){
                    $valueSQL .= "('$param->groupIndex', '$res'),";
                }
            }
        }
        $valueSQL = rtrim($valueSQL, ","); 

        if(empty($valueSQL)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if($param->groupType == '1'){
            $insertSQL = 'IA_CarGroupMap(`GroupInfo_Index`,`CarInfo_Index`)';
        }
        else{
            $insertSQL = 'IA_ManGroupMap(`GroupInfo_Index`,`ManInfo_Index`)';
        }
        
        $query = "INSERT IGNORE INTO ".$insertSQL."VALUE".$valueSQL;
        GLogger(__FILE__,__LINE__,__FUNCTION__,'获取分组下车牌信息'.$query);
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

// 
/**
 * 删除组下资源
 * @param
 *      groupIndex
 *      groupType
 *      resIndex
 */
function DeleteGroupResourceInfo($param){
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        if(!isset($param->groupIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->resIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->groupType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        
        if($param->groupType == '1'){
            $query = "DELETE FROM IA_CarGroupMap  WHERE `GroupInfo_Index` = '$param->groupIndex' AND `CarInfo_Index` = '$param->resIndex' ";
        }
        else{
            $query = "DELETE FROM IA_ManGroupMap  WHERE `GroupInfo_Index` = '$param->groupIndex' AND `ManInfo_Index` = '$param->resIndex' ";
        }

        GLogger(__FILE__,__LINE__,__FUNCTION__,'获取分组下车牌信息'.$query);

        $result = $db_link->Query($query);

        if(!$result){
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

function QueryFaceRecognitionHistroyInfo($param){
    try{
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->PUID)){
			$param->PUID = "";
		}

		if(!isset($param->Idx)){
			$param->Idx = "";
		}

		if(!isset($param->IDNumber)){
			$param->IDNumber = "";
		}

		if(!isset($param->BeginTime)){
			GLogger(__FILE__, __LINE__, __FUNCTION__, "BeginTime为空：$param->BeginTime");
			$aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
			return $aRet;
		}

		$param->BeginTime = strtotime($param->BeginTime);
		
		if(!isset($param->EndTime)){
			GLogger(__FILE__, __LINE__, __FUNCTION__, "EndTime为空：$param->EndTime");
			$aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
			return $aRet;
		}

		$param->EndTime = strtotime($param->EndTime);

		if(!isset($param->offset)){
			$param->offset = 0;
		}

		if(!isset($param->count)){
			$param->count = 200;
		}


		$DstXML = <<<XML
		<DstRes Type="SC" Idx="0" OptID="CTL_IA_GetFaceRecognitionHistory">
			<Param PUID="{$param->PUID}" Idx="{$param->Idx}" IDNumber="{$param->IDNumber}" BeginTime="{$param->BeginTime}" EndTime="{$param->EndTime}" Offset="{$param->offset}" Cnt="{$param->count}" Qkey="{$param->Qkey}">
			</Param>
		</DstRes>
XML;

        GLogger(__FILE__, __LINE__, __FUNCTION__, "XML：$DstXML");

        $param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
        );

        $param = json_decode(json_encode($param));
        GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($param,true));

        $ret = WSSdkSource::TransNUCommonMessage($param);
        GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($ret,true));
        if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] = ErrorCode::XML_REQUEST_ERROR;
            return $aRet;
        }
        else{

            $XMLContent = $ret['xml_content'];

			$xmldoc = new DOMDocument();
			$XMLContent = str_replace(PHP_EOL, '', $XMLContent);

			if(!$xmldoc->loadXML($XMLContent)){
				$aRet['errorCode'] = ErrorCode::XML_ANALYSIS_FAILED;
				GLogger(__FILE__, __LINE__, __FUNCTION__, "XML读取失败,XML=".$XMLContent);
				return $aRet;
			}

			$xpath = new DOMXPath($xmldoc);

			$cmd = $xpath->query("/Msg/Cmd")->item(0);
			$nuErrorCode = $cmd->getAttribute("NUErrorCode");
			GLogger(__FILE__, __LINE__, __FUNCTION__, 'NUErrorCode='.$nuErrorCode);

			if($nuErrorCode != 0)
			{
				$aRet['errorCode'] = ErrorCode::XML_RESPONSE_NU_ERROR;
				GLogger(__FILE__, __LINE__, __FUNCTION__, 'NU错误,ErrorCode='.$nuErrorCode);
				return $aRet;
            }
            
            $list = $xpath->query("/Msg/Cmd/DstRes/Param/List")->item(0);
            $count = $list->getAttribute("Count");
            GLogger(__FILE__, __LINE__, __FUNCTION__, 'Count='.$count);

			$HistoryInfos = $cmd->getElementsByTagName("FaceRecognitionHistory");

            $RecognitionArr = array();
            
            $previewURL = 'http://'.$_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT']."/nmi/php/nmi_ia_server.php";

			if(count($HistoryInfos) > 0){
				foreach($HistoryInfos as $row){
                    $recognitionInfo = new RecognitionHistoryInfoStruct();
                    $recognitionInfo->Index = $row->getAttribute('Index');
                    $recognitionInfo->GroupDetectHistory_Index = $row->getAttribute('GroupDetectHistory_Index');
                    $recognitionInfo->DeleteFlag = $row->getAttribute('DeleteFlag');
                    $recognitionInfo->DeleteTime = $row->getAttribute('DeleteTime');
                    $recognitionInfo->ModifyDateTime = $row->getAttribute('ModifyDateTime');
					$recognitionInfo->ManInfo_Index = $row->getAttribute('ManInfo_Index');
					$recognitionInfo->ManFeatureInfo_Index = $row->getAttribute('ManFeatureInfo_Index');
					$recognitionInfo->Time = $row->getAttribute('Time');
					$recognitionInfo->TimeStamp = $row->getAttribute('TimeStamp');
					$recognitionInfo->PUID = $row->getAttribute('PUID');
					$recognitionInfo->Idx = $row->getAttribute('Idx');
					$recognitionInfo->Number = $row->getAttribute('Number');
					$recognitionInfo->Function = $row->getAttribute('Function');
					$recognitionInfo->ProjectDepartment = $row->getAttribute('ProjectDepartment');
					$recognitionInfo->UserName = $row->getAttribute('Name');
					$recognitionInfo->IDNumber = $row->getAttribute('IDNumber');
					$recognitionInfo->Sex = $row->getAttribute('Sex');
					$recognitionInfo->FaceScore = $row->getAttribute('FaceScore');
					$recognitionInfo->Facerect_X = $row->getAttribute('Facerect_X');
					$recognitionInfo->Facerect_Y = $row->getAttribute('Facerect_Y');
					$recognitionInfo->Facerect_W = $row->getAttribute('Facerect_W');
					$recognitionInfo->Facerect_H = $row->getAttribute('Facerect_H');
					$recognitionInfo->Costtime = $row->getAttribute('Costtime');
					$NUID = $row->getAttribute('NUID');
					$recognitionInfo->NUID = $NUID;
					$recognitionInfo->NUType = $row->getAttribute('NUType');
					$filePath_Source = $row->getAttribute('PicturePath');
                    $filePath_Preview = $row->getAttribute('PicturePath_sl');
                    $sourcePath = $row->getAttribute('SourcePicturePath');
                    $sourcePath_sl = $row->getAttribute('SourcePicturePath_sl');
                    $recognitionInfo->sourcePicturePath = $sourcePath;
                    $recognitionInfo->sourcePicturePath_sl = $sourcePath_sl;
                    
					$recognitionInfo->HttpPicUrl_IA_Source = $previewURL."?NUID=".$NUID."&fname=".$sourcePath;
					$recognitionInfo->HttpPicUrl_IA_Preview = $previewURL."?NUID=".$NUID."&fname=".$sourcePath_sl;
                    $recognitionInfo->PicturePath = $filePath_Source;
                    $recognitionInfo->PicturePath_sl = $filePath_Preview;
					$recognitionInfo->HttpPicUrl_Recognition_Source = $previewURL."?NUID=".$NUID."&fname=".$filePath_Source;
					$recognitionInfo->HttpPicUrl_Recognition_Preview = $previewURL."?NUID=".$NUID."&fname=".$filePath_Preview;
					$recognitionInfo->Latitude = $row->getAttribute('Latitude');
                    $recognitionInfo->Longtitude = $row->getAttribute('Longtitude');
                    
					array_push($RecognitionArr, $recognitionInfo);
				}
			}

			$aRet['content'] = array(
                'Count'=>$count,
                'List'=> $RecognitionArr
            );

        }
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 查询人脸检测历史记录
 * @param
 *      date
 *      qkey
 */
function QueryFaceDetectHistoryInfo($param){
    try{
        if(!isset($param->PUID)){
			$param->PUID = "";
		}

		if(!isset($param->Idx)){
			$param->Idx = "";
		}

		if(!isset($param->BeginTime)){
			GLogger(__FILE__, __LINE__, __FUNCTION__, "BeginTime为空：$param->BeginTime");
			$aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
			return $aRet;
		}

		$param->BeginTime = strtotime($param->BeginTime);

		if(!isset($param->EndTime)){
			GLogger(__FILE__, __LINE__, __FUNCTION__, "EndTime为空：$param->EndTime");
			$aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
			return $aRet;
		}

		$param->EndTime = strtotime($param->EndTime);

		if(!isset($param->offset)){
			$param->offset = 0;
		}

		if(!isset($param->count)){
			$param->count = 200;
		}

		$DstXML = <<<XML
		<DstRes Type="SC" Idx="0" OptID="CTL_IA_GetFaceDetectHistory">
			<Param PUID="{$param->PUID}" Idx="{$param->Idx}" BeginTime="{$param->BeginTime}" EndTime="{$param->EndTime}" Offset="{$param->offset}" Cnt="{$param->count}" Qkey="{$param->Qkey}">
			</Param>
		</DstRes>
XML;

		GLogger(__FILE__, __LINE__, __FUNCTION__, "XML=".$DstXML);

		$param = array(
            'cmdType' => 'CTL',
            'nuType' => SEND_NU_TYPE,
            'NUID' => SEND_NU_ID,
            'xmlDstRes' => $DstXML
		);

		$options = json_decode(json_encode($param));
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send xml param=".print_r($options,true));

		$ret = WSSdkSource::TransNUCommonMessage($options);
		GLogger(__FILE__, __LINE__, __FUNCTION__, "send request param=".print_r($ret,true));
		if($ret['errorCode'] != "0x0000"){
            $aRet['errorCode'] = ErrorCode::XML_RESPONSE_NU_ERROR;
			GLogger(__FILE__, __LINE__, __FUNCTION__, '远程响应失败');
            return $aRet;
		}
		else{
			$XMLContent = $ret['xml_content'];

			$xmldoc = new DOMDocument();
			$xmldoc->loadXML($XMLContent);

			if(!$xmldoc->loadXML($XMLContent)){
				$aRet['errorCode'] = ErrorCode::XML_ANALYSIS_FAILED;
				GLogger(__FILE__, __LINE__, __FUNCTION__, "XML读取失败,XML=".$XMLContent);
				return $aRet;
			}

			$xpath = new DOMXPath($xmldoc);

			$cmd = $xpath->query("/Msg/Cmd")->item(0);
			$nuErrorCode = $cmd->getAttribute("NUErrorCode");
			GLogger(__FILE__, __LINE__, __FUNCTION__, 'NUErrorCode='.$nuErrorCode);

			if($nuErrorCode != 0)
			{
				$aRet['errorCode'] = ErrorCode::XML_RESPONSE_NU_ERROR;
				GLogger(__FILE__, __LINE__, __FUNCTION__, 'NU错误,ErrorCode='.$nuErrorCode);
				return $aRet;
            }
            
            $list = $xpath->query("/Msg/Cmd/DstRes/Param/List")->item(0);
            $count = $list->getAttribute("Count");
            GLogger(__FILE__, __LINE__, __FUNCTION__, 'Count='.$count);

			$DetectInfos = $cmd->getElementsByTagName("FaceDetectHistory");

			$DetectArr = array();

			$previewURL = 'http://'.$_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT']."/nmi/php/nmi_ia_server.php";

			if(count($DetectInfos) > 0){
				foreach($DetectInfos as $row){
                    $DetectInfo = new DetectHistoryInfoStruct();
                    $DetectInfo->Index = $row->getAttribute('Index');
                    $DetectInfo->Time = $row->getAttribute('Time');
                    $DetectInfo->TimeStamp = $row->getAttribute('TimeStamp');
                    $DetectInfo->PUID = $row->getAttribute('PUID');
                    $DetectInfo->Idx = $row->getAttribute('Idx');
                    $DetectInfo->FaceScore = $row->getAttribute('FaceScore');
                    $DetectInfo->Feature = $row->getAttribute('Feature');
                    $DetectInfo->Facerect_X = $row->getAttribute('Facerect_X');
                    $DetectInfo->Facerect_Y = $row->getAttribute('Facerect_Y');
                    $DetectInfo->Facerect_W = $row->getAttribute('Facerect_W');
                    $DetectInfo->Facerect_H = $row->getAttribute('Facerect_H');
                    $DetectInfo->Costtime = $row->getAttribute('Costtime');
                    $DetectInfo->NUType = $row->getAttribute('NUType');
                    $NUID = $row->getAttribute('NUID');
                    $DetectInfo->NUID = $NUID;
                    $DetectInfo->GroupDetectHistory_Index = $row->getAttribute('GroupDetectHistory_Index');
                    $DetectInfo->Latitude = $row->getAttribute('Latitude');
                    $DetectInfo->Longtitude = $row->getAttribute('Longtitude');
                    $DetectInfo->SkinColor = $row->getAttribute('SkinColor');
                    $DetectInfo->HairStyle = $row->getAttribute('HairStyle');
                    $DetectInfo->HairColor = $row->getAttribute('HairColor');
                    $DetectInfo->FaceStyle = $row->getAttribute('FaceStyle');
                    $DetectInfo->FacialFeature = $row->getAttribute('FacialFeature');
                    $DetectInfo->PhysicalFeature = $row->getAttribute('PhysicalFeature');
                    $DetectInfo->RespiratorColor = $row->getAttribute('RespiratorColor');
                    $DetectInfo->CapStyle = $row->getAttribute('CapStyle');
                    $DetectInfo->CapColor = $row->getAttribute('CapColor');
                    $DetectInfo->GlassStyle = $row->getAttribute('GlassStyle');
                    $DetectInfo->GlassColor = $row->getAttribute('GlassColor');
                    $DetectInfo->Attitude = $row->getAttribute('Attitude');
                    $DetectInfo->EyebrowStyle = $row->getAttribute('EyebrowStyle');
                    $DetectInfo->NoseStyle = $row->getAttribute('NoseStyle');
                    $DetectInfo->MustacheStyle = $row->getAttribute('MustacheStyle');
                    $DetectInfo->LipStyle = $row->getAttribute('LipStyle');
                    $DetectInfo->WrinklePouch = $row->getAttribute('WrinklePouch');
                    $DetectInfo->AcneStain = $row->getAttribute('AcneStain');
                    $DetectInfo->FreckleBirthmark = $row->getAttribute('FreckleBirthmark');
                    $DetectInfo->ScarDimple = $row->getAttribute('ScarDimple');
                    $DetectInfo->OtherFeature = $row->getAttribute('OtherFeature');
                    $DetectInfo->DeleteFlag = $row->getAttribute('DeleteFlag');
                    $DetectInfo->DeleteTime = $row->getAttribute('DeleteTime');
                    $DetectInfo->ModifyDateTime = $row->getAttribute('ModifyDateTime');
                    $DetectInfo->Remark = $row->getAttribute('Remark');
					$DetectInfo->Latitude = $row->getAttribute('Latitude');
                    $DetectInfo->Longtitude = $row->getAttribute('Longtitude');
                    
                    $filePath_Source = $row->getAttribute('PicturePath');
                    $filePath_Preview = $row->getAttribute('PicturePath_sl');
                    $DetectInfo->PicturePath = $filePath_Source;
                    $DetectInfo->PicturePath_sl = $filePath_Preview;
                    $DetectInfo->PicturePath_Source = $filePath_Source;
					$DetectInfo->PicturePath_Preview = $filePath_Preview;
                    $DetectInfo->sourcePicturePath = $filePath_Source;
                    $DetectInfo->sourcePicturePath_sl = $filePath_Preview;
                    
                    
                    $DetectInfo->HttpPicUrl_Detect_Source = $previewURL."?NUID=".$NUID."&fname=".$filePath_Source;
                    $DetectInfo->HttpPicUrl_Detect_Preview = $previewURL."?NUID=".$NUID."&fname=".$filePath_Preview;

				

					array_push($DetectArr, $DetectInfo);
				}
			}

			$aRet['content'] = array(
                'Count'=> $count,
                'List' => $DetectArr  
            );
		}
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}



function QueryPlateNumberHistroyInfo($param){
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!__checkSession())
        {
            $aRet['errorCode'] = ErrorCode::SESSION_UNVALID_OR_NOTEXISTS;
            $aRet['msg'] = '登录断开连接';
            return $aRet;
        }

        $whereSQL = "";

        if(!empty($param->qkey)){
            $whereSQL .= " AND (";
            $whereSQL .= " iph.`PUID` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`Idx` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`PlateColor` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`PlateNo` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`PlateNoAttach` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`Speed` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`Direction` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`VehicleClass` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`VehicleBrand` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`VehicleModel` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`VehicleColor` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`PlateReliability` LIKE '%{$param->qkey}%' ";
            $whereSQL .= " OR iph.`BrandReliability` LIKE '%{$param->qkey}%' ";
            $whereSQL .= ")";
        }

        if(!isset($param->date)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "SELECT iph.* FROM `IA_PlateDetectHistory` iph";
        $query .= " WHERE 1=1 and iph.`DeleteFlag` = '0'".$whereSQL;
        $query .= " AND FROM_UNIXTIME(iph.`TimeStamp`,'%Y-%m-%d') = '$param->date'";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'获取识别历史记录'.$query);
        $result = $db_link->Query($query);
        if(!$result){
            $aRet['content'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{
            $content = array();

            while(RowFetchObject($row, $result, $db_link)){
                $carInfo = new CarHistoryInfoStruct();
                $carInfo->Index = $row->Index;
                $carInfo->Time = $row->Time;
                $carInfo->PUID = $row->PUID;
                $carInfo->Idx = $row->Idx;
                $carInfo->Platerect_x = $row->Platerect_X;
                $carInfo->Platerect_y = $row->Platerect_Y;
                $carInfo->Platerect_w = $row->Platerect_W;
                $carInfo->Platerect_h = $row->Platerect_H;
                $carInfo->Costtime = $row->Costtime;
                $carInfo->PicturePath = $row->PicturePath;
                $carInfo->PicturePath_sl = $row->PicturePath_sl;
                $carInfo->NUType = $row->NUType;
                $carInfo->NUID = $row->NUID;
                $carInfo->GroupDetectHistory_Index = $row->GroupDetectHistory_Index;
                $carInfo->Latitude = $row->Latitude;
                $carInfo->Longtitude = $row->Longtitude;
                $carInfo->PlateColor = $row->PlateColor;
                $carInfo->PlateNo = $row->PlateNo;
                $carInfo->PlateNoAttach = $row->PlateNoAttach;
                $carInfo->Speed = $row->Speed;
                $carInfo->Direction = $row->Direction;
                $carInfo->VehicleClass = $row->VehicleClass;
                $carInfo->VehicleBrand = $row->VehicleBrand;
                $carInfo->VehicleModel = $row->VehicleModel;
                $carInfo->VehicleColor = $row->VehicleColor;
                $carInfo->PlateReliability = $row->PlateReliability;
                $carInfo->BrandReliability = $row->BrandReliability;
                $carInfo->DeleteFlag = $row->DeleteFlag;
                $carInfo->DeleteTime = $row->DeleteTime;
                $carInfo->ModifyDateTime = $row->ModifyDateTime;
                $carInfo->Remark = $row->Remark;

                array_push($content, $carInfo);
            }

            $aRet['content'] = $content;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
    }
}


// Custom function
function __checkSession()
{
    try{
        $logined = false;
        if($_SESSION['NMI']){
            $logined = $_SESSION['NMI']['IsLogined'];
            if($logined){
                return true;
            }
        }
        if(!$logined){
            if($_SESSION['IA'])
            {
                $keepAliveTime = $_SESSION['IA']['TIME'];
                GLogger(__FILE__, __LINE__, __FUNCTION__, 'time='.time()."keepalivetime=".$keepAliveTime);
                if(time() < $keepAliveTime){
                    return true;
                }
                else{
                    session_unset("IA");
                    return false;
                }
            }
            else{
                session_unset("IA");
                return false;
            }
        }
     }
     catch(Exception $e){
        GLogger(__FILE__, __LINE__, __FUNCTION__, '发生异常'.$e->getMessage());
        return array(
            'msg'=> '抛出异常'.$e->getMessage(),
            'errorCode' => ErrorCode::THREAD
        );
     }
}

/**
 * 内部接口（获取WS加密Key）
 */
function __insideQueryWSInfo()
{
    try{
        global $db_link;
        $query = "SELECT FieldValue FROM cnrms_ces_v8.`config` WHERE `FieldName` IN ('WSDesKey') ";
        GLogger(__FILE__, __LINE__, __FUNCTION__, '获取WS加密Key，SQL='.$query);

        $content = array();
        $result = $db_link->Query($query);
        if(!$result){
            return false;
        }
        else{
            $WSDesKey = null;
            while(RowFetchObject($row, $result, $db_link))
            {
                $WSDesKey = $row->FieldValue;
            }
            $content = array(
                'WSKey'=>$WSDesKey
            );
        }
        return $content;
    }
    catch(Exception $e){
        $excepStr = '发生异常,异常信息='.$e->getMessage().",错误码=".$e->getCode().",发生在 ".$e->getLine();
        $this->Logger(__FILE__, __LINE__, __FUNCTION__, $excepStr);
        return array('errorCode' => ErrorCode::THREAD,'Msg'=>$excepStr);
    }
}


function GetNuInfo($param)
{
    try{
        global $db_link;

        $aRet = array(
            'errorCode' =>  ErrorCode::SUCCESS
        );

        $whereSQL = " WHERE nud.RouteIDType = '65'  AND nud.Property = 'AlgServerIndex'  ";
        if(isset($param->dbIndex)){
            $whereSQL .= " AND nuv.`Value` = '{$param->dbIndex}' ";
        }

        // 获取所有NUType == 65的网元
        $query = "SELECT nuv.NUID, nud.RouteIDType as NUType,fdb.`Index`, fdb.IP, fdb.`Port`, fdb.`Name`, fdb.`IAType`, fdb.`Description` FROM cnrms_ces_v8.nuproperty_value nuv ";
        $query .= " LEFT JOIN cnrms_ces_v8.nuproperty_description nud ON nud.`Index` = nuv.NUProperty_Description_Index  ";
        $query .= " INNER JOIN cnrms_ces_v8_ia.ia_facedbinfo fdb ON fdb.`Index` = nuv.`Value` ";
        $query .= $whereSQL;

        GLogger(__FILE__, __LINE__, __FUNCTION__, '获取支持的IAU信息，SQL='.$query);
        
        $result = $db_link->Query($query);

        if(!$result){
            $aRet['content'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{
            $NUInfoArr = array();

            while(RowFetchObject($row, $result, $db_link)){
                $NuInfo = new NuInfoStruct();
                $NuInfo->NUID = $row->NUID;
                $NuInfo->NUType = $row->NUType;
                $NuInfo->DBIndex = $row->Index;
                $NuInfo->IAType = $row->IAType;
                $NuInfo->IP = $row->IP;
                $NuInfo->Port = $row->Port;
                $NuInfo->DBName = $row->Name;
                $NuInfo->DBDescription = $row->Description;


                array_push($NUInfoArr, $NuInfo);
            }
            $aRet['content'] = $NUInfoArr;
        }

        return $aRet;
    }
    catch(Exception $e){
        $excepStr = '发生异常,异常信息='.$e->getMessage().",错误码=".$e->getCode().",发生在 ".$e->getLine();
        $this->Logger(__FILE__, __LINE__, __FUNCTION__, $excepStr);
        return array('errorCode' => ErrorCode::THREAD,'Msg'=>$excepStr);
    }
}

ob_end_flush();
exit();
?>