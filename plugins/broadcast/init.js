<?php

		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR."en.php");
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
		if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php")) {
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php");
		}

		foreach ($broadcast_language as $i => $l) {
			$broadcast_language[$i] = str_replace("'", "\'", $l);
		}

                $width = $camWidth;
		$height = $camHeight;

                if($videoPluginType == 2) {
                    $width = $vidWidth;
                    $height = $vidHeight;
                }
?>

/*
 * CometChat
 * Copyright (c) 2014 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/

(function($){

		$.ccbroadcast = (function () {
		var title = '<?php echo $broadcast_language[0];?>';
		var type = <?php echo $videoPluginType;?>;
		if(type == 2) {allowresize = 0} else {allowresize = 1}

		var lastcall = 0;

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
					if(chatroommode == 1) {
						baseUrl = $.cometchat.getBaseUrl();
						basedata = $.cometchat.getBaseData();
						loadCCPopup(baseUrl+'plugins/broadcast/index.php?action=call&chatroommode=1&broadcast=0&type=1&grp='+id+'&basedata='+basedata, 'broadcast',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $width;?>,height=<?php echo $height;?>",<?php echo $width;?>,<?php echo $height;?>,'<?php echo $broadcast_language[8];?>',1,1,allowresize,1,windowMode);
					} else {
						var random = '';
						var currenttime = new Date();
						currenttime = parseInt(currenttime.getTime()/1000);
						if (currenttime-lastcall > 10) {
							baseUrl = $.cometchat.getBaseUrl();
							baseData = $.cometchat.getBaseData();
							if (jqcc.cometchat.getThemeArray('buddylistIsDevice',id) == 1) {
								jqcc.ccmobilenativeapp.sendnotification('<?php echo $broadcast_language[5];?>', id, jqcc.cometchat.getName(jqcc.cometchat.getThemeVariable('userid')));
							}
					  		loadCCPopup(baseUrl+'plugins/broadcast/index.php?action=request&broadcast=0&type=1&to='+id+'&basedata='+baseData, 'broadcast',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $width;?>,height=<?php echo $height;?>",<?php echo $width;?>,<?php echo $height;?>,'<?php echo $broadcast_language[8];?>',1,1,allowresize,1,windowMode);

							lastcall = currenttime;
						} else {
							alert('<?php echo $broadcast_language[1];?>');
						}
					}
				},

				accept: function (params) {
					id = params.to;
					grp = params.grp;
					if(typeof(params.windowMode) == "undefined") {
						windowMode = 0;
					} else {
						windowMode = 1;
					}
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();
					loadCCPopup(baseUrl+'plugins/broadcast/index.php?action=call&broadcast=1&type=0&grp='+grp+'&basedata='+baseData, 'broadcast',"status=0,toolbar=0,menubar=0,directories=0,type=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $width;?>,height=<?php echo $height;?>",<?php echo $width;?>,<?php echo $height;?>,'<?php echo $broadcast_language[8];?>',1,1,allowresize,1,windowMode);
				},

				join: function (params) {
					id = params.grp;
					baseUrl = $.cometchat.getBaseUrl();
					basedata = $.cometchat.getBaseData();
					var windowMode = 0;
					if(typeof(params.windowMode) == "undefined") {
						windowMode = 0;
					} else {
						windowMode = 1;
					}
					loadCCPopup(baseUrl+'plugins/broadcast/index.php?action=call&broadcast=1&chatroommode=1&type=0&join=1&grp='+id+'&basedata='+basedata, 'broadcast',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $width;?>,height=<?php echo $height;?>",<?php echo $width;?>,<?php echo $height;?>,'<?php echo $broadcast_language[8];?>',1,1,allowresize,1,windowMode);
				},

				end_call : function(params){
					var id = params.to;
					var grp = params.grp;
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();
					if((jqcc.cometchat.getInternalVariable('endcallOnceWindow_'+grp) !== '1' && jqcc.cometchat.getInternalVariable('endcallOnce_'+grp) !== '1')) {
						var popoutopencalled = jqcc.cometchat.getInternalVariable('broadcastpopoutcalled');
						var endcallrecieved = jqcc.cometchat.getInternalVariable('endcallrecievedfrom_'+grp);
						if(popoutopencalled !== '1'){
							if(endcallrecieved !== '1') {
								$.ajax({
									url : baseUrl+'plugins/broadcast/index.php?action=endcall',
									type : 'GET',
									data : {to: id, basedata: baseData , grp: grp},
									dataType : 'jsonp',
									success : function(data) {

									},
									error : function(data) {
										console.log('Something went wrong');
									}
								});
							}
						}
					}
					jqcc.cometchat.setInternalVariable('endcallrecievedfrom_'+grp,'0');
					jqcc.cometchat.setInternalVariable('broadcastpopoutcalled','0');
				},

				inviteBroadcast: function(params) {
					var id = params.id;
					baseData = $.cometchat.getBaseData();
					baseUrl = $.cometchat.getBaseUrl();
					loadCCPopup(baseUrl + "plugins/broadcast/invite.php?action=invite&roomid="+ id +"&basedata="+ baseData ,"invitebroadcast","status=0,toolbar=0,menubar=0,directories=0,resizable=0,location=0,status=0,scrollbars=1, width=400,height=190",400,190,"<?php echo $broadcast_language[11];?>");
				},
				getLangVariables: function() {
					return <?php echo json_encode($broadcast_language); ?>;
				},
				processControlMessage : function(controlparameters) {
					var broadcast_language = jqcc.ccbroadcast.getLangVariables();
					var processedmessage = null;
					<?php if($videoPluginType == 3) : ?>

					switch(controlparameters.method){
						case 'endcall':
						jqcc.cometchat.setInternalVariable('endcallrecievedfrom_'+controlparameters.params.grp,'1');
						processedmessage = broadcast_language[24];
						break;
						default :
						processedmessage = null;
						break;
					}
					<?php endif; ?>
					return processedmessage;
				}
			};
		})();

})(jqcc);

jqcc(document).ready(function(){
	jqcc('.join_Broadcast').live('click',function(){
		var grp = jqcc(this).attr('grp');
		if(typeof(parent) != 'undefined' && parent != null && parent != self){
			var controlparameters = {"type":"plugins", "name":"ccbroadcast", "method":"join", "params":{"grp":grp}};
			controlparameters = JSON.stringify(controlparameters);
			if(typeof(parent) != 'undefined' && parent != null && parent != self){
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			} else {
				window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
			}
		} else {
			var controlparameters = {"grp":grp};
            jqcc.ccbroadcast.join(controlparameters);
		}
	});

	jqcc('.broadcastAccept').live('click',function(){
		var to = jqcc(this).attr('to');
		var grp = jqcc(this).attr('grp');
		if((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self){
			var controlparameters = {"type":"plugins", "name":"ccbroadcast", "method":"accept", "params":{"to":to, "grp":grp}};
			controlparameters = JSON.stringify(controlparameters);
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		} else {
			var controlparameters = {"to":to, "grp":grp};
            jqcc.ccbroadcast.accept(controlparameters);
		}
	});
});