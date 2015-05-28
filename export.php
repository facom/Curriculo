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
<?php

////////////////////////////////////////////////////
//COMMAND LINE
////////////////////////////////////////////////////
if(!isset($_GET["ver_curso"])){
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
}

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
$SIGNATURE="http://astronomia-udea.co/principal/Curriculo/images/zul0807sgtr-1.jpg";
$TMPDIR="tmp";

////////////////////////////////////////////////////
//DATABASE
////////////////////////////////////////////////////
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
//$H2PDF="../../temp/wkhtmltopdf-i386";
$H2PDF="../../temp/wkhtmltopdf-amd64";
require("$ROOTDIR/etc/configuration.php");
require("$ROOTDIR/etc/database.php");
$db=mysqli_connect("localhost",$USER,$PASSWORD,$DATABASE);

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
//VER UN CURSO
////////////////////////////////////////////////////
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
  if(isset($$field)){
    $value=$$field;
  }else{$value="";}
  $fname=preg_replace("/^F\d+_/","",$field);
  $$fname=$value;
  $type=$DBASE[$field]["type"];
  if($type!="text"){continue;}
  $file="$coursedir/$field.txt";
  $fl=fopen($file,"r");
  $$field=fread($fl,filesize($file));
  $value=$$field;
  fclose($fl);
  $value=preg_replace("/\n/","<br/>",$value);
  $$fname=$value;
}
 
//==================================================
//FORMATO PLANO
//==================================================
$border="border:1px solid;";
$colorgray="background-color:lightgray";
$heavygray="background-color:gray";
$style="style='font-weight:bold' width=30% valign=top";
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
 //$coursedir="$DATADIR/data/$ver_curso";
 $fl=fopen("$coursedir/$ver_curso-plano.html","w");
 fwrite($fl,$table);
 fclose($fl);
 if(file_exists("$coursedir/.$ver_curso-plano.pdf.md5sum")){
   shell_exec("cd $coursedir;md5sum $ver_curso-plano.html > /tmp/md5");
   $out=shell_exec("cd $coursedir;diff /tmp/md5 .$ver_curso-plano.pdf.md5sum");
 }else{$out="NEW";}
 if(!isBlank($out)){
   sleep(2);
   echo "Converting to pdf...";
   shell_exec("cd $coursedir;$H2PDF $ver_curso-plano.html $ver_curso-plano.pdf &> pdf.log");
   shell_exec("cd $coursedir;md5sum $ver_curso-plano.html > .$ver_curso-plano.pdf.md5sum");
 }else{
   echo "No Converting to pdf...";
 }
 
 if(file_exists("$coursedir/$ver_curso-plano.pdf")){
   $filepdf="(<a href=$coursedir/$ver_curso-plano.pdf target=_blank>PDF</a>)";
 }else{
   $filepdf="";
 }
}
  
if($mode=="FCEN" or $mode=="Todos"){
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
  <p style="font-size:10px">
  <b>Última actualización</b>: $DATE<br/>
  <b>Firma Autorizada Facultad</b>: $signature
  </p>
</body>
</html>
TABLE;
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
}
?>
