<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* SETTINGS START */

$videoPluginType = '3';
$vidWidth = '350';
$vidHeight = '262';
$maxP = '10';
$quality = '90';
$connectUrl = '';
$camWidth = '450';
$camHeight = '335';
$fps = '30';
$soundQuality = '7';
$opentokApiKey = '';
$opentokApiSecret = '';

/* SETTINGS END */
/* videoPluginType
0.FMS
1.red5
2.Opentok 2.0
3.CometChat Server (WebRTC)
*/
$webRTCServer = 'r.chatforyoursite.com';
if ($videoPluginType == '3') {
	$camWidth = '450';
	$camHeight = '335';
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////