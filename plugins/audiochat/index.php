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

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."plugins.php");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR."en.php");
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang".DIRECTORY_SEPARATOR.$lang.".php");
}
$webrtcTheme = $theme;
if (!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR."audiochat".$rtl.".css")) {
	$theme = "standard";
}

if ($p_<4) exit;

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
$cbfn = '';
if(!empty($_REQUEST['callbackfn'])){
	$cbfn = $_REQUEST['callbackfn'];
	$_SESSION['noguestmode'] = '1';
}
$cc_theme = '';
if(!empty($_REQUEST['cc_theme'])){
	$cc_theme = $_REQUEST['cc_theme'];
}
configCheck();

if($action == 'endcall') {
	if (!empty($chatroommode)) {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'endcall', 'params' => array('grp' => $grp, 'chatroommode' => 1));
		$controlparameters = json_encode($controlparameters);
		sendChatroomMessage($to, 'CC^CONTROL_'.$controlparameters,0);
	} else {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'endcall', 'params' => array('grp' => $grp, 'chatroommode' => 0));
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
if($action == 'rejectcall') {
	if (!empty($chatroommode)) {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'rejectcall', 'params' => array('grp' => $grp, 'chatroommode' => 1));
		$controlparameters = json_encode($controlparameters);
		sendChatroomMessage($to, 'CC^CONTROL_'.$controlparameters,0);
	} else {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'rejectcall', 'params' => array('grp' => $grp, 'chatroommode' => 0));
		$controlparameters = json_encode($controlparameters);
		sendMessage($to, 'CC^CONTROL_'.$controlparameters,1);
	}
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'].'('.json_encode(1).')';
	} else {
		echo json_encode(1);
	}
}

if($action == 'noanswer') {
	if (!empty($chatroommode)) {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'noanswer', 'params' => array('grp' => $grp, 'chatroommode' => 1));
		$controlparameters = json_encode($controlparameters);
		sendChatroomMessage($to, 'CC^CONTROL_'.$controlparameters,0);
	} else {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'noanswer', 'params' => array('grp' => $grp, 'chatroommode' => 0));
		$controlparameters = json_encode($controlparameters);
		sendMessage($to, 'CC^CONTROL_'.$controlparameters,1);
	}
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'].'('.json_encode(1).')';
	} else {
		echo json_encode(1);
	}
}

if($action == 'canceloutgoingcall') {
	if (!empty($chatroommode)) {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'canceloutgoingcall', 'params' => array('grp' => $grp, 'chatroommode' => 1));
		$controlparameters = json_encode($controlparameters);
		sendChatroomMessage($to, 'CC^CONTROL_'.$controlparameters,0);
	} else {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'canceloutgoingcall', 'params' => array('grp' => $grp, 'chatroommode' => 0));
		$controlparameters = json_encode($controlparameters);
		sendMessage($to, 'CC^CONTROL_'.$controlparameters,2);
		incrementCallback();
		sendMessage($to, 'CC^CONTROL_'.$controlparameters,1);
	}
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'].'('.json_encode(1).')';
	} else {
		echo json_encode(1);
	}
}

if($action == 'busycall') {
	if (!empty($chatroommode)) {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'busycall', 'params' => array('grp' => $grp, 'chatroommode' => 1));
		$controlparameters = json_encode($controlparameters);
		sendChatroomMessage($to, 'CC^CONTROL_'.$controlparameters,0);
	} else {
		$controlparameters = array('type' => 'plugins', 'name' => 'audiochat', 'method' => 'busycall', 'params' => array('grp' => $grp, 'chatroommode' => 0));
		$controlparameters = json_encode($controlparameters);
		sendMessage($to, 'CC^CONTROL_'.$controlparameters,1);
	}
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'].'('.json_encode(1).')';
	} else {
		echo json_encode(1);
	}
}

