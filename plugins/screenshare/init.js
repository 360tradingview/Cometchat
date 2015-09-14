<?php
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR."en.php");

		if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php")) {
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php");
		}

		foreach ($screenshare_language as $i => $l) {
			$screenshare_language[$i] = str_replace("'", "\'", $l);
		}
?>

/*
 * CometChat
 * Copyright (c) 2014 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){

	$.ccscreenshare = (function () {

		var title = '<?php echo $screenshare_language[0];?>';
		var lastcall = 0;
		var height = <?php echo $scrHeight;?>;
		var width = <?php echo $scrWidth;?>;
		var type = '<?php echo $screensharePluginType;?>';

        return {

			getTitle: function() {
				return title;
			},

			init: function (params) {
				var id = params.to;
				var chatroommode = params.chatroommode;
				var windowMode = 0;
				var currenttime = new Date();
				if(typeof(params.windowMode) == "undefined") {
					windowMode = 0;
				} else {
					windowMode = 1;
				}
				currenttime = parseInt(currenttime.getTime()/1000);
				if (currenttime-lastcall > 10) {
					var random = currenttime;
					if(chatroommode == 1) {
						baseUrl = $.cometchat.getBaseUrl();
						baseData = $.cometchat.getBaseData();
						if (type == '2') {
							$.ajax({
								url : baseUrl+'plugins/screenshare/index.php?chatroommode=1&action=request&callback=?',
								type : 'GET',
								data : {to: id, id: random, basedata: baseData},
								dataType : 'text',
								success : function(data) {
									var flag = data.split('^');
									if (flag[0] == '1'){
										alert('<?php echo $screenshare_language[12];?> '+flag[1]);
									}
								},
								error : function(data) {
								}
							});
						} else {
							$.getJSON(baseUrl+'plugins/screenshare/index.php?chatroommode=1&action=request&callback=?', {to: id, id: random, basedata: baseData});
						}
					} else {
						baseUrl = $.cometchat.getBaseUrl();
						baseData = $.cometchat.getBaseData();
						$.getJSON(baseUrl+'plugins/screenshare/index.php?action=request&callback=?', {to: id, id: random, basedata: baseData});
						if (jqcc.cometchat.getThemeArray('buddylistIsDevice',id) == 1) {
							jqcc.ccmobilenativeapp.sendnotification('<?php echo $screenshare_language[2];?>', id, jqcc.cometchat.getName(jqcc.cometchat.getThemeVariable('userid')));
						}
					}
					lastcall = currenttime;
					if(type=='1') {
						var w = window.open (baseUrl+'plugins/screenshare/index.php?action=screenshare&type=1&id='+random+'&basedata='+baseData, 'screenshare',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width=825,height=350");
						w.focus();
					} else if(type =='0') {
						if(chatroommode == 1){
							loadCCPopup(baseUrl+'plugins/screenshare/index.php?action=screenshare&type=1&id='+random+'&basedata='+baseData, 'screenshare',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width=430,height=100",430,100,'<?php echo $screenshare_language[7];?>',0,1,1,1,windowMode);
						} else {
							loadCCPopup(baseUrl+'plugins/screenshare/index.php?action=screenshare&type=1&id='+random+'&basedata='+baseData, 'screenshare',"status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=0, width=430,height=100",430,100,'<?php echo $screenshare_language[7];?>',0,1,1,1,windowMode);
						}
					}


				} else {
					alert('<?php echo $screenshare_language[1];?>');
				}
			},

			accept: function (params) {
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				id = params.to;
				random = params.grp;
				join_url = params.join_url;
				start_url = params.start_url;
				mode = params.chatroommode;
				windowMode = 0;
				if(typeof(params.windowMode) == "undefined") {
					windowMode = 0;
				} else {
					windowMode = 1;
				}
				if(mode == 1) {
					if (type == '2') {
						$.ajax({
							url : baseUrl+'plugins/screenshare/index.php?chatroommode=1&action=accept&callback=?',
							type : 'GET',
							data : {to: id, start_url:start_url, grp: random, basedata: baseData},
							dataType : 'text',
							success : function(data) {
								var flag = data.split('^');
								if (flag[0] == '1'){
									alert('<?php echo $screenshare_language[12];?> '+flag[1]);
								}
							},
							error : function(data) {
							}
						});
					} else {
						$.getJSON(baseUrl+'plugins/screenshare/index.php?chatroommode=1&action=accept&callback=?', {to: id, start_url:start_url, grp: random, basedata: baseData});
					}
				} else {
					$.getJSON(baseUrl+'plugins/screenshare/index.php?action=accept&callback=?', {to: id, start_url:start_url, grp: random, basedata: baseData});
				}
				if(type == '2') {
					window.open(join_url, 'screenshare','width=<?php echo $scrWidth;?>,height=<?php echo $scrHeight;?>,scrollbars=yes, resizable=yes');
				} else{
					if(mode == 1){
						var controlparameters = {"type":"plugins", "name":"core", "method":"loadCCPopup", "params":{"url": baseUrl+'plugins/screenshare/index.php?action=screenshare&type=0&id='+random+'&basedata='+baseData, "name":"screenshare", "properties":"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width="+width+",height="+height, "width":width, "height":height, "title":'<?php echo $screenshare_language[7];?>', "force":"0", "allowmaximize":"1", "allowresize":"1", "allowpopout":"1", "windowMode":windowMode}};
	                    controlparameters = JSON.stringify(controlparameters);
	                    parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					} else {
						loadCCPopup(baseUrl+'plugins/screenshare/index.php?action=screenshare&type=0&id='+random+'&basedata='+baseData, 'screenshare',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width="+width+",height="+height,width,height,'<?php echo $screenshare_language[7];?>',0,1,1,1,windowMode);
					}
				}
			},

			accept_fid: function (params) {
				id = params.to;
				start_url = params.start_url;
				chatroommode = params.chatroommode;
				windowMode = 0;
				if(typeof(params.windowMode) == "undefined") {
					windowMode = 0;
				} else {
					windowMode = 1;
				}

				if (type == '2') {
					window.open(start_url, 'screenshare','width=<?php echo $scrWidth;?>,height=<?php echo $scrHeight;?>,scrollbars=yes, resizable=yes');
				} else {
					if(chatroommode == 1){
						var controlparameters = {"type":"plugins", "name":"core", "method":"loadCCPopup", "params":{"url": start_url, "name":"screenshare", "properties":"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $scrWidth;?>,height=<?php echo $scrHeight;?>", "width":width, "height":height, "title":'<?php echo $screenshare_language[7];?>', "force":"0", "allowmaximize":"1", "allowresize":"1", "allowpopout":"1", "windowMode":windowMode}};
	                    controlparameters = JSON.stringify(controlparameters);
	                    parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					} else {
						loadCCPopup(start_url,'screenshare',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $scrWidth;?>,height=<?php echo $scrHeight;?>",width,height,'<?php echo $screenshare_language[7];?>',0,1,1,1,windowMode);
					}
				}
			}
        };
    })();

})(jqcc);

jqcc(document).ready(function(){
	jqcc('.acceptSceenshare').live('click',function(){
		var to = jqcc(this).attr('to');
		var grp = jqcc(this).attr('grp');
		var join_url = jqcc(this).attr('join_url');
		var start_url = jqcc(this).attr('start_url');
		var chatroommode = jqcc(this).attr('chatroommode');
		if(typeof(parent) != 'undefined' && parent != null && parent != self){
			var controlparameters = {"type":"plugins", "name":"ccscreenshare", "method":"accept", "params":{"to":to, "grp":grp, "join_url":join_url, "start_url":start_url, "chatroommode":chatroommode}};
			controlparameters = JSON.stringify(controlparameters);
			if(typeof(parent) != 'undefined' && parent != null && parent != self){
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			} else {
				window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
			}
		} else {
			var controlparameters = {"to":to, "grp":grp, "join_url":join_url, "start_url":start_url, "chatroommode":chatroommode};
            jqcc.ccscreenshare.accept(controlparameters);
		}
	});

	jqcc('.accept_fidSceenshare').live('click',function(){
		var to = jqcc(this).attr('to');
		var start_url = jqcc(this).attr('start_url');
		var chatroommode = jqcc(this).attr('chatroommode');
		if(typeof(parent) != 'undefined' && parent != null && parent != self){
			var controlparameters = {"type":"plugins", "name":"ccscreenshare", "method":"accept", "params":{"to":to, "start_url":start_url, "chatroommode":chatroommode}};
			controlparameters = JSON.stringify(controlparameters);
			if(typeof(parent) != 'undefined' && parent != null && parent != self){
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			} else {
				window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
			}
		} else {
			var controlparameters = {"to":to, "start_url":start_url, "chatroommode":chatroommode};
            jqcc.ccscreenshare.accept(controlparameters);
		}
	});
});