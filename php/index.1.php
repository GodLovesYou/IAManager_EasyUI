<?php
/* ------ Power By Crearo. ------ */
ob_start ();
session_set_cookie_params (5 * 24 * 60 * 60);
session_start ();
/// 默认时区
ini_set ( "date.timezone", "Asia/Shanghai" );
header ( 'content-Type:text/html;charset=utf-8' );

include_once('entrydoor.php');
include_once('pgsoap.class.php');
include_once('custom.conf.php');

// 预设操作表
define(CreMediaServer,'cnrms_ces_v8.opmanagerinfo');

switch ($request->action)
{
    case Action::Login:
		$arr_RV = Login($request->content);
		break;
    case Action::QueryAllLibs:
        $arr_RV = QueryAllLibs($request->content);
        break;
    case Action::AddLib:
        $arr_RV = AddLib($request->content);
        break;
    case Action::ClearLib:
        $arr_RV = ClearLib($request->content);
        break;
    case Action::DeleteLib:
        $arr_RV = DeleteLib($request->content);
        break;
    case Action::DeleteImage:
        break;
    case Action::QueryAllEmployeeBaseInfo:
        $arr_RV = QueryAllEmployeeBaseInfo($request->content);
        break;


    case Action::AddEmployeeBaseInfo:
        $arr_RV = AddEmployeeBaseInfo($request->content);
        break;
    case Action::ModifyEmployeeBaseInfo:
        $arr_RV = ModifyEmployeeBaseInfo($request->content);
        break;
    case Action::CheckAttributes:
        $arr_RV = CheckAttributes($request->content);
        break;
    case Action::AddEmployeePhotos:
        $arr_RV = AddEmployeePhotos($request->content);
        break;
    case Action::ModifyExamResult:
        $arr_RV = ModifyExamResult($request->content);
        break;

    case Action::QueryEmployeeInfo:
        $arr_RV = QueryEmployeeInfo($request->content);
        break;
    case Action::QueryEPImagesInfo:
        $arr_RV = QueryEPImagesInfo($request->content);
        break;

    case Action::AddEmployeeAdditionalInfo:
    case Action::ModifyEmployeeAdditionalInfo:
        $arr_RV = AddEmployeeAdditionalInfo($request->content);
        break;
    case Action::QueryEmployeeDetailInfo:
        $arr_RV = QueryEmployeeDetailInfo($request->content);
        break;
    case Action::AddUploadFiles:
        $arr_RV = AddUploadFiles($request->content);
        break;
    case Action::QueryGroup:
        $arr_RV = QueryGroup($request->content);
        break;
    case Action::AddGroup:
        $arr_RV = AddGroup($request->content);
        break;
    case Action::ModifyGroup:
        $arr_RV = ModifyGroup($request->content);
        break;
    case Action::DeleteGroup:
        $arr_RV = DeleteGroup($request->content);
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
 * 检查身份证和工号是否存在
 * @param
 *          checkID         需要检查的身份证
 *          checkNumber     需要检查的工号
 * @return array
 */
function CheckAttributes($param)
{
    try
    {
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

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

        $query = "SELECT COUNT(*) AS SUM FROM ManInfo_TG WHERE (`IDNumber`='$param->checkID' OR `Number`='$param->checkNumber') AND `DeleteFlag` = '0' ";
        GLogger(__FILE__, __LINE__, __FUNCTION__, "查询".$query);

        $result = $db_link->Query($query);
        $count = "";
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
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}


/**
 * 管理员用户登录
 * @param
 *          userID
 *          password
 * @author
 *          zenghx
 * @return array
 */
function Login($param)
{
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

        $query = "SELECT COUNT(*) as Count FROM ".CreMediaServer." WHERE Identity='$param->userID' AND Password='$psw'";

		GLogger(__FILE__, __LINE__, __FUNCTION__, "$query");
		$result = $db_link->Query($query);

		if (!$result)
		{
			$aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
			return $aRet;
		}

		$aRet['content'] = '';
		$db_link->FreeResult();
		return $aRet;
	}catch (Exception $e) {
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 获取所有算法库
 * @param
 *          iaType      算法库类型[0:商汤，1：图麟] default : 0
 * @author
 *          zenghx
 * @return array
 */
function QueryAllLibs($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        $whereSQL = "";
        if(isset($param->iaType))
        {
            $whereSQL .= " AND fdb.`IAType` = '$param->iaType' ";
        }

        $query = "SELECT fdb.`Index`, fdb.`Name`, fdb.`IP`, fdb.`Port`, fdb.`Size`, fdb.`Count`, fdb.`IAType`, fdb.`APPKey`, fdb.`APPSecret`, fdb.`Description`, fdb.`Remark` FROM facedbinfo fdb WHERE 1=1".$whereSQL;

        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询所有库'.$query);

        $dbInfoArr = array();

        $result = $db_link->Query($query);

        if(!$result)
        {
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{

            while(RowFetchObject($row, $result, $db_link))
            {
                $dbInfo = new FaceDBStructInfo();
                $dbInfo->index = $row->Index;
                $dbInfo->ip = $row->IP;
                $dbInfo->port = $row->Port;
                $dbInfo->size = $row->Size;
                $dbInfo->count = $row->Count;
                $dbInfo->iaType = intval($row->IAType);
                $dbInfo->appKey = $row->APPKey;
                $dbInfo->appSecret = $row->APPSecret;
                $dbInfo->remark = $row->Remark;

                if(intval($param->iaType) == 0){
                    $dbInfo->description = $row->Description;
                    $dbInfo->name = $row->Name;
                }else{
                    $dbInfo->description = $row->Name;
                    $dbInfo->name = $row->Description;
                }
                array_push($dbInfoArr, $dbInfo);
            }
        }
        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询所有库'.print_r($dbInfoArr,true));
        $aRet['content'] = $dbInfoArr;
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}


/**
 * 添加算法库（算法集合）
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
function AddLib($param)
{
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
                $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
                return $aRet;
            }

            if(!isset($param->appsecret))
            {
                $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
                return $aRet;
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

        $query = "";
        if(intval($param->iaType) == 0)
        {
            $url = 'http://'.$param->ip.":".$param->port."/verify/target/add";
            GLogger(__FILE__, __LINE__, __FUNCTION__,'post url='.$url);
            $options = array(
                'dbName' => $param->dbname,
                'size'=>'100000'
            );

            $serverResponse = HTTP_CURL_CallInterfaceCommon_ST($url, 'POST', $options);
            $serverResponse = json_decode($serverResponse);

            if($serverResponse->result == 'success')
            {
                $query = "INSERT INTO `facedbInfo` (`Name`, `IP`, `Port`, `Size`, `Count`, `IAType`, `APPKey`, `APPSecret`, `Description`, `Remark`) VALUES ('$param->dbname', '$param->ip', '$param->port', '$param->size', '$param->count','$param->iaType', '$param->appkey', '$param->appsecret', '$param->description', '$param->remark')";
            }
            else{
                $aRet['errorCode'] = ErrorCode::REMOTE_SERVER_REQUEST_ERROR;
                return $aRet;
            }
        }
        else{
            $url = 'http://'.$param->ip.":".$param->port."/face/api/v1/facegroup/create";
            GLogger(__FILE__, __LINE__, __FUNCTION__,'post url='.$url);
            $options = array(
                'name' => $param->dbname
            );
            $serverResponse = HTTP_CURL_CallInterfaceCommon_TL($param->appkey, $param->appsecret, $url, 'POST', $options);
            $serverResponse = json_decode($serverResponse);
            GLogger(__FILE__, __LINE__, __FUNCTION__, 'server response='.print_r($serverResponse,true));

            if(empty($serverResponse->facegroup_token))
            {
                $aRet['errorCode'] = ErrorCode::REMOTE_SERVER_REQUEST_ERROR;
                return $aRet;
            }
            else{
                $token = $serverResponse->facegroup_token;

                $query = "INSERT INTO `facedbInfo` (`Name`, `IP`, `Port`, `Size`, `Count`, `IAType`, `APPKey`, `APPSecret`, `Description`, `Remark`) VALUES ('$token', '$param->ip', '$param->port', $param->size, $param->count,'$param->iaType', '$param->appkey', '$param->appsecret', '$param->dbname', '$param->remark')";
            }
        }

        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询语句='.$query);

        $result = $db_link->Query($query);
        if(!$result){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 清空库
 * @param
 *          ip
 *          port
 *          dbname
 *          iaType
 *          appkey
 *          appsecret
 * @author
 *          zenghx
 * @return array
 */
function ClearLib($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

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

        if(!isset($param->dbname))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->iaType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(intval($param->iaType) == 1)
        {
            if(!isset($param->appkey))
            {
                $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
                return $aRet;
            }

            if(!isset($param->appsecret))
            {
                $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
                return $aRet;
            }
        }

        $sqlArr = array();
        if(intval($param->iaType) == 0)
        {
            $url = 'http://'.$param->ip.":".$param->port."/verify/target/clear";
            GLogger(__FILE__, __LINE__, __FUNCTION__,'post url='.$url);

            $options = array(
                'dbName' => $param->dbname,
            );
            $serverResponse = HTTP_CURL_CallInterfaceCommon_ST($url,'POST',$options);
            $serverResponse = json_decode($serverResponse);

            if($serverResponse->result == "success")
            {
                $sqlArr = array(
                    "UPDATE `facedetecthistory` fdh INNER JOIN `manfeatureinfo` mfi ON fdh.`ManFeatureInfo_Index` = mfi.`Index` INNER JOIN `facedbinfo` fi ON mfi.`FaceDBInfo_Index` = fi.`Index` SET fdh.`DeleteFlag` = '1' WHERE  fi.`IP`= '$param->ip' AND fi.`Port`='$param->port' AND fi.`Name`= '$param->dbname'",
                    "DELETE mfi from `facedbinfo` fi INNER JOIN manfeatureinfo mfi ON fi.`Index` = mfi.`FaceDBInfo_Index` WHERE  fi.`IP`= '$param->ip' AND fi.`Port`='$param->port' AND fi.`Name`= '$param->dbname' AND fi.`IAType`='$param->iaType'"
                );
            }
        }
        else{
            // iatype = 1
            $url = "http://'$param->ip':'$param->port'/face/api/v1/facegroup/'$param->dbname'/empty";
            GLogger(__FILE__, __LINE__, __FUNCTION__,'post url='.$url);

            $options = array();
            $serverResponse = HTTP_CURL_CallInterfaceCommon_TL($param->appkey, $param->appsecret, $url, 'POST',$options);

            $serverResponse = json_decode($serverResponse);
            GLogger(__FILE__, __LINE__, __FUNCTION__,'contents='.print_r($serverResponse,true));

            $sqlArr = array(
                "UPDATE `facedetecthistory` fdh INNER JOIN `manfeatureinfo` mfi ON fdh.`ManFeatureInfo_Index` = mfi.`Index` INNER JOIN `facedbinfo` fi ON mfi.`FaceDBInfo_Index` = fi.`Index` SET fdh.`DeleteFlag` = '1' WHERE  fi.`IP`= '$param->ip' AND fi.`Port`='$param->port' AND fi.`Name`= '$param->dbname'",
                "DELETE mfi from `facedbinfo` fi INNER JOIN manfeatureinfo mfi ON fi.`Index` = mfi.`FaceDBInfo_Index` WHERE  fi.`IP`= '$param->ip' AND fi.`Port`='$param->port' AND fi.`Name`= '$param->dbname' AND fi.`IAType`='$param->iaType'"
            );
        }

        GLogger(__FILE__, __LINE__, __FUNCTION__,'contents='.print_r($sqlArr,true));
        $result = $db_link->Trans_Query($sqlArr);
        if(!result)
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 获取所有员工基本信息
 * @param $param
 * @return array
 */
function QueryAllEmployeeBaseInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        $query = "";
        $query .= "SELECT DISTINCT fdb.`APPKey`, fdb.`APPSecret`, fdb.`IAType`, fdb.`Name` AS DBName, fdb.`IP` AS ServerIP, fdb.`Port` AS ServerPort, mfi.`Image_ID`, mfi.`PicturePath`, mfi.`NUType`, mfi.`NUID`,mfi.`Time`, mi.*, up1.`PicName` as PersonalFileName, up1.`PicPath` as PersonalFilePath,up2.`PicName` as ID_A_FileName,up2.`PicPath` as ID_A_FilePath,up3.`PicName` as ID_B_FileName, up3.`PicPath` as ID_B_FilePath,up4.`PicName` as PaperPhotoName,up4.`PicPath` as PaperPhotoPath,up5.`PicName` as MedicalReportName,up5.`PicPath` as MedicalReportPath, up6.`PicName` as SpecialWorkPermitName,up6.`PicPath` as SpecialWorkPermitPath FROM maninfo_tg mi ";
        $query .= " LEFT JOIN `manfeatureinfo` mfi ON mi.`Index` = mfi.`ManInfo_Index` ";
        $query .= " LEFT JOIN `facedbinfo` fdb ON mfi.`FaceDBInfo_Index` = fdb.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up1 ON mi.`PersonalFile_Index` = up1.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up2 ON mi.`ID_A_Index` = up2.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up3 ON mi.`ID_B_Index` = up3.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up4 ON mi.`PaperPhoto_Index` = up4.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up5 ON mi.`MedicalReport_Index` = up5.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up6 ON mi.`SpecialWorkPermit_Index` = up6.`Index` ";
        $query .= " WHERE 1 = 1 ";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询语句='.$query);

        $result = $db_link->Query($query);
        $userInfoArr = array();
        if(!$result)
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }
        else{
            while(RowFetchObject($row, $result, $db_link))
            {
                $faceManInfo = new FaceManInfoStructInfo();
                $faceManInfo->appkey = $row->APPKey;
                $faceManInfo->appsecret = $row->APPSecret;
                $faceManInfo->DBName = $row->DBName;
                $faceManInfo->serverIP = $row->ServerIP;
                $faceManInfo->serverPort = $row->ServerPort;
                $faceManInfo->imagerID = $row->Image_ID;
                $faceManInfo->iaType = $row->IAType;
                $faceManInfo->picturePath = $row->PicturePath;
                $faceManInfo->index = $row->Index;
                $faceManInfo->number = $row->Number;
                $faceManInfo->jobNumber = $row->JobNumber;
                $faceManInfo->function = $row->Function;
                $faceManInfo->projectDepartment = $row->ProjectDepartment;
                $faceManInfo->name = $row->Name;
                $faceManInfo->sex = $row->Sex;
                $faceManInfo->group = $row->Group;
                $faceManInfo->subcontractTream = $row->SubcontractTream;
                $faceManInfo->dept = $row->Dept;
                $faceManInfo->company = $row->Company;
                $faceManInfo->birthDate = $row->BirthDate;
                $faceManInfo->IDNumber = $row->IDNumber;
                $faceManInfo->personFile_Index = $row->PersonalFile_Index;
                $faceManInfo->personFileName = $row->PersonalFileName;
                $faceManInfo->personFilePath = $row->PersonalFilePath;
                $faceManInfo->ID_A_Index = $row->ID_A_Index;
                $faceManInfo->ID_A_FileName = $row->ID_A_FileName;
                $faceManInfo->ID_A_FilePath = $row->ID_A_FilePath;
                $faceManInfo->ID_B_Index = $row->ID_B_Index;
                $faceManInfo->ID_B_FileName = $row->ID_B_FileName;
                $faceManInfo->ID_B_FilePath = $row->ID_B_FilePath;
                $faceManInfo->paperPhoto_Index = $row->PaperPhoto_Index;
                $faceManInfo->paperPhotoName = $row->PaperPhotoName;
                $faceManInfo->paperPhotoPath = $row->PaperPhotoPath;
                $faceManInfo->medicalReport_Index = $row->MedicalReport_Index;
                $faceManInfo->medicalReportName = $row->MedicalReportName;
                $faceManInfo->medicalReportPath = $row->MedicalReportPath;
                $faceManInfo->specialWorkPermit_Index = $row->SpecialWorkPermit_Index;
                $faceManInfo->specialWorkPermitName = $row->SpecialWorkPermitName;
                $faceManInfo->specialWorkPermitPath = $row->SpecialWorkPermitPath;
                $faceManInfo->examResult = $row->ExamResult;
                $faceManInfo->auditBeginTime = $row->AuditBeginTime;
                $faceManInfo->auditEndTime = $row->AuditEndTime;
                $faceManInfo->auditStatus = $row->AuditStatus;
                $faceManInfo->auditor = $row->Auditor;
                $faceManInfo->auditTime = $row->AuditTime;
                $faceManInfo->illegalInformation = $row->IllegalInformation;
                $faceManInfo->description = $row->Description;
                $faceManInfo->deleteFlag = $row->DeleteFlag;
                $faceManInfo->modifyDateTime = $row->ModifyDateTime;
                $faceManInfo->remark = $row->Remark;

                array_push($userInfoArr, $faceManInfo);
            }
        }

        $aRet['content'] = $userInfoArr;
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 获取员工信息
 * @param
 *          ip
 *          port
 *          dbname
 *          iaType
 * @author
 *          zenghx
 * @return array
 */
function QueryEmployeeInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );
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

        if(!isset($param->dbname))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->iaType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "";
        $query .= "SELECT DISTINCT fdb.`APPKey`, fdb.`APPSecret`, fdb.`IAType`, fdb.`Name` AS DBName, fdb.`IP` AS ServerIP, fdb.`Port` AS ServerPort, mfi.`Image_ID`, mfi.`PicturePath`, mfi.`NUType`, mfi.`NUID`,mfi.`Time`, mi.*, up1.`PicName` as PersonalFileName, up1.`PicPath` as PersonalFilePath,up2.`PicName` as ID_A_FileName,up2.`PicPath` as ID_A_FilePath,up3.`PicName` as ID_B_FileName, up3.`PicPath` as ID_B_FilePath,up4.`PicName` as PaperPhotoName,up4.`PicPath` as PaperPhotoPath,up5.`PicName` as MedicalReportName,up5.`PicPath` as MedicalReportPath, up6.`PicName` as SpecialWorkPermitName,up6.`PicPath` as SpecialWorkPermitPath FROM facedbinfo fdb ";
        $query .= " INNER JOIN `manfeatureinfo` mfi ON fdb.`Index` = mfi.`FaceDBInfo_Index` ";
        $query .= " INNER JOIN `maninfo_tg` mi ON mfi.`ManInfo_Index` = mi.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up1 ON mi.`PersonalFile_Index` = up1.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up2 ON mi.`ID_A_Index` = up2.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up3 ON mi.`ID_B_Index` = up3.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up4 ON mi.`PaperPhoto_Index` = up4.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up5 ON mi.`MedicalReport_Index` = up5.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up6 ON mi.`SpecialWorkPermit_Index` = up6.`Index` ";
        $query .= " WHERE 1 = 1 ";
        $query .= " AND fdb.`IP` = '$param->ip' ";
        $query .= " AND fdb.`Port` = '$param->port' ";
        $query .= " AND fdb.`Name` = '$param->dbname' ";
        $query .= " AND fdb.`IAType`= '$param->iaType' ";
        $query .= " AND mfi.`DeleteFlag` = '0' ";
        $query .= " AND mi.`DeleteFlag` = '0' ";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询语句='.$query);

        $result = $db_link->Query($query);
        $userInfoArr = array();
        if(!$result)
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }
        else{
            while(RowFetchObject($row, $result, $db_link))
            {
                $faceManInfo = new FaceManInfoStructInfo();
                $faceManInfo->appkey = $row->APPKey;
                $faceManInfo->appsecret = $row->APPSecret;
                $faceManInfo->DBName = $row->DBName;
                $faceManInfo->serverIP = $row->ServerIP;
                $faceManInfo->serverPort = $row->ServerPort;
                $faceManInfo->imagerID = $row->Image_ID;
                $faceManInfo->iaType = $row->IAType;
                $faceManInfo->picturePath = $row->PicturePath;
                $faceManInfo->index = $row->Index;
                $faceManInfo->number = $row->Number;
                $faceManInfo->jobNumber = $row->JobNumber;
                $faceManInfo->function = $row->Function;
                $faceManInfo->projectDepartment = $row->ProjectDepartment;
                $faceManInfo->name = $row->Name;
                $faceManInfo->sex = $row->Sex;
                $faceManInfo->group = $row->Group;
                $faceManInfo->subcontractTream = $row->SubcontractTream;
                $faceManInfo->dept = $row->Dept;
                $faceManInfo->company = $row->Company;
                $faceManInfo->birthDate = $row->BirthDate;
                $faceManInfo->IDNumber = $row->IDNumber;
                $faceManInfo->personFile_Index = $row->PersonalFile_Index;
                $faceManInfo->personFileName = $row->PersonalFileName;
                $faceManInfo->personFilePath = $row->PersonalFilePath;
                $faceManInfo->ID_A_Index = $row->ID_A_Index;
                $faceManInfo->ID_A_FileName = $row->ID_A_FileName;
                $faceManInfo->ID_A_FilePath = $row->ID_A_FilePath;
                $faceManInfo->ID_B_Index = $row->ID_B_Index;
                $faceManInfo->ID_B_FileName = $row->ID_B_FileName;
                $faceManInfo->ID_B_FilePath = $row->ID_B_FilePath;
                $faceManInfo->paperPhoto_Index = $row->PaperPhoto_Index;
                $faceManInfo->paperPhotoName = $row->PaperPhotoName;
                $faceManInfo->paperPhotoPath = $row->PaperPhotoPath;
                $faceManInfo->medicalReport_Index = $row->MedicalReport_Index;
                $faceManInfo->medicalReportName = $row->MedicalReportName;
                $faceManInfo->medicalReportPath = $row->MedicalReportPath;
                $faceManInfo->specialWorkPermit_Index = $row->SpecialWorkPermit_Index;
                $faceManInfo->specialWorkPermitName = $row->SpecialWorkPermitName;
                $faceManInfo->specialWorkPermitPath = $row->SpecialWorkPermitPath;
                $faceManInfo->examResult = $row->ExamResult;
                $faceManInfo->auditBeginTime = $row->AuditBeginTime;
                $faceManInfo->auditEndTime = $row->AuditEndTime;
                $faceManInfo->auditStatus = $row->AuditStatus;
                $faceManInfo->auditor = $row->Auditor;
                $faceManInfo->auditTime = $row->AuditTime;
                $faceManInfo->illegalInformation = $row->IllegalInformation;
                $faceManInfo->description = $row->Description;
                $faceManInfo->deleteFlag = $row->DeleteFlag;
                $faceManInfo->modifyDateTime = $row->ModifyDateTime;
                $faceManInfo->remark = $row->Remark;

                array_push($userInfoArr, $faceManInfo);
            }
        }

        $aRet['content'] = $userInfoArr;
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 获取员工的图片
 * @param $param
 * @return array
 */
function QueryEPImagesInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

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

        if(!isset($param->dbname))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->iaType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->imageID))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(intval($param->iaType) == 1)
        {
            if(!isset($param->appkey))
            {
                $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
                return $aRet;
            }

            if(!isset($param->appsecret))
            {
                $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
                return $aRet;
            }
        }

        $base64image = "";
        if(intval($param->iaType) == 0)
        {
            $url = "http://".$param->ip.":".$param->port."/verify/face/gets?imageId=".$param->imageID;
            GLogger(__FILE__, __LINE__, __FUNCTION__,'send face server url='.$url);

            $serverResponse = HTTP_CURL_CallInterfaceCommon_ST($url,'GET','');
            $base64image = base64_encode($serverResponse);
        }
        else{
            $url = "http://".$param->ip.":".$param->port."/face/api/v1/facegroup/$param->dbname/".$param->imageID;
            GLogger(__FILE__, __LINE__, __FUNCTION__,'send face server url='.$url);

            $serverResponse = HTTP_CURL_CallInterfaceCommon_TL($param->appkey,$param->appsecret, $url,'GET','');
            $serverResponse = json_decode($serverResponse);
            GLogger(__FILE__, __LINE__, __FUNCTION__,'server response='.print_r($serverResponse,true));
            if(empty($serverResponse->image))
            {
                $aRet['errorCode'] = ErrorCode::REMOTE_SERVER_REQUEST_ERROR;
                return $aRet;
            }
            else{
                $base64image = $serverResponse->image;
            }
        }

        $aRet['content'] = $base64image;
        return $aRet;
    }
    catch(Exception $e)
    {
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加员工额外信息
 * @param
 *          userIndex       用户索引
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
function AddEmployeeAdditionalInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->userIndex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->personalFile_Index))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->id_a_index))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->id_b_index))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->paperPhoto_Index))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->examResult))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->medicalReport_Index))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->SpecialWorkPermit_Index))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->AuditStatus))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->AuditBeginTime))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->AuditEndTime))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->Auditor))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->AuditTime))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->illegalInformation))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->description))
        {
            $param->description = '';
        }

        if(!isset($param->remark))
        {
            $param->remark = '';
        }

        $query = "";

        $query .= " UPDATE ManInfo_TG SET";
        $query .= " `PersonalFile_Index` = '$param->personalFile_Index', ";
        $query .= " `ID_A_Index` = '$param->id_a_index', ";
        $query .= " `ID_B_Index` = '$param->id_b_index', ";
        $query .= " `PaperPhoto_Index` = '$param->paperPhoto_Index', ";
        $query .= " `MedicalReport_Index` = '$param->medicalReport_Index', ";
        $query .= " `SpecialWorkPermit_Index` = '$param->SpecialWorkPermit_Index', ";
        $query .= " `AuditStatus` = '$param->AuditStatus', ";
        $query .= " `AuditBeginTime` = '$param->AuditBeginTime', ";
        $query .= " `AuditEndTime` = '$param->AuditEndTime', ";
        $query .= " `Auditor` = '$param->Auditor', ";
        $query .= " `AuditTime` = '$param->AuditTime', ";
        $query .= " `IllegalInformation`='$param->illegalInformation', ";
        $query .= " `Description` = '$param->description', ";
        $query .= " `Remark` = '$param->remark' ";
        $query .= " WHERE `Index`='$param->userIndex' ";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询语句='.$query);

        $result = $db_link->Query($query);

        if(!result){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 获取单一员工信息
 * @param
 *          userIndex
 *          ip
 *          port
 *          dbname
 *          iaType
 * @author
 *          zenghx
 * @return array
 */
function QueryEmployeeDetailInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->userIndex))
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

        if(!isset($param->dbname))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->iaType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "";
        $query .= "SELECT DISTINCT fdb.`APPKey`, fdb.`APPSecret`, fdb.`IAType`, fdb.`Name` AS DBName, fdb.`IP` AS ServerIP, fdb.`Port` AS ServerPort, mfi.`Image_ID`, mfi.`PicturePath`, mfi.`NUType`, mfi.`NUID`,mfi.`Time`, mi.*, up1.`PicName` as PersonalFileName, up1.`PicPath` as PersonalFilePath,up2.`PicName` as ID_A_FileName,up2.`PicPath` as ID_A_FilePath,up3.`PicName` as ID_B_FileName, up3.`PicPath` as ID_B_FilePath,up4.`PicName` as PaperPhotoName,up4.`PicPath` as PaperPhotoPath,up5.`PicName` as MedicalReportName,up5.`PicPath` as MedicalReportPath, up6.`PicName` as SpecialWorkPermitName,up6.`PicPath` as SpecialWorkPermitPath FROM facedbinfo fdb ";
        $query .= " INNER JOIN `manfeatureinfo` mfi ON fdb.`Index` = mfi.`FaceDBInfo_Index` ";
        $query .= " INNER JOIN `maninfo_tg` mi ON mfi.`ManInfo_Index` = mi.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up1 ON mi.`PersonalFile_Index` = up1.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up2 ON mi.`ID_A_Index` = up2.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up3 ON mi.`ID_B_Index` = up3.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up4 ON mi.`PaperPhoto_Index` = up4.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up5 ON mi.`MedicalReport_Index` = up5.`Index` ";
        $query .= " LEFT JOIN `uploadpicinfo` up6 ON mi.`SpecialWorkPermit_Index` = up6.`Index` ";
        $query .= " WHERE 1 = 1 ";
        $query .= " AND fdb.`IP` = '$param->ip' ";
        $query .= " AND fdb.`Port` = '$param->port' ";
        $query .= " AND fdb.`Name` = '$param->dbname' ";
        $query .= " AND fdb.`IAType`= '$param->iaType' ";
        $query .= " AND mi.`Index`= '$param->userIndex' ";
        $query .= " AND mfi.`DeleteFlag` = '0' ";
        $query .= " AND mi.`DeleteFlag` = '0' ";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询语句='.$query);

        $result = $db_link->Query($query);
        $userInfoArr = array();
        if(!$result)
        {
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{
            while(RowFetchObject($row, $result, $db_link))
            {
                $faceManInfo = new FaceManInfoStructInfo();
                $faceManInfo->appkey = $row->APPKey;
                $faceManInfo->appsecret = $row->APPSecret;
                $faceManInfo->DBName = $row->DBName;
                $faceManInfo->serverIP = $row->ServerIP;
                $faceManInfo->serverPort = $row->ServerPort;
                $faceManInfo->imagerID = $row->Image_ID;
                $faceManInfo->iaType = $row->IAType;
                $faceManInfo->picturePath = $row->PicturePath;
                $faceManInfo->index = $row->Index;
                $faceManInfo->number = $row->Number;
                $faceManInfo->jobNumber = $row->JobNumber;
                $faceManInfo->function = $row->Function;
                $faceManInfo->projectDepartment = $row->ProjectDepartment;
                $faceManInfo->name = $row->Name;
                $faceManInfo->sex = $row->Sex;
                $faceManInfo->group = $row->Group;
                $faceManInfo->subcontractTream = $row->SubcontractTream;
                $faceManInfo->dept = $row->Dept;
                $faceManInfo->company = $row->Company;
                $faceManInfo->birthDate = $row->BirthDate;
                $faceManInfo->IDNumber = $row->IDNumber;
                $faceManInfo->personFile_Index = $row->PersonalFile_Index;
                $faceManInfo->personFileName = $row->PersonalFileName;
                $faceManInfo->personFilePath = $row->PersonalFilePath;
                $faceManInfo->ID_A_Index = $row->ID_A_Index;
                $faceManInfo->ID_A_FileName = $row->ID_A_FileName;
                $faceManInfo->ID_A_FilePath = $row->ID_A_FilePath;
                $faceManInfo->ID_B_Index = $row->ID_B_Index;
                $faceManInfo->ID_B_FileName = $row->ID_B_FileName;
                $faceManInfo->ID_B_FilePath = $row->ID_B_FilePath;
                $faceManInfo->paperPhoto_Index = $row->PaperPhoto_Index;
                $faceManInfo->paperPhotoName = $row->PaperPhotoName;
                $faceManInfo->paperPhotoPath = $row->PaperPhotoPath;
                $faceManInfo->medicalReport_Index = $row->MedicalReport_Index;
                $faceManInfo->medicalReportName = $row->MedicalReportName;
                $faceManInfo->medicalReportPath = $row->MedicalReportPath;
                $faceManInfo->specialWorkPermit_Index = $row->SpecialWorkPermit_Index;
                $faceManInfo->specialWorkPermitName = $row->SpecialWorkPermitName;
                $faceManInfo->specialWorkPermitPath = $row->SpecialWorkPermitPath;
                $faceManInfo->examResult = $row->ExamResult;
                $faceManInfo->auditBeginTime = $row->AuditBeginTime;
                $faceManInfo->auditEndTime = $row->AuditEndTime;
                $faceManInfo->auditor = $row->Auditor;
                $faceManInfo->auditTime = $row->AuditTime;
                $faceManInfo->auditStatus = $row->AuditStatus;
                $faceManInfo->illegalInformation = $row->IllegalInformation;
                $faceManInfo->description = $row->Description;
                $faceManInfo->deleteFlag = $row->DeleteFlag;
                $faceManInfo->modifyDateTime = $row->ModifyDateTime;
                $faceManInfo->remark = $row->Remark;

                array_push($userInfoArr, $faceManInfo);
            }
        }

        $aRet['content'] = $userInfoArr;
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加文件上传
 * @param $param
 * @return array|bool|string
 */
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

            if (!in_array($file_ext, array('jpg', 'bmp', 'png', 'gif')))
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
                    $query = "INSERT INTO UploadPicInfo(`PicName`,`PicType`,`PicPath`,`Description`,`DeleteFlag`,`ModifyDateTime`,`Remark`) VALUES('$saveName','0','$savePath','',0,now(),'')";
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
            'errorCode' => ErrorCode::THREAD
        );
    }
}


