#-*-coding:utf-8-*-
from initialize import *

###################################################
#SQL COMMAND
###################################################
curriculo,connection=loadDatabase()
db=connection.cursor()

#drop table if exists MicroCurriculos_Publicos;
sql="""
use Curriculo;
create table MicroCurriculos_Publicos ("""
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
