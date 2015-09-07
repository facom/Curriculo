<?php
////////////////////////////////////////////////////
//VARIABLES GLOBALES
////////////////////////////////////////////////////
if(isset($_GET["pass"])){echo "Key:".md5($_GET["user"]."%".$_GET["pass"]);}
$TEMPLATECODE="0300000";
$QARCHIVO=0;//1 si quiere mostrar los cursos archivados
$QRECYCLE=1;//1 si quiere mostrar los cursos reciclados
$SITEURL="http://astronomia-udea.co/principal/Curriculo/";
$SITE="http://astronomia-udea.co/principal/Curriculo/index.php";
$DATADIR=".";
$LOGOUDEA="http://astronomia-udea.co/principal/sites/default/files";
$SIGNATURE="http://astronomia-udea.co/principal/Curriculo/images/zul0807sgtr-1.jpg";
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
    function enviarPlan(codigo){
	emailname="#email_"+codigo;
	urlname="#url_"+codigo;
	nombrename="#nombresend_"+codigo;
	emails=$(emailname).val();
	url=$(urlname).attr('urlbase');
	nombre=$(nombrename).attr('nombresend');
	urlsend="index.php?planes_asignatura&accion=Enviar&emails="+emails+"&urlbase="+url+"&nombresend="+nombre+"#"+codigo;
	//alert(urlsend);
        window.location.href=urlsend;
    }
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
//$H2PDF="../../temp/wkhtmltopdf-i386";
$H2PDF="../../temp/wkhtmltopdf-amd64";
require("$ROOTDIR/etc/configuration.php");
require("$ROOTDIR/etc/database.php");
require("$ROOTDIR/etc/database.php");
require("$ROOTDIR/etc/PHPMailer/PHPMailerAutoload.php");

