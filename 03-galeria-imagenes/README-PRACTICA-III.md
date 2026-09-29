# Practica 3 - Galeria de imagenes

Aplicacion web para subir, validar, redimensionar y borrar imagenes. Usa
`intervention/image` para las miniaturas.

## Archivos

- `config.php` - las constantes (rutas, limites, zona horaria)
- `gestor.php` - la clase `GestorImagenes`
- `index.php` - el formulario de carga y la galeria
- `css/styles.css` - los estilos
- `uploads/` - donde quedan las imagenes

## Que valida

- Que la carga no haya dado error
- Que no pase de 5MB
- Que el tipo MIME sea jpeg, png o gif
- Que la extension este permitida

Los nombres de los archivos se generan con `uniqid()`. Las imagenes se
redimensionan a 300x300 y para borrarlas pide confirmacion.

## Como correrlo

```
docker compose up --build
```

http://localhost:8082

Sin docker, PHP necesita la extension GD y `composer install`:

```
composer require intervention/image
php -S localhost:8000
```
