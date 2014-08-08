<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <script src="etc/jquery.js"></script>
</head>
<body>
<?
////////////////////////////////////////////////////
//CONFIGURATION
////////////////////////////////////////////////////
$SCRIPTNAME=$_SERVER[SCRIPT_FILENAME];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/configuration.php");
require("$ROOTDIR/etc/database.php");
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
if(isset($_COOKIE["verify"])){
  $verify=$_COOKIE["verify"];
  $ADMIN=$PASS_INFORMATION["$verify"];
  $INSTITUTO=$INSTITUTOS["$ADMIN"];
  $QADMIN=1;
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
  $selection.="<select name='$name'>";
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

////////////////////////////////////////////////////
//DATABASE
////////////////////////////////////////////////////
$db=mysqli_connect("localhost",$USER,$PASSWORD,$DATABASE);

////////////////////////////////////////////////////
//HEADER DEFINITION
////////////////////////////////////////////////////
$menu="";
$menu.="<a href=index.php>Principal</a>";
$menu.=" - <a href=index.php?planes_asignatura>Planes de Asignatura</a>";
if(!$QADMIN){
  $headbar="";
  $menu.=" - <a href=login.php>Conectarse</a>";
}
else{
  $headbar="<div style='background-color:lightgray;text-align:center;font-size:10px'>ADMINISTRADOR: $ADMIN ($INSTITUTO)</div>";
  $menu.=" - <a href=login.php?logout>Desconectarse</a>";
  $menu.=" - <a href=index.php?edita_curso>Nuevo curso</a>";
}
$header=<<<HEADER
$headbar
<table width=100% border=0>
<tr>
<td width=10%><image src="images/udea_fcen.jpg"/ height=120px></td>
<td valign=bottom>
  <b style='font-size:32'><a href=index.php>Plataforma de Información Curricular</a></b><br/>
  <b style='font-size:24'>Facultad de Ciencias Exactas y Naturales</b><br/>
  <b style='font-size:24'>Universidad de Antioquia</b><br/>
</td>
</table>
<hr/>
$menu
<hr/>
HEADER;
$errmsg=<<<ERR
$header
  <i style="color:red">Este contenido solo esta habilitado para un usuario autorizado</i>
ERR;

////////////////////////////////////////////////////
//PÁGINA PRINCIPAL
////////////////////////////////////////////////////
if(count(array_keys($_GET))<1 and count(array_keys($_POST))<1){
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

echo<<<MAIN
$header
<p style='font-size:16'>

Bienvenido al sistema de información curricular de la Facultad de
Ciencias Exactas y Naturales.<br/><br/>

En este sitio encontrará información sobre diversos aspectos del
currículo de los programas de la Facultad incluyendo acceso a los
documentos rectores de la Transformación Curricular de cada
dependencia, planes de estudio, entre otros.<br/><br/>

De acuerdo a sus necesidades escoja una de las siguientes opciones:
<ul>

  <li>
    <b>
      <aa href='?descargas'>Sección de Descargas</a>.
    </b>
    Aquí podrá descargar distintos documentos relacionados con la
    Transformación Curricular de la Facultad.
  </li>

  <li>
    <b>
      <aa href='?planes_estudio'>Planes de Estudio</a>.
    </b>
    Aquí podrá ver los planes de estudio de todos los programas de la
    Facultad.
  </li>

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
  $page="$header";
  $page.="<h2>Lista de Cursos</h2>";
  foreach(array_keys($INSTITUTOS) as $key){
    $instituto=$INSTITUTOS["$key"];
    if($instituto=="Facultad"){continue;}
    $page.="<h4>Instituto de $instituto</h4>";
    $sql="select 100_Codigo,110_Nombre_Asignatura,280_Instituto from MicroCurriculos where 280_Instituto='Instituto de $instituto' order by 330_Semestre_Plan;";
    //echo "$sql<br/>";
    if(!($out=mysqli_query($db,$sql))){
      die("Error:".mysqli_error($db));
    }
    $lista="";
    while($row=mysqli_fetch_array($out)){
      $codigo=$row[0];
      $nombre=$row[1];
      $instituto=$row[2];
      $lista.="<li>$nombre - $codigo ";
      if($QADMIN and ($instituto=="Instituto de $INSTITUTO" or $INSTITUTO=="Facultad")){
	$lista.="(";
	$lista.="<a href='?carga_curso=$codigo&edita_curso'>Cargar</a> - ";
	$lista.="<a href='?ver_curso=$codigo&mode=Plano'>Ver Plano</a> - ";
	$lista.="<a href='?ver_curso=$codigo&mode=FCEN'>Ver FCEN</a>";
	$lista.=")";
      }
      $lista.="</li>";
    }
    if(!preg_match("/\w+/",$lista)){$lista="<i>(No se encontraron cursos)</i>";}
    else{$lista.="</ul>";}
    $page.="<ul>$lista</ul>";
  }
	
  if($QADMIN){
    $sql="select 100_Codigo,110_Nombre_Asignatura,280_Instituto from MicroCurriculos_Recycle;";
    $out=mysqli_query($db,$sql);
    $recycle="";
    while($row=mysqli_fetch_array($out)){
      $codigo=$row[0];
      $nombre=$row[1];
      $instituto=$row[2];
      $recycle.="<li>$instituto - $nombre - $codigo (<a href='?carga_curso=$codigo&edita_curso&recover'>Recuperar</a>)</li>";
    }	
    if(!preg_match("/\w+/",$recycle)){$recycle="<i>(No se encontraron cursos)</i>";}
    else{$recycle.="</ul>";}
    $page.="<h4>Papelera de reciclaje</h4><ul>$recycle</ul>";
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
  $table="MicroCurriculos";
  if(isset($recover)){
    $table="MicroCurriculos_Recycle";
  }
  $sql="select * from $table where 100_Codigo='$carga_curso';";
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
  //CARGANDO TEXT INFORMATION
  $coursedir="data/$carga_curso";
  if(isset($recover)){$coursedir="recycle/$carga_curso";}
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
if(($accion=="Guardar" or $accion=="Reciclar") and $QADMIN){
  $table="MicroCurriculos";
  if($accion=="Reciclar"){
    $table="MicroCurriculos_Recycle";
  }
  ////////////////////////////////////////////////////
  //GUARDANDO REGISTRO
  ////////////////////////////////////////////////////
  //INSERT IF NOT EXISTS
  $name="100_Codigo";
  $codigo=$$name;
  $sql="insert into $table (100_Codigo) values (\"$codigo\") on duplicate key update 100_Codigo=\"$codigo\"";
  //echo "SQL:<pre>$sql</pre>";
  if(!mysqli_query($db,$sql)){
    die("Error:".mysqli_error($db));
  }
  //UPDATE IF EXISTS
  $sql="update $table set ";
  foreach($FIELDS as $field){
    $type=$DBASE[$field]["type"];
    if($field=="100_Codigo" or $type=="text"){continue;}
    $value=$$field;
    $sql.="$field='$value',";
  }
  $sql=trim($sql,",");
  $name="100_Codigo";
  $codigo=$$name;
  $sql.=" where 100_Codigo='$codigo';";
  //echo "SQL:<p>$sql</p>";
  if(!mysqli_query($db,$sql)){
    die("Error:".mysqli_error($db));
  }else if($accion!="Reciclar"){
    $result="<i style='color:blue'>Registro guardado exitosamente.</i>";
  }
  if($accion=="Reciclar"){
    $sql="delete from MicroCurriculos where 100_Codigo=\"$codigo\";";
    if(!mysqli_query($db,$sql)){
      die("Error:".mysqli_error($db));
    }else{
      $result="<i style='color:red'>Registro reciclado exitosamente.</i>";
    }
  }
  //SAVE TEXT FIELDS
  $coursedir="data/$codigo";
  if($accion=="Reciclar"){
    shell_exec("rm -rf $coursedir");
    $coursedir="recycle/$codigo";
  }
  $fc=fopen("$coursedir/notext.txt","w");
  foreach($FIELDS as $field){
    $value=$$field;
    $type=$DBASE[$field]["type"];
    if($type!="text"){
      fwrite($fc,"\$$field=\"$value\";\n");
    }else{
      system("mkdir -p \"$coursedir\"");
      $fl=fopen("$coursedir/$field.txt","w");
      fwrite($fl,$value);
      fclose($fl);
    }
  }
 }else if(!$QADMIN and ($accion=="Guardar" or $accion=="Reciclar")){echo $accion.$errmsg;return;}

////////////////////////////////////////////////////
//EDICIÓN DE UN CURSO
////////////////////////////////////////////////////
if(isset($edita_curso) and $QADMIN){
  $page="";

  //AUTORIZACION VICEDECANO
  $var="020_AUTH_Autorizacion_Vicedecano";
  $auto=$$var;
  if(isBlank($auto)){
    $auto=$DBASE["020_AUTH_Autorizacion_Vicedecano"]["default"];
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

$page.=<<<FORM
$header
  <h2>Edición de Plan de Asignatura</h2>
<div>
$result
</div>
<form action="index.php" method="post">
<input type='hidden' name='edita_curso' value=1>
FORM;

$buttons=<<<BUTTONS
<div style='position:fixed;right:0px;'>
<input type='submit' name='accion' value='Guardar'>
<input type='submit' name='accion' value='Reciclar'>
</div>
<br/><br/>
BUTTONS;
//$page.=$buttons;
 $form="";
  foreach($FIELDS as $field){
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
    $help=preg_replace("/\n/","<br/>",$help);
    //echo "FIELD:-$field-<br/>";
    //BLOCK
    $block="";
    $qauth=0;
    $display="block";

    //CAMPOS OCULTOS
    if(preg_match("/AUTH/",$field) and !$QAUTH){
      $input="$value<input type='hidden' name='$field' value='$value'><br/>";
      $qauth=1;
      $display="none";
      //echo "AUTH<br/>";
    }
    if(preg_match("/AUTO/",$field)){
      $block="disabled";
      //echo "AUTO<br/>";
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
    //CAMPOS DE ENTRADA SIMPLE
    if(!preg_match("/\w/",$values) and !$qauth){
      if(preg_match("/varchar\((\d+)\)/",$type,$matches)){
	$size=$matches[1];
	$input="<input type='text' name='$field' value='$value' size=$size $block>";
	if($block=="disabled"){
	  $input.="<input type='hidden' name='$field' value='$value'>";
	}
      }else if(!preg_match("/text/",$type)){
	$input="<input type='text' name='$field' value='$value' size=10 $block>";
      }else{
	$input="<textarea name='$field' rows=10 cols=80>$value</textarea>";
      }
    }
    //CAMPOS DE TEXTO
    else if(!$qauth){
      $input=generateSelection($values,$field,$value);
    }

$form.=<<<QUERY
<div style='display:$display'>
<b>$query</b>
<sup>
<a href="JavaScript:void(null)" onclick="$('#help_$field').toggle('fast',null);" style="font-size:10px">Ayuda</a>
</sup>
<br/>
$input
<div id="help_$field" style="display:none;font-style:italic;background-color:lightblue;width:600px;padding:10px">$help</div>
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
  $sql="select * from $table where 100_Codigo='$ver_curso';";
  $out=mysqli_query($db,$sql);
  if(!($row=mysqli_fetch_array($out))){die("Error:".mysqli_error($db));}

  //CARGA VALORES EN VARIABLES
  foreach($FIELDS as $field){
    $type=$DBASE[$field]["type"];
    if($type=="text"){continue;}
    $$field=$row["$field"];
  }

  //RECUPERA INFORMACIÓN DEL CURSO DE ARCHIVOS
  $coursedir="data/$ver_curso";
  foreach($FIELDS as $field){
    $value=$$field;
    $fname=preg_replace("/^\d+_/","",$field);
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
  //TITULO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $var="110_Nombre_Asignatura";
  $curso=$$var;
  $border="border:1px solid;";
  $colorgray="background-color:lightgray";
  $heavygray="background-color:gray";
  
$page.=<<<TITULO
<p>
<b>Curso</b>
<br/>
<b style='font-size:20px'>$curso</b>
</p>
TITULO;

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //GENERA ARCHIVO EN FORMATO REQUERIDO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
 if($mode=="Plano"){
    $table="";
$table.=<<<TABLE
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<h1>$curso</h1>
TABLE;
    $table.="<table border=0 width=650 style='border-collapse:collapse;'>";
    foreach($FIELDS as $field){
      $value=$$field;
      $value=preg_replace("/\n/","<br/>",$value);
      $query=$DBASE[$field]["query"];
      $type=$DBASE[$field]["type"];
      if(preg_match("/Unidad/",$field) and !preg_match("/\w/",$value)){
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
    $coursedir="data/$ver_curso";
    $fl=fopen("$coursedir/$ver_curso-plano.html","w");
    shell_exec("cd $coursedir;unoconv $ver_curso-plano.html $ver_curso-plano.pdf");
    fwrite($fl,$table);
    fclose($fl);
$page.=<<<DESCARGA
<a href=$coursedir/$ver_curso-plano.html target=_blank>
  Formato plano
</a><br/>
<a href=$coursedir/$ver_curso-plano.pdf target=_blank>
  Descarga en pdf
</a><br/>
DESCARGA;
  }
  
  if($mode=="FCEN"){
    //UNIDADES
    $unidades="";
    for($i=1;$i<=10;$i++){
      $var="Unidad$i_Titulo";
      $titulo=$$var;
      echo "Unidad $i:$titulo<br/>";
      if(isBlank($titulo)){break;}
      $var="Unidad$i_Conceptual";
      $conceptual=$$var;
      $var="Unidad$i_Procedimental";
      $procedimental=$$var;
      $var="Unidad$i_Actitudinal";
      $procedimental=$$var;
$unidades.=<<<UNIDADES
<b>Unidad $i. $titulo</b><br/><br/>
  <p>Contenidos coneptuales:</p>
	<blockquote>$conceptuales</blockquote>
  <p>Contenidos procedimentales</p>:
 	<blockquote>$procedimentales</blockquote>
  <p>Contenidos actitudinales</p>:
 	<blockquote>$actitudinales</blockquote>
UNIDADES;
    }

    $table="";
$table.=<<<TABLE
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
  <table border=0 width=650>
    <tr>
      <td><img src="../../images/udea.jpg" width=100px/></td>
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
      <td style='$border;'></td>
      <td style='$border;$colorgray;'>DEL</td>
      <td style='$border;'></td>
    </tr>
  </table>
  <p style='width:650px;text-align:center;'>FORMATO DE MICROCURRICULO O PLAN DE ASIGNATURA</p>
  <table border=0 width=650 style='border-collapse:collapse'>
    <tr>
      <td width=30%></td><td width=20%></td><td width=20%></td><td width=20%></td>
    </tr>
    <tr>
      <td style='$border;$heavygray;' colspan=4>
	1. IDENTIFICACIÓN GENERAL
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
	2. IDENTIFICACIÓN ESPECÍFICA
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
      <td style='$border;$colorgray;' colspan=1>Intensidad horaria:</td>
      <td colspan=1 style='$border;'>HDD:$Intensidad_HDD</td>
      <td colspan=1 style='$border;'>HDA:$Intensidad_HDA</td>
      <td colspan=1 style='$border;'>TI:$Intensidad_TI</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Semanas</td><td style='$border;' colspan=3>$Semanas</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Curso</td>
      <td style='$border;' colspan=1>Teórico: $Teorico</td>
      <td style='$border;' colspan=1>Práctico: $Practico</td>
      <td style='$border;' colspan=1>Teórico-Práctico: $Teorico_Practico</td>
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
	3. DATOS DE LOS PROFESORES QUE ELABORAN EL PLAN DE ASIGNATURA
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
    <tr><td style='$border;$heavygray;' colspan=4>4. DESCRIPCIÓN</td></tr>
    <tr><td style='$border;' colspan=4>$Descripcion</td>
    <tr><td style='$border;$heavygray;' colspan=4>5. JUSTIFICACIÓN</td></tr>
    <tr><td style='$border;' colspan=4>$Justificacion</td>
    <tr><td style='$border;$heavygray;' colspan=4>6. OBJETIVOS</td></tr>
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
    <tr><td style='$border;$heavygray;' colspan=4>7. CONTENIDOS</td></tr>
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
    <tr><td style='$border;$heavygray;' colspan=4>8. ESTRATEGIAS METODOLÓGICAS</td></tr>
    <tr><td style='$border;' colspan=4>$Estrategia_Metodologica</td>
    <tr><td style='$border;$heavygray;' colspan=4>9. EVALUACIÓN</td></tr>
    <tr><td style='$border;' colspan=4>$Evaluacion</td>
    <tr><td style='$border;$heavygray;' colspan=4>10. BIBLIOGRAFÍA</td></tr>
    <tr><td style='$border;' colspan=4>$Bibliografia_General</td>
  </table>
	 
</body>
</html>
TABLE;
    $coursedir="data/$ver_curso";
    $fl=fopen("$coursedir/$ver_curso-FCEN.html","w");
    shell_exec("cd $coursedir;unoconv $ver_curso-FCEN.html $ver_curso-FCEN.pdf");
    fwrite($fl,$table);
    fclose($fl);
$page.=<<<DESCARGA
<a href=$coursedir/$ver_curso-FCEN.html target=_blank>
  Formato FCEN
</a><br/>
<a href=$coursedir/$ver_curso-FCEN.pdf target=_blank>
  Descarga en pdf
</a><br/>
DESCARGA;
  }  
  echo $page;
  return;
}
if(isset($entra_curso)){
}
if($lista){
}
?>

<?
////////////////////////////////////////////////////
//FOOTER
////////////////////////////////////////////////////
?>
<hr/>
<p style='font-size:12px'>
<a href=mailto:jorge.zuluaga@udea.edu.co>Jorge I. Zuluaga</a> (C) 2014
</p>
</body>
</html>
