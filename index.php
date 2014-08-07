<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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


////////////////////////////////////////////////////
//DATABASE
////////////////////////////////////////////////////
$db=mysqli_connect("localhost",$USER,$PASSWORD,$DATABASE);

////////////////////////////////////////////////////
//COMMON
////////////////////////////////////////////////////
echo<<<CURRICULO
<h1>Microcurriculos</h1>
<h3>
<a href="?">Lista Cursos</a> -
<a href="?entra_curso">Nuevo Curso</a>
</h3>
CURRICULO;
if($accion=="Guardar" or $accion=="Reciclar"){
  $table="MicroCurriculos";
  if($accion=="Reciclar"){$table="MicroCurriculos_Recycle";}
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
    echo "<i style='color:blue'>Registro guardado exitosamente.</i>";
  }
  if($accion=="Reciclar"){
    $sql="delete from MicroCurriculos where 100_Codigo=\"$codigo\";";
    if(!mysqli_query($db,$sql)){
      die("Error:".mysqli_error($db));
    }else{
      echo "<i style='color:red'>Registro reciclado exitosamente.</i>";
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
}
if(isset($carga_curso)){
  ////////////////////////////////////////////////////
  //CARGANDO REGISTRO
  ////////////////////////////////////////////////////
  $table="MicroCurriculos";
  if(isset($recover)){$table="MicroCurriculos_Recycle";}
  $sql="select * from $table where 100_Codigo='$carga_curso';";
  $out=mysqli_query($db,$sql);
  if(!($row=mysqli_fetch_array($out))){
    die("Error:".mysqli_error($db));
  }else{
    echo "<i style='color:blue'>Curso $carga_curso cargado exitosamente.</i>";
  }
  //print_r($row);
  foreach($FIELDS as $field){
    $type=$DBASE[$field]["type"];
    if($type=="text"){continue;}
    $$field=$row["$field"];
    //echo "Normal $field = ".$$field;echo "<br/>";
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
    //echo "Text $field = ".$$field;echo "<br/>";
  }
}
if(isset($ver_curso)){
  ////////////////////////////////////////////////////
  //VIENDO CURSO
  ////////////////////////////////////////////////////
  $table="MicroCurriculos";
  $sql="select * from $table where 100_Codigo='$ver_curso';";
  $out=mysqli_query($db,$sql);
  if(!($row=mysqli_fetch_array($out))){die("Error:".mysqli_error($db));}
  else{
    echo "<p style='color:blue'>Curso $ver_curso generado exitosamente.</p>";
  }
  foreach($FIELDS as $field){
    $type=$DBASE[$field]["type"];
    if($type=="text"){continue;}
    $$field=$row["$field"];
  }
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
  //MODO DE VISUALIZACION
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($mode=="Todo"){
    $table="";
$table.=<<<TABLE
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
TABLE;
    $table.="<table border=0 width=650 style='border-collapse:collapse;'>";
    $border="border:1px solid;";
    $colorgray="lightgray";
    foreach($FIELDS as $field){
      $value=$$field;
      $value=preg_replace("/\n/","<br/>",$value);
      $query=$DBASE[$field]["query"];
      $type=$DBASE[$field]["type"];
      if(preg_match("/Unidad/",$field) and !preg_match("/\w/",$value)){
	break;
      }
      if($type!="text"){
	$table.="<tr><td style='$border;background-color:$colorgray;' width=30%><b>$query</b></td>";
	$table.="<td style='$border' width=60%>$value</td></tr>";
      }else{
	$table.="<tr><td style='$border;background-color:$colorgray;' colspan=2><b>$query</b></td></tr>";
	$table.="<tr><td style='$border;' colspan=2>$value</td></tr>";
      }
    }
    $table.="</table>";
    $coursedir="data/$ver_curso";
    $fl=fopen("$coursedir/$ver_curso.html","w");
    fwrite($fl,$table);
    fclose($fl);
    echo "<a href=$coursedir/$ver_curso.html>Ver Documento</a>";
  }

  
  if($mode=="FCEN"){
    $table="";
    $border="border:1px solid";
    $colorgray="background-color:lightgray";
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
      <td width=25%></td><td width=25%></td><td width=25%></td><td width=25%></td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=4>
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
      <td style='$border;$colorgray;' colspan=2>Programa(s) Académicos</td><td colspan=2 style='$border;'>$Programas_Academicos</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=2>Área Académica</td><td colspan=2 style='$border;'>$Area_Academica</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Ciclo</td><td colspan=1 style='$border;'>$Ciclo</td>
      <td style='$border;' colspan=1>Tipo de Curso</td><td colspan=1 style='$border;'>$Tipo_Curso</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Profesores Responsables:</td><td colspan=3 style='$border;'>$Profesores_Responsables</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;'>Asistencia:</td><td colspan=3 style='$border;'>$Asistencia</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=4>
	2. IDENTIFICACIÓN ESPECÍFICA
      </td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=2>Nombre de la Asignatura:</td><td colspan=2 style='$border;'>$Nombre_Asignatura</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Código:</td><td colspan=3 style='$border;'>$Codigo</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=3>Semestre en el plan de Formación: I</td>
      <td style='$border;$colorgray;' colspan=1>Número de Créditos: $Creditos</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Intensidad horaria:</td>
      <td colspan=1 style='$border;$colorgray;'>HDD:$Intensidad_HDD</td>
      <td colspan=1 style='$border;$colorgray;'>HDA:$Intensidad_HDA</td>
      <td colspan=1 style='$border;$colorgray;'>TI:$Intensidad_TI</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Semana:</td>
      <td style='$border;' colspan=1>$Semanas</td>
      <td style='$border;$colorgray;' colspan=1>Semestre:</td>
      <td style='$border;' colspan=1>$Semestre</td>
    </tr>
    <tr>
      <td style='$border;$colorgray;' colspan=1>Teórico: $Teorico</td>
      <td style='$border;$colorgray;' colspan=1>Práctico: $Practico</td>
      <td style='$border;$colorgray;' colspan=2>Teórico-Práctico: $Teorico_Practico</td>
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
    <tr><td style='$border;$colorgray;' colspan=4>Prerrequisitos</td></tr>
    <tr><td style='$border;' colspan=4>$Requisitos</td></tr>
    <tr><td style='$border;$colorgray;' colspan=4>Correquisitos</td></tr>
    <tr><td style='$border;' colspan=4>$Correquisitos</td></tr>
    <tr><td style='$border;$colorgray;' colspan=4>Sede en la que se dicta la asignatura:</td></tr>
    <tr><td style='$border;' colspan=4>$Sede</td></tr>
    <tr>
      <td style='$border;$colorgray;' colspan=4>
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
    <tr><td style='$border;' colspan=4><i style='color:white'>BLANK</i></td></tr>
    <tr><td style='$border;$colorgray;' colspan=4>4. DESCRIPCIÓN</td></tr>
    <tr><td style='$border;' colspan=4>$Descripcion</td>
    <tr><td style='$border;' colspan=4><i style='color:white'>BLANK</i></td></tr>
    <tr><td style='$border;$colorgray;' colspan=4>5. JUSTIFICACIÓN</td></tr>
    <tr><td style='$border;' colspan=4>$Justificacion</td>
    <tr><td style='$border;' colspan=4><i style='color:white'>BLANK</i></td></tr>
    <tr><td style='$border;$colorgray;' colspan=4>6. OBJETIVOS</td></tr>
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
    <tr><td style='$border;' colspan=4><i style='color:white'>BLANK</i></td></tr>
    <tr><td style='$border;$colorgray;' colspan=4>7. CONTENIDOS</td></tr>
    <tr>
      <td colspan=4 style='$border;'>
      <p><b>Contenido Resumido</b></p>
      <blockquote>$Contenido_Resumido</blockquote>
      </td>
    </tr>
    <tr><td style='$border;' colspan=4><i style='color:white'>BLANK</i></td></tr>
    <tr><td style='$border;$colorgray;' colspan=4>8. ESTRATEGIAS METODOLÓGICAS</td></tr>
    <tr><td style='$border;' colspan=4>$Estrategia_Metodologica</td>
    <tr><td style='$border;$colorgray;' colspan=4>9. EVALUACIÓN</td></tr>
    <tr><td style='$border;' colspan=4>$Evaluacion</td>
    <tr><td style='$border;$colorgray;' colspan=4>10. BIBLIOGRAFÍA</td></tr>
    <tr><td style='$border;' colspan=4>$Unidad1_Bibliografia</td>
  </table>
	 
</body>
</html>
TABLE;
    $coursedir="data/$ver_curso";
    $fl=fopen("$coursedir/$ver_curso.html","w");
    fwrite($fl,$table);
    fclose($fl);
    echo "<a href=$coursedir/$ver_curso.html>Ver Documento</a>";
  }

  return;
}
if(isset($entra_curso)){
  ////////////////////////////////////////////////////
  //COURSE FORM
  ////////////////////////////////////////////////////
echo<<<FORM
<h2>Entrada de Plan de Asignatura</h2>
<form method="post">
<input type='hidden' name='entra_curso' value=1>
FORM;
  $buttons=<<<BUTTONS
<input type='submit' name='accion' value='Guardar'>
<input type='submit' name='accion' value='Reciclar'>
<br/><br/>
BUTTONS;
  echo $buttons;
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
    if(!preg_match("/\w/",$values)){
      if(preg_match("/varchar\((\d+)\)/",$type,$matches)){
	$size=$matches[1];
	$input="<input type='text' name='$field' value='$value' size=$size>";
      }else{
	$input="<textarea name='$field' rows=10 cols=80>$value</textarea>";
      }
    }else{
      $input=generateSelection($values,$field,$value);
    }
echo<<<QUERY
<b>$query</b>
<br/>
$input
<br/>
<i>$help</i>
<br/><br/>
QUERY;
  }
echo<<<FORM
$buttons
</form>
FORM;
}else{

  $sql="select 100_Codigo,110_Nombre_Asignatura,280_Instituto from MicroCurriculos order by 280_Instituto;";
  $out=mysqli_query($db,$sql);
  $lista="";
  $lista.="<ul>";
  while($row=mysqli_fetch_array($out)){
    $codigo=$row[0];
    $nombre=$row[1];
    $instituto=$row[2];
    $lista.="<li>$instituto - $nombre - $codigo (";
    $lista.="<a href='?carga_curso=$codigo&entra_curso'>Cargar</a> - ";
    $lista.="<a href='?ver_curso=$codigo&mode=Todo'>Todo</a> - ";
    $lista.="<a href='?ver_curso=$codigo&mode=FCEN'>FCEN</a> - ";
    $lista.="<a href='?ver_curso=$codigo&mode=Vicedocencia'>Vicedocencia</a>";
    $lista.=")</li>";
  }	
  $lista.="</ul>";

  $sql="select 100_Codigo,110_Nombre_Asignatura,280_Instituto from MicroCurriculos_Recycle;";
  $out=mysqli_query($db,$sql);
  $recycle="";
  $recycle.="<ul>";
  while($row=mysqli_fetch_array($out)){
    $codigo=$row[0];
    $nombre=$row[1];
    $instituto=$row[2];
    $recycle.="<li>$instituto - $nombre - $codigo (<a href='?carga_curso=$codigo&entra_curso&recover'>Recuperar</a>)</li>";
  }	
  $recycle.="</ul>";

echo<<<LISTA
  <h2>Lista de Cursos</h2>
  $lista
  <h3>Reciclaje</h3>
  $recycle
LISTA;
}
?>

<?
////////////////////////////////////////////////////
//FOOTER
////////////////////////////////////////////////////
?>
<hr/>
<a href=mailto:zuluagajorge@gmail.com>Jorge I. Zuluaga</a> (CC) 2014
</body>
</html>