/**
 * 删除算法库
 * @param
 *          ip
 *          port
 *          dbname
 *          iaType
 * @return array
 */
function DeleteLib($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

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

        if(!isset($param->dbname))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->iaType))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(intval($param->iaType) == 0)
        {
            $url = 'http://'.$param->ip.":".$param->port."/verify/target/deletes";
            GLogger(__FILE__, __LINE__, __FUNCTION__,'post url='.$url);
            $options = array(
                'dbName' => $param->dbname,
            );
            $options = json_encode($options);
            GLogger(__FILE__, __LINE__, __FUNCTION__,'post data='.print_r($options,true));

            $serverResponse = HTTP_CURL_CallInterfaceCommon_ST($url,'POST',$options);

            $serverResponse = json_decode($serverResponse);
            GLogger(__FILE__, __LINE__, __FUNCTION__,'server response result='.$serverResponse->result);

            if($serverResponse->result == "success")
            {
                // 这里通过事务的方式去处理删除和更新表的操作
                $sqlArr = array(
                    "UPDATE `facedetecthistory` fdh INNER JOIN `manfeatureinfo` mfi ON fdh.`ManFeatureInfo_Index` = mfi.`Index` INNER JOIN `facedbinfo` fi ON mfi.`FaceDBInfo_Index` = fi.`Index` SET fdh.`DeleteFlag` = '1' WHERE  fi.`IP`= '$param->ip' AND fi.`Port`='$param->port' AND fi.`Name`= '$param->dbname'",
                    "DELETE fi,mfi from `facedbinfo` fi INNER JOIN manfeatureinfo mfi ON fi.`Index` = mfi.`FaceDBInfo_Index` WHERE  fi.`IP`= '$param->ip' AND fi.`Port`='$param->port' AND fi.`Name`= '$param->dbname' AND fi.`IAType` = '$param->iaType' "
                );
                $t_result = $db_link->Trans_Query($sqlArr);
                if(!$t_result){
                    $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
                    return $aRet;
                }
            }
            else{
                $aRet['errorCode'] = ErrorCode::REMOTE_SERVER_REQUEST_ERROR;
                return $aRet;
            }
        }
        else{
            $url = 'http://'.$param->ip.":".$param->port."/face/api/v1/facegroup/delete";

            $options = array(
                "facegroup_token" => '$facegroup_token',
                "check_empty" => 0
            );

            GLogger(__FILE__, __LINE__, __FUNCTION__,'Send ia server attributes ='.print_r($options,true));

            $serverResponse = HTTP_CURL_CallInterfaceCommon_TL($url,'POST',$options);
            $serverResponse = json_decode($serverResponse);

            GLogger(__FILE__, __LINE__, __FUNCTION__,'serverResponse='.print_r($serverResponse,true));

            if(!empty($serverResponse->error_code))
            {
                $aRet['errorCode'] = ErrorCode::REMOTE_SERVER_REQUEST_ERROR;
                return $aRet;
            }
            else{
                $query = "DELETE FROM facedbinfo WHERE Description = '$param->dbname' ";
                $result = $db_link->Query($query);
                if(!$result){
                    $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
                    return $aRet;
                }
            }
        }
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加员工基本信息
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
function AddEmployeeBaseInfo($param)
{
    try{
        global  $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->number))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->jobNumber))
        {
            $param->jobNumber = NULL;
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

        if(!isset($param->group))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->sex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->subcontractTeam))
        {
            $param->subcontractTeam = NULL;
        }

        if(!isset($param->dept))
        {
            $param->dept = NULL;
        }

        if(!isset($param->company))
        {
            $param->company = NULL;
        }

        if(!isset($param->id))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->birthDate))
        {
            $param->birthDate = NULL;
        }

        if(!isset($param->description))
        {
            $param->description = NULL;
        }

        $psoap = new PGSoap(GSOAP_CUSTOM_URL, GSOAP_DES_KEY);

        $psoap->SetGlobalValidUser('admin', 'system');

        $sendXML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<Msg Name="CUCommonMsgReq" DomainRoad="">
    <Cmd Type="CTL" Prio="1" EPID="system">
        <DstRes Type="SC" Idx="0" OptID="CTL_IA_AddManInfo">
            <Param Number="$param->number" Name="$param->name" Function="$param->function" ProjectDepartment="$param->projectDepartment" Group="$param->group" Sex="$param->sex" IDNumber="$param->id"></Param>
        </DstRes>
    </Cmd>
