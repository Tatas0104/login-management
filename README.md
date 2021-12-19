# PHP LOGIN MANAGEMENT
## Requirement
* PHP VERSI =>8
* Composer
* Mysql
* Git
## Download Repository
> git clone  https://github.com/tatastuhu/login-management.git
## Setup Database
setup dabase bisa di lakukan dengan cara mengguanakan file database.sql
```sql
CREATE DATABASE php_login_management;

CREATE DATABASE php_login_management_test;

CREATE TABLE users(
    id VARCHAR(255) PRIMARY KEY ,
    name VARCHAR(255) NOT NULL ,
    password VARCHAR(255) NOT NULL
) ENGINE InnoDB;

CREATE TABLE sessions(
    id VARCHAR(255) PRIMARY KEY ,
    user_id VARCHAR(255) NOT NULL
)ENGINE InnoDB;

ALTER TABLE sessions
ADD CONSTRAINT fk_sessions_user
    FOREIGN KEY (user_id)
        REFERENCES users(id);
```
## Masuk Ke Direcrory Hasil Download
> cd login-management
## Install Library
> composer install 
## Manjalankan Autoload
> composer dump-autoload 
## Menjalankan Program
> cd public; php -S localhost:8080 
