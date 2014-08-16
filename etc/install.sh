#Opciones
opciones=$1;shift

#Archivos de configuración plantilla
echo "Copiando archivos plantilla..."
cp temp/*.* etc

#Archivo de base de datos para php
echo "Generando md5 para contraseñas..."
cp temp/configuration.php etc/
php temp/configuration.php >> etc/configuration.php

#Instala Curriculo en un servidor
echo "Pasandome al directorio de configuracion..."
cd etc

if [ "x$opciones" = "x" ];then
    #Base de datos
    echo "Creando el usuario y la base de datos en myslq..."
    echo "Entre la contraseña del 'root' de mysql:"
    mysql -u root -p < initialize.sql
fi
  
#Archivo de base de datos para php
echo "Creando el archivo con base de datos para php..."
python initialize.py $opciones &> ../tmp/error.log

cd ..
echo "Hecho."