////////////////////////////////////////////////////
//DESBLOQUEO SALIR
////////////////////////////////////////////////////
$qprofesorsalir=0;
if($_POST["accion"]=="Salir"){
  $curso=$_POST["F100_Codigo"];
  $coursedir="$DATADIR/data/$curso";
  $lockfile="$coursedir/.lock";
  shell_exec("rm -rf $lockfile");
  $_GET=array();
  $_POST=array();
  $_GET["planes_asignatura"]=1;
  echo  "<script type='text/javascript'>";
  echo "window.close();";
  echo "</script>";
  $qprofesorsalir=1;
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
function sendMail($email,$subject,$message,$headers="")
{
  date_default_timezone_set('Etc/UTC');
  $mail = new PHPMailer;
  $mail->isSMTP();
  $mail->SMTPDebug = 0;
  $mail->Debugoutput = 'html';
  $mail->Host = 'smtp.gmail.com';
  $mail->Port = 587;
  $mail->SMTPSecure = 'tls';
  $mail->SMTPAuth = true;
  $mail->Username = $GLOBALS["EMAIL_USERNAME"];
  $mail->Password = $GLOBALS["EMAIL_PASSWORD"];
  $mail->setFrom($mail->Username, 'Sistema de Solicitud de Comisiones FCEN/UdeA');
  $mail->addReplyTo($mail->Username, 'Sistema de Solicitud de Comisiones FCEN/UdeA');
  $mail->addAddress($email,"Destinatario");
  $mail->Subject=$subject;
  $mail->CharSet="UTF-8";
  $mail->Body=$message;
  $mail->IsHTML(true);
  if(!($status=$mail->send())) {
    $status="Mailer Error:".$mail->ErrorInfo;
  }
  return $status;
}

function generateSelection($values,$name,$value,$options="",$style="")
{
  if(preg_match("/,/",$values)){
    $parts=preg_split("/,/",$values);
  }else{
    $parts=$values;
  }
  $selection="";
  $selection.="<select name='$name' style='$style' $options>";
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
$menu.=" - <a href=index.php?planes_aprobados>Planes Aprobados</a>";
if($QADMIN>1){
  $menu.=" - <a href=index.php?planes_asignatura>Planes en Edición</a>";
}
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
      <a href='?planes_asignatura'>Planes en Edición</a>.
    </b>
    Aquí podrá ver (o editar en caso de ser administrador) los planes
    de asignatura de todos los cursos de la Facultad.
  </li>

  <li>
    <b>
      <a href='?planes_aprobados'>Planes Aprobados</a>.
    </b>
    Aquí podrá ver los planes
    de asignatura aprobados de todos los cursos de la Facultad.
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
//LISTA TOTAL DE CURSOS
////////////////////////////////////////////////////
if(isset($_GET["planes_aprobados"])){

  $page="$header";

  //==================================================
  //FORMULARIO
  //==================================================
  $input=generateSelection(array_values($INSTITUTOS),"ap_instituto",$ap_instituto);

$page.=<<<FORMULARIO
  <h4>Busca</h4>
  <form>
  <input type="hidden" name="planes_aprobados">
  <table border=0>
  <tr><td>Instituto:</td><td>$input</td></tr>
  <tr><td>Semestre:</td><td><input type="text" name="ap_semestre" value="$ap_semestre" maxlength=6 size=6></td></tr>
  <tr><td>Nombre Curso:</td><td><input type="text" name="ap_nombre" value="$ap_nombre" maxlength=6 size=6></td></tr>
  <tr colspan=2><td><input type="submit" name="ap_submit" value="Muestre"></td></tr>
  </table>
  </form>
FORMULARIO;

  //==================================================
  //LISTA DE PLANES APROBADOS
  //==================================================
  $aprobados="";
  if($ap_instituto=="Profesor" or
     $ap_instituto=="Administrador"){goto end_aprobados;}
  $listaaprob="";
  $listapriv="";
  $sql="select F100_Codigo,F110_Nombre_Asignatura,F280_Instituto,F060_AUTH_Publica_Curso,F010_AUTO_Fecha_Actualizacion,F015_AUTO_Usuario_Actualizacion,F050_Nombre_Actualiza,F020_AUTH_Autorizacion_Vicedecano,F330_Semestre_Plan,F330_Semestre,F000_AUTO_Codigoid,F025_AUTH_Version from MicroCurriculos_Publicos where (F330_Semestre like '%$ap_semestre%' AND F110_Nombre_Asignatura like '%$ap_nombre%' AND F280_Instituto='$ap_instituto') order by F330_Semestre_Plan*1,F100_Codigo,F110_Nombre_Asignatura;";
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
    $codigoid=$row[10];
    $version=$row[11];
    
$listaaprob.=<<<LISTA
<li>
  Semestre $semestre : <a href='?ver_curso=$codigoid&source=public&mode=Todos&nogen' target="_blank">$nombre - $codigo - $semestreactual - Version $version</a>
LISTA;
  if($QADMIN and ($instituto=="$INSTITUTO" or $INSTITUTO=="Facultad") and 0){
    $listaaprob.=" - <a href='?carga_curso=$codigo&edita_curso&profesor' target='_blank'>Editar</a>";
  }
    $listaaprob.="</li>";
  }
 end_aprobados:
  //LISTA PUBLICOS
  if(!preg_match("/\w+/",$listaaprob)){$listaaprob="<i>(No se encontraron cursos)</i>";}
  else{$listaaprob.="</ul>";}
  //MUESTRA LISTAS
  $aprobados.="<h4>$ap_instituto</h4><ul>$listaaprob</ul>";
  $page.="<h2>Lista de Cursos Aprobados</h2>$aprobados";
  echo $page;
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
      //*
      $sql="update MicroCurriculos set F330_Semestre='$semestre_all'";
      if(!mysqli_query($db,$sql)){
	die("No se pudo cambiar el semestre:".mysqli_error($db));
      }
      //*/
      /* 
      //Use esto si quiere cambiar algo globalmente de todos los cursos
      $value="Este plan de asignatura es válido entre los semestres 2002-1 y 2014-2."
      $sql="update MicroCurriculos set F335_Notas='$value' where F020_AUTH_Autorizacion_Vicedecano='Si'";
      echo "SQL: $sql<br/>";
      if(!mysqli_query($db,$sql)){
	die("No se pudo cambiar el semestre:".mysqli_error($db));
      }
      $resultado.="Semestre cambiado exitosamente.";
      //*/
    }
    $resultado.="</p>";

  } 

  //TABLA DE CONTENIDO
  $tablacontenido="";
  $i=1;
  foreach($FIELDS as $field){
    if(preg_match("/AUTO/",$field)){continue;}
    if(preg_match("/Unidad\d/",$field) and
       !preg_match("/Titulo/",$field)){continue;}
    //if(($i%10)==0){$tablacontenido.="<br/>";}
    $fname=$field;
    /*
    $fname=preg_replace("/^F\d+_/","",$field);
    $fname=preg_replace("/_/"," ",$fname);
    $fname=preg_replace("/AUTH/","",$fname);
    */
      
$tablacontenido.=<<<TABLA
  $fname -  
TABLA;
 $i++;
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
  $page="$header";

  //==================================================
  //LISTA DE PLANES
  //==================================================
  if(!isBlank($filtra_all)){$filtra="and ($filtra_all)";}
  else{$filtra="";}
  $publicos="";
  $privados="";
  foreach(array_keys($INSTITUTOS) as $key){
    $instituto=$INSTITUTOS["$key"];
    if($instituto=="Profesor" or
       $instituto=="Administrador"){continue;}
    $listapub="";
    $listapriv="";
    $sql="select F100_Codigo,F110_Nombre_Asignatura,F280_Instituto,F060_AUTH_Publica_Curso,F010_AUTO_Fecha_Actualizacion,F015_AUTO_Usuario_Actualizacion,F050_Nombre_Actualiza,F020_AUTH_Autorizacion_Vicedecano,F330_Semestre_Plan,F330_Semestre,F025_AUTH_Version,F060_AUTH_Publica_Curso from MicroCurriculos where (F280_Instituto='$instituto' $filtra) order by F330_Semestre_Plan*1,F100_Codigo,F110_Nombre_Asignatura;";
    //echo "SQL: $sql<br/>";
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
      $version=$row[10];
      $publicado=$row[11];
      if($publicado=="Si"){$publicado="<i style='color:red'>Si</i>";}
      if($publicado=="No"){$publicado="<i style='color:blue'>No</i>";}

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
<a href='?ver_curso=$codigo&mode=Todos' target="_blank">$nombre - $codigo</a>
LISTA;
	if($QADMIN and ($instituto=="$INSTITUTO" or $INSTITUTO=="Facultad")){
	  $listapub.=" - <a href='?carga_curso=$codigo&edita_curso&profesor' target='_blank'>Editar</a>";
	}
	$listapub.="</li>";
      }
      $enlace="";$editar="";$correo="";
      if($QADMIN and ($instituto=="$INSTITUTO" or $INSTITUTO=="Facultad")){
	$codemd5=md5($codigo);
	$link="$SITE?carga_curso=$codigo&edita_curso&profesor";
	$fl=fopen("$ROOTDIR/links/$codemd5.html","w");
	fwrite($fl,"
<html>
<head>
<meta http-equiv='refresh' content='0;URL=$link'>
</head>
<body></body>
</html>
");  
	fclose($fl);
	$showlink="$SITEURL/links/$codemd5.html";
	$urlcode="$codemd5";
	$editar=" <sup><a href='?carga_curso=$codigo&edita_curso&profesor' target='_blank'>Editar</a></sup>";
	$enlace="Enlace para enviar al profesor: <i style='background-color:lightgray;padding:0px;'><a href='$showlink'>$showlink</a></i>";
	$enlace="Enlace para enviar al profesor: <i style='background-color:lightgray;padding:0px;'><a id='url_$codigo' urlbase='$urlcode' href='$showlink'>$showlink</a></i>";
	$correo="Correo(s) para el envío: <input id='email_$codigo' type='text' size=50><input type='submit' value='Enviar' onclick='enviarPlan(\"$codigo\")'>";
      }
$listapriv.=<<<LISTA
<li>
  <a name='$codigo'></a>
  <a id="nombresend_$codigo" href='?ver_curso=$codigo&mode=Todos' target="_blank" nombresend="$nombre"><b>$nombre - $codigo</b></a>$editar<br/>
  <i style="text-decoration:underline">Última actualización</i>: $actualizacion - $usuario - $modifica <br/>
  <i style="text-decoration:underline">Versión</i>: $version <br/>
  <i style="text-decoration:underline">Revisado y Aprobado</i>: $autorizacion<br/>
  <i style="text-decoration:underline">Curso publicado</i>: $publicado<br/>
  <i style="text-decoration:underline">Porcentaje completado</i>: $procentaje_bar $porcentaje_text <br/>
  <i style="text-decoration:underline">Semestre en el Plan</i>: $semestre <br/>
  <i style="text-decoration:underline">Semestre Actual</i>: $semestreactual <br/>
  <i style="text-decoration:underline">Historia de cambios</i>: <a href="$DATADIR/data/$codigo/changes.log" target="_blank">changes.log</a> <br/>
  $enlace<br/>
  $correo<br/>
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

  //$page.="<h2>Lista de Cursos Públicos</h2>$publicos";

  if($QADMIN>=2){
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //OEPRACIONES GLOBALES
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    if($QADMIN>=4){
$page.=<<<GLOBALES
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
  <li>
  Filtra cursos:
  <input type="text" name="filtra_all" value="$filtra_all" size=50>
  <input type="submit" name="accion_global" value="Filtra"><br/>
  Use sintaxis de SQL.  <i>Ejemplo: F330_Semestre='10'</i>
  <div>
    <a href="JavaScript:void(null)" onclick="$('#tabla_contenido').toggle('fast',null);" style="font-size:10px" tabIndex="-1">
      Vea la lista de campos
    </a>
  </div>
  <div class="hidden" style="display:none;background:lightblue;padding:10px;margin:10px 0px 0px 0px;font-size:10px" id="tabla_contenido">
    <b>Campos</b><br/>
    $tablacontenido
    <br/><br/>
    <a href="JavaScript:void(null)" onclick="$('#tabla_contenido').toggle('fast',null);" style="font-size:10px" tabIndex="-1">
      Ocultar
    </a>
  </div>

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
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//ENVIA UN CORREO
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if($accion=="Enviar"){
  echo "<hr/><h2>Resultados de operación</h2>";
  $editlink="$SITEURL/links/$urlbase.html";
  $parts=preg_split("/\s*,\s*/",$emails);
  echo "Enviando enlace de curso '$nombresend'...<br/>";
  echo "Url '$editlink'...<br/>";
  foreach($parts as $email){
    if(isBlank($email)){continue;}
    if(!preg_match("/@/",$email)){continue;}
$message=<<<M
Se&ntilde;or(a) Profesor(a),
<p>
Le solicitamos amablemente revisar y si es del caso corregir el Plan
de Asignatura del curso <i>$nombresend</i>.  Para ello use el link
provisto aquí:
</p>
<center><a href='$editlink'>$editlink</a></center>
<p>
En la ventana inicial ingrese como 'Nombre' su nombre completo; como
'Usuario' use la palabra <i><b>profesor</b></i> (en minúscula sostenida) y como
'Contraseña' use <i><b>profesor2014</b></i> (en minúscula sostenida, sin
espacios).
</p>
<p>
Un videotutorial sobre como usar el sistema de edición de planes de
asignatura esta disponible
en <a href='http://youtu.be/p-uquMmBs_Q'>este
enlace</a>.  Le recomendamos verlo completamente (son unos pocos
minutos) antes de revisar el plan, especialmente si es la primera vez.
</p>
<p>
Otros profesores pueden estar revisando este plan de asignatura.  Este
mensaje en particular fue enviado a todos los siguientes
destinatarios: <b>$emails</b>. En el video tutorial se explica como evitar
que dos o mas profesores editen simultáneamente el mismo programa.
</p>
Atentamente,<br/><br/>
<b>Coordinación de Pregrado</b>
M;
    $headers="";
    $headers.="From: pregradofisica@udea.edu.co\r\n";
    $headers.="Reply-to: pregradofisica@udea.edu.co\r\n";
    $headers.="MIME-Version: 1.0\r\n";
    $headers.="MIME-Version: 1.0\r\n";
    $headers.="Content-type: text/html\r\n";

    echo "Enviando mensaje a $email...<br/>";
    $subject="Revisión del plan de asignatura del curso $nombresend";
    sendMail($email,$subject,$message,$headers);

    $emailcc="pregradofisica@udea.edu.co";
    echo "Enviando copia de mensaje a $emailcc...<br/>";
    $subject="[Copia] Revisión del plan de asignatura del curso $nombresend";
    sendMail($emailcc,$subject,$message,$headers);
  }
}

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
if(($accion=="Guardar" or $accion=="Reciclar" or $accion=="Archivar" or $accion=="Publicar") and $QADMIN){
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

  $name="F100_Codigo";
  $codigo=$$name;

  $tableid="F100_Codigo";
  $codigoid=$codigo;

  $table="MicroCurriculos";
  if($accion=="Reciclar"){
    $table="MicroCurriculos_Recycle";
  }
  if($accion=="Publicar"){
    $table="MicroCurriculos_Publicos";
    $tableid="F000_AUTO_Codigoid";
    $F000_AUTO_Codigoid="$codigo-v$F025_AUTH_Version-$F330_Semestre";
    $F060_AUTH_Publica_Curso="Si";
    $codigoid=$F000_AUTO_Codigoid;
  }

  ////////////////////////////////////////////////////
  //GUARDANDO REGISTRO
  ////////////////////////////////////////////////////
  //INSERT IF NOT EXISTS
  $sql="insert into $table ($tableid) values (\"$codigoid\") on duplicate key update $tableid=\"$codigoid\"";
  if(!mysqli_query($db,$sql)){
    die("Error:".mysqli_error($db));
  }
  //UPDATE IF EXISTS
  $sql="update $table set ";
  foreach($FIELDS as $field){
    $type=$DBASE[$field]["type"];
    if($field=="$tableid" or $type=="text"){continue;}
    $value=$$field;
    if(preg_match("/AUTO/",$field)){
      if(preg_match("/_Fecha/",$field)){
	$value=$DATE;
      }
      if(preg_match("/_Usuario/",$field)){
	$value=$INSTITUTO;
      }
      if(preg_match("/_Version/",$field) and $accion!="Publicar"){
	$value=$value+1;
      }
    }
    $sql.="$field='$value',";
  }
  $sql=trim($sql,",");
  $name="$tableid";
  $codigoid=$$name;
  $sql.=" where $tableid='$codigoid';";
  if(!mysqli_query($db,$sql)){
    die("Error:".mysqli_error($db));
  }else if($accion!="Reciclar"){
    if($accion!="Publicar"){
      $ps=porcentajeCompletado($codigo);
      $p=$ps[0];
      $n=$ps[1];
      $porcentaje=round($p,0)."% $n";
      $result.="<i style='color:blue'>Registro guardado exitosamente ($porcentaje completado).</i>";
    }else{
      $result.="<i style='color:blue'>El cuso ha sido publicado. A partir de este
momento todas las versiones de este curso serán nuevas.</i>";
    }
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

  //Create link to course edition
  $codemd5=md5($codigo);
  $link="$SITE?carga_curso=$codigo&edita_curso&profesor";
  $fl=fopen("$ROOTDIR/links/$codemd5.html","w");
  fwrite($fl,"
<html>
<head>
<meta http-equiv='refresh' content='0;URL=$link'>
</head>
<body></body>
</html>
");  
  fclose($fl);

  if($accion=="Publicar"){
    $pubcourse="$DATADIR/public/$codigoid";
    system("mkdir -p \"$pubcourse\"");
    $fc=fopen("$pubcourse/notext.txt","w");
    fwrite($fc,"<?php\n");
    foreach($FIELDS as $field){
      $value=$$field;
      $type=$DBASE[$field]["type"];
      if($type!="text"){
	fwrite($fc,"\$$field=\"$value\";\n");
      }else{
	$fl=fopen("$pubcourse/$field.txt","w");
	fwrite($fl,$value);
	fclose($fl);
      }
    }
    fwrite($fc,"?>\n");
    fclose($fc);
  }

  if($accion=="Reciclar"){
    echo "$header$menu$result";
    goto footer;
  }

  if($accion=="Publicar"){
    $F060_AUTH_Publica_Curso="Si";
    $F020_AUTH_Autorizacion_Vicedecano="No";
    $F025_AUTH_Version+=1;
    $F030_AUTH_Acta_Numero="";
    $F040_AUTH_Acta_Fecha="";
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
<div style='position:fixed;top:20px;right:0px;'>
<div id='recuerdo' style='background-color:pink;display:$display;padding:10px;width:200px;font-style:italic'>
  Señor profesor/administrador: no olvide darle salir después de terminar.$msg
</div>
<input type='submit' name='accion' value='Guardar'>
<input id='salir' type='submit' name='accion' value='Salir' style='background-color:$bcolor;'>
BUTTONS;

  //TABLA DE CONTENIDO
  $tablacontenido="";
  $i=1;
  foreach($FIELDS as $field){
    if(preg_match("/AUTO/",$field)){continue;}
    if(preg_match("/Unidad\d/",$field) and
       !preg_match("/Titulo/",$field)){continue;}
    if(($i%3)==0){$tablacontenido.="<br/>";}
    $fname=preg_replace("/^F\d+_/","",$field);
    $fname=preg_replace("/_/"," ",$fname);
    $fname=preg_replace("/AUTH/","",$fname);
    
$tablacontenido.=<<<TABLA
  <a href="JavaScript:void(null)" onclick="document.location.href='#$field'">
  $fname
  </a> -  
TABLA;
 $i++;
  }

 if($QADMIN>=2){
   
$buttons.=<<<BUTTONS
<input type='submit' name='accion' value='Reciclar'>
<!--<input type='submit' name='accion' value='Archivar'>-->
BUTTONS;
 }

 if($QADMIN>=3 and $F020_AUTH_Autorizacion_Vicedecano=="Si"){
$buttons.=<<<BUTTONS
<input type='submit' name='accion' value='Publicar'>
BUTTONS;
 }

$buttons.=<<<BUTTONS
<br/>
<div>
<a href="JavaScript:void(null)" onclick="$('#tabla_contenido').toggle('fast',null);" style="font-size:10px" tabIndex="-1">
  Vaya rápido a un campo
</a>
</div>
<div class="hidden" style="display:none;background:lightblue;padding:10px;margin:10px 0px 0px 0px;font-size:10px" id="tabla_contenido">
<b>Campos</b><br/>
$tablacontenido
<br/><br/>
<a href="JavaScript:void(null)" onclick="$('#tabla_contenido').toggle('fast',null);" style="font-size:10px" tabIndex="-1">
  Ocultar
</a>
</div>
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
      $block="readonly";
    }
    if(preg_match("/Publica_Curso/",$field)){
      $block="readonly";
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
      $block="readonly";
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
	if($block=="readonly"){
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
      $input=generateSelection($values,$field,$value,$options=$block);
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
  $tableid="F100_Codigo";
  $coursedir="$DATADIR/data/$ver_curso";
  $signature="(No autorizado. Este documento es solo un borrador.)";
  if($source=="public"){
    $table="MicroCurriculos_Publicos";
    $tableid="F000_AUTO_Codigoid";
    $coursedir="$DATADIR/public/$ver_curso";
    $signature="<img src='$SIGNATURE' width=100 align='top'>";
    $source="source=public";
  }
  $sql="select * from $table where $tableid='$ver_curso';";
  $out=mysqli_query($db,$sql);
  if(!($row=mysqli_fetch_array($out))){die("Error:".mysqli_error($db));}

  //CARGA VALORES EN VARIABLES
  foreach($FIELDS as $field){
    $type=$DBASE[$field]["type"];
    if($type=="text"){continue;}
    $$field=$row["$field"];
  }

  //RECUPERA INFORMACIÓN DEL CURSO DE ARCHIVOS
  foreach($FIELDS as $field){
    $value=$$field;
    $fname=preg_replace("/^F\d+_/","",$field);
    $$fname=$value;
    $type=$DBASE[$field]["type"];
    if($type!="text"){continue;}
    $file="$coursedir/$field.txt";
    $fl=fopen($file,"r");
    $$field=fread($fl,filesize($file));
    $value=$$field;
    $value=preg_replace("/\n/","<br/>",$value);
    $$fname=$value;
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
  <tr><td $style>Semestre actual</td><td>$Semestre</td></tr>
  <tr><td $style>Última Actualización</td><td>$AUTO_Fecha_Actualizacion</td></tr>
  <tr><td $style>Número de Créditos</td><td>$Creditos</td></tr>
  <tr><td $style>Programas</td><td>$Programas_Academicos</td></tr>
  <tr><td $style>Prerrequisitos</td><td>$Requisitos</td></tr>
  <tr><td $style>Correquisitos</td><td>$Correquisitos</td></tr>
  <tr><td $style>Semestre en el plan</td><td>$Semestre_Plan</td></tr>
  <tr><td $style>Descripción</td><td>$Descripcion</td></tr>
  <tr><td $style>Justificación</td><td>$Justificacion</td></tr>
  <tr><td $style>Formatos disponibles</td><td>
TITULO;

 //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
 //REVISA DISPONIBILIDAD DE FORMATO
 //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
 if(file_exists("$coursedir/$ver_curso-plano.html")){
$filepdf="(<a href=$coursedir/$ver_curso-plano.pdf target=_blank>PDF</a>)";
$page.=<<<DESCARGA
<a href=$coursedir/$ver_curso-plano.html target=_blank>Formato plano</a>
  $filepdf
<br/>
DESCARGA;
 }

 if(file_exists("$coursedir/$ver_curso-FCEN.html")){
$filepdf="(<a href=$coursedir/$ver_curso-FCEN.pdf target=_blank>PDF</a>)";
$page.=<<<DESCARGA
<a href=$coursedir/$ver_curso-FCEN.html target=_blank>
  Formato FCEN
</a>$filepdf<br/>
DESCARGA;
 }

 if(file_exists("$coursedir/$ver_curso-vicedocencia.html")){
$filepdf="(<a href=$coursedir/$ver_curso-vicedocencia.pdf target=_blank>PDF</a>)";
$page.=<<<DESCARGA
<a href=$coursedir/$ver_curso-vicedocencia.html target=_blank>
  Formato Vicedocencia
</a>$filepdf<br/>
DESCARGA;
 }

$page.=<<<TABLE
</td></tr>
<tr><td><b>Acciones</b></td>
<td>
<a href="export.php?ver_curso=$ver_curso&mode=Plano&$source">Generar formato plano</a><br/>
<a href="export.php?ver_curso=$ver_curso&mode=FCEN&$source">Generar formato FCEN</a><br/>
<a href="export.php?ver_curso=$ver_curso&mode=Vicedocencia&$source">Generar formato Vicedocencia</a><br/>
<a href="export.php?ver_curso=$ver_curso&mode=Todos&$source">Generar todos los formatos</a>
</td></tr>
TABLE;

  $page.="</table>";
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
