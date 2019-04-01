CREATE PROCEDURE `PROC_UpdateManInfo_TG`(IN `num` VARCHAR(255), IN `fun` VARCHAR(255), IN `proj` VARCHAR(255), IN `group` VARCHAR(255), IN `name` VARCHAR(255), IN `sex` VARCHAR(8), IN `idnum` VARCHAR(32), IN `desc` VARCHAR(255))
    Main:BEGIN

    /**
     * Desc : 人员基本信息插入即更新
     * Author : zenghx
     * Date : 2017-12-27
     */


    IF `num` IS NULL OR `num` = '' THEN
      LEAVE Main;
    END IF;

    IF `fun` IS NULL OR `fun` = '' THEN
      LEAVE  Main;
    END IF;

    IF `proj` IS NULL OR `proj`='' THEN
      LEAVE Main;
    END IF;

    IF `group` IS NULL OR `group`='' THEN
      LEAVE Main;
    END IF;

    IF `name` IS NULL OR `name` = '' THEN
      LEAVE Main;
    END IF;

    IF `sex` IS NULL OR `sex` = '' THEN
      LEAVE Main;
    END IF;

    IF `idnum` IS NULL OR `idnum` = '' THEN
      LEAVE Main;
    END IF;

    IF `desc` IS NULL THEN
      LEAVE Main;
    END IF;

    SET @count = 0;

    SELECT count(*) INTO @count FROM ManInfo_TG mi WHERE mi.`Number` = `num` AND mi.`IDNumber`=`idnum` AND mi.`DeleteFlag` = '0';

    IF @count = 0 THEN
      INSERT INTO `ManInfo_TG` (`Number`, `JobNumber`, `Function`, `ProjectDepartment`, `Group`, `Name`, `Sex`, `SubcontractTeam`, `Dept`, `Company`, `BirthDate`, `IDNumber`, `PersonalFile_Index`, `ID_A_Index`, `ID_B_Index`, `PaperPhoto_Index`, `ExamResult`, `MedicalReport_Index`, `SpecialWorkPermit_Index`, `AuditStatus`, `AuditBeginTime`, `AuditEndTime`, `Auditor`, `AuditTime`, `IllegalInformation`, `Description`, `DeleteFlag`,`ModifyDateTime`, `Remark`) VALUE(num,NULL,fun, proj, `group`, name, sex, NULL, NULL,NULL,NULL,idnum,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,`desc`,'0', now(),'');

    ELSE
      SET @update = CONCAT("UPDATE `ManInfo_TG` SET `Name`='",`name`,"', `Function`='",fun,"', `Group`='",`group`,"', `sex`='",sex,"', `Description`='",`desc`,"', `ProjectDepartment`='",proj,"' WHERE `Number`='",num,"'AND `IDNumber`='",idnum,"' ");
      PREPARE updateSQL FROM @update;
      EXECUTE  updateSQL;
      DEALLOCATE  PREPARE updateSQL;
    END IF;

  END;

