var IAFrame = __iaf = {

    Load : function(){
        __iaf.Init();
        jQuery(window).resize(function(){
            var width = $(window).width();
            var height = $(window).height();
            IAClient.OnResize(width, height);
        });
    },

    Init : function(){
    	IAClient.CheckLogin();
    },
    
    Login : {
    	
    	Init : function(){
    		__iaf.Login.CreateLoginFrame();
    	},
    	
    	CreateLoginFrame : function(){
    		var html = "";
    		html += "<div id='login' class='login'>";
    		html += "    <div id='login_insert' class='login_insert'>";
    		html += "         <div id='login_insert_image' class='login_insert_image'></div>";
    		html += "    </div>";
    		html += "    <div id='login_box' class='login_box'>";
    		html += "         <div id='login_box_inside' class='login_box_inside'>";
    		html += "              <div id='login_box_illustration' class='login_box_illustration'></div>";
    		html += "              <div id='login_box_userID' class='login_box_userID'>";
    		html += "                   <div id='user_head' class='user_head'></div>";
    		html += "                   <div id='user_input' class='user_input'>";
    		html += "                        <input id='userID' class='userID'/>";
    		html += "                   </div>";
    		html += "              </div>";
    		html += "              <div id='login_box_passwd' class='login_box_passwd'>";
    		html += "                   <div id='passwd_lock' class='passwd_lock'></div>";
    		html += "                   <div id='passwd_input' class='passwd_input'>";
    		html += "                        <input id='passwd' class='passwd' type='password'/>";
    		html += "                   </div>";
    		html += "              </div>";
    		html += "               <div id='login_box_checkbox' class='login_box_checkbox'>";
    		html += "                    <div style='float:left;margin-top:10.2px;'><input id='input_checkbox' class='input_checkbox' type='checkbox'/></div> <div class='box_checkbox'>"+IALanguage.Login.accnum+"</div>";
    		html += "               </div>";
    		html += "               <div id='login_box_submit' class='login_box_submit'>"+IALanguage.Login.Sign+"</div>";
    		html += "         </div>";
    		html += "    </div>";
    		html += "</div>";
    		$("body").html(html);
    		IALogin.LoginControl();
    		IALogin.LoginCSS($(window).width(),$(window).height());
    		IAClient.curFrameResize.set("resize",{'callback':IALogin.LoginCSS});
    	},
    	
    	end : true
    },
    
    Main:{
    	Init : function(){
    		__iaf.Main.CreateMainFrame();
    	},
    	
    	CreateMainFrame : function(){
    		var html = "";
    		html += "<div class='main' id='main'>";
    		html += "   <div class='main_head' id='main_head'>";
    		html += "       <div class='Face_Management' id='Face_Management'><div class='Face_Management_png'></div>"+IALanguage.Main.Tab.FaceManage+"</div>";
    		html += "       <div class='License_Plate_Panagement' id='License_Plate_Panagement'><div class='License_Plate_Panagement_png'></div>"+IALanguage.Main.Tab.PlateManage+"</div>";
    		html += "   </div>";
    		html += "   <div class='main_content_face' id='main_content_face'></div>";
    		html += "   <div class='main_content_plate' id='main_content_plate'></div>";
    		html += "   <div id='PlateOrganizeManage_layout_Tree_mm' style='width:80px;'></div>";
    		html += "   <div id='PlateOrganizeManage_layout_Tree_mm_group' style='width:80px;'></div>";
    		html += "</div>";
    		html += "<div class='main-images-box' style='position:absolute;'></div>";
    		$("body").html(html);
    		
    		IAFace.MainCSS();
    		IAFace.MainControl();
    		IAClient.GetNuInfo();
    		__iaf.Main.CreateFaceFrame();
    	},
    	
    	CreateFaceFrame:function(){
    		var html = "";
    		html += "<div id='main_content_tabs' class='main_content_tabs'>";
    		html += "</div>";
    		$("#main_content_face").html(html);
    		IAFace.MainControlEasyui();
    	},
    	CreateLicensePateFrame:function(){
    		var html = "";
    		html += "<div id='main_plate_tabs' class='main_plate_tabs'>";
    		html += "</div>";
    		$("#main_content_plate").html(html);
    		IACar.MainControlEasyuiPlate();
    	},
    	
    	
    	CreateFaceSetManageFrame:function(){
    		if($("#main_content_tabs_FaceSetManage")[0]){
    			var html = "";
	    		html += "<div id='FaceSetManage_layout' class='FaceSetManage_layout'>";
	    		html += "</div>";
	    		html += "<div id='FaceSetManage_layout_Tree_mm' style='width:80px;'>";
                html += "</div>";
                html += "<div id='FaceSetManage_layout_Tree_mm_child' style='width:80px;'>";
                html += "</div>";
	    		$("#main_content_tabs_FaceSetManage").html(html);
	    		IAFace.MainControlEasyuiLayout();
    		}
    	},
    	
    	CreateFaceManageFrame:function(){
    		if($("#main_content_tabs_FaceManage")[0]){
    			var width=$(window).width();
		        var height=$(window).height();
    			var html = "";
	    		html += "<div id='FaceManage' class='FaceManage'>";
	    		html += "    <div id='FaceManage_combo' class='FaceManage_combo'>";
	    		html += "         <div id='FaceManage_combo_box_algo' class='FaceManage_combo_box_algo'>";
	    		html += "              "+IALanguage.Main.Algorithm_type+" : <input id='FaceManage_combo_algo_combox' name='dept'>";
	    		html += "         </div>";
	    		html += "         <div id='FaceManage_combo_box_time' class='FaceManage_combo_box_time'>";
	    		html += "              "+IALanguage.Main.Face_set_name+" : <input id='FaceManage_combo_time_combox' name='dept'>";
	    		html += "         </div>";
	    		html += "    </div>";
	    		html += "    <div id='FaceManage_datagrid_box' class='FaceManage_datagrid_box'>";
	    		html += "          <div id='FaceManage_datagrid' class='FaceManage_datagrid'></div>";
	    		html += "    </div>";
	    		html += "</div>";
	    		$("#main_content_tabs_FaceManage").html(html);
	    		IAFace.MainControlEasyuiDatagrid();
	    		$("#FaceManage_datagrid_box").css({
					"width": width,
					"height":(height-110)+"px"
			    });
    		}
    	},
    	
    	CreateDataAnalysisFrame:function(){
    		if($("#main_content_tabs_DataAnalysis")[0]){
    			var html = "";
	    		html += "<div id='DataAnalysis_tabs' class='DataAnalysis_tabs'>";
	    		html += "</div>";
	    		$("#main_content_tabs_DataAnalysis").html(html);
	    		IAFace.MainControlEasyuiDataAnalysis_tabs();
    		}
    	},
    	
    	DataAnalysis_Identification:function(){
    		var html="";
    		html += "<div id='DataAnalysis_Identification' class='DataAnalysis_Identification'>";
    		html += "    <div id='DataAnalysis_combo_Identification' class='DataAnalysis_combo_Identification'>";
    		html += "         <div id='DataAnalysis_combo_box_time_Identification' class='DataAnalysis_combo_box_time_Identification' style='margin-left:7.5px;'>";
    		html += "              <input id='DataAnalysis_combo_begin_time_combox_Identification' name='dept'> - ";
    		html += "         </div>";
    		html += "         <div id='DataAnalysis_combo_box_time_Identification' class='DataAnalysis_combo_box_time_Identification' style='margin-left:1px;'>";
    		html += "              <input id='DataAnalysis_combo_end_time_combox_Identification' name='dept'>";
    		html += "         </div>";
    		html += "         <div id='DataAnalysis_combo_box_search_Identification' class='DataAnalysis_combo_box_search_Identification'>";
    		html += "              <input id='DataAnalysis_combo_search_combox_Identification' name='dept'>";
    		html += "         </div>";
    		html += "    </div>";
    		html += "    <div class='DataAnalysis_image_Identification' id='DataAnalysis_image_Identification'>";
    		
    		html += "    </div>";
    		html += "</div>";
    		$("#Identification_record_table").html(html);
      	    IAFace.MainControlEasyuiDataAnalysis_Identification();
    	},
    	DataAnalysis_Detection:function(){
    		var html="";
    		html += "<div id='DataAnalysis_Detection' class='DataAnalysis_Detection'>";
    		html += "    <div id='DataAnalysis_combo_Detection' class='DataAnalysis_combo_Detection'>";
    		html += "         <div id='DataAnalysis_combo_box_time_Detection' class='DataAnalysis_combo_box_time_Detection' style='margin-left:7.5px;'>";
    		html += "              <input id='DataAnalysis_combo_begin_time_combox_Detection' name='dept'> - ";
    		html += "         </div>";
    		html += "         <div id='DataAnalysis_combo_box_time_Detection' class='DataAnalysis_combo_box_time_Detection' style='margin-left:1px;'>";
    		html += "              <input id='DataAnalysis_combo_end_time_combox_Detection' name='dept'>";
    		html += "         </div>";
    		html += "         <div id='DataAnalysis_combo_box_search_Detection' class='DataAnalysis_combo_box_search_Detection'>";
    		html += "              <input id='DataAnalysis_combo_search_combox_Detection' name='dept'>";
    		html += "         </div>";
    		html += "    </div>";
    		html += "    <div class='DataAnalysis_image_Detection' id='DataAnalysis_image_Detection'>";
    		html += "    </div>";
    		html += "</div>";
    		$("#Detection_record_table").html(html);
      	    IAFace.MainControlEasyuiDataAnalysis_Detection();
    	},
    	
    	CreateOrganizeManageFrame:function(){
    		if($("#main_content_tabs_OrganizeManage")[0]){
    			var html = "";
	    		html += "<div id='OrganizeManage_layout' class='OrganizeManage_layout'>";
	    		html += "</div>";
	    		$("#main_content_tabs_OrganizeManage").html(html);
	    		IAFace.MainControlOrganizeLayout();
    		}
    	},
    	
    	AddMainControlOrganize:function(groupType,parent_index){
    		var html="";
			html +="<div id='AddMainControlOrganize' style='overflow:hidden;'>";
			html +="    <div style='margin:15px 30px'><span style='margin:10px 5px;'>"+IALanguage.Main.group+": </span><input id='add_group' style='width:180px;height:26px;'><a id='add_group_btn' style='font-size:16px;margin:5px;'> "+IALanguage.Main.submit+"</a>";
		    html +="    </div>";
			html +="</div>";
			$("body").append(html);
			
			$("#add_group").textbox({
				
			});
			
			$("#add_group_btn").linkbutton({});
			$('#add_group_btn').bind('click', function(){
				IAClient.AddGroupInfo($("#add_group").val(),groupType,parent_index);
		    });
			$("#AddMainControlOrganize").window({
		        title:IALanguage.Main.add_group,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:400,
				height:100,
				onClose:function(){
					$("#AddMainControlOrganize").window("destroy");
				}
		    });
    	},
    	ModifyMainControlOrganize:function(text,groupType,index){
    		var html="";
			html +="<div id='ModifyMainControlOrganize' style='overflow:hidden;'>";
			html +="    <div style='margin:15px 30px'><span style='margin:10px 5px;'>"+IALanguage.Main.group+": </span><input id='modfiy_group' style='width:200px;height:26px;'><a id='modfiy_group_btn' style='font-size:16px;margin:5px;'> "+IALanguage.Main.submit+" </a>";
			html +="</div>";
			$("body").append(html);
			
			
			$("#modfiy_group").textbox({
				value:text
			});
			
			$("#modfiy_group_btn").linkbutton({});
			$('#modfiy_group_btn').bind('click', function(){
				IAClient.ModifyGroupInfo($("#modfiy_group").val(),index,groupType);
		    });
			$("#ModifyMainControlOrganize").window({
		        title:IALanguage.Main.modify_group,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:400,
				height:100,
				onClose:function(){
					$("#ModifyMainControlOrganize").window("destroy");
				}
		    });
    	},
    	
    	
    	
    	
    	
    	
    	
    	
    	
    	
    	
    	 CreatePlateManageFrame:function(){
	    	if($("#main_plate_tabs_PlateManage")[0]){
				var html = "";
	    		html += "<div id='PlateManage_datagrid' class='PlateManage_datagrid'>";
	    		html += "</div>";
	    		$("#main_plate_tabs_PlateManage").html(html);
	  		    IACar.MainControlEasyuiPlateDatagrid();
	    	}
	    },
	
	    CreatePlateAnalysisFrame:function(){
	    	if($("#main_plate_tabs_PlateAnalysis")[0]){
				var html = "";
	    		html += "<div id='PlateDataAnalysis' class='PlateDataAnalysis'>";
	    		html += "    <div id='PlateDataAnalysis_combo' class='PlateDataAnalysis_combo'>";
	    		html += "         <div id='PlateDataAnalysis_combo_box_time' class='PlateDataAnalysis_combo_box_time'>";
	    		html += "              <input id='PlateDataAnalysis_combo_time_combox' name='dept'>";
	    		html += "         </div>";
	    		html += "         <div id='PlateDataAnalysis_combo_box_search' class='PlateDataAnalysis_combo_box_search'>";
	    		html += "              <input id='PlateDataAnalysis_combo_search_combox' name='dept'>";
	    		html += "         </div>";
	    		html += "    </div>";
	    		html += "    <div class='PlateDataAnalysis_image_box' id='PlateDataAnalysis_image_box'>";
	    		html += "    </div>";
	    		html += "</div>";
	    		$("#main_plate_tabs_PlateAnalysis").html(html);
	    		IACar.MainControlEasyuiPlateDataAnalysis();
			}
	    },
	    CreatePlateOrganizeManageFrame:function(){
	    	if($("#main_plate_tabs_PlateOrganizeManage")[0]){
    			var html = "";
	    		html += "<div id='PlateOrganizeManage_layout' class='PlateOrganizeManage_layout'>";
	    		html += "</div>";
	    		$("#main_plate_tabs_PlateOrganizeManage").html(html);
	    		IACar.MainControlPlateOrganizeLayout();
    		}
	    },
	    
	    CreateAddBox:function(){
			var html="";
			html +="<div id='addBox1' style='overflow:hidden;'>";
			html +="	<div style='padding-top:40px;'>";
			html +="            <div><span style='margin-right:20px;margin-left:20px;'>地址：</span><input id='serverIP' class='addinfo' style='height:30px;width:260px;'><span style='margin-left:20px;color:red;font-size:16px;'>*</span></div>";
			html +="            <div style='margin-top:30px;'><span style='margin-right:20px;margin-left:20px;'>端口：</span><input id='serverPort' class='addinfo' style='height:30px;width:260px'><span style='margin-left:20px;color:red;font-size:16px;'>*</span></div>";
			html +="    </div>";
		    html +="  	<div style='text-align:center;padding:20px;margin-top:30px;'>";
		    html +="      	<a id='resett1'>"+IALanguage.Main.reset+"</a>";
		    html +="      	<a id='add1'>"+IALanguage.Main.add+"</a>";
		    html +="  	</div>";
			html +="</div>";
			$("body").append(html);
			$("#addBox1").window({
		        title:IALanguage.Main.add_service,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:400,
				height:280,
				onClose:function(){
					$("#addBox1").window("destroy");
				}
		    });
		    
		    $("#serverIP").textbox({
		    	prompt:'192.168.0.1'
		    });
		    $("#serverPort").textbox({
		    	prompt:'8080'
		    });
		    $("#resett1").linkbutton({
		    	iconCls:'icon-reload'
		    });
		    $("#add1").linkbutton({
		    	iconCls:'icon-ok'
		    });
		    $("#resett1").off().on('click',function(){
		    	$(".addinfo").textbox('clear');
		    });
	
		    
		    $("#add1").off().on('click',function(){
		    	if($('#serverIP').val()=='' || $('#serverPort').val()==''){
		    		$.messager.alert(IALanguage.Main.info,IALanguage.Main.message_input,"warning",function(){})
		    	}else{
		    		var reg1 = /^(\d+)\.(\d+)\.(\d+)\.(\d+)$/;
		    		var reg2 = /^([0-9]|[1-9]\d{1,3}|[1-5]\d{4}|6[0-5]{2}[0-3][0-5])$/;
				    if(reg1.test($("#serverIP").val())){
				    }else{
				    	$.messager.alert(IALanguage.Main.info,IALanguage.Main.message_ip,"warning",function(){})
				    };
				    if(reg2.test($("#serverPort").val())){
				    }else{
				    	$.messager.alert(IALanguage.Main.info,IALanguage.Main.message_port,"warning",function(){})
				    };
				    
				    var path = $("#serverIP").val() + ":" + $("#serverPort").val();
		    		var node  =$('#FaceSetManage_layout_Tree').tree('getRoot');
			        var leaf = $('#FaceSetManage_layout_Tree').tree('isLeaf', node);  
			        var parentOne = $('#FaceSetManage_layout_Tree').tree('getChildren', node);
			       	var libsdataArr = new Array();
				    $.each(parentOne,function(num,nodes){
				    	libsdataArr.push(nodes.text);
				    })
				    if($.inArray( path, libsdataArr) == -1){
				    	if(reg1.test($("#serverIP").val()) && reg2.test($("#serverPort").val())){
					    	var server_IP = $("#serverIP").val();
					    	var server_PORT = $("#serverPort").val();
					    	$("#addBox1").window("destroy");
					    	var node  =$('#FaceSetManage_layout_Tree').tree('getSelected');
					    	$('#FaceSetManage_layout_Tree').tree('append',{
						    	parent:node.target,
						    	data:[{
						    		id:server_IP+':'+server_PORT,
						    		iaType:node.id,
						    		text:server_IP+':'+server_PORT,
						    		ip:server_IP,
						    		port:server_PORT,
						    		type:'library',
						    		iconCls:"icons-tubiao2",
						    	}]
						    });
						}
				    }else{
				    	$.messager.alert(IALanguage.Main.info,IALanguage.Main.same_library,"warning",function(){});
				    	$("#addBox1").window("destroy");
				    }
				}
		    });
		},
	    
	    AddlibsData:function(iaType,ip,port){
			var html="";
			html +="<div id='addBox2' style='overflow:hidden;'>";
			html +="		<div style='padding-top:20px;'>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:20px;margin-left:20px;'>库名：</span><input id='dbname' class='addinfo' style='height:30px;width:260px'><span style='margin-left:20px;color:red;font-size:16px;'>*</span></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:20px;margin-left:20px;'>容量：</span><input id='size' class='addinfo' style='height:30px;width:260px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:20px;margin-left:20px;'>描述：</span><input id='description' class='addinfo' style='height:30px;width:260px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:20px;margin-left:20px;'>备注：</span><input id='remark' class='addinfo' style='height:30px;width:260px'></div>";
			html +="    	</div>";
		    html +="  	<div style='text-align:center;padding:20px;margin-top:10px;'>";
		    html +="      	<a id='resett2'>"+IALanguage.Main.reset+"</a>";
		    html +="      	<a id='add2'>"+IALanguage.Main.add+"</a>";
		    html +="  	</div>";
			html +="</div>";
			$("body").append(html);
			$("#addBox2").window({
		        title:IALanguage.Main.add_library,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:400,
				height:300,
				onClose:function(){
					$("#addBox2").window("destroy");
				}
		    });
	
		    $("#dbname").textbox({
		    	prompt:IALanguage.Main.prompt
		    });
		    $("#size").textbox({
		    });
		    $("#description").textbox({
		    });
		    $("#remark").textbox({
		    });
		    $("#resett2").linkbutton({
		    	iconCls:'icon-reload'
		    });
		    $("#add2").linkbutton({
		    	iconCls:'icon-ok'
		    });
		 
		    $("#resett2").off().on('click',function(){
		    	$(".addinfo").textbox('clear');
		    });
		    var reg3 = /^\w+$/;
		    $("#add2").off().on('click',function(){
		    	if($('#dbname').val()==''){
		    		$.messager.alert(IALanguage.Main.info,IALanguage.Main.please_message,"warning",function(){});
		    	}
		    	if(ip && port && reg3.test($("#dbname").val())){
		    		var Size = $("#size").val();
				    if(Size == ""){
				    	Size = 0;
				    }
				    Size = parseInt(Size);
					var Description = $("#description").val();
					if(Description == ""){
						Description = 0;
					}
					var Remark = $("#remark").val();
					if(Remark == ""){
						Remark = 0;
					}
					IAClient.AddIAServerInfo(iaType,$("#dbname").val(),ip,port);
					load();
				}else if(!reg3.test($("#dbname").val()) && !$("#dbname").val()==''){
			    	$.messager.alert(IALanguage.Main.info,IALanguage.Main.name_library,"warning",function(){})
			    };
		    })
		},
		
		AddBaseInfo:function(){
			IAClient.ObjSet=[];
			IAClient.faFlag=true;
			$("#SelectModify").window("destroy");
			$("#InfoWin").empty();
			var html="";
			html +="<div id='ImportBaseInfoWin' style='overflow:hidden;'>";
			html += "	<div id='InfoWin' style='padding-top:15px;'>";
			html += "   </div>";
		    html +="  	<div style='text-align:center;padding:20px;margin-top:10px;'>";
		    html +="      	<a id='lastStep'>"+IALanguage.Main.last+"</a>";
		    html +="      	<a id='nextStep'>"+IALanguage.Main.next+"</a>";
		    html +="  	</div>";
			html +="</div>";
			$("body").append(html);
			__iaf.Main.InputBasicInformation();
			$("#ImportBaseInfoWin").window({
		        title:IALanguage.Main.entry_message,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:400,
				height:400,
				onClose:function(){
					$("#ImportBaseInfoWin").window("destroy");
					if(IAClient.idNumber&&!IAClient.faFlag){
						IAClient.DeleteFaceBaseInfo(IAClient.idNumber);
					}
				}
		  });
		 
		 
		    $("#lastStep").linkbutton({
		    	disabled:true,
		    	onClick:function(){
			    	if(IAClient.idNumber){
						IAClient.DeleteFaceBaseInfo(IAClient.idNumber);
					}
		    	}
		    });
		    $("#nextStep").linkbutton({
		    	disabled:true,
		    	onClick:function(){
		    		if(IAClient.faFlag){
		    		    var Number=$("#Number").val();
				    	var Id=$("#IDNumber").val();
				    	var Name=$("#Name").val();
				    	var Sex=$("#Sex").val();
				    	var Function=$("#Function").val();
				    	var ProjectDepartment=$("#ProjectDepartment").val();
				    	var index=$("#ObjSet").combobox("getValue");
				    	IAClient.ObjSet.push(IAClient.ObjSets[index]);
				    	var reg = new RegExp("[\\u4E00-\\u9FFF]+","g");
						var pat=new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]"); 
						if(Number&&Id&&Name&&Sex&&Function&&ProjectDepartment&&index){
							if(pat.test(Name)==true)
							{
								alert(IALanguage.Main.name_error);
							    return;
							}
							if(pat.test(Function)==true)
							{
								alert(IALanguage.Main.function_error);
							    return;
							}
							if(pat.test(ProjectDepartment)==true)
							{
								alert(IALanguage.Main.projectDepartment_error);
							    return;
							}
							if(pat.test(Number)==true) 
							{ 
								alert(IALanguage.Main.number_error);
							    return;
							    if(pat.test(Id)==true) 
								{ 
									alert(IALanguage.Main.idNumber_error);
								    return;
								}
							}else if(reg.test(Number)){
								alert(IALanguage.Main.number_err);
								return;
								if(reg.test(Id)){
									alert(IALanguage.Main.idNumber_err);
									return;
								}
							}else if(Number.length>18){
								alert(IALanguage.Main.number_err);
								return;
								if(Id.length>18){
									alert(IALanguage.Main.idNumber_err);
									return;
								}
							}else{
								load();
								IAClient.AddFaceBaseInfo(Number,Function,ProjectDepartment,Name,Sex,Id);	
							}
						}else{
							alert(IALanguage.Main.improve_information);
						}
		    		}else{
		    			$("#ImportBaseInfoWin").window("destroy");
		    		}
		    	}
		    });
		},
		
		InputBasicInformation:function(){
			$("#InfoWin").empty();
			var html="";
			html += "<div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.number+"：</div><div id='NumberBox' style='float:left;'><input id='Number' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			html += "<div style='clear:left;height:17px;'></div>";
			html += "<div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.idNumber+"：</div><div id='IDNumberBox' style='float:left;'><input id='IDNumber' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			html += "<div style='clear:left;height:17px;'></div>";
			html += "<div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.name+"：</div><div id='NameBox' style='float:left;'><input id='Name' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			html += "<div style='clear:left;height:17px;'></div>";
			html += "<div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.sex+"：</div><div id='SexBox' style='float:left;'><input id='Sex' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			html += "<div style='clear:left;height:17px;'></div>";
			html += "<div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.post+"：</div><div id='FunctionBox' style='float:left;'><input id='Function' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			html += "<div style='clear:left;height:17px;'></div>";
			html += "<div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.Project_Department+"：</div><div id='ProjectDepartmentBox' style='float:left;'><input id='ProjectDepartment' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			html += "<div style='clear:left;height:17px;'></div>";
			html += "<div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.ObjSet+"：</div><div id='ObjSet' style='float:left;'><input id='ObjSet' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			html += "<div style='clear:left;height:17px;'></div>";
			html += "<div style='clear:left'></div>";
			$("#InfoWin").html(html);
			
		    $(".baseinfo").textbox({
		    });
		    $("#Sex").combobox({
		    	editable:false,
		    	value:IALanguage.Main.male,
		    	valueField: 'id',
			    textField: 'text',
			    panelHeight:50,
			    data: [{
			    	"id":IALanguage.Main.male,
			    	"text":IALanguage.Main.male
			    },{
			    	"id":IALanguage.Main.fomale,
			    	"text":IALanguage.Main.fomale
			    }]
		    });
		    $("#ObjSet").combobox({
		    	editable:false,
		    	value:"0",
		    	valueField: 'index',
			    textField: 'NUID',
			    panelHeight:50,
			    data: IAClient.ObjSets
		    });
		    $("#Number").textbox({
		    	required: true,
			    validType:['string','length[0,18]'],
		    	prompt:IALanguage.Main.Entry_not_modify,
		    	onChange:function(newValue,oldValue){
		    		var number= $("#Number").textbox('getValue');
		    		var idNumber=$("#IDNumber").textbox('getValue');
		    		if(number&&idNumber){		    			
						IAClient.CheckAttributes(number,idNumber);
		    		}
		    		if($("#IDNumber").textbox('isValid')&&$("#Number").textbox('isValid')){
		    			$("#nextStep").linkbutton("enable");
		    		}else{
		    			$("#nextStep").linkbutton("disable");
		    		}
		    	}
		    });
		    $("#IDNumber").textbox({
		    	required: true,
			    validType:['string','length[0,18]'],
		    	prompt:IALanguage.Main.Entry_not_modify,
		    	onChange:function(newValue,oldValue){
		    		var number= $("#Number").textbox('getValue');
		    		var idNumber=$("#IDNumber").textbox('getValue');
		    		if(number&&idNumber){
		    			IAClient.CheckAttributes(number,idNumber);
		    		}
		    		if($("#IDNumber").textbox('isValid')&&$("#Number").textbox('isValid')){
		    			$("#nextStep").linkbutton("enable");
		    		}else{
		    			$("#nextStep").linkbutton("disable");
		    		}
		    	}
		    });
		    $("#Name").textbox({
		    	required: true,
			    validType:['number','length[0,18]'],
		    	prompt:IALanguage.Main.Entry_not_modify,
		    	onChange:function(newValue,oldValue){
		    		if($("#Name").textbox('isValid')&&IAClient.subMit){
		    			$("#nextStep").linkbutton("enable");
		    		}else{
		    			$("#nextStep").linkbutton("disable");
		    		}
		    	}
		    });
		},
		InputFaceInformation:function(Id){
			var html = "";
			html += "<div style='height:20px;'></div>";
			html += "<div><div style='float:left;width:80px;margin-left:120px;'></div><div id='ServerPathBox' style='float:left;color:red;'></div></div>";
			html += "<div style='clear:left;height:10px;'></div>";
			html += "<div><div style='float:left;width:80px;margin-left:120px;'></div><div id='DBNameBox' style='float:left;color:red;'></div></div>";
			html += "<div style='clear:left;height:10px;'></div>";
			html += "<div style='margin:0 auto;border:1px solid #95B8E7;width:252px;height:162px;'><img class='fileimg' style='width:inherit;height:inherit;' /></div>";
			html += "<div style='height:2px;'></div>";
			html += "<div id='sliderWin' style='width:250px;height:5px;border:1px solid #95B8E7;border-radius:1px;margin:0 auto;'><div id='slider' style='background-color:#95B8E7;width:0px;border-radius:1px;font-size:1px;'>&nbsp;</div></div>";
			html += "<div style='clear:left;height:10px;'></div>";
			html += "<div><div style='float:left;width:80px;margin-left:40px;'>"+IALanguage.Main.selectImage+"：</div><div id='form_imgBox' style='float:left;'><form id='form_ImportImgFile' method='post' enctype='multipart/form-data' style=''><input id='addface' name='addface'  style='width:175px;overflow:hidden' /></form></div></div>";
			$("#InfoWin").html(html);

		    $(".baseinfo").textbox({
	    	});

	    	$("#ServerPath").combobox({});
		    $("#DBName").combobox({});

		    $("#addface").filebox({
		    	buttonText:IALanguage.Main.browse,
			    	onChange:function(){
			    		load();
		    			var objUrl = IAFace.GetObjectURL(this.form[2].files[0]);
						if (objUrl) {
							var fileParam = {
								request:{
									action:'AddFace',
									content:{"ID":Id,"ObjSets":IAClient.ObjSet}
								}
							}
							var fileParams = $.toJSON(fileParam);
							$(".fileimg").attr("src", objUrl);

							$(this).parent().ajaxSubmit({
								url:'php/index.php?'+ encodeURIComponent(fileParams),
								dataType:'json', 
									success: function(data) {
										if(data.response.errorCode == "0x0000"){
											IAClient.BaseInfo=null;
											IAClient.faFlag=true;
											var index=$("#FaceManage_combo_time_combox").combobox('getValue');
		                                    IAClient.QueryFaceInfo(index);
											var SliderAdd = function(){
												var sliderW = $("#slider").width() + 50 + 'px';
												$("#slider").css("width",sliderW);
												$("#slider").css("background-color","#95B8E7");
												if($("#slider").width() >= 250){
													clearInterval(Interval);
													$("#gif_win").remove();
													disLoad();
													$("#ImportBaseInfoWin").window("destroy");
													$.messager.show({
														title:IALanguage.Main.title,
														msg:IALanguage.Main.msg,
														showType:'slide'
													});
												}
											}
											var Interval = setInterval(SliderAdd,100);
										}else{
											$("#gif_win").remove();
											disLoad();
											Ajax.Construct(data.response.errorCode);
										}
									},    
								error:function(xhr){
								}
						 });
			    	  }
			     }
		   });
		},
		
		SelectWindow:function(select){
			var datagrid = $("#FaceManage_datagrid").datagrid('getSelected');
			if(datagrid||select==IALanguage.Main.add){
				var html = "";
				html += "<div id='SelectModify' style='text-align:center;'>";
				html += "	<div style='margin-top:70px;'>";
				html += "		<div id='selBaseInfo' style='float:left;border:2px solid green;width:150px;height:100px;border-radius:5px;margin-left:30px;background-color:#E0FFFF'><div style='margin-top:30px;font-weight:bold;font-size:23px;'>"+select+"基本信息</div></div>";
				html += "		<div id='selExtraInfo' style='float:left;border:2px solid green;width:150px;height:100px;border-radius:5px;margin-left:20px;background-color:#E0FFFF'><div style='margin-top:30px;font-weight:bold;font-size:23px;'>"+select+"补录信息</div></div>";
				html += "	</div>";
				html += "</div>";
				$("body").append(html);
				$("#SelectModify").window({
					title:select,
			        resizable:false,
			        draggable:false,
			        collapsible:false,
			        minimizable:false,
			        maximizable:false,
			        modal:true,
					width:400,
					height:300,
					onClose:function(){
						$("#SelectModify").window("destroy");
					}
			    });
			    $("#selBaseInfo").mouseover(function(){
			    	$("#selBaseInfo").css({"border":"3px solid green","cursor":"pointer"})
			    })
			    $("#selBaseInfo").mouseout(function(){
			    	$("#selBaseInfo").css("border","2px solid green")
			    })
			    $("#selExtraInfo").mouseover(function(){
			    	$("#selExtraInfo").css({"border":"3px solid green","cursor":"pointer"})
			    })
			    $("#selExtraInfo").mouseout(function(){
			    	$("#selExtraInfo").css("border","2px solid green")
			    })

			    $("#selBaseInfo").click(function(){
			    	switch(select){
			    		case IALanguage.Main.add:
			    		    IAFrame.Main.AddBaseInfo();
			    		break;
			    		case IALanguage.Main.modify:
			    		    IAFrame.Main.ModifyBaseInfo(IALanguage.Main.modify);
			    		break;
			    	}
			    });


			    $("#selExtraInfo").click(function(){
			    	switch(select){
			    		case IALanguage.Main.add:
			    		    IAFrame.Main.AddExtraInfo();
			    		break;
			    		case IALanguage.Main.modify:
			    		    IAFrame.Main.ModifyExtraInfo(IALanguage.Main.modify);
			    		break;
			    	}
			    });
			}else{
				$.messager.alert(IALanguage.Main.info,IALanguage.Main.please_select,"info",function(){});
			}
		},
		
		
		ModifyBaseInfo:function(){
			var datagrid = $("#FaceManage_datagrid").datagrid('getSelected');
			$("#SelectModify").window("destroy");
	    	var htmlstr="";
			htmlstr += "<div id='ModifyBaseInfoWin' style='overflow:auto;'>";
			htmlstr += "	<div id='InfoWin' style='padding-top:15px;'>";
			htmlstr += "    <div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.number+"：</div><div id='NumberBox' style='float:left;'><input id='modify-Number' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "    <div style='clear:left;height:17px;'></div>";
			htmlstr += "    <div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.idNumber+"：</div><div id='IDNumberBox' style='float:left;'><input id='modify-IDNumber' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "    <div style='clear:left;height:17px;'></div>";
			htmlstr += "    <div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.name+"：</div><div id='NameBox' style='float:left;'><input id='modify-Name' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "    <div style='clear:left;height:17px;'></div>";
			htmlstr += "    <div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.sex+"：</div><div id='SexBox' style='float:left;'><input id='modify-Sex' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "    <div style='clear:left;height:17px;'></div>";
			htmlstr += "    <div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.post+"：</div><div id='FunctionBox' style='float:left;'><input id='modify-Function' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "    <div style='clear:left;height:17px;'></div>";
			htmlstr += "    <div><div style='float:left;width:100px;margin-left:20px;'>"+IALanguage.Main.Project_Department+"：</div><div id='ProjectDepartmentBox' style='float:left;'><input id='modify-ProjectDepartment' class='baseinfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "		<div style='clear:left;height:10px;'></div>";
			htmlstr += "		<div style='clear:left'></div>";
			htmlstr += "    </div>";
		    htmlstr += "  	<div style='text-align:center;margin-top:15px;'>";
		    htmlstr += "      	<a id='ModifyBase_Reset'>"+IALanguage.Main.reset+"</a>";
		    htmlstr += "      	<a id='ModifyBase_Submit'>"+IALanguage.Main.submit+"</a>";
		    htmlstr += "  	</div>";
			htmlstr += "</div>";
			$("body").append(htmlstr);
			$("#ModifyBaseInfoWin").window({
		        title:IALanguage.Main.modify_message,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:370,
				height:340,
				onClose:function(){
					$("#ModifyBaseInfoWin").window("destroy");
				}
		    });
		    $("#ModifyBase_Reset").linkbutton({
		    	iconCls:'icon-reload'
		    });
		    $("#ModifyBase_Submit").linkbutton({
		    	iconCls:'icon-ok'
		    });
		    $("#modify-Number").val(datagrid.user_number);
		    $("#modify-Number").attr("disabled",true);
		    $("#modify-IDNumber").val(datagrid.user_idNumber);
		    $("#modify-IDNumber").attr("disabled",true);
		    $("#modify-Name").val(datagrid.user_name);
		    $("#modify-Name").attr("disabled",true);
		    $("#modify-Sex").val(datagrid.user_sex);
		    $("#modify-Function").val(datagrid.user_function);
		    $("#modify-ProjectDepartment").val(datagrid.user_projectDepartment);
	
		    $(".baseinfo").textbox({
		    });
	
	        $("#modify-Sex").combobox({
		    	editable:false,
		    	valueField: 'id',
			    textField: 'text',
			    panelHeight:50,
			    data: [{
			    	"id":IALanguage.Main.male,
			    	"text":IALanguage.Main.male
			    },{
			    	"id":IALanguage.Main.fomale,
			    	"text":IALanguage.Main.fomale
			    }]
		    });
		    $("#ModifyBase_Reset").click(function(){
		    	$("#modify-Function").textbox('clear');
		        $("#modify-ProjectDepartment").textbox('clear');
		    });
	        var pat=new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]"); 
		    $("#ModifyBase_Submit").click(function(){
		    	var TrueInfo=0;
		    	
	    		if($("#modify-Number").val() && $("#modify-Function").val() && $("#modify-ProjectDepartment").val() && $("#modify-Name").val() && $("#modify-Sex").val()){
	    			if(pat.test($("#modify-Name").val())==true)
					{
						alert(IALanguage.Main.name_error);
					    return;
					}
					if(pat.test($("#modify-Function").val())==true)
					{
						alert(IALanguage.Main.function_error);
					    return;
					}
					if(pat.test($("#modify-ProjectDepartment").val())==true)
					{
						alert(IALanguage.Main.projectDepartment_error);
					    return;
					}
	    			TrueInfo = 1;
		    	}
	
		    	if(TrueInfo == 1){
		    		load();
			    	var ModBasecallback = function(rv2){
			    		disLoad();
						if(rv2.response.errorCode == '0x0000'){
							$("#ModifyBaseInfoWin").window("destroy");
							var index=$("#FaceManage_combo_time_combox").combobox('getValue');
		                    IAClient.QueryFaceInfo(index);
						}else{
							Ajax.Construct(rv2.response.errorCode);
						}
					}
					var RequestContent = {
			    		number:$("#modify-Number").val(),
				    	function:$("#modify-Function").val(),
				    	projectDepartment:$("#modify-ProjectDepartment").val(),
				    	name:$("#modify-Name").val(),
				    	sex:$("#modify-Sex").val(),
				    	ID:datagrid.user_idNumber
			    	}
			    	Ajax.SendAjax('ModifyFaceBaseInfo',RequestContent,ModBasecallback);
			    }else{
			    	$.messager.alert(IALanguage.Main.info,IALanguage.Main.improve_information,"info",function(){});
			    }
		    })
		},
		
		
		
		ModifyExtraInfo:function(){
			var datagrid = $("#FaceManage_datagrid").datagrid('getSelected');
			if(datagrid.user_examResult=="-")
			{
				alert(IALanguage.Main.error_message);
				return;
			}      
			var personalFile_Index = datagrid.user_personalFileIndex;
			var id_a_index = datagrid.user_ID_A_Index;
			var id_b_index = datagrid.user_ID_B_Index;
			var paperPhoto_Index = datagrid.user_paperPhoto_Index;
			var medicalReport_Index = datagrid.user_medicalReportIndex;
			var SpecialWorkPermit_Index = datagrid.user_specialWorkPermit_Index;
			$("#SelectModify").window("destroy");
			var htmlstr="";
			htmlstr +="<div id='ModifyExtraInfoWin' style='overflow:auto;'>";
			htmlstr +="		<div id='InfoWin' style='padding-top:20px;'>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.name+"：</div><div id='NameBox' style='float:left;'><input id='Name' class='extra' type='text' style='width:215px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			
			htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.jobNumber+"：</div><div id='jobNumberBox' style='float:left;'><input id='jobNumber' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "		<div style='clear:left;height:10px;'></div>";
			htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.SubcontractTeam+"：</div><div id='SubcontractTeamBox' style='float:left;'><input id='SubcontractTeam' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "		<div style='clear:left;height:10px;'></div>";
			htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.Dept+"：</div><div id='DeptBox' style='float:left;'><input id='Dept' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "		<div style='clear:left;height:10px;'></div>";
			htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.Company+"：</div><div id='CompanyBox' style='float:left;'><input id='Company' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "		<div style='clear:left;height:10px;'></div>";
			htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.BirthDate+"：</div><div id='BirthDateBox' style='float:left;'><input id='BirthDate' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr += "		<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.personalFile+"：</div><div id='PersonFileBox' style='float:left;'><form id='form_PersonFile' action='action.php' method='post' enctype='multipart/form-data' style='float:left;'><input id='PersonFile' class='extraImg' name='UploadImage'  style='width:215px;overflow:hidden' /></form><img id='PersonFileImg' class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.id_a_index+"：</div><div id='IDCardBox_A' style='float:left;'><form id='form_IDCard__A' action='action.php' method='post' enctype='multipart/form-data' style='float:left;'><input id='IDCard_A' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img id='IDCard_AImg' class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.id_b_index+"：</div><div id='IDCardBox_B' style='float:left;'><form id='form_IDCard__B' action='action.php' method='post' enctype='multipart/form-data' style='float:left;'><input id='IDCard_B' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img id='IDCard_BImg' class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.paperPhoto+"：</div><div id='PaperPhotoBox' style='float:left;'><form id='form_PaperPhoto' action='action.php' method='post' enctype='multipart/form-data' style='float:left;'><input id='PaperPhoto' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img id='PaperPhotoImg' class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.medicalReport+"：</div><div id='PhysicalExamBox' style='float:left;'><form id='form_PhysicalExam' action='action.php' method='post' enctype='multipart/form-data' style='float:left;'><input id='PhysicalExam' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img id='PhysicalExamImg' class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.special_permit+"：</div><div id='SpecialProofBox' style='float:left;'><form id='form_SpecialProof' action='action.php' method='post' enctype='multipart/form-data' style='float:left;'><input id='SpecialProof' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img id='SpecialProofImg' class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.Exam_results+"：</div><div id='ExamResultBox' style='float:left;'><input id='ExamResult' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.Audit_status+"：</div><div id='AuditStatusBox' style='float:left;'><input id='AuditStatus' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.Audit_start+"：</div><div id='AuditBeginTimeBox' style='float:left;'><input id='AuditBeginTime' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.Audit_end+"：</div><div id='AuditEndTimeBox' style='float:left;'><input id='AuditEndTime' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.Auditor+"：</div><div id='AuditorBox' style='float:left;'><input id='Auditor' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.AuditTime+"：</div><div id='AuditTimeBox' style='float:left;'><input id='AuditTime' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.illegalInformation+"：</div><div id='IllegalInformationBox' style='float:left;'><input id='IllegalInformation' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div><div style='float:left;width:120px;margin-left:20px;'>"+IALanguage.Main.datagrid.description+"：</div><div id='DescriptionBox' style='float:left;'><input id='Description' class='extrainfo' type='text' style='width:215px;' /></div></div>";
			htmlstr +="			<div style='clear:left;height:10px;'></div>";
			htmlstr +="			<div style='clear:left;'></div>";
			htmlstr +="    	</div>";
		    htmlstr +="  	<div style='text-align:center;margin-top:15px;'>";
		    htmlstr +="      	<a id='ModifyExtra_Reset'>"+IALanguage.Main.reset+"</a>";
		    htmlstr +="      	<a id='ModifyExtra_Submit'>"+IALanguage.Main.submit+"</a>";
		    htmlstr +="  	</div>";
			htmlstr +="</div>";
			$("body").append(htmlstr);
			$("#ModifyExtraInfoWin").window({
		        title:IALanguage.Main.Modifying_supplementary_information,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:450,
				height:550,
				onClose:function(){
					$("#ModifyExtraInfoWin").window("destroy");
				}
		    });
		    $("#ModifyExtra_Reset").linkbutton({
		    	iconCls:'icon-reload'
		    });
		    $("#ModifyExtra_Submit").linkbutton({
		    	iconCls:'icon-ok'
		    });
		    $("#Name").textbox({
		    });
		    $(".extrainfo").textbox({
		    });
		    $(".extraImg").textbox({
		    });
	    	$('#AuditBeginTime').datebox({
	    		editable:false,
			});
			$('#AuditEndTime').datebox({
				editable:false ,
			});
			$('#AuditTime').datetimebox({
				editable:false ,
			});
			$('#BirthDate').datebox({
				editable:false ,
			});

		    $("#Name").textbox('setValue',datagrid.user_name);
		    $("#Name").textbox({disabled:true});
            
            $("#jobNumber").textbox('setValue',datagrid.user_jobNumber=="-"?"":datagrid.user_jobNumber);
		    $("#SubcontractTeam").textbox('setValue',datagrid.user_subcontractTeam=="-"?"":datagrid.user_subcontractTeam);
		    $("#Dept").textbox('setValue',datagrid.user_dept=="-"?"":datagrid.user_dept);
		    $("#Company").textbox('setValue',datagrid.user_company=="-"?"":datagrid.user_company);
		    $("#BirthDate").textbox('setValue',datagrid.user_birthDate=="-"?"":datagrid.user_birthDate);
            
		    $("#PersonFile").textbox('setValue',datagrid.user_personalFileName=="-"?"":datagrid.user_personalFileName);
		    $("#IDCard_A").textbox('setValue',datagrid.user_ID_A_FileName=="-"?"":datagrid.user_ID_A_FileName);
		    $("#IDCard_B").textbox('setValue',datagrid.user_ID_B_FileName=="-"?"":datagrid.user_ID_B_FileName);
		    $("#PaperPhoto").textbox('setValue',datagrid.user_paperPhotoFileName=="-"?"":datagrid.user_paperPhotoFileName);
		    $("#PhysicalExam").textbox('setValue',datagrid.user_medicalReportFileName=="-"?"":datagrid.user_medicalReportFileName);
		    $("#SpecialProof").textbox('setValue',datagrid.user_specialWorkPermitFileName=="-"?"":datagrid.user_specialWorkPermitFileName);
		    $("#ExamResult").textbox('setValue',datagrid.user_examResult=="-"?"":datagrid.user_examResult);
		    $("#AuditStatus").textbox('setValue',datagrid.user_auditStatus=="-"?"":datagrid.user_auditStatus);
		    $("#AuditBeginTime").datebox('setValue',datagrid.user_auditBeginTime=="-"?"":datagrid.user_auditBeginTime);
		    $("#AuditEndTime").datebox('setValue',datagrid.user_auditEndTime=="-"?"":datagrid.user_auditEndTime);
		    $("#Auditor").textbox('setValue',datagrid.user_auditor=="-"?"":datagrid.user_auditor);
		    $("#AuditTime").datetimebox('setValue',datagrid.user_auditTime=="-"?"":datagrid.user_auditTime);
		    $("#IllegalInformation").textbox('setValue',datagrid.user_IllegalInformation=="-"?"":datagrid.user_IllegalInformation);
		    $("#Description").textbox('setValue',datagrid.user_description=="-"?"":datagrid.user_description);

		    var personPath = datagrid.user_PersonalPath;

		    var IDCARD_APath = datagrid.user_ID_A_Path;

		    var IDCARD_BPath = datagrid.user_ID_B_Path;

		    var PaperPhoPath = datagrid.user_paperPhotoPath;

		    var PhysicalPath = datagrid.user_medicalReportPath;

		    var SpecialProofPath = datagrid.user_sourcePicturePath;


		    $("#PersonFileImg").attr("src",personPath);
		    $("#IDCard_AImg").attr("src",IDCARD_APath);
		    $("#IDCard_BImg").attr("src",IDCARD_BPath);
		    $("#PaperPhotoImg").attr("src",PaperPhoPath);
		    $("#PhysicalExamImg").attr("src",PhysicalPath);
		    $("#SpecialProofImg").attr("src",SpecialProofPath);

		    $(".extrainfo").textbox({
		    });
		    $(".extraImg").filebox({
		    	buttonText:IALanguage.Main.browse,
		    	onChange:function(){
		    		load();
		    		var objUrl = IAFace.GetObjectURL(this.form[2].files[0]);
					if (objUrl) {
						var fileParam = {
							request:{
								action:'AddUploadFiles',
								content:''
							}
						}
						var fileParams = $.toJSON(fileParam);
						var element = $(this);
						$(this).parent().next().attr("src", objUrl);
						$(this).parent().ajaxSubmit({
							url:'php/index.php?'+ encodeURIComponent(fileParams),
							dataType:'json', 
							success: function(data) {
								disLoad();
								if(element.attr('id') == 'PersonFile'){
									personalFile_Index = data.response.content;
								}
								if(element.attr('id') == 'IDCard_A'){
									id_a_index = data.response.content;
								}
								if(element.attr('id') == 'IDCard_B'){
									id_b_index = data.response.content;
								}
								if(element.attr('id') == 'PaperPhoto'){
									paperPhoto_Index = data.response.content;
								}
								if(element.attr('id') == 'PhysicalExam'){
									medicalReport_Index = data.response.content;
								}
								if(element.attr('id') == 'SpecialProof'){
									SpecialWorkPermit_Index = data.response.content;
								}
							},    
							error:function(xhr){
								$.messager.alert(IALanguage.Main.info,IALanguage.Main.Upload_pictures_failed,"info",function(){});
							}
					  	});
					}
		    	}
		    });

		    
			$(".fileimg").dblclick(function(){
				var htmlstr = "";
				htmlstr += "<div id='imgwin'>";
				htmlstr += "	<div><img id='winimg' src='' style='width:585px;height:360px;' /></div>"
				htmlstr += "</div>";
				$("body").append(htmlstr);
				$("#imgwin").window({
			        title:' ',
			        resizable:false,
			        draggable:false,
			        collapsible:false,
			        minimizable:false,
			        maximizable:false,
			        modal:true,
					width:600,
					height:400,
					onClose:function(){
						$("#imgwin").window("destroy");
					}
			    });
			    var imgsrc = $(this).attr("src");
			    $("#winimg").attr("src",imgsrc)
			})

		    $("#ModifyExtra_Reset").click(function(){
		    	$(".fileimg").removeAttr("src");
		    	$(".extrainfo").textbox('reset');
		    	$(".extraImg").filebox('reset');
		    })

		    $("#ModifyExtra_Submit").click(function(){
		    	var RequestContent = {
		    		userIndex:datagrid.user_manInfoEx_Index,
		    		examResult:$("#ExamResult").val() || "",
			    	jobNumber:$("#jobNumber").val() || "",
			    	SubcontractTeam:$("#SubcontractTeam").val() || "",
			    	Dept:$("#Dept").val() || "",
			    	Company:$("#Company").val() || "",
			    	BirthDate:$("#BirthDate").datebox('getValue') || "",
			    	personalFile_Index:personalFile_Index || "",
			    	id_a_index:id_a_index || "",
			    	id_b_index:id_b_index || "",
			    	paperPhoto_Index:paperPhoto_Index || "",
			    	examResult:$("#ExamResult").val(),
			    	medicalReport_Index:medicalReport_Index || "",
			    	SpecialWorkPermit_Index:SpecialWorkPermit_Index || "",
			    	AuditStatus:$("#AuditStatus").val(),
			    	AuditBeginTime:$("#AuditBeginTime").datebox('getValue'),
			    	AuditEndTime:$("#AuditEndTime").datebox('getValue'),
			    	Auditor:$("#Auditor").val(),
			    	AuditTime:$("#AuditTime").datetimebox('getValue'),
			    	illegalInformation:$("#IllegalInformation").val(),
			    	description:$("#Description").val()
		    	}
		    	var AddEPInfocallback = function(rv3){
		    		if(rv3.response.errorCode == '0x0000'){
		    			var index=$("#FaceManage_combo_time_combox").combobox('getValue');
	                    IAClient.QueryFaceInfo(index);
		    			$("#ModifyExtraInfoWin").window("destroy");
		    		}else{
		    			Ajax.Construct(rv3.response.errorCode);
		    		}
		    	}
		    	Ajax.SendAjax('ModifyFaceAdditionalInfo',RequestContent,AddEPInfocallback);
		    });
			
		},
		
		
		AddExtraInfo:function(){
			$("#SelectModify").window("destroy");
			var datagrid = $("#FaceManage_datagrid").datagrid('getSelected');
			if(datagrid){
				if(!datagrid.user_manInfoEx_Index){
					$("#SelectAdd").window("destroy");
					var personalFile_Index = "";
					var id_a_index = "";
					var id_b_index = "";
					var paperPhoto_Index = "";
					var medicalReport_Index = "";
					var SpecialWorkPermit_Index = "";
					var htmlstr="";
					htmlstr += "<div id='AddExtraInfoWin' style='overflow:auto;'>";
					htmlstr += "	<div id='InfoWin' style='padding-top:20px;'>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>姓名：</div><div id='NameBox' style='float:left;'><input id='Name' class='extra' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>工号：</div><div id='jobNumberBox' style='float:left;'><input id='jobNumber' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>所属分包队伍：</div><div id='SubcontractTeamBox' style='float:left;'><input id='SubcontractTeam' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>部门：</div><div id='DeptBox' style='float:left;'><input id='Dept' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>公司：</div><div id='CompanyBox' style='float:left;'><input id='Company' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>生日：</div><div id='BirthDateBox' style='float:left;'><input id='BirthDate' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>个人档案：</div><div id='PersonFileBox' style='float:left;'><form id='form_PersonFile' method='post' enctype='multipart/form-data' style='float:left;'><input id='PersonFile' class='extraImg' name='UploadImage'  style='width:215px;overflow:hidden' /></form><img class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>身份证正面：</div><div id='IDCardBox_A' style='float:left;'><form id='form_IDCard__A' method='post' enctype='multipart/form-data' style='float:left;'><input id='IDCard_A' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>身份证反面：</div><div id='IDCardBox_B' style='float:left;'><form id='form_IDCard__B' method='post' enctype='multipart/form-data' style='float:left;'><input id='IDCard_B' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>试卷照片：</div><div id='PaperPhotoBox' style='float:left;'><form id='form_PaperPhoto' method='post' enctype='multipart/form-data' style='float:left;'><input id='PaperPhoto' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>体检报告：</div><div id='PhysicalExamBox' style='float:left;'><form id='form_PhysicalExam' method='post' enctype='multipart/form-data' style='float:left;'><input id='PhysicalExam' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>特种作业证：</div><div id='SpecialProofBox' style='float:left;'><form id='form_SpecialProof' method='post' enctype='multipart/form-data' style='float:left;'><input id='SpecialProof' class='extraImg' name='UploadImage' style='width:215px;overflow:hidden' /></form><img class='fileimg' src='' style='float:left;margin-left:10px;width:40px;height:20px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>考试成绩：</div><div id='ExamResultBox' style='float:left;'><input id='ExamResult' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>审核状态：</div><div id='AuditStatusBox' style='float:left;'><input id='AuditStatus' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>审核开始有效期：</div><div id='AuditBeginTimeBox' style='float:left;'><input id='AuditBeginTime' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>审核结束有效期：</div><div id='AuditEndTimeBox' style='float:left;'><input id='AuditEndTime' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>审核人：</div><div id='AuditorBox' style='float:left;'><input id='Auditor' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>审核时间：</div><div id='AuditTimeBox' style='float:left;'><input id='AuditTime' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>违章信息：</div><div id='IllegalInformationBox' style='float:left;'><input id='IllegalInformation' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div><div style='float:left;width:120px;margin-left:20px;'>描述：</div><div id='DescriptionBox' style='float:left;'><input id='Description' class='extrainfo' type='text' style='width:215px;' /></div></div>";
					htmlstr += "		<div style='clear:left;height:10px;'></div>";
					htmlstr += "		<div style='clear:left;'></div>";
					htmlstr += "	</div>";
				    htmlstr += "  	<div style='text-align:center;margin-top:15px;'>";
				    htmlstr += "      	<a id='AddExtraInfo_Reset'>"+IALanguage.Main.reset+"</a>";
				    htmlstr += "      	<a id='AddExtraInfo_Submit'>"+IALanguage.Main.submit+"</a>";
				    htmlstr += "  	</div>";
					htmlstr += "</div>";
					$("body").append(htmlstr);
					$("#AddExtraInfoWin").window({
				        title:IALanguage.Main.Supplementary_personal_information,
				        resizable:false,
				        draggable:false,
				        collapsible:false,
				        minimizable:false,
				        maximizable:false,
				        modal:true,
						width:450,
						height:550,
						onClose:function(){
							$("#AddExtraInfoWin").window("destroy");
						}
				    });
				    $("#AddExtraInfo_Reset").linkbutton({
				    	iconCls:'icon-reload'
				    });
				    $("#AddExtraInfo_Submit").linkbutton({
				    	iconCls:'icon-ok'
				    });
	                $("#Name").textbox({});
				    $(".extrainfo").textbox({});
					$(".extraImg").filebox({});
	
					$('#AuditBeginTime').datebox({
						editable:false 
					});
					$('#AuditEndTime').datebox({
						editable:false 
					});
					$('#BirthDate').datebox({
						editable:false 
					});
					$('#AuditTime').datetimebox({
						editable:false 
					});
	
				    $("#Name").textbox('setValue',datagrid.user_name);
				    $("#Name").textbox({disabled:true});
	
				    
			    	$(".extrainfo").textbox({
				    });

				    $(".extraImg").filebox({
				    	buttonText:IALanguage.Main.browse,
				    	onChange:function(){
				    		load();
				    		var objUrl = IAFace.GetObjectURL(this.form[2].files[0]);
							if (objUrl) {
								var fileParam = {
									request:{
										action:'AddUploadFiles',
										content:''
									}
								}
								var fileParams = $.toJSON(fileParam);
								var element = $(this);
								$(this).parent().next().attr("src", objUrl);
								$(this).parent().ajaxSubmit({
									url:'php/index.php?'+ encodeURIComponent(fileParams),
									dataType:'json', 
									success: function(data) {
										disLoad();
										if(element.attr('id') == 'PersonFile'){
											personalFile_Index = data.response.content;
										}
										if(element.attr('id') == 'IDCard_A'){
											id_a_index = data.response.content;
										}
										if(element.attr('id') == 'IDCard_B'){
											id_b_index = data.response.content;
										}
										if(element.attr('id') == 'PaperPhoto'){
											paperPhoto_Index = data.response.content;
										}
										if(element.attr('id') == 'PhysicalExam'){
											medicalReport_Index = data.response.content;
										}
										if(element.attr('id') == 'SpecialProof'){
											SpecialWorkPermit_Index = data.response.content;
										}
									},    
									error:function(xhr){
										$.messager.alert(IALanguage.Main.info,IALanguage.Main.Upload_pictures_failed,"info",function(){});
									}
							  	});
							}
				    	}
				    })

				    $("#AddExtraInfo_Reset").click(function(){
				    	$(".fileimg").removeAttr("src");
				    	$(".extrainfo").textbox('reset');
				    	$(".extraImg").filebox('reset');
				    })

				    $("#AddExtraInfo_Submit").click(function(){
				    	if($("#BirthDate").datebox('getValue') && $("#Company").val() && $("#Dept").val() && $("#SubcontractTeam").val() && $("#jobNumber").val() && personalFile_Index && id_a_index && id_b_index && paperPhoto_Index && medicalReport_Index && SpecialWorkPermit_Index && $("#ExamResult").val() && $("#AuditStatus").val() && $("#AuditBeginTime").val() && $("#AuditEndTime").val() && $("#Auditor").val() && $("#AuditTime").val() && $("#IllegalInformation").val()){
					    	var RequestContent = {
					    		userIndex:datagrid.user_index,
					    		examResult:$("#ExamResult").val(),
						    	jobNumber:$("#jobNumber").val(),
						    	SubcontractTeam:$("#SubcontractTeam").val(),
						    	Dept:$("#Dept").val(),
						    	Company:$("#Company").val(),
						    	BirthDate:$("#BirthDate").datebox('getValue'),
					    		examResult:$("#ExamResult").val(),
						    	personalFile_Index:personalFile_Index,
						    	id_a_index:id_a_index,
						    	id_b_index:id_b_index,
						    	paperPhoto_Index:paperPhoto_Index,
						    	medicalReport_Index:medicalReport_Index,
						    	SpecialWorkPermit_Index:SpecialWorkPermit_Index,
						    	AuditStatus:$("#AuditStatus").val(),
						    	AuditBeginTime:$("#AuditBeginTime").datebox('getValue'),
						    	AuditEndTime:$("#AuditEndTime").datebox('getValue'),
						    	Auditor:$("#Auditor").val(),
						    	AuditTime:$("#AuditTime").datetimebox('getValue'),
						    	illegalInformation:$("#IllegalInformation").val(),
						    	description:$("#Description").val()
					    	}
					    	var AddEPInfocallback = function(rv){
					    		if(rv.response.errorCode == '0x0000'){
					    			var index=$("#FaceManage_combo_time_combox").combobox('getValue');
	    		                    IAClient.QueryFaceInfo(index);
					    			$("#AddExtraInfoWin").window("destroy");
					    		}else{
					    			Ajax.Construct(rv.response.errorCode);
					    		}
					    	}
					    	Ajax.SendAjax('AddFaceAdditionalInfo',RequestContent,AddEPInfocallback);
					    }else{
					    	$.messager.alert(IALanguage.Main.info,IALanguage.Main.complete_supplementary_information,"info",function(){});
					    }
				    });
				    
				    
	
				    
					$(".fileimg").dblclick(function(){
						var htmlstr = "";
						htmlstr += "<div id='imgwin'>";
						htmlstr += "	<div><img id='winimg' src='' style='width:585px;height:360px;' /></div>"
						htmlstr += "</div>";
						$("body").append(htmlstr);
						$("#imgwin").window({
					        title:' ',
					        resizable:false,
					        draggable:false,
					        collapsible:false,
					        minimizable:false,
					        maximizable:false,
					        modal:true,
							width:600,
							height:400,
							onClose:function(){
								$("#imgwin").window("destroy");
							}
					    });
					    var imgsrc = $(this).attr("src");
					    $("#winimg").attr("src",imgsrc)
					})
				}else{
					$.messager.alert(IALanguage.Main.info,IALanguage.Main.Already_supplementary_information,"info",function(){});
				}
			}else{
				$.messager.alert(IALanguage.Main.info,IALanguage.Main.please_select,"info",function(){});
			}
		},
			
		CreateWinBoxInfo:function(attributes){
			var html="";
			html +="<div id='winBox'>";
			html +="	<div>";
			html +="     	<div id='imagebox1' style='padding:5px;border:1px solid #95B8E7;text-align:center'>";
			html +="        	<img class='table_img' id='showImageOne' src='"+attributes.user_sourcePicturePath+"' style='width:100px;height:100px;border:1px black dotted'/>";
			html +="     	</div>";
			html +="	</div>";
			html +="  	<div id='window_info' style=''>";
			html +="		<table id='InfoTable' style='width:369px;overflow:hidden;'></table>";
		    html +="  	</div>";
			html +="</div>";
			$("body").append(html);
			$("#winBox").window({
		        title:attributes.user_name,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
				width:400,
				height:600,
			    modal:true,
				onClose:function(){
					$("#winBox").window('destroy');
				}
		    });
	
		    $('#InfoTable').propertygrid({
			    showGroup: false,
			    showHeader: false,
			    scrollbarSize: 0
			});
	
	
		    var htmlstr1 = "<img class='table_img' src='"+attributes.user_PersonalPath+"' style='width:50px;height:35px;'/>";

	
		    var htmlstr2 = "<img class='table_img' src='"+attributes.user_ID_A_Path+"' style='width:50px;height:35px;'/>";
	
		   
		    var htmlstr3 = "<img class='table_img' src='"+attributes.user_ID_B_Path+"' style='width:50px;height:35px;'/>";	
		    var htmlstr4 = "<img class='table_img' src='"+attributes.user_paperPhotoPath+"' style='width:50px;height:35px;'/>";
	
		    var htmlstr5 = "<img class='table_img' src='"+attributes.user_medicalReportPath+"' style='width:50px;height:35px;'/>";
		
	
			var rows = [
		        { "name": IALanguage.Main.datagrid.Number,"value": attributes.user_number || "-"},
		        { "name": IALanguage.Main.datagrid.JobNumber,"value": attributes.user_jobNumber || "-"},
		        { "name": IALanguage.Main.datagrid.post, "value": attributes.user_function},
		        { "name": IALanguage.Main.datagrid.Project_Department, "value": attributes.user_projectDepartment || "-"},
		        { "name": IALanguage.Main.datagrid.group, "value": attributes.user_groupName || "-"},
		        { "name": IALanguage.Main.datagrid.name, "value": attributes.user_name || "-"},
		        { "name": IALanguage.Main.datagrid.sex, "value": attributes.user_sex || "-"},
		        { "name": IALanguage.Main.datagrid.SubcontractTeam, "value": attributes.user_subcontractTeam || "-"},
		        { "name": IALanguage.Main.datagrid.Dept, "value": attributes.user_dept || "-"},
		        { "name": IALanguage.Main.datagrid.Company, "value": attributes.user_company || "-"},
		        { "name": IALanguage.Main.datagrid.BirthDate, "value": attributes.user_birthDate || "-"},
		        { "name": IALanguage.Main.datagrid.idNumber, "value": attributes.user_idNumber || "-"},
		        { "name": IALanguage.Main.datagrid.personalFile, "value": htmlstr1 || "-"},
		        { "name": IALanguage.Main.datagrid.id_a_index, "value": htmlstr2 || "-"},
		        { "name": IALanguage.Main.datagrid.id_b_index, "value": htmlstr3 || "-"},
		        { "name": IALanguage.Main.datagrid.paperPhoto, "value": htmlstr4 || "-"},
		        { "name": IALanguage.Main.datagrid.medicalReport, "value": htmlstr5 || "-"},
		        { "name": IALanguage.Main.datagrid.Exam_results, "value": attributes.user_examResult || "-"},
		        { "name": IALanguage.Main.datagrid.Audit_status, "value": attributes.user_auditStatus || "-"},
		        { "name": IALanguage.Main.datagrid.Audit_start, "value": attributes.user_auditBeginTime || "-"},
		        { "name": IALanguage.Main.datagrid.Audit_end, "value": attributes.user_auditEndTime || "-"},
		        { "name": IALanguage.Main.datagrid.Auditor, "value": attributes.user_auditor || "-"},
		        { "name": IALanguage.Main.datagrid.AuditTime, "value": attributes.user_auditTime || "-"},
		        { "name": IALanguage.Main.datagrid.illegalInformation, "value": attributes.user_IllegalInformation || "-"}
		    ];
			$('#InfoTable').propertygrid('loadData',rows);
	
	
		    $(".table_img").off().on('click',function(){
				$(".main-images-box").css({
					"display":"block",
					"background":"#000000 url("+$(this)[0].src+") no-repeat center center"
				});
			});
		},
		
		
		
		AddPlateInfo:function(){
			var html="";
			html +="<div id='addPlateInfo' style='overflow:hidden;'>";
			html +="        <div style='margin-top:20px;'><span style='margin-right:15px;margin-left:20px;'>"+IALanguage.Main.plate.plate_number+"：</span><input id='PlateNo' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:30px;margin-left:20px;'>"+IALanguage.Main.plate.plate_color+"：</span><input id='PlateColor' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:21px;margin-left:20px;'>"+IALanguage.Main.plate.Vehicle_type+"：</span><input id='VehicleClass' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:16px;margin-left:20px;'>"+IALanguage.Main.plate.Vehicle_brand+"：</span><input id='VehicleBrand' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:11px;margin-left:20px;'>"+IALanguage.Main.plate.Vehicle_model+"：</span><input id='VehicleModel' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:7px;margin-left:20px;'>"+IALanguage.Main.plate.trailer_number+"：</span><input id='PlateNoAttach' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:22px;margin-left:20px;'>"+IALanguage.Main.plate.trailer_color+"：</span><input id='VehicleColor' class='addinfo' style='height:25px;width:200px'></div>";
		    html +="  	<div style='text-align:center;padding:20px;margin-top:10px;'>";
		    html +="      	<a id='reset'>"+IALanguage.Main.reset+"</a>";
		    html +="      	<a id='add_plate'>"+IALanguage.Main.add+"</a>";
		    html +="  	</div>";
			html +="</div>";
			$("body").append(html);
			$("#addPlateInfo").window({
		        title:IALanguage.Main.add,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:350,
				height:380,
				onClose:function(){
					$("#addPlateInfo").window("destroy");
				}
		    });
	
		     $(".addinfo").textbox({
		    	
		    });
		    $("#PlateNo").textbox({
		    	required: true,
			    validType:['number','length[0,18]'],
		    });
		    $("#PlateColor").textbox({
		    	required: true,
			    validType:['number','length[0,18]'],
		    });
		    $("#reset").linkbutton({
		    	iconCls:'icon-reload'
		    });
		    $("#add_plate").linkbutton({
		    	iconCls:'icon-ok'
		    });
		 
		    $("#reset").off().on('click',function(){
		    	$(".addinfo").textbox('clear');
		    });
		    $("#add_plate").off().on('click',function(){
		    	var PlateColor=$("#PlateColor").val();
		    	var PlateNo=$("#PlateNo").val();
		    	var PlateNoAttach=$("#PlateNoAttach").val();
		    	var VehicleClass=$("#VehicleClass").val();
		    	var VehicleBrand=$("#VehicleBrand").val();
		    	var VehicleModel=$("#VehicleModel").val();
		    	var VehicleColor=$("#VehicleColor").val();
		    	IAClient.AddCarIDInfo(PlateColor,PlateNo,PlateNoAttach,VehicleClass,VehicleBrand,VehicleModel,VehicleColor);
		    });
		
		},
		
		ModifyPlateInfo:function(plateIndex,PlateColor,PlateNo,PlateNoAttach,VehicleClass,VehicleBrand,VehicleModel,VehicleColor){
			var html="";
			html +="<div id='ModifyPlateInfo' style='overflow:hidden;'>";
			html +="        <div style='margin-top:20px;'><span style='margin-right:15px;margin-left:20px;'>"+IALanguage.Main.plate.plate_number+"：</span><input id='PlateNo' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:30px;margin-left:20px;'>"+IALanguage.Main.plate.plate_color+"：</span><input id='PlateColor' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:21px;margin-left:20px;'>"+IALanguage.Main.plate.Vehicle_type+"：</span><input id='VehicleClass' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:16px;margin-left:20px;'>"+IALanguage.Main.plate.Vehicle_brand+"：</span><input id='VehicleBrand' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:11px;margin-left:20px;'>"+IALanguage.Main.plate.Vehicle_model+"：</span><input id='VehicleModel' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:7px;margin-left:20px;'>"+IALanguage.Main.plate.trailer_number+"：</span><input id='PlateNoAttach' class='addinfo' style='height:25px;width:200px'></div>";
			html +="        <div style='margin-top:10px;'><span style='margin-right:22px;margin-left:20px;'>"+IALanguage.Main.plate.trailer_color+"：</span><input id='VehicleColor' class='addinfo' style='height:25px;width:200px'></div>";
		    html +="  	<div style='text-align:center;padding:20px;margin-top:10px;'>";
		    html +="      	<a id='reset'>"+IALanguage.Main.reset+"</a>";
		    html +="      	<a id='add_plate'>"+IALanguage.Main.add+"</a>";
		    html +="  	</div>";
			html +="</div>";
			$("body").append(html);
			$("#ModifyPlateInfo").window({
		        title:IALanguage.Main.modify,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:350,
				height:380,
				onClose:function(){
					$("#ModifyPlateInfo").window("destroy");
				}
		   });
		   $(".addinfo").textbox({
		    	
		    });
		    $("#PlateNo").textbox({
		    	required: true,
			    validType:['number','length[0,18]'],
		    });
		    $("#PlateColor").textbox({
		    	required: true,
			    validType:['number','length[0,18]'],
		    });
		    $("#reset").linkbutton({
		    	iconCls:'icon-reload'
		    });
		    $("#add_plate").linkbutton({
		    	iconCls:'icon-ok'
		    });
		 
		    $("#reset").off().on('click',function(){
		    	$(".addinfo").textbox('clear');
		    });
		    $("#add_plate").off().on('click',function(){
		    	var PlateColor=$("#PlateColor").val();
		    	var PlateNo=$("#PlateNo").val();
		    	var PlateNoAttach=$("#PlateNoAttach").val();
		    	var VehicleClass=$("#VehicleClass").val();
		    	var VehicleBrand=$("#VehicleBrand").val();
		    	var VehicleModel=$("#VehicleModel").val();
		    	var VehicleColor=$("#VehicleColor").val();
		    	IAClient.ModifyCarIDInfo(plateIndex,PlateColor,PlateNo,PlateNoAttach,VehicleClass,VehicleBrand,VehicleModel,VehicleColor);
		    });
		},
		
		AddGroupResourceInfo:function(str){
			var html="";
			html +="<div id='AddGroupResourceInfo' style='overflow:hidden;'>";
			html +="    <div style='width:330px;height:280px;'>";
			html +="         <div id='AddGroupResourceInfo_datagrid'></div>";
			html +="    </div>";
		    html +="  	<div style='text-align:center;padding:20px;'>";
		    html +="      	<a id='AddGroupResource'>"+IALanguage.Main.submit+"</a>";
		    html +="  	</div>";
			html +="</div>";
			$("body").append(html);
			$("#AddGroupResourceInfo").window({
		        title:IALanguage.Main.plate.Add_organization_resources,
		        resizable:false,
		        draggable:false,
		        collapsible:false,
		        minimizable:false,
		        maximizable:false,
		        modal:true,
				width:330,
				height:380,
				onClose:function(){
					$("#AddGroupResourceInfo").window("destroy");
				}
		  });
		  $("#AddGroupResource").linkbutton({
		    	iconCls:'icon-ok'
		   });
		   if(str==IALanguage.Main.face){
			   	Ajax.SendAjax('QueryAllFaceInfo',{},function(rv){
				   	    if(rv.response.errorCode=="0x0000"){
				   	    	var data=new Array();
				   	    	for(var i=0;i<rv.response.content.length;i++){
		    					data.push({
		    						index:rv.response.content[i].user_index,
		    						user_name:rv.response.content[i].user_name
		    					});
		    				}
				   	    	$("#AddGroupResourceInfo_datagrid").datagrid({
						   	    fit:true,
								fitColumns:true,
								border:true,
							    columns:[[
									{field:'user_name',title:IALanguage.Main.username,width:100,align:'center'},
							    ]],
							    data:data
						   });
				   	    }
				   });
				  
				  $("#AddGroupResource").off().on('click',function(){
				  	    var nodes=$("#AddGroupResourceInfo_datagrid").datagrid('getSelections');
				  	    if(nodes.length>0)
				  	    {
				  	    	var resList=[];
				  	    	for(var i=0;i<nodes.length;i++)
				  	    	{
				  	    		resList.push(nodes[i].index);
				  	    	}
				  	    	var node=$('#OrganizeManage_layout_Tree').tree('getSelected');
				  	    	IAClient.AddGroupResourceInfo(node.id,0,resList);
				  	    }
				  	    else{
				  	    	$.messager.alert(IALanguage.Main.info,IALanguage.Main.please_select,"info",function(){});
				  	    }
				  });
		   }else if(str==IALanguage.Main.plate.plate){
			   	Ajax.SendAjax('QueryCarIDInfo',{},function(rv){
			   	    if(rv.response.errorCode=="0x0000"){
			   	    	var data=new Array();
			   	    	for(var i=0;i<rv.response.content.length;i++){
	    					data.push({
	    						index:rv.response.content[i].Index,
	    						PlateNo:rv.response.content[i].PlateNo
	    					});
	    				}
			   	    	$("#AddGroupResourceInfo_datagrid").datagrid({
					   	    fit:true,
							fitColumns:true,
							border:true,
						    columns:[[
								{field:'PlateNo',title:IALanguage.Main.plate.plate_number,width:100,align:'center'},
						    ]],
						    data:data
					   });
			   	    }
			   });
			  
			  $("#AddGroupResource").off().on('click',function(){
			  	    var nodes=$("#AddGroupResourceInfo_datagrid").datagrid('getSelections');
			  	    if(nodes.length>0)
			  	    {
			  	    	var resList=[];
			  	    	for(var i=0;i<nodes.length;i++)
			  	    	{
			  	    		resList.push(nodes[i].index);
			  	    	}
			  	    	var node=$('#PlateOrganizeManage_layout_Tree').tree('getSelected');
			  	    	IAClient.AddGroupResourceInfo(node.id,1,resList);
			  	    }
			  	    else{
			  	    	$.messager.alert(IALanguage.Main.info,IALanguage.Main.please_select,"info",function(){});
			  	    }
			   });
		   }
		},
		
    },
    end : true
}
//弹出加载层
function load() 
{  
    $("<div class=\"datagrid-mask\"></div>").css({ display: "block", width: "100%", height: $(window).height() }).appendTo("body");  
    $("<div class=\"datagrid-mask-msg\"></div>").html(IALanguage.Main.load).appendTo("body").css({ display: "block", left: ($(document.body).outerWidth(true) - 190) / 2, top: ($(window).height() - 45) / 2 });  
}
//取消加载层  
function disLoad() {  
    $(".datagrid-mask").remove();  
    $(".datagrid-mask-msg").remove();  
}
