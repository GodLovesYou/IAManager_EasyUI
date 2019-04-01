CREATE PROCEDURE `PROC_QueryManInfos_TG`(IN `v_offset` VARCHAR(20),IN `v_count` VARCHAR(20))
    Main:BEGIN

    /*
    * @desc: 查询人员信息与特征信息表相关联的 TG
    * @author: zenghx
    * @time: 2017-12-29
    */

    IF `v_offset` IS NULL OR `v_offset` = '' THEN
      select -1 as ErrorCode;
      LEAVE Main;
    END IF;

    IF `v_count` IS NULL OR `v_count` = '' THEN
      select -1 as ErrorCode;
      LEAVE Main;
    END IF;


    SET @selectSql = CONCAT("SELECT mi.`Index`, mi.`Number`, mi.`JobNumber`, mi.`Function`, mi.`ProjectDepartment`, mi.`Group`, mi.`Name`, mi.`Sex`, mi.`SubcontractTeam`, mi.`Dept`, mi.`Company`, mi.`BirthDate`, mi.`IDNumber`, mi.`ExamResult`, mfi.`Image_ID` as ImageID, mfi.`PicturePath`,mfi.`NUType`,mfi.`NUID`,mfi.`Time` from `ManInfo_TG` mi left join `ManFeatureInfo` mfi on mfi.`ManInfo_Index` = mi.`Index` where 1 = 1 and mi.`DeleteFlag` = '0' and mfi.`DeleteFlag` = '0' limit ",v_offset,",",v_count);

    prepare selectSql from @selectSql;
    execute selectSql;
    deallocate prepare selectSql;

  END;

