create user 'curriculo'@'localhost' identified by '123';
create database Curriculo;
grant all privileges on Curriculo.* to 'curriculo'@'localhost';
flush privileges;
