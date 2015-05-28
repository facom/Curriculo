#-*-coding:utf-8-*-
from initialize import *

###################################################
#SQL COMMAND
###################################################
curriculo,connection=loadDatabase()
db=connection.cursor()

sql="""
use Curriculo;
drop table if exists MicroCurriculos_Publicos;
create table MicroCurriculos_Publicos (
\tF000_AUTO_Codigoid varchar(100) not null default '',"""
for field in Fields:
    fname=re.search("\d+_(.+)",field).group(1)
    tipo=Database[field][0]
    default=""
    if "varchar" in tipo:
        default="not null default ''"
    sql+="\n\t%s %s %s,"%(field,tipo,default)

sql+="""
primary key (F000_AUTO_Codigoid)
);
"""
#primary key (F100_Codigo)

print sql
db.execute(sql)
