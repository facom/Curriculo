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
5:Ejemplo
"""
Database={
    #META
    "F010_AUTO_Fecha_Actualizacion":
        [
        "varchar(30)",
        "Fecha de actualización",
        "",
        "",
        "Fecha en la que se realiza la actualización. Este campo es automático",
        "Este campo es automático."
        ],
    "F015_AUTO_Usuario_Actualizacion":
        [
        "varchar(30)",
        "Usuario que realiza la actualización",
        "",
        "",
        "Este es el usuario administrativo que esta realizando esta actualización. Este campo es automático.",
        "Este campo es automático."
        ],
    "F020_AUTH_Autorizacion_Vicedecano":
        [
        "varchar(30)",
        "Autorización Vicedecano",
        "No",
        "Si,No",
        "Una vez el vicedecano autoriza el curso no puede ser editado por ningún otro usuario autorizado.  El curso solo puede volverse a editar cuando el vicedecano cambie este campo a No.",
        "Si"
        ],
    "F025_AUTH_Version":
        [
        "varchar(3)",
        "Última versión del curso",
        "",
        "",
        "Última versión del curso.",
        "1"
        ],
    "F030_AUTH_Acta_Numero":
        [
        "varchar(4)",
        "Número de Acta del Consejo de Facultad",
        "",
        "",
        "Número de acta en el que el curso fue aprobado.  Si el número de acta es 00 el curso nunca ha sido aprobado. Si es distinto pero el Vicedecano no lo ha aprobado esta acta corresponde a la versión anterior del curso.",
        "123"
        ],
    "F040_AUTH_Acta_Fecha":
        [
        "varchar(30)",
        "Fecha del Acta del Consejo de Facultad",
        "",
        "",
        "Fecha del acta del Consejo de Facultad. Si la fecha es MM/DD/CCYY el curso nunca ha sido aprobado.  Si es distinto pero el Vicedecano no lo ha aprobado esta acta corresponde a la versión anterior del curso.",
        "11/01/2014"
        ],
    "F050_Nombre_Actualiza":
        [
        "varchar(30)",
        "Nombre de quien modifica esta última versión",
        "",
        "",
        "Indique el nombre de quien esta modificando esta última versión del curso.",
        "Jorge I. Zuluaga"
        ],
    "F060_AUTH_Publica_Curso":
        [
        "varchar(3)",
        "Publica curso",
        "--",
        "--,Si,No",
        "Si coloca *Si* el curso será visible por usuarios no autorizados.",
        "No"
        ],

    #IDENTIFICACION
    "F100_Codigo":
        [
            "varchar(7)",
        "Codigo Curso",
        "0300000",
        "",
        "El código del curso tiene 6 dígitos: FFPPNNN, donde FF es la Facultad (03, FCEN), PP es el Programa (11, Astronomía), NNN es el número del curso",
        "0311150"
        ],

    #PROPIEDADES BASICAS
    "F110_Nombre_Asignatura":
        [
        "varchar(100)",
        "Nombre de la Asignatura",
        "Nombre Asignatura",
        "",
        "El nombre completo del curso debe coincidir con el que esta en el sistema Mares",
        "Introducción a la Informática"
        ],
    "F120_Tipo_Curso":
        [
        "varchar(20)",
        "Tipo de Curso",
        "--",
        "--,Básico,Profesional,Profundización",
        "Tipo de curso deacuerdo a su ubicación en el pensum.",
        "Básico"
        ],
    "F130_Asistencia":
        [
        "varchar(50)",
        "Tipo de Asistencia",
        "--",
        "--,Obligatoria,No obligatoria",
        "Indique el tipo de asistencia. El Comité de Curriculo define normalmente qué tipo de cursos son de asistencia obligatoria.",
        "Obligatoria"
        ],
    "F140_Creditos":
        [
        "varchar(3)",
        "Numero de Creditos",
        "--",
        "--,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15",
        "Indique el número de créditos.  De acuerdo al 1295 cada crédito corresponde a 3 horas de trabajo en el curso.",
        "4"
        ],
    "F150_Intensidad_HDD":
        [
        "varchar(3)",
        "Horas de Docencia Directa (HDD)",
        "--",
        "--,0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas de docencia directa por semestre. Las horas de docencia directa son aquellas que realiza el profesor en actividades magistrales o presentación de contenidos. Normalmente equivalen al número de horas teóricas por semana multiplicado por 16. 0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176",
        "64"
        ],
    "F160_Intensidad_HDA":
        [
        "varchar(3)",
        "Horas de Docencia Asistida (HDA)",
        "--",
        "--,0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas de docencia asistida por semana. Las horas de docencias asistida son aquellas que se relacionan con actividades realizadas directamente por el estudiante pero con el acompañamiento presencial del profesor.  Este tipo de modalidad se utiliza especialmente en cursos prácticos o teórico prácticos.  Normalmente equivalen al número de horas prácticas o teórico-prácticas por semana (en los que las prácticas se hacen asistidas por el profesor) multiplicado por 16.  0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176",
        "32"
        ],
    "F170_Intensidad_TI":
        [
        "varchar(3)",
        "Horas de Trabajo Independiente (TI)",
        "--",
        "--,0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas de trabajo independiente por semana. Las horas de trabajo independientes son las que realiza el estudiante por fuera de clase.  El valor por semana se calcula multiplicando por 3 el número de crédios y restando al resultado el número de horas en las que el estudiante esta acompañado por el profesor (teóricas, prácticas o teórico prácticas.  Ejemplo: si un curso tiene 4 créditos (12 horas por semana totales) y 4 horas son en actividades en clase (acompañadas por el docente) entonces habrán 8 horas de trabajo independiente.  El número a reportar aquí debe ser el número de horas por semestre que es igual a lo que se obtuvo multiplicado por 16. 0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176",
        "32"
        ],
    "F180_Horas_Teoricas_Semanales":
        [
        "varchar(3)",
        "Horas teóricas semanales",
        "--",
        "--,0,1,2,3,4,5,6,7,8,9,10,11,12",
        "Indique el número de horas teóricas por semana.",
        "4"
        ],
    "F183_Horas_Practicas_Semanales":
        [
        "varchar(3)",
        "Horas Prácticas Semanales",
        "--",
        "--,0,1,2,3,4,5,6,7,8,9,10,11,12",
        "Indique el número de horas prácticas por semana.",
        "2"
        ],
    "F186_Horas_Teorico_Practicas_Semanales":
        [
        "varchar(3)",
        "Horas Teórico-Prácticas Semanales",
        "--",
        "--,0,1,2,3,4,5,6,7,8,9,10,11,12",
        "Indique el número de horas teórico-prácticas por semana.",
        "0"
        ],
    "F190_Horas_Teoricas_Semestrales":
        [
        "varchar(3)",
        "Horas teóricas semestrales",
        "--",
        "--,0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas teóricas por semestre.  0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176",
        "64"
        ],
    "F193_Horas_Practicas_Semestrales":
        [
        "varchar(3)",
        "Horas prácticas semestrales",
        "--",
        "--,0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas prácticas por semestre. 0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176",
        "0"
        ],
    "F196_Horas_TeoricoPracticas_Semestrales":
        [
        "varchar(3)",
        "Horas teórico-prácticas semestrales",
        "--",
        "--,0,16,32,48,64,80,96,112,128,144,160,176",
        "Indique el número de horas teórico-prácticas por semestre. 0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176",
        "0"
        ],
    "F200_Semanas":
        [
        "varchar(3)",
        "Número de semanas",
        "--",
        "--,16",
        "Número de semanas por semestre.",
        "16"
        ],
    "F210_Teorico":
        [
        "varchar(2)",
        "Curso teórico",
        "--",
        "--,Si,No",
        "Indique si es un curso teórico.",
        "Si"
        ],
    "F220_Practico":
        [
        "varchar(2)",
        "Curso práctico",
        "--",
        "--,Si,No",
        "Indique si es un curso práctico.",
        "No"
        ],
    "F230_Teorico_Practico":
        [
        "varchar(2)",
        "Curso teórico-práctico",
        "--",
        "--,Si,No",
        "Indique si es un curso teórico-práctico.",
        "No"
        ],
    "F240_Habilitable":
        [
        "varchar(2)",
        "Curso habilitable",
        "--",
        "--,Si,No",
        "Indique si es un curso habilitable. No aplica normalmente para cursos prácticos.",
        "No"
        ],
    "F250_Validable":
        [
        "varchar(2)",
        "Curso validable",
        "--",
        "--,Si,No",
        "Indique si es un curso validable.",
        "No"
        ],
    "F260_Clasificable":
        [
        "varchar(2)",
        "Curso clasificable",
        "--",
        "--,Si,No",
        "Indique si es un curso clasificable.  Aplica normalmente para cursos de primer semestre.",
        "No"
        ],

    #LOCALIZACION    
    "F270_Facultad":
        [
        "varchar(50)",
        "Facultad",
        "Facultad de Ciencias Exactas y Naturales",
        "",
        "Facultad",
        "Facultad de Ciencias Exactas y Naturales"
        ],
    "F280_Instituto":
        [
        "varchar(50)",
        "Instituto",
        "--",
        "--,Instituto de Física,Instituto de Química,Instituto de Biología,Instituto de Matemáticas,Facultad",
        "Instituto o Dependencia al que pertenece",
        "Instituto de Física"        
        ],
    "F290_Programas_Academicos":
        [
        "varchar(80)",
        "Programas académicos a los que se ofrece",
        "",
        "",
        "Programas académicos a los que se ofrece",
        "Astronomía, Física"
        ], 
    "F300_Area_Academica":
        [
        "varchar(50)",
        "Área académica",
        "--",
        "--,Astronomía,Biología,Química,Física,Matemáticas,Sociohumanística,Inglés,Ciencias,Computación",
        "Indique el área específica en la que se enmarca el curso.  El comité de currículo define un número límitado de áreas en la Facultad.",
        "Astronomía"
        ],
    "F310_Campo_Formacion":
        [
        "varchar(50)",
        "Campo de formación",
        "",
        "--Física--,Física Básica,Física Experimental,Física Teórica,Física Computacional,Física Matemática,Investigación,--Astronomía--,Astronomía Básica,Astronomía Práctica,Astrofísica y Comología,Didáctica",
        "Indique el área de formación dentro de la disciplina. Este campo va en el formato de la vicerrectoría de Docencia.",
        "Astronomía Práctica"
        ],
    "F320_Ciclo":
        [
        "varchar(30)",
        "Ciclo",
        "--",
        "--,Fundamentación,Profesionalización,Profundización",
        "Ciclo de formación de acuerdo al Documento Rector de la Transformación Curricular.",
        "Fundamentación"
        ],
    "F330_Semestre":
        [
        "varchar(10)",
        "Semestre actual",
        "",
        "",
        "Indique el último semestre en el que se ofrece el programa.",
        "2014-1"
        ],
    "F330_Semestre_Plan":
        [
        "varchar(50)",
        "Semestre en el Plan de Formación",
        "",
        "",
        #"--,1,2,3,4,5,6,7,8,9,10",
        "Indique el semestre en el plan de formación.  Si el curso se ofrece en varios programas y el semestre en cada uno de ellos es distinto use el nombre del programa en paréntesis para distinguirlo (ver ejemplo).  Si se trata de una electiva use 10",
        "1 (Física), 2 (Astronomía)"
        ],
    "F340_Horario_clase":
        [
        "varchar(20)",
        "Horario de clase",
        "",
        "",
        "Horario u horarios en los que se ofrece el curso en el último semestre.  Para múltiples horarios use ',', e.g. MJ12-14, L16-18",
        "L14-16, MJ8-10"
        ],
    "F350_Requisitos":
        [
        "varchar(100)",
        "Prerrequisitos",
        "",
        "",
        "Prerrequisitos del curso.  Indique el código de los prerrequisito de acuerdo a la última versión del pensum aprobada. Si no tiene prerrequisito use *(Ninguno)*. Si el curso tiene prerrequisitos específicos en otro programa ponga el nombre del programa entre paréntesis antes del prerrequisito (vea el ejemplo)",
        "0311101, 0311305, (Física) 0302133"
        ],
    "F360_Correquisitos":
        [
        "varchar(100)",
        "Correquisitos",
        "",
        "",
        "Correquisitos del curso.  Indique el código de los correquisito de acuerdo a la última versión del pensum aprobada. Si no tiene correquisito use *(Ninguno)*",
        "0311101, 0311305"
        ],
    "F370_Sede":
        [
        "varchar(100)",
        "Sede en el que se ofrece",
        "Ciudad Universitaria Medellín",
        "",
        "Indique las sedes de la Universidad en las que se ofrece el curso.",
        "Ciudad Universitaria Medellín y regiones donde se ofrece el programa"
        ],

    #RESPONSABILIDAD
    "F380_Profesores_Responsables":
        [
        "varchar(100)",
        "Profesores Responsables",
        "",
        "",
        "Indique el(los) profesor(es) que ofrecieron el curso en el último semestre.",
        "Jorge I. Zuluaga, Nelsón Vanegas"
        ],
    "F390_Profesores_Oficinas":
        [
        "varchar(50)",
        "Oficina de Profesores",
        "",
        "",
        "Indique las oficinas de los profesores que ofrecieron el curso en el último semestre.",
        "6-414, 6-212"
        ],
    "F400_Horario_atencion":
        [
        "varchar(50)",
        "Horario de atención de los profesores",
        "",
        "",
        "Indique el horario de atención de los profesores que ofrecieron el curso en el presente semestre.",
        "Jorge Zuluaga: MJ16-18, Nelsón Vanegas: MJ8-10"
        ],
    "F410_Profesores_Elaboran":
        [
        "varchar(100)",
        "Profesores que elaboran este plan de asignatura",
        "",
        "",
        "Indique el nombre de los profesores que contribuyeron con la elaboración de esta versión del plan de asignatura.",
        "Pablo Cuartas, Ignacio Ferrín"
        ],
    "F420_Correos_Electronicos":
        [
        "varchar(100)",
        "Correos electronicos de profesores que elaboran",
        "",
        "",
        "Lista de correos electrónicos de los profresores que elaboran esta versión del programa.",
        "pablo.cuartas@udea.edu.co, ignacio.ferrin@udea.edu.co"
        ],

    #JUSTIFICACION
    "F430_Descripcion":
        [
        "text",
        "Descripción general del curso",
        "",
        "",
        """Corresponde a una síntesis de los principales elementos que caracterizan la asignatura a la luz de los contenidos,  problemas y preguntas. Cuando se describe se da respuesta a: qué es, cómo es, cómo se comporta, que partes lo constituyen, para qué sirve, qué hace, cómo se define; en este caso, en el contexto del campo de la ciencia y/o disciplina.""",
        """Este curso presenta algunas temáticas básicas de la informática requeridas específicamente para el trabajo científico o técnico.  El curso comienza con la descripción del funcionamiento del computador, las redes de computadores y el uso de la Internet con propósitos académicos y científicos (Internet Científica).  Se presenta una introducción general a por lo menos 2 lenguajes de programación (Python y C o C++) partiendo inicialmente desde el desarrollo de competencias algorítmicas y finalizando con la exploración de la sintaxis específica de cada lenguaje.  El curso también aborda la temática de la representación gráfica de los datos introduciendo para ello algunas herramientas de acceso libre (Matplotlib y Gnuplot).  Finalmente se introduce al estudiante en el uso del LaTeX como herramienta para la presentación de resultados científicos en la forma de reportes y artículos técnicos.  En síntesis, el curso hace un recorrido por los problemas y las herramientas utilizadas para la gestión completa de los datos científicos, incluyendo, su generación, procesamiento (programación), representación gráfica y presentación final en la forma, por ejemplos, de reportes y artículos."""
        ],
    "F440_Proposito":
        [
        "text",
        "Propósito del curso es:",
        "",
        "DEPRECATED",
        "Normalmente se puede usar para este campo el mismo que la Descripción.  También se puede dejar en blanco.",
        """Este curso presenta algunas temáticas básicas de la informática requeridas específicamente para el trabajo científico o técnico.  El curso comienza con la descripción del funcionamiento del computador, las redes de computadores y el uso de la Internet con propósitos académicos y científicos (Internet Científica).  Se presenta una introducción general a por lo menos 2 lenguajes de programación (Python y C o C++) partiendo inicialmente desde el desarrollo de competencias algorítmicas y finalizando con la exploración de la sintaxis específica de cada lenguaje.  El curso también aborda la temática de la representación gráfica de los datos introduciendo para ello algunas herramientas de acceso libre (Matplotlib y Gnuplot).  Finalmente se introduce al estudiante en el uso del LaTeX como herramienta para la presentación de resultados científicos en la forma de reportes y artículos técnicos.  En síntesis, el curso hace un recorrido por los problemas y las herramientas utilizadas para la gestión completa de los datos científicos, incluyendo, su generación, procesamiento (programación), representación gráfica y presentación final en la forma, por ejemplos, de reportes y artículos."""
        ],
    "F450_Justificacion":
        [
        "text",
        "Justificación del curso",
        "",
        "",
        """Debe incluir: (1) La pertinencia de la asignatura en el plan de formación en relación con: (a) El objetivo y los propósitos de formación del respectivo programa de pregrado de ciencias exactas y naturales, (b) La(s) relación(es) de formación entre el ciclo anterior y el posterior, (c) Los saberes y experiencias previas en las asignaturas ya cursadas y las que se desarrollan de modo paralelo.  (2) El aporte al desarrollo de las competencias genéricas y específicas propias de la formación del profesional en el respectivo programa de ciencias exactas y naturales: cognitivas, de comunicación y representación, así como   procedimentales y actitudinales. (3) La actualidad e importancia científica, cultural y social de las problemáticas específicas que serán tratadas  en la asignatura. (4) Las relaciones disciplinares e interdisciplinares con otras asignaturas del plan de formación del respectivo programa. (5) La proyección académica y social de los contenidos de la asignatura en relación con el desarrollo del individuo, de la sociedad y de la profesión.""",
        """En el quehacer académico y científico los datos juegan un papel fundamental.  Su obtención, manipulación, almacenamiento, representación gráfica y presentación en forma de reportes, artículos, entre otros, constituyen tareas muy comunes de la actividad científica.  Para esta labor existen y se desarrollan constantemente herramientas computacionales que facilitan estas operaciones y que el científico en formación debe conocer y manipular adecuadamente. Entre estas herramientas se pueden enumerar los lenguajes de programación, las herramientas para la edición y manipulación de archivos o los paquetes y bibliotecas numéricas orientadas a la programación científica.

