<html> 
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"></meta>
	
	<style>
	.pro_label {width:130px;text-align:center;}
	.submit_div {width:300px;text-align:center;margin-top:20px;}
	
	</style>
	 
	<script type="text/javascript" src="../lib/jquery/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="../lib/jquery/lib/jquery.json-2.4.js"></script>
	
	<script type="text/javascript">

	jQuery(document).ready(function () {

	    if(true){
            data = {
                request: {
                    action: 'QueryIAServerInfo',
                    content: {
						dbIndex : '',
						IAType : 1
                    }
                }
            };
        }

		if(false){
            data = {
                request: {
                    action: 'AddIAServerInfo',
                    content: {
						iaType:'2',
						dbname:'20180313001',
						ip:'192.168.31.194',
						port:'80',
						appkey:'d47bf894-4b3c-4766-bd72-91eb1c1aaf0e',
						appsecret:'c39fe532-7cd4-4f6c-8a69-2b557ecfe202',      
						description:'',
						remark:''
                    }
                }
            };
        }

		if(false){
            data = {
                request: {
                    action: 'DeleteIAServerInfo',
                    content: {
						iaType:'2',
						dbname:'20180313001',
						ip:'192.168.31.194',
						port:'80',
						desc:'20180313001'
                    }
                }
            };
        }

		if(false){
            data = {
                request: {
                    action: 'QueryFaceInfo',
                    content: {
						DBIndex : 2
                    }
                }
            };
        }

		if(false){
            data = {
                request: {
                    action: 'QueryAllFaceInfo',
                    content: {
						offset : 0,
						count : 10000
                    }
                }
            };
		}
		
		if(false){
            data = {
                request: {
                    action: 'GetNuInfo',
                    content: {
						
                    }
                }
            };
        }

		if(false){
            data = {
                request: {
                    action: 'QueryFaceRecognitionHistroyInfo',
                    content: {
						PUID:'151038400000002172',
						Idx:'0',
						IDNumber : '255564',
						BeginTime : '2017-01-21 18:57:40',
						EndTime : '2022-01-21 18:57:40',
						offset : 0,
						count : 20,
						Qkey : '255'
                    }
                }
            };
        }

		if(false){
            data = {
                request: {
                    action: 'QueryFaceDetectHistoryInfo',
                    content: {
						PUID:'151038401764648572',
						Idx:'0',
						BeginTime : '2017-01-21 18:57:40',
						EndTime : '2022-01-21 18:57:40',
						offset : 0,
						count : 20,
						Qkey : '115'
                    }
                }
            };
        }
	
		var jsonStr = jQuery.toJSON(data);
		
		jQuery('#request').val(jsonStr);
			
	});
	
	var send_http_request = function () {
		try {
			var jsonStr = jQuery('#request').val();
			
			jQuery.ajax({
				url: 'index.php',
				method: 'post',
				data: jsonStr,
				dataType: 'json',
				processData: false,
				success: function (data, ts) {
					jQuery('#response').val(jQuery.toJSON(data));
				},
				error: function (xhr, ts) {
					jQuery('#response').val('请求出错');
				}
			});
			
		}
		catch (e) {
			alert("excep -> " + e.name + "," + e.message);
		}
	};

	</script>
	
</head>

<body>
	<table>
		<tr>
			<td>请求报文主体：</td>
			<td><textarea id="request" rows="10" cols="100"></textarea></td>
		</tr>
		<tr>
			<td><button onclick="send_http_request();">发送请求</button></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>响应报文主体：</td>
			<td><textarea id="response" rows="10" cols="100"></textarea></td>
		</tr>
	</table>

</body>
</html>