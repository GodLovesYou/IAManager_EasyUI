var IACar = __ica = {
	
	MainControlEasyuiPlate:function(){
		$('#main_plate_tabs').tabs({
		    border:false,
		    fit:true,
		    onSelect:function(title){
		    	
		    }
		});
		
		$('#main_plate_tabs').tabs('add',{
		    title:IALanguage.Car.LicensePlateManagement,
		    content:'<div class="main_plate_tabs_all" id="main_plate_tabs_PlateManage"></div>',
		    selected: true,
		    iconCls:'icon-five',
		    closable:false
		});
		IAFrame.Main.CreatePlateManageFrame();
		
		$('#main_plate_tabs').tabs('add',{
		    title:IALanguage.Car.LicensePlateAnalysis,
		    content:'<div class="main_plate_tabs_all" id="main_plate_tabs_PlateAnalysis"></div>',
		    selected: false,
		    iconCls:'icon-six',
		    closable:false
		});
		IAFrame.Main.CreatePlateAnalysisFrame();
		
		$('#main_plate_tabs').tabs('add',{
		    title:IALanguage.Car.OrganizationManagement,
		    content:'<div class="main_plate_tabs_all" id="main_plate_tabs_PlateOrganizeManage"></div>',
		    selected: false,
		    iconCls:'icon-seven',
		    closable:false
		});
		IAFrame.Main.CreatePlateOrganizeManageFrame();
	},
	
	
	MainControlEasyuiPlateDatagrid:function(){
		$("#PlateManage_datagrid").datagrid({
			singleSelect:true,
			fit:true,
			border:false,
			fitColumns:true,
		    columns:[[
		        {field:'PlateNo',title:IALanguage.Car.plate_number,width:100,align:'center'},
		        {field:'VehicleClass',title:IALanguage.Car.Vehicle_type,width:100,align:'center'},
		        {field:'VehicleBrand',title:IALanguage.Car.Vehicle_brand,width:100,align:'center'},
		        {field:'VehicleModel',title:IALanguage.Car.Vehicle_model,width:100,align:'center'},
				{field:'PlateColor',title:IALanguage.Car.trailer_color,width:100,align:'center'},
				{field:'PlateNoAttach',title:IALanguage.Car.trailer_number,width:100,align:'center'},
				{field:'VehicleColor',title:IALanguage.Car.plate_color,width:100,align:'center'}
		    ]],
		    toolbar: [{
				iconCls: 'icon-add',
				text:IALanguage.Car.Add,
				handler: function(){
					IAFrame.Main.AddPlateInfo();
				}
			},'-',{
				iconCls: 'icon-edit',
				text:IALanguage.Car.Modify,
				handler: function(){
					var node = $("#PlateManage_datagrid").datagrid('getSelected');
			        if(node){
			        	IAFrame.Main.ModifyPlateInfo(node.plateIndex,node.PlateColor,node.PlateNo,node.PlateNoAttach,node.VehicleClass,node.VehicleBrand,node.VehicleModel,node.VehicleColor);
			        }else{
			        	$.messager.alert(IALanguage.Main.info,IALanguage.Main.please_select,"info",function(){});
			        }
				}
			},'-',{
				iconCls: 'icon-remove',
				text:IALanguage.Car.Delete,
				handler: function(){
					var datagrid = $("#PlateManage_datagrid").datagrid('getSelected');
			        if(datagrid){
			        	$.messager.confirm(IALanguage.Car.warning,IALanguage.Car.confirm, function(r){
						if (r){
						        IAClient.DeleteCarIDInfo(datagrid.plateIndex);
							}
						});
			        }else{
			        	$.messager.alert(IALanguage.Main.info,IALanguage.Main.please_select,"info",function(){});
			        }
				}
			}]
		});
		IAClient.QueryCarIDInfo();
	},
	
	MainControlEasyuiPlateDataAnalysis:function(){
		$("#PlateDataAnalysis_combo_time_combox").datebox({
			width:90,
		    value: '3/4/2010',
		    required: true,
		    onSelect: function(){
		    	var date=$('#PlateDataAnalysis_combo_time_combox').datebox('getValue');
				IAClient.QueryPlateNumberHistroyInfo("",date);					
			}
		});
		
		$("#PlateDataAnalysis_combo_search_combox").searchbox({
			width:135,
		    searcher:function(value,name){
		        var date=$('#PlateDataAnalysis_combo_time_combox').datebox('getValue');
				IAClient.QueryPlateNumberHistroyInfo(value,date);	
		    },
		    prompt:'Please Input Value'
		});
	},
	
	MainControlPlateOrganizeLayout:function(){
		$('#PlateOrganizeManage_layout').layout({
			fit:true,
		});
		
		$('#PlateOrganizeManage_layout').layout('add',{
		    region: 'west',
		    width: 250,
		    content:'<div id="PlateOrganizeManage_layout_Tree"></div>'
		});
		
		
		$('#PlateOrganizeManage_layout').layout('add',{
		    region: 'center',
		    border:false,
		    content:'<div class="PlateOrganizeManage_datagrid" id="PlateOrganizeManage_datagrid"></div>'
		});
		__ica.MainPlateControlOrganize();
	},
	
	
	MainPlateControlOrganize:function(){
		if($("#PlateOrganizeManage_layout_Tree")[0]){
			$('#PlateOrganizeManage_layout_Tree').tree({
				lines:true,
			    onClick:function(node){
					if(node.text!=IALanguage.Car.OrganizationCategory){
						IAClient.QueryGroupResourceInfo(node.id,1);
					}
				},
				onContextMenu: function(e, node){
					if(node.text==IALanguage.Car.OrganizationCategory)
					{
						e.preventDefault();
					    $('#PlateOrganizeManage_layout_Tree').tree('select', node.target);
						$('#PlateOrganizeManage_layout_Tree_mm_group').menu('show', {
							left: e.pageX,
							top: e.pageY
						});
					}else{
						e.preventDefault();
					    $('#PlateOrganizeManage_layout_Tree').tree('select', node.target);
						$('#PlateOrganizeManage_layout_Tree_mm').menu('show', {
							left: e.pageX,
							top: e.pageY
						});
					}
				}
			});
			IAClient.QueryGroupInfo(1);
			
			$('#PlateOrganizeManage_layout_Tree_mm_group').empty();
			
			$('#PlateOrganizeManage_layout_Tree_mm_group').menu();
			$('#PlateOrganizeManage_layout_Tree_mm_group').menu('appendItem', {
				text: IALanguage.Car.Add,
				iconCls: 'icon-add',
				onclick: function(){
					var node=$('#PlateOrganizeManage_layout_Tree').tree('getSelected');
					IAFrame.Main.AddMainControlOrganize(1,0);
				}
			});
			
			
			$('#PlateOrganizeManage_layout_Tree_mm').menu();
			$('#PlateOrganizeManage_layout_Tree_mm').menu('appendItem', {
				text: IALanguage.Car.Modify,
				iconCls: 'icon-edit',
				onclick: function(){
					var node=$('#PlateOrganizeManage_layout_Tree').tree('getSelected');
					IAFrame.Main.ModifyMainControlOrganize(node.text,1,node.id);
				}
			});
			$('#PlateOrganizeManage_layout_Tree_mm').menu('appendItem', {
				text: IALanguage.Car.Delete,
				iconCls: 'icon-remove',
				onclick: function(){
					$.messager.confirm(IALanguage.Car.warning,IALanguage.Car.confirm, function(r){
						if (r){
							var node=$('#PlateOrganizeManage_layout_Tree').tree('getSelected');
					        IAClient.DeleteGroupInfo(1,node.id);
						}
					});
				}
			});
			
			
			
			
			$("#PlateOrganizeManage_datagrid").datagrid({
				singleSelect:true,
				fit:true,
				fitColumns:true,
			    columns:[[
					{field:'resName',title:IALanguage.Car.plate_number,width:100,align:'center'},
			    ]],
			    toolbar: [{
				iconCls: 'icon-add',
				text:IALanguage.Car.AddResource,
				handler: function(){
					var node=$('#PlateOrganizeManage_layout_Tree').tree('getSelected');
					if(node&&node.text!=IALanguage.Car.OrganizationCategory)
					{
					     IAFrame.Main.AddGroupResourceInfo(IALanguage.Car.plate);
					}else{
						$.messager.alert(IALanguage.Main.info,IALanguage.Car.Tips,"info",function(){});
					}
				}
			},'-',{
				iconCls: 'icon-remove',
				text:IALanguage.Car.DeleteResource,
				handler: function(){
					var node=$('#PlateOrganizeManage_layout_Tree').tree('getSelected');
					var datagrid = $("#PlateOrganizeManage_datagrid").datagrid('getSelected');
			        if(datagrid){
			        	$.messager.confirm(IALanguage.Car.warning,IALanguage.Car.confirm, function(r){
						if (r){
						        IAClient.DeleteGroupResourceInfo(node.id,1,datagrid.index);
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
	
}