Manejar adecuadamente herramientas computacionales le permite al científico solucionar problemas mediante procesos automatizados, economizando tiempo e incrementando su capacidad para abordar problemas muy complejos. Las competencias informáticas le permiten además verificar modelos teóricos a través por ejemplo de simulaciones.  Los computadores, además, son herramientas fundamentales para la gestión de la información científica. El estudiante en formación debe conocer las posibilidades que le ofrece el computador, al igual que sus limitaciones.

La programación, en particular, es fundamental para el desarrollo del pensamiento analítico y algorítmico, habilidades imprescindibles para desarrollar otras competencias científicas tanto en el ámbito de la computación misma como en otros ámbitos específicos de la disciplina.

Muchas de las asignaturas del plan de estudios en los programas en los que se ofrece este curso (física y astronomía), requieren competencias importantes en el uso y programación de computadores.  Este es el caso por ejemplo de los cursos de naturaleza práctica tales como la física experimental (3 cursos) y la astronomía observacional (3 cursos).  En un mundo con problemas cada vez más complejos, incluso los cursos teóricos se están valiendo de la computación como herramienta didáctica y de investigación.  Así pues, la formación de los estudiantes en competencias computacionales desde el primer nivel de los programas en los que se ofrece, es condición fundamental para los retos académicos que enfrentarán en el resto de sus carreras."""
        ],
    "F460_Objetivo_General":
        [
        "text",
        "Objetivo General",
        "",
        "",
        """Se refiere a la concreción de las intenciones educativas en la asignatura según el ciclo de formación (fundamentación, profesionalización o profundización); se expresa en términos de las competencias que los estudiantes  deben desarrollar, lo cual implica proyectar los avances de aprendizaje esperados en los ámbitos conceptual, procedimental y actitudinal.
