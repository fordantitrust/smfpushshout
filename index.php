<?php
session_start();
require_once('../forum/smf_api.php');
smf_authenticateUser();
smf_loadSession();
smf_logOnline();
if(empty($smf_user_info['memberName'])) {
        die('<strong>Please <a href="http://www.thaithinkpad.com/forum">login to ThaiThinkPad</a></strong>');
};
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>My Status</title>
	<link rel="stylesheet" href="css/general.css" type="text/css" media="screen" />
</head>
<body>
	<div id="container">
		<ul class="menu">
			<li>
                <form method="post" id="form">
                    <p>
                        <input class="text" id="message" type="text" maxlength="140" />
                        <input id="send" type="submit" value="ส่งข้อความ !" /> <input id="update" type="button" value="แสดงข้อความใหม่ !" />
                        <span id="showtimer">30</span> : <span id="showupdatetime">0</span>/10
                    </p>
                </form>
            </li>
		</ul>
		<span class="clear"></span>
		<div class="content">
            <div id="loading">กำลังโหลดข้อมูล ...</div>
			<ul>
			<ul>
		</div>
	</div>
	<script type="text/javascript" src="jquery.js"></script>
	<script type="text/javascript" src="jquery.timer.js"></script>
    <script type="text/javascript" src="shoutbox.js"></script>
</body>
</html>
<!-- <iframe title="shoutbox" src="http://www.thaithinkpad.com/shoutbox/" frameborder="0" height="280" scrolling="auto" width="725"><br />
&amp;amp;amp;amp;amp;amp;amp;amp;lt;a href="http://www.thaithinkpad.com/shoutbox"&amp;amp;amp;amp;amp;amp;amp;amp;gt;View shoutbox&amp;amp;amp;amp;amp;amp;amp;amp;lt;/a&amp;amp;amp;amp;amp;amp;amp;amp;gt;<br />
</iframe> -->