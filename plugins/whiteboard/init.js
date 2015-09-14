<?php
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR."en.php");

		if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php")) {
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php");
		}

		foreach ($whiteboard_language as $i => $l) {
			$whiteboard_language[$i] = str_replace("'", "\'", $l);
		}
?>

/*
 * CometChat
 * Copyright (c) 2014 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){

	$.ccwhiteboard = (function () {

		var title = '<?php echo $whiteboard_language[0];?>';
		var lastcall = 0;
		var height = <?php echo $whitebHeight;?>;
		var width = <?php echo $whitebWidth;?>;

        return {

			getTitle: function() {
				return title;
			},

			init: function (params) {
				var id = params.to;
				var chatroommode = params.chatroommode;
				var windowMode = 0;
				if(typeof(params.windowMode) == "undefined") {
					windowMode = 0;
				} else {
					windowMode = 1;
				}
				if(chatroommode == 1){
					var currenttime = new Date();
					currenttime = parseInt(currenttime.getTime()/1000);
					if (currenttime-lastcall > 10) {
						baseUrl = $.cometchat.getBaseUrl();
						basedata = $.cometchat.getBaseData();

						var random = currenttime;
						loadCCPopup(baseUrl+'plugins/whiteboard/index.php?action=whiteboard&chatroommode=1&subaction=request&id='+id+'&basedata='+basedata, 'whiteboard',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0,width=<?php echo $whitebWidth;?>,height=<?php echo $whitebHeight;?>",width,height-50,'<?php echo $whiteboard_language[9];?>',1,1,1,1,windowMode);
					} else {
						alert('<?php echo $whiteboard_language[1];?>');
					}

				} else {
					var currenttime = new Date();
					currenttime = parseInt(currenttime.getTime()/1000);
					if (currenttime-lastcall > 10) {
						baseUrl = $.cometchat.getBaseUrl();
						baseData = $.cometchat.getBaseData();

						var random = currenttime;
						$.getJSON(baseUrl+'plugins/whiteboard/index.php?action=request&callback=?', {to: id, id: random, basedata: baseData});
						lastcall = currenttime;

						loadCCPopup(baseUrl+'plugins/whiteboard/index.php?action=whiteboard&id='+id+'&basedata='+baseData, 'whiteboard',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width="+width+",height="+height,width,height-50,'<?php echo $whiteboard_language[9];?>',0,1,1,1,windowMode);
						if (jqcc.cometchat.getThemeArray('buddylistIsDevice',id) == 1) {
							jqcc.ccmobilenativeapp.sendnotification('<?php echo $whiteboard_language[5];?>', id, jqcc.cometchat.getName(jqcc.cometchat.getThemeVariable('userid')));
						}

					} else {
						alert('<?php echo $whiteboard_language[1];?>');
					}
				}
			},

			accept: function (params) {
				var id = params.to;
				var random = params.random;
				var chatroommode = params.chatroommode;
				var windowMode = 0;
				if(typeof(params.windowMode) == "undefined") {
					windowMode = 0;
				} else {
					windowMode = 1;
				}
				if(chatroommode == 1) {
					baseUrl = $.cometchat.getBaseUrl();
					basedata = $.cometchat.getBaseData();
					var controlparameters = {"type":"plugins", "name":"core", "method":"loadCCPopup", "params":{"url": baseUrl+'plugins/whiteboard/index.php?action=whiteboard&chatroommode=1&id='+id+'&basedata='+basedata, "name":"whiteboard", "properties":"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $whitebWidth;?>,height=<?php echo $whitebHeight;?>", "width":width, "height":height-50, "title":'<?php echo $whiteboard_language[9];?>', "force":"1", "allowmaximize":"1", "allowresize":"1", "allowpopout":"1", "windowMode":windowMode}};
                    controlparameters = JSON.stringify(controlparameters);
                    parent.postMessage('CC^CONTROL_'+controlparameters,'*');
				} else {
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();
					$.getJSON(baseUrl+'plugins/whiteboard/index.php?action=accept&callback=?', {to: id, basedata: baseData});
					loadCCPopup(baseUrl+'plugins/whiteboard/index.php?action=whiteboard&id='+id+'&basedata='+baseData, 'whiteboard',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width="+width+",height="+height,width,height-50,'<?php echo $whiteboard_language[9];?>',0,1,1,1,windowMode);

				}
			}
        };
    })();

})(jqcc);

jqcc(document).ready(function(){
	jqcc('.accept_White').live('click',function(){
		var to = jqcc(this).attr('to');
		var random = jqcc(this).attr('random');
		var chatroommode = jqcc(this).attr('chatroommode');
		if(typeof(parent) != 'undefined' && parent != null && parent != self){
			var controlparameters = {"type":"plugins", "name":"ccwhiteboard", "method":"accept", "params":{"to":to, "random":random, "chatroommode":chatroommode}};
			controlparameters = JSON.stringify(controlparameters);
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		} else {
			var controlparameters = {"to":to, "random":random, "chatroommode":chatroommode};
            jqcc.ccwhiteboard.accept(controlparameters);
		}
	});
});