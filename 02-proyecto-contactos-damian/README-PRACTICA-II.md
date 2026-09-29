# Práctica Evaluada II — Ejercicio 2: Gestor de Contactos

CRUD completo (Crear, Leer, Actualizar, Eliminar) con formularios HTML, PHP
orientado a objetos y MySQL, usando **consultas preparadas** en todas partes.

## Estructura

```
02-gestor-contactos/
├── composer.json
├── index.php                        ← listado + filtro por tipo
├── crear.php                        ← formulario de alta
├── editar.php                       ← formulario de edición
├── procesar.php                     ← validaciones y guardado
├── eliminar.php                     ← confirmación y borrado
├── database/
│   └── schema.sql                   ← crear BD + tablas + datos
└── src/
    ├── Database.php                 ← conexión PDO
    ├── ContactoRepository.php       ← CRUD de contactos
    └── TipoContactoRepository.php   ← catálogo de tipos
```

## Base de datos

Base `gestor_contactos` con dos tablas relacionadas:

- `tipos_contacto` (id, nombre)
- `contactos` (id, nombre, email UNIQUE, telefono, tipo_contacto_id, fecha_creacion)

## Validaciones implementadas

- Nombre obligatorio y máximo 100 caracteres
- Email obligatorio, formato válido (`FILTER_VALIDATE_EMAIL`) y **único**
- Teléfono obligatorio y máximo 20 caracteres
- El tipo de contacto debe existir en `tipos_contacto`
- Antes de editar o eliminar se verifica que el contacto exista
- Todas las consultas usan consultas preparadas (PDO)
- Los datos se escapan con `htmlspecialchars` al mostrarlos

## Cómo ejecutarlo

### Con Docker (recomendado — MySQL incluido)

```bash
docker compose up --build
```

Abrir: <http://localhost:8081>

MySQL queda con usuario `root` y contraseña `root`.

### Sin Docker

1. Ejecutar `database/schema.sql` en MySQL (XAMPP, phpMyAdmin, etc.)
2. Ajustar las variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` o ponerlas
   como variables de entorno.
3. `composer install` y servir la carpeta con Apache o:
   ```bash
   php -S localhost:8000
   ```
