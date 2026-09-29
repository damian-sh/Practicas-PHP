# Práctica Evaluada I — Sistema de Reservas (CLI → Web)

Transformación de la aplicación CLI original a una aplicación web, manteniendo
intacta toda la lógica OOP (interfaz `Reservable`, clase abstracta `Espacio`,
3 subclases concretas, `Horario`, `Reserva` y los servicios).

## Estructura

```
01-sistema-reservas-web/
├── public/
│   └── index.php          ← punto de entrada web (nuevo)
├── data/
│   └── reservas.json      ← datos (movido desde la raíz)
├── src/                   ← clases del dominio (sin cambios)
├── vendor/
├── main.php               ← versión CLI original (sigue funcionando)
├── composer.json
├── Dockerfile
└── docker-compose.yml
```

## Qué se agregó

- `public/index.php`: punto de entrada web que carga los datos de
  `data/reservas.json`, los hidrata en objetos `Reserva`/`Horario` y los
  recorre polimórficamente (sin `instanceof`).
- `data/`: carpeta creada según la guía; `main.php` ahora exporta ahí.

La lógica OOP de `src/` **no se modificó**.

## Cómo ejecutarlo

### Con Docker (recomendado)

```bash
docker compose up --build
```

Abrir: <http://localhost:8001>

### Sin Docker

```bash
composer install
php main.php                 # genera data/reservas.json
php -S localhost:8000 -t public/
```

Abrir: <http://localhost:8000>
