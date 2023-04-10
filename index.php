<?php
error_reporting (E_ALL ^ E_NOTICE);
// require_once ���������� ���� �������������� ��������� ��� �� ��������� ����, ���� �� �� �������� ��� �� ����������
// access.php
require_once('./inc/access.php');
//require_once('./global.php');

// require ('./inc/config.php'); - ������ ���� ���������� 
require ('./inc/config.php');

require ('./inc/api.php');
$tpl = new Template($template);

// ������� �����, �� ������ ���������� ����� <form method='post' action='index.php'> ������� �������� �������� ���������� � ������ $_POST. if (isset($_POST['username']) and isset($_POST['password'])){. ��������� username � password �������� �������� �� ������� $_POST, �� ������ ���� ��� ���� ���������.
if (isset($_POST['username']) and isset($_POST['password'])){
	$username = $_POST['username'];
	$password = $_POST['password'];

	// ����������� ���������� ���������� � ����� ���������. ���������� ��� ������������ � ������ ��������� �� ������ AD � ��������� ���������� �� ������ ������������.
	include ("./inc/LDAP/adLDAP.php");
    try {
			$adldap = new adLDAP();
    }
    catch (adLDAPException $e){
            echo $e; 
            exit();   
    }

	// authenticate - ������� ��������� ��������� ����� � ������ � ���� ��� �������� AD
	// user - Get the userclass interface
	// isset � ����������, ���� �� ����������� ���������� ���������, �������� �� null
	// strlen � ���������� ����� ������
	// substr � ���������� ���������
	// strpos � ���������� ������� ������� ��������� ���������
	if ($adldap->authenticate($username, $password)){
		$arr = $adldap->user()->info($username, array("*"));
		$_SESSION["username"] = $username;
		//setcookie("username", $username, time()+60*30);
		if (isset($arr[0]["mail"][0])) $_SESSION["mail"] = $arr[0]["mail"][0]; else $_SESSION["mail"] = "";
		if (isset($arr[0]["department"][0])) $_SESSION["department"] = $arr[0]["department"][0]; else $_SESSION["department"] = "";
		if (isset($arr[0]["displayname"][0])) $_SESSION["displayname"] = $arr[0]["displayname"][0]; else $_SESSION["displayname"] = "";
		$_SESSION["memberof"] = array();

		// ������ ������? ���� �**?
		foreach ($arr[0]["memberof"] as $memberof)
		{
			if (strlen($memberof) < 3) continue;
			$_SESSION["memberof"][] = substr($memberof, 3, strpos($memberof, ',')-strlen($memberof));
		}
		
		if (isset($_POST['submit_remember']) and ($_POST['submit_remember'] == 'on'))
		{	
			// �������� cookie � �������, ���������: �������� ����, �������� ����, ����� ��������.
			setcookie("username", $_SESSION["username"], time()+60*60*24*30);
			setcookie("mail", $_SESSION["mail"], time()+60*60*24*30);
			setcookie("department", $_SESSION["department"], time()+60*60*24*30);
			setcookie("displayname", $_SESSION["displayname"], time()+60*60*24*30);
			setcookie("memberof", implode(",", $_SESSION["memberof"]), time()+60*60*24*30);
		}
		
		// $dem_catalog = 'http://dem/';
		$redir = "Location: {$dem_catalog}";
		// header � �������� HTTP-���������, ������� ����� ������� ������ ���� ������� ��� �� ������������ ������.
		header($redir);
		exit();
	}
	else{
		$redir = "Location: {$dem_catalog}inc/templates/middle/login/loginfailed.html";
		header($redir);
	}
}
	
