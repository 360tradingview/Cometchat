<?php

/*

CometChat
Copyright (c) 2014 Inscripts

CometChat ('the Software') is a copyrighted work of authorship. Inscripts
retains ownership of the Software and any copies of it, regardless of the
form in which the copies may exist. This license is not a sale of the
original Software or any copies.

By installing and using CometChat on your server, you agree to the following
terms and conditions. Such agreement is either on your own behalf or on behalf
of any corporate entity which employs you or which you represent
('Corporate Licensee'). In this Agreement, 'you' includes both the reader
and any Corporate Licensee and 'Inscripts' means Inscripts (I) Private Limited:

CometChat license grants you the right to run one instance (a single installation)
of the Software on one web server and one web site for each license purchased.
Each license may power one instance of the Software on one domain. For each
installed instance of the Software, a separate license is required.
The Software is licensed only to you. You may not rent, lease, sublicense, sell,
assign, pledge, transfer or otherwise dispose of the Software in any form, on
a temporary or permanent basis, without the prior written consent of Inscripts.

The license is effective until terminated. You may terminate it
at any time by uninstalling the Software and destroying any copies in any form.

The Software source code may be altered (at your risk)

All Software copyright notices within the scripts must remain unchanged (and visible).

The Software may not be used for anything that would represent or is associated
with an Intellectual Property violation, including, but not limited to,
engaging in any activity that infringes or misappropriates the intellectual property
rights of others, including copyrights, trademarks, service marks, trade secrets,
software piracy, and patents held by individuals, corporations, or other entities.

If any of the terms of this Agreement are violated, Inscripts reserves the right
to revoke the Software license at any time.

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

use OpenTok\OpenTok;

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR."en.php");
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php");
}

if (!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$color.DIRECTORY_SEPARATOR."broadcast".$rtl.".css")) {
	$color = "standard";
}

$basedata = $to = $grp  = $action = $chatroommode = $embed = null;
if(!empty($_REQUEST['basedata'])) {
    $basedata = $_REQUEST['basedata'];
}
if(!empty($_REQUEST['to'])){
	$to = $_REQUEST['to'];
}
if(!empty($_REQUEST['grp'])){
	$grp = $_REQUEST['grp'];
}
if(!empty($_REQUEST['action'])){
	$action = $_REQUEST['action'];
}
if(!empty($_REQUEST['chatroommode'])){
	$chatroommode = $_REQUEST['chatroommode'];
}
if(!empty($_REQUEST['embed'])){
	$embed = $_REQUEST['embed'];
}

$broadcast = 0;
if(!empty($_REQUEST['broadcast'])){
	$broadcast = 1;
}

$cbfn = '';
if(!empty($_REQUEST['callbackfn'])){
	$cbfn = $_REQUEST['callbackfn'];
	$_SESSION['noguestmode'] = '1';
}

$cc_theme = '';
if(!empty($_REQUEST['cc_theme'])){
	$cc_theme = $_REQUEST['cc_theme'];
}

if($action == 'endcall') {
	if (!empty($chatroommode)) {
		$controlparameters = array('type' => 'plugins', 'name' => 'broadcast', 'method' => 'endcall', 'params' => array('grp' => $grp, 'chatroommode' => 1));
		$controlparameters = json_encode($controlparameters);
		sendChatroomMessage($to, 'CC^CONTROL_'.$controlparameters,0);
	} else {
		$controlparameters = array('type' => 'plugins', 'name' => 'broadcast', 'method' => 'endcall', 'params' => array('grp' => $grp, 'chatroommode' => 0));
		$controlparameters = json_encode($controlparameters);
		sendMessage($to,'CC^CONTROL_'.$controlparameters,2);
		incrementCallback();
		sendMessage($to, 'CC^CONTROL_'.$controlparameters,1);
	}
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'].'('.json_encode(1).')';
	} else {
		echo json_encode(1);
	}
}

if ($p_<4) exit;



if(!checkcURL() && $videoPluginType == '2') {
	echo "<div style='background:white;'>Please contact your site administrator to configure this plugin.</div>";
	exit;
}

if($videoPluginType == '2') {
	if($opentokApiSecret == '' || $opentokApiKey == '' || !file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'OpenTok'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php')){
		echo "<div style='background:white;'>The plugin has not been configured correctly. Please contact the site owner.</div>";
		exit;
	}
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'OpenTok'.DIRECTORY_SEPARATOR.'vendor/autoload.php');

	$apiKey = $opentokApiKey;
	$apiSecret = $opentokApiSecret;
	try {
		$apiObj = new OpenTok($apiKey, $apiSecret);
	} catch (Exception $e) {
		echo "<div style='background:white;padding:15px;'>Please ask your administrator to configure this plugin using administration panel.</div>";
		exit;
	}
}

if ($_REQUEST['action'] == 'request') {
	if($videoPluginType == '2') {
		$location = time();
		if(!empty($_SERVER['REMOTE_ADDR'])){
			$location = $_SERVER['REMOTE_ADDR'];
		}
		try {
			$session = $apiObj->createSession(array( 'location' => $location ));
			$grp = $session->getSessionId();
			$avchat_token = $apiObj->generateToken($grp);
		} catch (Exception $e) {
			echo "<div style='background:white;padding:15px;'>Please ask your administrator to configure this plugin using administration panel.</div>";
			exit;
		}
	} else {
		$grp = time();
	}

	sendMessage($_REQUEST['to'],$broadcast_language[2]." <a href='javascript:void(0);' class='broadcastAccept' to='".$userid."' grp='".$grp."' mobileAction=\"javascript:jqcc.ccbroadcast.accept('".$userid."','".$grp."');\">".$broadcast_language[3]."</a> ".$broadcast_language[4],1);
	sendMessage($_REQUEST['to'],$broadcast_language[5],2);

	if (!empty($_REQUEST['callback'])) {
		header('content-type: application/json; charset=utf-8');
		echo $_REQUEST['callback'].'()';
	}
}

if ($_REQUEST['action'] == 'call' ) {
	$grp = $_REQUEST['grp'];
	if($videoPluginType == '2' &&
		empty($_REQUEST['chatroommode'])){
		if (!empty($_SESSION['avchat_token'])) {
			$avchat_token = $_SESSION['avchat_token'];
		} else {
			$avchat_token = $apiObj->generateToken($grp);
		}
	}
}

if($videoPluginType < '2') {
	$sender = $_REQUEST['type'];
	if (!empty($_REQUEST['chatroommode'])) {
		if (empty($_REQUEST['join'])) {
			sendChatroomMessage($grp,$broadcast_language[17]." <a href='javascript:void(0);' onclick=\"javascript:jqcc.ccbroadcast.join('".$_REQUEST['grp']."');\">".$broadcast_language[16]."</a>",0);
		}
	}
	ini_set('display_errors', 0);
	$mode = 3;
	$flashvariables = '{grp:"'.$grp.'",connectUrl: "'.$connectUrl.'",name:"",quality: "'. $quality. '",bandwidth: "'.$bandwidth.'",fps:"'.$fps.'",mode: "'.$mode.'",maxP: "'.$maxP.'",camWidth: "'.$camWidth.'",camHeight: "'.$camHeight.'",soundQuality: "'.$soundQuality.'",sender: "'.$sender.'"}';
	$file = '_fms';

	echo <<<EOD
	<!DOCTYPE html>
	<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>{$broadcast_language[8]}</title>
                <link media="all" rel="stylesheet" type="text/css" href="../../css.php?type=plugin&name=broadcast&subtype=fmsred5"/>
		<script type="text/javascript" src="../../js.php?type=plugin&name=broadcast&subtype=fmsred5"></script>
		<script type="text/javascript">
			var swfVersionStr = "10.1.0";
			var xiSwfUrlStr = "playerProductInstall.swf";
			var flashvars = {$flashvariables};
			var params = {};
			params.quality = "high";
			params.bgcolor = "#000000";
			params.allowscriptaccess = "sameDomain";
			params.allowfullscreen = "true";
			var attributes = {};
			attributes.id = "audiovideochat";
			attributes.name = "audiovideochat";
			attributes.align = "middle";
			swfobject.embedSWF(
				"audiovideochat{$file}.swf?v2.2", "flashContent",
				"100%", "100%",
				swfVersionStr, xiSwfUrlStr,
				flashvars, params, attributes);
			swfobject.createCSS("#flashContent", "display:block;text-align:left;");

			function getFocus() {
				setTimeout('self.focus();',10000);
			}
			window.onbeforeunload = function() {
				var AddCallbackExample = document.getElementById("audiovideochat_fms");
				AddCallbackExample.getUnsavedDataWarning();
			}
		</script>
		</head>
		<body onblur="getFocus()">
		  <div id="flashContent">
					<p>
						To view this page ensure that Adobe Flash Player version
						10.1.0 or greater is installed. If the issue still persists please configure the plugin through Administration Panel.
					</p>
					<script type="text/javascript">
						var pageHost = ((document.location.protocol == "https:") ? "https://" :	"http://");
						document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='"
										+ pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" );
					</script>
				</div>
		</body>
	</html>
EOD;
	} elseif($videoPluginType == '3'){
		if (!empty($_REQUEST['chatroommode'])) {
			if (empty($_REQUEST['join'])) {
				sendChatroomMessage($grp,$broadcast_language[17]." <a href='javascript:void(0);' grp='".$_REQUEST['grp']."' class='join_Broadcast' mobileAction=\"javascript:jqcc.ccbroadcast.join('".$_REQUEST['grp']."');\">".$broadcast_language[16]."</a>",0);
			}
		}
		$name = "";
		$sql = getUserDetails($userid);
		if ($guestsMode && $userid >= 10000000) {
			$sql = getGuestDetails($userid);
		}

		$result = mysqli_query($GLOBALS['dbh'],$sql);
		if($row = mysqli_fetch_assoc($result)) {
			if (function_exists('processName')) {
				$row['username'] = processName($row['username']);
			}
			$name = $row['username'];
		}
		$name = urlencode($name);

		$baseUrl = BASE_URL;
		$embed = '';
		$embedcss = '';
		$resize = 'window.resizeTo(';
		$invitefunction = 'window.open';

		if (!empty($_REQUEST['embed']) && $_REQUEST['embed'] == 'web') {
			$embed = 'web';
			$resize = "parent.resizeCCPopup('broadcast',";
			$embedcss = 'embed';
			$invitefunction = 'parent.loadCCPopup';
		}

		if (!empty($_REQUEST['embed']) && $_REQUEST['embed'] == 'desktop') {
			$embed = 'desktop';
			$resize = "parentSandboxBridge.resizeCCPopupWindow('broadcast',";
			$embedcss = 'embed';
			$invitefunction = 'parentSandboxBridge.loadCCPopupWindow';
		}

		$server_name = '';
		$onload = 'endCall(1)';
		if(strpos(BASE_URL,'//')=== false) {
			$server_name = '//'.$_SERVER['SERVER_NAME'];
		}
		if(CROSS_DOMAIN==1){
			$cssurl = BASE_URL.'css.php?cc_theme='.$cc_theme;
		}else{
			$cssurl = $server_name.BASE_URL.'css.php?cc_theme='.$cc_theme;
		}
		$endcall = '<a href="#" onclick="endCall(1)" id="endcall" class="cometchat_statusbutton" style="display: block;text-decoration: none;z-index: 10000;">'.$broadcast_language[19].'</a>';
		if(!empty($chatroommode)||CROSS_DOMAIN == 1){
			$onload = 'closeWin()';
			$endcall = '<a href="#" onclick="closeWin()" id="endcall" class="cometchat_statusbutton" style="display: block;text-decoration: none;z-index: 10000;">'.$broadcast_language[19].'</a>';
		}
		$invitecall = '';
		if(!$broadcast){
			$invitecall = '<a href="#" id="broadcastInvite" class="cometchat_statusbutton" style="display: block;text-decoration: none;z-index: 10000;">'.$broadcast_language[12].'</a>';
		}
		$v1 = rawurlencode($broadcast_language[20]);
		$v0 = rawurlencode($broadcast_language[21]);
		$m1 = rawurlencode($broadcast_language[22]);
		$m0 = rawurlencode($broadcast_language[23]);
		$grp = md5($channelprefix.$grp);
		echo <<<EOD
			<!DOCTYPE html>
			<html>
				<head>
					<title>{$broadcast_language[8]}</title>
					<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
					<link href="../../css.php?type=plugin&name=broadcast&subtype=webrtc" type="text/css" rel="stylesheet" >
					<link href="../../css.php?cc_theme={$cc_theme}" type="text/css" rel="stylesheet" >
					<script src="../../js.php?type=plugin&name=broadcast&subtype=webrtc&embed={$embed}" type="text/javascript"></script>
					<script>
						var basedata = '{$basedata}';
						var sessionId = '{$grp}';
						var invitefunction = "{$invitefunction}";
						var baseUrl = '{$baseUrl}';
						jqcc(document).ready(function(){
							jqcc('#broadcastInvite').on('click',function(){

								if(typeof(parent) != 'undefined' && parent != null && parent != self){
									var controlparameters = {"type":"plugins", "name":"ccbroadcast", "method":"inviteBroadcast", "params":{"id":sessionId}};
						            controlparameters = JSON.stringify(controlparameters);
						            if(typeof(parent) != 'undefined' && parent != null && parent != self){
						                parent.postMessage('CC^CONTROL_'+controlparameters,'*');
						            } else {
						                window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
						            }
						        } else {
						            var controlparameters = {"id":sessionId};
						            jqcc.ccbroadcast.inviteBroadcast(controlparameters);
						        }
							});
						});

						if(typeof(parent) === 'undefined'){
							var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnce", "grp":"{$grp}", "value":"0"}};
                            controlparameters = JSON.stringify(controlparameters);
                            window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');

                            var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnceWindow", "grp":"{$grp}", "value":"0"}};
                            controlparameters = JSON.stringify(controlparameters);
                            window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
						}else{
							var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnce", "grp":"{$grp}", "value":"0"}};
                            controlparameters = JSON.stringify(controlparameters);
                            parent.postMessage('CC^CONTROL_'+controlparameters,'*');

                            var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnceWindow", "grp":"{$grp}", "value":"0"}};
                            controlparameters = JSON.stringify(controlparameters);
                            parent.postMessage('CC^CONTROL_'+controlparameters,'*');
						}
						function endCall(caller){
							if(typeof(parent) === 'undefined'){
								var controlparameters = {"type":"plugins", "name":"ccbroadcast", "method":"end_call", "params":{"to":"{$to}", "grp":"{$grp}","chatroommode":"0"}};
                                controlparameters = JSON.stringify(controlparameters);
                                window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');

								var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnceWindow", "grp":"{$grp}", "value":"1"}};
	                            controlparameters = JSON.stringify(controlparameters);
	                            window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');

								window.close();
							} else {
								var controlparameters = {"type":"plugins", "name":"ccbroadcast", "method":"end_call", "params":{"to":"{$to}", "grp":"{$grp}","chatroommode":"0"}};
                                controlparameters = JSON.stringify(controlparameters);
                                parent.postMessage('CC^CONTROL_'+controlparameters,'*');

								var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnce", "grp":"{$grp}", "value":"1"}};
	                            controlparameters = JSON.stringify(controlparameters);
	                            parent.postMessage('CC^CONTROL_'+controlparameters,'*');
								if(caller)
								var controlparameters = {'type':'plugins', 'name':'broadcast', 'method':'closeCCPopup', 'params':{}};
                                controlparameters = JSON.stringify(controlparameters);
                                parent.postMessage('CC^CONTROL_'+controlparameters,'*');
							}
						}
						function closeWin(){
							if(typeof(parent) === 'undefined'){
								var controlparameters = {"type":"plugins", "name":"ccbroadcast", "method":"end_call", "params":{"to":"{$to}", "grp":"{$grp}","chatroommode":"0"}};
                                controlparameters = JSON.stringify(controlparameters);
                                window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');

								var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnceWindow", "grp":"{$grp}", "value":"1"}};
	                            controlparameters = JSON.stringify(controlparameters);
	                            window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');

								window.close();
							} else {
								var controlparameters = {"type":"plugins", "name":"ccbroadcast", "method":"end_call", "params":{"to":"{$to}", "grp":"{$grp}","chatroommode":"0"}};
                                controlparameters = JSON.stringify(controlparameters);
                                parent.postMessage('CC^CONTROL_'+controlparameters,'*');

								var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnce", "grp":"{$grp}", "value":"1"}};
	                            controlparameters = JSON.stringify(controlparameters);
	                            parent.postMessage('CC^CONTROL_'+controlparameters,'*');

								var controlparameters = {'type':'plugins', 'name':'broadcast', 'method':'closeCCPopup', 'params':{}};
                                controlparameters = JSON.stringify(controlparameters);
                                parent.postMessage('CC^CONTROL_'+controlparameters,'*');
							}
						}
					</script>
				</head>
				<body onunload="{$onload}">
					<iframe id ="webrtc" src="//{$webRTCServer}/index.php?v1={$v1}&v0={$v0}&m1={$m1}&m0={$m0}&broadcast={$broadcast}&room={$grp}&cssurl={$cssurl}" width=100% height=100% seamless allowfullscreen></iframe>
					<div id="broadButtons">
						{$endcall}
						{$invitecall}
					</div>
				</div>
				</body>
			</html>
EOD;
	}else {

	if (!empty($_REQUEST['chatroommode'])) {
		$grporg = $grp ;
		$sql = ("select vidsession from cometchat_chatrooms where id = '".mysqli_real_escape_string($GLOBALS['dbh'],$grp)."'");
		$query = mysqli_query($GLOBALS['dbh'],$sql);
		$chatroom = mysqli_fetch_assoc($query);

		if (empty($chatroom['vidsession'])) {
			if(!empty($_SERVER['REMOTE_ADDR'])){
				$location = $_SERVER['REMOTE_ADDR'];
			}
			try {
				$session = $apiObj->createSession(array( 'location' => $location ));
				$newsessionid = $session->getSessionId();
			} catch (Exception $e) {
				echo "<div style='background:white;padding:15px;'>Please ask your administrator to configure this plugin using administration panel.</div>";
				exit;
			}
			$sql = ("update cometchat_chatrooms set  vidsession = '".mysqli_real_escape_string($GLOBALS['dbh'],$newsessionid)."' where id = '".mysqli_real_escape_string($GLOBALS['dbh'],$grp)."'");
			$query = mysqli_query($GLOBALS['dbh'],$sql);
			$grp = $newsessionid;
		} else {
			$grp = $chatroom['vidsession'];
		}

		if (empty($_REQUEST['join'])) {
			sendChatroomMessage($grporg,$broadcast_language[9]." <a href='javascript:void(0);' onclick=\"javascript:jqcc.ccbroadcast.join('".$grporg."');\">".$broadcast_language[10]."</a>",0);
		}

		$avchat_token = $apiObj->generateToken($grp);
	}

	$name = "";
	$sql = getUserDetails($userid);
	if ($guestsMode && $userid >= 10000000) {
		$sql = getGuestDetails($userid);
	}

	$result = mysqli_query($GLOBALS['dbh'],$sql);
	if($row = mysqli_fetch_assoc($result)) {
		if (function_exists('processName')) {
			$row['username'] = processName($row['username']);
		}
		$name = $row['username'];
	}
	$name = urlencode($name);

	$baseUrl = BASE_URL;
	$embed = '';
	$embedcss = '';
	$resize = 'window.resizeTo(';
	$invitefunction = 'window.open';

	if (!empty($_REQUEST['embed']) && $_REQUEST['embed'] == 'web') {
		$embed = 'web';
		$resize = "parent.resizeCCPopup('broadcast',";
		$embedcss = 'embed';
		$invitefunction = 'parent.loadCCPopup';
	}
	if (!empty($_REQUEST['embed']) && $_REQUEST['embed'] == 'desktop') {
		$embed = 'desktop';
		$resize = "parentSandboxBridge.resizeCCPopupWindow('broadcast',";
		$embedcss = 'embed';
		$invitefunction = 'parentSandboxBridge.loadCCPopupWindow';
	}

	$publish = "";
	$control = "";

	if ($_REQUEST['type'] == 1) {
		$publish = "publisher = OT.initPublisher('canvasPub', {width: $vidWidth, height: $vidHeight,insertMode: 'replace'});session.publish(publisher);";
		$control = '<div id="navigation" style="display:block">
			<div id="navigation_elements">
				<a href="#" onclick="javascript:inviteUser()" id="inviteLink"><img src="res/invite.png"></a>
				<a href="#" id="publishVideo"><img src="res/turnoffvideo.png"></a>
				<div style="clear:both"></div>
			</div>
			<div style="clear:both"></div>
		</div>';
	}

	echo <<<EOD
	<!DOCTYPE html>
	<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>{$broadcast_language[8]}</title>
                <link media="all" rel="stylesheet" type="text/css" href="../../css.php?type=plugin&name=broadcast&subtype=opentok"/>
                <style>
                	html,body{
                		width:100%;
                		height:100%;
                	}
                </style>
				<script src='//static.opentok.com/webrtc/v2.2/js/opentok.min.js'></script>
				<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
				<script src="../../js.php?type=plugin&name=broadcast&subtype=opentok&embed={$embed}" type="text/javascript"></script>
				<script type="text/javascript" charset="utf-8">
                    var basedata = '{$basedata}';
                    var apiKey = '{$apiKey}';
					var sessionId = '{$grp}';
					var session = OT.initSession(apiKey, sessionId);
					var token = '{$avchat_token}';
					var invitefunction = "{$invitefunction}";
					session.on({
					  streamCreated: function(event) {
					    session.subscribe(event.stream, 'canvasSub', {insertMode: 'append',width: $vidWidth, height: $vidHeight});
					  }
					});
					var publisher;
					$(function(){

						session.connect(token, function(error) {
							  if (error) {
							    console.log(error.message);
							  } else {
							    {$publish}
							  }
							});

						$('#publishVideo').click(function(e){
							e.preventDefault();
							pub = $('#publishVideo');
							innerHtml = pub.html();
							if(innerHtml == '<img src="res/turnoffvideo.png">'){
								pub.html('<img src="res/turnonvideo.png">');
								publisher.publishVideo(false);
							}else{
								pub.html('<img src="res/turnoffvideo.png">');
								publisher.publishVideo(true);
							}
						});
					});
				</script>
		</head>
		<body>
			<div id="canvasPub"></div>
			<div id="canvasSub"></div>
			{$control}
		</body>
		<script>
		var reSize = function(){
			var otSubscribers = $('#canvasSub > .OT_subscriber');
			if(otSubscribers.length == 1){
				$('#canvasSub').css( 'width', '100%' );
				$('#canvasSub').css( 'height', '100%' );
				$('#canvasSub > .OT_subscriber').each(function () {
				    this.style.setProperty( 'width', '100%', 'important' );
			    	this.style.setProperty( 'height', '100%', 'important' );
				});

			} else{
			    $('#canvasPub').css( 'width', '100%' );
			    $('#canvasPub').css( 'height', '100%' );
			}
		}
		window.onresize = reSize;
		</script>
	</html>
EOD;
}