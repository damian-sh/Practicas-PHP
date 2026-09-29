# Práctica Evaluada III — Galería de Imágenes

Aplicación web para subir, validar, redimensionar, listar y eliminar imágenes.
Usa **Intervention Image** para generar las miniaturas.

## Estructura

```
03-galeria-imagenes/
├── composer.json
├── config.php            ← constantes: rutas, límites, zona horaria
├── gestor.php            ← clase GestorImagenes
├── index.php             ← formulario de carga + galería
├── css/
│   └── styles.css        ← estilos (responsive)
└── uploads/              ← imágenes guardadas
```

## Funcionalidades

- Carga de imágenes con validación
- Validación de: error de carga, tamaño (máx. 5 MB), tipo MIME y extensión
- Nombres de archivo seguros generados con `uniqid()`
- Redimensionado automático a 300×300 px
- Listado dinámico de las imágenes subidas
- Eliminación con confirmación
- Mensajes de éxito, error y advertencia
- Interfaz responsive

## Configuración (`config.php`)

| Constante | Valor |
|---|---|
| `TAMANIO_MAXIMO` | 5 MB |
| `TIPOS_PERMITIDOS` | `image/jpeg`, `image/png`, `image/gif` |
| `ANCHO_MINIATURA` / `ALTO_MINIATURA` | 300 / 300 |

## Cómo ejecutarlo

### Con Docker (recomendado — GD incluido)

```bash
docker compose up --build
```

Abrir: <http://localhost:8082>

### Sin Docker

Requiere PHP con las extensiones **GD** y **fileinfo**.

```bash
composer require intervention/image
php -S localhost:8000
```

Abrir: <http://localhost:8000>
