# Prácticas Evaluadas — Desarrollo de Páginas Web con Software Libre

**Autor:** Harry Damian (`damian-sh`)
**Materia:** Desarrollo de Páginas Web con Software Libre

Las tres prácticas están en este repositorio, cada una en su propia carpeta y
con su propio `docker-compose.yml`. Cada una se levanta y se prueba por
separado.

---

## Inicio rápido

Lo único que hace falta es **Docker**. No hay que instalar PHP, MySQL ni GD.

```bash
cd 01-sistema-reservas-web   && docker compose up --build   # → http://localhost:8001
cd 02-proyecto-contactos-damian && docker compose up --build   # → http://localhost:8081
cd 03-galeria-imagenes      && docker compose up --build   # → http://localhost:8082
```

Para detener: `Ctrl + C` y después `docker compose down`
(en la Práctica II: `docker compose down -v` para borrar también la base de datos).

---

## Las tres prácticas

| # | Práctica | Qué hace | Puerto |
|---|----------|----------|--------|
| 01 | [Sistema de Reservas (CLI → Web)](01-sistema-reservas-web/) | Convierte la aplicación CLI original a web, manteniendo la lógica OOP intacta | 8001 |
| 02 | [Gestor de Contactos](02-proyecto-contactos-damian/) | CRUD completo con formularios, POO y MySQL | 8081 |
| 03 | [Galería de Imágenes](03-galeria-imagenes/) | Validación de datos y gestión de imágenes con Intervention Image | 8082 |

Cada carpeta tiene su propio `README-PRACTICA-*.md` con el detalle.

---

## Práctica I — Sistema de Reservas

Transformación de la aplicación CLI a web. Se agregó `public/index.php` y la
carpeta `data/`, y se movió `reservas.json` ahí. **Las clases de `src/` no se
modificaron**: la página web las reutiliza, hidrata el JSON en objetos `Reserva`
y `Horario`, y los recorre polimórficamente (sin `instanceof`).

El `main.php` original sigue funcionando en modo consola.

## Práctica II — Gestor de Contactos (Ejercicio 2)

CRUD con MySQL usando consultas preparadas (PDO) en todas las consultas.

Base de datos `gestor_contactos` con dos tablas relacionadas: `tipos_contacto`
y `contactos`. El script está en `02-proyecto-contactos-damian/database/schema.sql`
y se ejecuta solo al levantar el contenedor.

Incluye: listado, filtro por tipo de contacto, alta, edición, eliminación con
confirmación, email único y todas las validaciones pedidas.

**Acceso a MySQL:** usuario `root`, contraseña `root`, puerto `3306`.

Validaciones implementadas: nombre obligatorio (máx. 100), email obligatorio con
formato válido y único, teléfono obligatorio (máx. 20), el tipo de contacto debe
existir en la base, y se verifica que el contacto exista antes de editar o
eliminar.

## Práctica III — Galería de Imágenes

Subida, validación, redimensionado, listado y eliminación de imágenes con
Intervention Image.

- Valida error de carga, tamaño (máx. 5 MB), tipo MIME y extensión
- Nombres seguros generados con `uniqid()`
- Redimensionado automático a 300×300 px
- Eliminación con confirmación
- Interfaz responsive

---

## Alternativa sin Docker

Si se prefiere trabajar con XAMPP / Laragon:

- **Práctica I:** `composer install`, luego `php -S localhost:8000 -t public/`
- **Práctica II:** ejecutar `database/schema.sql` en MySQL, `composer install`,
  ajustar las variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, y
  `php -S localhost:8000`
- **Práctica III:** requiere PHP con las extensiones **GD** y **fileinfo**,
  `composer install`, y `php -S localhost:8000`

---

## Requisitos

- Docker y Docker Compose
- PHP 8.2+ (solo para la alternativa sin Docker)
- Composer (solo para la alternativa sin Docker)

## Notas

- Las carpetas `vendor/` están incluidas para que los proyectos corran sin
  instalar dependencias.
- La Práctica II usa el puerto **8081** en vez del 8080 habitual porque el 8080
  ya estaba ocupado en la máquina donde se probó.
