<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* SETTINGS START */

$chatroomTimeout = '604800';
$lastMessages = '1000';
$allowUsers = '0';
$allowDelete = '0';
$displayFullName = '1';
$allowAvatar = '1';
$crguestsMode = '0';
$hideEnterExit = '1';
$minHeartbeat = '60000';
$maxHeartbeat = '60000';
$autoLogin = '11';
$messageBeep = '0';
$newMessageIndicator = '1';


/* SETTINGS END */

/* MODERATOR START */

$moderatorUserIDs = array(4,2,46,39);


/* MODERATOR END */



if (USE_COMET == 1 && COMET_CHATROOMS == 1) {
	$minHeartbeat = $maxHeartbeat = REFRESH_BUDDYLIST.'000';
	$hideEnterExit = 1;
}

/* ADDITIONAL SETTINGS */

$chatroomLongNameLength = 60;	// The chatroom length after which characters will be truncated
$chatroomShortNameLength = 30;	// The chatroom length after which characters will be truncated




////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////