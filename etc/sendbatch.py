#!/usr/bin/env python
#-*- coding:utf-8 -*-
from os import system
import csv
siteurl="http://astronomia-udea.co/principal/Curriculo"
csvfile=open("revision-planes.csv","rU")
content=csv.DictReader(csvfile,dialect="excel",delimiter=",")
i=0
for row in content:
    f=open("send.log","a")
    emails=row["Emails"]
    nombre=row["Curso"]
    codigo=row["Codigo"]
    urlbase=row["urlbase"].split(".")
    urlbase=urlbase[0]
    # emails="jorge.zuluaga@udea.edu.co, zuluagajorge@gmail.com"
    print "Enviando plan de asignatura de %s (%s) a %s..."%(nombre,codigo,emails)
    out=system("grep '%s:%s' send.log > /dev/null"%(codigo,emails))
    if out>0:
        urlsend="%s/index.php?planes_asignatura&accion=Enviar&emails=%s&urlbase=%s&nombresend=%s"%(siteurl,emails,urlbase,nombre);
        system("links -dump '%s' >> send-output.log"%urlsend)
        print "\tDone."
        f.write("%s:%s\n"%(codigo,emails))
    else:
        print "\tYa enviado..."
    f.close()
    i+=1
