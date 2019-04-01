var IAFace = __ifa = {
	
	
	MainCSS:function(){
		var width=$(window).width();
		var height=$(window).height();
		$("html").css({
			"width": width,
			"height": height,
			"margin":"0px"
		});
		$("body").css({
			"width": width,
			"height": height,
			"margin":"0px"
		});
		$(".main").css({
			"width": width,
			"height": height,
			"margin":"0px"
		});
        $("#main_head").css({
			"width": width,
			"height": "40px",
			"margin":"0px",
			"background":"#256688"
		});
		$("#main_content_face").css({
			"width": width,
			"height": (height-40)+"px",
			"margin":"0px"
		});
		
		$("#main_content_plate").css({
			"width": width,
			"height": (height-40)+"px",
			"margin":"0px"
		});
       IAClient.curFrameResize.set("resize",{'callback':IAFace.OnResize});
	},
	
	OnResize:function(width,height){
		$("html").css({
			"width": width,
			"height": height,
			"margin":"0px"
		});
		$("body").css({
			"width": width,
			"height": height,
			"margin":"0px"
		});
		$(".main").css({
			"width": width,
			"height": height,
			"margin":"0px"
		});
		$("#main_head").css({
			"width": width,
			"height": "40px",
			"margin":"0px",
			"background":"#256688"
		});
		$("#main_content_face").css({
			"width": width,
			"height": (height-40)+"px",
			"margin":"0px"
		});
		$("#main_content_plate").css({
			"width": width,
			"height": (height-40)+"px",
			"margin":"0px"
		});
		$("#FaceManage_datagrid_box").css({
			"width": width,
			"height":(height-110)+"px"
		});
	},
	
	MainControl:function(){
		$("#Face_Management").off().on('click',function(){
			$("#main_content_face").css("display","block");
			$("#main_content_plate").css("display","none");
			if($("#main_content_face").html()!="")
			{
				$(".Face_Management").css("background","#2176A6");
				$(".License_Plate_Panagement").css("background","#256688");
			}
			else{
				$(".Face_Management").css("background","#2176A6");
				$(".License_Plate_Panagement").css("background","#256688");
				IAFrame.Main.CreateFaceFrame();
			}
		});
		$("#License_Plate_Panagement").off().on('click',function(){
			$("#main_content_face").css("display","none");
			$("#main_content_plate").css("display","block");
			if($('#main_content_plate').html()!="")
			{
				$(".License_Plate_Panagement").css("background","#2176A6");
				$(".Face_Management").css("background","#256688");
			}
			else{
				$(".License_Plate_Panagement").css("background","#2176A6");
				$(".Face_Management").css("background","#256688");
				IAFrame.Main.CreateLicensePateFrame();
			}
		});
		
		$(".main-images-box").off().on('click',function(){
			$(".main-images-box").css({
				"display":"none"
			});
		});
		
		$(document).keyup(function(event){
			switch(event.keyCode) {
				 case 27:
					 $(".main-images-box").css({
						"display":"none"
					 });
					 break;
				 case 96:
					 $(".main-images-box").css({
						"display":"none"
				     });
				     break;
			}
		});
	},
	
	MainControlEasyui:function(){
		$('#main_content_tabs').tabs({
		    border:false,
		    fit:true,
		    onSelect:function(title){
		    	if(title==IALanguage.Face.FaceManagement){
		    		var arr=$("#FaceManage_combo_algo_combox").combobox("getValues");
		    		__ifa.FaceLayoutLoadData(arr[0]);
		    		var index=$("#FaceManage_combo_time_combox").combobox('getValue');
		    		IAClient.QueryFaceInfo(index);
		    	}
		    	if(title==IALanguage.Face.DataAnalysis){
		    		IAFrame.Main.CreateDataAnalysisFrame();
		    	}
		    }
		});
		$('#main_content_tabs').tabs('add',{
		    title:IALanguage.Face.FaceSetManagement,
		    content:'<div class="main_content_tabs_all" id="main_content_tabs_FaceSetManage"></div>',
		    selected: true,
		    iconCls:'icon-one',
		    closable:false
		});
		IAFrame.Main.CreateFaceSetManageFrame();
		
		$('#main_content_tabs').tabs('add',{
		    title:IALanguage.Face.FaceManagement,
		    content:'<div class="main_content_tabs_all" id="main_content_tabs_FaceManage"></div>',
		    selected: false,
		    iconCls:'icon-two',
		    closable:false
		});
		IAFrame.Main.CreateFaceManageFrame();
		
		$('#main_content_tabs').tabs('add',{
		    title:IALanguage.Face.DataAnalysis,
		    content:'<div class="main_content_tabs_all" id="main_content_tabs_DataAnalysis"></div>',
		    selected: false,
		    iconCls:'icon-three',
		    closable:false
		});
		IAFrame.Main.CreateDataAnalysisFrame();
		
		$('#main_content_tabs').tabs('add',{
		    title:IALanguage.Face.OrganizationManagement,
		    content:'<div class="main_content_tabs_all" id="main_content_tabs_OrganizeManage"></div>',
		    selected: false,
		    iconCls:'icon-four',
		    closable:false
		});
		IAFrame.Main.CreateOrganizeManageFrame();
		
	},
	
	MainControlEasyuiLayout:function(){
		$('#FaceSetManage_layout').layout({
			fit:true,
		});
		
		$('#FaceSetManage_layout').layout('add',{
		    region: 'west',
		    width: 250,
		    content:'<div id="FaceSetManage_layout_Tree"></div>'
		});
		__ifa.MainControlEasyuiTree();
		
		
		$('#FaceSetManage_layout').layout('add',{
		    region: 'center',
		    border:false,
		    content:'<div class="PuBuLiu_Image" id="PuBuLiu_Image"></div>'
		});		
	},
	
	MainControlEasyuiTree:function(){
		if($("#FaceSetManage_layout_Tree")[0]){
			$('#FaceSetManage_layout_Tree').tree({
				lines:true,
			    data:[{
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
				}],
				onBeforeLoad: function(node, param){
					var nodes = $('#FaceSetManage_layout_Tree').tree('options').data[0].children;
					for(var i=0;i<nodes.length;i++){
						IAClient.QueryIAServerInfo(i,$('#FaceSetManage_layout_Tree').tree('find',i));
					}
				},
				onContextMenu: function(e, node){
					e.preventDefault();
					if(node.type=="library"||node.type=="ALL"){
					    $('#FaceSetManage_layout_Tree').tree('select', node.target);
						$('#FaceSetManage_layout_Tree_mm').menu('show', {
							left: e.pageX,
							top: e.pageY
						});
					}
//					else if(node.type=='Algorithm '){
//						$('#FaceSetManage_layout_Tree').tree('select', node.target);
//						$('#FaceSetManage_layout_Tree_mm_child').menu('show', {
//							left: e.pageX,
//							top: e.pageY
//						});
//					}
				},
				onClick:function(node){
				    if(node.child){
				    	IAClient.QueryFaceInfo(node.index);
				    }
				}
			});
			$('#FaceSetManage_layout_Tree_mm').menu();
			$('#FaceSetManage_layout_Tree_mm').menu('appendItem', {
				text: IALanguage.Face.Add,
				iconCls: 'icon-add',
				onclick: function(){
					var node=$('#FaceSetManage_layout_Tree').tree('getSelected');
					if(node.type=="ALL"){
						IAFrame.Main.CreateAddBox();
					}else if(node.type=="library"){
					    IAFrame.Main.AddlibsData(node.iaType,node.ip,node.port);
					}
				}
			});
//			$('#FaceSetManage_layout_Tree_mm_child').menu();
//			$('#FaceSetManage_layout_Tree_mm_child').menu('appendItem', {
//				text: IALanguage.Face.Delete,
//				iconCls: 'icon-remove',
//				onclick: function(){
//					
//				}
//			});
		}
	},
	
	ReLoadImgImage:function(item){	
		$("#PuBuLiu_Image").empty();
		var html = "";
		for(var i=0;i<item.length;i++)
		{
			html += "<div class='image-boxre' attribute='"+item[i]+"'>";
			html += "    <img class='img-box' src ='"+item[i].user_sourcePicturePath_ls+"' lowsrc='"+item[i].user_sourcePicturePath+"'/>";
			html += "    <div class='img-text'>"+item[i].user_name+"</div>";
			html += "</div>";
		}
		$("#PuBuLiu_Image").html(html);
		$(".image-boxre").off().on('click',function(){
			var __self;
			$(".main-images-box").empty();
			var img=new Image();
			img.src = $(this).children()[0].lowsrc;
			var w=0;
			var h=0;
			var width=$(window).width();
		    var height=$(window).height();
			img.onload=function(){
				__self=this;
				var realWidth = __self.width;
				var realHeight = __self.height;
				w=realWidth;
				h=realHeight;
				if(realWidth>width){
					w=width;
					h=(width/realWidth)*height;
				}
				if(realHeight>height){
					h=height;
					w=(height/realHeight)*width;
				}
				$(".main-images-box").css({
					"display":"block",
					"background":"#000000 url("+img.src+") no-repeat center center",
					"background-size":w+"px "+h+"px"
				});
	     	};
		});
	},
	
	ReLoad_Detection_Image:function(item){	
		$("#DataAnalysis_image_Detection").empty();
		var html = "";
		for(var i=0;i<item.length;i++)
		{
			html += "<div class='image-boxda' id='"+i+"'>";
			html += "    <img class='img-box' src ='"+item[i].PicturePath_sl+"' lowsrc='"+item[i].PicturePath+"'/>";
			html += "    <div class='img-text'>"+item[i].Time+"</div>";
			html += "</div>";
		}
		$("#DataAnalysis_image_Detection").html(html);
		$(".image-boxda").off().on('click',function(){
			$(".main-images-box").empty();
			var __self;
			var imageID = this.id;
			
            var img=new Image();
			img.src = $(this).children()[0].lowsrc;
			img.onload = function(){
				__self= this;
				var width=$(window).width();
		        var height=$(window).height();
				var realWidth = this.width;
				var realHeight = this.height;
				var w=((width-realWidth)/2)+parseInt(item[imageID].Facerect_x);
				var h=((height-realHeight)/2)+parseInt(item[imageID].Facerect_y);
				
				var html = "";
				html +="<div class='check-box' onClick='event.cancelBubble = true' style='width:115px;height:30px;color:#ffffff;float:right;'>";
				html +=  "<div style='float:left;margin-top:6px;'><input style='width:16px;height:16px;' class='check' checked=checked type='checkbox'/></div><div style='float:left;margin-top:5px;'>是否框人脸</div>";
				html +="</div>";
				html +="<div class='main-images-box-img'></div>";
				
				$(".main-images-box").html(html);
				
				$(".check-box").off().on('click',function(){
					 if($(".check").prop('checked')){
					 	$(".check").removeAttr("checked","checked");
					    $(".main-images-box-img").css('display','none');
					 }else{
					 	$(".check").prop("checked","checked");
					    $(".main-images-box-img").css('display','block');
					 }
				});
				
				$(".main-images-box-img").css({
					"width":item[imageID].Facerect_w+"px",
					"height":item[imageID].Facerect_h+"px",
					"border":"solid 1px red",
					"margin-left":w+"px",
					"margin-top":h+"px"
				});
			};
			jQuery(window).resize(function(){
				var width=$(window).width();
		        var height=$(window).height();
				var realWidth = __self.width;
				var realHeight = __self.height;
				var w=((width-realWidth)/2)+parseInt(item[imageID].Facerect_x);
				var h=((height-realHeight)/2)+parseInt(item[imageID].Facerect_y);

				$(".main-images-box-img").css({
					"width":item[imageID].Facerect_w+"px",
					"height":item[imageID].Facerect_h+"px",
					"border":"solid 1px red",
					"margin-left":w+"px",
					"margin-top":h+"px"
				});
			});
			$(".main-images-box").css({
				"display":"block",
				"background":"#000000 url("+$(this).children()[0].lowsrc+") no-repeat center center"
			});
		});
	},
	ReLoad_Identification_Image:function(item){	
		$("#DataAnalysis_image_Identification").empty();
		var html = "";
		for(var i=0;i<item.length;i++)
		{
			html += "<div class='image-boxad' id='"+i+"'>";
			html += "    <img class='img-box' src ='"+item[i].PicturePath_sl+"' lowsrc='"+item[i].PicturePath+"'/>";
			html += "    <div style='width:120px;height:60px;'><div style='margin:5px 15px;'>姓名:"+(item[i].username || "-")+"</div><div style='margin:5px 15px;'>编号:"+(item[i].Number||"-")+"</div></div>";
			html += "</div>";
		}
		$("#DataAnalysis_image_Identification").html(html);
		$(".image-boxad").off().on('click',function(){
			$(".main-images-box").empty();
			var __self = this;
			var imageID = this.id;
			
            var img=new Image();
			img.src = $(this).children()[0].lowsrc;
			img.onload = function(){
				var width=$(window).width();
		        var height=$(window).height();
		        var wid=width/2;
		        var hei=(height/8)*7;
				
				var html="";
				html +="<div style='width:"+width+"px;height:"+hei+"px;'>";
				html +="   <div class='pic-source' style='width:"+wid+"px;height:"+hei+"px;float:left;'></div>";
				html +="   <div class='pic-path' style='width:"+wid+"px;height:"+hei+"px;float:left;'></div>";
				html +="</div>";
				
				html +="<table>";
				html +="  <tr>";
				html +="    <td class='base-info'>姓名 : "+item[imageID].username+"</td>";
				html +="    <td class='base-info'>身份证 : "+item[imageID].IDNumber+"</td>";
				html +="    <td class='base-info'>编号 : "+item[imageID].Number+"</td>";
				html +="    <td class='base-info'>置信度 : "+(item[imageID].FaceScore*100)+"%</td>";
				html +="  </tr>";
				html +="  <tr>";
				html +="    <td class='base-info'>设备ID : "+item[imageID].PUID+"</td>";
				html +="    <td class='base-info'>设备索引 : "+item[imageID].Idx+"</td>";
				html +="    <td class='base-info'>时间 : "+item[imageID].Time+"</td>";
				html +="    <td></td>";
				html +="  </tr>";
				html +="</table>";
				
				
				$(".main-images-box").html(html);
				
				$(".pic-source").css({
					"display":"block",
					"background":"#000000 url("+item[imageID].sourcePicturePath+") no-repeat center center",
					"background-size":"calc(90%) calc(90%)"
				});
				$(".pic-path").css({
					"display":"block",
					"background":"#000000 url("+item[imageID].PicturePath+") no-repeat center center",
					"background-size":"calc(90%) calc(90%)"
				});
				
				$(".base-info").css({
					"width":(width/5)+"px",
					"float":"left",
					"color":"#ffffff",
					"text-overflow":"ellipsis"
				});
			};
			jQuery(window).resize(function(){
				var width=$(window).width();
		        var height=$(window).height();
		        var wid=width/2;
		        var hei=(height/8)*7;
				
				var html="";
				html +="<div style='width:"+width+"px;height:"+hei+"px;'>";
				html +="   <div class='pic-source' style='width:"+wid+"px;height:"+hei+"px;float:left;'></div>";
				html +="   <div class='pic-path' style='width:"+wid+"px;height:"+hei+"px;float:left;'></div>";
				html +="</div>";
				
				html +="<table style='width:"+width+"px;height:"+(height/8)+"px;overflow:hidden;'>";
				html +="  <tr>";
				html +="    <td class='base-info'>"+IALanguage.Face.username+" : "+item[imageID].username+"</td>";
				html +="    <td class='base-info'>"+IALanguage.Face.IDNumber+" : "+item[imageID].IDNumber+"</td>";
				html +="    <td class='base-info'>"+IALanguage.Face.Number+" : "+item[imageID].Number+"</td>";
				html +="    <td class='base-info'>"+IALanguage.Face.FaceScore+": "+(item[imageID].FaceScore*100)+"%</td>";
				html +="  </tr>";
				html +="  <tr>";
				html +="    <td class='base-info'>"+IALanguage.Face.PUID+" : "+item[imageID].PUID+"</td>";
				html +="    <td class='base-info'>"+IALanguage.Face.Idx+" : "+item[imageID].Idx+"</td>";
				html +="    <td class='base-info'>"+IALanguage.Face.Time+": "+item[imageID].Time+"</td>";
				html +="    <td></td>";
				html +="  </tr>";
				html +="</table>";
				
				
				$(".main-images-box").html(html);
				
				$(".pic-source").css({
					"display":"block",
					"background":"#000000 url("+item[imageID].sourcePicturePath+") no-repeat center center",
					"background-size":"calc(90%) calc(90%)"
				});
				$(".pic-path").css({
					"display":"block",
					"background":"#000000 url("+item[imageID].PicturePath+") no-repeat center center",
					"background-size":"calc(90%) calc(90%)"
				});
				
				$(".base-info").css({
					"width":(width/5)+"px",
					"float":"left",
					"color":"#ffffff",
					"text-overflow":"ellipsis"
				});
			});
			$(".main-images-box").css({
				"display":"block",
				"background":"#000000"
			});
		});
	},
	
	ReLoad_Plate_Image:function(item){
		$("#PlateDataAnalysis_image_box").empty();
		var html = "";
		for(var i=0;i<item.length;i++)
		{
			html += "<div class='image-boxpl'>";
			html += "    <img class='img-box' src ='"+item[i].PicturePath_sl+"' lowsrc='"+item[i].PicturePath+"'/>";
			html += "    <div class='img-text'>"+item[i].PlateNo+"</div>";
			html += "</div>";
		}
		$("#PlateDataAnalysis_image_box").html(html);
		$(".image-boxpl").off().on('click',function(){
			$(".main-images-box").empty();
			var __self;
			var imageID = this.id;
			
            var img=new Image();
			img.src = $(this).children()[0].lowsrc;
			img.onload = function(){
				__self = this;
				var width=$(window).width();
		        var height=$(window).height();
				var realWidth = __self.width;
				var realHeight = __self.height;
				var w=((width-realWidth)/2)+parseInt(item[imageID].Facerect_x);
				var h=((height-realHeight)/2)+parseInt(item[imageID].Facerect_y);
				var html = "";
				html +="<div class='check-box' onClick='event.cancelBubble = true' style='width:115px;height:30px;color:#ffffff;float:right;'>";
				html +=  "<div style='float:left;margin-top:6px;'><input style='width:16px;height:16px;' class='check' checked=checked type='checkbox'/></div><div style='float:left;margin-top:5px;'>是否框人脸</div>";
				html +="</div>";
				html +="<div class='main-images-box-img'></div>";
				
				$(".main-images-box").html(html);
				
				$(".check-box").off().on('click',function(){
					 if($(".check").prop('checked')){
					 	$(".check").removeAttr("checked","checked");
					    $(".main-images-box-img").css('display','none');
					 }else{
					 	$(".check").prop("checked","checked");
					    $(".main-images-box-img").css('display','block');
					 }
				});
				
				$(".main-images-box").html(html);
				$(".main-images-box-img").css({
					"width":item[imageID].Facerect_w+"px",
					"height":item[imageID].Facerect_h+"px",
					"border":"solid 1px red",
					"margin-left":w+"px",
					"margin-top":h+"px"
				});
				if($(".check").attr('checked')=="checked"){
					$(".main-images-box-img").css('display','block');
				}else{
					$(".main-images-box-img").css('display','none');
				}
			};
			jQuery(window).resize(function(){	
				var width=$(window).width();
		        var height=$(window).height();
				var realWidth = __self.width;
				var realHeight = __self.height;
				var w=((width-realWidth)/2)+parseInt(item[imageID].Facerect_x);
				var h=((height-realHeight)/2)+parseInt(item[imageID].Facerect_y);

				$(".main-images-box-img").css({
					"width":item[imageID].Facerect_w+"px",
					"height":item[imageID].Facerect_h+"px",
					"border":"solid 1px red",
					"margin-left":w+"px",
					"margin-top":h+"px"
				});
			});
			$(".main-images-box").css({
				"display":"block",
				"background":"#000000 url("+$(this).children()[0].lowsrc+") no-repeat center center"
			});
		});
	},
	
	MainControlEasyuiDatagrid:function(){
		$("#FaceManage_combo_algo_combox").combobox({
			width:200,
			valueField:'id',
            textField:'text',
            editable:false,
            data:[{
			    "id":0,
			    "text":IAMConfig.iaServer.SHT,
			    "selected":true
			},{
			    "id":1,
			    "text":IAMConfig.iaServer.TL
			},{
			    "id":2,
			    "text":IAMConfig.iaServer.AS
			}],
			onSelect: function(rec){
				__ifa.FaceLayoutLoadData(rec.id);
			}
		});
		$("#FaceManage_combo_time_combox").combobox({
			width:185,
			valueField:'id',
            textField:'text',
            editable:false,
            onSelect: function(node){
				setTimeout(function(){
					IAClient.QueryFaceInfo(node.index);
				},500);
			}
		});
		
		$("#FaceManage_datagrid").datagrid({
			singleSelect:true,
			rownumbers:true,
			fit:true,
			frozenColumns:[[
					{ field : 'user_name', title:IALanguage.Main.datagrid.name, width : 100, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_idNumber', title:IALanguage.Main.datagrid.idNumber, width : 200, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_number', title:IALanguage.Main.datagrid.Number, width : 100, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_sex', title:IALanguage.Main.datagrid.sex, width : 50, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_function', title:IALanguage.Main.datagrid.post, width : 100, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_projectDepartment', title:IALanguage.Main.datagrid.Project_Department, width : 100, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}}
			]],
			columns :[[
			{ field : 'user_dept', title:IALanguage.Main.datagrid.Dept, width :100, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_jobNumber', title:IALanguage.Main.datagrid.JobNumber, width : 100, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_subcontractTeam', title:IALanguage.Main.datagrid.SubcontractTeam, width : 100, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
			        { field : 'user_company', title:IALanguage.Main.datagrid.Company, width : 150, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
			        { field : 'user_birthDate', title:IALanguage.Main.datagrid.BirthDate, width : 200, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_examResult', title:IALanguage.Main.datagrid.Exam_results, width : 100, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
			        { field : 'user_auditBeginTime', title:IALanguage.Main.datagrid.Audit_start, width : 200, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
			        { field : 'user_auditEndTime', title:IALanguage.Main.datagrid.Audit_end, width : 200, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_auditor', title:IALanguage.Main.datagrid.Auditor, width : 100, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_auditTime', title:IALanguage.Main.datagrid.AuditTime, width : 200, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},
					{ field : 'user_auditStatus', title:IALanguage.Main.datagrid.Audit_status, width : 250, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}},					
					{ field : 'user_IllegalInformation', title:IALanguage.Main.datagrid.illegalInformation, width : 300, align:'center', formatter : function(value, rowData,rowIndex){return '<span title="'+value+'">'+value+'</span>';}}
			]],
			onDblClickRow:function(rowIndex, rowData){
				IAFrame.Main.CreateWinBoxInfo(rowData);
			},
			toolbar:[{
				id:'tianjia',
				iconCls:'icons-useradd',
				disabled:true,
				text:IALanguage.Face.Add,
				handler:function(){
					IAFrame.Main.SelectWindow(IALanguage.Face.Add);
				}
			},{
				iconCls:'icons-useredit',
				text:IALanguage.Face.Modify,
				handler:function(){
					IAFrame.Main.SelectWindow(IALanguage.Face.Modify);
				}
			},{
				iconCls:'icons-userdelete',
				text:IALanguage.Face.Delete,
				handler:function(){
					var datagrid = $("#FaceManage_datagrid").datagrid('getSelected');
					if(datagrid){
						$.messager.confirm(IALanguage.Face.warning, IALanguage.Face.confirm, function(r){
							if (r){
								load();
						        IAClient.DeleteFaceBaseInfo(datagrid.user_idNumber);
							}
						});
					}else{
						$.messager.alert(IALanguage.Main.info,IALanguage.Main.please_select,"info",function(){});
					}
				}
			},{
				iconCls:'icon-reload',
				text:IALanguage.Face.refresh,
				handler:function(){
					var index=$("#FaceManage_combo_time_combox").combobox('getValue');
		            IAClient.QueryFaceInfo(index);
				}
			}]
		});
		
	},

	
	
	MainControlEasyuiDataAnalysis_tabs:function(){
		$('#DataAnalysis_tabs').tabs({
		    border:false,
		    fit:true,
		    tabPosition:'bottom',
		    onSelect:function(title){
		    	
		    }
		});
		$('#DataAnalysis_tabs').tabs('add',{
		    title:IALanguage.Face.DetectionRecord,
		    content:'<div class="main_content_tabs_all" id="Detection_record_table"></div>',
		    selected: true,
		    closable:false
		});
		IAFrame.Main.DataAnalysis_Detection();
		
		$('#DataAnalysis_tabs').tabs('add',{
		    title:IALanguage.Face.IdentificationRecord,
		    content:'<div class="main_content_tabs_all" id="Identification_record_table"></div>',
		    selected: false,
		    closable:false
		});
		IAFrame.Main.DataAnalysis_Identification();
	},
	
	
	
	MainControlEasyuiDataAnalysis_Identification:function(){
		$("#DataAnalysis_combo_begin_time_combox_Identification").datetimebox({
			width:140,
		    required: true,
		    editable: false,
		    onHidePanel: function(date){
                validateDateTime('DataAnalysis_combo_begin_time_combox_Identification','DataAnalysis_combo_end_time_combox_Identification','DataAnalysis_combo_begin_time_combox_Identification',"",true);
            }
		});
		$("#DataAnalysis_combo_end_time_combox_Identification").datetimebox({
			width:140,
			editable: false,
		    required: true,
		    onHidePanel: function(date){
                validateDateTime('DataAnalysis_combo_begin_time_combox_Identification','DataAnalysis_combo_end_time_combox_Identification','DataAnalysis_combo_end_time_combox_Identification',"",true);
            }
		});
		
		$("#DataAnalysis_combo_search_combox_Identification").searchbox({
			width:140,
		    searcher:function(value,name){
		    	validateDateTime('DataAnalysis_combo_begin_time_combox_Detection','DataAnalysis_combo_end_time_combox_Detection','DataAnalysis_combo_begin_time_combox_Detection',value,true);
		    },
		    prompt:IALanguage.Face.Prompt
		});
		
		
	},
	MainControlEasyuiDataAnalysis_Detection:function(){
		$("#DataAnalysis_combo_begin_time_combox_Detection").datetimebox({
			width:140,
		    editable: false,
		    required: true,
		    onHidePanel: function(date){
                validateDateTime('DataAnalysis_combo_begin_time_combox_Detection','DataAnalysis_combo_end_time_combox_Detection','DataAnalysis_combo_begin_time_combox_Detection',"",false);
            }
		});
		
		$("#DataAnalysis_combo_end_time_combox_Detection").datetimebox({
			width:140,
		    editable: false,
		    required: true,
		    onHidePanel: function(date){
                validateDateTime('DataAnalysis_combo_begin_time_combox_Detection','DataAnalysis_combo_end_time_combox_Detection','DataAnalysis_combo_end_time_combox_Detection',"",false);
            }
		});
		
		$("#DataAnalysis_combo_search_combox_Detection").searchbox({
			width:135,
		    searcher:function(value,name){
		        validateDateTime('DataAnalysis_combo_begin_time_combox_Detection','DataAnalysis_combo_end_time_combox_Detection','DataAnalysis_combo_end_time_combox_Detection',value,false);	
		    },
		    prompt:IALanguage.Face.Prompt
		});
	},
	
	
	
	
	MainControlOrganizeLayout:function(){
		$('#OrganizeManage_layout').layout({
			fit:true,
		});
		
		$('#OrganizeManage_layout').layout('add',{
		    region: 'west',
		    width: 250,
		    content:'<div id="OrganizeManage_layout_Tree"></div><div id="OrganizeManage_layout_Tree_mm" style="width:80px;"></div><div id="OrganizeManage_layout_Tree_mm_group" style="width:80px;"></div>'
	    });
		
		
		$('#OrganizeManage_layout').layout('add',{
		    region: 'center',
		    border:false,
		    content:'<div class="OrganizeManage_datagrid" id="OrganizeManage_datagrid"></div>'
		});
		__ifa.MainControlOrganize();
	},
	
	MainControlOrganize:function(){
		if($("#OrganizeManage_layout_Tree")[0]){
			$('#OrganizeManage_layout_Tree').tree({
				lines:true,
				onClick:function(node){
					if(node.text!=IALanguage.Face.OrganizationCategory){
						IAClient.QueryGroupResourceInfo(node.id,0);
					}
				},
				onContextMenu: function(e, node){
					if(node.text==IALanguage.Face.OrganizationCategory)
					{
						e.preventDefault();
					    $('#OrganizeManage_layout_Tree').tree('select', node.target);
						$('#OrganizeManage_layout_Tree_mm_group').menu('show', {
							left: e.pageX,
							top: e.pageY
						});
					}else{
						e.preventDefault();
					    $('#OrganizeManage_layout_Tree').tree('select', node.target);
						$('#OrganizeManage_layout_Tree_mm').menu('show', {
							left: e.pageX,
							top: e.pageY
						});
					}
				}
			});
			IAClient.QueryGroupInfo(0);
			
			$('#OrganizeManage_layout_Tree_mm_group').menu();
			$('#OrganizeManage_layout_Tree_mm_group').menu('appendItem', {
				text: IALanguage.Face.Add,
				iconCls: 'icon-add',
				onclick: function(){
					var node=$('#OrganizeManage_layout_Tree').tree('getSelected');
					IAFrame.Main.AddMainControlOrganize(0,0);
				}
			});
			
			$('#OrganizeManage_layout_Tree_mm').menu();
			$('#OrganizeManage_layout_Tree_mm').menu('appendItem', {
				text: IALanguage.Face.Modify,
				iconCls: 'icon-edit',
				onclick: function(){
					var node=$('#OrganizeManage_layout_Tree').tree('getSelected');
					IAFrame.Main.ModifyMainControlOrganize(node.text,0,node.id);
				}
			});
			$('#OrganizeManage_layout_Tree_mm').menu('appendItem', {
				text: IALanguage.Face.Delete,
				iconCls: 'icon-remove',
				onclick: function(){
					$.messager.confirm(IALanguage.Face.warning,IALanguage.Face.confirm, function(r){
						if (r){
							var node=$('#OrganizeManage_layout_Tree').tree('getSelected');
					        IAClient.DeleteGroupInfo(0,node.id);
						}
					});
				}
			});
			
			
			
			
			
			$("#OrganizeManage_datagrid").datagrid({
				fit:true,
				fitColumns:true,
				singleSelect:true,
			    columns:[[
					{field:'resName',title:IALanguage.Face.username,width:100,align:'center'}
			    ]],
			    toolbar: [{
					iconCls: 'icon-add',
					text:IALanguage.Face.AddResource,
					handler: function(){
						var node=$('#OrganizeManage_layout_Tree').tree('getSelected');
						if(node&&node.text!=IALanguage.Face.OrganizationCategory)
						{
						     IAFrame.Main.AddGroupResourceInfo(IALanguage.Main.face);	
						}else{
							$.messager.alert(IALanguage.Main.info,IALanguage.Face.Tips,"info",function(){});
						}
					}
				},'-',{
					iconCls: 'icon-remove',
					text:IALanguage.Face.DeleteResource,
					handler: function(){
						var node=$('#OrganizeManage_layout_Tree').tree('getSelected');
						var datagrid = $("#OrganizeManage_datagrid").datagrid('getSelected');
				        if(datagrid){
				        	$.messager.confirm(IALanguage.Face.warning,IALanguage.Face.confirm, function(r){
							if (r){
							        IAClient.DeleteGroupResourceInfo(node.id,0,datagrid.index);
								}
							});
				        }else{
				        	$.messager.alert(IALanguage.Main.info,IALanguage.Main.please_select,"info",function(){});
				        }
					}
				}]
			});
		}
	},
	
	
	GetObjectURL:function(file) {
		var url = null;
		if (window.createObjectURL!=undefined) { 
			url = window.createObjectURL(file);
		} else if (window.URL!=undefined) {
			url = window.URL.createObjectURL(file);
		} else if (window.webkitURL!=undefined) {
			url = window.webkitURL.createObjectURL(file);
		}
		return url;
	},
	FaceLayoutLoadData:function(id){
		setTimeout(function(){
			var data=new Array();
			var node=$('#FaceSetManage_layout_Tree').tree('find',id);
			if(node.children){
				$("#tianjia").linkbutton('enable');
				for(var i=0;i<node.children[0].children.length;i++){
					if(i==0){
						data.push({
							"id":node.children[0].children[i].index,
							"index":node.children[0].children[i].index,
							"text":node.children[0].children[i].text,
							"selected":true
						});
					}else{
						data.push({
							"id":node.children[0].children[i].index,
							"index":node.children[0].children[i].index,
							"text":node.children[0].children[i].text,
						});
					}
				}
				$("#FaceManage_combo_time_combox").combobox('loadData',data);
			}else{
				$("#FaceManage_combo_time_combox").combobox('loadData',data);
				$("#FaceManage_combo_time_combox").combobox('setValue',"");
				$("#tianjia").linkbutton('disable');
				return;
			}
		},100);
	},
	
	end:true
}
function validateDateTime(beginTimeId,endTimeId,whichTimeId,qkey,flag)
{
    var v1=$('#'+beginTimeId).datetimebox("getValue");
    var date1 = new Date(v1);
    var v2=$('#'+endTimeId).datetimebox("getValue");
    var date2 = new Date(v2);
    
    if(v1==''||v2=='')
    {
        return true;
    }    
    if(date1<date2)
    {
    	if(flag){
    		load();
    		IAClient.QueryFaceRecognitionHistroyInfo(v1,v2,qkey);
    	}else{
    		load();
    		IAClient.QueryFaceDetectHistoryInfo(v1,v2,qkey);
    	}
        return true;
    }
    try{
        $('#'+whichTimeId).datetimebox("setValue","");
       }catch(e){
    }
    $.messager.alert(IALanguage.Main.info,IALanguage.Main.CompareTime);
    return false;       
}