<?
error_reporting (E_ALL);

require_once('inc/access.php');
require_once('./global.php');
require ('inc/config.php');
require ('inc/api.php');
$tpl = new Template($template);
//˜˜˜˜˜˜˜˜˜ ˜˜˜˜˜ ˜˜˜˜˜
session_start();
if (!isset($_SESSION['JPY'])){get_curs();}
$curs = $_SESSION;
$usd = round($curs['USD']['VALUE'],3);


$center = "";
$info = "";
$constr = "";
$export = "";
$help = "";
$tree_menu = "";

if (isset ($_GET['class']))$class_t = $_GET['class'];

if (isset ($_GET['module'])) $_SESSION['module'] = $_GET['module'];
if (isset($_SESSION['module']))$module =  $_SESSION['module'];

if (isset ($_GET['action_pos'])){
    include ('inc/action_pos.php');
    }

if (!isset ($_GET['action'])){
    include ('inc/main.php');
    include ('inc/news.php');
    //       include ('inc/info.php');
    }
else {
    if ($_GET['action']=='orders'){
            include ('inc/orders/orders.php');
            require ('inc/help.php');//include ('inc/info.php');
            }
    elseif ($_GET['action']=='pl'){
            include ('inc/pap_lose/pap_lose.php');
           // include ('inc/info.php');
            }
    elseif ($_GET['action']=='bz'){
            include ('inc/bz/bz.php');
            include ('inc/info.php');
            }
    elseif ($_GET['action']=='curency'){
            require ('inc/curency.php');
            }
    elseif ($_GET['action']=='optima'){
            include ('inc/optima.php');
            include ('inc/info.php');
            }
    elseif ($_GET['action']=='news'){
            include ('inc/news.php');
            }
    elseif ($_GET['action']=='export'){
            include ('inc/constr.php');
            }
    elseif ($_GET['action']=='export_kl-gl'){
            include ('inc/export_kl-gl.php');
            }
    elseif ($_GET['action']=='export_kl-gl-new'){
            include ('inc/export_kl-gl.new.php');
            }
    elseif ($_GET['action']=='admin'){
            $title .= $title_sp."˜˜˜˜˜˜˜˜˜˜";
            include ('inc/admin/access.php');
            if (isset ($_SESSION['allow'])){
                        include ('inc/admin/admin.php');
                        include ('inc/admin/menu.php');
                               require ('inc/help.php');
                        }
            }
    elseif ($_GET['action']=='nelikvid'){
            include ('inc/nelikvid.php');
            //include ('inc/info.php');
            }
    }
require ('inc/menu_new.php');
//require ('inc/online.php');
$online = '';

eval("echo \"".$tpl->get("main_new")."\";");
?>