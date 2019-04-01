var IALogin = __ial ={
	
	LoginCSS : function(width,height){
		$("body").css({
			"width": width,
			"height": height,
			"margin":"0px"
		});
        $(".login").css({
        	"width": width,
			"height": height,
			"position": "absolute",
			"background-image": "url(themes/images/denglubeijing.png)"
        });
		$(".login_insert").css({
			float:"left",
			width:(width*3)/5+"px",
			height:height+"px",
		});
		$(".login_box").css({
			float:"right",
			width:(width*2)/5+"px",
			height:height+"px",
		});
		$(".login_insert_image").css({
			"width": "758px",
			"height": "663px",
			"margin-top":(height-663)/2+"px",
			"margin-left":(((width*3)/5)-758)/2+"px",
			"background-image": "url(themes/images/chatu1.png)",
		});
		$(".login_box_inside").css({
			"width": "496px",
			"height":"526px",
			"margin-left":(((width*2)/5)-496)/2+"px",
			"margin-top": (height-526)/2+"px",
			"background": "white",
			"position":"absolute"
		});	
	},
	LoginControl : function(){
		$("#login_box_submit").off().on('click',function(){
			var userID=$("#userID").val();
			var password=$("#passwd").val();
			IAClient.Login(userID,password);
		});
		$(document).keyup(function(event){
		    if(event.keyCode ==13){
		       var userID=$("#userID").val();
			   var password=$("#passwd").val();
			   IAClient.Login(userID,password);
		     }
		});
	}
}