</Msg>
XML;

        GLogger(__FILE__,__LINE__,__FUNCTION__, 'send XML='.$sendXML);

        $result = $psoap->TransCustomMessage($sendXML);
        GLogger(__FILE__,__LINE__,__FUNCTION__, 'result='.print_r($result, true));
        if($result->rv !== "0x0000"){
            $aRet = array(
                'errorCode' => ErrorCode::REMOTE_SERVER_REQUEST_ERROR
            );
        }else{
            GLogger(__FILE__,__LINE__,__FUNCTION__, 'send XML=成功');
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}


/**
 * 添加用户图片
 * @param
 *          id
 * @return array|bool
 */
function AddEmployeePhotos($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->id)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $imageBase64 = "";

        if(!empty($_FILES)){
            $photo = $_FILES['EmployeeImage'];

            $file_ext = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));

            if (!in_array($file_ext, array('jpg', 'bmp', 'png', 'gif'))){
                GLogger(__FILE__, __LINE__, __FUNCTION__, "extension($file_ext) not allowed.");
                return $aRet['errorCode'] = ErrorCode::UPLOAD_PHOTO_EXT_ERROR;
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
            return false;
        }
        $formatBase64 = "<![CDATA[".$imageBase64."]]>";

        $formatBase64 = str_replace(array("\r\n", "\r", "\n"), "", $formatBase64);

        $sendXML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<Msg Name="CUCommonMsgReq" DomainRoad="">
    <Cmd Type="CTL" Prio="1" EPID="system">
        <DstRes Type="SC" Idx="0" OptID="CTL_IA_AddFacePic">
            <Param IDNumber="$param->id" Force="1" >
                <Pic>$formatBase64</Pic>
            </Param>
        </DstRes>
    </Cmd>
