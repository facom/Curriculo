import MySQLdb as mdb
from sys import exit,argv
import random
import numpy as np
import copy
import pickle
from os import system
import re
from time import sleep
from sys import argv

###################################################
#CONFIGURACION
###################################################
BASENAME="Curriculo"
DATABASE="Curriculo"
USER="curriculo"
PASSWORD="123"

###################################################
#ROUTINES
###################################################
class dict2obj(object):
    def __init__(self,dic={}):self.__dict__.update(dic)
    def __add__(self,other):
        for attr in other.__dict__.keys():
            exec("self.%s=other.%s"%(attr,attr))
        return self

def loadDatabase(server='localhost',
                 user=USER,
                 password=PASSWORD,
                 database=DATABASE):
    con=mdb.connect(server,user,password,database)
    with con:
        dbdict=dict()
        db=con.cursor()
        db.execute("show tables;")
        tables=db.fetchall()
        for table in tables:
            table=table[0]
            dbdict[table]=dict()
            
            db.execute("show columns from %s;"%table)
            fields=db.fetchall()
            dbdict[table]['fields']=[]
            for field in fields:
                fieldname=field[0]
                fieldtype=field[3]
                dbdict[table]['fields']+=[fieldname]
                if fieldtype=='PRI':
                    dbdict[table]['primary']=fieldname

            db.execute("select * from %s;"%table)
            rows=db.fetchall()

            dbdict[table]['rows']=dict()
            for row in rows:
                rowdict=dict()
                i=0
                for field in dbdict[table]['fields']:
                    rowdict[field]=row[i]
                    if field==dbdict[table]['primary']:
                        primary=row[i].strip()
                    i+=1
                dbdict[table]['rows'][primary]=rowdict

    return dbdict,con

def updateDatabase(dbdict,con):
    with con:
        db=con.cursor()
        for table in dbdict.keys():
            print "Actualizando tabla ",table
            for row in dbdict[table]['rows'].keys():
                sql="update %s set "%table;
                for field in dbdict[table]['fields']:
                    if field==dbdict[table]['primary']:
                        suffix="where %s='%s'"%(field,dbdict[table]['rows'][row][field])
                        continue
                    sql+="%s = '%s',"%(field,dbdict[table]['rows'][row][field])
                sql=sql.strip(",")+" %s;"%suffix
                db.execute(sql);
    con.commit()

def updateTable(dbdict,table,con):
    with con:
        db=con.cursor()
        print "Actualizando tabla ",table
        for row in dbdict[table]['rows'].keys():
            sql="update %s set "%table;
            for field in dbdict[table]['fields']:
                if field==dbdict[table]['primary']:
                    suffix="where %s='%s'"%(field,dbdict[table]['rows'][row][field])
                    continue
                sql+="%s = '%s',"%(field,dbdict[table]['rows'][row][field])
            sql=sql.strip(",")+" %s;"%suffix
            db.execute(sql);
    con.commit()
