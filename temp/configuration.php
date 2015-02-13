<?php
$LOGIN_INFORMATION=array
    (
     'admin'=>'123',
     'facultad'=>'fcen2014',
     'fisica'=>'fisica2014',
     'matematicas'=>'matematicas2014',
     'biologia'=>'biologia2014',
     'quimica'=>'quimica2014',
     'profesor'=>'profesor2014',
     );

$INSTITUTOS=array
  (
   'admin'=>'Administrador',
   'facultad'=>'Facultad',
   'fisica'=>'Instituto de Física',
   'matematicas'=>'Instituto de Matemáticas',
   'biologia'=>'Instituto de Biología',
   'quimica'=>'Instituto de Química',
   'profesor'=>'Profesor'
   );

$USER="curriculo";
$PASSWORD="123";
$DATABASE="Curriculo";
if(!isset($_SERVER['SERVER_NAME'])){
$pass=<<<PASS
<?php
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
