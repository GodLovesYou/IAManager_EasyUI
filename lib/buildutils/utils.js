/*
---
name: Utils
time: 2014.08.04
author: huzw
...
*/

var Utils =
{
	version: "v1.2.1",
	
	debug: false,
	
	// 绑定数据源
	DSRender: 
	{
		Store: null,
		
		defaults: {
			page: 1,
			perSize: 20,
			sizeRange: [10, 20, 50, 100],
			orderBy: null,
			sortName: null,
			sortType: null,
			totalSize: null,
			filterString: {
				step1 : "第",
				step2 : "页,共{0}页",
				step3 : "显示{0}到{1},共{2}记录" 
			},
			loading: "正在处理,请稍候..."
		},
		
		Init: function (options)
		{
			try
			{
				var fn = "Utils.DSRender.Init";
				
				if (!this.Store)
				{
					this.Store = new Utils.Hash();
				}
			
				var options = options || {};
				
				if (options.target && options.url && $.isArray(options.columns))
				{
					var tmp = {};
					jQuery.extend(tmp, options);
					jQuery.extend(options, this.defaults, tmp);
					
					if (this.Store.containsKey(options.target)) {
						this.Clear(options.target);
					}
					this.Store.set(options.target, {
						condition: options,
						result: null
					});
					
					if (Utils.debug) Utils.Log(fn, $.toJSON(options));
					
					options.async = jQuery.type(options.async)!="undefined"?options.async:true;
					options.processData = jQuery.type(options.processData)!="undefined"?options.processData:true;
					
					this.CreateFrame(options.target);
					this.DoingAjax(options.target);
					
					return true;
				}
				else 
				{
					if (Utils.debug) Utils.Log(fn, "some parameters error...");
					return false;
				}
			}
			catch (e) {
				return false;
			}
		},
		
		CreateFrame: function (target)
		{
			try
			{
				var fn = "Utils.DSRender.CreateFrame";
			
				if (this.Store.containsKey(target)) {
					var dsNoder = this.Store.get(target);
					
					var options = dsNoder.condition,
						columns = options.columns;
					
					var html = [];
					// create data-header
					html.push('<div id="'+target+'-data-header" md-target="'+target+'" class="data-header">');
					jQuery.each(columns, function (index, item) {
						var width = jQuery.type(item.width) == "undefined" ? '100px' : (!isNaN(item.width) ? (item.width + 'px') : item.width);
						html.push('<div md-index="'+index+'" md-field="'+item.field+'" class="divth" title="'+(item.title||'')+'" style="width:'+(width)+';">'+(item.title||'')+'</div>');
					});
					html.push('</div>');
					
					// create data-middle
					html.push('<div id="'+target+'-data-middle" md-target="'+target+'" class="data-middle"></div>');
					
					// create data-footer
					html.push('<div id="'+target+'-data-footer" md-target="'+target+'" class="data-footer">');
						html.push('<div class="span-select">');
							html.push('<select md-select="perSize">');
							jQuery.each(options.sizeRange, function (index, item) {
								html.push('<option value="'+item+'">'+item+'</option>');
							})							
							html.push('</select>');
						html.push('</div>');
						html.push('<div class="span-flip">');
							html.push('<span>|</span>&nbsp;');
							html.push('<input type="button" md-flip="first" class="first" value="&lt;&lt;" />');
							html.push('&nbsp;<input type="button" md-flip="prev" class="prev" value="&lt;" />');
							html.push('&nbsp;<span>|</span>&nbsp;');
							html.push('<span><label>第</label>&nbsp;<input type="text" md-flip="go-page" class="go-page" value="1" />&nbsp;<label>页,共{0}页</label></span>');
							html.push('&nbsp;<span>|</span>&nbsp;');
							html.push('<input type="button" md-flip="next" class="next" value="&gt;" />');
							html.push('&nbsp;<input type="button" md-flip="last" class="last" value="&gt;&gt;" />');
							html.push('&nbsp;<span>|</span>&nbsp;');
							html.push('&nbsp;<input type="button" class="refresh" md-type="reload" title="" />&nbsp;');
						html.push('</div>');
						html.push('<div class="span-detail">显示{0}到{1},共{2}记录</div>');
					html.push('</div>');
					
					var jqTarget = jQuery("#" + target)
						.html(html.join(""));
					
					jqTarget.find(".data-footer .span-flip label:eq(0)").html(options.filterString.step1);
					jqTarget.find(".data-footer .span-flip label:eq(1)").html(options.filterString.step2);
					jqTarget.find(".data-footer .span-detail").html(options.filterString.step3);
					
					// 绑定改变页显示条数
					jqTarget
						.find(".data-footer[md-target='"+target+"'] .span-select select[md-select='perSize']")
						.val(options.perSize)
						.off("change")
						.on("change", function (event) {
							options.perSize = this.value || options.perSize;
							options.page = 1;
							Utils.DSRender.DoingAjax(target, true);
						});
					// 绑定跳转控制
					jqTarget
						.find(".data-footer[md-target='"+target+"'] .span-flip input[md-flip!='go-page']")
						.off("click")
						.on("click", function (event) {
							Utils.DSRender.Flip(options.target, jQuery(this).attr("md-flip"));
						});
					jqTarget
						.find(".data-footer[md-target='"+target+"'] input[md-flip='go-page']")
						.off("change")
						.on("change", function (event) {
							if (isNaN(this.value) || !/^\d+$/.test(this.value) || this.value < 1 || this.value > options.totalPage) {
								this.value = options.page;
							}
							else {
								options.page = parseInt(this.value);
								Utils.DSRender.DoingAjax(target);
							}
						});
					jqTarget
						.find(".data-footer[md-target='"+target+"'] .span-flip input[md-type='reload']")
						.off("click")
						.on("click mouseover mouseout", function (event) {
							switch (event.type) {
								case "click":
									Utils.DSRender.DoingAjax(target);
									break;
							}
						});
					
					return true;
				}
				return false;
			}
			catch (e) {
				return false;
			}
		},
		
		// 跳转控制
		Flip: function (target, action)
		{
			try
			{
				var fn = "Utils.DSRender.Flip";
			
				if (this.Store.containsKey(target)) {
					var dsNoder = this.Store.get(target);
					
					var options = dsNoder.condition;
					
					var curPage = options.page, toPage = curPage;
					switch (action) {
						case "first":
							toPage = 1;
							break;
						case "last":
							toPage = options.totalPage;
							break;
						case "prev":
							toPage = curPage - 1;
							if (toPage <= 0) {
								toPage = 1;
							}
							break;
						case "next":
							toPage = curPage + 1;
							if (toPage > options.totalPage) {
								toPage = options.totalPage;
							}
							break;
					}
					if (toPage <= 0) {
						toPage = 1;
					}
					
					options.page = toPage;
					Utils.DSRender.DoingAjax(target);
					
					return true;
				}
				return false;
			}
			catch (e) {
				return false;
			}
		},
		
		DoingAjax: function (target, removeData)
		{
			try
			{
				var fn = "Utils.DSRender.DoingAjax";
			
				if (this.Store.containsKey(target)) {
					var dsNoder = this.Store.get(target);
					
					var options = dsNoder.condition;
					if (removeData === true) {
						dsNoder.result = new Utils.Hash();
					}
					
					this.LoadingMask.Show(target);
					
					if (options.processData !== false) {
						options.data += "&page=" + options.page + "&perSize=" + options.perSize;
					}
					
					var doAjax = jQuery.ajax({
						url: options.url,
						async: options.async,
						data: options.data,
						type: options.type,
						dataType: options.dataType,
						processData: options.processData,
						success: function (data, textStatus) {
							Utils.DSRender.ResponseAjax(options.target, data);
							
							if (jQuery.type(options.callback) == "function") {
								options.callback("success", data, textStatus);
							}
						},
						error: function (xhr, textStatus) {
							if (jQuery.type(options.callback) == "function") {
								options.callback("error", xhr, textStatus);
							}
						},
						complete: function (xhr) {
							Utils.DSRender.LoadingMask.Hide(target);
						}
					});
					
					//if (options.async !== true) {
					//	this.ResponseAjax(options.target, jQuery.parseJSON(doAjax.responseText));
					//}
					return true;
				}
				return false;
			}
			catch (e) {
				return false;
			}
		},
		
		// 加载掩码
		LoadingMask: 
		{
			Show: function (target) 
			{
				if (Utils.DSRender.Store.containsKey(target)) {
					var dsNoder = Utils.DSRender.Store.get(target);
					
					var options = dsNoder.condition;
					
					var jqTarget = jQuery("#" + target).css("position", "relative");
						
					// loading tip
					if (!jqTarget.find(".loading-mask")[0]) {
						var html = [];
						html.push('<div class="loading-mask"></div>');
						html.push('<center class="loading-mask-box"><span class="loading"></span>'+options.loading+'&nbsp;</center>');
						
						jqTarget.prepend(html.join(""));
					}
					jqTarget.find(".loading-mask").show();
					var jq_mask_box = jqTarget.find(".loading-mask-box").show();
					jq_mask_box.css({
						left: (jqTarget.width() - jq_mask_box.width()) / 2 + "px",
						top: (jqTarget.height() - jq_mask_box.height()) / 2 + "px"
					});
					jqTarget
						.find(".data-footer[md-target='"+target+"'] .span-flip input[md-type='reload']")
						.attr("class", "loading");
				}
			},
			Hide: function (target)
			{
				var jqTarget = jQuery("#" + target).css("position", "status");
				jqTarget.find(".loading-mask").hide();
				jqTarget.find(".loading-mask-box").hide();
				
				jqTarget
					.find(".data-footer[md-target='"+target+"'] .span-flip input[md-type='reload']")
					.attr("class", "refresh");
			},
			end: true
		},
		
		ResponseAjax: function (target, data) 
		{
			try 
			{
				var data = data || null;
				
				if (jQuery.isPlainObject(data)) {
					
					if (this.Store.containsKey(target)) {
						var dsNoder = this.Store.get(target);
						
						var options = dsNoder.condition,
							columns = options.columns;
						
						if (data.status == "success") {
							if (jQuery.isPlainObject(data.content)) {
								options.totalSize = Number(data.content.total);
								
								dsNoder.result = dsNoder.result || new Utils.Hash();
								dsNoder.result.set(options.page, data.content.data);
								
								var html = [];
								jQuery.each(data.content.data, function (index, item) {
									html.push('<div md-index="'+index+'" md-target="'+target+'" class="divtr">');
									jQuery.each (columns, function (c_index, c_item) {
										if (typeof item[c_item.field] != "undefined") {
											var width = jQuery.type(c_item.width) == "undefined" ? '100px' : (!isNaN(c_item.width) ? (c_item.width + 'px') : c_item.width);
											
											var renderValue = item[c_item.field];
											if (typeof c_item.render != "undefined") {
												renderValue = c_item.render(index, renderValue, item);
											}
											html.push('<div md-column-Index="'+c_index+'" md-column-field="'+c_item.field+'" class="divtd" title="'+renderValue+'" style="width:'+(width)+';">'+renderValue+'</div>');
										}
									});
									html.push('</div>');
								});
								var jqDM = jQuery("#" + target + " .data-middle[md-target='"+target+"']")
									.html(html.join(""))
									.find("div[md-index][md-target='"+target+"\']")
									.each(function (line_index, line_item) {
										var clsname = line_index % 2 == 0 ? "odd" : "even";
										if (!jQuery(line_item).hasClass(clsname)) {
											jQuery(line_item).addClass(clsname);
										}
									})
									.off("click mouseover mouseout")
									.on("click mouseover mouseout", function (event) {
										switch (event.type) {
											case "click":
												
												break;
											case "mouseover":
												if (!jQuery(this).hasClass("hover")) {
													jQuery(this).addClass("hover");
												}
												break;
											case "mouseout":
												if (jQuery(this).hasClass("hover")) {
													jQuery(this).removeClass("hover");
												}
												break;
										}
									});
								
								//jQuery("#" + target + " .data-header").css("width", jqDM[0].clientWidth + "px");
								
								var jqDF = jQuery("#" + target + " .data-footer[md-target='"+target+"']");
								jqDF.find(".span-flip .go-page").val(options.page);
								options.totalPage = Math.ceil(options.totalSize/options.perSize);
								jqDF.find(".span-flip label:eq(1)")
									.html(Utils.stringFormatter(options.filterString.step2, options.totalPage));
								
								var beginDis = options.perSize * (options.page - 1) + 1;
								var endDis = beginDis + (data.content.data.length - 1);
								jqDF.find(".span-detail")
									.html(Utils.stringFormatter(options.filterString.step3, beginDis, endDis, options.totalSize));
							} 
							
							if (jQuery.type(options.callback) == "function") {
								options.callback("success", data);
							} 
						}
					}
				}
			}
			catch (e) {
			}
		},
		
		Clear: function (target)
		{
			try
			{
				
			}
			catch (e) {
			}
		},
		
		end: true
	},
	stringFormatter: function (formatter) {
		try {
			var formatter = formatter || "";
			if (!formatter) return formatter;
			
			for (var i = 1; i <= arguments.length - 1; i++) {
				var r = new RegExp("\\{"+(i-1)+"\\}", "gm");
				formatter = formatter.replace(r, arguments[i]); 
			}
		}
		catch (e) {
		}
		finally {
			return formatter || "";	
		}
	},
	/*
    ---
    [Public]
    fn: Utils.Hash
    desc: 哈希表对象
    time: 2013.09.03 -> 2014.11.07 mdf
    author:
        - zenghx
        - huzw
    remark:
        - [Common Struct Method]
    ...
    */
    Hash : function (object) 
    {
        var size = 0;
        var entry = new Object();
        
        this.set = function (key, value) 
        {
            if (typeof key == "undefined" || key == null || key == "") {
                return false;
            }
            if (!this.containsKey(key)) {
                size++;
            }
            entry[key] = typeof value != "undefined" ? value : null;
        };
        this.unset = function (key) 
        {
            if (this.containsKey(key)) {
                this.remove(key);
            }
        };
        this.get = function (key) 
        {
            return this.containsKey(key) ? entry[key] : null;
        };
        this.remove = function (key) 
        {
            if (this.containsKey(key) && (delete entry[key])) {
                size--;
            }
        };
        this.containsKey = function (key)
        {
            return (key in entry);
        };
        this.containsValue = function (value)
        {
            for (var prop in entry) {
                if (entry[prop] == value) {
                    return true;
                }
            }
            return false;
        };
        this.keys = function () 
        {
            var _keys = new Array();
            for (var prop in entry) {
                _keys.push(prop);
            }
            return _keys;
        };
        this.values = function () 
        {
            var _values = new Array();
            for (var prop in entry) {
                _values.push(entry[prop]);
            }
            return _values;
        };
        this.size = function () 
        {
            return size || 0;
        };
        this.clear = function () 
        {
            size = 0;
            entry = new Object();
        };
        this.inf = this._self = function () 
        {
            return entry;
        };
        this.each = function (iterator, context) 
        {
            var iterator = iterator || function () {};
            var context = context || this;
            var i = 0;
            for (var prop in entry) {
                var item = {
                    key : prop, value : entry[prop] 
                };
                if (iterator.call(context, item, i++)) {
					break;	
				}
            }
            i = 0;
        };
		this.any = function (iterator, content) 
		{
			var iterator = iterator || function () {};
            var content = content || this;
			var i = 0;
			for (var prop in entry) {
				var item = {
                    key : prop, value : entry[prop] 
                };
				if (iterator.call(content, item, i++)) {
					return true;
					break;	
				}
			};
			return false;
		};
		this.all = function (iterator, content) 
		{
			var iterator = iterator || function () {};
            var content = content || this;
			var i = 0;
			for (var prop in entry) {
				var item = {
                    key : prop, value : entry[prop] 
                };
				if (!iterator.call(content, item, i++)) {
					return false;
					break;	
				}
			};
			return true;
		};
		
		this.initialize = function (object)
        {
        	if (typeof object == "object" && object.constructor != Array)
    		{
        		var SELF = this;
        		
        		for(var key in object)
    			{
        			if (object[key] !== undefined)
    				{
        				SELF.set(key, object[key]);
    				}
    			}
    		}
        };
        this.initialize(object);
    },
    Array : 
    {
        // - 在数组中查找
        indexOf : function (array, value, from) 
        {
            try 
            {
                if (array && array.constructor == Array) 
                {
                    var from = Number(from) || 0;
                    from = ( from < 0 ? Math.ceil(from) : Math.floor(from) );
                    if ( from < 0 ) {
                        from += array.length;
                    }
                    var found = false;
                    for (; from < array.length; from++) {
                        if (from in array && array[from] === value) {
                            found = true;
                            break;
                        }
                    }
                    return found ? from : - 1;
                }
                else {
                    return - 1;
                }
            }
            catch (e) {
                return - 1;
            }
        },
		// - 查找最后匹配索引
		lastIndexOf : function (array, value, from)
		{
			try
			{
				if (array && array.constructor == Array) 
                {
                    var from = Number( from ) || 0;
                    from = ( from < 0 ? Math.ceil( from ) : Math.floor( from ) );
					
					if ( isNaN( from ) )
					{
						from = array.length - 1;
					}
					else
					{
						if (from < 0) 
							 from += array.length; 
						else if (from >= len) 
							 from = array.length - 1; 
					}
					
                    var found = false;
					for (; from > -1; from--) 
					{ 
					 	if (from in array && array[from] === value) 
						{
                            found = true;
                            break;
						} 
					}  
                  	
                    return found ? from : - 1;
                }
                else {
                    return - 1;
                }
			}
			catch (e) {
				return -1;	
			}
		},
        end : true 
    },
	/*
    ---
    [Public]
    fn: Utils.Timer
    desc: 定时器对象
    time: 2013.09.24
    author:
        - huzw
    remark:
        - [Common Timer]
    ...
    */
	Timer :
	{
		interval : 100, count : 0, timer : null, events: null,

        Start : function () 
		{
			try
			{
				var fn = "Utils.Timer.Start";
				
				if (Utils.Timer.timer == null)
				{
					Utils.Timer.timer = setInterval
					(
						Utils.Timer.Call, 
						Utils.Timer.interval
					);
				}
				
				return true;
			}
			catch(e) {
				if (Utils.Log)
				{
					Utils.Log(fn, "excp error = " + e.message + "::" + e.name);	
				}
				return false;	
			}			
        },

        Stop : function () 
		{
			try
			{
				var fn = "Utils.Timer.Start";
				
				if (Utils.Timer.timer != null)
				{
					clearInterval(Utils.Timer.timer);
					Utils.Timer.timer = null;
					Utils.Timer.events = new Utils.Hash();
					Utils.Timer.count = 0;
				}
				
				return true;
			}
			catch(e) {
				if (Utils.Log)
				{
					Utils.Log(fn, "excp error = " + e.message + "::" + e.name);	
				}
				return false;	
			}            
        },
		
		Set : function (ev, cb) 
		{
			try
			{
				var fn = "Utils.Timer.Set";
				
				if (Utils.Timer.events == null || !Utils.Timer.events instanceof Utils.Hash)
				{
					Utils.Timer.events = new Utils.Hash();
				}
				
				if (typeof Utils.Timer.events == "undefined") 
				{
					if (Utils.Log) Utils.Log(fn, "Utils.Timer.events undefined");
					return false;
				}
	
				if (typeof cb != "object" || typeof cb.name != "string" || typeof cb.fu != "function") 
				{
					if (Utils.Log) Utils.Log(fn, "cb struct error");
					return false;
				}
	
				if (!Utils.Timer.events.get(ev)) 
				{
					Utils.Timer.events.set(ev, new Utils.Hash());
				}
				if (Utils.Timer.events.get(ev)) 
				{
					Utils.Timer.events.get(ev).set
					(
						cb.name, 
						{
							name : cb.name,
							fu : cb.fu,
							interval : cb.interval
						}
					);
				}
				
				return true;
			}
			catch(e) {
				if (Utils.Log)
				{
					Utils.Log(fn, "excp error = " + e.message + "::" + e.name);	
				}
				return false;	
			}
        },
		ContainsKey : function (ev, cbName)
		{
			try
			{
				var fn = "Utils.Timer.ContainsKey";
				
				if (Utils.Timer.events == null || !Utils.Timer.events instanceof Utils.Hash)
				{
					Utils.Timer.events = new Utils.Hash();
				}
				
				if (typeof Utils.Timer.events == "undefined") 
				{
					if (Utils.Log) Utils.Log(fn, "Utils.Timer.events undefined");
					return false;
				}
	
				if (!Utils.Timer.events.get(ev) 
					|| !cbName || !Utils.Timer.events.get(ev).get(cbName)) 
				{
					if (Utils.Log) Utils.Log(fn, "ev or cbName not exists");
					return false;
				}
				
				return true;
			}
			catch(e) {
				if (Utils.Log)
				{
					Utils.Log(fn, "excp error = " + e.message + "::" + e.name);	
				}
				return false;	
			}
		},
		UnSet : function (ev, cbName) 
		{
			try
			{
            	var fn = "Utils.Timer.UnSet";
				
				if (typeof Utils.Timer.events == "undefined") 
				{
					if (Utils.Log) Utils.Log(fn, "Utils.Timer.events undefined");
					return false;
				}
	
				if (!Utils.Timer.events.get(ev)) 
				{
					if (Utils.Log) Utils.Log(fn, "ev undefined");
					return false;
				}
	
				if (typeof cbName != "string") 
				{
					if (Utils.Log) Utils.Log(fn, "cb name undefined");
					return false;
				}
	
				Utils.Timer.events.get(ev).unset(cbName);
				return true;
			}
			catch(e) {
				if (Utils.Log)
				{
					Utils.Log(fn, "excp error = " + e.message + "::" + e.name);	
				}
				return false;	
			}
        },
		
		Call : function()
		{
			try
			{
            	var fn = "Utils.Timer.Call";
				
				Utils.Timer.count++;
				
				Utils.Timer.events.each
				(
					function (item) 
					{
						var ev = item.value;
						if (ev && typeof ev.each == "function") 
						{
							ev.each
							(
								function (evItem) 
								{
									var evItemNode = evItem.value;
									if ((Utils.Timer.count * Utils.Timer.interval) % evItemNode.interval == 0) 
									{
										if (typeof evItemNode.fu == "function") 
										{
											evItemNode.fu();
										}
									}
								}
							)
						}
					}
				);
				
				if (Utils.Timer.count == 100000000) 
				{
					Utils.Timer.count = 0;
				}
				 
				return true;
			}
			catch(e) {
				if (Utils.Log)
				{
					Utils.Log(fn, "excp error = " + e.message + "::" + e.name);	
				}
				return false;	
			}
		},
		
		end : true
	},
    /*
    ---
    deac: 内部调试对象别名
    ...
    */
    Log : function (fnStr, log) {
		try
		{
			if (Utils.debug) {
				
				var dtext = "["+(fnStr||"Unknown")+"] " + (log||"");
				
				if (typeof console != "undefined") {
					console.log(dtext);
				}
				else {
					alert(dtext);
				}
			}
		}
		catch (e) {
		}
	}, /*
    *    函数名        ：DateFormat
    *    函数功能    ：格式化返回当前客户端系统时间  
    *    备注        ：无
    *    作者        ：Lingsen
    *    时间        ：2010年11月26日 
    *    返回值        ：无
    *    参数说明    ：1个参数.  
    *  string mask 时间样式
    */
    DateFormat : function (mask, d) 
    {
        if (typeof d == "undefined" || !d instanceof Date) {
            d = new Date();
        }
        if (typeof mask == "undefined" || mask == "" || mask == null) {
            mask = "yyyy-MM-dd HH:mm:ss";
        }
        return mask.replace(/"[^"]*"|'[^']*'|\b(?:d{1,4}|[m|M]{1,4}|yy(?:yy)?|([hHMstT])\1?|[lLZ])\b/g, 
        function ($0) 
        {
            var _zeroize = Utils.Zeroize || function (_me) 
            {
                return _me;
            };
            switch ($0) 
            {
                case 'd':
                    return d.getDate();
                case 'dd':
                    return _zeroize(d.getDate());
                case 'ddd':
                    return ['Sun', 'Mon', 'Tue', 'Wed', 'Thr', 'Fri', 'Sat'][d.getDay()];
                case 'dddd':
                    return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][d.getDay()];
                case 'M':
                    return d.getMonth() + 1;
                case 'MM':
                    return _zeroize(d.getMonth() + 1);
                case 'MMM':
                    return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][d.getMonth()];
                case 'MMMM':
                    return ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 
                    'October', 'November', 'December'][d.getMonth()];
                case 'yy':
                    return String(d.getFullYear()).substr(2);
                case 'yyyy':
                    return d.getFullYear();
                case 'h':
                    return d.getHours() % 12 || 12;
                case 'hh':
                    return _zeroize(d.getHours() % 12 || 12);
                case 'H':
                    return d.getHours();
                case 'HH':
                    return _zeroize(d.getHours());
                case 'm':
                    return d.getMinutes();
                case 'mm':
                    return _zeroize(d.getMinutes());
                case 's':
                    return d.getSeconds();
                case 'ss':
                    return _zeroize(d.getSeconds());
                case 'l':
                    return _zeroize(d.getMilliseconds(), 3);
                case 'L':
                    var m = d.getMilliseconds();
                    if (m > 99) {
                        m = Math.round(m  / 10);
                    }
                    return _zeroize(m);
                case 'tt':
                    return d.getHours() < 12 ? 'am' : 'pm';
                case 'TT':
                    return d.getHours() < 12 ? 'AM' : 'PM';
                case 'Z':
                    return d.toUTCString().match(/[A-Z]+$/);
                default:
                    return $0.substr(1, $0.length - 2);
            }
        });
    },
    GetDateTimeUTCSeconds : function (d) 
    {
        if (typeof d == "undefined" || !d instanceof Date) {
            d = new Date();
        }
        return d.getTime()  / 1000;
    },
    /* 标准的时间字符串转为时间戳 */
    DTStrToTimestamp : function (dateStr) 
    {
        dateStr = dateStr.strip();
        var d = new Date();
        var patn = /^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-)) (20|21|22|23|[0-1]?\d):[0-5]?\d:[0-5]?\d$/;
        if (patn.test(dateStr)) 
        {
            return new Date(dateStr.substr(0, 4), (parseInt(dateStr.substr(5, 2), 10) - 1), dateStr.substr(8, 
            2), dateStr.substr(11, 2), dateStr.substr(14, 2), dateStr.substr(17, 2));
        }
        else {
            return d;
        }
    },
    /*
    *    函数名        ：Zeroize
    *    函数功能    ：根据长度左补零
    *    备注        ：无
    *    作者        ：Lingsen
    *    时间        ：2010年11月26日 
    *    返回值        ：无
    *    参数说明    ：2个参数 
    *  string value     需要补零的值 
    *  number length     需要补零的值的长度
    */
    Zeroize : function (value, length) 
    {
        if (!length) {
            length = 2;
        }
        value = String(value);
        for (var i = 0, zeros = ''; i < (length - value.length); i++) {
            zeros += '0';
        }
        return zeros + value;
    },
    NetToHost16 : function (u) 
    {
        u = parseInt(u, 10);
        return ((((u) << 8) & 0xFF00) | ((u) >> 8));
    },
    NetToHost32 : function (u) 
    {
        u = parseInt(u, 10);
        return (((u) << 24) | (((u) << 8) & 0x00FF0000) | (((u) >> 8) & 0x0000FF00) | (0x000000FF & ((u) >> 24)));
    },
    XML : function (type, xmlFile) 
    {
        try 
        {
			var xmlDoc,
				isLoadFile = typeof type != "undefined" && type == "path" ? true : false;
			
			if (isLoadFile) {
				if (window.XMLHttpRequest) {
					var xhr = new window.XMLHttpRequest();
					xhr.open("GET", xmlFile, false);
					xhr.send();
					xmlDoc = xhr.responseXML;
				}
				else if (document.implementation && document.implementation.craeteDocument) {
					xmlDoc = document.implementation.createDocument('', '', null);
					xmlDoc.load(xmlFile); 
				}
				else {
					xmlDoc = new ActiveXObject('Microsoft.XMLDOM');
					xmlDoc.async = false;
					xmlDoc.load(xmlFile);
				}
			}
			else {
				if (window.DOMParser) {
					var parser = new window.DOMParser();
					xmlDoc = parser.parseFromString(xmlFile, "text/xml");
				}
				else {
					xmlDoc = new ActiveXObject('Microsoft.XMLDOM');
					xmlDoc.async = false;
					xmlDoc.loadXML(xmlFile);
				}
			}
            return xmlDoc || null;
        }
        catch (e) { 
			return null;
		}
    },
    /*
    *    对象名        ：CheckByteLength
    *    功能        ：验证字串长度  
    *    备注        ：无
    *    作者        ：Lingsen
    *    时间        ：2011年04月10日 
    */
    CheckByteLength : function (value, minlen, maxlen) 
    {
        if (!value) {
            value = "";
        }
        var l = value.length;
        var blen = 0;
        for (i = 0; i < l; i++) {
            if ((value.charCodeAt(i) & 0xff00) != 0) {
                blen++;
            }
            blen++;
        }
        if (blen > maxlen || blen < minlen) {
            return false;
        }
        return true;
    },
    /*
    ---
    fn: GetStringRealLength
    time: 2013.01.30
    author: 
    - huzw
    returns:
    - succ length of source string
    params:
    - source(string) 源中英文混合字符串
    ...
    */
    GetStringRealLength : function (source) 
    {
        try 
        {
            var fn = "Utils.GetStringRealLength";
            var source = source || "";
            var l = source.length;
            var blen = 0;
            for (i = 0; i < l; i++) {
                blen++;
                if ((source.charCodeAt(i) & 0xff00) != 0) {
                    blen++;
                }
            }
            return blen;
        }
        catch (e) 
        {
            if (Utils.Log) {
                Utils.Log(fn, "exception, error = " + e.name + "::" + e.message);
            }
            return "";
        }
    },
    /*
    *    对象名        ：Regexs
    *    功能        ：预定义正则式,   
    *    备注        ：无
    *    作者        ：Lingsen
    *    时间        ：2010年08月10日 
    */
    Regexs : 
    {
        "uint" : /^[0-9]*$/, "domain" : "^((https|http|ftp|rtsp|mms)?://)" + "?(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?" /*
        ftp的user@ */
         + "(([0-9]{1,3}\.){3}[0-9]{1,3}" /* IP形式的URL- 199.194.52.184 */
         + "|" /* 允许IP和DOMAIN（域名）*/
         + "([0-9a-z_!~*'()-]+\.)*" /* 域名- www. */
         + "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\." /* 二级域名 */
         + "[a-z]{2,6})" /* first level domain- .com or .museum */
         + "(:[0-9]{1,5})?" /* 端口- :80 */
         + "((/?)|" /* a slash isn't required if there is no file name */
         + "(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$", guid : /^0x[a-z0-9]{32}$/i, "puid" : /^[A-Za-z0-9]+$/i, 
        // puid reg
        strip : /(^\s*)|(\s*$)/g, end : true 
    },
	UnicodetoUTF8 : function (s) 
	{
		var c, d = "";
		for (var i = 0; i < s.length; i++) 
		{
			c = s.charCodeAt(i);
			if (c <= 0x7f) {
				d += s.charAt(i);
			}
			else if (c >= 0x80 && c <= 0x7ff) 
			{
				d += String.fromCharCode(((c >> 6) & 0x1f) | 0xc0);
				d += String.fromCharCode((c & 0x3f) | 0x80);
			}
			else 
			{
				d += String.fromCharCode((c >> 12) | 0xe0);
				d += String.fromCharCode(((c >> 6) & 0x3f) | 0x80);
				d += String.fromCharCode((c & 0x3f) | 0x80);
			}
		}
		return d;
	},
	UTF8toUnicode : function (s) 
	{
		var c, d = "", flag = 0, tmp;
		for (var i = 0; i < s.length; i++) 
		{
			c = s.charCodeAt(i);
			if (flag == 0) 
			{
				if ((c & 0xe0) == 0xe0) {
					flag = 2;
					tmp = (c & 0x0f) << 12;
				}
				else if ((c & 0xc0) == 0xc0) {
					flag = 1;
					tmp = (c & 0x1f) << 6;
				}
				else if ((c & 0x80) == 0) {
					d += s.charAt(i);
				}
				else {
					flag = 0;
				}
			}
			else if (flag == 1) {
				flag = 0;
				d += String.fromCharCode(tmp | (c & 0x3f));
			}
			else if (flag == 2) {
				flag = 3;
				tmp |= (c & 0x3f) << 6;
			}
			else if (flag == 3) {
				flag = 0;
				d += String.fromCharCode(tmp | (c & 0x3f));
			}
			else {
				flag = 0;
			}
		}
		return d;
	},
	UnicodetoGB2312 : function (str) 
	{
		return unescape(str.replace(/\\u/gi, '%u'));
	},
	GB2312toUnicode : function (str) 
	{
		return escape(str).toLocaleLowerCase().replace(/%u/gi, '\\u');
	},

	/**
	 * IFDataType @huzw 2014.11.07 
	 */
	IFDataType: function(data, type)
	{
		switch(type)
		{
			case "string":
				return Object.prototype.toString.call(data) === "[object String]";
				break;
			case "object":
				return Object.prototype.toString.call(data) === "[object Object]";
				break;
			case "function":
				return Object.prototype.toString.call(data) === "[object Function]";
				break;
			case "array":
				return Object.prototype.toString.call(data) === "[object Array]";
				break;
			default:
				return	Object.prototype.toString.call(data);
				break;
		}
	},
	
	/**
	 * 特殊字符转义
	 */
	ExcludeSpecialChars: function (orgString) {
		try {
			
			return orgString
					.replace(/\'/g,'&apos;')
					.replace(/\</g,'&lt;')
					.replace(/\>/g,'&gt;')
					.replace(/\&/g,'&amp;')
					.replace(/\"/g,'&quot;');
			
		}
		catch (e) {
			return orgString || '';
		}
	},
	/**
	 * 特殊字符剔除
	 */
	SetNullSpecialChars: function (orgString) {
		try {
			
			return orgString
					// 去掉转义字符  
					.replace(/[\'\"\\\/\b\f\n\r\t]/g, '')
					// 去掉特殊字符  
					.replace(/[\@\#\$\%\^\&\*\{\}\:\"\L\<\>\?]/, '');
		}
		catch (e) {
			return orgString || '';
		}
	},
	
	
	
	/**
	 * Form @huzw 2014.11.07
	 */
	Form: {
		// 提示CSS样式
		NoteClass: {
			ok: "note-ok",
			warning: "note-warning",
			error: "note-error",
			normal: "note-normal",
			Get: function (key) {
				return this[key] || "";
			},
			Set: function (key, value) {
				if (this[key]) this[key] = value;
			}
		},
		
		// 验证
		Validator: function(formEl) {
			this.formEl = null;
			this.elements = [];
			
			this.NoteInfors = {
				infors: null,
				Get: function (inforKey) {
					if (inforKey && this.infors && this.infors[inforKey]) {
						return this.infors[inforKey];
					}
				},
				Set: function (inforKey, value) {
					if (this.infors == null)
					{
						this.infors = {};
					}
					if (inforKey && value !== undefined)
					{
						this.infors.set(infoKey, value);
					}
				},
				Init: function (infors) {
					if (typeof infors == "object" && infors.constructor != Array) {
						this.infors = infors || {};
					}
				}
			};
			
			this.Init = function (formEl) {
				try {
					if (Utils.IFDataType(formEl, "string")) {
						this.formEl = document.getElementById(formEl);
					}
					else if (Utils.IFDataType(formEl, "object")) {
						this.formEl = formEl;
					}
					
					if (!this.formEl) {
						if (Utils.debug) {
							Utils.Log("Utils.Form.Validator::Init", "the formEl unknown");
						}
						return false;
					}
					
					var els = this.formEl.getElementsByTagName("*");
					for(var i = 0; i < els.length; i++) {
						if (!els[i].getAttribute("name")) continue;
						this.elements.push(els[i]);
					}
				}
				catch (e) {
					return false;
				}
			};
			
			this.Validate = function (infors) {
				try {
					var SELF = this;
					
					this.NoteInfors.Init(infors);
					
					var els = this.elements;
					
					for(var i = 0; i < els.length; i++) {
						var el = els[i];
						
						if (this.NoteInfors && this.NoteInfors.Get(el.name)) {
							switch (el.type.toLowerCase()) {
							case "text":
							case "textarea":
							case "password":
								var noteEl = document.getElementById(el.id + "_note");
								if (noteEl) {
									noteEl.className = "inputnote " + Utils.Form.NoteClass.Get('normal');
								}
								 
								el.onfocus = function () {
									var noteEl = document.getElementById(this.id + "_note");
									if (noteEl.className != Utils.Form.NoteClass.Get('error')) {
										var noteInfor = SELF.NoteInfors.Get(this.name);
										noteEl.innerHTML = noteInfor.focusNote;
										noteEl.className = "inputnote " + Utils.Form.NoteClass.Get('warning'); 
									}
								};
								el.onblur = function () {
									var noteEl = document.getElementById(this.id + "_note");
									var noteInfor = SELF.NoteInfors.Get(this.name);
									
									var checkrv = Utils.Validate.Check({
										value: this.value,
										checkReg: noteInfor.regKey,
										minlen: noteInfor.minlen,
										maxlen: noteInfor.maxlen,
										isnull: noteInfor.isnull,
										cmpObj: noteInfor.cmpObj || null
									});
									
									if (checkrv) {
										if (noteInfor.remoteCheck && typeof noteInfor.getRemoteArgs == "function") {
											Utils.Validate.RemoteCheck(noteInfor, noteEl, (noteInfor.getRemoteArgs() || ""));
										}
										else {
											noteEl.innerHTML = noteInfor.okNote;
											noteEl.className = "inputnote " + Utils.Form.NoteClass.Get('ok');
										}
									}
									else {
										noteEl.innerHTML = noteInfor.errorNote;
										noteEl.className = "inputnote " + Utils.Form.NoteClass.Get('error');
									}
								}; 
								break;
							default:
								break;
							}
						}
					}
					
				}
				catch (e) {
				}	
			};
			
			this.IsValid = function () {
				try {
					
				}
				catch (e) {
					return false;
				}
			};
			
			this.Init(formEl);
		},
		
		Serialize: function () {
			try {
				
			}
			catch (e) {
				return "";
			}
		},
		
		end: true
	}, 
	
	/**
	 * 验证
	 */
	Validate: {
	    // options of {value: ?, checkReg: ?, minlen: ?, maxlen: ?, min: ?, max: ?, isnull: ?, callback: ? }
		Check: function (options) {
			try {
				var checkrv = false;
				
				options = options || {};
				
				if (options.checkReg) {
					if (options.checkReg.constructor == String) {
						switch(options.checkReg) {
						case "Length":
							checkrv = this.CheckByteLength(options.value, options.minlen, options.maxlen, options.isnull);
							break;
						case "NumberRange":
							checkrv = this.CheckByteLength(options.value, options.min, options.max, options.isnull);
							break;
						case "Email":
							checkrv = this.CheckEmail(options.value, options.isnull, options.callback);
							break;
						case "AlphabetNumber_":
							checkrv = this.CheckAlphabetNumber_(options.value, options.isnull);
							break;
						case "UnsignedInt":
							checkrv = this.CheckUnsignedInt(options.value, options.isnull);
							break;
						case "Decimal":
							checkrv = this.CheckDecimal(options.value, options.decimal, options.isnull);
							break;
						case "Password":
							checkrv = this.CheckPassword(options.value, options.isnull, options.minlen, options.maxlen);
							break;
						case "CPassword":
							checkrv = this.CheckCPassword(options.value, options.cmpObj);
							break;
						case "Phone":
							checkrv = this.CheckPhone(options.value, options.isnull);
							break;
						case "Phones":
							checkrv = this.CheckPhones(options.value, options.isnull);
							break;
						case "IPPort":
							checkrv = this.CheckIPPort(options.value, options.isnull);
							break;
						}
					}
					else if (options.checkReg.constructor == Array) {
						var o = {};
						for(var key in options) {
							if (o[key] == undefined) {
								o[key] = options[key];
							}
						}
						for (var i = 0; i < options.checkReg.length; i++) {
							o.checkReg = options.checkReg[i];
							checkrv = this.Check(o);
							if (!checkrv) {
								break;
							}
							checkrv = true;
						}  
					} 
				}
				
				return checkrv; 
			}
			catch (e) {
				if (Utils.debug) {
					Utils.Log("Utils.Validate.Check", "excp error = " + e.name + "::" + e.message);
				}
				return false;
			}
		},
		// 远程校验
		RemoteCheck: function (noteInfor, noteEl, remoteArgs) {
			try {
				var checkrv = true;
				
				
				return checkrv;
			}
			catch (e) {
				if (Utils.debug) {
					Utils.Log("Utils.Validate.RemoteCheck", "excp error = " + e.name + "::" + e.message);
				}
				return false;
			}
		},
		
		// 检查字符串长度
		CheckByteLength: function (value, minlen, maxlen, isnull) {
			if (value === "" || value === undefined || value == null) {
				return isnull == true ? true : false;
			}
			return Utils.CheckByteLength(value.toString(), minlen, maxlen); 
		},
		// 检查数字范围
		CheckNumberRange: function (value, min, max, isnull) {
			if (!value || value == null || value == "") {
				return isnull == true ? true : false;
			}
			if (isNaN(value)) return false;
			
			if (isNaN(min) || min == null || min == undefined) {
				return false;
			}
			if (isNaN(max) || max == null || max == undefined) {
				return false;
			}
			if (parseFloat(value) < parseFloat(min) || parseFloat(value) > parseFloat(max)) {
				return false;
			}
			return true;
		},
		// 检查邮箱地址
		CheckEmail: function (value, isnull) {
			if (!value || value == null || value == undefined) {
				return isnull == true ? true : false;
			}
			
			var r = /^[_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]*)*@[a-zA-Z0-9\-]+([\.][a-zA-Z0-9\-]+)+$/;
			return r.test(value) ? true : false; 
		},
		// 校验是否为字母、数字、下划线组成
		CheckAlphabetNumber_: function (value, isnull) {
			if (!value || value == null || value == undefined) {
				return isnull == true ? true : false;
			}
			
			var r = /^[a-zA-Z0-9_]$/;
			if (!r.test(value)) return false;
			return true;
		},
		// 校验是否为正整数
		CheckUnsignedInt: function (value, isnull) {
			if (!value || value == null || value == undefined) {
				return isnull == true ? true : false;
			}
			
			var r = /^\d*$/;
			if (!r.test(value)) return false;
			return true;
		},
		// 校验是否为小数正整数
		CheckDecimal: function (value, decimal, isnull) {
			if (!value || value == null || value == undefined) {
				return isnull == true ? true : false;
			}
			
			if (!decimal || !this.CheckUnsignedInt(decimal)) {
				decimal = 0;
			}
			
			var r = eval("/\^\\d*(\\d|(\\.[0-9]{0,"+decimal+"}))\$/");
			if (r.test(value) && !isNaN(value)) {
				return true;
			}
			return false;
		},
		// 校验密码
		CheckPassword: function(value, isnull, minlen, maxlen) {
			if(isnull && (!value || value == null || value == ""))
			{
				return true;
			}
			var str = value;
			if(!this.CheckByteLength(str,(maxlen==null||minlen==undefined?6:minlen),(maxlen==null||maxlen==undefined?20:maxlen))) return false;
			return true;
		},
		// 确认密码
		CheckCPassword: function (value, cmpObj) {
			if (value != cmpObj.value) return false;
			return true;
		},
		// 校验是否为电话
		CheckPhone: function (value, isnull) {
			if (!value || value == null || value == undefined) {
				return isnull == true ? true : false;
			}
			var r = /^[0-9-\/]*$/;
			if (!r.test(value)) return false;
			return true;
		},
		// 校验多个电话，以英文';'分割
		CheckPhones: function (value, isnull) {
			if (!value || value == null || value == undefined) {
				return isnull == true ? true : false;
			}
			var r = /^[0-9-\;]*$/;
			if (!r.test(value)) return false;
			return true;
		},
		// 校验IP
		CheckIPValue: function (value, isnull) {
			if (!value || value == null || value == undefined) {
				return isnull == true ? true : false;
			}
			var r =  /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/   
			if (!r.test(value)) return false;
			return true;
		},
		// 校验IP:Port
		CheckIPPort: function (value, isnull) {
			if (!value || value == null || value == undefined) {
				return isnull == true ? true : false;
			}
			var r = /^([0-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-5])[\.]([0-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-5])[\.]([0-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-5])[\.]([0-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-5])[\:]([0-9]{1,5})*$/;
			if (!r.test(value)) return false;
			return true;
		},
		SubString: function(str, len, hasDot) { 
            var newLength = 0; 
            var newStr = ""; 
            var chineseRegex = /[^\x00-\xff]/g; 
            var singleChar = ""; 
            var strLength = str.replace(chineseRegex,"**").length; 
            for(var i = 0;i < strLength;i++) 
            { 
                singleChar = str.charAt(i).toString(); 
                if(singleChar.match(chineseRegex) != null) 
                { 
                    newLength += 2; 
                } 
                else 
                { 
                    newLength++; 
                } 
                if(newLength > len) 
                { 
                    break; 
                } 
                newStr += singleChar; 
            } 

            if(hasDot && strLength > len) 
            { 
                newStr += "..."; 
            } 
            return newStr; 
        },
		end: true
	},
	
	/*
	---
	desc: 基于jQuery发送Ajax请求
	author: huzw 
	time: 2015.10.17
	params: options
			{
				async: true(default) | false 是否异步
				url: ?
				data: ?
				dataType: json | jsonp | text...
				callback: ? 回调函数
				...
			}
	...
	*/
	_doAjax: function (options) {
		try {
			var options = options || {};
			
			var xhr = jQuery.ajax({
				url: options.url,
				contentType: options.contentType || 'application/x-www-form-urlencoded',
				async: options.async === false ? false : true,
				method: options.method || 'POST',
				data: options.data || {},
				dataType: options.dataType || 'json',
				processData: options.processData === false ? false : true,
				complete: function (data, ts) {
					try {
						if (jQuery.isFunction (options.complete)) {
							options.complete.call(this, data, ts);
						}
					}
					catch (e) {
					}
				},
 				success: function (data, ts, xhr) {
					try {
					
						if (jQuery.isFunction (options.success)) {
							options.success.call(this, data, ts, xhr);
						}
						if (jQuery.isFunction(options.callback)) {
							options.callback.call(options.callback, data);
						}
					}
					catch (e) {
					}
				},
				error: function (xhr, ts) {
					try {
						if (jQuery.isFunction (options.error)) {
							options.error.call(this, xhr, ts);
						}
					}
					catch (e) {
					}
				}
			});
			
			if (options.async === false) {
				if (jQuery.isFunction(options.callback)) {
					options.callback(xhr.responseText);
				}
				return xhr;
			}			
			
			return true;
		}
		catch (e) {
			return false;
		}
	},
	
	/*
	---
	desc: 加载文件助手（JS、CSS等）
	author: huzw 
	time: 2016.10.08
	...
	*/
	LoadHelper: {
		store: {},
		
		/*
		---
		options -> key: ?(eg:'vodplayer'), override: true/false, callback: ?, files: [{..},...]
		eg: Utils.LoadHelper.Register({
				key: 'vodplayer',
				override: false, // true (override old files) | false (default) 
				callback: ?,
				files: [
					{type: 'script', src: 'js/crvodplayer.js'}
				]
			});
		...
		*/
		Register: function (options)
		{
			try
			{
				var fn = "Utils.LoadHelper.Register";
				
				var options = options || {};
				if (options.key && options.files) {
					
					// 覆盖原先的文件对象
					if (options.override === true) {
						Utils.LoadHelper.UnRegister();
					}
					
					if (typeof this.store[options.key] == "undefined")
					{
						options.files = options.files.constructor != Array ? [options.files] : options.files;
						if (options.files.length <= 0) {
							return false;
						}
						
						this.store[options.key] = options;
						
						var container = null;
						
						var containers = document.getElementsByTagName('head');
						if (containers.length > 0) {
							container = containers.item(0);
						}
						else {
							container = document.getElementsByTagName('body').item(0);
						}
						
						for (var i = 0; i < options.files.length; i++)
						{
							// 使用闭包原理处置
							(function (f) {
								if (typeof f.type == "undefined") {
									f.type = 'script';
								}
								
								// 如果已经存在，那么不需要重新加载了
								if (typeof f.id != "undefined") {
									
									var exists = false;
									
									if (typeof f.context != "undefined") {
										if (f.context.document && f.context.document.getElementById(f.id)) {
											exists = true;
											f.ele = f.context.document.getElementById(f.id);
										}
									}
									else {
										if (document.getElementById(f.id)) {
											exists = true;
											f.ele = document.getElementById(f.id);
										}
									}
									
									if (exists) {

										// 移除原文件
										if (options.override === true) {
											try {
												if (f.ele.parentNode != undefined) {
													f.ele.parentNode.removeChild(f.ele);
												}
											}
											catch (e) {
											}
										}
										else {
											f._loaded = true;
											
											// 实际上处在闭包函数中，故需要返回
											return false;
										}
									}
								}
								
								f.ele = null;
								switch (f.type)
								{
									case 'script':
										f.ele = document.createElement('script');
										f.ele.setAttribute('type', 'text/javascript');
										if (f.id) f.ele.setAttribute('id', f.id);
										if (f.name) f.ele.setAttribute('name', f.name);
										if (f.src) f.ele.setAttribute('src', f.src);
										break;
									case 'css':
										f.ele = document.createElement('link');
										f.ele.setAttribute('type', 'text/css');
										f.ele.setAttribute('rel', 'stylesheet');
										if (f.id) f.ele.setAttribute('id', f.id);
										if (f.name) f.ele.setAttribute('name', f.name);
										if (f.href) f.ele.setAttribute('href', f.href);
										break;
								}
								if (f.ele) {
									f._loaded = false;
									
									f.ele.onload = f.ele.onreadystatechange = function () {
										if (!this.readyState || this.readyState === "loaded" || this.readyState === "complete") {
											
											f._loaded = true;
											
											f.ele.onload = f.ele.onreadystatechange = null;

											Utils.LoadHelper.Call(options.key);
										}
									};
									container.appendChild(f.ele);
								}
								
							})(options.files[i]);
							
						}

						// 先检测一下是否已经全部加载了
						Utils.LoadHelper.Call(options.key);
					}
				}
			}
			catch (e)
			{
				if (Utils.debug) {
					Utils.Log(fn, "excp error = " + e.name + "::" + e.message);
				}
			}
		},
		/*
		---
		options -> key: ?(eg:'vodplayer')
		...
		*/
		UnRegister: function (options)
		{
			try
			{
				var fn = "Utils.LoadHelper.UnRegister";
			
				if (typeof this.store[key] != "undefined")
				{
					this.store[key] = null;
					delete this.store[key];
				}
			}
			catch (e)
			{
				if (Utils.debug) {
					Utils.Log(fn, "excp error = " + e.name + "::" + e.message);
				}
			}
		},
		
		GetLoadStatus: function (key)
		{
			try
			{
				var fn = "Utils.LoadHelper.GetLoadStatus";
				
				var status = {
					found: false
				};
				
				if (!key || typeof this.store[key] == "undefined")
				{
					return status;
				}
				
				if (typeof this.store[key] != "undefined")
				{
					var node = this.store[key];
					
					status.found = true;
					
					if (node._response === true) {
						status.loaded = true;
					}
					else {
						status.loaded = false;
					}
				}
				
				return status;
			}
			catch (e)
			{
				return {
					found: false
				};
			}
		},
		
		Call: function (key) 
		{
			try
			{
				var fn = "Utils.LoadHelper.Call";
				
				if (typeof this.store[key] != "undefined")
				{
					var node = this.store[key];
					
					if (node._response === true) {
						return false;
					}
					
					var waitLoaded = 0;
					var doneLoaded = 0; 
					
					for (var i = 0; i < node.files.length; i++)
					{
						var f = node.files[i];
						if (f && f.type) {
							if (typeof f._loaded != "undefined") {
								waitLoaded++;
								if (f._loaded) {
									doneLoaded++;
								}
							}
						}
					}
					
					if (waitLoaded == doneLoaded) {
						if (typeof node.callback == "function") {
							node.callback();
							
							node._response = true;
						}
					}
				}
			}
			catch (e)
			{
				if (Utils.debug) {
					Utils.Log(fn, "excp error = " + e.name + "::" + e.message);
				}
			}
		},
		
		end: true
	},
	
	// 蒙版文字提示（依赖jQuery&easyui）
	Mask: 
	{
		maskID: "cr-mask-window-xxx",
		
		maskObj: null,
		
		count: 0,
		
		Show: function (msg, isModal)
		{
			try {
				var fn = "Utils.Mask.Show";
				
				if (!msg) return false;
				
				var isModal = isModal === false ? false : true;
				
				this.count ++;
				
				var maskID = this.maskID,
					contentID = maskID + "_content";
					
				if (!this.maskObj) {
					if (!jQuery("#" + maskID)[0]) {
						this.maskObj = jQuery("body")
							.append('<div id="'+maskID+'"></div>')
							.find("#" + maskID)
							.window({
								width: 200,
								height: 40,
								noheader: true,
								collapsible: false,
								minimizable: false,
								maximizable: false,
								closable: false,
								resizable: false,
								modal: isModal,
								content: '<div id="'+contentID+'" class="loading" style="width:100%;height:26px;line-height:26px;vertical-align:middle;text-indent:20px;border:0px gray solid;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;"></div>'
							});
					}
				}
				if (this.maskObj) {
					
					this.maskObj.window({
						modal: isModal
					});
					
					if (jQuery("#" + contentID)[0]) {
						var realLength = Utils.GetStringRealLength(msg),
							originalLength = msg.length;
						jQuery("#" + contentID).html(msg);
						this.maskObj.window("resize", {
							width: (45 + 6 * realLength - 0 * originalLength)
						});
					}
					this.maskObj.window("open").window("center");
				}
			}
			catch (e) {
				return false;
			}
		},
		
		SetCenter: function ()
		{
			if (this.maskObj) {
				this.maskObj.window("center");
			} 
		},
		
		Hide: function (remove, forceHide)
		{
			try {
				var fn = "Utils.Mask.Hide";
				
				if (this.maskObj) {
					
					if (forceHide !== true && -- this.count > 0) {
						return false;	
					}
					
					if (remove === true) {
						this.maskObj.window("destroy");
						this.maskObj = null;
					}
					else {
						this.maskObj.window("close");
					}
					
					this.count = 0;
				}
			}
			catch (e) {
				return false;
			}
		},
		
		end: true
	},
	
	// ========================================================================
	// 	- MD5 code ---
	// ========================================================================
	MD5: {
		hexcase : 0,  /* hex output format. 0 - lowercase; 1 - uppercase        */
		b64pad  : "", /* base-64 pad character. "=" for strict RFC compliance   */
		chrsz   : 8,  /* bits per input character. 8 - ASCII; 16 - Unicode      */
		
		Hex_MD5:function (s)
		{
			return Utils.MD5.BinlToHex(Utils.MD5.Core_MD5(Utils.MD5.StrToBinl(s), s.length * Utils.MD5.chrsz));
		},
		
		B64_MD5:function(s){ return Utils.MD5.BinlToB64(Utils.MD5.Core_MD5(Utils.MD5.StrToBinl(s), s.length * Utils.MD5.chrsz));},
		Str_MD5:function(s){ return Utils.MD5.BinlToStr(Utils.MD5.Core_MD5(Utils.MD5.StrToBinl(s), s.length * Utils.MD5.chrsz));},
		Hex_HMac_MD5:function(key, data) { return Utils.MD5.BinlToHex(Utils.MD5.Core_HMac_MD5(key, data)); },
		B64_HMac_MD5:function(key, data) { return Utils.MD5.BinlToB64(Utils.MD5.core_HMac_MD5(key, data)); },
		Str_HMac_MD5:function(key, data) { return Utils.MD5.BinlToStr(Utils.MD5.core_HMac_MD5(key, data)); },
		
		/*
		 * Perform a simple self-test to see if the VM is working
		 */
		MD5_VM_Test:function()
		{
		  return Utils.MD5.Hex_MD5("abc") + "==900150983cd24fb0d6963f7d28e17f72";
		},
	
		/*
		 * Calculate the MD5 of an array of little-endian words, and a bit length
		 */
		Core_MD5:function(x, len)
		{
		  /* append padding */
			x[len >> 5] |= 0x80 << ((len) % 32);
			x[(((len + 64) >>> 9) << 4) + 14] = len;
			
			var a =  1732584193;
			var b = -271733879;
			var c = -1732584194;
			var d =  271733878;
			
			for(var i = 0,max = x.length; i < max; i += 16)
			{
				var olda = a;
				var oldb = b;
				var oldc = c;
				var oldd = d;
				
				a = Utils.MD5.MD5_FF(a, b, c, d, x[i+ 0], 7 , -680876936);
				d = Utils.MD5.MD5_FF(d, a, b, c, x[i+ 1], 12, -389564586);
				c = Utils.MD5.MD5_FF(c, d, a, b, x[i+ 2], 17,  606105819);
				b = Utils.MD5.MD5_FF(b, c, d, a, x[i+ 3], 22, -1044525330);
				a = Utils.MD5.MD5_FF(a, b, c, d, x[i+ 4], 7 , -176418897);
				d = Utils.MD5.MD5_FF(d, a, b, c, x[i+ 5], 12,  1200080426);
				c = Utils.MD5.MD5_FF(c, d, a, b, x[i+ 6], 17, -1473231341);
				b = Utils.MD5.MD5_FF(b, c, d, a, x[i+ 7], 22, -45705983);
				a = Utils.MD5.MD5_FF(a, b, c, d, x[i+ 8], 7 ,  1770035416);
				d = Utils.MD5.MD5_FF(d, a, b, c, x[i+ 9], 12, -1958414417);
				c = Utils.MD5.MD5_FF(c, d, a, b, x[i+10], 17, -42063);
				b = Utils.MD5.MD5_FF(b, c, d, a, x[i+11], 22, -1990404162);
				a = Utils.MD5.MD5_FF(a, b, c, d, x[i+12], 7 ,  1804603682);
				d = Utils.MD5.MD5_FF(d, a, b, c, x[i+13], 12, -40341101);
				c = Utils.MD5.MD5_FF(c, d, a, b, x[i+14], 17, -1502002290);
				b = Utils.MD5.MD5_FF(b, c, d, a, x[i+15], 22,  1236535329);
				
				a = Utils.MD5.MD5_GG(a, b, c, d, x[i+ 1], 5 , -165796510);
				d = Utils.MD5.MD5_GG(d, a, b, c, x[i+ 6], 9 , -1069501632);
				c = Utils.MD5.MD5_GG(c, d, a, b, x[i+11], 14,  643717713);
				b = Utils.MD5.MD5_GG(b, c, d, a, x[i+ 0], 20, -373897302);
				a = Utils.MD5.MD5_GG(a, b, c, d, x[i+ 5], 5 , -701558691);
				d = Utils.MD5.MD5_GG(d, a, b, c, x[i+10], 9 ,  38016083);
				c = Utils.MD5.MD5_GG(c, d, a, b, x[i+15], 14, -660478335);
				b = Utils.MD5.MD5_GG(b, c, d, a, x[i+ 4], 20, -405537848);
				a = Utils.MD5.MD5_GG(a, b, c, d, x[i+ 9], 5 ,  568446438);
				d = Utils.MD5.MD5_GG(d, a, b, c, x[i+14], 9 , -1019803690);
				c = Utils.MD5.MD5_GG(c, d, a, b, x[i+ 3], 14, -187363961);
				b = Utils.MD5.MD5_GG(b, c, d, a, x[i+ 8], 20,  1163531501);
				a = Utils.MD5.MD5_GG(a, b, c, d, x[i+13], 5 , -1444681467);
				d = Utils.MD5.MD5_GG(d, a, b, c, x[i+ 2], 9 , -51403784);
				c = Utils.MD5.MD5_GG(c, d, a, b, x[i+ 7], 14,  1735328473);
				b = Utils.MD5.MD5_GG(b, c, d, a, x[i+12], 20, -1926607734);
				
				a = Utils.MD5.MD5_HH(a, b, c, d, x[i+ 5], 4 , -378558);
				d = Utils.MD5.MD5_HH(d, a, b, c, x[i+ 8], 11, -2022574463);
				c = Utils.MD5.MD5_HH(c, d, a, b, x[i+11], 16,  1839030562);
				b = Utils.MD5.MD5_HH(b, c, d, a, x[i+14], 23, -35309556);
				a = Utils.MD5.MD5_HH(a, b, c, d, x[i+ 1], 4 , -1530992060);
				d = Utils.MD5.MD5_HH(d, a, b, c, x[i+ 4], 11,  1272893353);
				c = Utils.MD5.MD5_HH(c, d, a, b, x[i+ 7], 16, -155497632);
				b = Utils.MD5.MD5_HH(b, c, d, a, x[i+10], 23, -1094730640);
				a = Utils.MD5.MD5_HH(a, b, c, d, x[i+13], 4 ,  681279174);
				d = Utils.MD5.MD5_HH(d, a, b, c, x[i+ 0], 11, -358537222);
				c = Utils.MD5.MD5_HH(c, d, a, b, x[i+ 3], 16, -722521979);
				b = Utils.MD5.MD5_HH(b, c, d, a, x[i+ 6], 23,  76029189);
				a = Utils.MD5.MD5_HH(a, b, c, d, x[i+ 9], 4 , -640364487);
				d = Utils.MD5.MD5_HH(d, a, b, c, x[i+12], 11, -421815835);
				c = Utils.MD5.MD5_HH(c, d, a, b, x[i+15], 16,  530742520);
				b = Utils.MD5.MD5_HH(b, c, d, a, x[i+ 2], 23, -995338651);
				
				a = Utils.MD5.MD5_II(a, b, c, d, x[i+ 0], 6 , -198630844);
				d = Utils.MD5.MD5_II(d, a, b, c, x[i+ 7], 10,  1126891415);
				c = Utils.MD5.MD5_II(c, d, a, b, x[i+14], 15, -1416354905);
				b = Utils.MD5.MD5_II(b, c, d, a, x[i+ 5], 21, -57434055);
				a = Utils.MD5.MD5_II(a, b, c, d, x[i+12], 6 ,  1700485571);
				d = Utils.MD5.MD5_II(d, a, b, c, x[i+ 3], 10, -1894986606);
				c = Utils.MD5.MD5_II(c, d, a, b, x[i+10], 15, -1051523);
				b = Utils.MD5.MD5_II(b, c, d, a, x[i+ 1], 21, -2054922799);
				a = Utils.MD5.MD5_II(a, b, c, d, x[i+ 8], 6 ,  1873313359);
				d = Utils.MD5.MD5_II(d, a, b, c, x[i+15], 10, -30611744);
				c = Utils.MD5.MD5_II(c, d, a, b, x[i+ 6], 15, -1560198380);
				b = Utils.MD5.MD5_II(b, c, d, a, x[i+13], 21,  1309151649);
				a = Utils.MD5.MD5_II(a, b, c, d, x[i+ 4], 6 , -145523070);
				d = Utils.MD5.MD5_II(d, a, b, c, x[i+11], 10, -1120210379);
				c = Utils.MD5.MD5_II(c, d, a, b, x[i+ 2], 15,  718787259);
				b = Utils.MD5.MD5_II(b, c, d, a, x[i+ 9], 21, -343485551);
				
				a = Utils.MD5.Safe_Add(a, olda);
				b = Utils.MD5.Safe_Add(b, oldb);
				c = Utils.MD5.Safe_Add(c, oldc);
				d = Utils.MD5.Safe_Add(d, oldd);
			}
			return Array(a, b, c, d);
		},
		
		/*
		 * These functions implement the four basic operations the algorithm uses.
		 */
		MD5_CMN:function(q, a, b, x, s, t)
		{
		  return Utils.MD5.Safe_Add(Utils.MD5.Bit_Rol(Utils.MD5.Safe_Add(Utils.MD5.Safe_Add(a, q), Utils.MD5.Safe_Add(x, t)), s),b);
		},
		MD5_FF:function(a, b, c, d, x, s, t)
		{
		  return Utils.MD5.MD5_CMN((b & c) | ((~b) & d), a, b, x, s, t);
		},
		MD5_GG:function(a, b, c, d, x, s, t)
		{
		  return Utils.MD5.MD5_CMN((b & d) | (c & (~d)), a, b, x, s, t);
		},
		MD5_HH:function(a, b, c, d, x, s, t)
		{
		  return Utils.MD5.MD5_CMN(b ^ c ^ d, a, b, x, s, t);
		},
		MD5_II:function(a, b, c, d, x, s, t)
		{
		  return Utils.MD5.MD5_CMN(c ^ (b | (~d)), a, b, x, s, t);
		},
		
		/*
		 * Calculate the HMAC-MD5, of a key and some data
		 */
		Core_HMac_MD5:function(key, data)
		{
		  var bkey = Utils.MD5.StrToBinl(key);
		  if(bkey.length > 16) bkey = Utils.MD5.Core_MD5(bkey, key.length * Utils.MD5.chrsz);
		
		  var ipad = Array(16), opad = Array(16);
		  for(var i = 0; i < 16; i++)
		  {
			ipad[i] = bkey[i] ^ 0x36363636;
			opad[i] = bkey[i] ^ 0x5C5C5C5C;
		  }
		
		  var hash = Utils.MD5.Core_MD5(ipad.concat(Utils.MD5.StrToBinl(data)), 512 + data.length * Utils.MD5.chrsz);
		  return Utils.MD5.Core_MD5(opad.concat(hash), 512 + 128);
		},
		
		/*
		 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
		 * to work around bugs in some JS interpreters.
		 */
		Safe_Add:function(x, y)
		{
		  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
		  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
		  return (msw << 16) | (lsw & 0xFFFF);
		},
		
		/*
		 * Bitwise rotate a 32-bit number to the left.
		 */
		Bit_Rol:function(num, cnt)
		{
		  return (num << cnt) | (num >>> (32 - cnt));
		},
		
	
		/*
		 * Convert a string to an array of little-endian words
		 * If Utils.MD5.chrsz is ASCII, characters >255 have their hi-byte silently ignored.
		 */
		StrToBinl:function(str)
		{
		  var bin = Array();
		  var mask = (1 << Utils.MD5.chrsz) - 1;
		  for(var i = 0,max = str.length * Utils.MD5.chrsz; i < max; i += Utils.MD5.chrsz)
			bin[i>>5] |= (str.charCodeAt(i / Utils.MD5.chrsz) & mask) << (i%32);
		  return bin;
		},
		
		/*
		 * Convert an array of little-endian words to a string
		 */
		BinlToStr:function(bin)
		{
		  var str = "";
		  var mask = (1 << Utils.MD5.chrsz) - 1;
		  for(var i = 0,max = bin.length * 32; i < max; i += Utils.MD5.chrsz)
			str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
		  return str;
		},
		
		/*
		 * Convert an array of little-endian words to a hex string.
		 */
		BinlToHex:function(binarray)
		{
		  var hex_tab = Utils.MD5.hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
		  var str = "";
		  for(var i = 0; i < binarray.length * 4; i++)
		  {
			str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
				   hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
		  }
		  return str;
		},
	
		/*
		 * Convert an array of little-endian words to a base-64 string
		 */
		BinlToB64:function(binarray)
		{
		  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		  var str = "";
		  for(var i = 0; i < binarray.length * 4; i += 3)
		  {
			var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
						| (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
						|  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
			for(var j = 0; j < 4; j++)
			{
			  if(i * 8 + j * 6 > binarray.length * 32) str += Utils.MD5.b64pad;
			  else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
			}
		  }
		  return str;
		},
	
		end:true
	},

	end: true	
};


//========================================================================
// 	- MD5 code ---
// ========================================================================
var MD5 = {
	hexcase : 0,  /* hex output format. 0 - lowercase; 1 - uppercase        */
	b64pad  : "", /* base-64 pad character. "=" for strict RFC compliance   */
	chrsz   : 8,  /* bits per input character. 8 - ASCII; 16 - Unicode      */
	
	Hex_MD5:function (s)
	{
		return MD5.BinlToHex(MD5.Core_MD5(MD5.StrToBinl(s), s.length * MD5.chrsz));
	},
	
	B64_MD5:function(s){ return MD5.BinlToB64(MD5.Core_MD5(MD5.StrToBinl(s), s.length * MD5.chrsz));},
	Str_MD5:function(s){ return MD5.BinlToStr(MD5.Core_MD5(MD5.StrToBinl(s), s.length * MD5.chrsz));},
	Hex_HMac_MD5:function(key, data) { return MD5.BinlToHex(MD5.Core_HMac_MD5(key, data)); },
	B64_HMac_MD5:function(key, data) { return MD5.BinlToB64(MD5.core_HMac_MD5(key, data)); },
	Str_HMac_MD5:function(key, data) { return MD5.BinlToStr(MD5.core_HMac_MD5(key, data)); },
	
	/*
	 * Perform a simple self-test to see if the VM is working
	 */
	MD5_VM_Test:function()
	{
	  return MD5.Hex_MD5("abc") + "==900150983cd24fb0d6963f7d28e17f72";
	},

	/*
	 * Calculate the MD5 of an array of little-endian words, and a bit length
	 */
	Core_MD5:function(x, len)
	{
	  /* append padding */
		x[len >> 5] |= 0x80 << ((len) % 32);
		x[(((len + 64) >>> 9) << 4) + 14] = len;
		
		var a =  1732584193;
		var b = -271733879;
		var c = -1732584194;
		var d =  271733878;
		
		for(var i = 0,max = x.length; i < max; i += 16)
		{
			var olda = a;
			var oldb = b;
			var oldc = c;
			var oldd = d;
			
			a = MD5.MD5_FF(a, b, c, d, x[i+ 0], 7 , -680876936);
			d = MD5.MD5_FF(d, a, b, c, x[i+ 1], 12, -389564586);
			c = MD5.MD5_FF(c, d, a, b, x[i+ 2], 17,  606105819);
			b = MD5.MD5_FF(b, c, d, a, x[i+ 3], 22, -1044525330);
			a = MD5.MD5_FF(a, b, c, d, x[i+ 4], 7 , -176418897);
			d = MD5.MD5_FF(d, a, b, c, x[i+ 5], 12,  1200080426);
			c = MD5.MD5_FF(c, d, a, b, x[i+ 6], 17, -1473231341);
			b = MD5.MD5_FF(b, c, d, a, x[i+ 7], 22, -45705983);
			a = MD5.MD5_FF(a, b, c, d, x[i+ 8], 7 ,  1770035416);
			d = MD5.MD5_FF(d, a, b, c, x[i+ 9], 12, -1958414417);
			c = MD5.MD5_FF(c, d, a, b, x[i+10], 17, -42063);
			b = MD5.MD5_FF(b, c, d, a, x[i+11], 22, -1990404162);
			a = MD5.MD5_FF(a, b, c, d, x[i+12], 7 ,  1804603682);
			d = MD5.MD5_FF(d, a, b, c, x[i+13], 12, -40341101);
			c = MD5.MD5_FF(c, d, a, b, x[i+14], 17, -1502002290);
			b = MD5.MD5_FF(b, c, d, a, x[i+15], 22,  1236535329);
			
			a = MD5.MD5_GG(a, b, c, d, x[i+ 1], 5 , -165796510);
			d = MD5.MD5_GG(d, a, b, c, x[i+ 6], 9 , -1069501632);
			c = MD5.MD5_GG(c, d, a, b, x[i+11], 14,  643717713);
			b = MD5.MD5_GG(b, c, d, a, x[i+ 0], 20, -373897302);
			a = MD5.MD5_GG(a, b, c, d, x[i+ 5], 5 , -701558691);
			d = MD5.MD5_GG(d, a, b, c, x[i+10], 9 ,  38016083);
			c = MD5.MD5_GG(c, d, a, b, x[i+15], 14, -660478335);
			b = MD5.MD5_GG(b, c, d, a, x[i+ 4], 20, -405537848);
			a = MD5.MD5_GG(a, b, c, d, x[i+ 9], 5 ,  568446438);
			d = MD5.MD5_GG(d, a, b, c, x[i+14], 9 , -1019803690);
			c = MD5.MD5_GG(c, d, a, b, x[i+ 3], 14, -187363961);
			b = MD5.MD5_GG(b, c, d, a, x[i+ 8], 20,  1163531501);
			a = MD5.MD5_GG(a, b, c, d, x[i+13], 5 , -1444681467);
			d = MD5.MD5_GG(d, a, b, c, x[i+ 2], 9 , -51403784);
			c = MD5.MD5_GG(c, d, a, b, x[i+ 7], 14,  1735328473);
			b = MD5.MD5_GG(b, c, d, a, x[i+12], 20, -1926607734);
			
			a = MD5.MD5_HH(a, b, c, d, x[i+ 5], 4 , -378558);
			d = MD5.MD5_HH(d, a, b, c, x[i+ 8], 11, -2022574463);
			c = MD5.MD5_HH(c, d, a, b, x[i+11], 16,  1839030562);
			b = MD5.MD5_HH(b, c, d, a, x[i+14], 23, -35309556);
			a = MD5.MD5_HH(a, b, c, d, x[i+ 1], 4 , -1530992060);
			d = MD5.MD5_HH(d, a, b, c, x[i+ 4], 11,  1272893353);
			c = MD5.MD5_HH(c, d, a, b, x[i+ 7], 16, -155497632);
			b = MD5.MD5_HH(b, c, d, a, x[i+10], 23, -1094730640);
			a = MD5.MD5_HH(a, b, c, d, x[i+13], 4 ,  681279174);
			d = MD5.MD5_HH(d, a, b, c, x[i+ 0], 11, -358537222);
			c = MD5.MD5_HH(c, d, a, b, x[i+ 3], 16, -722521979);
			b = MD5.MD5_HH(b, c, d, a, x[i+ 6], 23,  76029189);
			a = MD5.MD5_HH(a, b, c, d, x[i+ 9], 4 , -640364487);
			d = MD5.MD5_HH(d, a, b, c, x[i+12], 11, -421815835);
			c = MD5.MD5_HH(c, d, a, b, x[i+15], 16,  530742520);
			b = MD5.MD5_HH(b, c, d, a, x[i+ 2], 23, -995338651);
			
			a = MD5.MD5_II(a, b, c, d, x[i+ 0], 6 , -198630844);
			d = MD5.MD5_II(d, a, b, c, x[i+ 7], 10,  1126891415);
			c = MD5.MD5_II(c, d, a, b, x[i+14], 15, -1416354905);
			b = MD5.MD5_II(b, c, d, a, x[i+ 5], 21, -57434055);
			a = MD5.MD5_II(a, b, c, d, x[i+12], 6 ,  1700485571);
			d = MD5.MD5_II(d, a, b, c, x[i+ 3], 10, -1894986606);
			c = MD5.MD5_II(c, d, a, b, x[i+10], 15, -1051523);
			b = MD5.MD5_II(b, c, d, a, x[i+ 1], 21, -2054922799);
			a = MD5.MD5_II(a, b, c, d, x[i+ 8], 6 ,  1873313359);
			d = MD5.MD5_II(d, a, b, c, x[i+15], 10, -30611744);
			c = MD5.MD5_II(c, d, a, b, x[i+ 6], 15, -1560198380);
			b = MD5.MD5_II(b, c, d, a, x[i+13], 21,  1309151649);
			a = MD5.MD5_II(a, b, c, d, x[i+ 4], 6 , -145523070);
			d = MD5.MD5_II(d, a, b, c, x[i+11], 10, -1120210379);
			c = MD5.MD5_II(c, d, a, b, x[i+ 2], 15,  718787259);
			b = MD5.MD5_II(b, c, d, a, x[i+ 9], 21, -343485551);
			
			a = MD5.Safe_Add(a, olda);
			b = MD5.Safe_Add(b, oldb);
			c = MD5.Safe_Add(c, oldc);
			d = MD5.Safe_Add(d, oldd);
		}
		return Array(a, b, c, d);
	},
	
	/*
	 * These functions implement the four basic operations the algorithm uses.
	 */
	MD5_CMN:function(q, a, b, x, s, t)
	{
	  return MD5.Safe_Add(MD5.Bit_Rol(MD5.Safe_Add(MD5.Safe_Add(a, q), MD5.Safe_Add(x, t)), s),b);
	},
	MD5_FF:function(a, b, c, d, x, s, t)
	{
	  return MD5.MD5_CMN((b & c) | ((~b) & d), a, b, x, s, t);
	},
	MD5_GG:function(a, b, c, d, x, s, t)
	{
	  return MD5.MD5_CMN((b & d) | (c & (~d)), a, b, x, s, t);
	},
	MD5_HH:function(a, b, c, d, x, s, t)
	{
	  return MD5.MD5_CMN(b ^ c ^ d, a, b, x, s, t);
	},
	MD5_II:function(a, b, c, d, x, s, t)
	{
	  return MD5.MD5_CMN(c ^ (b | (~d)), a, b, x, s, t);
	},
	
	/*
	 * Calculate the HMAC-MD5, of a key and some data
	 */
	Core_HMac_MD5:function(key, data)
	{
	  var bkey = MD5.StrToBinl(key);
	  if(bkey.length > 16) bkey = MD5.Core_MD5(bkey, key.length * MD5.chrsz);
	
	  var ipad = Array(16), opad = Array(16);
	  for(var i = 0; i < 16; i++)
	  {
		ipad[i] = bkey[i] ^ 0x36363636;
		opad[i] = bkey[i] ^ 0x5C5C5C5C;
	  }
	
	  var hash = MD5.Core_MD5(ipad.concat(MD5.StrToBinl(data)), 512 + data.length * MD5.chrsz);
	  return MD5.Core_MD5(opad.concat(hash), 512 + 128);
	},
	
	/*
	 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
	 * to work around bugs in some JS interpreters.
	 */
	Safe_Add:function(x, y)
	{
	  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
	  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
	  return (msw << 16) | (lsw & 0xFFFF);
	},
	
	/*
	 * Bitwise rotate a 32-bit number to the left.
	 */
	Bit_Rol:function(num, cnt)
	{
	  return (num << cnt) | (num >>> (32 - cnt));
	},
	

	/*
	 * Convert a string to an array of little-endian words
	 * If MD5.chrsz is ASCII, characters >255 have their hi-byte silently ignored.
	 */
	StrToBinl:function(str)
	{
	  var bin = Array();
	  var mask = (1 << MD5.chrsz) - 1;
	  for(var i = 0,max = str.length * MD5.chrsz; i < max; i += MD5.chrsz)
		bin[i>>5] |= (str.charCodeAt(i / MD5.chrsz) & mask) << (i%32);
	  return bin;
	},
	
	/*
	 * Convert an array of little-endian words to a string
	 */
	BinlToStr:function(bin)
	{
	  var str = "";
	  var mask = (1 << MD5.chrsz) - 1;
	  for(var i = 0,max = bin.length * 32; i < max; i += MD5.chrsz)
		str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
	  return str;
	},
	
	/*
	 * Convert an array of little-endian words to a hex string.
	 */
	BinlToHex:function(binarray)
	{
	  var hex_tab = MD5.hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
	  var str = "";
	  for(var i = 0; i < binarray.length * 4; i++)
	  {
		str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
			   hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
	  }
	  return str;
	},

	/*
	 * Convert an array of little-endian words to a base-64 string
	 */
	BinlToB64:function(binarray)
	{
	  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	  var str = "";
	  for(var i = 0; i < binarray.length * 4; i += 3)
	  {
		var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
					| (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
					|  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
		for(var j = 0; j < 4; j++)
		{
		  if(i * 8 + j * 6 > binarray.length * 32) str += MD5.b64pad;
		  else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
		}
	  }
	  return str;
	},

	end:true
};
