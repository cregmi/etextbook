CREATE database library;
USE library;

DROP TABLE IF EXISTS book; 
CREATE TABLE book (
	id smallint unsigned NOT NULL auto_increment, 
	name varchar(100) NOT NULL,  
	description_tag varchar(100) NOT NULL,
	language_tag varchar(100) NOT NULL,
	entry_date datetime NOT NULL,
	pdf_file_name varchar(100) NOT NULL,
	image_file_name varchar(100), 
	PRIMARY KEY (Id)
) ENGINE = INNODB;

DROP TABLE IF EXISTS lesson; 
CREATE TABLE lesson (
	id smallint unsigned NOT NULL auto_increment,  
	entry_date datetime NOT NULL, 
	content_file_name varchar(100) NOT NULL,
	content_language varchar(100) NOT NULL,
	content_description_tag varchar(500),
	PRIMARY KEY (id)
) ENGINE = INNODB;


DROP TABLE IF EXISTS exercise; 
CREATE TABLE exercise (
	id smallint unsigned NOT NULL auto_increment,
	entry_date datetime NOT NULL, 
	content_file_name varchar(100) NOT NULL,
	content_language varchar(100) NOT NULL,
	content_description_tag varchar(500),
	PRIMARY KEY (id)
) ENGINE = INNODB;


DROP TABLE IF EXISTS book_map; 
CREATE TABLE book_map (
	book_id smallint unsigned NOT NULL,
	chapter_id smallint unsigned NOT NULL,
	chapter_start_page smallint,
	chapter_end_page smallint,
	lesson_id smallint unsigned,
	exercise_id smallint unsigned,
	FOREIGN KEY (book_id) REFERENCES book(id),
	FOREIGN KEY (lesson_id) REFERENCES lesson(id),
	FOREIGN KEY (exercise_id) REFERENCES exercise(id),
	CONSTRAINT UNIQUE (book_id,chapter_id)
) ENGINE = INNODB;

DROP TABLE IF EXISTS user_admin;
CREATE TABLE user_admin (
	id smallint unsigned NOT NULL auto_increment, 
	username varchar(500) NOT NULL UNIQUE, 
	hashed_password varchar(5000) NOT NULL, 
	PRIMARY KEY (id)
);
