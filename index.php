<?php
error_reporting (E_ALL ^ E_NOTICE);
// require_once подключает фаил предварительно проверяет был ли подключен файл, если да то повторно его не подключает
// access.php
require_once('./inc/access.php');
//require_once('./global.php');

// require ('./inc/config.php'); - хранит кучу переменных 
require ('./inc/config.php');

require ('./inc/api.php');
$tpl = new Template($template);

// Обратка формы, на строке находиться форма <form method='post' action='index.php'> которая помещает значения переменных в массив $_POST. if (isset($_POST['username']) and isset($_POST['password'])){. Переемные username и password получают значения из массива $_POST, но только если они были присвоены.
if (isset($_POST['username']) and isset($_POST['password'])){
	$username = $_POST['username'];
	$password = $_POST['password'];

	// Подключение библиотеки работающий с актив директори. Полученные имя пользователя и пароль передаётся на сервер AD и проверяет существует ли данный пользователь.
	include ("./inc/LDAP/adLDAP.php");
    try {
			$adldap = new adLDAP();
    }
    catch (adLDAPException $e){
            echo $e; 
            exit();   
    }

	// authenticate - функция проверяет введенные логин и пароль с теми что хранятся AD
	// user - Get the userclass interface
	// isset — Определяет, была ли установлена переменная значением, отличным от null
	// strlen — Возвращает длину строки
	// substr — Возвращает подстроку
	// strpos — Возвращает позицию первого вхождения подстроки
	if ($adldap->authenticate($username, $password)){
		$arr = $adldap->user()->info($username, array("*"));
		$_SESSION["username"] = $username;
		//setcookie("username", $username, time()+60*30);
		if (isset($arr[0]["mail"][0])) $_SESSION["mail"] = $arr[0]["mail"][0]; else $_SESSION["mail"] = "";
		if (isset($arr[0]["department"][0])) $_SESSION["department"] = $arr[0]["department"][0]; else $_SESSION["department"] = "";
		if (isset($arr[0]["displayname"][0])) $_SESSION["displayname"] = $arr[0]["displayname"][0]; else $_SESSION["displayname"] = "";
		$_SESSION["memberof"] = array();

		// Массив членов? чего б**?
		foreach ($arr[0]["memberof"] as $memberof)
		{
			if (strlen($memberof) < 3) continue;
			$_SESSION["memberof"][] = substr($memberof, 3, strpos($memberof, ',')-strlen($memberof));
		}
		
		if (isset($_POST['submit_remember']) and ($_POST['submit_remember'] == 'on'))
		{	
			// отправка cookie в браузер, параметры: название куки, значение куки, время хранение.
			setcookie("username", $_SESSION["username"], time()+60*60*24*30);
			setcookie("mail", $_SESSION["mail"], time()+60*60*24*30);
			setcookie("department", $_SESSION["department"], time()+60*60*24*30);
			setcookie("displayname", $_SESSION["displayname"], time()+60*60*24*30);
			setcookie("memberof", implode(",", $_SESSION["memberof"]), time()+60*60*24*30);
		}
		
		// $dem_catalog = 'http://dem/';
		$redir = "Location: {$dem_catalog}";
		// header — Отправка HTTP-заголовка, функцию можно вызвать только если клиенту еще не передавались данные.
		header($redir);
		exit();
	}
	else{
		$redir = "Location: {$dem_catalog}inc/templates/middle/login/loginfailed.html";
		header($redir);
	}
}
	
