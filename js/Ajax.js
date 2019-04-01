var Ajax={
	SendAjax:function(action,params,callback){
		var fn="Ajax.SendAjax";
		try{
			if(typeof action=='undefined'||action==""||action==null)
			{
				return false;
			}
			var data = {
				request: {
					action:action,
					content:params
				}
			}
			var datas = $.toJSON(data);
			$.ajax({
				type:"POST",
				url:"php/index.php",
				dataType:"json",
				data:encodeURIComponent(datas),
				success:function(rv){
					if($.isFunction(callback))
					{
						callback(rv);
					}
				},
				error:function(error){
					console.log(IALanguage.Ajax.failure+error.message);
				}
			});
		}catch(e){
			console.log(fn+":发生异常，"+e.name+","+e.message)
		}
	},

	Construct:function(errorCode){
		switch(errorCode){
			case "0x0001" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.failure,"error",function(){});
				break;
			case "0x0002" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.exception,"error",function(){});
				break;
			case "0x0010" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.logged,"error",function(){});
				break;
			case "0x0011" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.notlogged,function(){});
				break;
			case "0x0020" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.action_error,"error",function(){});
				break;
			case "0x0021" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.action_noKnown,"error",function(){});
				break;
			case "0x0022" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.action_noExists,"error",function(){});
				break;
			case "0x0030" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.content_noExists,"error",function(){});
				break;
			case "0x0031" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.content_noExists,"error",function(){});
				break;
			case "0x0032" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.content_noExists,"error",function(){});
				break;
			case "0x0033" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.offset_max,"error",function(){});
				break;
			case "0x0040" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.content_wrongful,"error",function(){});
				break;
			case "0x0050" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.mysql_object,"error",function(){});
				break;
			case "0x0051" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.mysql_operation,"error",function(){});
				break;
			case "0x0060" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.WebService_request_error,"error",function(){});
				break;
			case "0x0061" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.WebService_password_error,"error",function(){});
				break;
			case "0x0062" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.WebService_response_error,"error",function(){});
				break;
			case "0x0063" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.WebService_content_error,"error",function(){});
				break;
			case "0x0064" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.WebService_object_error,"error",function(){});
				break;
			case "0x1000" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.login_failure,"error",function(){});
				break;
			case "0x1001" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.login_overtime,"error",function(){});
				break;
			case "0x1010" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.login_Username_format_error,"error",function(){});
				break;
			case "0x1011" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.login_Username_noExists,"error",function(){});
				break;
			case "0x1012" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.login_password_error,"error",function(){});
				break;
			case "0x1013" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.login_Username_content_wrongful,"error",function(){});
				break;
			case "0x1014" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.login_max_offset,"error",function(){});
				break;
			case "0x1020" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.session_faulure,"error",function(){});
				break;				
			case "0x1021" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.session_lose,"error",function(){});
				break;
			case "0x1022" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.session_wrongful,"error",function(){});
				break;
			case "0x1040" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.PLAT_NOT_LOGIN,"error",function(){});
				break;
			case "0x1041" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.PLAT_ALREADY_LOGIN,"error",function(){});
				break;
			case "0x1050" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.UPLOAD_ERROR,"error",function(){});
				break;
			case "0x1051" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.UPLOAD_PHOTO_EXT_ERROR,"error",function(){});
				break;
			case "0x2000" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.xml_send_error,"error",function(){});
				break;
			case "0x2001" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.xml_analysis_error,"error",function(){});
				break;
			case "0x2002" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.xml_return_error,"error",function(){});
				break;
			case "0x2003" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.xml_send_OPTID,"error",function(){});
				break;
			case "0x2004" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.XML_REQUEST_NU_FORMAT_ERROR,"error",function(){});
				break;
			case "0x2005" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.XML_RESPONSE_NU_ERROR,"error",function(){});
				break;
			case "0x2006" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.XML_RESPONSE_NU_TIMEOUT,"error",function(){});
				break;
			case "0x2010" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.REMOTE_SERVER_REQUEST_ERROR,"error",function(){});
				break;
			case "0x3000" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.Equipment_not_online,"error",function(){});
				break;
			case "0x3010" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.Request_flow_IPTOKEN_failed,"error",function(){});
				break;
			case "0x3020" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.Request_real_time_stream_RTMP_failed,"error",function(){});
				break;
			case "0x3021" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.Play_RTMP_stream_failed,"error",function(){});
				break;
			case "0x3022" :
				$.messager.alert(IALanguage.Ajax.info,IALanguage.Ajax.message.Stop_RTMP_stream_failed,"error",function(){});
				break;
		}
	}
}