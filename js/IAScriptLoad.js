var IAConfig = __icfg = {

	version : '',
    path:null,
	end : true
};

var IAScriptLoad = {
	version : "v1.5.1",
	langage:['zh_CN','en','zh_TW'],

	include : function (libraryName) {
        document.write('<script type="text/javascript" src="' + libraryName + '"></script>');
    },
    GetUrlRelativePath:function()
　      {
	    var lang="zh_CN";
	　　　var url = document.location.toString();
	　　　if(url.indexOf("?")!= -1){
	　　　　　var relUrl = url.split("?")[1];
	       if(relUrl!=""&&relUrl.indexOf("=")>-1)
	       {
	       	  var rel=relUrl.split("=")[1];
	       	  if(IAScriptLoad.langage.indexOf(rel)>-1){
	       	  	 lang=rel;
	       	  }
	       }
	    }
	    IAScriptLoad.load(lang);
　　  },
    load: function (lang) {

        var cr_scripts = new Array();
        var cr_csses = new Array();
        var scriptObjects = document.getElementsByTagName("script");


        for (var i = 0; i < scriptObjects.length; i++) {
            var s = scriptObjects[i];

            if (s.src && s.src.match(/IAScriptLoad\.js(\?.*)?$/)) {
                var path = s.src.replace(/js\/IAScriptLoad\.js(\?.*)?$/, '');
                __icfg.path=path;
                var type = s.src.match(/\?type=([a-z,]*)/);

                var t = new Date().getTime();

                cr_csses = [
                    path + "themes/stylesheet.css?t="+t,
                    path + "themes/icon.css?t="+t,
                    path + "lib/jquery/easyui-1.5.1/themes/default/easyui.css?t="+t,
                    path + "lib/jquery/easyui-1.5.1/themes/icon.css?t="+t,
                ];
                cr_scripts = cr_scripts.concat([
                    // - lib
                    path + 'lib/jquery/jquery-1.11.0.min.js',
                    path + 'lib/jquery/easyui-1.5.1/jquery.easyui.min.js',
                    path + 'lib/jquery/lib/jquery.cookie.js',
                    path + 'lib/jquery/lib/jquery.hash.js',
                    path + 'lib/jquery/lib/jquery.json-2.4.js',
                    path + 'lib/jquery/jquery-form.js',
                    
                    path + 'js/config.js',

                    path + 'js/lang-en.js',
                    
					// - custom js
                    path + 'js/Ajax.js',
                    path + 'js/custom.hash.js',
                    path + 'js/client.js',
                    path + 'js/frame.js',
                    path + 'js/login.js',
                    path + 'js/face.js',
                    path + 'js/car.js',
                ]);
                cr_scripts = cr_scripts.concat([
                	path + 'lib/jquery/easyui-1.5.1/locale/easyui-lang-'+lang+'.js',
                    path + 'js/lang-'+lang+'.js',
                ]);
                break;
            }
        }
        // CSS
        for (var j = 0; j < cr_csses.length; j++) {
            document.write('<link rel="stylesheet" type="text/css" href="' + cr_csses[j] + '" />');
        }
        for (var j = 0; j < cr_scripts.length; j++) {
        	IAScriptLoad.include(cr_scripts[j]);
        }
    },

    end :true
}

IAScriptLoad.GetUrlRelativePath();


if(window.attachEvent)
{
    window.attachEvent("onload",function(){
    	if(IAClient.BaseInfo!=null)
    	{
    		Ajax.SendAjax('DeleteFaceBaseInfo',IAClient.BaseInfo);
    	}
        if(typeof IAClient == "object" && IAClient.Load && typeof IAClient.Load == "function"){
            IAClient.Load();
        }
    });
    window.attachEvent("onUnload", function(){
    	if(IAClient.BaseInfo!=null)
    	{
    		Ajax.SendAjax('DeleteFaceBaseInfo',IAClient.BaseInfo);
    	}
        if(typeof IAClient == "object" && IAClient.UnLoad && typeof IAClient.UnLoad == "function"){
            IAClient.UnLoad();
        }
    });
}
else
{
    window.addEventListener("load",function(){
    	if(IAClient.BaseInfo!=null)
    	{
    		Ajax.SendAjax('DeleteFaceBaseInfo',IAClient.BaseInfo);
    	}
        if(typeof IAClient == "object" && IAClient.Load && typeof IAClient.Load == "function"){
            IAClient.Load();
        }
    },false);
    window.addEventListener("unload",function(){
    	if(IAClient.BaseInfo!=null)
    	{
    		Ajax.SendAjax('DeleteFaceBaseInfo',IAClient.BaseInfo);
    	}
        if(typeof IAClient == "object" && IAClient.UnLoad && typeof IAClient.UnLoad == "function"){
            IAClient.UnLoad();
        }
    },false);
}