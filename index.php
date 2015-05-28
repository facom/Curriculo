<?php
////////////////////////////////////////////////////
//VARIABLES GLOBALES
////////////////////////////////////////////////////
if(isset($_GET["pass"])){echo "Key:".md5($_GET["user"]."%".$_GET["pass"]);}
$TEMPLATECODE="0300000";
$QARCHIVO=0;//1 si quiere mostrar los cursos archivados
$QRECYCLE=1;//1 si quiere mostrar los cursos reciclados
$SITE="http://astronomia-udea.co/principal/Curriculo/index.php";
$DATADIR=".";
$LOGOUDEA="http://astronomia-udea.co/principal/sites/default/files";
session_start();
$SESSID=session_id();
$NAME=$_COOKIE["name"];
$TMPDIR="tmp";

////////////////////////////////////////////////////
//OBLIGA LOGIN EN CASO DE OPERACION DE PROFESOR
////////////////////////////////////////////////////
if(isset($_GET["profesor"])){
  $query=preg_replace("/&profesor/","",$_SERVER["QUERY_STRING"]);
echo<<<LOGIN
<html>
<head>
  <META HTTP-EQUIV="refresh" CONTENT="0;URL=login.php?$query"</head>
<body>
LOGIN;
return;
}
if(isset($_GET["phpinfo"])){
  phpinfo();
}
?>

<?php
////////////////////////////////////////////////////
//HEADER
////////////////////////////////////////////////////
echo<<<START
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <script src="etc/jquery.js"></script>
  <script>
  
  </script>
</head>
<body>
START;
?>

<?php
////////////////////////////////////////////////////
//CONFIGURATION
////////////////////////////////////////////////////
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
$H2PDF="../../temp/wkhtmltopdf-i386";
require("$ROOTDIR/etc/configuration.php");
require("$ROOTDIR/etc/database.php");

////////////////////////////////////////////////////
//DESBLOQUEO SALIR
////////////////////////////////////////////////////
if($_POST["accion"]=="Salir"){
  $curso=$_POST["F100_Codigo"];
  $coursedir="$DATADIR/data/$curso";
  $lockfile="$coursedir/.lock";
  shell_exec("rm -rf $lockfile");
  $_GET=array();
  $_POST=array();
  $_GET["planes_asignatura"]=1;
 }
if($_POST["accion"]=="Desbloquea"){
  $curso=$_POST["F100_Codigo"];
  $coursedir="$DATADIR/data/$curso";
  $lockfile="$coursedir/.lock";
  shell_exec("rm -rf $lockfile");
  $_POST["carga_curso"]=$curso;
  $_GET["carga_curso"]=$curso;
  //print_r($_GET);
}


////////////////////////////////////////////////////
//ENTRADAS
////////////////////////////////////////////////////
foreach(array_keys($_GET) as $field){
    $$field=$_GET[$field];
}
foreach(array_keys($_POST) as $field){
    $$field=$_POST[$field];
}
date_default_timezone_set('UTC-5');
$DATE=date(DATE_RFC2822);

////////////////////////////////////////////////////
//AUTHORIZATION
////////////////////////////////////////////////////
$ADMIN=0;
$QADMIN=0;
$QPROF=0;
if(isset($_COOKIE["verify"])){
  $verify=$_COOKIE["verify"];
  $ADMIN=$PASS_INFORMATION["$verify"];
  $INSTITUTO=$INSTITUTOS["$ADMIN"];
  if($INSTITUTO=="Facultad"){$QADMIN=3;}
  else if($INSTITUTO=="Profesor"){
    $QADMIN=1;$QPROF=1;
  }
  else if($INSTITUTO=="Administrador"){
    $INSTITUTO="Facultad";
    $QADMIN=4;
  }
  else{$QADMIN=2;}
}
$QAUTH=0;
if($INSTITUTO=="Facultad"){
  $QAUTH=1;
}

////////////////////////////////////////////////////
//ROUTINES
////////////////////////////////////////////////////
function generateSelection($values,$name,$value)
{
  $parts=preg_split("/,/",$values);
  $selection="";
  $selection.="<select name='$name' style=''>";
  foreach($parts as $part){
    $selected="";
    if($part==$value){$selected="selected";}
    $selection.="<option value='$part' $selected>$part";
  }
  $selection.="</select>";
  return $selection;
}

function isBlank($string)
{
  if(!preg_match("/[\w\d\W\D]+/",$string)) return 1;
  return 0;
}

function upAccents($string)
{
  $string=strtoupper($string);
  $accents=array("á"=>"Á","é"=>"É","í"=>"Í","ó"=>"Ó","ú"=>"Ú");
  foreach(array_keys($accents) as $acc){
    $string=preg_replace("/$acc/",$accents["$acc"],$string);
  }
  return $string;
}

////////////////////////////////////////////////////
//DATABASE
////////////////////////////////////////////////////
$db=mysqli_connect("localhost",$USER,$PASSWORD,$DATABASE);

function porcentajeCompletado($codigo)
{
  global $FIELDS,$DBASE,$DATADIR;
  $coursedir="$DATADIR/data/$codigo";
  include("$coursedir/notext.txt");
  $nfields=0;
  $vfields=0;
  $nocontent="(No contenido)";
  foreach($FIELDS as $field){
    $equivalent=1;
    $type=$DBASE[$field]["type"];
    $values=$DBASE[$field]["values"];
    if(preg_match("/AUTO/",$field) or
       preg_match("/AUTH/",$field) or 
       preg_match("/_Bibliografia/",$field) or
       preg_match("/DEPRECATED/",$values)){continue;}
    if($type!="text"){
      $value=$$field;
    }else{
      $file="$coursedir/$field.txt";
      $fl=fopen($file,"r");
      $value=fread($fl,filesize($file));
      $equivalent=10;
    }
    if(preg_match("/Unidad(\d+)_Titulo/",$field,$matchings)){
      //echo "F:$field, $value, $matchings[1]<br/>";
      if($matchings[1]=="1"){
	$nocontent="(No contenido)";
      }
      if(isBlank($value) and $matchings[1]>1){
	$nuni=$matchings[1]-1;
	$nocontent="(Contenido hasta unidad $nuni)";
      }
      if(isBlank($value)){
	break;
      }	
    }
    if(preg_match("/[\w\d]+/",$value) and
       !preg_match("/--/",$value)){
      $vfields+=$equivalent;
    }else{
      //echo "Field $field<br/>";
      //echo "Value: $value<br/>";
    }
    $nfields+=$equivalent;
    //echo "$nfields,$field,$value<br/>";
  }
  //echo "$vfields,$nfields<br/>";
  $p=(1.0*$vfields)/$nfields*100;
  return array($p,$nocontent);
}

////////////////////////////////////////////////////
//HEADER DEFINITION
////////////////////////////////////////////////////
if(!isset($edita_curso)){
$menu="";
$menu.="<a href=index.php>Principal</a>";
$menu.=" - <a href=index.php?planes_asignatura>Planes de Asignatura</a>";
if(!$QADMIN){
  $headbar="";
  $menu.=" - <a href=login.php>Conectarse</a>";
}
else{
  $headbar="<div style='background-color:lightgray;text-align:center;font-size:10px'>Usuario: $NAME como $ADMIN ($INSTITUTO) - Nivel: $QADMIN</div>";
  $menu.=" - <a href=login.php?logout>Desconectarse</a>";
}
if($QADMIN>1){
  $menu.=" - <a href=index.php?carga_curso=$TEMPLATECODE&edita_curso>Nuevo curso</a>";
}
$menu.="<hr/>";
 $a="a";
 }else{$menu="";$a="a";}

$header=<<<HEADER
$headbar
<table width=100% border=0>
<tr>
<td width=10%><image src="$LOGOUDEA/udea_fcen.jpg"/ height=120px></td>
<td valign=bottom>
  <b style='font-size:32'><$a href=index.php>Microcurriculos</a></b><br/>
  <b style='font-size:24'>Facultad de Ciencias Exactas y Naturales</b><br/>
  <b style='font-size:24'>Universidad de Antioquia</b><br/>
