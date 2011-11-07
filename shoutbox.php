<?php
/* **************************
 * First author this script
 * @author: Adrian "yEnS" Mato Gondelle & Ivan Guardado Castro
 * @website: www.yensdesign.com
 * @email: yensamg@gmail.com
 * @license: Feel free to use it, but keep this credits please!
 * **************************
 *
 * **************************
 * Secord author mod for
 * - improved code
 * - counting display (with 30 secord countdown)
 * - limit countdown (10 time)
 * - used memcached for cache data
 * - plugin to SMF (use smf_api.php, SMF 1.1.x API, http://download.simplemachines.org/?tools)
 * - link to profile in SMF
 * - store Name and User ID for SMF to database
 * - Lock for SMF user only
 * - No Guest
 *
 * @author: Ford AntiTrust
 * @website: www.thaicyberpoint.com
 * @email: annop@thaicyberpoint.com
 * @license: Feel free to use it, but keep this credits please!
 * @version 0.1a
 *
 *
 * Database Schema
 *
 * CREATE TABLE IF NOT EXISTS `shoutbox` (
 *  `id`        int(5) NOT NULL AUTO_INCREMENT,
 *  `date`      timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *  `userid`    int(11) NOT NULL,
 *  `username`  varchar(80) NOT NULL,
 *  `message`   varchar(1024) NOT NULL,
 *   PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 *
 * **************************
 */
session_start();
require_once('../forum/smf_api.php');
smf_authenticateUser();
smf_loadSession();
smf_logOnline();

if(empty($smf_user_info['memberName'])) {
    die('<li><strong>Please login</strong></li>');
};

$conf = array(
    'default_url_app' => '/forum'
);

$cacheConf = array(
    'host'              =>  'localhost',
    'port'              =>  '11211',
    'lifetime'          =>  (60*2),
    'cache_id_prefix'   => 'shout_ttp_'
);

/* @var $cache instant for Memcache OO API*/
$cache = new Memcache();

$cache->connect($cacheConf['host'], $cacheConf['port']) or die ("Could not connect");

/**
 * Get cache from memcached server
 * @param $k
 * @return mixed
 */
function cache_get($k) {

    global $cache, $cacheConf;

    return $cache->get($cacheConf['$cacheConf'].$k);
}

/**
 * Set cache to memcached server
 * @param $k
 * @param $v
 * @return bool
 */
function cache_set($k, $v){

    global $cache, $cacheConf;

    return $cache->set($cacheConf['$cacheConf'].$k, $v, false, $cacheConf['lifetime']);
}

/**
 * Remove cache from memcached server
 * @param $k
 * @return bool
 */
function cache_remove($k) {

    global $cache, $cacheConf;

    return $cache->delete($cacheConf['$cacheConf'].$k);
}

/**
 * Generate connection link to database server
 *
 * @param $host Hostname
 * @param $user Username for Database
 * @param $password Password for Database
 * @param $db_name Database Name
 * @return unknown_type
 */
function connect($host, $user, $password, $db_name){

	$link = @mysql_connect($host, $user, $password);
	if (!$link) {
	    die("Could not connect: ".mysql_error());
	} else {
		$host = mysql_select_db($db_name);
		if(!$host)
			die("Could not select database: ".mysql_error());
		else return $link;
	}
}

/**
 * Get resource from database server
 *
 * @param $link
 * @param $num number of rows for view
 * @return resource object
 */
function get_content($link, $num){
    $res = @mysql_query("SELECT id, date, userid, username, message FROM shoutbox ORDER BY date DESC LIMIT ".$num, $link);
    if(!$res) die("Error: ".mysql_error());
    return $res;
}
/**
 * Insert Message to database server
 *
 * @param $link
 * @param $userid User ID
 * @param $username Username
 * @param $message Message
 * @return resource object
 */
function insert_message($link, $userid, $username, $message){

    global $smf_user_info;

	$query = sprintf("INSERT INTO shoutbox(userid, username, message) VALUES('%s', '%s', '%s');",
	           mysql_real_escape_string(strip_tags($userid)),
	           mysql_real_escape_string(strip_tags($username)),
	           mysql_real_escape_string(strip_tags($message)));

	$res = @mysql_query($query, $link);
	if(!$res)
		die("Error: ".mysql_error());
	else {
        cache_remove($smf_user_info['ID_MEMBER']);
		return $res;
    }
}

/******************************
	MANAGE REQUESTS
/******************************/
if(!$_POST['action']){
	//We are redirecting people to our shoutbox page if they try to enter in our shoutbox.php
	header ("Location:".$conf['default_url_app']);
}
else{

    /**
     * Create $link var from connect function used configuration var in Settings.php in SMF
     * @var link
     */
	$link = connect($db_server, $db_user, $db_passwd, $db_name);

	switch($_POST['action']){

		case "update":
            if (!$result = cache_get($smf_user_info['ID_MEMBER'])) {
                #file_put_contents('log.txt', '1', FILE_APPEND );
			    $res = get_content($link, 20);
                while($row = mysql_fetch_array($res)){
                    $result .= "<li><span class=\"date\">".$row['date']."</span>&nbsp;".
                               "<strong><a href=\"".$conf['default_url_app']."/index.php?action=profile;u=".$row['userid']."\">".
                                stripslashes($row['username'])."<a/></strong> <img src=\"css/images/bullet.gif\" alt=\"-\" /> ".
                                stripslashes($row['message'])."</li>";
                }
                cache_set($result, $smf_user_info['ID_MEMBER']);
            }

			echo $result;
			break;

		case "insert":

			echo insert_message($link, $smf_user_info['ID_MEMBER'], $smf_user_info['memberName'],$_POST['message']);
			break;
	}
	/**
	 * close connction from $link var
	 */
	mysql_close($link);
}
?>