</Msg>
XML;

        $psoap = new PGSoap(GSOAP_CUSTOM_URL, GSOAP_DES_KEY);

        $psoap->SetGlobalValidUser('admin', 'system');

        GLogger(__FILE__,__LINE__,__FUNCTION__, 'send XML='.$sendXML);

        $result = $psoap->TransCustomMessage($sendXML);
        if($result->rv !== "0x0000"){
            $aRet = array(
                'errorCode' => ErrorCode::REMOTE_SERVER_REQUEST_ERROR
            );
        }

        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 修改员工基本信息
 * @param
 *          number
 *          function
 *          projectDepartment
 *          name
 *          group
 *          sex
 *          id
 *          description
 * @return array
 */
function ModifyEmployeeBaseInfo($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->number))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->jobNumber))
        {
            $param->jobNumber = NULL;
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

        if(!isset($param->group))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->sex))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->subcontractTeam))
        {
            $param->subcontractTeam = NULL;
        }

        if(!isset($param->dept))
        {
            $param->dept = NULL;
        }

        if(!isset($param->company))
        {
            $param->company = NULL;
        }

        if(!isset($param->id))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->birthDate))
        {
            $param->birthDate = NULL;
        }

        if(!isset($param->description))
        {
            $param->description = NULL;
        }

        $psoap = new PGSoap(GSOAP_CUSTOM_URL, GSOAP_DES_KEY);

        $psoap->SetGlobalValidUser('admin', 'system');

        $sendXML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<Msg Name="CUCommonMsgReq" DomainRoad="">
    <Cmd Type="CTL" Prio="1" EPID="system">
        <DstRes Type="SC" Idx="0" OptID="CTL_IA_UpdateManInfo">
            <Param Number="$param->number" Name="$param->name" Function="$param->function" ProjectDepartment="$param->projectDepartment" Group="$param->group" Sex="$param->sex" IDNumber="$param->id"></Param>
        </DstRes>
    </Cmd>
