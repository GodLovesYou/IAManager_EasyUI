jQuery.Hash = function(){
	
    this.items = new Array();
    
    this.itemsCount = 0;
    /**
	 * 向Hashtable中添加数据
	 */
    this.set = function (key, value) {
        if (!this.containsKey(key)) {
            this.items[key] = value;
            this.itemsCount++;
        }
        else {
            this.items[key] = value;
        }
    };
    /**
	 * 从Hashtable中获取指定的Key的值
	 */
    this.get = function (key) {
        if (this.containsKey(key))
            return this.items[key];
        else
            return null;
    };
    /**
	 * 从HshMap中删除Key
	 */
    this.remove = function (key) {
        if (this.containsKey(key)) {
            delete this.items[key];
            this.itemsCount--;
        }
        else
            throw "key '" + key + "' does not exists.";

    };
    /**
	 * Hashtable中是否包含指定的Key
	 */
    this.containsKey = function (key) {
        return typeof (this.items[key]) != "undefined";
    };
    /**
	 * Hashtable中是否包含指定的value
	 */
    this.containsValue = function (value) {
        for (var item in this.items) {
            if (this.items[item] == value)
                return true;
        }

        return false;
    };
    /**
	 * Hashtable中是否包含指定的Key或者value
	 */
    this.contains = function (keyOrValue) {
        return this.containsKey(keyOrValue) || this.containsValue(keyOrValue);
    };
    
    /** 
    * 获得Map中的所有Value 
    */  
    this.values = function(){  
        var _values= new Array();  
        for(var key in this.items){  
            _values.push(this.items[key]);  
        }  
        return _values;  
    };  
    
    /** 
    * 获得Map中的所有Key 
    */  
    this.keys = function(){  
        var _keys = new Array();  
        for(var key in this.items){  
            _keys.push(key);  
        }  
        return _keys;  
    };  
    
    /** 
    * 遍历Hash
    */  
    this.each = function(iterator, content){  
        var iterator = iterator || function () {};
        var content = content || this;
        var i = 0;
        for (var prop in this.items) {
            var item = {
                key : prop, value : this.items[prop] 
            };
            if (iterator.call(content, item, i++)) {
                break;	
            }
        }
        i = 0;
    };
    
    /**
	 * 清空Hashtable
	 */
    this.clear = function () {
        this.items = new Array();
        itemsCount = 0;
    };
    /**
	 * 获取Hashtable的大小
	 */
    this.size = function () {
        return this.itemsCount;
    };
    /**
	 * Hashtable中是否为空
	 */
    this.isEmpty = function () {
        return this.size() == 0;
    };
    
};