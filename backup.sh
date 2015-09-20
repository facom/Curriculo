#!/bin/bash
backfile="tmp/backup-curriculo.tar"

if [ -e $backfile.gz ];then rm $backfile.gz;fi

echo "Backuping database..."
mysqldump -u root --password=diplomaastro Curriculo > tmp/curriculo.sql

echo "Backuping data..."
tar chf $backfile data/
tar rf $backfile etc --exclude=PHPMailer

echo "Compressing..."
tar rf $backfile tmp/curriculo.sql 
gzip $backfile

echo "Done."
