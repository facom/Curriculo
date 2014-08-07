#Archivos de configuración plantilla
echo "Copiando archivos plantilla..."
cp temp/*.* etc

#Instala Curriculo en un servidor
echo "Pasandome al directorio de configuracion..."
cd etc

#Base de datos
echo "Creando el usuario y la base de datos en myslq..."
echo "Entre la contraseña del 'root' de mysql:"
mysql -u root -p < initialize.sql

#Archivo de base de datos para php
echo "Creando el archivo con base de datos para php..."
python initialize.py &> ../tmp/error.log

cd ..
echo "Hecho."
