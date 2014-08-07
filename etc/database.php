
<?

$FIELDS=array("100_Codigo","110_Nombre_Asignatura","120_Tipo_Curso","130_Asistencia","140_Creditos","150_Intensidad_HDD","160_Intensidad_HDA","170_Intensidad_TI","180_Horas_Teoricas_Semanales","190_Horas_Teoricas_Semestrales","200_Semanas","210_Teorico","220_Practico","230_Teorico_Practico","240_Habilitable","250_Validable","260_Clasificable","270_Facultad","280_Instituto","290_Programas_Academicos","300_Area_Academica","310_Campo_Formacion","320_Ciclo","330_Semestre","330_Semestre_Plan","340_Horario_clase","350_Requisitos","360_Correquisitos","370_Sede","380_Profesores_Responsables","390_Profesores_Oficinas","400_Horario_atencion","410_Profesores_Elaboran","420_Correos_Electronicos","430_Descripcion","440_Proposito","450_Justificacion","460_Objetivo_General","470_Objetivos_Especificos_Conceptuales","480_Objetivos_Especificos_Procedimentales","490_Objetivos_Especificos_Actitudinales","500_Estrategia_Metodologica","510_Evaluacion","520_Actividades_Obligatorias","530_Contenido_Resumido","601_Unidad1_Conceptual","602_Unidad1_Procedimental","603_Unidad1_Actitudinal","604_Unidad1_Bibliografia","605_Unidad1_Semanas","611_Unidad2_Conceptual","612_Unidad2_Procedimental","613_Unidad2_Actitudinal","614_Unidad2_Bibliografia","615_Unidad2_Semanas","621_Unidad3_Conceptual","622_Unidad3_Procedimental","623_Unidad3_Actitudinal","624_Unidad3_Bibliografia","625_Unidad3_Semanas","631_Unidad4_Conceptual","632_Unidad4_Procedimental","633_Unidad4_Actitudinal","634_Unidad4_Bibliografia","635_Unidad4_Semanas","641_Unidad5_Conceptual","642_Unidad5_Procedimental","643_Unidad5_Actitudinal","644_Unidad5_Bibliografia","645_Unidad5_Semanas","651_Unidad6_Conceptual","652_Unidad6_Procedimental","653_Unidad6_Actitudinal","654_Unidad6_Bibliografia","655_Unidad6_Semanas","661_Unidad7_Conceptual","662_Unidad7_Procedimental","663_Unidad7_Actitudinal","664_Unidad7_Bibliografia","665_Unidad7_Semanas","671_Unidad8_Conceptual","672_Unidad8_Procedimental","673_Unidad8_Actitudinal","674_Unidad8_Bibliografia","675_Unidad8_Semanas","681_Unidad9_Conceptual","682_Unidad9_Procedimental","683_Unidad9_Actitudinal","684_Unidad9_Bibliografia","685_Unidad9_Semanas","691_Unidad10_Conceptual","692_Unidad10_Procedimental","693_Unidad10_Actitudinal","694_Unidad10_Bibliografia","695_Unidad10_Semanas",);
$DBASE=array(
'100_Codigo'=>array('query'=>'Codigo Curso','type'=>'varchar(10)','default'=>'0300000','values'=>'','help'=>'El código del curso tiene 6 dígitos'),

'110_Nombre_Asignatura'=>array('query'=>'Nombre Asignatura','type'=>'varchar(100)','default'=>'Curso','values'=>'','help'=>'Entre el nombre completo del curso'),

'120_Tipo_Curso'=>array('query'=>'Tipo de Curso','type'=>'varchar(20)','default'=>'Profesional','values'=>'Básico,Profesional,Profundización','help'=>'Tipo de curso deacuerdo a ubicación en el pensum'),

'130_Asistencia'=>array('query'=>'Tipo de Asistencia','type'=>'varchar(50)','default'=>'Obligatoria','values'=>'Obligatoria,No obligatoria','help'=>'Indique el tipo de asistencia'),

'140_Creditos'=>array('query'=>'Numero de Creditos','type'=>'varchar(3)','default'=>'4','values'=>'0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15','help'=>'Indique el número de créditos'),

'150_Intensidad_HDD'=>array('query'=>'Horas de Docencia Directa (HDD)','type'=>'varchar(3)','default'=>'4','values'=>'','help'=>'Indique el número de horas de docencia directa por semana'),

'160_Intensidad_HDA'=>array('query'=>'Horas de Docencia Asistida','type'=>'varchar(3)','default'=>'0','values'=>'','help'=>'Indique el número de horas de docencia directa por semana'),

'170_Intensidad_TI'=>array('query'=>'Horas de Trabajo Independiente','type'=>'varchar(3)','default'=>'4','values'=>'','help'=>'Indique el número de horas de trabajo independiente por semana'),

'180_Horas_Teoricas_Semanales'=>array('query'=>'Horas teóricas semanales','type'=>'varchar(3)','default'=>'4','values'=>'','help'=>'Indique el número de horas teóricas por semana'),

'190_Horas_Teoricas_Semestrales'=>array('query'=>'Horas teóricas semestrales','type'=>'varchar(3)','default'=>'64','values'=>'','help'=>'Indique el número de horas teóricas por semestre'),

'200_Semanas'=>array('query'=>'Número de semanas','type'=>'varchar(3)','default'=>'16','values'=>'','help'=>'Número de semanas por semestre'),

'210_Teorico'=>array('query'=>'Curso teórico','type'=>'varchar(2)','default'=>'Si','values'=>'Si,No','help'=>'Indique si es un curso teórico'),

'220_Practico'=>array('query'=>'Curso práctico','type'=>'varchar(2)','default'=>'No','values'=>'Si,No','help'=>'Indique si es un curso práctico'),

'230_Teorico_Practico'=>array('query'=>'Curso teórico-práctico','type'=>'varchar(2)','default'=>'No','values'=>'Si,No','help'=>'Indique si es un curso práctico'),

'240_Habilitable'=>array('query'=>'Curso habilitable','type'=>'varchar(2)','default'=>'Si','values'=>'Si,No','help'=>'Indique si es un curso habilitable'),

'250_Validable'=>array('query'=>'Curso validable','type'=>'varchar(2)','default'=>'Si','values'=>'Si,No','help'=>'Indique si es un curso validable'),

'260_Clasificable'=>array('query'=>'Curso clasificable','type'=>'varchar(2)','default'=>'No','values'=>'Si,No','help'=>'Indique si es un curso clasificable'),

'270_Facultad'=>array('query'=>'Facultad','type'=>'varchar(50)','default'=>'Facultad de Ciencias Exactas y Naturales','values'=>'','help'=>'Facultad'),

'280_Instituto'=>array('query'=>'Instituto','type'=>'varchar(50)','default'=>'Instituto de Física','values'=>'Instituto de Física,Instituto de Química,Instituto de Biología,Instituto de Matemáticas','help'=>'Instituto'),

'290_Programas_Academicos'=>array('query'=>'Programas académicos a los que se ofrece','type'=>'varchar(80)','default'=>'Astronomía,Física','values'=>'','help'=>''),

'300_Area_Academica'=>array('query'=>'Área académica','type'=>'varchar(50)','default'=>'Historia de la Astronomía','values'=>'','help'=>'Indique el área específica en la que se enmarca el curso'),

'310_Campo_Formacion'=>array('query'=>'Campo de formación','type'=>'varchar(50)','default'=>'Astronomía','values'=>'','help'=>'Indique el área de formación'),

'320_Ciclo'=>array('query'=>'Ciclo','type'=>'varchar(30)','default'=>'Fundamentación','values'=>'Fundamentación,Profesionalización,Profundización','help'=>'Ciclo'),

'330_Semestre'=>array('query'=>'Semestre actual','type'=>'varchar(10)','default'=>'2014-1','values'=>'','help'=>'Indique el semestre de validez del presente programa'),

'330_Semestre_Plan'=>array('query'=>'Semestre en el Plan de Formación','type'=>'varchar(3)','default'=>'2014-1','values'=>'','help'=>'Indique el semestre en el plan de formación'),

'340_Horario_clase'=>array('query'=>'Horario de clase','type'=>'varchar(20)','default'=>'MJ8-10','values'=>'','help'=>''),

'350_Requisitos'=>array('query'=>'Prerrequisitos','type'=>'varchar(100)','default'=>'(Ninguno)','values'=>'','help'=>''),

'360_Correquisitos'=>array('query'=>'Correquisitos','type'=>'varchar(100)','default'=>'(Ninguno)','values'=>'','help'=>''),

'370_Sede'=>array('query'=>'Sede en el que se ofrece','type'=>'varchar(20)','default'=>'Medellín','values'=>'','help'=>''),

'380_Profesores_Responsables'=>array('query'=>'Profesores Responsables','type'=>'varchar(100)','default'=>'Jorge Zuluaga','values'=>'','help'=>'Indique el(los) profesor(es) que ofrecen el curso en el semestre de validez del programa'),

'390_Profesores_Oficinas'=>array('query'=>'Oficina de Profesores','type'=>'varchar(50)','default'=>'6-414','values'=>'','help'=>'Indique las oficinas de los profesores'),

'400_Horario_atencion'=>array('query'=>'Horario de los profesores','type'=>'varchar(50)','default'=>'MJ 16-18','values'=>'','help'=>'Indique el horario de atención de los profesores'),

'410_Profesores_Elaboran'=>array('query'=>'Profesores que elaboran','type'=>'varchar(100)','default'=>'Jorge Zuluaga','values'=>'','help'=>'Indique el nombre de los profesores que elaboran esta versión del programa'),

'420_Correos_Electronicos'=>array('query'=>'Correos electronicos de profesores que elaboran','type'=>'varchar(100)','default'=>'jorge.zuluaga@udea.edu.co','values'=>'','help'=>''),

'430_Descripcion'=>array('query'=>'Descripción','type'=>'text','default'=>'Explique en que consiste el curso','values'=>'','help'=>''),

'440_Proposito'=>array('query'=>'Propósito','type'=>'text','default'=>'El propósito del curso es','values'=>'','help'=>''),

'450_Justificacion'=>array('query'=>'Justificación','type'=>'text','default'=>'La justificación del curso es','values'=>'','help'=>''),

'460_Objetivo_General'=>array('query'=>'Objetivo General','type'=>'text','default'=>'','values'=>'','help'=>''),

'470_Objetivos_Especificos_Conceptuales'=>array('query'=>'Objetivos específicos conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'480_Objetivos_Especificos_Procedimentales'=>array('query'=>'Objetivos específicos procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'490_Objetivos_Especificos_Actitudinales'=>array('query'=>'Objetivos específicos actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'500_Estrategia_Metodologica'=>array('query'=>'Estrategia metodológica','type'=>'text','default'=>'','values'=>'','help'=>''),

'510_Evaluacion'=>array('query'=>'Evaluacion','type'=>'text','default'=>'','values'=>'','help'=>''),

'520_Actividades_Obligatorias'=>array('query'=>'Actividades de asistencia obligatoria','type'=>'text','default'=>'','values'=>'','help'=>''),

'530_Contenido_Resumido'=>array('query'=>'Contenido Resumido','type'=>'text','default'=>'','values'=>'','help'=>''),

'601_Unidad1_Conceptual'=>array('query'=>'Unidad 1 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'602_Unidad1_Procedimental'=>array('query'=>'Unidad 1 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'603_Unidad1_Actitudinal'=>array('query'=>'Unidad 1 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'604_Unidad1_Bibliografia'=>array('query'=>'Unidad 1 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'605_Unidad1_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),

'611_Unidad2_Conceptual'=>array('query'=>'Unidad 2 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'612_Unidad2_Procedimental'=>array('query'=>'Unidad 2 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'613_Unidad2_Actitudinal'=>array('query'=>'Unidad 2 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'614_Unidad2_Bibliografia'=>array('query'=>'Unidad 2 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'615_Unidad2_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),

'621_Unidad3_Conceptual'=>array('query'=>'Unidad 3 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'622_Unidad3_Procedimental'=>array('query'=>'Unidad 3 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'623_Unidad3_Actitudinal'=>array('query'=>'Unidad 3 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'624_Unidad3_Bibliografia'=>array('query'=>'Unidad 3 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'625_Unidad3_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),

'631_Unidad4_Conceptual'=>array('query'=>'Unidad 4 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'632_Unidad4_Procedimental'=>array('query'=>'Unidad 4 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'633_Unidad4_Actitudinal'=>array('query'=>'Unidad 4 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'634_Unidad4_Bibliografia'=>array('query'=>'Unidad 4 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'635_Unidad4_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),

'641_Unidad5_Conceptual'=>array('query'=>'Unidad 5 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'642_Unidad5_Procedimental'=>array('query'=>'Unidad 5 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'643_Unidad5_Actitudinal'=>array('query'=>'Unidad 5 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'644_Unidad5_Bibliografia'=>array('query'=>'Unidad 5 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'645_Unidad5_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),

'651_Unidad6_Conceptual'=>array('query'=>'Unidad 6 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'652_Unidad6_Procedimental'=>array('query'=>'Unidad 6 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'653_Unidad6_Actitudinal'=>array('query'=>'Unidad 6 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'654_Unidad6_Bibliografia'=>array('query'=>'Unidad 6 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'655_Unidad6_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),

'661_Unidad7_Conceptual'=>array('query'=>'Unidad 7 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'662_Unidad7_Procedimental'=>array('query'=>'Unidad 7 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'663_Unidad7_Actitudinal'=>array('query'=>'Unidad 7 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'664_Unidad7_Bibliografia'=>array('query'=>'Unidad 7 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'665_Unidad7_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),

'671_Unidad8_Conceptual'=>array('query'=>'Unidad 8 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'672_Unidad8_Procedimental'=>array('query'=>'Unidad 8 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'673_Unidad8_Actitudinal'=>array('query'=>'Unidad 8 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'674_Unidad8_Bibliografia'=>array('query'=>'Unidad 8 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'675_Unidad8_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),

'681_Unidad9_Conceptual'=>array('query'=>'Unidad 9 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'682_Unidad9_Procedimental'=>array('query'=>'Unidad 9 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'683_Unidad9_Actitudinal'=>array('query'=>'Unidad 9 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'684_Unidad9_Bibliografia'=>array('query'=>'Unidad 9 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'685_Unidad9_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),

'691_Unidad10_Conceptual'=>array('query'=>'Unidad 10 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>''),

'692_Unidad10_Procedimental'=>array('query'=>'Unidad 10 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>''),

'693_Unidad10_Actitudinal'=>array('query'=>'Unidad 10 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>''),

'694_Unidad10_Bibliografia'=>array('query'=>'Unidad 10 - Bibliografia','type'=>'text','default'=>'','values'=>'','help'=>'Si es la misma, repita este campo en todas las unidades.'),

'695_Unidad10_Semanas'=>array('query'=>'Semanas para la Unidad','type'=>'varchar(3)','default'=>'3','values'=>'','help'=>''),
);
?>