""",
        """Adquirir competencias básicas en informática y programación de computadores, incluyendo el manejo de herramientas computacionales para la manipulación, procesamiento y representación de datos científicos y para su presentación en la forma de reportes, artículos entre otros."""
        ],
    "F470_Objetivos_Especificos_Conceptuales":
        [
        "text",
        "Objetivos específicos conceptuales",
        "",
        "",
        """Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros.

Verbos generales: Analizar Formular Calcular Fundamentar Categorizar
Generar Comparar Identificar Compilar Inferir Concretar Mostrar
Contrastar Orientar Crear Oponer Definir Reconstruir Demostrar Relatar
Desarrollar Replicar Describir Reproducir Diagnosticar Revelar
Discriminar Planear Diseñar Presentar Efectuar Probar Enumerar
Producir Establecer Proponer Evaluar Situar Explicar Tasar Examinar
Trazar Exponer Valuar.

Verbos específicos: Advertir Enunciar Analizar Enumerar Basar
Especificar Calcular Estimar Calificar Examinar Categorizar Explicar
Comparar Fraccionar Componer Identificar Conceptuar Indicar Considerar
Interpretar Contrastar Justificar Deducir Mencionar Definir Mostrar
Demostrar Operacionalizar Detallar Organizar Determinar Registrar
Designar Relacionar Descomponer Resumir Descubrir Seleccionar
Discriminar Separar Distinguir Sintetizar Establecer Sugerir
""",
        """Identificar y enumerar las componentes de hardware y software de un computador.
