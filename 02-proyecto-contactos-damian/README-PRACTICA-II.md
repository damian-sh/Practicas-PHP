# Practica 2 - Gestor de contactos

CRUD con formularios HTML, PHP orientado a objetos y MySQL. Es el ejercicio 2
de los dos que trae la Practica 2.

## Archivos

- `index.php` - el listado con el filtro por tipo de contacto
- `crear.php` / `editar.php` - los formularios
- `procesar.php` - valida y guarda
- `eliminar.php` - pide confirmacion y borra
- `src/Database.php` - la conexion con PDO
- `src/ContactoRepository.php` - el CRUD de contactos
- `src/TipoContactoRepository.php` - los tipos de contacto
- `database/schema.sql` - crea la base y las tablas

## Base de datos

`gestor_contactos`, con `tipos_contacto` y `contactos` (esta tiene el email
UNIQUE y la llave foranea a `tipos_contacto`).

Usuario `root`, clave `root`, puerto `3306`.

El `schema.sql` se ejecuta solo cuando levanta el contenedor.

## Validaciones

- Nombre obligatorio, maximo 100 caracteres
- Email obligatorio, con formato valido y unico
- Telefono obligatorio, maximo 20 caracteres
- El tipo de contacto tiene que existir en `tipos_contacto`
- Antes de editar o eliminar se revisa que el contacto exista

Todas las consultas usan prepared statements.

## Como correrlo

```
docker compose up --build
```

http://localhost:8081

Sin docker, correr el `schema.sql` en MySQL, despues `composer install` y
`php -S localhost:8000`. Si el MySQL no esta en localhost o las credenciales
son otras, se cambian las variables `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS`.
