<?php
header('Content-Type: text/xml;charset=utf-8');
echo(utf8_encode("<?xml version='1.0' encoding='UTF-8' ?><options>"));

if (isset($_GET['debut'])) {
    $debut = utf8_decode($_GET['debut']);
} else {
    $debut = "";
}
$debut = strtolower($debut);
//On recupere la liste des entreprises dans la bdd

include_once("phpgwapi/js/fckeditor/fckeditor.php") ;
$GLOBALS['egw_info']=array();
$GLOBALS['egw_info']['flags']['noapi']=true;
$GLOBALS['egw_info']['flags']['currentapp']="spicademic";
require_once("../../../header.inc.php") ;

$connect=mysql_connect($GLOBALS['egw_domain']['default']['db_host'],$GLOBALS['egw_domain']['default']['db_user'],$GLOBALS['egw_domain']['default']['db_pass']) or die('Erreur');
mysql_select_db($GLOBALS['egw_domain']['default']['db_name'],$connect);
$req ="SELECT n_family, n_given, contact_email, contact_id FROM egw_addressbook WHERE LOWER(n_family) LIKE '$debut%' OR LOWER(n_given) LIKE '$debut%'";
$result = mysql_query($req);
$liste = array();
$i = 0;
while($data = mysql_fetch_array($result)){
	$liste[$i] = iconv('UTF-8', 'ISO-8859-1', $data[0].' '.$data[1].' '.$data[2]);
	++$i;
}
_debug_array($liste);
function generateOptions($debut,$liste) {
    $MAX_RETURN = 10;
    $i = 0;
    foreach ($liste as $element) {
        if ($i<$MAX_RETURN /*&& substr($element, 0, strlen($debut))==$debut*/) {
            echo(utf8_encode("<option>".$element."</option>"));
            $i++;
        }
    }
}

generateOptions($debut,$liste);

echo("</options>");
?>
