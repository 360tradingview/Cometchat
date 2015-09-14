<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* ADVANCED */

define('SET_SESSION_NAME','');			// Session name
define('SWITCH_ENABLED','0');
define('INCLUDE_JQUERY','1');
define('FORCE_MAGIC_QUOTES','1');

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* DATABASE */

if(!file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'wp-config.php')) {
	echo "Please check if cometchat is installed in the correct directory.<br /> The 'cometchat' folder should be placed at <WORDPRESS_HOME_DIRECTORY>/cometchat";
	exit;
}
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'wp-config.php');

// DO NOT EDIT DATABASE VALUES BELOW
// DO NOT EDIT DATABASE VALUES BELOW
// DO NOT EDIT DATABASE VALUES BELOW

define('DB_SERVER',			"localhost"					);
define('DB_PORT',			"3306"					);
define('DB_USERNAME',			"xxx"					);
if(defined('USE_CCAUTH') && USE_CCAUTH == '0'){
define('TABLE_PREFIX',			"fyk_"				);
define('DB_USERTABLE',			"users"					);
define('DB_USERTABLE_USERID',		"ID"					);
define('DB_USERTABLE_NAME',		"user_nicename"				);
define('DB_AVATARTABLE',		" "					);
define('DB_AVATARFIELD',		" CONCAT(".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID.",'|',".TABLE_PREFIX.DB_USERTABLE.".user_email)");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* FUNCTIONS */


	function getUserID() {
		$userid = 0;

		if (!empty($_SESSION['basedata']) && $_SESSION['basedata'] != 'null') {
			$_REQUEST['basedata'] = $_SESSION['basedata'];
		}

		if (!empty($_REQUEST['basedata'])) {

			if (function_exists('mcrypt_encrypt') && defined('ENCRYPT_USERID') && ENCRYPT_USERID == '1') {
				$key = "";
				if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
					$key = KEY_A.KEY_B.KEY_C;
				}
				$uid = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode(rawurldecode($_REQUEST['basedata'])), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
				if (intval($uid) > 0) {
					$userid = $uid;
				}
			} else {
				$userid = $_REQUEST['basedata'];
			}
		}


		if (!isset($_SESSION['cometchat']['cookieval'])) {
			$sql = ("SELECT option_value FROM ".TABLE_PREFIX."options WHERE option_name = 'siteurl'");
			$result = mysqli_query($GLOBALS['dbh'],$sql);
			$row = mysqli_fetch_assoc($result);
			$_SESSION['cometchat']['cookieval'] = 'wordpress_logged_in_'.md5($row['option_value']);
		}

		/*if (isset($_COOKIE[$_SESSION['cometchat']['cookieval']])) {
			$username = explode("|", $_COOKIE[$_SESSION['cometchat']['cookieval']]);
			$sql = ("SELECT ID FROM ".TABLE_PREFIX.DB_USERTABLE." WHERE user_login = '".$username[0]."'");
			$result = mysqli_query($GLOBALS['dbh'],$sql);
			$row = mysqli_fetch_assoc($result);
			$userid = $row['ID'];
		}*/
		//$_COOKIE['wordpress_logged_in_a2cb823e277036974fb13f5ed7fb6d92']
		
		if (isset($_COOKIE['wordpress_logged_in_a2cb823e277036974fb13f5ed7fb6d92'])) {
			$username = explode("|", $_COOKIE[$_SESSION['cometchat']['cookieval']]);
			$sql = ("SELECT ID FROM ".TABLE_PREFIX.DB_USERTABLE." WHERE user_login = '".$username[0]."'");
			$result = mysqli_query($GLOBALS['dbh'],$sql);
			$row = mysqli_fetch_assoc($result);
			$userid = $row['ID'];
		}
		$userid = intval($userid);
$sql = ("SELECT membership_level_id FROM mm_user_data WHERE wp_user_id = '".$userid."' ");
		$result = mysqli_query($GLOBALS['dbh'],$sql);
		$row = mysqli_fetch_assoc($result);
		if($row['membership_level_id'] == '1' || $row['membership_level_id'] == '2' || $row['membership_level_id'] == '6' || $row['membership_level_id'] == '' || $row['membership_level_id'] == null){
			$userid = 0;
		}
		
		return $userid;
	}

	function chatLogin($userName,$userPass) {

		$userid = 0;
		global $guestsMode;
		if(!empty($_REQUEST['guest_login']) && $userPass == "CC^CONTROL_GUEST" && $guestsMode == 1) {
            if(!empty($_REQUEST['basedata']) && $_REQUEST['basedata'] != 'null') {
                $userid = getUserID();
                $sql = ("UPDATE `cometchat_guests` SET `name` = '".mysqli_real_escape_string($GLOBALS['dbh'], $userName)."', `lastactivity` = '".getTimeStamp()."' WHERE `id` = ".mysqli_real_escape_string($GLOBALS['dbh'], $userid));
                $query = mysqli_query($GLOBALS['dbh'], $sql);
            } else {
                $sql = ("INSERT INTO `cometchat_guests` (`name`, `lastactivity`) VALUES('".mysqli_real_escape_string($GLOBALS['dbh'], $userName)."','".getTimeStamp()."')");
                $query = mysqli_query($GLOBALS['dbh'], $sql);
                $userid = mysqli_insert_id($GLOBALS['dbh']);
            }
            if (isset($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
                $sql = ("insert into cometchat_status (userid,isdevice) values ('".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."','1') on duplicate key update isdevice = '1'");
                mysqli_query($GLOBALS['dbh'], $sql);
            }
        } else {
			include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'wp-includes'.DIRECTORY_SEPARATOR.'class-phpass.php');
			$hasher = new PasswordHash(8, false);
			if (filter_var($userName, FILTER_VALIDATE_EMAIL)) {
				$sql = ("SELECT * FROM ".TABLE_PREFIX.DB_USERTABLE." WHERE user_email = '".mysqli_real_escape_string($GLOBALS['dbh'],$userName)."'");
			} else {
				$sql = ("SELECT * FROM ".TABLE_PREFIX.DB_USERTABLE." WHERE user_login = '".mysqli_real_escape_string($GLOBALS['dbh'],$userName)."'");
			}
			$result = mysqli_query($GLOBALS['dbh'],$sql);
			$row = mysqli_fetch_assoc( $result );
			$check = $hasher->CheckPassword($userPass, $row['user_pass']);
			if ($check) {
				$userid = $row['ID'];
	            if (isset($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp') {
	                $sql = ("insert into cometchat_status (userid,isdevice) values ('".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."','1') on duplicate key update isdevice = '1'");
	                mysqli_query($GLOBALS['dbh'], $sql);
	            }
			}
		}
		if ($userid && function_exists('mcrypt_encrypt') && defined('ENCRYPT_USERID') && ENCRYPT_USERID == '1') {
			$key = "";
				if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
					$key = KEY_A.KEY_B.KEY_C;
				}
			$userid = rawurlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $userid, MCRYPT_MODE_CBC, md5(md5($key)))));
		}

	    return $userid;
	}

	function getFriendsList($userid,$time) {
		global $hideOffline;
		$offlinecondition = '';
		if ($hideOffline) {
			$offlinecondition = "where ((cometchat_status.lastactivity > (".mysqli_real_escape_string($GLOBALS['dbh'],$time)."-".((ONLINE_TIMEOUT)*2).")) OR cometchat_status.isdevice = 1) and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline')";
		}
		$sql = ("select DISTINCT ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".user_nicename link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." ".$offlinecondition." order by username asc");

		return $sql;
	}

	function getFriendsIds($userid) {

		$sql = ("select ".TABLE_PREFIX."friends.friend_user_id friendid from ".TABLE_PREFIX."friends where ".TABLE_PREFIX."friends.initiator_user_id = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."' and is_confirmed = 1 union select ".TABLE_PREFIX."friends.initiator_user_id friendid from ".TABLE_PREFIX."friends where ".TABLE_PREFIX."friends.friend_user_id = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."' and is_confirmed = 1");

		return $sql;
	}

	function getUserDetails($userid) {
		$sql = ("select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".user_nicename link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."'");

		return $sql;
	}

	function updateLastActivity($userid) {
		$sql = ("insert into cometchat_status (userid,lastactivity) values ('".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."','".getTimeStamp()."') on duplicate key update lastactivity = '".getTimeStamp()."'");
		return $sql;
	}

	function getUserStatus($userid) {
		 $sql = ("select cometchat_status.message, cometchat_status.status from cometchat_status where userid = '".mysqli_real_escape_string($GLOBALS['dbh'],$userid)."'");
		 return $sql;
	}

	function fetchLink($link) {
	    return '';
	}

	function getAvatar($data) {
	        if(!empty($data)) {
	            $data = explode('|',$data);
	            return 'http://www.gravatar.com/avatar/'.md5($data[1]).'?d=wavatar&s=80';
	        } else {
	            return BASE_URL.'images/noavatar.png';
	        }
	}

	function getTimeStamp() {
		return time();
	}

	function processTime($time) {
		return $time;
	}

	if (!function_exists('getLink')) {
	  	function getLink($userid) { return fetchLink($userid); }
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/* HOOKS */

	function hooks_statusupdate($userid,$statusmessage) {

	}

	function hooks_forcefriends() {

	}

	function hooks_activityupdate($userid,$status) {

	}

	function hooks_message($userid,$to,$unsanitizedmessage) {

	}

	function hooks_updateLastActivity($userid) {

	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* LICENSE */

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'license.php');
$x = "\x62a\x73\x656\x34\x5fd\x65c\157\144\x65";
eval($x('JHI9ZXhwbG9kZSgnLScsJGxpY2Vuc2VrZXkpOyRwXz0wO2lmKCFlbXB0eSgkclsyXSkpJHBfPWludHZhbChwcmVnX3JlcGxhY2UoIi9bXjAtOV0vIiwnJywkclsyXSkpOw'));

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////