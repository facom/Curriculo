#-*-coding:utf-8-*-
from curriculo import *

###################################################
#DATABASE
###################################################
"""
Field structure:
0:Type
1:Query
2:Default
3:Values (for list values)
4:Help
"""
Database={
    #META
    "F010_AUTO_Fecha_Actualizacion":
        [
        "varchar(30)",
        "Fecha de actualización",
        "",
        "",
        "Fecha en la que se realiza la actualización."
        ],
    "F015_AUTO_Usuario_Actualizacion":
        [
        "varchar(30)",
        "Usuario que realiza la actualización",
        "",
        "",
        "Este es el usuario administrativo que esta realizando esta actualización."
        ],
    "F020_AUTH_Autorizacion_Vicedecano":
        [
        "varchar(30)",
        "Autorización Vicedecano",
        "No",
        "Si,No",
        "Una vez el vicedecano autoriza el curso no puede ser editado por ningún otro usuario autorizado.  El curso solo puede volverse a editar cuando el vicedecano cambie este campo a No."
        ],
    "F025_AUTH_Version":
        [
        "varchar(3)",
        "Última versión del curso",
        "1",
        "",
        "Última versión del curso."
        ],
    "F030_AUTH_Acta_Numero":
        [
        "varchar(4)",
        "Número de Acta del Consejo de Facultad",
        "00",
        "",
        "Número de acta en el que el curso fue aprobado.  Si el número de acta es 00 el curso nunca ha sido aprobado. Si es distinto pero el Vicedecano no lo ha aprobado esta acta corresponde a la versión anterior del curso."
        ],
    "F040_AUTH_Acta_Fecha":
        [
        "varchar(30)",
        "Fecha del Acta del Consejo de Facultad",
        "MM/DD/CCYY",
        "",
        "Fecha del acta del Consejo de Facultad. Si la fecha es MM/DD/CCYY el curso nunca ha sido aprobado.  Si es distinto pero el Vicedecano no lo ha aprobado esta acta corresponde a la versión anterior del curso."
        ],
    "F050_Nombre_Actualiza":
        [
        "varchar(30)",
        "Nombre de quien modifica esta última versión",
        "Jorge I. Zuluaga",
        "",
        "Indique el nombre de quien esta modificando esta última versión del curso."
        ],
    "F060_Publica_Curso":
        [
        "varchar(3)",
        "Publica curso",
        "No",
        "Si,No",
        "Si coloca *Si* el curso será visible por usuarios no autorizados."
        ],

    #IDENTIFICACION
    "F100_Codigo":
        [
        "varchar(10)",
         "Codigo Curso",
         "0300000",
         "",
         "El código del curso tiene 6 dígitos: FFPPNNN, donde FF es la Facultad (03, FCEN), PP es el Programa (11, Astronomía), NNN es el número del curso"
        ],

    #PROPIEDADES BASICAS
    "F110_Nombre_Asignatura":
        [
        "varchar(100)",
        "Nombre de la Asignatura",
        "Fundamentación en Ciencias",
        "",
        "El nombre completo del curso debe coincidir con el que esta en el sistema Mares"
        ],
    "F120_Tipo_Curso":
        [
        "varchar(20)",
        "Tipo de Curso",
        "Profesional",
        "Básico,Profesional,Profundización",
        "Tipo de curso deacuerdo a su ubicación en el pensum."
        ],
    "F130_Asistencia":
        [
        "varchar(50)",
        "Tipo de Asistencia",
        "Obligatoria",
        "Obligatoria,No obligatoria",
        "Indique el tipo de asistencia. El Comité de Curriculo define normalmente qué tipo de cursos son de asistencia obligatoria."
        ],
    "F140_Creditos":
        [
        "varchar(3)",
        "Numero de Creditos",
        "4",
        "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15",
        "Indique el número de créditos.  De acuerdo al 1295 cada crédito corresponde a 3 horas de trabajo en el curso."
        ],
    "F150_Intensidad_HDD":
        [
        "varchar(3)",
        "Horas de Docencia Directa (HDD)",
        "64",
        "0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas de docencia directa por semestre. Equivalen al número de horas por semana multiplicado por 16."
        ],
    "F160_Intensidad_HDA":
        [
        "varchar(3)",
        "Horas de Docencia Asistida",
        "0",
        "0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas de docencia directa por semana. Equivalen al número de horas por semana multiplicado por 16."
        ],
    "F170_Intensidad_TI":
        [
        "varchar(3)",
        "Horas de Trabajo Independiente",
        "64",
        "0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas de trabajo independiente por semana. Equivalen al número de horas por semana multiplicado por 16."
        ],
    "F180_Horas_Teoricas_Semanales":
        [
        "varchar(3)",
        "Horas teóricas semanales",
        "4",
        "0,1,2,3,4,5,6,7,8,9,10,11,12",
        "Indique el número de horas teóricas por semana."
        ],
    "F183_Horas_Practicas_Semanales":
        [
        "varchar(3)",
        "Horas Prácticas Semanales",
        "4",
        "0,1,2,3,4,5,6,7,8,9,10,11,12",
        "Indique el número de horas prácticas por semana."
        ],
    "F186_Horas_Teorico_Practicas_Semanales":
        [
        "varchar(3)",
        "Horas Teórico-Prácticas Semanales",
        "4",
        "0,1,2,3,4,5,6,7,8,9,10,11,12",
        "Indique el número de horas teórico-prácticas por semana."
        ],
    "F190_Horas_Teoricas_Semestrales":
        [
        "varchar(3)",
        "Horas teóricas semestrales",
        "64",
        "0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas teóricas por semestre."
        ],
    "F193_Horas_Practicas_Semestrales":
        [
        "varchar(3)",
        "Horas prácticas semestrales",
        "64",
        "0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas prácticas por semestre."
        ],
    "F196_Horas_TeoricoPracticas_Semestrales":
        [
        "varchar(3)",
        "Horas teórico-prácticas semestrales",
        "64",
        "0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas teórico-prácticas por semestre."
        ],
    "F200_Semanas":
        [
        "varchar(3)",
        "Número de semanas",
        "16",
        "16,17,18",
        "Número de semanas por semestre."
        ],
    "F210_Teorico":
        [
        "varchar(2)",
        "Curso teórico",
        "Si",
        "Si,No",
        "Indique si es un curso teórico."
        ],
    "F220_Practico":
        [
        "varchar(2)",
        "Curso práctico",
        "No",
        "Si,No",
        "Indique si es un curso práctico."
        ],
    "F230_Teorico_Practico":
        [
        "varchar(2)",
        "Curso teórico-práctico",
        "No",
        "Si,No",
        "Indique si es un curso teórico-práctico."
        ],
    "F240_Habilitable":
        [
        "varchar(2)",
        "Curso habilitable",
        "Si",
        "Si,No",
        "Indique si es un curso habilitable. No aplica normalmente para cursos prácticos."
        ],
    "F250_Validable":
        [
        "varchar(2)",
        "Curso validable",
        "Si",
        "Si,No",
        "Indique si es un curso validable."
        ],
    "F260_Clasificable":
        [
        "varchar(2)",
        "Curso clasificable",
        "No",
        "Si,No",
        "Indique si es un curso clasificable.  Aplica normalmente para cursos de primer semestre."
        ],

    #LOCALIZACION    
    "F270_Facultad":
        [
        "varchar(50)",
        "Facultad",
        "Facultad de Ciencias Exactas y Naturales",
        "",
        "Facultad"
        ],
    "F280_Instituto":
        [
        "varchar(50)",
        "Instituto",
        "Instituto de Física",
        "Instituto de Física,Instituto de Química,Instituto de Biología,Instituto de Matemáticas,Facultad",
        "Instituto o Dependencia al que pertenece"
        ],
    "F290_Programas_Academicos":
        [
        "varchar(80)",
        "Programas académicos a los que se ofrece",
        "Astronomía, Física",
        "",
        "Programas académicos a los que se ofrece"
        ], 
    "F300_Area_Academica":
        [
        "varchar(50)",
        "Área académica",
        "Astronomía",
        "Astronomía,Biología,Química,Física,Matemáticas,Sociohumanística,Inglés,Ciencias",
        "Indique el área específica en la que se enmarca el curso.  El comité de currículo define un número límitado de áreas en la Facultad."
        ],
    "F310_Campo_Formacion":
        [
        "varchar(50)",
        "Campo de formación",
        "Fundamentación en Ciencias",
        "",
        "Indique el área de formación dentro de la disciplina."
        ],
    "F320_Ciclo":
        [
        "varchar(30)",
        "Ciclo",
        "Fundamentación",
        "Fundamentación,Profesionalización,Profundización",
        "Ciclo de formación de acuerdo al Documento Rector de la Transformación Curricular."
        ],
    "F330_Semestre":
        [
        "varchar(10)",
        "Semestre actual",
        "2014-1",
        "",
        "Indique el último semestre en el que se ofrece el programa.",
        ],
    "F330_Semestre_Plan":
        [
        "varchar(3)",
        "Semestre en el Plan de Formación",
        "1",
        "1,2,3,4,5,6,7,8,9,10,11,12",
        "Indique el semestre en el plan de formación.",
        ],
    "F340_Horario_clase":
        [
        "varchar(20)",
        "Horario de clase",
        "MJ8-10",
        "",
        "Horario u horarios en los que se ofrece el curso en el último semestre.  Para múltiples horarios use ',', e.g. MJ12-14, L16-18"
        ],
    "F350_Requisitos":
        [
        "varchar(100)",
        "Prerrequisitos",
        "(Ninguno)",
        "",
        "Prerrequisitos del curso.  Indique el código de los prerrequisito de acuerdo a la última versión del pensum aprobada."
        ],
    "F360_Correquisitos":
        [
        "varchar(100)",
        "Correquisitos",
        "(Ninguno)",
        "",
        "Correquisitos del curso.  Indique el código de los correquisito de acuerdo a la última versión del pensum aprobada."
        ],
    "F370_Sede":
        [
        "varchar(100)",
        "Sede en el que se ofrece",
        "Ciudad Universitaria Medellín",
        "",
        "Indique las sedes de la Universidad en las que se ofrece el curso."
        ],

    #RESPONSABILIDAD
    "F380_Profesores_Responsables":
        [
        "varchar(100)",
        "Profesores Responsables",
        "Jorge Zuluaga",
        "",
        "Indique el(los) profesor(es) que ofrecieron el curso en el último semestre."
        ],
    "F390_Profesores_Oficinas":
        [
        "varchar(50)",
        "Oficina de Profesores",
        "6-414",
        "",
        "Indique las oficinas de los profesores que ofrecieron el curso en el último semestre."
        ],
    "F400_Horario_atencion":
        [
        "varchar(50)",
        "Horario de los profesores",
        "MJ16-18",
        "",
        "Indique el horario de atención de los profesores que ofrecieron el curso en el último semestre."
        ],
    "F410_Profesores_Elaboran":
        [
        "varchar(100)",
        "Profesores que elaboran",
        "Jorge Zuluaga",
        "",
        "Indique el nombre de los profesores que elaboran esta versión del programa."
        ],
    "F420_Correos_Electronicos":
        [
        "varchar(100)",
        "Correos electronicos de profesores que elaboran",
        "jorge.zuluaga@udea.edu.co",
        "",
        "Lista de correos electrónicos de los profresores que elaboran esta versión del programa."
        ],

    #JUSTIFICACION
    "F430_Descripcion":
        [
        "text",
        "Descripción general del curso",
        "",
        "",
        """Corresponde a una síntesis de los principales elementos que caracterizan la asignatura a la luz de los contenidos,  problemas y preguntas. Cuando se describe se da respuesta a: qué es, cómo es, cómo se comporta, que partes lo constituyen, para qué sirve, qué hace, cómo se define; en este caso, en el contexto del campo de la ciencia y/o disciplina."""
        ],
    "F440_Proposito":
        [
        "text",
        "Propósito del curso es:",
        "",
        "",
        "Normalmente se puede usar para este campo el mismo que la Descripción.  También se puede dejar en blanco."
        ],
    "F450_Justificacion":
        [
        "text",
        "Justificación del curso",
        "",
        "",
        """Debe incluir: (1) La pertinencia de la asignatura en el plan de formación en relación con: (a) El objetivo y los propósitos de formación del respectivo programa de pregrado de ciencias exactas y naturales, (b) La(s) relación(es) de formación entre el ciclo anterior y el posterior, (c) Los saberes y experiencias previas en las asignaturas ya cursadas y las que se desarrollan de modo paralelo.  (2) El aporte al desarrollo de las competencias genéricas y específicas propias de la formación del profesional en el respectivo programa de ciencias exactas y naturales: cognitivas, de comunicación y representación, así como   procedimentales y actitudinales. (3) La actualidad e importancia científica, cultural y social de las problemáticas específicas que serán tratadas  en la asignatura. (4) Las relaciones disciplinares e interdisciplinares con otras asignaturas del plan de formación del respectivo programa. (5) La proyección académica y social de los contenidos de la asignatura en relación con el desarrollo del individuo, de la sociedad y de la profesión."""
        ],
    "F460_Objetivo_General":
        [
        "text",
        "Objetivo General",
        "",
        "",
        "Se refiere a la concreción de las intenciones educativas en la asignatura según el ciclo de formación (fundamentación, profesionalización o profundización); se expresa en términos de las competencias que los estudiantes  deben desarrollar, lo cual implica proyectar los avances de aprendizaje esperados en los ámbitos conceptual, procedimental y actitudinal."
        ],
    "F470_Objetivos_Especificos_Conceptuales":
        [
        "text",
        "Objetivos específicos conceptuales",
        "",
        "",
        "Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros."
        ],
    "F480_Objetivos_Especificos_Procedimentales":
        [
        "text",
        "Objetivos específicos procedimentales",
        "",
        "",
        "Metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones."
        ],
    "F490_Objetivos_Especificos_Actitudinales":
        [
        "text",
        "Objetivos específicos actitudinales",
        "",
        "",
        "Entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros."
        ],
    "F500_Estrategia_Metodologica":
        [
        "text",
        "Estrategia metodológica",
        "",
        "",
"""Se plantea aquí la o las modalidades de trabajo académico desde las cuales se enseñan los contenidos, los procedimientos y las actitudes, mediante: seminarios, talleres, cátedra magistral, aprendizaje basado en problemas (ABP) u otras que se consideren convenientes. Igualmente, se hace mención a la disponibilidad de la asignatura en formato virtual y a la posibilidad de trabajo a partir de ambientes virtuales.

Se trata de la explicitación del camino por el cual el estudiante logra integrar los contenidos y desarrollar sus competencias en relación con los saberes  que son objeto de enseñanza por parte del profesor. Se refiere a los métodos, al cómo propiciar de manera adecuada el aprendizaje de los conceptos, los procedimientos y las actitudes, y por lo tanto, requiere de la reflexión acerca de la construcción de las ciencias; es decir, se fundamenta en la comprensión de la historiay de la epistemología de los respectivos campos científicos. 

El aspecto metodológico hace referencia a las múltiples formas (técnicas y procedimientos) en las cuales, en un lugar y tiempo determinados,  se relacionan los integrantes del grupo. Al respecto, es importante tener en cuenta que este aspecto permite la concreción de los procesos creativos de cada profesor y, por lo tanto, sólo se describen algunas sugerencias, las cuales serán enriquecidas en los casos y contextos particulares.

Es importante tener en cuenta la definición y enunciación de las actividades según el sistema de créditos vigente que rige para todas las asignaturas, para lo cual demanda la elaboración del cronograma que exprese en el:

•Ciclo de fundamentación: actividades presenciales, de acompañamiento directo y/o indirecto y de trabajo independiente.
•Ciclo de profesionalización: actividades presenciales y de trabajo independiente.
•Ciclo de profundización: actividades presenciales y de trabajo independiente con mayor autonomía del estudiante.
"""
        ],
    "F510_Evaluacion":
        [
        "text",
        "Evaluacion General",
        "",
        "",
        """Según la profesora Salinas en conferencia presentada a la facultad de Ciencias Exactas y Naturales en el 2010, la evaluación, en su sentido general, está articulada a una valoración de los procesos formativos consignados en el Documento Rector de la Facultad para todos los programas de pregrado. Desde allí se brinda el marco de referencia sobre el cual descansan las políticas de evaluación. Para el caso de cada uno de las asignaturas, se espera que se expliciten los criterios que orientarán la evaluación en su sentido integral y las pautas desde las cuales se llevará a cabo el seguimiento y promoción de los estudiantes. Ello supone aclarar y mencionar cuáles son los procedimientos para la calificación, la agenda para la presentación de pruebas, las condiciones para la presentación de sustentaciones y exposiciones orales, documentos y trabajos escritos, la distribución de porcentajes relacionados con las notas parciales u otros aspectos que se consideren pertinentes; todo ello en concordancia con el Reglamento Estudiantil.

La evaluación, como proceso inherente e inseparable  de la enseñanza y del aprendizaje, cumple una función formativa, en la búsqueda de un permanente mejoramiento y, una función social, que implica la certificación académica del logro de los objetivos, el desarrollo de competencias, la adquisición de conocimientos y la incorporación de actitudes y valores.

Las estrategias de participación evaluativa son:

•La autoevaluación, implica que el estudiante se examine y reconozca los logros, dificultades y participación en el proceso, entre otros. Para ello, el estudiante se confronta con el programa de la asignatura, y en conversaciones con el profesor y sus compañeros.
•La heteroevaluación, son los criterios del profesor en términos de lo aprendido y los aspectos que falten por mejorar, depurar o realizar.
•La coevaluación, se refiere a la valoración de los aprendizajes en forma colectiva, apoyada en los criterios del área, del profesor y de los compañeros de la asignatura.

La evaluación requiere de la implementación de procesos de autorregulación de los aprendizajes tanto en actividades presenciales, de acompañamiento y en aquellas que no exigen presencialidad.

Es importante describir las formas y los instrumentos de evaluación que el plan de asignatura va a privilegiar. En este aspecto, es necesario definir con claridad las reglas que rigen el proceso en términos, por ejemplo, de asignación de porcentajes, tiempo y fechas entre otros.

Es importante  construir con los estudiantes estrategias para la valoración del curso en términos de sus fortalezas y debilidades.
"""
        ],
    "F515_Evaluacion_Especifica":
        [
        "text",
        "Actividades de Evaluación Específicas",
        "",
        "",
        "Detalle aquí la lista de actividades de evaluación específicas indicando, nombre de la actividad, porcentaje total que cada actividad representa en el total de la evaluación y fechas específicas de las actividades de evaluación."
        ],
    "F520_Actividades_Obligatorias":
        [
        "text",
        "Actividades de asistencia obligatoria",
        "",
        "",
        ""
        ],

    #CONTENIDO RESUMIDO
    "F530_Contenido_Resumido":
        [
        "text",
        "Contenido Resumido",
        "",
        "",
        "Indique el contenido resumido.  Si deja en blanco el título de las unidades indicadas abajo será usado para construir este campo en el formato de salida."
        ],
    "F540_Bibliografia_General":
        [
        "text",
        "Bibliografía General del Curso",
        "",
        "",
        """Tenga en cuenta:
•Que sea suficiente, pertinente, actualizada y en los casos propicios acudir a los textos científicos clásicos.
•Que incluya textos básicos y de referencia para ampliar y profundizar las problemáticas tratadas, para superar el solo estudio con notas de clase.
•Que se apoye con textos impresos y de formato digital. 
•Que se recurra a fuentes primarias, especialmente a textos producidos por los profesores, los artículos de revistas especializadas y a informes de investigación.
•Que se incluyan textos en otro idioma.

Nota: La autonomía intelectual implica que los estudiantes puedan acceder a información actualizada, comprenderla y utilizarla según sus necesidades formativas. Por ello cada docente de la Universidad debe conocer las revistas especializadas de su área y apoyarse en la Biblioteca Central para que les colabore a los estudiantes con el uso adecuado de las bases de datos y las búsquedas avanzadas en internet.
"""
        ],
    
    #UNIDADES
    "F600_Unidad_Titulo":
        [
        "varchar(50)",
        "Título de la Unidad X",
        "",
        "",
        "Título de la Unidad.  Use un título abreviado e informativo."
        ],
    "F601_Unidad_Conceptual":
        [
        "text",
        "Unidad X - Contenidos Conceptuales",
        "",
        "",
        "Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros"
        ],
    "F602_Unidad_Procedimental":
        [
        "text",
        "Unidad X - Contenidos Procedimentales",
        "",
        "",
        "Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones."
        ],
    "F603_Unidad_Actitudinal":
        [
        "text",
        "Unidad X - Contenidos Actitudinales",
        "",
        "",
        "Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros."
        ],
    "F604_Unidad_Bibliografia":
        [
        "text",
        "Unidad X - Bibliografia Específica",
        "",
        "",
        "Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco."
        ],
    "F605_Unidad_Semanas":
        [
        "varchar(3)",
        "Semanas para la Unidad X",
        "3",
        "",
        "Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas."
        ]
    }

