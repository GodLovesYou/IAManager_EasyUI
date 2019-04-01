<?php

class FaceDBStructInfo
{
    public $index;
    public $name;
    public $ip;
    public $port;
    public $size;
    public $count;
    public $iaType;
    public $appKey;
    public $appSecret;
    public $description;
    public $remark;
}

class DBManStructInfo
{
    public $db_index;
    public $db_name;
    public $db_ip;
    public $db_port;
    public $db_size;
    public $db_count;
    public $db_type;
    public $db_key;
    public $db_secret;
    public $db_description;
    public $db_remark;
    public $user_manInfoEx_Index;
    public $user_index;
    public $user_sourcePicturePath;
    public $user_sourcePicturePath_sl;
    public $user_number;
    public $user_function;
    public $user_projectDepartment;
    public $user_name;
    public $user_sex;
    public $user_idNumber;
    public $user_groupName;
    public $user_groupIndex;
    public $user_company;
    public $user_jobNumber;
    public $user_subcontractTeam;
    public $user_dept;
    public $user_birthDate;
    public $user_personalFileIndex;
    public $user_personalFileName;
    public $user_PersonalPath;
    public $user_ID_A_Index;
    public $user_ID_A_FileName;
    public $user_ID_A_Path;
    public $user_ID_B_Index;
    public $user_ID_B_FileName;
    public $user_ID_B_Path;
    public $user_paperPhoto_Index;
    public $user_paperPhotoFileName;
    public $user_paperPhotoPath;
    public $user_examResult;
    public $user_medicalReportIndex;
    public $user_medicalReportFileName;
    public $user_medicalReportPath;
    public $user_specialWorkPermit_Index;
    public $user_specialWorkPermitFileName;
    public $user_specialWorkPermitPath;
    public $user_auditStatus;
    public $user_auditBeginTime;
    public $user_auditEndTime;
    public $user_auditor;
    public $user_auditTime;
    public $user_IllegalInformation;
}

class GroupInfo
{
    public $index;
    public $group;
    public $parent_index;
    public $remark;
}

class PlateInfoStruct
{
    public $Index;
    public $PicturePath;
    public $PicturePath_sl;
    public $PlateColor;
    public $PlateNo;
    public $PlateNoAttach;
    public $VehicleClass;
    public $VehicleBrand;
    public $VehicleModel;
    public $VehicleColor;
    public $DeleteFlag;
    public $ModifyDateTime;
    public $Remark;
}

class RecognitionHistoryInfoStruct{
    public $Index;
    public $Time;
    public $TimeStamp;
    public $PUID;
    public $Idx;
    public $ManFeatureInfo_Index;
    public $FaceScore;
    public $Facerect_X;
    public $Facerect_Y;
    public $Facerect_W;
    public $Facerect_H;
    public $Costtime;
    public $NUType;
    public $NUID;
    public $GroupDetectHistory_Index;
    public $Latitude;
    public $Longtitude;
    public $DeleteFlag;
    public $DeleteTime;
    public $ModifyDateTime;
    public $Remark;
    public $sourcePicturePath;
    public $sourcePicturePath_sl;
    public $HttpPicUrl_Source;
    public $HttpPicUrl_Preview;
    public $UserName;
    public $IDNumber;
    public $Number;
}


class DetectHistoryInfoStruct{
    public $Index;
    public $Time;
    public $TimeStamp;
    public $PUID;
    public $Idx;
    public $FaceScore;
    public $Feature;
    public $Facerect_X;
    public $Facerect_Y;
    public $Facerect_W;
    public $Facerect_H;
    public $Costtime;
    public $NUType;
    public $NUID;
    public $GroupDetectHistory_Index;
    public $Latitude;
    public $Longtitude;
    public $SkinColor;
    public $HairStyle;
    public $HairColor;
    public $FaceStyle;
    public $FacialFeature;
    public $PhysicalFeature;
    public $RespiratorColor;
    public $CapStyle;
    public $CapColor;
    public $GlassStyle;
    public $GlassColor;
    public $Attitude;
    public $EyebrowStyle;
    public $NoseStyle;
    public $MustacheStyle;
    public $LipStyle;
    public $WrinklePouch;
    public $AcneStain;
    public $FreckleBirthmark;
    public $ScarDimple;
    public $OtherFeature;
    public $DeleteFlag;
    public $DeleteTime;
    public $sourcePicturePath;
    public $sourcePicturePath_sl;
    public $HttpPicUrl_Source;
    public $HttpPicUrl_Preview;
    public $ModifyDateTime;
    public $Remark;
}

class CarHistoryInfoStruct{
    public $Index;
    public $Time;
    public $PUID;
    public $Idx;
    public $Platerect_x;
    public $Platerect_y;
    public $Platerect_w;
    public $Platerect_h;
    public $Costtime;
    public $PicturePath;
    public $PicturePath_sl;
    public $NUType;
    public $NUID;
    public $GroupDetectHistory_Index;
    public $Latitude;
    public $Longtitude;
    public $PlateColor;
    public $PlateNo;
    public $PlateNoAttach;
    public $Speed;
    public $Direction;
    public $VehicleClass;
    public $VehicleBrand;
    public $VehicleModel;
    public $VehicleColor;
    public $PlateReliability;
    public $BrandReliability;
    public $DeleteFlag;
    public $DeleteTime;
    public $ModifyDateTime;
    public $Remark;
}

class NuInfoStruct{
    public $NUID;
    public $NUType;
    public $DBIndex;
    public $IP;
    public $IAType;
    public $Port;
    public $DBName;
    public $DBDescription;
}

?>