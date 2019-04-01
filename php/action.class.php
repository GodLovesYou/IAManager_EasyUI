<?php

class Action
{
    private function __construct() {}

    // 登录
    CONST Login = "Login";
	
	CONST CheckLogin = "CheckLogin";
	
	CONST KeepAlive = "KeepAlive";
    
    // 登出
    CONST Logout = "Logout";
        
    // 获取IA服务器
    CONST QueryIAServerInfo = 'QueryIAServerInfo';

    // 添加IA服务器
    CONST AddIAServerInfo = 'AddIAServerInfo';

    // 删除IA服务器
    CONST DeleteIAServerInfo = 'DeleteIAServerInfo';

    // 获取人脸集人脸
    CONST QueryFaceInfo = 'QueryFaceInfo';
    
    // 获取所有人脸
    CONST QueryAllFaceInfo = "QueryAllFaceInfo";

    // 获取人脸图片列表
    CONST QueryFaceList = 'QueryFaceList';

    // 获取NU信息
    CONST GetNuInfo = "GetNuInfo";

    // 添加人脸
    CONST AddFace = 'AddFace';

    // 删除人脸
    CONST DeleteFace = 'DeleteFace';

    // 添加人脸基本信息
    CONST AddFaceBaseInfo = 'AddFaceBaseInfo';

    // 修改人脸基本信息
    CONST ModifyFaceBaseInfo = 'ModifyFaceBaseInfo';

    // 删除人脸基本信息
    CONST DeleteFaceBaseInfo = 'DeleteFaceBaseInfo';

    // 添加人脸补录信息
    CONST AddFaceAdditionalInfo = 'AddFaceAdditionalInfo';

    // 修改人脸补录信息
    CONST ModifyFaceAdditionalInfo = 'ModifyFaceAdditionalInfo';

    // 删除人脸补录信息
    CONST DeleteFaceAdditionalInfo = 'DeleteFaceAdditionalInfo';

    // 添加上传附件
    CONST AddUploadFiles = 'AddUploadFiles';

    // 删除文件上传
    CONST DeleteUploadFiles = 'DeleteUploadFiles';
    
    // 检查属性
    CONST CheckAttributes = 'CheckAttributes';

    // 获取组信息
    CONST QueryGroupInfo = 'QueryGroupInfo';

    // 添加组信息
    CONST AddGroupInfo = 'AddGroupInfo';

    // 修改组信息
    CONST ModifyGroupInfo = 'ModifyGroupInfo';

    // 删除组信息
    CONST DeleteGroupInfo = 'DeleteGroupInfo';

    // 获取车牌信息
    CONST QueryCarIDInfo = 'QueryCarIDInfo';

    // 添加车牌信息
    CONST AddCarIDInfo = 'AddCarIDInfo';

    // 修改车牌信息
    CONST ModifyCarIDInfo = 'ModifyCarIDInfo';

    // 删除车牌信息
    CONST DeleteCarIDInfo = 'DeleteCarIDInfo';

    // 获取组下资源信息
    CONST QueryGroupResourceInfo = 'QueryGroupResourceInfo';

    // 添加组下资源信息
    CONST AddGroupResourceInfo = 'AddGroupResourceInfo';

    // 删除组下资源
    CONST DeleteGroupResourceInfo = 'DeleteGroupResourceInfo';


    // 查询人脸识别历史记录
    CONST QueryFaceRecognitionHistroyInfo = 'QueryFaceRecognitionHistroyInfo';

    // 查询人脸检测历史记录
    CONST QueryFaceDetectHistoryInfo = 'QueryFaceDetectHistoryInfo';

    // 查询车牌识别历史记录
    CONST QueryPlateNumberHistroyInfo = 'QueryPlateNumberHistroyInfo';

    // 位置请求
    CONST UnknownAction = 'UnknownAction';
}

?>