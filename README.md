# Practicas PHP

Practicas 1, 2 y 3 de Desarrollo de Paginas Web con Software Libre.

Cada Practica esta en su propia carpeta y tiene su propio `docker-compose.yml`.
Con Docker no hay que instalar nada mas.

## Practica 1 - Sistema de reservas

Converti el sistema de reservas que estaba en consola a una aplicacion web.

Agregue la carpeta `public/` con el `index.php` y la carpeta `data/`, y movi
`reservas.json` ahi. Las clases de `src/` no las toque, la pagina web las usa
igual que antes: arma los objetos `Reserva` y `Horario` desde el json y los
recorre sin usar `instanceof`.

El `main.php` original sigue funcionando en consola.

```
cd 01-sistema-reservas-web
docker compose up --build
```

http://localhost:8001

## Practica 2 - Gestor de contactos

CRUD con formularios HTML, PHP orientado a objetos y MySQL.
Es el ejercicio 2 de los dos que trae la Practica 2.

Las consultas SQL usan prepared statements, no concateno strings.

Base de datos: `gestor_contactos`
Usuario: `root` / Clave: `root` / Puerto: `3306`

El script esta en `database/schema.sql` y se ejecuta solo cuando levanta el
contenedor, asi que no hay que correrlo a mano.

```
cd 02-proyecto-contactos-damian
docker compose up --build
```

http://localhost:8081

Lo que tiene: listado, filtro por tipo de contacto, crear, editar, eliminar con
confirmacion. Valida que el nombre no pase de 100 caracteres, que el email tenga
formato valido y no este repetido, que el telefono no pase de 20, y que el tipo
de contacto exista en la base. Antes de editar o borrar revisa que el contacto
exista.

## Practica 3 - Galeria de imagenes

Subir imagenes, validarlas, redimensionarlas a 300x300 y borrarlas.
Usa `intervention/image`.

Valida el error de la carga, que no pase de 5MB, el tipo MIME y la extension.
Los nombres se generan con `uniqid()`. Para borrar pide confirmacion.

```
cd 03-galeria-imagenes
docker compose up --build
```

http://localhost:8082

## Sin Docker

Tambien se puede con XAMPP o Laragon, pero hay que tener PHP 8.2 o mayor y
Composer. La Practica 2 necesita MySQL y la 3 necesita la extension GD.

- Practica 1: `composer install` y `php -S localhost:8000 -t public/`
- Practica 2: correr `database/schema.sql`, `composer install` y `php -S localhost:8000`
- Practica 3: `composer install` y `php -S localhost:8000`

Las carpetas `vendor/` las subi al repo para que no haya que instalar nada.