</td>
</table>
<hr/>
$menu
HEADER;
$errmsg=<<<ERR
$header
  <i style="color:red">Este contenido solo esta habilitado para un usuario autorizado</i>
ERR;

////////////////////////////////////////////////////
//PAGINA PRINCIPAL
////////////////////////////////////////////////////
if(count(array_keys($_GET))<1 and count(array_keys($_POST))<1){
/*
if(!$QADMIN){
  $headbar="";
$admin=<<<ADMIN
  <li>
    <b>
      <a href='login.php'>Conectarse como Administrador</a>.
    </b>
    Si es administrador aquí podrá conectarse para realizar tareas de
    edición.
  </li>
ADMIN;
 }else{
  $headbar="<div style='background-color:lightgray;text-align:center;font-size:10px'>ADMINISTRADOR: $ADMIN</div>";
$admin=<<<ADMIN
  <li>
    <b>
      <a href='login.php?logout'>Desconectarse</a>.
    </b>
  Esta conectado como administrador (usuario: <b>$ADMIN</b>).  Use sus atributos con responsabilidad.
  </li>
ADMIN;
 }
*/

echo<<<MAIN
$header
<p style='font-size:16'>

Bienvenido al sistema de Microcurriculos de la Facultad de
Ciencias Exactas y Naturales.<br/><br/>

En este sitio encontrará información sobre los microcurriculos (planes de asignatura) de los cursos de los distintos programas de estudio de la Facultad.<br/><br/>

De acuerdo a sus necesidades escoja una de las siguientes opciones:
<ul>

  <li>
    <b>
      <a href='?planes_asignatura'>Planes de Asignatura</a>.
    </b>
    Aquí podrá ver (o editar en caso de ser administrador) los planes
    de asignatura de todos los cursos de la Facultad.
  </li>

  $admin
</ul>

<i>Toda la información consignada aquí es de caracter informativo y ha
sido publicada para facilitar su acceso desde cualquier lugar.  Los
documentos originales y aprobados por las autoridades de la
Universidad deben consultarse en sus fuentes originales.  Algunos de
esos documentos podrían tener cambios respecto a los publicados
aquí.</i>

</p>
MAIN;
}

////////////////////////////////////////////////////
//LISTA DE CURSOS
////////////////////////////////////////////////////
if(isset($_GET["planes_asignatura"])){

  //==================================================
  //OPERACIONES GLOBALES
  //==================================================
  if($QADMIN>=4){
    $resultado="<p style='color:blue;font-style:italic'>";
    if(isset($unlock_all)){
      $resultado.="Todos los cursos desbloqueados";
      shell_exec("find $DATADIR/data/ -name '.lock' -exec rm {} \\; &> /tmp/a.log");
    }
    if(isset($clean_recycle)){
      $sql="truncate table MicroCurriculos_Recycle;";
      if(!mysqli_query($db,$sql)){
	die("No se pudo limpiar la papelera:".mysqli_error($db));
      }
      shell_exec("rm -r $DATADIR/recycle/* &> $TMPDIR/recycle.log");
      $resultado.="Papelera vaciada.";
    }    
    if(isset($semestre_all)){
      $sql="update MicroCurriculos set F330_Semestre='$semestre_all'";
      if(!mysqli_query($db,$sql)){
	die("No se pudo cambiar el semestre:".mysqli_error($db));
      }
      $resultado.="Semestre cambiado exitosamente.";
    }
    $resultado.="</p>";
  }

  //==================================================
  //CONTENT TABLE
  //==================================================
  $content="";
  $i=0;
  foreach(array_keys($INSTITUTOS) as $key){
    $indinst=$INSTITUTOS["$key"];
    if($indinst=="Profesor" or
       $indinst=="Administrador"){continue;}
    if($i>0){$content.=" - ";}
$content.=<<<CONTENT
  <a href='#$indinst'>$indinst</a>
CONTENT;
    $i++;
  }
  $content.="<p></p>";

  //==================================================
  //LISTA DE PLANES
  //==================================================
  $page="$header";
  $publicos="";
  $privados="";
  foreach(array_keys($INSTITUTOS) as $key){
    $instituto=$INSTITUTOS["$key"];
    if($instituto=="Profesor" or
       $instituto=="Administrador"){continue;}
    $listapub="";
    $listapriv="";
    $sql="select F100_Codigo,F110_Nombre_Asignatura,F280_Instituto,F060_AUTH_Publica_Curso,F010_AUTO_Fecha_Actualizacion,F015_AUTO_Usuario_Actualizacion,F050_Nombre_Actualiza,F020_AUTH_Autorizacion_Vicedecano,F330_Semestre_Plan,F330_Semestre from MicroCurriculos where F280_Instituto='$instituto' order by F330_Semestre_Plan*1,F100_Codigo,F110_Nombre_Asignatura;";
    if(!($out=mysqli_query($db,$sql))){
      die("Error:".mysqli_error($db));
    }
    $lista="";
    while($row=mysqli_fetch_array($out)){
      $codigo=$row[0];
      $nombre=$row[1];
      $instituto=$row[2];
      $publica=$row[3];
      $actualizacion=$row[4];
      $usuario=$row[5];
      $modifica=$row[6];
      $autorizacion=$row[7];
      $semestre=$row[8];
      $semestreactual=$row[9];
      $ps=porcentajeCompletado($codigo);
      $p=$ps[0];
      $n=$ps[1];
      $porcentaje_text=round($p,0)."% $n";
      $width=100;
      $wbar=$width*($p/100);
      if($p<10){$barcolor="pink";}
      else if($p<50){$barcolor="yellow";}
      else if($p<80){$barcolor="lightblue";}
      else{$barcolor="lightgreen";}
$procentaje_bar=<<<PORCENTAJE
  <div style="width:${width}px;border:solid black 1px;position:relative;display:inline-block">
  <div style="width:${wbar}px;color:white;background-color:$barcolor;border-right:solid black 1px;">-</div>
  </div>
PORCENTAJE;
      $lockfile="$DATADIR/data/$codigo/.lock";
      $lock="";
      if(file_exists($lockfile)){
	$props=file($lockfile);
	$lock="<i style='color:red'>El curso esta siendo editado desde $props[0].</i><br/>";
      }
      if($publica=="Si"){
$listapub=<<<LISTA
<li>
<a href='?ver_curso=$codigo&mode=Todos'>$nombre - $codigo</a>
LISTA;
	if($QADMIN and ($instituto=="$INSTITUTO" or $INSTITUTO=="Facultad")){
	  $listapub.=" - <a href='?carga_curso=$codigo&edita_curso&profesor' ttarget='_blank'>Editar</a>";
	}
	$listapub.="</li>";
      }
      $enlace="";$editar="";
      if($QADMIN and ($instituto=="$INSTITUTO" or $INSTITUTO=="Facultad")){
	$link="$SITE?carga_curso=$codigo&edita_curso&profesor";
	$editar=" <sup><a href='?carga_curso=$codigo&edita_curso&profesor' ttarget='_blank'>Editar</a></sup>";
	$enlace="Enlace para enviar al profesor: <i style='background-color:lightgray;padding:0px;'><a href='$link'>$link</a></i>";
      }
$listapriv.=<<<LISTA
<li>
  <a href='?ver_curso=$codigo&mode=Todos'><b>$nombre - $codigo</b></a>$editar<br/>
  <i style="text-decoration:underline">Última actualización</i>: $actualizacion - $usuario - $modifica <br/>
  <i style="text-decoration:underline">Revisado y Aprobado</i>: $autorizacion<br/>
  <i style="text-decoration:underline">Porcentaje completado</i>: $procentaje_bar $porcentaje_text <br/>
  <i style="text-decoration:underline">Semestre en el Plan</i>: $semestre <br/>
  <i style="text-decoration:underline">Semestre Actual</i>: $semestreactual <br/>
  <i style="text-decoration:underline">Historia de cambios</i>: <a href="$DATADIR/data/$codigo/changes.log" target="_blank">changes.log</a> <br/>
  $enlace<br/>
  $lock
LISTA;
 $listapriv.="</li><br/>";
    }
    //LISTA PUBLICOS
    if(!preg_match("/\w+/",$listapub)){$listapub="<i>(No se encontraron cursos)</i>";}
    else{$listapub.="</ul>";}
    //LISTA NO PUBLICOS
    if(!preg_match("/\w+/",$listapriv)){$listapriv="<i>(No se encontraron cursos)</i>";}
    else{$listapriv.="</ul>";}
    //MUESTRA LISTAS
    $publicos.="<h4>$instituto</h4><ul>$listapub</ul>";
    $privados.="<a name='$instituto'></a><h4>$instituto</h4><ul>$listapriv</ul>";
  }

  $page.="<h2>Lista de Cursos Públicos</h2>$publicos";

  if($QADMIN>=2){
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //OEPRACIONES GLOBALES
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    if($QADMIN>=4){
$page.=<<<GLOBALES
<hr/>
<h2>Operaciones Globales</h2>
<form>
<input type="hidden" name="planes_asignatura" value="">
$resultado
<ul>
  <li><a href=?planes_asignatura&unlock_all>Desbloquea todos</a></li>
  <li><a href=?planes_asignatura&clean_recycle>Limpia la papelera de reciclaje</a></li>
  <li>
    Cambia todos a semestre: 
    <input type="text" name="semestre_all" value="2014-2" size=8>
    <input type="submit" name="accion_global" value="Cambia">
  </li>
<!--
  <li>
  Filtra cursos:
  <input type="text" name="filtra_all" value="" size=8>
  <input type="submit" name="accion_global" value="Filtra"><br/>
  Use sintaxis de SQL.  <i>Ejemplo:</i>
-->
</ul>
</form>
GLOBALES;
    }
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //NOT PUBLIC COURSES
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    $page.="<h2>Todos los Cursos</h2>$content$privados";

    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //RECYCLE BIN
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    if($QRECYCLE){
      $sql="select F100_Codigo,F110_Nombre_Asignatura,F280_Instituto from MicroCurriculos_Recycle;";
      $out=mysqli_query($db,$sql);
      $recycle="";
      while($row=mysqli_fetch_array($out)){
	$codigo=$row[0];
	$nombre=$row[1];
	$instituto=$row[2];
$recycle=<<<RECYCLE
  <li>$instituto - $nombre - $codigo (<a href='?carga_curso=$codigo&edita_curso&recover'>Recuperar</a>)</li>
RECYCLE;
      }
      if(!preg_match("/\w+/",$recycle)){$recycle="<i>(No se encontraron cursos)</i>";}
      else{$recycle.="</ul>";}
      $page.="<hr/><h2>Papelera de reciclaje</h2><ul>$recycle</ul>";
    }

    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //FILE BIN
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    if($QARCHIVO){
      $out=shell_exec("cd archive/;ls -md *");
      $courses=preg_split("/\s*,\s*/",$out);
      $archive="";
      foreach($courses as $course){
	$course=trim($course);
	if(isBlank($course)){continue;}
	$nombre=shell_exec("grep Nombre_Asignatura archive/$course/notext.txt | cut -f 2 -d '\"'");
	$archive.="<li>$nombre - $course (<a href='?carga_curso=$course&edita_curso&archive'>Desarchiva</a>)</li>";
      }
      if(!preg_match("/\w+/",$archive)){$archive="<i>(No se encontraron cursos)</i>";}
      $page.="<hr/><h2>Archivo en disco</h2><ul>$archive</ul>";
    }
  }
	
  echo $page;
}