###################################################
#UNITS
###################################################
for field in Database.keys():
    if not "Unidad_" in field:continue
    r=re.search("F(\d+)_Unidad_(.+)",field)
    num=int(r.group(1))
    name=r.group(2)
    cont=Database[field]
    for i in xrange(0,10):
        n=i+1
        numu=num+i*10
        fieldu="F%d_Unidad%d_%s"%(numu,n,name)
        contu=cont+[]
        contu[1]=contu[1].replace("X","%d"%n)
        Database[fieldu]=contu
    Database.pop(field,None)

Fields=Database.keys()
Fields.sort()

###################################################
#SQL COMMAND
###################################################
curriculo,connection=loadDatabase()
db=connection.cursor()

sql="""
use Curriculo;
drop table if exists MicroCurriculos,MicroCurriculos_Recycle;
create table MicroCurriculos ("""
for field in Fields:
    fname=re.search("\d+_(.+)",field).group(1)
    tipo=Database[field][0]
    default=""
    if "varchar" in tipo:
        default="not null default ''"
    sql+="\n\t%s %s %s,"%(field,tipo,default)

sql+="""
primary key (F100_Codigo)
);
"""
#print sql
#db.execute(sql)

sql+="""
create table MicroCurriculos_Recycle ("""
for field in Fields:
    fname=re.search("\d+_(.+)",field).group(1)
    tipo=Database[field][0]
    default=""
    if "varchar" in tipo:
        default="not null default ''"
    sql+="\n\t%s %s %s,"%(field,tipo,default)

sql+="""
primary key (F100_Codigo)
);
"""
print sql
db.execute(sql)

###################################################
#CREATING PHP CONFIGURATION FILE
###################################################
content="""
<?
"""
fields_content="$FIELDS=array("
dbase_content="$DBASE=array("

for field in Fields:
    fname=re.search("F\d+_(.+)",field).group(1)
    fields_content+="\"%s\","%field
    tipo=Database[field][0]
    query=Database[field][1]
    default=Database[field][2]
    values=Database[field][3]
    ayuda=Database[field][4]
    dbase_content+="""
'%s'=>array('query'=>'%s','type'=>'%s','default'=>'%s','values'=>'%s','help'=>'%s'),
"""%(field,query,tipo,default,values,ayuda)

fields_content+=");"
fields_content=fields_content.strip(",")
dbase_content+=");"
dbase_content=dbase_content.strip(",")

content+="""
%s
%s
?>
"""%(fields_content,dbase_content)

fl=open("database.php","w")
fl.write(content)
fl.close()
