<?
$LOGIN_INFORMATION=array('admin'=>'fcen2014','fisica'=>'fisica2014');
$USER="curriculo";
$PASSWORD="123";
$DATABASE="Curriculo";
if(!isset($_SERVER['SERVER_NAME'])){
$pass=<<<PASS
<?
\$PASS_INFORMATION=array(
PASS;
  foreach(array_keys($LOGIN_INFORMATION) as $key){
    $md5=md5($key.'%'.$LOGIN_INFORMATION["$key"]);
    $pass.="'$md5'=>'$key',";
  }
$pass=trim($pass,",");
$pass.=<<<PASS
);
?>
PASS;
  echo $pass;
}
?>