////////////////////////////////////////////////////
//ACCIONES
////////////////////////////////////////////////////
$result="";
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//CARGA UN CURSO GUARDADO
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if(isset($carga_curso) and $QADMIN){
  if(isset($archive)){
    include("archive/$carga_curso/notext.txt");
    $result="<i style='color:blue'>Curso $carga_curso desarchivado exitosamente.</i>";
  }else{
    $table="MicroCurriculos";
    if(isset($recover)){
      $table="MicroCurriculos_Recycle";
    }
    $sql="select * from $table where F100_Codigo='$carga_curso';";
    $out=mysqli_query($db,$sql);
    if(!($row=mysqli_fetch_array($out))){
      die("Error:".mysqli_error($db));
    }else{
      $result="<i style='color:blue'>Curso $carga_curso cargado exitosamente.</i>";
    }
    foreach($FIELDS as $field){
      $type=$DBASE[$field]["type"];
      if($type=="text"){continue;}
      $$field=$row["$field"];
    }
    if(isset($recover)){
      //REMOVE ENTRY
      $sql="delete from MicroCurriculos_Recycle where F100_Codigo=\"$carga_curso\";";
      if(!mysqli_query($db,$sql)){
	die("Error:".mysqli_error($db));
      }
      shell_exec("rm -rf recycle/$carga_curso");
    }
  }

  //CARGANDO TEXT INFORMATION
  $coursedir="$DATADIR/data/$carga_curso";
  if(isset($recover)){$coursedir="recycle/$carga_curso";}
  if(isset($archive)){$coursedir="archive/$carga_curso";}
  foreach($FIELDS as $field){
    $value=$$field;
    $type=$DBASE[$field]["type"];
    if($type!="text"){continue;}
    $file="$coursedir/$field.txt";
    $fl=fopen($file,"r");
    $$field=fread($fl,filesize($file));
    fclose($fl);
  }

 }else if(!$QADMIN and isset($carga_curso)){echo $errmsg;return;}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//CARGA UN CURSO GUARDADO
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if(($accion=="Guardar" or $accion=="Reciclar"  or $accion=="Archivar") and $QADMIN){
  $name="F100_Codigo";
  $codigo=$$name;
  $name="F280_Instituto";
  $inst=$$name;
  $name="F330_Semestre_Plan";
  $semplan=$$name;
  $result="";

  ////////////////////////////////////////////////////
  //NEW COURSE VERIFICATIONS
  ////////////////////////////////////////////////////

  //ATTEMPTING TO SAVE TEMPLATE COURSE
  //echo "QNEW=$qnew<br>";
  if($semplan>10 and $QADMIN<4){
    $mensaje="<i style='color:red'>El semestre en el plan debe ser menor o igual que 10.</i>";
    $result.=$mensaje;
    $msg=$mensaje;
    goto end_archive;
  }
  if($codigo==$TEMPLATECODE and $QADMIN<4){
    $result.="<i style='color:red'>No se puede guardar el curso con el código $codigo.</i>";
    $msg="<br/><i style='color:red'>No se puede guardar el curso con el código $codigo.</i>";
    goto end_archive;
  }
  if($inst=="--" and $QADMIN<4){
    $result.="<i style='color:red'>Debe escoger un Instituto o Dependencia.</i>";
    $msg="<br/><i style='color:red'>Debe escoger un Instituto o Dependencia.</i>";
    goto end_archive;
  }
  
  //CHECKING IF COURSE IS NEW
  if($qnew=="1"){
    $result.="<i style='color:red'>El curso $codigo es nuevo.</i><br/>";
    $coursedir="$DATADIR/data/$codigo";
    if(file_exists("$coursedir") and $QADMIN<4){
      $result.="<i style='color:red'>El curso con codigo $codigo ya existe.</i>";
      $msg="<br/><i style='color:red'>El curso con codigo $codigo ya existe.</i>";
      goto end_archive;
    }
  }else{
    $result.="<i style='color:green'>El curso $codigo existe.</i><br/>";
  }

  if($accion=="Archivar" or 1){
    if(file_exists("archive/$codigo")){
      shell_exec("rm -rf archive/$codigo");
    }
    shell_exec("cp -rf $DATADIR/data/$codigo archive/");
    if($accion=="Archivar"){
      $result.="<i style='color:green'>Curso archivado exitosamente.</i>";
      goto end_archive;
    }
  }
  $table="MicroCurriculos";
  if($accion=="Reciclar"){
    $table="MicroCurriculos_Recycle";
  }
  if($accion=="Publicar"){
    $table="MicroCurriculos_Publicos";
  }
  ////////////////////////////////////////////////////
  //GUARDANDO REGISTRO
  ////////////////////////////////////////////////////
  //INSERT IF NOT EXISTS
  $name="F100_Codigo";
  $codigo=$$name;
  $sql="insert into $table (F100_Codigo) values (\"$codigo\") on duplicate key update F100_Codigo=\"$codigo\"";
  if(!mysqli_query($db,$sql)){
    die("Error:".mysqli_error($db));
  }
  //UPDATE IF EXISTS
  $sql="update $table set ";
  foreach($FIELDS as $field){
    $type=$DBASE[$field]["type"];
    if($field=="F100_Codigo" or $type=="text"){continue;}
    $value=$$field;
    if(preg_match("/AUTO/",$field)){
      if(preg_match("/_Fecha/",$field)){
	$value=$DATE;
      }
      if(preg_match("/_Usuario/",$field)){
	$value=$INSTITUTO;
      }
      if(preg_match("/_Version/",$field)){
	$value=$value+1;
      }
    }
    $sql.="$field='$value',";
  }
  $sql=trim($sql,",");
  $name="F100_Codigo";
  $codigo=$$name;
  $sql.=" where F100_Codigo='$codigo';";
  if(!mysqli_query($db,$sql)){
    die("Error:".mysqli_error($db));
  }else if($accion!="Reciclar"){
    $ps=porcentajeCompletado($codigo);
    $p=$ps[0];
    $n=$ps[1];
    $porcentaje=round($p,0)."% $n";
    $result.="<i style='color:blue'>Registro guardado exitosamente ($porcentaje completado).</i>";
    $qnew=0;
  }
  if($accion=="Reciclar"){
    $sql="delete from MicroCurriculos where F100_Codigo=\"$codigo\";";
    if(!mysqli_query($db,$sql)){
      die("Error:".mysqli_error($db));
    }else{
      $result.="<i style='color:red'>Registro reciclado exitosamente.</i>";
    }
  }
  //SAVE TEXT FIELDS
  $coursedir="$DATADIR/data/$codigo";
  if($accion=="Reciclar"){
    shell_exec("rm -rf $coursedir");
    $coursedir="recycle/$codigo";
  }
  system("mkdir -p \"$coursedir\"");
  $fc=fopen("$coursedir/notext.txt","w");
  //echo "COURSE DIR: $fc<br/>";
  fwrite($fc,"<?php\n");
  foreach($FIELDS as $field){
    $value=$$field;
    $type=$DBASE[$field]["type"];
    if($type!="text"){
      fwrite($fc,"\$$field=\"$value\";\n");
    }else{
      $fl=fopen("$coursedir/$field.txt","w");
      fwrite($fl,$value);
      fclose($fl);
    }
  }
  fwrite($fc,"?>\n");
  fclose($fc);
  if($accion=="Reciclar"){
    echo "$header$menu$result";
    goto footer;
  }
 end_archive:
  $qarchive=1;
 }else if(!$QADMIN and ($accion=="Guardar" or $accion=="Reciclar")){echo $accion.$errmsg;return;}

