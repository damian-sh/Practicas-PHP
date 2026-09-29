# Practica 1 - Sistema de reservas

El sistema de reservas estaba hecho en consola, el ejercicio era pasarlo a web
sin tocar la logica OOP.

## Que agregue

- `public/index.php` - el punto de entrada de la web
- `data/` - la carpeta donde queda `reservas.json`

`main.php` sigue funcionando en consola, solo le cambie la ruta para que guarde
el json en `data/`.

Las clases de `src/` no se modificaron.

## Como correrlo

```
docker compose up --build
```

http://localhost:8001

Sin docker:

```
composer install
php main.php
php -S localhost:8000 -t public/
```

La primera vez hay que correr `php main.php` porque es el que genera
`data/reservas.json`.
