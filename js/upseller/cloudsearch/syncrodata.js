var cloudsearchSynchronization = Class.create();
cloudsearchSynchronization.prototype = {


	initialize: function(options) {
	 	this.ajax(options,options.initurl);
	  	
	},
	ajax : function(options,url){
		//alert(this.options.ajaxurl);
		//$('loadingmigration').show();
		new Ajax.Request(url, {
		  method: 'post',
		  parameters: options.fromdata,
		  onSuccess: function(transport){
			var json = transport.responseText.evalJSON();
			//console.log(json);
			cloudsearchSynchronization.prototype.resposnceFunction(json,options);
		  },
		  onFailure : function(transport){
		  	
			var json = transport.responseText.evalJSON();
			//console.log(json);
			
		  }
		});	
		  
	},
	resposnceFunction : function(json,options){
		document.getElementById("syncronizationdata").innerHTML=json.loading_html;
		if(json.error==false){
			if(json.finish==false){
				cloudsearchSynchronization.prototype.ajax(options,options.continueurl);  
			}else{
				//alert("finish");
			}
		}else{
			alert(json.error_message);
		}
	  
	  	//$('loadingmigration').hide();
	  
	}
};