Describir las funciones de las componente del hardware de un computador.
Enumerar los más importantes sistemas operativos utilizados por computadores de escritorio.
Definir lo que es un protocolo de comunicación y enumerar algunos protocolos de comunicación básicos (IP, http, etc.)
Definir lo que es un lenguaje de programación interpretado y uno compilado.
Enumerar las diferencias, pros y contras de los lenguajes de programación interpretados y compilados.
"""
        ],
    "F480_Objetivos_Especificos_Procedimentales":
        [
        "text",
        "Objetivos específicos procedimentales",
        "",
        "",
        """Metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.

Verbos específicos: Advertir Enunciar Analizar Enumerar Basar
Especificar Calcular Estimar Calificar Examinar Categorizar Explicar
Comparar Fraccionar Componer Identificar Conceptuar Indicar Considerar
Interpretar Contrastar Justificar Deducir Mencionar Definir Mostrar
Demostrar Operacionalizar Detallar Organizar Determinar Registrar
Designar Relacionar Descomponer Resumir Descubrir Seleccionar
Discriminar Separar Distinguir Sintetizar Establecer Sugerir
""",
        """Reconocer la diferencia en prestaciones de distintas configuraciones de hardware y software en un computador.
Utilizar buscadores de Internet usando opciones no triviales.
Buscar literatura especializada usando herramientas de búsqueda propias de su disciplina (Google Scholar, ADS, inSpires, arXiv).
Instalar el sistema operativo Linux en un computador de escritorio.
Manipular archivos y directorios utilizando la línea de comandos de Linux.
Editar archivos de texto plano utilizando editores simples en el sistema operativo Linux."""
        ],
    "F490_Objetivos_Especificos_Actitudinales":
        [
        "text",
        "Objetivos específicos actitudinales",
        "",
        "",
        """Entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.

