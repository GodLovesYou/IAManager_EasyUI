CREATE PROCEDURE `PROC_AddFaceDetectHistory_IA`(IN `valueList` MEDIUMTEXT)
    COMMENT '批量插入人脸检测记录'
Main:BEGIN
/**
 * @desc：批量插入人脸检测记录
 * @param:
 *		valueList MEDIUMTEXT
 *			time
 *			timestamp
 *			puid
 *			idx
 *			faceScore
 *			feature
 *			facerect_left
 *			facerect_top
 *			facerect_right
 *			facerect_bottom
 *			costtime
 *			picturePath
 *			picturePath_sl
 *			nutype
 *			nuid
 *			histroy
 *			latitude
 *			longtitude
 *			skinColor
 *			hairStyle
 *			hairColor
 *			faceStyle
 *			facialFeature
 *			physicalFeature
 *			respiratorColor
 *			capStyle
 *			capColor
 *			glassStyle
 *			glassColor
 *			attitude
 *			eyebrowStyle
 *			noseStyle
 *			mustacheStyle
 *			lipStyle
 *			wrinklePouch
 *			acneStain
 *			freckleBirthmark
 *			scarDimple
 *			otherFeature
 *			deleteTime
 *			remark
 *
 * @update
 *		v18.09.18 创建
 */
	IF `valueList` IS NULL OR `valueList` = '' THEN
		LEAVE Main;
	END IF;
	
	SET @resourceDelimiter = ";";
	SET @resourceCount = 0;
	SET @resourceCount = FUNC_GetSplitStringTotal(valueList,@resourceDelimiter);
	SET @insertSQL = "";
	SET @i = 0;
	
	ListResource:WHILE @i < @resourceCount
	DO
		SET @i = @i + 1;
		SET @resourceItem = FUNC_GetSplitString(valueList,@resourceDelimiter,@i);
		SET @resourceItemDelimiter = ",";
		SET @resourceItemCount = 0;
		SET @resourceItemCount = FUNC_GetSplitStringTotal(@resourceItem,@resourceItemDelimiter);
		
		SET @time = '';
		SET @timestamps = '';
		SET @PUID = '';
		SET @Idx = '';
		SET @faceScore = '';
		SET @feature = '';
		SET @facerect_left = '';
		SET @facerect_top = '';
		SET @facerect_right = '';
		SET @facerect_bottom = '';
		SET @costtime = '';
		SET @picturePath = '';
		SET @picturePath_sl = '';
		SET @NUType = '';
		SET @NUID = '';
		SET @history = '';
		SET @latitude = '';
		SET @longtitude = '';
		SET @skinColor = '';
		SET @hairStyle = '';
		SET @hairColor = '';
		SET @faceStyle = '';
		SET @facialFeature = '';
		SET @physicalFeature = '';
		SET @respiratorColor = '';
		SET @capStyle = '';
		SET @capColor = '';
		SET @glassStyle = '';
		SET @glassColor = '';
		SET @attitude = '';
		SET @eyebrowStyle = '';
		SET @noseStyle = '';
		SET @mustacheStyle = '';
		SET @lipStyle = '';
		SET @wrinklePouch = '';
		SET @acneStain = '';
		SET @freckleBirthmark = '';
		SET @scarDimple = '';
		SET @otherFeature = '';
		SET @deleteTime = '';
		SET @remark = '';

		SET @j = 0;
		WHILE @j < @resourceItemCount AND @j < 41
		DO
			SET @j = @j + 1;
			IF @j = 1 THEN
				SET @time = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 2 THEN
				SET @timestamps = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 3 THEN
				SET @PUID = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 4 THEN
				SET @Idx = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 5 THEN
				SET @faceScore = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 6 THEN
				SET @feature = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 7 THEN
				SET @facerect_left = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 8 THEN
				SET @facerect_top = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 9 THEN
				SET @facerect_right = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 10 THEN
				SET @facerect_bottom = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 11 THEN
				SET @costtime = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 12 THEN
				SET @picturePath = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 13 THEN
				SET @picturePath_sl = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 14 THEN
				SET @NUType = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 15 THEN
				SET @NUID = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 16 THEN
				SET @history = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 17 THEN
				SET @latitude = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;

			IF @j = 18 THEN
				SET @longtitude = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 19 THEN
				SET @skinColor = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 20 THEN
				SET @hairStyle = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 21 THEN
				SET @hairColor = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 22 THEN
				SET @faceStyle = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 23 THEN
				SET @facialFeature = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 24 THEN
				SET @physicalFeature = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 25 THEN
				SET @respiratorColor = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 26 THEN
				SET @capStyle = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 27 THEN
				SET @capColor = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 28 THEN
				SET @glassStyle = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 29 THEN
				SET @glassColor = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 30 THEN
				SET @attitude = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 31 THEN
				SET @eyebrowStyle = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 32 THEN
				SET @noseStyle = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 33 THEN
				SET @mustacheStyle = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 34 THEN
				SET @lipStyle = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 35 THEN
				SET @wrinklePouch = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 36 THEN
				SET @acneStain = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 37 THEN
				SET @freckleBirthmark = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 38 THEN
				SET @scarDimple = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 39 THEN
				SET @otherFeature = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
			IF @j = 40 THEN
				SET @deleteTime = FUNC_GetSplitString(@resourceItem,@resourceItemDelimiter,@j);
			END IF;
			
		END WHILE;
			
		IF @puid = "" OR @idx = "" OR @nutype = "" OR @nuID = "" THEN
			ITERATE ListResource;
		END IF;  
		
		IF @insertSQL = "" THEN

            SET @insertSQL = CONCAT(@insertSQL, "(",@time,",",@timestamps,",'",@PUID,"',",@Idx,",",@faceScore,",'",@feature,"',",@facerect_left,",",@facerect_top,",",@facerect_right,",",@facerect_bottom,",",@costtime,",'",@picturePath,"','",@picturePath_sl,"',",@NUType,",'",@NUID,"',",@history,",",@latitude,",",@longtitude,",'",@skinColor,"','",@hairStyle,"','",@hairColor,"','",@faceStyle,"','",@facialFeature,"','",@physicalFeature,"','",@respiratorColor,"','",@capStyle,"','",@capColor,"','",@glassStyle,"','",@glassColor,"','",@attitude,"','",@eyebrowStyle,"','",@noseStyle,"','",@mustacheStyle,"','",@lipStyle,"','",@wrinklePouch,"','",@acneStain,"','",@freckleBirthmark,"','",@scarDimple,"','",@otherFeature,"','0',",@deleteTime,", now())");
			
		ELSE
			 SET @insertSQL = CONCAT(@insertSQL, "(",@time,",",@timestamps,",'",@PUID,"',",@Idx,",",@faceScore,",'",@feature,"',",@facerect_left,",",@facerect_top,",",@facerect_right,",",@facerect_bottom,",",@costtime,",'",@picturePath,"','",@picturePath_sl,"',",@NUType,",'",@NUID,"',",@history,",",@latitude,",",@longtitude,",'",@skinColor,"','",@hairStyle,"','",@hairColor,"','",@faceStyle,"','",@facialFeature,"','",@physicalFeature,"','",@respiratorColor,"','",@capStyle,"','",@capColor,"','",@glassStyle,"','",@glassColor,"','",@attitude,"','",@eyebrowStyle,"','",@noseStyle,"','",@mustacheStyle,"','",@lipStyle,"','",@wrinklePouch,"','",@acneStain,"','",@freckleBirthmark,"','",@scarDimple,"','",@otherFeature,"','0',",@deleteTime,", now()),");
		END IF;
	END WHILE; 

	IF @insertSQL <> '' THEN
      SET @insertSQL = CONCAT("INSERT INTO ia_facedetecthistory (`Time`, `TimeStamp`, `PUID`, `Idx`, `FaceScore`, `Feature`, `Facerect_X`, `Facerect_Y`, `Facerect_W`, `Facerect_H`, `Costtime`, `PicturePath`, `PicturePath_sl`, `NUType`, `NUID`, `GroupDetectHistory_Index`, `Latitude`, `Longtitude`, `SkinColor`, `HairStyle`, `HairColor`, `FaceStyle`, `FacialFeature`, `PhysicalFeature`, `RespiratorColor`, `CapStyle`, `CapColor`, `GlassStyle`, `GlassColor`, `Attitude`, `EyebrowStyle`, `NoseStyle`, `MustacheStyle`, `LipStyle`, `WrinklePouch`, `AcneStain`, `FreckleBirthmark`, `ScarDimple`, `OtherFeature`, `DeleteFlag`, `DeleteTime`, `ModifyDateTime`)VALUES ",@insertSQL);
			
      PREPARE insertSQL FROM @insertSQL;
      EXECUTE insertSQL;
      DEALLOCATE PREPARE insertSQL;
  END IF;

END