</Msg>
XML;

        GLogger(__FILE__,__LINE__,__FUNCTION__, 'send XML='.$sendXML);

        $result = $psoap->TransCustomMessage($sendXML);

        if($result->rv !== "0x0000"){
            $aRet = array(
                'errorCode' => ErrorCode::REMOTE_SERVER_REQUEST_ERROR
            );
        }
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 修改考试成绩
 * @param
 *          id              身份证
 *          examResult      考试成绩
 * @return array
 */
function ModifyExamResult($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode'=>ErrorCode::SUCCESS
        );

        if(!isset($param->id)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        if(!isset($param->examResult)){
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $psoap = new PGSoap(GSOAP_CUSTOM_URL, GSOAP_DES_KEY);

        $psoap->SetGlobalValidUser('admin', 'system');

        $sendXML = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<Msg Name="CUCommonMsgReq" DomainRoad="">
    <Cmd Type="CTL" Prio="1" EPID="system">
        <DstRes Type="SC" Idx="0" OptID="CTL_IA_AddExamResult">
            <Param IDNumber="$param->id" ExamResult="$param->examResult"></Param>
        </DstRes>
    </Cmd>
</Msg>
XML;

        GLogger(__FILE__,__LINE__,__FUNCTION__, 'send XML='.$sendXML);

        $result = $psoap->TransCustomMessage($sendXML);

        if($result->rv !== "0x0000"){
            $aRet = array(
                'errorCode' => ErrorCode::REMOTE_SERVER_REQUEST_ERROR
            );
        }
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' =>ErrorCode::THREAD
        );
    }
}