if ($action == 'request') {
	$audiochat_token = '';
	if(empty($grp)){
		$grp = $userid<$to? md5($userid).md5($to) : md5($to).md5($userid);
		$grp = md5($_SERVER['HTTP_HOST'].$grp);
	}
	if(isset($chatroommode)){
		sendChatroomMessage($to, $audiochat_language[19]." <a token ='".$audiochat_token."' href='javascript:void(0);' onclick=\"javascript:jqcc.ccaudiochat.join('".$to."');\">".$audiochat_language[20]."</a> ",0);
	}else{
		sendMessage($to,$audiochat_language[2]." <a class='audiochat_link_".$grp." acceptAudioChat' token ='".$audiochat_token."' mobileAction=\"javascript:jqcc.ccaudiochat.accept('".$userid."','".$grp."');\" href='javascript:void(0);' to='".$userid."' grp='".$grp."' >".$audiochat_language[3]."</a> ".$audiochat_language[45]."<a href='javascript:void(0);' class='audiochat_link_".$grp."' onclick=\"javascript:jqcc.ccaudiochat.reject_call('".$userid."','".$grp."');\">".$audiochat_language[43].".</a>".$audiochat_language[46],1);
		incrementCallback();
		$_REQUEST['callback'];
		sendMessage($to,$audiochat_language[5].$audiochat_language[44]."<a href='javascript:void(0);' class='audiochat_link_".$grp."' onclick=\"javascript:jqcc.ccaudiochat.cancel_call('".$to."','".$grp."');\">".$audiochat_language[43].".</a>",2);
	}
	if (!empty($_REQUEST['callback'])) {
		header('content-type: application/json; charset=utf-8');
		echo json_encode(1);
	} else {
		echo json_encode(1);
	}
	exit;
}
if ($action == 'accept') {
	incrementCallback();
	$_REQUEST['callback'];
	sendMessage($to,$audiochat_language[6]." <a href='javascript:void(0);' class='audiochat_link_".$grp." accept_fid' mobileAction=\"javascript:jqcc.ccaudiochat.accept_fid('".$userid."','".$grp."');\" to='".$userid."' grp='".$grp."' >".$audiochat_language[7]."</a>",1);
	exit;
}
if ($action == 'call') {
	$baseUrl = BASE_URL;
	$embed = '';
	$embedcss = '';
	$onload = 'endCall(1)';
	$resize = 'window.resizeTo(';
	$invitefunction = 'window.open';
	if (!empty($embed) && $embed == 'web') {
		$embed = 'web';
		$resize = "parent.resizeCCPopup('audiochat',";
		$embedcss = 'embed';
		$invitefunction = 'parent.loadCCPopup';
	}
	if (!empty($embed) && $embed == 'desktop') {
		$embed = 'desktop';
		$resize = "parentSandboxBridge.resizeCCPopupWindow('audiochat',";
		$embedcss = 'embed';
		$invitefunction = 'parentSandboxBridge.loadCCPopupWindow';
	}
	if(CROSS_DOMAIN == 1){
		$cssurl=BASE_URL.'css.php?cc_theme='.$cc_theme;
	}else{
		$cssurl='//'.$_SERVER['SERVER_NAME'].BASE_URL.'css.php?cc_theme='.$cc_theme;
	}
	$endcall = '<a href="#" onclick="endCall(1)" id="endcall" class="cometchat_statusbutton" style="display: block;text-decoration: none;z-index: 10000;">'.$audiochat_language[49].'</a>';
	if(!empty($chatroommode) || CROSS_DOMAIN == 1){
      	$onload = 'closeWin()';
		$endcall = '<a href="#" onclick="closeWin()" id="endcall" class="cometchat_statusbutton" style="display: block;text-decoration: none;z-index: 10000;">'.$audiochat_language[49].'</a>';
	}
	$m1=rawurlencode($audiochat_language[50]);
	$m0=rawurlencode($audiochat_language[51]);
	$grp = md5($channelprefix.$grp);
	echo <<<EOD
	<!DOCTYPE html>
	<html>
		<head>
			<title>{$audiochat_language[8]}</title>
			<link href="../../css.php?cc_theme={$cc_theme}" type="text/css" rel="stylesheet" >
			<link href="../../css.php?type=plugin&name=audiochat&subtype=webrtc" type="text/css" rel="stylesheet" >
			<script>
			var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnce", "grp":"{$grp}", "value":"0"}};
            controlparameters = JSON.stringify(controlparameters);
            parent.postMessage('CC^CONTROL_'+controlparameters,'*');

            var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnceWindow", "grp":"{$grp}", "value":"0"}};
            controlparameters = JSON.stringify(controlparameters);
            parent.postMessage('CC^CONTROL_'+controlparameters,'*');

			function endCall(caller){
				if(typeof(parent) === 'undefined'){
					var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnceWindow", "grp":"{$grp}", "value":"1"}};
                    controlparameters = JSON.stringify(controlparameters);
                    window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');

                    var controlparameters = {"type":"plugins", "name":"ccaudiochat", "method":"end_call", "params":{"to":"{$to}", "grp":"{$grp}", "chatroommode":"0"}};
                    controlparameters = JSON.stringify(controlparameters);
                    window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');

					window.close();
				} else {
					var controlparameters = {"type":"plugins", "name":"ccaudiochat", "method":"end_call", "params":{"to":"{$to}", "grp":"{$grp}","chatroommode":"0"}};
                    controlparameters = JSON.stringify(controlparameters);
                    parent.postMessage('CC^CONTROL_'+controlparameters,'*');

                    var controlparameters = {"type":"plugins", "name":"cometchat", "method":"setInternalVariable", "params":{"type":"endcallOnce", "grp":"{$grp}", "value":"1"}};
                        controlparameters = JSON.stringify(controlparameters);
                        parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					if(caller)
						var controlparameters = {'type':'plugins', 'name':'audiochat', 'method':'closeCCPopup', 'params':{}};
                        controlparameters = JSON.stringify(controlparameters);
                        parent.postMessage('CC^CONTROL_'+controlparameters,'*');
				}
			}
			function closeWin(){
				if(typeof(parent) === 'undefined'){
					window.close();
				} else {
					var controlparameters = {'type':'plugins', 'name':'audiochat', 'method':'closeCCPopup', 'params':{}};
                    controlparameters = JSON.stringify(controlparameters);
                    parent.postMessage('CC^CONTROL_'+controlparameters,'*');
				}
			}
			</script>
		</head>
		<body onunload="{$onload}">
			<iframe id ="webrtc" src="//{$webRTCServer}/?audioOnly=1&m1={$m1}&m0={$m0}&room={$grp}&cssurl={$cssurl}" width=100% height=100% seamless allowfullscreen></iframe>
			{$endcall}
		</div>
		</body>
	</html>
EOD;
}

function configCheck(){
	$errorFlag = 0;
	if(!empty($to)){
		global $connectUrl,$audioPluginType;
		$error = $audiochat_language[47];
		switch($audioPluginType){
			case '1':
			break;
		}
		if($errorFlag){
			sendMessage($to,$error,2);
			exit;
		}
	}
}