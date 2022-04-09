CREATE DATABASE IF NOT EXISTS RedSocial;
USE RedSocial;


CREATE TABLE users(
id  int(255) AUTO_INCREMENT NOT NULL,
Nombre  varchar(255)NOT NULL,
Apellido varchar(255),
Role    varchar(20),
Email   varchar(255)NOT NULL,
Password varchar (255) NOT NULL,
Descripcion text,
Imagen varchar(255),
Creaate_at datetime DEFAULT NULL,
Update_at datetime DEFAULT NULL,
Token varchar(255)

CONSTRAINT pk_users PRIMARY KEY(id)

)ENGINE=innoDb;

CREATE TABLE Categorias(
id int(255) AUTO_INCREMENT NOT NULL,
Nombre varchar(255),
creat_at datetime DEFAULT null,
update_at datetime DEFAULT null,
CONSTRAINT pk_Categorias PRIMARY key(id)



)ENGINE=innoDb;


CREATE TABLE posts(
id int(255) AUTO_INCREMENT NOT NULL,
user_id int(255) not null,
categoria_id int(255) not null,
tutilo varchar(255) not null,
content text not null,
imagen varchar(255),
create_at datetime DEFAULT NULL,
update_at datetime DEFAULT NULL,
CONSTRAINT pk_posts PRIMARY key (id),
CONSTRAINT fk_post_user FOREIGN KEY (user_id) REFERENCES users(id),
CONSTRAINT fk_post_categoria FOREIGN KEY (categoria_id) REFERENCES Categorias(id)

)ENGINE=innoDb;