/**
 * 获取所有组别
 */
function QueryGroup($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        $query = "SELECT * FROM groupinfo";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询所有库'.$query);

        $groupInfoArr = array();

        $result = $db_link->Query($query);

        if(!$result)
        {
            $aRet['errorCode'] = ErrorCode::DB_MYSQL_OPERATE_FAILED;
            return $aRet;
        }
        else{

            while(RowFetchObject($row, $result, $db_link))
            {
                $dbInfo = new GroupInfo();
                $dbInfo->index = $row->Index;
                $dbInfo->group = $row->Group;
                $dbInfo->parent_index = $row->Parent_Index;
                $dbInfo->remark = $row->Remark;
                array_push($groupInfoArr, $dbInfo);
            }
        }
        GLogger(__FILE__,__LINE__,__FUNCTION__,'查询所有库'.print_r($groupInfoArr,true));
        $aRet['content'] = $groupInfoArr;
        return $aRet;
    }
    catch(Exception $e){
        return array(
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 添加组别
 */
function AddGroup($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->group))
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

        $query = "INSERT INTO groupinfo (`Group`, `Parent_Index`, `Remark`) VALUES ('$param->group','$parent_index' ,'$remark')";

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
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 修改组别
 */
function ModifyGroup($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );
        if(!isset($param->group))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }
        if(!isset($param->index))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "UPDATE groupinfo SET `Group` = '$param->group' WHERE `Index` = '$param->index'";

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
            'errorCode' => ErrorCode::THREAD
        );
    }
}

/**
 * 删除组别
 */
function DeleteGroup($param)
{
    try{
        global $db_link;
        $aRet = array(
            'errorCode' => ErrorCode::SUCCESS
        );

        if(!isset($param->index))
        {
            $aRet['errorCode'] = ErrorCode::PARAM_NOT_VALID;
            return $aRet;
        }

        $query = "DELETE FROM groupinfo WHERE `Index` = '$param->index'";

        GLogger(__FILE__,__LINE__,__FUNCTION__,'删除库'.$query);

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
            'errorCode' => ErrorCode::THREAD
        );
    }
}


ob_end_flush();
exit();
?>