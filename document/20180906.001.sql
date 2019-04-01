set NAMES UTF8;

drop database if exists CNRMS_CES_V8_IA;
CREATE DATABASE CNRMS_CES_V8_IA;

use CNRMS_CES_V8_IA;

DROP TABLE if exists  `IA_FaceDBInfo`;
CREATE TABLE `IA_FaceDBInfo`(
    `Index` bigint(8) unsigned  NOT NULL AUTO_INCREMENT COMMENT '索引',
    `Name` varchar(255) NOT NULL COMMENT '人脸集名称',
    `IP` varchar(16) NOT NULL DEFAULT '127.0.0.1' COMMENT 'IA服务器所在的IP',
    `Port` varchar(12) NOT NULL DEFAULT '80' COMMENT 'IA服务器的端口号',
    `Size` bigint(8) unsigned NULL DEFAULT 10000 COMMENT '库最大容量',
    `Count` bigint(8) unsigned NULL DEFAULT 0 COMMENT '库内图片的数量',
    `IAType` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'IA算法类型0：ST 1：TL',
    `APPKey` varchar(255) NULL COMMENT 'TL-APPKEY',
    `APPSecret` varchar(255) NULL COMMENT 'TL-APPSecret',
    `Description` varchar(255) NULL COMMENT '描述',
    `Remark` text NULL COMMENT '备注',
    PRIMARY KEY (`Index`),
    UNIQUE KEY `uniqueServer` (`Name`,`IP`,`Port`) USING BTREE
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人脸数据库表'; 

DROP TABLE if exists `IA_ManInfo`;
CREATE TABLE `IA_ManInfo` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `Number` varchar(32) NOT NULL COMMENT '编号',
  `Function` varchar(255) NOT NULL COMMENT '职务',
  `ProjectDepartment` varchar(255) NOT NULL COMMENT '项目部',
  `Group` bigint(8) unsigned NOT NULL COMMENT '组别',
  `Name` varchar(255) NOT NULL COMMENT '名称',
  `Sex` varchar(32) NOT NULL COMMENT '性别',
  `IDNumber` varchar(32) NOT NULL COMMENT '身份证号',
  `ManInfoEx_Index` bigint(8) unsigned NOT NULL COMMENT '人员信息补充表索引',
  `DeleteFlag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除标志，0未删除 1已删除',
  PRIMARY KEY (`Index`),
  UNIQUE KEY `id` (`IDNumber`) USING BTREE,
  UNIQUE KEY `number` (`Number`) USING BTREE
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人脸数据库表';

DROP TABLE if exists `IA_ManInfoEx`;
CREATE TABLE `IA_ManInfoEx` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `JobNumber` varchar(32) NOT NULL COMMENT '工号（针对供电公司员工）',
  `SubcontractTeam` varchar(255) NOT NULL COMMENT '所属分包队伍（针对外委队伍）',
  `Dept` varchar(255) NOT NULL COMMENT '部门（针对供电公司员工）',
  `Company` varchar(255) NOT NULL COMMENT '公司（针对供电公司员工）',
  `BirthDate` date NOT NULL COMMENT '出生年月',
  `IDNumber` varchar(32) NOT NULL COMMENT '身份证号',
  `PersonalFile_Index` bigint(8) unsigned NOT NULL COMMENT '个人档案（图片）索引',
  `ID_A_Index` bigint(8) unsigned NOT NULL COMMENT '身份证正面（图片）索引',
  `ID_B_Index` bigint(8) unsigned NOT NULL COMMENT '身份证背面（图片）索引',
  `PaperPhoto_Index` bigint(8) unsigned NOT NULL COMMENT '试卷照片（图片）索引',
  `ExamResult` int(8) NOT NULL COMMENT '考试成绩',
  `MedicalReport_Index` bigint(8) unsigned NOT NULL COMMENT '体检报告（图片）索引',
  `SpecialWorkPermit_Index` bigint(8) unsigned NOT NULL COMMENT '特种作业证（图片）索引',
  `AuditStatus` int(8) NOT NULL COMMENT '审核状态',
  `AuditBeginTime` date NOT NULL COMMENT '审核开始有效期',
  `AuditEndTime` date NOT NULL COMMENT '审核结束有效期',
  `Auditor` varchar(255) NOT NULL COMMENT '审核人',
  `AuditTime` datetime NOT NULL COMMENT '审核时间',
  `IllegalInformation` varchar(255) NOT NULL COMMENT '违章信息',
  `Description` varchar(255) COMMENT '描述',
  `DeleteFlag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除标志，0未删除 1已删除',
  `ModifyDateTime` datetime NOT NULL COMMENT '修改时间',
  `Remark` text NULL COMMENT '备注',
  PRIMARY KEY (`Index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人员信息表';

DROP TABLE if exists `IA_GroupInfo`;
CREATE TABLE `IA_GroupInfo` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `Group` varchar(255) NOT NULL COMMENT '组别名称',
  `Parent_Index` bigint(8) unsigned NOT NULL DEFAULT '0' COMMENT '父级索引',
  `GroupType` enum('0','1') NOT NULL DEFAULT '0' COMMENT '组别类型0人脸，1车牌',
  `Remark` varchar(255) NULL COMMENT '备注',
  PRIMARY KEY (`Index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分组信息表';

DROP TABLE if exists `IA_ManGroupMap`;
CREATE TABLE `IA_ManGroupMap` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `GroupInfo_Index` bigint(8) unsigned NOT NULL COMMENT '组别信息表索引',
  `ManInfo_Index` bigint(8) unsigned NOT NULL COMMENT '人员信息表索引',
  `Remark` text NULL COMMENT '备注',
  PRIMARY KEY (`Index`),
  UNIQUE KEY `index` (`GroupInfo_Index`,`ManInfo_Index`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人员组别映射表';

DROP TABLE if exists `IA_UploadPicInfo`;
CREATE TABLE `IA_UploadPicInfo` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `PicName` varchar(255) NOT NULL COMMENT '图片名称',
  `PicType` int(8) NOT NULL COMMENT '图片类型',
  `PicPath` varchar(255) NOT NULL COMMENT '图片路径',
  `Description` varchar(255) NULL COMMENT '描述',
  `DeleteFlag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除标志，0未删除 1已删除',
  `ModifyDateTime` datetime NOT NULL COMMENT '修改时间',
  `Remark` text NULL COMMENT '备注',
  PRIMARY KEY (`Index`),
  UNIQUE KEY `index` (`PicName`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上传图片路径表';

DROP TABLE if exists `IA_ManFeatureInfo`;
CREATE TABLE `IA_ManFeatureInfo` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `ManInfo_Index` bigint(8) unsigned NOT NULL COMMENT '人员信息表索引',
  `FaceDBInfo_Index` bigint(8) unsigned NOT NULL COMMENT '人脸数据库表索引',
  `Image_ID` varchar(255) NOT NULL COMMENT '图片ID',
  `PicturePath` varchar(255) NOT NULL COMMENT '照片路径',
  `NUType` int(4) NOT NULL COMMENT 'NU类型',
  `NUID` varchar(32) NOT NULL COMMENT 'NUID',
  `Time` datetime NOT NULL COMMENT '录入时间',
  `DeleteFlag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除标志，0未删除 1已删除',
  `ModifyDateTime` datetime NOT NULL COMMENT '修改时间',
  `Remark` text NULL COMMENT '备注',
  PRIMARY KEY (`Index`),
  UNIQUE KEY `index` (`Image_ID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人员特征信息表';

DROP TABLE if exists `IA_FaceRecognitionHistory`;
CREATE TABLE `IA_FaceRecognitionHistory` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `Time` datetime NOT NULL COMMENT '打开时间',
  `TimeStamp` bigint(8) unsigned NOT NULL COMMENT '被检测帧时间戳',
  `PUID` varchar(64) NOT NULL COMMENT '设备PUID',
  `Idx` smallint(2) unsigned NOT NULL COMMENT '视频资源索引',
  `ManFeatureInfo_Index` bigint(32) NOT NULL COMMENT '人员特征表索引',
  `FaceScore` float NOT NULL COMMENT '置信度',
  `Facerect_left` int(4) NULL COMMENT '人脸坐标左',
  `Facerect_top` int(4) NULL COMMENT '人脸坐标上',
  `Facerect_right` int(4) NULL COMMENT '人脸坐标右',
  `Facerect_bottom` int(4) NULL COMMENT '人脸坐标下',
  `Costtime` int(4) NULL COMMENT '人脸识别耗时',
  `PicturePath` varchar(255) NOT NULL COMMENT '照片路径',
  `NUType` int(4) NOT NULL COMMENT 'NU类型',
  `NUID` varchar(32) NOT NULL COMMENT 'NUID',
  `GroupDetectHistory_Index` bigint(8) NULL COMMENT '组别打卡记录表索引',
  `Latitude` double NULL COMMENT '经度',
  `Longtitude` double  NULL COMMENT '纬度',
  `DeleteFlag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除标志，0未删除 1已删除',
  `DeleteTime` datetime NOT NULL COMMENT '删除时间',
  `ModifyDateTime` datetime NOT NULL COMMENT '修改时间',
  `Remark` text NULL COMMENT '备注',
  PRIMARY KEY (`Index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人脸识别记录表';

DROP TABLE if exists `IA_FaceDetectHistory`;
CREATE TABLE `IA_FaceDetectHistory` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `Time` datetime NOT NULL COMMENT '打开时间',
  `TimeStamp` bigint(8) unsigned NOT NULL COMMENT '被检测帧时间戳',
  `PUID` varchar(64) NOT NULL COMMENT '设备PUID',
  `Idx` smallint(2) unsigned NOT NULL COMMENT '视频资源索引',
  `ManFeatureInfo_Index` bigint(32) NOT NULL COMMENT '人员特征表索引',
  `FaceScore` float NOT NULL COMMENT '置信度',
  `Feature` varchar(16) NULL COMMENT '人脸特征数据',
  `Facerect_left` int(4) NULL COMMENT '人脸坐标左',
  `Facerect_top` int(4) NULL COMMENT '人脸坐标上',
  `Facerect_right` int(4) NULL COMMENT '人脸坐标右',
  `Facerect_bottom` int(4) NULL COMMENT '人脸坐标下',
  `Costtime` int(4) NULL COMMENT '人脸识别耗时',
  `PicturePath` varchar(255) NOT NULL COMMENT '照片路径',
  `NUType` int(4) NOT NULL COMMENT 'NU类型',
  `NUID` varchar(32) NOT NULL COMMENT 'NUID',
  `GroupDetectHistory_Index` bigint(8) NULL COMMENT '组别打卡记录表索引',
  `Latitude` double NULL COMMENT '经度',
  `Longtitude` double  NULL COMMENT '纬度',
  `SkinColor` varchar(255)  NULL COMMENT '肤色',
  `HairStyle` varchar(255)  NULL COMMENT '发型',
  `HairColor` varchar(255)  NULL COMMENT '发色',
  `FaceStyle` varchar(255)  NULL COMMENT '脸型',
  `FacialFeature` varchar(255)  NULL COMMENT '脸部特征',
  `PhysicalFeature` varchar(255)  NULL COMMENT '体貌特征',
  `RespiratorColor` varchar(255)  NULL COMMENT '口罩颜色',
  `CapStyle` varchar(255)  NULL COMMENT '帽子款式',
  `CapColor` varchar(255)  NULL COMMENT '帽子颜色',
  `GlassStyle` varchar(255)  NULL COMMENT '眼镜款式',
  `GlassColor` varchar(255)  NULL COMMENT '眼镜颜色',
  `Attitude` varchar(255)  NULL COMMENT '姿态分布',
  `EyebrowStyle` varchar(255)  NULL COMMENT '眉型',
  `NoseStyle` varchar(255)  NULL COMMENT '鼻型',
  `MustacheStyle` varchar(255)  NULL COMMENT '胡型',
  `LipStyle` varchar(255)  NULL COMMENT '嘴唇',
  `WrinklePouch` varchar(255)  NULL COMMENT '皱纹眼袋',
  `AcneStain` varchar(255)  NULL COMMENT '痤疮色斑',
  `FreckleBirthmark` varchar(255)  NULL COMMENT '黑痣胎记',
  `ScarDimple` varchar(255)  NULL COMMENT '疤痕酒窝',
  `OtherFeature` varchar(255)  NULL COMMENT '其他特征',
  `DeleteFlag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除标志，0未删除 1已删除',
  `DeleteTime` datetime NOT NULL COMMENT '删除时间',
  `ModifyDateTime` datetime NOT NULL COMMENT '修改时间',
  `Remark` text DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`Index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人脸检测记录表';

DROP TABLE if exists `IA_CarInfo`;
CREATE TABLE `IA_CarInfo`(
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `PicturePath` varchar(255) NOT NULL COMMENT '照片路径',
  `PlateColor` varchar(255) NOT NULL COMMENT '车牌颜色',
  `PlateNo` varchar(255) NOT NULL COMMENT '车牌号',
  `PlateNoAttach` varchar(255) COMMENT '挂车牌号',
  `VehicleClass` varchar(255) COMMENT '车辆类型',
  `VehicleBrand` varchar(255) COMMENT '车辆品牌',
  `VehicleModel` varchar(255) COMMENT '车辆型号',
  `VehicleColor` varchar(255) COMMENT '挂车牌号',
  `DeleteFlag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除标志，0未删除 1已删除',
  `ModifyDateTime` datetime NOT NULL COMMENT '修改时间',
  `Remark` text COMMENT '备注',
  PRIMARY KEY (`Index`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车辆信息表';

DROP TABLE if exists `IA_CarGroupMap`;
CREATE TABLE `IA_CarGroupMap` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `GroupInfo_Index` bigint(8) unsigned NOT NULL COMMENT '组别信息表索引',
  `CarInfo_Index` bigint(8) unsigned NOT NULL COMMENT '车辆信息表索引',
  `Remark` text NULL COMMENT '备注',
  PRIMARY KEY (`Index`),
  UNIQUE KEY `index` (`GroupInfo_Index`,`CarInfo_Index`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人员组别映射表';

DROP TABLE if exists `IA_PlateDetectHistory`;
CREATE TABLE `IA_PlateDetectHistory` (
  `Index` bigint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `Time` datetime NOT NULL COMMENT '打开时间',
  `PUID` varchar(64) NOT NULL COMMENT '设备PUID',
  `Idx` smallint(2) unsigned NOT NULL COMMENT '视频资源索引',
  `Platerect_left` int(4) NULL COMMENT '坐标左',
  `Platerect_top` int(4) NULL COMMENT '坐标上',
  `Platerect_right` int(4) NULL COMMENT '坐标右',
  `Platerect_bottom` int(4) NULL COMMENT '坐标下',
  `Costtime` int(4) NULL COMMENT '人脸识别耗时',
  `PicturePath` varchar(255) NOT NULL COMMENT '照片路径',
  `NUType` int(4) NOT NULL COMMENT 'NU类型',
  `NUID` varchar(32) NOT NULL COMMENT 'NUID',
  `GroupDetectHistory_Index` bigint(8) NULL COMMENT '组别打卡记录表索引',
  `Latitude` double NULL COMMENT '经度',
  `Longtitude` double  NULL COMMENT '纬度',
  `PlateColor` varchar(255)  NOT NULL COMMENT '车牌颜色',
  `PlateNo` varchar(255)  NOT NULL COMMENT '车牌号',
  `PlateNoAttach` varchar(255) NULL COMMENT '挂车牌号',
  `Speed` varchar(255) NULL COMMENT '行驶速度',
  `Direction` varchar(255) NULL COMMENT '行驶方向',
  `VehicleClass` varchar(255) NULL COMMENT '车辆类型',
  `VehicleBrand` varchar(255) NULL COMMENT '车辆品牌',
  `VehicleModel` varchar(255) NULL COMMENT '车辆型号',
  `VehicleColor` varchar(255) NULL COMMENT '车辆颜色',
  `PlateReliability` varchar(255) NULL COMMENT '车牌置信度',
  `BrandReliability` varchar(255) NULL COMMENT '品牌标志置信度',
  `DeleteFlag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否删除标志，0未删除 1已删除',
  `DeleteTime` datetime NOT NULL COMMENT '删除时间',
  `ModifyDateTime` datetime NOT NULL COMMENT '修改时间',
  `Remark` text DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`Index`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车牌检测记录表';