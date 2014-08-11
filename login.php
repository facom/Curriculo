<?
include("etc/protect.php");
?>
<?
$query=preg_replace("/logout/","",$_SERVER["QUERY_STRING"]);
echo<<<CONTENT
<html>
<head>
  <META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php?$query">
</head>
</html>
CONTENT;
?>