<?php
error_reporting(0);

session_start();
/**
 * Database mysql
 */
$db_host = "127.0.0.1";
$db_user = $db_pass = "root";
$db_name = "ctftraining";
$conn = mysql_connect($db_host, $db_user, $db_pass) or die('Could not connect: ' . mysql_error());

mysql_select_db($db_name, $conn) or die('Could select database: ' . mysql_error());

register_shutdown_function(function () {
	global $conn;
	mysql_close($conn);
});

function query($sql) {
	$result = mysql_query($sql);
	$rows = mysql_fetch_assoc($result);
	return $rows;
}
/**
 * Main
 */

$action = isset($_GET['action']) ? $_GET['action'] : "index";
if (!in_array($action, ['login', 'reg']) && !isset($_SESSION['username'])) {
	header("Location: /?action=login");
	exit;
}

switch ($action) {
case 'login':
	if (isset($_POST['username'])) {
		$username = addslashes($_POST['username']);
		$password = md5($_POST['password']);
		$res = query("select * from users where username='{$username}' and password='{$password}';");
		if ($res) {
			$_SESSION['username'] = $res['username'];
			header("Location: /?action=index");
			exit;
		}
	}
	?>
		<h1>Login</h1>
		<form action="/?action=login" method="POST">
			Username : <input type="text" name="username">
			<br>
			Password : <input type="password" name="password">
			<br>
			<a href="/?action=reg">Go to Register</a>
			<input type="submit" value="Login">
		</form>
	<?php

	break;
case 'reg':
	if (isset($_POST['username']) && $_POST['username'] != "") {
		$username = addslashes($_POST['username']);
		$password = md5($_POST['password']);
		if (mysql_query("insert into users(username,password,info) values ('{$username}','{$password}','十月太懒，没有简介');")) {
			header("Location: /?action=login");
		} else {
			header("Location: /?action=reg&msg=注册失败");
		}
		exit;
	} else {
		?>
		<h1>Register</h1>
		<div><?=addslashes($_GET['msg']);?></div>
		<br>
		<form action="/?action=reg" method="POST">
			Username : <input type="text" name="username">
			<br>
			Password : <input type="password" name="password">
			<br>
			<a href="/?action=login">Go to Login</a>
			<input type="submit" value="Register">
		</form>
	<?php
}
	break;
case 'change':
	if (isset($_POST['info'])) {
		$info = addslashes($_POST['info']);
		if (mysql_query("update users set info='{$info}' where username='{$_SESSION['username']}';")) {
			header("Location: /?action=index");
		} else {
			header("Location: /?action=change&msg=修改失败");
		}
		exit;
	}
	break;
case 'logout':
	session_destroy();
	header("Location: /?action=login");
	exit;
	break;
case 'index':
default:
	$info = query("select info from users where username='{$_SESSION['username']}';");
	?>
		<h1>Info</h1>
		<div><?=addslashes($info['info']);?></div>
		<hr>
		<div><?=addslashes($_GET['msg']);?></div>
		<br>
		<form action="/?action=change" method="POST">
			Info : <input type="text" name="info">
			<br>
			<input type="submit" value="Change">
		</form>
		<a href="/?action=logout">Logout</a>
	<?php
break;
}
echo "<!-- October nb!!!!! -->";