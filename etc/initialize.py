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

    #IDENTIFICACION
    "100_Codigo":
        [
        "varchar(10)",
         "Codigo Curso",
         "0300000",
         "",
         "El código del curso tiene 6 dígitos"
        ],

    #PROPIEDADES BASICAS
    "110_Nombre_Asignatura":
        [
        "varchar(100)",
        "Nombre Asignatura",
        "Curso",
        "",
        "Entre el nombre completo del curso"
        ],
    "120_Tipo_Curso":
        [
        "varchar(20)",
        "Tipo de Curso",
        "Profesional",
        "Básico,Profesional,Profundización",
        "Tipo de curso deacuerdo a ubicación en el pensum"
        ],
    "130_Asistencia":
        [
        "varchar(50)",
        "Tipo de Asistencia",
        "Obligatoria",
        "Obligatoria,No obligatoria",
        "Indique el tipo de asistencia"
        ],
    "140_Creditos":
        [
        "varchar(3)",
        "Numero de Creditos",
        "4",
        "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15",
        "Indique el número de créditos"
        ],
    "150_Intensidad_HDD":
        [
        "varchar(3)",
        "Horas de Docencia Directa (HDD)",
        "4",
        "",
        "Indique el número de horas de docencia directa por semana"
        ],
    "160_Intensidad_HDA":
        [
        "varchar(3)",
        "Horas de Docencia Asistida",
        "0",
        "",
        "Indique el número de horas de docencia directa por semana"
        ],
    "170_Intensidad_TI":
        [
        "varchar(3)",
        "Horas de Trabajo Independiente",
        "4",
        "",
        "Indique el número de horas de trabajo independiente por semana"
        ],
    "180_Horas_Teoricas_Semanales":
        [
        "varchar(3)",
        "Horas teóricas semanales",
        "4",
        "",
        "Indique el número de horas teóricas por semana"
        ],
    "190_Horas_Teoricas_Semestrales":
        [
        "varchar(3)",
        "Horas teóricas semestrales",
        "64",
        "",
        "Indique el número de horas teóricas por semestre"
        ],
    "200_Semanas":
        [
        "varchar(3)",
        "Número de semanas",
        "16",
        "",
        "Número de semanas por semestre"
        ],
    "210_Teorico":
        [
        "varchar(2)",
        "Curso teórico",
        "Si",
        "Si,No",
        "Indique si es un curso teórico"
        ],
    "220_Practico":
        [
        "varchar(2)",
        "Curso práctico",
        "No",
        "Si,No",
        "Indique si es un curso práctico"
        ],
    "230_Teorico_Practico":
        [
        "varchar(2)",
        "Curso teórico-práctico",
        "No",
        "Si,No",
        "Indique si es un curso práctico"
        ],
    "240_Habilitable":
        [
        "varchar(2)",
        "Curso habilitable",
        "Si",
        "Si,No",
        "Indique si es un curso habilitable"
        ],
    "250_Validable":
        [
        "varchar(2)",
        "Curso validable",
        "Si",
        "Si,No",
        "Indique si es un curso validable"
        ],
    "260_Clasificable":
        [
        "varchar(2)",
        "Curso clasificable",
        "No",
        "Si,No",
        "Indique si es un curso clasificable"
        ],

    #LOCALIZACION    
    "270_Facultad":
        [
        "varchar(50)",
        "Facultad",
        "Facultad de Ciencias Exactas y Naturales",
        "",
        "Facultad"
        ],
    "280_Instituto":
        [
        "varchar(50)",
        "Instituto",
        "Instituto de Física",
        "Instituto de Física,Instituto de Química,Instituto de Biología,Instituto de Matemáticas",
        "Instituto"
        ],
    "290_Programas_Academicos":
        [
        "varchar(80)",
        "Programas académicos a los que se ofrece",
        "Astronomía,Física",
        "",
        ""
        ], 
    "300_Area_Academica":
        [
        "varchar(50)",
        "Área académica",
        "Historia de la Astronomía",
        "",
        "Indique el área específica en la que se enmarca el curso"
        ],
    "310_Campo_Formacion":
        [
        "varchar(50)",
        "Campo de formación",
        "Astronomía",
        "",
        "Indique el área de formación"
        ],
    "320_Ciclo":
        [
        "varchar(30)",
        "Ciclo",
        "Fundamentación",
        "Fundamentación,Profesionalización,Profundización",
        "Ciclo"
        ],
    "330_Semestre":
        [
        "varchar(10)",
        "Semestre actual",
        "2014-1",
        "",
        "Indique el semestre de validez del presente programa",
        ],
    "330_Semestre_Plan":
        [
        "varchar(3)",
        "Semestre en el Plan de Formación",
        "2014-1",
        "",
        "Indique el semestre en el plan de formación",
        ],
    "340_Horario_clase":
        [
        "varchar(20)",
        "Horario de clase",
        "MJ8-10",
        "",
        ""
        ],
    "350_Requisitos":
        [
        "varchar(100)",
        "Prerrequisitos",
        "(Ninguno)",
        "",
        ""
        ],
    "360_Correquisitos":
        [
        "varchar(100)",
        "Correquisitos",
        "(Ninguno)",
        "",
        ""
        ],
    "370_Sede":
        [
        "varchar(20)",
        "Sede en el que se ofrece",
        "Medellín",
        "",
        ""
        ],

    #RESPONSABILIDAD
    "380_Profesores_Responsables":
        [
        "varchar(100)",
        "Profesores Responsables",
        "Jorge Zuluaga",
        "",
        "Indique el(los) profesor(es) que ofrecen el curso en el semestre de validez del programa"
        ],
    "390_Profesores_Oficinas":
        [
        "varchar(50)",
        "Oficina de Profesores",
        "6-414",
        "",
        "Indique las oficinas de los profesores"
        ],
    "400_Horario_atencion":
        [
        "varchar(50)",
        "Horario de los profesores",
        "MJ 16-18",
        "",
        "Indique el horario de atención de los profesores"
        ],
    "410_Profesores_Elaboran":
        [
        "varchar(100)",
        "Profesores que elaboran",
        "Jorge Zuluaga",
        "",
        "Indique el nombre de los profesores que elaboran esta versión del programa"
        ],
    "420_Correos_Electronicos":
        [
        "varchar(100)",
        "Correos electronicos de profesores que elaboran",
        "jorge.zuluaga@udea.edu.co",
        "",
        ""
        ],

    #JUSTIFICACION
    "430_Descripcion":
        [
        "text",
        "Descripción",
        "Explique en que consiste el curso",
        "",
        ""
        ],
    "440_Proposito":
        [
        "text",
        "Propósito",
        "El propósito del curso es",
        "",
        ""
        ],
    "450_Justificacion":
        [
        "text",
        "Justificación",
        "La justificación del curso es",
        "",
        ""
        ],
    "460_Objetivo_General":
        [
        "text",
        "Objetivo General",
        "",
        "",
        ""
        ],
    "470_Objetivos_Especificos_Conceptuales":
        [
        "text",
        "Objetivos específicos conceptuales",
        "",
        "",
        ""
        ],
    "480_Objetivos_Especificos_Procedimentales":
        [
        "text",
        "Objetivos específicos procedimentales",
        "",
        "",
        ""
        ],
    "490_Objetivos_Especificos_Actitudinales":
        [
        "text",
        "Objetivos específicos actitudinales",
        "",
        "",
        ""
        ],
    "500_Estrategia_Metodologica":
        [
        "text",
        "Estrategia metodológica",
        "",
        "",
        ""
        ],
    "510_Evaluacion":
        [
        "text",
        "Evaluacion",
        "",
        "",
        ""
        ],
    "520_Actividades_Obligatorias":
        [
        "text",
        "Actividades de asistencia obligatoria",
        "",
        "",
        ""
        ],

    #CONTENIDO RESUMIDO
    "530_Contenido_Resumido":
        [
        "text",
        "Contenido Resumido",
        "",
        "",
        ""
        ],
    
    #UNIDADES
    "601_Unidad_Conceptual":
        [
        "text",
        "Unidad X - Contenidos Conceptuales",
        "",
        "",
        ""
        ],
    "602_Unidad_Procedimental":
        [
        "text",
        "Unidad X - Contenidos Procedimentales",
        "",
        "",
        ""
        ],
    "603_Unidad_Actitudinal":
        [
        "text",
        "Unidad X - Contenidos Actitudinales",
        "",
        "",
        ""
        ],
    "604_Unidad_Bibliografia":
        [
        "text",
        "Unidad X - Bibliografia",
        "",
        "",
        "Si es la misma, repita este campo en todas las unidades."
        ],
    "605_Unidad_Semanas":
        [
        "varchar(3)",
        "Semanas para la Unidad",
        "3",
        "",
        ""
        ]
    }

###################################################
#UNITS
###################################################
for field in Database.keys():
    if not "Unidad_" in field:continue
    r=re.search("(\d+)_Unidad_(.+)",field)
    num=int(r.group(1))
    name=r.group(2)
    cont=Database[field]
    for i in xrange(0,10):
        n=i+1
        numu=num+i*10
        fieldu="%d_Unidad%d_%s"%(numu,n,name)
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
primary key (100_Codigo)
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
primary key (100_Codigo)
) row_format=dynamic;
"""
#print sql
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
    fname=re.search("\d+_(.+)",field).group(1)
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