// Проверяем пройдена ли авторизация пользователем
// Если в куках и в переменной сессии не было установлено имя пользователя, то показываем форму для авторизации пользователя.
if (!isset($_COOKIE["username"]) and !isset($_SESSION["username"]))
{
	echo("
<HTML>
<HEAD>
<TITLE>Деметра</TITLE>
<META http-equiv=Content-Type content='text/html; charset=windows-1251'>
<META content='Деметра, монтаж, расчёт, списание, материалы, ПВХ, алюминий' name=keywords>
<META content='Деметра. Расчёт' name=description>
<META NAME='Document-state' CONTENT='Dynamic'>
<META NAME='Revizit-after' CONTENT='20 days'>
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'>
<META NAME='Classification' CONTENT='Деметра-Софт'>
<META NAME='Robots' CONTENT='index, follow'>
<META NAME='author' content='Programm'>
<META HTTP-EQUIV='Reply-to' content='simonov_as@demetra.ru'>
<META NAME='Copyright' content='demetra.ru ©2008 All rights located in constitution RF.'>
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
    <TD vAlign=middle ><a href='./'><IMG width=160 src='./pic/logo1.png' border=0 alt='Деметра'></IMG></TD>
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
<tr align='center'><td colspan='2'>Для доступа к DEM необходимо ввести имя пользователя и пароль, используемые для входа в Windows</td></tr>
<tr><td align='right'>Пользователь:</td><td align='left'><input class='inputbox' type='text' name='username' value='' /></td></tr>
<tr><td align='right'>Пароль:</td><td align='left'><input class='inputbox' type='password' name='password' /></td></tr>
<tr align='center'><td colspan='2'><input class='button' type='checkbox' name='submit_remember' /><span>&nbspвходить автоматически</span></td></tr>
<tr align='center'><td colspan='2'><input class='button' type='submit' name='submit' value='Войти' /></td></tr>
</form>
<tr align='center'><td colspan='2'>
<a href='http://z-dem:85/PapierloseFertigung/' target=''><img src='./pic/nano_images/menu_plf18x18.gif' style='padding-top: 5px;' /> ВХОД В БЕЗБУМАЖКУ</a>
</td></tr>
<tr align='center'><td colspan='2'>
</td></tr>
</TABLE>

</td></tr>
<tr><td>

<DIV align=center><P>©2021</P></DIV>

</td></tr>
</table>
</BODY>
</HTML>	
	");
	
	exit;
}

// Если имя установлено в куках то передаем их на сервер
// _COOKIE - это переменная живущая в браузере, она привязывается к id сессии
// _SESSION - переменная сервера, живет 15 минут, 
// explode — Разбивает строку с помощью разделителя. В данном случае запятой ( , )
// iconv — Преобразует строку из одной кодировки символов в другую
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
	// Подключение базы банных 
	// Переменные из config.php 
	// $host = "192.168.3.39";
	// $user = "";
	// $pass = "";
	// $base = "demetra";
	// mysql_connect - Открывает новое соединение с сервером MySQL или использует уже существующее.
	// mysql_select_db Выбирает базу данных MySQL.
	$link1 = mysql_connect($host, $user, $pass, $base);
	mysql_select_db($base, $link1);
	if (!$link1) die('mysql error in access pages');
	
	// Только просмотр? 
	// count — Подсчитывает количество элементов массива
	// strpos — Возвращает позицию первого вхождения подстроки
	// mysql_query — Посылает запрос MySQL
	// mysql_num_rows — Возвращает количество рядов результата запроса
	// mysql_fetch_assoc — Возвращает ряд результата запроса в качестве ассоциативного массива
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

	//  ID инженера
	$enginer = 0;
	$query = "SELECT * FROM `demetra`.`dem_enginers` WHERE `enginer_eng` LIKE '{$username}'";
	$result = mysql_query($query, $link1);
	while ($row = mysql_fetch_assoc($result)){
		$enginer = $row['enginerid'];
	}
	mysql_close($link1);
}
//Получение курса валют
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

// Если в адресной строке браузера  не было установлено $_GET['action'] то подключаются следующие файлы. 
if (!isset ($_GET['action'])){
	// main.php - весь код закомментирован
    include ('./inc/main.php');
	// news.php - показывает новости
    include ('./inc/news.php');
    // include ('./inc/info.php');
}
else{
	// навигация по адресной строке.
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
            $title .= $title_sp."Админцентр";
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
// eval — Выполняет код PHP, содержащейся в строке
eval("echo \"".$tpl->get("main_new")."\";");

?>