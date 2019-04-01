var IAClient = __iac = {
    
    curFrameResize : new jQuery.Hash(),
    flag:null,
    data:new Array(),
    subMit:false,
    idNumber:null,
    faFlag:true,
    BaseInfo:null,
    ObjSets:[],
    ObjSet:[],
    Load : function(){
        IAFrame.Load();
    },

    UnLoad : function(){
//  	__iac.Logout();
    },

    OnResize : function(width, height){
        if(typeof __iac.curFrameResize == "object" && __iac.curFrameResize.itemsCount > 0){
            __iac.curFrameResize.each(function(item){
                var node = item.value;
                var callbackFun = node.callback;
                if(jQuery.isFunction(callbackFun)){
                    callbackFun(width, height);
                }
            })
        }
        
    },
    
    Login:function(userID,password){
    	var callback=function(rv){
    		if(rv.response.errorCode=="0x0000"){
    			IAFrame.Main.Init();
    			setInterval(__iac.KeepAlive(),900);
    		}
    		else{
    			Ajax.Construct(rv.errorCode);
    		}
    	}
    	Ajax.SendAjax('Login',{userID:userID,password:password},callback);
    },
    
    CheckLogin:function(){
    	var callback=function(rv){
    		if(rv.response.errorCode=="0x0000"){
    			IAFrame.Main.Init();
    		}
    		else{
    			IAFrame.Login.Init();
    		}
    	}
    	Ajax.SendAjax('CheckLogin',{},callback);
    },

    KeepAlive:function(){
    	var callback=function(rv){
    	}
    	Ajax.SendAjax('KeepAlive',{},callback);
    },
    Logout:function(){
    	var callback=function(rv){
    	}
    	Ajax.SendAjax('Logout',{},callback);
    },
    GetNuInfo:function(){
    	var callback=function(rv){
    		if(rv.response.errorCode=="0x0000"){
    			for(var i=0;i<rv.response.content.length;i++){
    				IAClient.ObjSets.push({
    					index:i,
    					NUID:rv.response.content[i].NUID,
    					NUType:rv.response.content[i].NUType
    				});
    			}
    		}else{
    			Ajax.Construct(rv.response.errorCode);
    		}
    	}
    	Ajax.SendAjax('GetNuInfo',{},callback);
    },
    QueryIAServerInfo:function(iaType,node){
    	var callback=function(rv){
    		if(rv.response.errorCode=="0x0000"&&rv.response.content.length>0){
    			var children=new Array();
	    		for(var i=0;i<rv.response.content.length;i++)
	    		{
	    			children.push({
	    				type:'Algorithm ',
	    				iconCls:"icons-tubiao2",
	    				text:rv.response.content[i].name,
	    				index:rv.response.content[i].index,
	    				name:rv.response.content[i].name,
	    				count:rv.response.content[i].count,
	    				remark:rv.response.content[i].remark,
	    				size:rv.response.content[i].size,
	    				ip:rv.response.content[i].ip,
	    				port:rv.response.content[i].port,
	    				child:true
	    			});
	    		}
	    		var data=[{
	    			type:'library',
	    			text:rv.response.content[0].ip+":"+rv.response.content[0].port,
	    			ip:rv.response.content[0].ip,
	    			port:rv.response.content[0].port,
	    			iaType:iaType,
	    			iconCls:"icons-tubiao2",
	    			children:children
	    		}];
	    		$('#FaceSetManage_layout_Tree').tree('append', {
					parent: node.target,
					data: data
				});
    		}
    		else{
    			Ajax.Construct(rv.errorCode);
    		}
    	}
    	Ajax.SendAjax('QueryIAServerInfo',{'IAType':iaType},callback);
    },
    
    QueryFaceInfo:function(dbIndex){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
				var content=rv.response.content;
				var data=new Array();
				for(var i=0;i<content.length;i++){
					data.push({
						user_manInfoEx_Index:content[i].user_manInfoEx_Index,
						user_index:content[i].user_index || "-",
						user_name:content[i].user_name || "-",
						user_sex:content[i].user_sex || "-",
						user_dept:content[i].user_dept || "-",
						user_number:content[i].user_number || "-",
						user_jobNumber:content[i].user_jobNumber || "-",
						user_function:content[i].user_function || "-",
						user_projectDepartment:content[i].user_projectDepartment || "-",
						user_subcontractTeam:content[i].user_subcontractTeam || "-",
						user_company:content[i].user_company || "-",
						user_birthDate:content[i].user_birthDate || "-",
						user_groupName:content[i].user_groupName || "-",
						user_idNumber:content[i].user_idNumber || "-",
						user_examResult:content[i].user_examResult || "-",
						user_auditBeginTime:content[i].user_auditBeginTime || "-",
						user_auditEndTime:content[i].user_auditEndTime || "-",
						user_auditor:content[i].user_auditor || "-",
						user_auditTime:content[i].user_auditTime || "-",
						user_auditStatus:content[i].user_auditStatus || "-",
						user_IllegalInformation:content[i].user_IllegalInformation || "-",
						
						user_ID_A_Path:IAClient.AddressTranslation(content[i].NUID,content[i].user_ID_A_Path),
						user_ID_B_Path:IAClient.AddressTranslation(content[i].NUID,content[i].user_ID_B_Path),
						user_PersonalPath:IAClient.AddressTranslation(content[i].NUID,content[i].user_PersonalPath),
						user_medicalReportPath:IAClient.AddressTranslation(content[i].NUID,content[i].user_medicalReportPath),
						user_paperPhotoPath:IAClient.AddressTranslation(content[i].NUID,content[i].user_paperPhotoPath),
						
						user_sourcePicturePath:IAClient.AddressTranslation(content[i].NUID,content[i].user_sourcePicturePath),
						user_sourcePicturePath_ls:IAClient.AddressTranslation(content[i].NUID,content[i].user_sourcePicturePath_sl),
						
						user_ID_A_Index:content[i].user_ID_A_Index,
						user_ID_B_Index:content[i].user_ID_B_Index,
						user_personalFileIndex:content[i].user_personalFileIndex,
						user_medicalReportIndex:content[i].user_medicalReportIndex,
						user_paperPhoto_Index:content[i].user_paperPhoto_Index,
						user_specialWorkPermit_Index:content[i].user_specialWorkPermit_Index,
						
						user_ID_A_FileName:content[i].user_ID_A_FileName || "-",
						user_ID_B_FileName:content[i].user_ID_B_FileName || "-",
						user_personalFileName:content[i].user_personalFileName || "-",
						user_medicalReportFileName:content[i].user_medicalReportFileName || "-",
						user_paperPhotoFileName:content[i].user_paperPhotoFileName || "-",
						user_specialWorkPermitFileName:content[i].user_specialWorkPermitFileName || "-"
					});
				}
				
				var tab = $('#main_content_tabs').tabs('getSelected');
				var index = $('#main_content_tabs').tabs('getTabIndex',tab);
				if(index==0){
				    IAFace.ReLoadImgImage(data);	
				}else if(index==1){
					$("#FaceManage_datagrid").datagrid('loadData',data);
				}
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('QueryFaceInfo',{"DBIndex":dbIndex},callback);
    },
    
    AddressTranslation:function(NUID,path_source){
    	var path=IAConfig.path.toLocaleLowerCase().replace("iamanager/", "nmi/php/nmi_ia_server.php");
    	var source=path+"?nuid="+NUID+"&fname="+path_source;
    	return source;
    },
    
    
    
    AddIAServerInfo:function(iaType,dbname,ip,port){
    	var callback=function(rv){
    		disLoad();
    		if(rv.response.errorCode == "0x0000"){
				$("#addBox2").window("destroy");
				var data=[{
				    "text":IALanguage.Main.IntelligentAlgorithmManagement,
				    iconCls:"icons-tubiao1",
				    "children":[{
				    	"id":"0",
						"text":IAMConfig.iaServer.SHT,
						"type":"ALL",
						"iconCls":"icons-tubiao2",
						"checked":true
				    },{
				    	"id":"1",
						"text":IAMConfig.iaServer.TL,
						"type":"ALL",
						"iconCls":"icons-tubiao2",
						"checked":false
				    },{
				    	"id":"2",
						"text":IAMConfig.iaServer.AS,
						"type":"ALL",
						"iconCls":"icons-tubiao2",
						"checked":false
				    }]
				}];
				$('#FaceSetManage_layout_Tree').tree("loadData",data);
				var nodes = $('#FaceSetManage_layout_Tree').tree('options').data[0].children;
				for(var i=0;i<nodes.length;i++){
					IAClient.QueryIAServerInfo(i,$('#FaceSetManage_layout_Tree').tree('find',i));
				}
			}else{
				Ajax.Construct(rv.response.errorCode);
				$("#addBox2").window("destroy");
			}
    	}
    	Ajax.SendAjax('AddIAServerInfo',{"iaType":iaType,"dbname":dbname,"ip":ip,"port":port},callback);
    },
    
    
    
    
    AddFaceBaseInfo:function(Number,Function,ProjectDepartment,Name,Sex,Id){
    	var callback=function(rv){
    		disLoad();
    		if(rv.response.errorCode=="0x0000"){
    			if(IAClient.subMit){
    				IAClient.BaseInfo={
    					"ID":Id
    				};
    				$("#nextStep").linkbutton({text:IALanguage.Client.complete});
    				IAClient.faFlag=false;
			    	$("#lastStep").linkbutton("enable");
			    	$("#nextStep").hide();
			    	__iaf.Main.InputFaceInformation(Id);
    			}else{
    				$.messager.alert(IALanguage.Client.info,IALanguage.Client.message,"info",function(){});
    			}
    		}else{
    			Ajax.Construct(rv.response.errorCode);
    		}
    	}
    	Ajax.SendAjax('AddFaceBaseInfo',{"number":Number,"function":Function,"projectDepartment":ProjectDepartment,"name":Name,"sex":Sex,"ID":Id,"ObjSets":IAClient.ObjSet},callback);
    },
    
    CheckAttributes:function(number,idNumber){
    	var callback=function(rv){
    		if(rv.response.errorCode=="0x0000"){
    			var count=rv.response.content.count;
    			if(count=="0"){
    				$("#nextStep").linkbutton("enable");
    				IAClient.subMit=true;
    			    IAClient.idNumber=idNumber;
    			}else{
    				IAClient.subMit=false;
    				alert(IALanguage.Client.message);
    				$("#nextStep").linkbutton("disable");
    			}
    		}else{
    			Ajax.Construct(rv.response.errorCode);
    		}
    	}
    	Ajax.SendAjax('CheckAttributes',{"checkID":idNumber,"checkNumber":number},callback);
    },
    
    DeleteFaceBaseInfo:function(ID){
    	var callback=function(rv){
    		disLoad();
    		if(rv.response.errorCode=="0x0000"){
    			var index=$("#FaceManage_combo_time_combox").combobox('getValue');
		        IAClient.QueryFaceInfo(index);
		        IAClient.faFlag=true;
		        $("#nextStep").linkbutton({text:IALanguage.Client.next});
			    $("#lastStep").linkbutton("disable");
			    __iaf.Main.InputBasicInformation();
		        $("#nextStep").show();
    		}else{
    			Ajax.Construct(rv.response.errorCode);
    		}
    	}
    	Ajax.SendAjax('DeleteFaceBaseInfo',{"ID":ID},callback);
    },
    
    
    QueryFaceRecognitionHistroyInfo:function(beginTime,endTime,qkey){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			$("#DataAnalysis_image_Identification").empty();
    		    var content=rv.response.content;
				var data=new Array();
				for(var i=0;i<content.length;i++){
					data.push({
						username:content[i].UserName,
						Facerect_x:content[i].Facerect_x,
						Facerect_y:content[i].Facerect_y,
						Facerect_w:content[i].Facerect_w,
						Facerect_h:content[i].Facerect_h,
						Time:content[i].Time,
						FaceScore:content[i].FaceScore,
						DeleteTime:content[i].DeleteTime,
						ModifyDateTime:content[i].ModifyDateTime,
						IDNumber:content[i].IDNumber,
						Number:content[i].Number,
						PUID:content[i].PUID,
						Idx:content[i].Idx,
						Costtime:content[i].Costtime,
						PicturePath_sl:IAClient.AddressTranslation(content[i].NUID,content[i].sourcePicturePath_sl),
						PicturePath:IAClient.AddressTranslation(content[i].NUID,content[i].sourcePicturePath),
						sourcePicturePath_sl:IAClient.AddressTranslation(content[i].NUID,content[i].sourcePicturePath_sl),
						sourcePicturePath:IAClient.AddressTranslation(content[i].NUID,content[i].sourcePicturePath),
					});
				}
				IAFace.ReLoad_Identification_Image(data);
				disLoad();
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('QueryFaceRecognitionHistroyInfo',{"BeginTime":beginTime,"EndTime":endTime,"Qkey":qkey},callback);
    },
    
    QueryFaceDetectHistoryInfo:function(beginTime,endTime,qkey){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			$("#DataAnalysis_image_Detection").empty();
    		    var content=rv.response.content;
				var data=new Array();
				for(var i=0;i<content.length;i++){
					data.push({
						Time:content[i].Time,
						Facerect_x:content[i].Facerect_x,
						Facerect_y:content[i].Facerect_y,
						Facerect_w:content[i].Facerect_w,
						Facerect_h:content[i].Facerect_h,
						PicturePath_sl:IAClient.AddressTranslation(content[i].NUID,content[i].PicturePath_sl),
						PicturePath:IAClient.AddressTranslation(content[i].NUID,content[i].PicturePath),
					});
				}
				IAFace.ReLoad_Detection_Image(data);
				disLoad();
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('QueryFaceDetectHistoryInfo',{"BeginTime":beginTime,"EndTime":endTime,"Qkey":qkey},callback);
    },
    
    
    
    
	QueryGroupInfo:function(groupType)
	{
		var callback=function(rv)
		{		
			if(rv.response.errorCode=="0x0000")
			{
				var content=rv.response.content;
				var store=new Array();
				if(content.length>0){
					for(var i=0;i<content.length;i++)
					{
						store.push({
							id:content[i].index,
							text:content[i].group,
							iconCls:'icon-tree-chart',
							parentIndex:content[i].parent_index,
							remark:content[i].remark
						});	
				   }
				}
				var data=[{
					id:'0',
					iconCls:'icons-World',
					text:IALanguage.Client.OrganizationCategory,
					children:store
				}];
				if(groupType==0){
					$('#OrganizeManage_layout_Tree').tree('loadData',data);
				}else{
					$('#PlateOrganizeManage_layout_Tree').tree('loadData',data);
				}
			}
			else
			{
				Ajax.Construct(rv.response.errorCode);
			}
		}
		Ajax.SendAjax("QueryGroupInfo",{"groupType":groupType},callback);
	},
	
	
	
	AddGroupInfo:function(group,groupType,parent_index){
		var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
				$("#AddMainControlOrganize").window("destroy");
    			IAClient.QueryGroupInfo(groupType);
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('AddGroupInfo',{"group":group,"groupType":groupType,"parent_index":parent_index},callback);
	},
    
    ModifyGroupInfo:function(groupName,groupIndex,groupType){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
				$("#ModifyMainControlOrganize").window("destroy");
				IAClient.QueryGroupInfo(groupType);
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('ModifyGroupInfo',{"groupName":groupName,"groupIndex":groupIndex,"groupType":groupType},callback);
    },
    DeleteGroupInfo:function(groupType,groupIndex){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			IAClient.QueryGroupInfo(groupType);
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('DeleteGroupInfo',{"groupIndex":groupIndex},callback);
    },
    
    QueryGroupResourceInfo:function(groupIndex,groupType){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			var data=new Array();
    			var content=rv.response.content;
				for(var i=0;i<content.length;i++){
					data.push({
						index:rv.response.content[i].Index,
						resName:content[i].resName
					});
				}
				if(groupType==0){
					$("#OrganizeManage_datagrid").datagrid('loadData',data);
				}else{
					$("#PlateOrganizeManage_datagrid").datagrid('loadData',data);
				}
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('QueryGroupResourceInfo',{"groupIndex":groupIndex,"groupType":groupType},callback);
    },
    
    AddGroupResourceInfo:function(groupIndex,groupType,resList){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			$("#AddGroupResourceInfo").window("destroy");
    			if(groupType==0){
    				var node=$('#OrganizeManage_layout_Tree').tree('getSelected');
    			    IAClient.QueryGroupResourceInfo(node.id,0);
    			}else{
    				var node=$('#PlateOrganizeManage_layout_Tree').tree('getSelected');
    			    IAClient.QueryGroupResourceInfo(node.id,1);
    			}
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('AddGroupResourceInfo',{"groupIndex":groupIndex,"groupType":groupType,"resList":resList},callback);
    },
    
    DeleteGroupResourceInfo:function(groupIndex,groupType,resIndex){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			if(groupType==0){
    				var node=$('#OrganizeManage_layout_Tree').tree('getSelected');
    			    IAClient.QueryGroupResourceInfo(node.id,0);
    			}else{
    				var node=$('#PlateOrganizeManage_layout_Tree').tree('getSelected');
    			    IAClient.QueryGroupResourceInfo(node.id,1);
    			}
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('DeleteGroupResourceInfo',{"groupIndex":groupIndex,"groupType":groupType,"resIndex":resIndex},callback);
    },
    
    
    
    QueryCarIDInfo:function(){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			var content=rv.response.content;
    			if(content.length>0){
    				var data=new Array();
    				for(var i=0;i<content.length;i++){
    					data.push({
    						plateIndex:content[i].Index,
    						ModifyDateTime:content[i].ModifyDateTime,
    						PicturePath_sl:IAClient.AddressTranslation(content[i].NUID,content[i].PicturePath_sl),
						    PicturePath:IAClient.AddressTranslation(content[i].NUID,content[i].PicturePath),
    						PlateColor:content[i].PlateColor,
    						PlateNo:content[i].PlateNo,
    						PlateNoAttach:content[i].PlateNoAttach,
    						Remark:content[i].Remark,
    						VehicleBrand:content[i].VehicleBrand,
    						VehicleClass:content[i].VehicleClass,
    						VehicleColor:content[i].VehicleColor,
    						VehicleModel:content[i].VehicleModel
    					});
    				}
    				$("#PlateManage_datagrid").datagrid('loadData',data);
    			}
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('QueryCarIDInfo',{},callback);
    },
    
    AddCarIDInfo:function(PlateColor,PlateNo,PlateNoAttach,VehicleClass,VehicleBrand,VehicleModel,VehicleColor){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			$("#addPlateInfo").window("destroy");
    			IAClient.QueryCarIDInfo();
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('AddCarIDInfo',{"PlateColor":PlateColor,"PlateNo":PlateNo,"PlateNoAttach":PlateNoAttach,"VehicleClass":VehicleClass,"VehicleBrand":VehicleBrand,"VehicleModel":VehicleModel,"VehicleColor":VehicleColor},callback);
    },
    
    ModifyCarIDInfo:function(plateIndex,PlateColor,PlateNo,PlateNoAttach,VehicleClass,VehicleBrand,VehicleModel,VehicleColor){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			$("#ModifyPlateInfo").window("destroy");
    			IAClient.QueryCarIDInfo();
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('ModifyCarIDInfo',{"plateIndex":plateIndex,"PlateColor":PlateColor,"PlateNo":PlateNo,"PlateNoAttach":PlateNoAttach,"VehicleClass":VehicleClass,"VehicleBrand":VehicleBrand,"VehicleModel":VehicleModel,"VehicleColor":VehicleColor},callback);
    },
    
    DeleteCarIDInfo:function(plateIndex){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			IAClient.QueryCarIDInfo();
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('DeleteCarIDInfo',{"plateIndex":plateIndex},callback);
    },
    
    QueryPlateNumberHistroyInfo:function(qkey,date){
    	var callback=function(rv){
    		if(rv.response.errorCode == "0x0000"){
    			$("#PlateDataAnalysis_image_box").empty();
    			var content=rv.response.content;
    			if(content.length>0){
					var data=new Array();
					for(var i=0;i<content.length;i++){
						data.push({
							PlateNo:content[i].PlateNo,
							Facerect_x:content[i].Facerect_x,
							Facerect_y:content[i].Facerect_y,
							Facerect_w:content[i].Facerect_w,
							Facerect_h:content[i].Facerect_h,
							PicturePath_sl:IAClient.AddressTranslation(content[i].NUID,content[i].PicturePath_sl),
						    PicturePath:IAClient.AddressTranslation(content[i].NUID,content[i].PicturePath),
						});
					}
					IAFace.ReLoad_Plate_Image(data);
    			}
    		    	
			}else{
				Ajax.Construct(rv.response.errorCode);
			}
    	}
    	Ajax.SendAjax('QueryPlateNumberHistroyInfo',{"qkey":qkey,"date":date},callback);
    },
    
    
    end : true
}