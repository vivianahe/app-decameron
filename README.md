# APP DECAMERON

## Descripción

Este proyecto corresponde a la **Prueba Técnica** para el puesto de **Desarrollador PHP**. El sistema permite ingresar los hoteles disponibles, incluyendo los datos básicos y tributarios, y asignarles diferentes tipos de habitación con validaciones específicas.

## Requisitos

1. Clona el repositorio: **git clone https://github.com/vivianahe/app-decameron.git** o Descarga el .zip

2. Instala composer al proyecto: **composer install** 

3. Instala las dependencias del proyecto: **npm install**

## Configuración del entorno 

Copia el archivo .env.example a un nuevo archivo llamado .env:

**cp .env.example .env**

DB_CONNECTION=pgsql

DB_HOST=127.0.0.1

DB_PORT=5432

DB_DATABASE=db_decameron

DB_USERNAME=postgres

DB_PASSWORD=tu_constraseña


## Generar la clave de la aplicación
Ejecuta el siguiente comando para generar la clave de la aplicación:
**php artisan key:generate**

## Ejecutar migraciones y seeder
Ejecuta los siguientes comandos para crear las tablas en la base de datos y ejecutar los seeder:

**php artisan migrate**

**php artisan db:seed**

## Compilar los assets
Compila los archivos de Vue.js y los archivos CSS con el siguiente comando:
**npm run dev**

## Ejecución de la Aplicación

Finalmente, ejecuta el servidor de desarrollo de Laravel:
**php artisan serve**

Accede al sistema a través de http://127.0.0.1:8000

## Acceso a la Aplicación
Para ingresar al sistema, utiliza las siguientes credenciales:

Correo: **admin@gmail.com**
Contraseña: **Admin-1234**