////////////////////////////////////////////////////
//EDICIÓN DE UN CURSO
////////////////////////////////////////////////////
if(isset($edita_curso) and $QADMIN){

  $page="";

  //AUTORIZACION VICEDECANO
  $var="F020_AUTH_Autorizacion_Vicedecano";
  $auto=$$var;
  if(isBlank($auto)){
    $auto=$DBASE["F020_AUTH_Autorizacion_Vicedecano"]["default"];
  }
  if(isBlank($F280_Instituto) and $QADMIN<4){
    $F280_Instituto="$INSTITUTO";
  }
  //echo "AUTO:$auto<br/>";
  if(!$QAUTH and 
     $auto=="Si"){
echo<<<NOAUTH
$header
<p style='font-style:italic;color:red'>
  El curso $codigo no esta autorizado para edición por el Vicedecano.
</p>
NOAUTH;
 return;
  }

  if(isset($F100_Codigo)){
$verprograma=<<<VERPROGRAMA
<h3>
  <a href="?ver_curso=$F100_Codigo&mode=Todos" target="_blank">
    Ver programa
  </a>
</h3>
VERPROGRAMA;
 $Accion=$accion;
 if(isBlank($Accion)){$Accion="Edita";}
 shell_exec("echo '$DATE;$SESSID;$ADMIN;$NAME;$F100_Codigo;$Accion' >> $DATADIR/data/$F100_Codigo/changes.log");
  }else{$verprograma="";}

  $display="none";
  $bcolor="white";
  if($accion=="Guardar"){$display="block";$bcolor="pink";}
$buttons=<<<BUTTONS
<div style='position:fixed;right:0px;'>
<div id='recuerdo' style='background-color:pink;display:$display;padding:10px;width:200px;font-style:italic'>
  Señor profesor/administrador: no olvide darle salir después de terminar.$msg
</div>
<input type='submit' name='accion' value='Guardar'>
<input id='salir' type='submit' name='accion' value='Salir' style='background-color:$bcolor;'>
BUTTONS;

 if($QADMIN>=2){
$buttons.=<<<BUTTONS
<input type='submit' name='accion' value='Reciclar'>
<!--<input type='submit' name='accion' value='Archivar'>-->
BUTTONS;
 }

$buttons.=<<<BUTTONS
</div>
<br/><br/>
BUTTONS;

  //ARCHIVO DE BLOQUEO
 if(!isset($qnew)){$qnew=0;}
 if(isset($carga_curso)){
   $curso_lock=$carga_curso;
 }else if(isset($F100_Codigo)){
   $curso_lock=$F100_Codigo;
 }else{
   $curso_lock=$TEMPLATECODE;
 }
 if($curso_lock==$TEMPLATECODE){$qnew=1;}

 $coursedir="$DATADIR/data/$curso_lock";
 $lockfile="$coursedir/.lock";
 //echo "$lockfile<br/>";
 if(file_exists($lockfile)){
     $props=file($lockfile);
     if(trim($props[2])!=$SESSID){
$result.=<<<RESULT
<br/><br/>

<div style='color:red;background-color:yellow;padding:10px;width:600px'>
  Archivo de bloqueo existente (<b>$props[1]</b>, <b>$props[0]</b>).
  Esto indica que otro usuario esta editando este curso en este
  momento.  También puede indicar que el último usuario que lo dejo no
  presionó el botón de salir al términar.  Revise la hora del último
  bloqueo (arriba en negrilla).  Si es de hace mucho la recomendación
  es desbloquear manualmente el archivo con el botón 'Desbloquea' a la
  derecha.  Use esta opción con precaución.  También puede esperar un
  poco e intentar cargar esta página mas tarde.
</div>

  <br/>

RESULT;
    $buttons="";
$buttons=<<<BUTTONS
<div style='position:fixed;right:0px;'>
<input type='submit' name='accion' value='Desbloquea'>
</div>
<br/><br/>
BUTTONS;
     }
 }else{
   shell_exec("date > $coursedir/.lock");
   shell_exec("echo $INSTITUTO >> $coursedir/.lock");
   shell_exec("echo $SESSID >> $coursedir/.lock");
 }

$page.=<<<FORM
$header
  <h2>Edición de Plan de Asignatura</h2>
  <a name="principio"></a>
  <h3>$F110_Nombre_Asignatura $F100_Codigo</h3>
  $verprograma
<div>
$result
</div>
<form id="form" action="index.php#principio" method="post">
<input type='hidden' name='edita_curso' value=1>
<input type='hidden' name='qnew' value=$qnew>
FORM;

//$page.=$buttons;
 $form="";
$form=<<<FORM
<a id="mostrar" href="JavaScript:void(null)" onclick="$('.hidden').toggle('fast',null);" style="font-size:12px">Mostrar/Ocultar todas las ayudas</a><br/><br/>
FORM;

  //CONTENT TABLE
$content.=<<<CONTENT
<a id="mostrar_enlaces" href="JavaScript:void(null)" onclick="$('#enlaces').toggle('fast',null);" style="font-size:12px">Mostrar/Ocultar Enlaces Rápidos</a>
<div id="enlaces" style='display:none;font-size:12px;background-color:lightgray;padding:5px'>
CONTENT;
  $i=0;
  foreach($FIELDS as $field){
    if(preg_match("/AUTH/",$field) or
       preg_match("/DEPRECATED/",$DBASE[$field]["values"])){
      continue;
    }
    $fname=$field;
    $fname=preg_replace("/F\d+_/","",$fname);
    $fname=preg_replace("/AUTO_/","",$fname);
    $fname=preg_replace("/_/"," ",$fname);
    if($i>0){$content.=" - ";}
$content.=<<<CONTENT
  <a href='#$field'>$fname</a>
CONTENT;
    $i++;
  }
  $content.="</div><p></p>";
  $form.="$content";

  foreach($FIELDS as $field){
    $onfocus="onfocus=\"$('#field_$field').css('background-color','lightgreen');$('#form').attr('action','index.php#$field');\"";
    $onblur="onblur=\"$('#field_$field').css('background-color','white')\"";
    $id="id='field_$field'";
    $value=$$field;
    $query=$DBASE[$field]["query"];
    $type=$DBASE[$field]["type"];
    $default=$DBASE[$field]["default"];
    $values=$DBASE[$field]["values"];
    if(!preg_match("/[\w\d]+/",$value)){
      $value=$default;
    }
    $values=$DBASE[$field]["values"];
    $help=$DBASE[$field]["help"];
    $ejemplo=$DBASE[$field]["ejemplo"];
    $help=preg_replace("/\n/","<br/>",$help);
    $ejemplo=preg_replace("/\n/","<br/>",$ejemplo);
    //echo "FIELD:-$field-$value-<br/>";
    //BLOCK
    $block="";
    $qauth=0;
    $display="block";
    
    //CAMPOS DEPRECATED
    if(preg_match("/DEPRECATED/",$values)){continue;}

    //QUIEN ACTUALIZA
    if(preg_match("/Nombre_Actualiza/",$field)){
      $value=$NAME;
      $block="disabled";
    }

    //CAMPOS OCULTOS
    if(preg_match("/AUTH/",$field) and !$QAUTH){
      $input="$value<input type='hidden' name='$field' value='$value'><br/>";
      $qauth=1;
      $display="none";
      //echo "AUTH<br/>";
    }
    if(preg_match("/AUTO/",$field) or
       (preg_match("/Codigo/",$field) and
	isset($F100_Codigo) and
	!isset($archive) and 
	$QPROF)
       ){
      $block="disabled";
      //echo "AUTO<br/>";
      /*
      if(preg_match("/_Fecha/",$field)){
	$value=$DATE;
      }
      if(preg_match("/_Usuario/",$field)){
	$value=$INSTITUTO;
      }
      if(preg_match("/_Version/",$field)){
	$value=$value+1;
      }
      */
    }    
    //CAMPOS DE ENTRADA SIMPLE
    if(!preg_match("/\w/",$values) and !$qauth){
      if(preg_match("/varchar\((\d+)\)/",$type,$matches)){
	$size=$matches[1];
	$input="<input $id $onfocus $onblur type='text' name='$field' value='$value' maxlength=$size size=$size $block>";
	if($block=="disabled"){
	  $input.="<input type='hidden' name='$field' value='$value'>";
	}
      }else if(!preg_match("/text/",$type)){
	$input="<input $id type='text' name='$field' value='$value' size=10 $block $onfocus $onblur>";
      }else{
	$input="<textarea $id name='$field' rows=10 cols=80 $onfocus $onblur>$value</textarea>";
      }
    }
    //CAMPOS DE TEXTO
    else if(!$qauth){
      $input=generateSelection($values,$field,$value);
      $input=preg_replace("/style=''/","style='' $id $onfocus $onblur",$input);
    }

$form.=<<<QUERY
<div style='display:$display'>
<a name="$field"></a>
<b>$query</b>
<sup>
<a href="JavaScript:void(null)" onclick="$('#help_$field').toggle('fast',null);" style="font-size:10px" tabIndex="-1">Ayuda</a>,
<a href="JavaScript:void(null)" onclick="$('#ejemplo_$field').toggle('fast',null);" style="font-size:10px" tabIndex="-1">Ejemplo</a>
</sup>
<br/>
$input

<!--AYUDA-->
<div class="hidden" id="help_$field" style="display:none;font-style:italic;background-color:lightblue;width:600px;padding:10px">
<div style="position:relative;left:560px;top:-5px">
<a href="JavaScript:void(null)" onclick="$('#help_$field').toggle('fast',null);" style="font-size:10px">Ocultar</a>
</div>
  $help
</div>

<!--EJEMPLO-->
<div class="hidden" id="ejemplo_$field" style="display:none;background-color:pink;width:600px;padding:10px">
<div style="position:relative;left:560px;top:-5px">
<a href="JavaScript:void(null)" onclick="$('#ejemplo_$field').toggle('fast',null);" style="font-size:10px">Ocultar</a>
</div>
  $ejemplo
</div>

<br/>
</div>

QUERY;
  }
  $page.="$buttons$form";
 echo $page;
}else if(!$QADMIN and isset($edita_curso)){echo $errmsg;return;}