Verbos específicos: Advertir Enunciar Analizar Enumerar Basar
Especificar Calcular Estimar Calificar Examinar Categorizar Explicar
Comparar Fraccionar Componer Identificar Conceptuar Indicar Considerar
Interpretar Contrastar Justificar Deducir Mencionar Definir Mostrar
Demostrar Operacionalizar Detallar Organizar Determinar Registrar
Designar Relacionar Descomponer Resumir Descubrir Seleccionar
Discriminar Separar Distinguir Sintetizar Establecer Sugerir
""",
        """Reconocer la computación como un área fundamental en la formación del científico y demostrar compromiso para conocer y asimilar nuevas herramientas.
Describir la importancia de la representación gráfica de los datos para el trabajo científico.
Valorar el trabajo realizado por desarrolladores de software e ingenieros en la creación de herramientas que facilitan el trabajo científico."""
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
""",
        """Este curso es de naturaleza teórico-práctica.  Por la misma razón se requiere la participación activa de los estudiantes en todas las actividades de clase.  

Para conseguir este objetivo se sugiere utilizar las siguientes estrategias metodológicas:

Para la presentación de los contenidos teóricos se recomienda restringirse a exposiciones cortas que involucren ejercicios rápidos de parte de los estudiantes.  Los ejercicios pueden incluir la solución a preguntas abiertas, la búsqueda de material en Internet o la solución a pequeños problemas.

Para las sesiones de carácter práctico con acompañamiento directo del Profesor se sugiere involucrar siempre a los estudiantes en el proceso.  Para ello se puede hacer pasar a un estudiante al computador del profesor, resolver partes del problema práctico y realizar una revisión permanente del proceso de solución.

La evaluación de carácter formativo es fundamental en el curso.  Para ello es importante promover la participación de los estudiantes en la solución de preguntas o la realización de encuestas sencillas sobre el avance del proceso en clase."""
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
""",
        """Dada la naturaleza e intensidad del curso se sugieren los siguientes mecanismos evaluativos:

Evaluación formativa permanente durante las actividades de docencia directa.  Esta evaluación se puede realizar con ejercicios cortos durante las presentaciones teóricas o mediante controles de avance durante las actividades prácticas orientadas por el profesor.

Al menos una evaluación sumativa semanal.  De nuevo, por la naturaleza del curso, es necesario garantizar la puesta en práctica de las competencias enseñadas dentro y fuera del aula de clase.  Para ello se sugiere realizar una evaluación corta semanal que evidencie claramente el desarrollo de las competencias.  Para su corrección se sugiere usar las modalidades de auto o coevaluación que contribuyan además a hacer participe a los mismos estudiantes del proceso evaluativo.

Adicionalmente y por lo menos en dos oportunidades durante el desarrollo del curso, se sugiere realizar evaluaciones sumativas más complejas.  Estas evaluaciones tendrán como propósito evaluar el desarrollo de las competencias en el mediano plazo."""
        ],
    "F515_Evaluacion_Especifica":
        [
        "text",
        "Actividades de Evaluación Específicas",
        "",
        "",
        "Detalle aquí la lista de actividades de evaluación específicas indicando, nombre de la actividad, porcentaje total que cada actividad representa en el total de la evaluación y fechas específicas de las actividades de evaluación.",
        """Evaluación semanal, 70%, 1 vez cada semana