// ��������� �������� �� ����������� �������������
// ���� � ����� � � ���������� ������ �� ���� ����������� ��� ������������, �� ���������� ����� ��� ����������� ������������.
if (!isset($_COOKIE["username"]) and !isset($_SESSION["username"]))
{
	echo("
<HTML>
<HEAD>
<TITLE>�������</TITLE>
<META http-equiv=Content-Type content='text/html; charset=windows-1251'>
<META content='�������, ������, ������, ��������, ���������, ���, ��������' name=keywords>
<META content='�������. ������' name=description>
<META NAME='Document-state' CONTENT='Dynamic'>
<META NAME='Revizit-after' CONTENT='20 days'>
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'>
<META NAME='Classification' CONTENT='�������-����'>
<META NAME='Robots' CONTENT='index, follow'>
<META NAME='author' content='Programm'>
<META HTTP-EQUIV='Reply-to' content='simonov_as@demetra.ru'>
<META NAME='Copyright' content='demetra.ru �2008 All rights located in constitution RF.'>
<LINK rel='stylesheet' type='text/css' href='./inc/templates/middle/template_css.css' media='screen'>
<LINK rel='stylesheet' type='text/css' href='./inc/templates/middle/template_print.css' media='print'>
<LINK rel='stylesheet' type='text/css' href='./inc/templates/middle/redmond/jquery-ui.css'>
<script language='javascript' src='./inc/templates/middle/jquery.js'></SCRIPT>
<script language='javascript' src='inc/templates/middle/jquery-ui.js'></SCRIPT>
</HEAD>
<body class='body'><A name=top></A>

<TABLE cellSpacing=0 cellPadding=0 width='100%' align=left border=0>
<tr><td>

<TABLE class=header cellSpacing=0 cellPadding=0 width='300' align='center' border=0>
<TBODY>
 <TR>
  <TD vAlign=middle background='./pic/bg-dlinn-new.jpg' align=center width='100%' height=50>
  <TABLE width='' cellSpacing=0 cellPadding=0 border=0>
  <TBODY>
   <TR>
    <TD vAlign=middle ><a href='./'><IMG width=160 src='./pic/logo1.png' border=0 alt='�������'></IMG></TD>
    <TD vAlign='top' align='right' width='100%' height='50'>
    
    </TD>
   </TR>
  </TBODY>
  </TABLE>
  </TD>
 </TR>
</TBODY>
</TABLE>

</td></tr>
<tr><td>

<TABLE width='300' align='center' border=0>
<form method='post' action='index.php'>
<tr align='center'><td colspan='2'>��� ������� � DEM ���������� ������ ��� ������������ � ������, ������������ ��� ����� � Windows</td></tr>
<tr><td align='right'>������������:</td><td align='left'><input class='inputbox' type='text' name='username' value='' /></td></tr>
<tr><td align='right'>������:</td><td align='left'><input class='inputbox' type='password' name='password' /></td></tr>
<tr align='center'><td colspan='2'><input class='button' type='checkbox' name='submit_remember' /><span>&nbsp������� �������������</span></td></tr>
<tr align='center'><td colspan='2'><input class='button' type='submit' name='submit' value='�����' /></td></tr>
</form>
<tr align='center'><td colspan='2'>
<a href='http://z-dem:85/PapierloseFertigung/' target=''><img src='./pic/nano_images/menu_plf18x18.gif' style='padding-top: 5px;' /> ���� � ����������</a>
</td></tr>
<tr align='center'><td colspan='2'>
</td></tr>
</TABLE>

</td></tr>
<tr><td>

<DIV align=center><P>�2021</P></DIV>

</td></tr>
</table>
</BODY>
</HTML>	
	");
	
	exit;
}

// ���� ��� ����������� � ����� �� �������� �� �� ������
// _COOKIE - ��� ���������� ������� � ��������, ��� ������������� � id ������
// _SESSION - ���������� �������, ����� 15 �����, 
// explode � ��������� ������ � ������� �����������. � ������ ������ ������� ( , )
// iconv � ����������� ������ �� ����� ��������� �������� � ������
else{
	if(isset($_COOKIE["username"])){
		$_SESSION["username"] = $_COOKIE["username"];
		$_SESSION["displayname"] = $_COOKIE["displayname"];
		$_SESSION["department"] = $_COOKIE["department"];
		$_SESSION["mail"] = $_COOKIE["mail"];
		$_SESSION["memberof"] = explode(",", $_COOKIE["memberof"]);
	}	
	$username = $_SESSION["username"];
	if (isset($_SESSION["displayname"])) $displayname = iconv('UTF-8', 'CP1251', $_SESSION["displayname"]); else $displayname = "";
	if (isset($_SESSION["department"])) $department = iconv('UTF-8', 'CP1251', $_SESSION["department"]); else $department = "";	
	if (isset($_SESSION["mail"])) $usermail = $_SESSION["mail"]; else $usermail = "";
	$memberof = array();
	$memberof = $_SESSION["memberof"];
	// ����������� ���� ������ 
	// ���������� �� config.php 
	// $host = "192.168.3.39";
	// $user = "";
	// $pass = "";
	// $base = "demetra";
	// mysql_connect - ��������� ����� ���������� � �������� MySQL ��� ���������� ��� ������������.
	// mysql_select_db �������� ���� ������ MySQL.
	$link1 = mysql_connect($host, $user, $pass, $base);
	mysql_select_db($base, $link1);
	if (!$link1) die('mysql error in access pages');
	
	// ������ ��������? 
	// count � ������������ ���������� ��������� �������
	// strpos � ���������� ������� ������� ��������� ���������
	// mysql_query � �������� ������ MySQL
	// mysql_num_rows � ���������� ���������� ����� ���������� �������
	// mysql_fetch_assoc � ���������� ��� ���������� ������� � �������� �������������� �������
	$only_view = 1;
	for ($i=0; $i<count($memberof); $i++){
		if (strpos($memberof[$i], 'dem_') !== false){ 
			$sql = "SELECT * FROM `dem_access` WHERE ADgroup LIKE '{$memberof[$i]}'";
			$result = mysql_query($sql, $link1);
			if (mysql_num_rows($result) > 0){
				$access_rights = mysql_fetch_assoc($result);
				$only_view = 0;
				break;
			}
		}
	}

	//  ID ��������
	$enginer = 0;
	$query = "SELECT * FROM `demetra`.`dem_enginers` WHERE `enginer_eng` LIKE '{$username}'";
	$result = mysql_query($query, $link1);
	while ($row = mysql_fetch_assoc($result)){
		$enginer = $row['enginerid'];
	}
	mysql_close($link1);
}
//��������� ����� �����
if (!isset($_SESSION['JPY'])){get_curs();}
$curs = $_SESSION;
$usd = round($curs['USD']['VALUE'],3);


$center = "";
$info = "";
$constr = "";
$export = "";
$help = "";
$tree_menu = "";
$class_t = "";

if (isset ($_GET['class'])) $class_t = $_GET['class'];

if (isset ($_GET['module'])) $_SESSION['module'] = $_GET['module'];
if (isset($_SESSION['module'])) $module =  $_SESSION['module'];

// 
if (isset ($_GET['action_pos'])){
    include ('./inc/action_pos.php');
}

// ���� � �������� ������ ��������  �� ���� ����������� $_GET['action'] �� ������������ ��������� �����. 
if (!isset ($_GET['action'])){
	// main.php - ���� ��� ���������������
    include ('./inc/main.php');
	// news.php - ���������� �������
    include ('./inc/news.php');
    // include ('./inc/info.php');
}
else{
	// ��������� �� �������� ������.
    if ($_GET['action'] == 'orders'){
            //if($access_rights['admin']==1)
			// include ('./inc/orders/orders_test.php');
			//else
			include ('./inc/orders/orders_test.php');
            
			require ('./inc/help.php');//include ('./inc/info.php');
    }
    elseif ($_GET['action'] == 'pl'){
            include ('./inc/pap_lose/pap_lose.php');
           // include ('./inc/info.php');
    }
    elseif ($_GET['action'] == 'bz'){
            include ('./inc/bz/bz.php');
            include ('./inc/info.php');
    }
    elseif ($_GET['action'] == 'curency'){
            require ('./inc/curency.php');
    }
    elseif ($_GET['action'] == 'optima'){
            include ('./inc/optima.php');
            include ('./inc/info.php');
    }
    elseif ($_GET['action'] == 'news'){
            include ('./inc/news.php');
    }
    elseif ($_GET['action'] == 'export'){
            include ('./inc/constr.php');
    }
    elseif ($_GET['action'] == 'export_kl-gl'){
            include ('./inc/export_kl-gl.php');
    }
    elseif ($_GET['action'] == 'export_kl-gl-sp'){
            include ('./inc/export_kl-gl-sp.php');
    }
    elseif ($_GET['action'] == 'export_kl-gl-new'){
            include ('./inc/export_kl-gl.new.php');
    }
    elseif ($_GET['action'] == 'admin'){
            $title .= $title_sp."����������";
            //include ('./inc/admin/access.php');
            //if (isset ($_SESSION['allow'])){
			include ('./inc/admin/admin.php');
			include ('./inc/admin/menu.php');
			require ('./inc/help.php');
            //            }
    }
    elseif ($_GET['action'] == 'nelikvid'){
            include ('./inc/nelikvid.php');
            //include ('./inc/info.php');
    }
	elseif ($_GET['action'] == 'import-profstroy') {
			include ('./inc/iprofstroy.php');
	}
	elseif ($_GET['action'] == 'import-profstroy-1c') {
			include ('./inc/iprofstroy-1c.php');
	}
	elseif ($_GET['action'] == 'import-profstroy-send') {
			include ('./inc/iprofstroy-send.php');
	}		
	elseif ($_GET['action'] == 'import-profstroy-vo') {
			include ('./inc/iprofstroy-vo.php');
	}
	elseif ($_GET['action'] == 'import-profstroy-kis') {
			include ('./inc/profstroy_kis.php');
	}				
	elseif ($_GET['action'] == 'minidem') {
		if ($_GET['type'] == 'mat') include ('./inc/minidem/materials.php');
		if ($_GET['type'] == 'new') include ('./inc/minidem/neworder.php');
		if ($_GET['type'] == 'view') include ('./inc/minidem/vieworder.php');
	}
}
require ('./inc/menu_new.php');
//require ('./inc/online.php');
$online = '';
// eval � ��������� ��� PHP, ������������ � ������
eval("echo \"".$tpl->get("main_new")."\";");

?>