////////////////////////////////////////////////////
//VER UN CURSO
////////////////////////////////////////////////////
if(isset($ver_curso)){
  $page="";
  $page.="$header";
  //RECUPERA INFORMACION DEL CURSO DE LA BASE DE DATOS
  $table="MicroCurriculos";
  $sql="select * from $table where F100_Codigo='$ver_curso';";
  $out=mysqli_query($db,$sql);
  if(!($row=mysqli_fetch_array($out))){die("Error:".mysqli_error($db));}

  //CARGA VALORES EN VARIABLES
  foreach($FIELDS as $field){
    $type=$DBASE[$field]["type"];
    if($type=="text"){continue;}
    $$field=$row["$field"];
  }

  //RECUPERA INFORMACIÓN DEL CURSO DE ARCHIVOS
  $coursedir="$DATADIR/data/$ver_curso";
  foreach($FIELDS as $field){
    $value=$$field;
    $fname=preg_replace("/^F\d+_/","",$field);
    //echo "$fname = $value<br/>";
    $$fname=$value;
    $type=$DBASE[$field]["type"];
    if($type!="text"){continue;}
    $file="$coursedir/$field.txt";
    $fl=fopen($file,"r");
    $$field=fread($fl,filesize($file));
    $value=$$field;
    $value=preg_replace("/\n/","<br/>",$value);
    $$fname=$value;
    //echo "$fname<br/>";
    fclose($fl);
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //INFORMACIÓN DEL CURSO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $border="border:1px solid;";
  $colorgray="background-color:lightgray";
  $heavygray="background-color:gray";
  $style="style='font-weight:bold' width=30% valign=top";
$page.=<<<TITULO
<h2>$Nombre_Asignatura</h2>
<table border=1 style='border-collapse:collapse;width:600px'>
  <tr><td $style>Código</td><td>$Codigo</td></tr>
  <tr><td $style>Última Actualización</td><td>$AUTO_Fecha_Actualizacion</td></tr>
  <tr><td $style>Número de Créditos</td><td>$Creditos</td></tr>
  <tr><td $style>Programas</td><td>$Programas_Academicos</td></tr>
  <tr><td $style>Prerrequisitos</td><td>$Requisitos</td></tr>
  <tr><td $style>Correquisitos</td><td>$Correquisitos</td></tr>
  <tr><td $style>Semestre en el plan</td><td>$Semestre_Plan</td></tr>
  <tr><td $style>Descripción</td><td>$Descripcion</td></tr>
  <tr><td $style>Formatos disponibles</td><td>
TITULO;

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //GENERA ARCHIVO EN FORMATO REQUERIDO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
 
 //==================================================
 //FORMATO PLANO
 //==================================================
 if($mode=="Plano" or $mode=="Todos"){
    $table="";
$table.=<<<TABLE
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<h1>
  $Nombre_Asignatura<br/>
  $Codigo
</h1>
TABLE;
    $table.="<table border=0 width=650 style='border-collapse:collapse;'>";
    foreach($FIELDS as $field){
      $fname=preg_replace("/^F\d+_/","",$field);
      $value=$$fname;
      //echo "FIELD: $fname<br/>VALUE: $value<br/>";
      $query=$DBASE[$field]["query"];
      $type=$DBASE[$field]["type"];
      if(preg_match("/Unidad\d+_Titulo/",$field) and 
	 !preg_match("/\w/",$value)){
	//echo "Last field: $field,$fname,$value<br/>";
	break;
      }
      if($type!="text"){
	$table.="<tr><td style='$border;$colorgray;' width=30%><b>$query</b></td>";
	$table.="<td style='$border' width=60%>$value</td></tr>";
      }else{
	$table.="<tr><td style='$border;$colorgray;' colspan=2><b>$query</b></td></tr>";
	$table.="<tr><td style='$border;' colspan=2>$value</td></tr>";
      }
    }
    $table.="</table></body></html>";
    $coursedir="$DATADIR/data/$ver_curso";
    $fl=fopen("$coursedir/$ver_curso-plano.html","w");
    fwrite($fl,$table);
    fclose($fl);
    if(file_exists("$coursedir/.$ver_curso-plano.pdf.md5sum")){
      shell_exec("cd $coursedir;md5sum $ver_curso-plano.html > /tmp/md5");
      $out=shell_exec("cd $coursedir;diff /tmp/md5 .$ver_curso-plano.pdf.md5sum");
    }else{$out="NEW";}
    if(!isBlank($out)){
      sleep(2);
      shell_exec("cd $coursedir;$H2PDF $ver_curso-plano.html $ver_curso-plano.pdf");
      shell_exec("cd $coursedir;md5sum $ver_curso-plano.html > .$ver_curso-plano.pdf.md5sum");
    }

    if(file_exists("$coursedir/$ver_curso-plano.pdf")){
      $filepdf="(<a href=$coursedir/$ver_curso-plano.pdf target=_blank>PDF</a>)";
    }else{
      $filepdf="";
    }
$page.=<<<DESCARGA
<a href=$coursedir/$ver_curso-plano.html target=_blank>Formato plano</a>
$filepdf
<br/>
DESCARGA;
  }
  
  if($mode=="FCEN" or $mode=="Todos"){
    //UNIDADES
    $unidades="";
    $offset=600;
    $ContenidoResumido="";
    $BibliografiaCompleta="";
    for($i=1;$i<=10;$i++){
      $n=$offset+10*($i-1);
      $var="Unidad${i}_Titulo";
      //echo "$var<br/>";
      $titulo=$$var;
      //echo "Unidad $i:$titulo<br/>";
      if(isBlank($titulo)){break;}
      $var="Unidad${i}_Conceptual";
      $conceptual=$$var;
      $var="Unidad${i}_Procedimental";
      $procedimental=$$var;
      $var="Unidad${i}_Actitudinal";
      $actitudinal=$$var;
      $var="Unidad${i}_Bibliografia";
      $bibliografia=$$var;
      $var="Unidad${i}_Semanas";
      $semanas=$$var;
      $semtxt="";
      if($semanas>0){
	$semtxt="($semanas semanas)";
      }
      
      if(!isBlank($conceptual)){
	$txtconcep="<p><i>Contenidos conceptuales:</i></p><blockquote>$conceptual</blockquote>";
      }else{$txtconcep="";}
      if(!isBlank($procedimental)){
	$txtproced="<p><i>Contenidos procedimentales:</i></p><blockquote>$procedimental</blockquote>";
      }else{$txtproced="";}
      if(!isBlank($actitudinal)){
	$txtactitud="<p><i>Contenidos actitudinales:</i></p><blockquote>$actitudinal</blockquote>";
      }else{$txtactitud="";}

      $BibliografiaCompleta.="$bibliografia";
$unidades.=<<<UNIDADES
  <b>Unidad $i. $titulo</b> $semtxt<br/>
  $txtconcep
  $txtproced
  $txtactitud
UNIDADES;
      $ContenidoResumido.="$i-$titulo<br/>";
    }
    $Contenido_Resumido=$ContenidoResumido;
    $table="";
$table.=<<<TABLE
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
  <table border=0 width=650>
    <tr>
      <td><img src="$LOGOUDEA/udea.jpg" width=100px/></td>
      <td style='text-align:center'>FACULTAD DE CIENCIAS EXACTAS Y NATURALES<br/>$Instituto</td>
    </tr>
  </table>
  <table border=0 width=650 style='border-collapse:collapse'>
    <tr><td width=50%></td><td width=10%></td><td width=20%></td><td width=10%></td><td></td></tr>
    <tr><td></td>
      <td colspan=4 style='text-align:center;$border;$colorgray'>
	APROBADO CONSEJO DE FACULTAD DE CIENCIAS EXACTAS Y NATURALES
      </td>
    </tr>
    <tr><td></td>
      <td style='$border;$colorgray;'>ACTA</td>
      <td style='$border;'>$AUTH_Acta_Numero</td>
      <td style='$border;$colorgray;'>DEL</td>
      <td style='$border;'>$AUTH_Acta_Fecha</td>
    </tr>
  </table>
  <p style='width:650px;text-align:center;'>FORMATO DE MICROCURRICULO O PLAN DE ASIGNATURA</p>
  <table border=0 width=650 style='border-collapse:collapse'>
  <thead>
    <tr>
      <td width=30%></td><td width=20%></td><td width=20%></td><td width=20%></td>
    </tr>
  </thead>
    <tr>
      <td style='$border;$heavygray;' colspan=4>
	<b>1. IDENTIFICACIÓN GENERAL</b>
      </td>
    </tr>
    <tr>
      <td style='$border;$colorgray;'>Facultad</td><td colspan=3 style='$border;'>$Facultad</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;'>Instituto</td><td colspan=3 style='$border;'>$Instituto</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Programa(s) Académicos</td><td colspan=3 style='$border;'>$Programas_Academicos</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Área Académica</td><td colspan=3 style='$border;'>$Area_Academica</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Ciclo</td><td colspan=3 style='$border;'>$Ciclo</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Tipo de Curso</td><td colspan=3 style='$border;'>$Tipo_Curso</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Profesores Responsables</td><td colspan=3 style='$border;'>$Profesores_Responsables</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;'>Asistencia</td><td colspan=3 style='$border;'>$Asistencia</td>
    </tr>
    <tr>
      <td style='$border;$heavygray;' colspan=4>
	<b>2. IDENTIFICACIÓN ESPECÍFICA</b>
      </td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Semestre</td><td style='$border;' colspan=3>$Semestre</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Nombre de la Asignatura</td><td colspan=3 style='$border;'>$Nombre_Asignatura</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Código</td><td colspan=3 style='$border;'>$Codigo</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Semestre en el plan</td><td colspan=3 style='$border;'>$Semestre_Plan</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Número de Créditos</td><td colspan=3 style='$border;'>$Creditos</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Horas Semestrales</td>
      <td colspan=1 style='$border;'>HDD:$Intensidad_HDD</td>
      <td colspan=1 style='$border;'>HDA:$Intensidad_HDA</td>
      <td colspan=1 style='$border;'>TI:$Intensidad_TI</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Semanas</td><td style='$border;' colspan=3>$Semanas</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Intensidad Semanal</td>
      <td style='$border;' colspan=1>Teórico: $Horas_Teoricas_Semanales</td>
      <td style='$border;' colspan=1>Práctico: $Horas_Practicas_Semanales</td>
      <td style='$border;' colspan=1>Teórico-Práctico: $Horas_Teorico_Practicas_Semanales</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>H (Habilitable)</td>
      <td style='$border;' colspan=3>$Habilitable</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>V (Validable)</td>
      <td style='$border;' colspan=3>$Validable</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>C (Clasificable)</td>
      <td style='$border;' colspan=3>$Clasificable</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Prerrequisitos</td>
      <td style='$border;' colspan=3>$Requisitos</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Correquisitos</td>
      <td style='$border;' colspan=3>$Correquisitos</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Sede en la que se dicta</td>
      <td style='$border;' colspan=3>$Sede</td>
    </tr>
    <tr>
      <td style='$border;$heavygray;' colspan=4>
	<b>3. DATOS DE LOS PROFESORES QUE ELABORAN EL PLAN DE ASIGNATURA</b>
      </td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Nombres y Apellidos</td>
      <td style='$border;' colspan=3>$Profesores_Elaboran</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Correo Electrónico</td>
      <td style='$border;' colspan=3>$Correos_Electronicos</td>
    </tr>
    <tr><td style='$border;$heavygray;' colspan=4><b>4. DESCRIPCIÓN</b></td></tr>
    <tr><td style='$border;' colspan=4>$Descripcion</td>
    <tr><td style='$border;$heavygray;' colspan=4><b>5. JUSTIFICACIÓN</b></td></tr>
    <tr><td style='$border;' colspan=4>$Justificacion</td>
    <tr><td style='$border;$heavygray;' colspan=4><b>6. OBJETIVOS</b></td></tr>
    <tr>
      <td style='$border;' colspan=4>
	<p><b>Objetivo General:</b></p>
	<p>$Objetivo_General</p>
	<p><b>Objetivos Específicos:</b></p>
	<p>Al terminar el semestre el estudiante podrá:<p>
	<p>Objetivos Conceptuales:<br/><blockquote>$Objetivos_Especificos_Conceptuales</blockquote></p>
	<p>Objetivos Actitudinales:<br/><blockquote>$Objetivos_Especificos_Actitudinales</blockquote></p>
	<p>Objetivos Procedimentales:<br/><blockquote>$Objetivos_Especificos_Procedimentales</blockquote></p>
      </td>
    </tr>
    <tr><td style='$border;$heavygray;' colspan=4><b>7. CONTENIDOS</b></td></tr>
    <tr>
      <td colspan=4 style='$border;'>
      <p><b>Contenido Resumido</b></p>
      <blockquote>$Contenido_Resumido</blockquote>
      <p><b>Unidades Detalladas</b></p>
      <blockquote>
      $unidades
      </blockquote>
      </td>
    </tr>
    <tr><td style='$border;$heavygray;' colspan=4><b>8. ESTRATEGIAS METODOLÓGICAS</b></td></tr>
    <tr><td style='$border;' colspan=4>$Estrategia_Metodologica</td>
    <tr><td style='$border;$heavygray;' colspan=4><b>9. EVALUACIÓN</b></td></tr>
    <tr><td style='$border;' colspan=4>$Evaluacion</td>
    <tr><td style='$border;$heavygray;' colspan=4><b>10. BIBLIOGRAFÍA</b></td></tr>
    <tr><td style='$border;' colspan=4>$Bibliografia_General<br/>$BibliografiaCompleta</td>
  </table>
</body>
</html>
TABLE;
    $coursedir="$DATADIR/data/$ver_curso";
    /*
    $mpdf=new mPDF();
    $mpdf->WriteHTML($table);
    $mpdf->Output();
    */
    $fl=fopen("$coursedir/$ver_curso-FCEN.html","w");
    fwrite($fl,$table);
    fclose($fl);
    if(file_exists("$coursedir/.$ver_curso-FCEN.pdf.md5sum")){
      shell_exec("cd $coursedir;md5sum $ver_curso-FCEN.html > /tmp/md5");
      $out=shell_exec("cd $coursedir;diff /tmp/md5 .$ver_curso-FCEN.pdf.md5sum");
    }else{$out="NEW";}
    if(!isBlank($out)){
      sleep(2);
      shell_exec("cd $coursedir;$H2PDF $ver_curso-FCEN.html $ver_curso-FCEN.pdf");
      shell_exec("cd $coursedir;md5sum $ver_curso-FCEN.html > .$ver_curso-FCEN.pdf.md5sum");
    }

    if(file_exists("$coursedir/$ver_curso-FCEN.pdf")){
      $filepdf="(<a href=$coursedir/$ver_curso-FCEN.pdf target=_blank>PDF</a>)";
    }else{
      $filepdf="";
    }

$page.=<<<DESCARGA
<a href=$coursedir/$ver_curso-FCEN.html target=_blank>
  Formato FCEN
</a>$filepdf<br/>
DESCARGA;
  }

  if($mode=="Vicedocencia" or $mode=="Todos"){
    $table="";
    $INST=upAccents($Instituto);
    $CURSO=upAccents($Nombre_Asignatura);
    $col1=40;
    $col2=100-$col1;

    $ContenidoResumido="";
    $BibliografiaCompleta="$Bibliografia_General";
    $unidades="";
    for($i=1;$i<=10;$i++){
      $n=$offset+10*($i-1);
      $var="Unidad${i}_Titulo";
      //echo "$var<br/>";
      $titulo=$$var;
      //echo "Unidad $i:$titulo<br/>";
      if(isBlank($titulo)){break;}
      $var="Unidad${i}_Conceptual";
      $conceptual=$$var;
      $var="Unidad${i}_Procedimental";
      $procedimental=$$var;
      $var="Unidad${i}_Actitudinal";
      $actitudinal=$$var;
      $var="Unidad${i}_Bibliografia";
      $bibliografia=$$var;
      $var="Unidad${i}_Semanas";
      $semanas=$$var;
      $semtxt="";
      if($semanas>0){
	$semtxt="$semanas";
      }
      $BibliografiaCompleta.="$bibliografia";
      if(isblank($bibliografia)){
	$bibliografia=$Bibliografia_General;
      }
$unidades.=<<<UNIDADES
<p><b>Unidad No. $i.</b></p>
<table border=0 width=650px style='border-collapse:collapse'>
  <tr>
    <td width=$col1%></td><td width=$col2%></td>
  </tr>
  <tr>
    <td width=$col1% style='$border;' valign=top><b>Tema(s) a desarrollar</b></td>
    <td width=$col2% style='$border;'>$titulo</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;' valign=top><b>Subtemas</b></td>
    <td width=$col2% style='$border;'>
      $conceptual<br/>
      $procedimental<br/>
      $actitudinal<br/>
    </td>
  </tr>
  <tr>
    <td width=$col1% style='$border;' valign=top><b>No. de semanas que se le dedicarán a esta unidad</b></td>
    <td width=$col2% style='$border;'>$semtxt</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;' colspan=2><b>BIBLIOGRAFÍA BÁSICA correspondiente a esta unidad</b></td>
  </tr>
  <tr>
    <td width=$col1% style='$border;' colspan=2>$bibliografia</td>
  </tr>
</table>
UNIDADES;
      $ContenidoResumido.="$i-$titulo<br/>";
    }
    if(isBlank($Contenido_Resumido)){$Contenido_Resumido=$ContenidoResumido;}

$table.=<<<TABLE
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style type="text/css">
  BODY{
  font-family:Arial,Helvetica;
  }
  </style>
</head>
<body>
<table border=0 width=650>
  <tr>
    <td style='text-align:center'>
      UNIVERSIDAD DE ANTIOQUIA<br/>
      FACULTAD DE CIENCIAS EXACTAS Y NATURALES<br/><br/>
      <div style='font-size:20px'>$INST</div>
    </td>
  </tr>
</table>
<p></p>
<table border=0 width=650px style='border-collapse:collapse'>
  <tr>
    <td width=50%></td><td width=50%></td>
  </tr>
  <tr>
    <td></td>
    <td style='text-align:center;$border;'>
      APROBADO EN EL CONSEJO DE FACULTAD DE CIENCIAS EXACTAS Y NATURALES ACTA $AUTH_Acta_Numero DEL $AUTH_Acta_Fecha.
    </td>
  </tr>
</table>
<p></p>
<p></p>
<p style="text-align:center;font-size:18px;width:650px">
  <b>PROGRAMA DE $CURSO</b>
</p>
<p></p>
<table border=0 width=650px style='border-collapse:collapse'>
  <tr>
    <td width=$col1%></td><td width=$col2%></td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>NOMBRE DE LA MATERIA</b></td>
    <td width=$col2% style='$border;'>$Nombre_Asignatura</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>PROFESOR</b></td>
    <td width=$col2% style='$border;'>$Profesores_Responsables</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>OFICINA</b></td>
    <td width=$col2% style='$border;'>$Profesores_Oficinas</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>HORARIO DE CLASE</b></td>
    <td width=$col2% style='$border;'>$Horario_clase</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>HORARIO DE ATENCIÓN</b></td>
    <td width=$col2% style='$border;'>$Horario_atencion</td>
  </tr>
</table>
<p></p>
<p><b>INFORMACIÓN GENERAL</b></p>
<table border=0 width=650px style='border-collapse:collapse'>
  <tr>
    <td width=$col1%></td><td width=$col2%></td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Código de la materia</b></td>
    <td width=$col2% style='$border;'>$Codigo</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Semestre</b></td>
    <td width=$col2% style='$border;'>$Semestre_Plan</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Área</b></td>
    <td width=$col2% style='$border;'>$Area_Academica</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Horas teóricas semanales</b></td>
    <td width=$col2% style='$border;'>$Horas_Teoricas_Semanales</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Horas teóricas semestrales</b></td>
    <td width=$col2% style='$border;'>$Horas_Teoricas_Semestrales</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>No. de créditos</b></td>
    <td width=$col2% style='$border;'>$Creditos</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Horas de clase por semestre</b></td>
    <td width=$col2% style='$border;'>$Intensidad_HDD</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Campo de Formación</b></td>
    <td width=$col2% style='$border;'>$Campo_Formacion</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Validable</b></td>
    <td width=$col2% style='$border;'>$Validable</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Habilitable</b></td>
    <td width=$col2% style='$border;'>$Habilitable</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Clasificable</b></td>
    <td width=$col2% style='$border;'>$Clasificable</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Requisitos</b></td>
    <td width=$col2% style='$border;'>$Requisitos</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Corequisitos</b></td>
    <td width=$col2% style='$border;'>$Correquisitos</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;'><b>Programas a los que se ofrece la materia</b></td>
    <td width=$col2% style='$border;'>$Programas_Academicos</td>
  </tr>
</table>

<p></p>
<p><b>INFORMACIÓN COMPLEMENTARIA</b></p>
<table border=0 width=650px style='border-collapse:collapse;page-break-inside:avoid'>
  <thead>
  <tr>
    <td width=$col1%></td><td width=$col2%></td>
  </tr>
  </thead>
  <tr>
    <td width=$col1% style='$border;' valign=top><b>Propósito del Curso:</b></td>
    <td width=$col2% style='$border;'>$Proposito</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;' valign=top><b>Justificación:</b></td>
    <td width=$col2% style='$border;'>$Justificacion</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;' valign=top><b>Objetivo General:</b></td>
    <td width=$col2% style='$border;'>$Objetivo_General</td>
  </tr>
  <tr>
    <td width=$col1% style='$border;' valign=top><b>Objetivos Específicos:</b></td>
    <td width=$col2% style='$border;'>
      $Objetivos_Especificos_Conceptuales<br/>
      $Objetivos_Especificos_Procedimentales<br/>
      $Objetivos_Especificos_Actitudinales<br/>
    </td>
  </tr>
  <tr>
    <td width=$col1% style='$border;' valign=top><b>Contenido Resumido:</b></td>
    <td width=$col2% style='$border;'>$ContenidoResumido</td>
  </tr>
</table>

<p></p>
<p><b>UNIDADES DETALLADAS</b></p>
$unidades

<p></p>
<table border=0 width=650px style='border-collapse:collapse'>
  <tr>
    <td style='$border;' valign=top>
      <b>METODOLOGÍA a seguir en el desarrollo del curso:</b>
      <p>
	$Estrategia_Metodologica
      </p>
    </td>
  </tr>
</table>

<p></p>
<table border=0 width=650px style='border-collapse:collapse'>
  <tr>
    <td width=30%></td><td width=30%></td><td width=30%></td>
  </tr>
  <tr>
    <td style='$border;' valign=top colspan=3>
      <b>EVALUACIÓN</b>
    </td>
  </tr>
  <tr>
    <td style='$border;' colspan=1><b>Actividad</b></td>
    <td style='$border;' colspan=1><b>Porcentaje</b></td>
    <td style='$border;' colspan=1><b>Fecha (día, mes, año)</b></td>
  </tr>
  <tr>
    <td style='$border;' valign=top colspan=3>
      $Evaluacion_Especifica
    </td>
  </tr>
</table>

<p></p>
<table border=0 width=650px style='border-collapse:collapse'>
  <tr>
    <td style='$border;' valign=top>
      <b>Actividades de Asistencia Obligatoria:</b>
      <p>
	$Actividades_Obligatorias
      </p>
    </td>
  </tr>
</table>

<p></p>
<b>BIBLIOGRAFÍA COMPLEMENTARIA
<table border=0 width=650px style='border-collapse:collapse'>
  <tr>
    <td style='$border;' valign=top>
      $BibliografiaCompleta
    </td>
  </tr>
</table>

TABLE;
    
    $fl=fopen("$coursedir/$ver_curso-vicedocencia.html","w");
    fwrite($fl,$table);
    fclose($fl);
    if(file_exists("$coursedir/.$ver_curso-vicedocencia.pdf.md5sum")){
      shell_exec("cd $coursedir;md5sum $ver_curso-vicedocencia.html > /tmp/md5");
      $out=shell_exec("cd $coursedir;diff /tmp/md5 .$ver_curso-vicedocencia.pdf.md5sum");
    }else{$out="NEW";}
    if(!isBlank($out)){
      sleep(2);
      shell_exec("cd $coursedir;$H2PDF $ver_curso-vicedocencia.html $ver_curso-vicedocencia.pdf");
      shell_exec("cd $coursedir;md5sum $ver_curso-vicedocencia.html > .$ver_curso-vicedocencia.pdf.md5sum");
    }

    if(file_exists("$coursedir/$ver_curso-vicedocencia.pdf")){
      $filepdf="(<a href=$coursedir/$ver_curso-vicedocencia.pdf target=_blank>PDF</a>)";
    }else{
      $filepdf="";
    }
    
$page.=<<<DESCARGA
<a href=$coursedir/$ver_curso-vicedocencia.html target=_blank>
  Formato Vicedocencia
</a>$filepdf<br/>
DESCARGA;
  }

  $page.="</td></tr></table>";
  echo $page;
  return;
}
if(isset($entra_curso)){
}
if($lista){
}
?>

<?php
////////////////////////////////////////////////////
//FOOTER
////////////////////////////////////////////////////
footer:
$filetime=date(DATE_RFC2822,filemtime("index.php"));
echo<<<FOOTER
<hr/>
<p style='font-size:12px'>
  Última actualización: $filetime - 
<a href=mailto:jorge.zuluaga@udea.edu.co>Jorge I. Zuluaga</a> (C) 2014
</p>
</body>
</html>
FOOTER;
?>
