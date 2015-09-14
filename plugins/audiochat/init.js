<?php
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR."en.php");

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php");
}

foreach ($audiochat_language as $i => $l) {
	$audiochat_language[$i] = str_replace("'", "\'", $l);
}

$width = 225;
$height = 200;
?>

/*
		* CometChat
		* Copyright (c) 2014 Inscripts - support@cometchat.com | http://www.cometchat.com | http://www.inscripts.com
*/
String.prototype.replaceAll=function(s1, s2) {return this.split(s1).join(s2);};
(function($){

	$.ccaudiochat = (function () {

		var title = '<?php echo $audiochat_language[0];?>';
		var type = '<?php echo $audioPluginType;?>';
		var supported = true;
		var lastcall = 0;
		var allowresize = 1;
		var Browser = (function(){
			var ua= navigator.userAgent, tem,
			M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
			if(/trident/i.test(M[1])){
				tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
				return 'IE '+(tem[1] || '');
			}
			if(M[1]=== 'Chrome'){
				tem= ua.match(/\bOPR\/(\d+)/);
				if(tem!= null) return 'Opera '+tem[1];
			}
			M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
			if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
			return M;
		})();
		if(Browser[0].search(/(msie|safari)/i) > -1){
			supported = false;
		}

		return {

			getTitle: function() {
				return title;
			},

			init: function (params) {
				var id = params.to;
				if(supported) {
					var currenttime = new Date();
					currenttime = parseInt(currenttime.getTime()/1000);
					if (currenttime-lastcall > 10) {

						baseUrl = $.cometchat.getBaseUrl();
						baseData = $.cometchat.getBaseData();
						jqcc.ajax({
							url : baseUrl+'plugins/audiochat/index.php?action=request',
							type : 'GET',
							data : {to: id, basedata: baseData},
							dataType : 'jsonp',
							success : function(data) {
							},
							error : function(data) {
							}
						});
						if (jqcc.cometchat.getThemeArray('buddylistIsDevice',id) == 1) {
							jqcc.ccmobilenativeapp.sendnotification('<?php echo $audiochat_language[5];?>', id, jqcc.cometchat.getName(jqcc.cometchat.getThemeVariable('userid')));
						}
						lastcall = currenttime;
					} else {
						alert('<?php echo $audiochat_language[1];?>');
					}
				} else {
					alert('<?php echo $audiochat_language[48];?>');
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
				if(supported){
					baseUrl = $.cometchat.getBaseUrl();
					baseData = $.cometchat.getBaseData();
					jqcc.ccaudiochat.delinkaudiochat(grp);
					$.getJSON(baseUrl+'plugins/audiochat/index.php?action=accept&callback=?', {to: id, start_url:null, grp: grp, basedata: baseData});
					loadCCPopup(baseUrl+'plugins/audiochat/index.php?action=call&grp='+grp+'&basedata='+baseData+'&to='+id, 'audiochat',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $camWidth;?>,height=<?php echo $camHeight;?>",<?php echo $camWidth;?>,<?php echo $camHeight;?>,'<?php echo $audiochat_language[8];?>',0,1,allowresize,1,windowMode);
				} else {
					alert('<?php echo $audiochat_language[48];?>');
				}
			},

			accept_fid: function (params) {
				id = params.to;
				grp = params.grp;
				if(typeof(params.windowMode) == "undefined") {
					windowMode = 0;
				} else {
					windowMode = 1;
				}
				jqcc.ccaudiochat.delinkaudiochat(grp);
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				loadCCPopup(baseUrl+'plugins/audiochat/index.php?action=call&grp='+grp+'&basedata='+baseData+'&to='+id, 'audiochat',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $camWidth;?>,height=<?php echo $camHeight;?>",<?php echo $camWidth;?>,<?php echo $camHeight;?>,'<?php echo $audiochat_language[8];?>',0,1,allowresize,1,windowMode);
			},

			ignore_call : function(id,grp){
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				$.ajax({
					url : baseUrl+'plugins/audiochat/index.php?action=noanswer',
					type : 'GET',
					data : {to: id,grp: grp,basedata:baseData},
					dataType : 'jsonp',
					success : function(data) {},
					error : function(data) {}
				});
			},

			cancel_call : function(id,grp){
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				$.ajax({
					url : baseUrl+'plugins/audiochat/index.php?action=canceloutgoingcall',
					type : 'GET',
					data : {to: id,grp: grp,basedata:baseData},
					dataType : 'jsonp',
					success : function(data) {},
					error : function(data) {}
				});
			},

			reject_call : function(id,grp){
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				jqcc.ccaudiochat.delinkaudiochat(grp);

				$.ajax({
					url : baseUrl+'plugins/audiochat/index.php?action=rejectcall',
					type : 'GET',
					data : {to: id,grp: grp,basedata:baseData},
					dataType : 'jsonp',
					success : function(data) {},
					error : function(data) {}
				});
			},

			end_call : function(params){
				id = params.to;
				grp = params.grp;
				baseUrl = $.cometchat.getBaseUrl();
				baseData = $.cometchat.getBaseData();
				var popoutopencalled = jqcc.cometchat.getInternalVariable('audiochatpopoutcalled');
				var endcallrecieved = jqcc.cometchat.getInternalVariable('endcallrecievedfrom_'+grp);
				if(popoutopencalled !== '1'){
					if(endcallrecieved !== '1') {
						$.ajax({
							url : baseUrl+'plugins/audiochat/index.php?action=endcall',
							type : 'GET',
							data : {to: id, basedata: baseData , grp: grp},
							dataType : 'jsonp',
							success : function(data) {},
							error : function(data) {}
						});
					}
				}
				jqcc.cometchat.setInternalVariable('endcallrecievedfrom_'+grp,'0');
				jqcc.cometchat.setInternalVariable('audiochatpopoutcalled','0');
			},

			join: function (id) {
				baseUrl = $.cometchat.getBaseUrl();
				basedata = $.cometchat.getBaseData();
				$[$.cometchat.getChatroomVars('calleeAPI')].loadCCPopup(baseUrl+'plugins/audiochat/index.php?action=call&chatroommode=1&type=0&join=1&grp='+id+'&basedata='+basedata, 'audiochat',"status=0,toolbar=0,menubar=0,directories=0,resizable=1,location=0,status=0,scrollbars=0, width=<?php echo $camWidth;?>,height=<?php echo $camHeight;?>",<?php echo $camWidth;?>,<?php echo $camHeight;?>,'<?php echo $audiochat_language[8];?>',1,1,allowresize,1);
			},

			getLangVariables: function() {
				return <?php echo json_encode($audiochat_language); ?>;
			},

			delinkaudiochat: function(grp){
				$('a.audiochat_link_'+grp).each(function(){
					$(this).attr('onclick','').unbind('click');
					this.style.setProperty( 'color', '#95a5a6', 'important' );
					$(this).css('text-decoration','none');
					$(this).css('cursor','text');
				});
			},

			processControlMessage : function(controlparameters) {
				var audiochat_language = jqcc.ccaudiochat.getLangVariables();
				var processedmessage = null;
				jqcc.ccaudiochat.delinkaudiochat(controlparameters.params.grp);
				switch(controlparameters.method){
					case 'endcall':
						jqcc.cometchat.setInternalVariable('endcallrecievedfrom_'+controlparameters.params.grp,'1');
						processedmessage = audiochat_language[38];
						break;
					case 'rejectcall':
						processedmessage = audiochat_language[39];
						break;
					case 'noanswer':
						processedmessage = audiochat_language[40];
						break;
					case 'busycall':
						processedmessage = audiochat_language[39];
						break;
					case 'cancelcall':
						processedmessage = audiochat_language[37];
						break;
					default :
						processedmessage = null;
						break;
				}
				return processedmessage;
			}

		};
	})();
})(jqcc);

jqcc(document).ready(function(){
	jqcc('.acceptAudioChat').live('click',function(){
		var to = jqcc(this).attr('to');
		var grp = jqcc(this).attr('grp');
		if((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self){
			var controlparameters = {"type":"plugins", "name":"ccaudiochat", "method":"accept", "params":{"to":to, "grp":grp}};
			controlparameters = JSON.stringify(controlparameters);
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		} else {
			var controlparameters = {"to":to, "grp":grp};
            jqcc.ccaudiochat.accept(controlparameters);
		}
	});

	jqcc('.accept_fid').live('click',function(){
		var to = jqcc(this).attr('to');
		var grp = jqcc(this).attr('grp');
		if((typeof(parent) != 'undefined' && parent != null && parent != self) || window.top != window.self){
			var controlparameters = {"type":"plugins", "name":"ccaudiochat", "method":"accept_fid", "params":{"to":to, "grp":grp}};
			controlparameters = JSON.stringify(controlparameters);
			parent.postMessage('CC^CONTROL_'+controlparameters,'*');
		} else {
			var controlparameters = {"to":to, "grp":grp};
            jqcc.ccaudiochat.accept_fid(controlparameters);
		}
	});
});
