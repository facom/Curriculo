#-*-coding:utf-8-*-
from initialize import *
import numpy as np
from os import system

###################################################
#LOAD PUBLIC COURSES
###################################################
years=np.arange(2002,2015+1,1)
sems=[1,2]
"""
courses=["0302100","0302164","0302251","0302252","0302261","0302262","0302265","0302275","0302301","0302308","0302310","0302313","0302314","0302318","0302330","0302331","0302332","0302335","0302340","0302341","0302360","0302361","0302375","0302408","0302411","0302412","0302414","0302420","0302422","0302425","0302435","0302463","0302470","0302475","0302480","0302490","0302491","0302495","0302500","0302501"]
"""
courses=["0302100","0302164","0302251"]

###################################################
#SQL COMMAND
###################################################
curriculo,connection=loadDatabase()
db=connection.cursor()
fields=curriculo["MicroCurriculos"]["fields"]

sql="truncate table MicroCurriculos_Publicos;"
db.execute(sql);
system("rm -r ../public/*")
connection.commit()

for year in years:
    print "Year: ",year
    for sem in sems:
        semid=str(year)+"-"+str(sem)
        print "\tSemester: ",sem,semid
        for codigo in courses:
            print "\t\tCourse: ",codigo
            version=int(curriculo["MicroCurriculos"]["rows"]["0302150"]["F025_AUTH_Version"])
            codigoid="%s-v%d-%s"%(codigo,version,semid)
            print "\t\t\tCodigo curso publicado:",codigoid

            try:
                curriculo["MicroCurriculos_Publicos"]["rows"][codigoid]["F000_AUTO_Codigoid"]
                print "\t\t\tCurso ya publicado."
            except:
                print "\t\t\tCurso no publicado todavia."
                curriculo["MicroCurriculos_Publicos"]["rows"][codigoid]=dict()
                curriculo["MicroCurriculos_Publicos"]["rows"][codigoid]["F000_AUTO_Codigoid"]=codigoid
                sql="insert into MicroCurriculos_Publicos (F000_AUTO_Codigoid) values (\"%s\") on duplicate key update F000_AUTO_Codigoid=\"%s\""%(codigoid,codigoid);  
                db.execute(sql);
                connection.commit()
                system("cp -r ../data/%s ../public/%s"%(codigo,codigoid))

            for field in fields:
                value=curriculo["MicroCurriculos"]["rows"][codigo][field]
                curriculo["MicroCurriculos_Publicos"]["rows"][codigoid][field]=value
            curriculo["MicroCurriculos_Publicos"]["rows"][codigoid]["F330_Semestre"]=semid

system("chown -R www-data.www-data ../public")
updateDatabase(curriculo,connection)

for year in years:
    print "Year: ",year
    for sem in sems:
        semid=str(year)+"-"+str(sem)
        print "\tSemester: ",sem,semid
        for codigo in courses:
            print "\t\tCourse: ",codigo
            codigoid="%s-v%d-%s"%(codigo,version,semid)
            system("cd ..;php export.php ver_curso=%s source=public mode=Todos > /dev/null"%(codigoid))