Evaluación de Competencia Final, 30%, Semana de evaluaciones finales"""
        ],
    "F520_Actividades_Obligatorias":
        [
        "text",
        "Actividades de asistencia obligatoria",
        "",
        "",
        "",
        """Dada la naturaleza permanente de la evaluación formativa y sumativa en este curso además de su carácter práctico, todas las actividades del curso son de asistencia obligatoria."""
        ],

    #CONTENIDO RESUMIDO
    "F530_Contenido_Resumido":
        [
        "text",
        "Contenido Resumido",
        "",
        "DEPRECATED",
        "Indique el contenido resumido.  Si deja en blanco el título de las unidades indicadas abajo será usado para construir este campo en el formato de salida.",
        ""
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
""",
        """Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
"""
        ],
    
    #UNIDADES
    "F600_Unidad_Titulo":
        [
        "varchar(50)",
        "Título de la Unidad X",
        "",
        "",
        "Título de la Unidad.  Use un título abreviado e informativo.",
        "El Computador"
        ],
    "F601_Unidad_Conceptual":
        [
        "text",
        "Unidad X - Contenidos Conceptuales",
        "",
        "",
        "Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros",
        """Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware"""
        ],
    "F602_Unidad_Procedimental":
        [
        "text",
        "Unidad X - Contenidos Procedimentales",
        "",
        "",
        "Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.",
        """Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux."""
        ],
    "F603_Unidad_Actitudinal":
        [
        "text",
        "Unidad X - Contenidos Actitudinales",
        "",
        "",
        "Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.",
        """Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos."""
        ],
    "F604_Unidad_Bibliografia":
        [
        "text",
        "Unidad X - Bibliografia Específica",
        "",
        "",
        "Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.",
        """Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
"""
        ],
    "F605_Unidad_Semanas":
        [
        "varchar(3)",
        "Semanas para la Unidad X",
        "",
        "",
        "Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.",
        "3"
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

if __name__=="__main__":
    try:
        option=argv[1]
    except:
        option=""

    ###################################################
    #CREATING PHP CONFIGURATION FILE
    ###################################################
    content="""
    <?php
    """
    fields_content="$FIELDS=array("
    dbase_content="$DBASE=array("

    for field in Fields:
        print field
        fname=re.search("F\d+_(.+)",field).group(1)
        fields_content+="\"%s\","%field
        tipo=Database[field][0]
        query=Database[field][1]
        default=Database[field][2]
        values=Database[field][3]
        ayuda=Database[field][4]
        ejemplo=Database[field][5]
        dbase_content+="""
    '%s'=>array('query'=>'%s','type'=>'%s','default'=>'%s','values'=>'%s','help'=>'%s','ejemplo'=>'%s'),
    """%(field,query,tipo,default,values,ayuda,ejemplo)

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

    if option=="nodb":
        print "No database"
        exit(0)

    ans=raw_input("De verdad quiere limpiar la base de datos? (s/n)...")
    if ans=="n":exit(0)

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

