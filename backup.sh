#!/bin/bash
echo "Dumping..."
filename="microcurriculos"
database="Curriculo"
dirback="archive/backup"

mysqldump -u root -p Curriculo > archive/backup/$filename.sql
echo "Compressing..."
tar cf archive/backup/$filename.tar archive/backup/$filename.sql $filename
p7zip archive/backup/$filename.tar
echo "Splitting..."
cd archive/backup/dump
rm $filename.tar.7z-*
split -b 1024k ../$filename.tar.7z $filename.tar.7z-
cd - &> /dev/null
echo "Git adding..."
git add --all -f archive/backup/dump/*
echo "Done."
