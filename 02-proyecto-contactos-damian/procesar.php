<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\ContactoRepository;
use App\Database;
use App\TipoContactoRepository;

session_start();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: index.php');
    exit;
}

$pdo = Database::obtenerConexion();
$contactosRepo = new ContactoRepository($pdo);
$tiposRepo = new TipoContactoRepository($pdo);

$accion = (string) ($_POST['accion'] ?? '');

/* ---------------------------------------------------------------
 | Sanear los datos recibidos: trim para guardar, el escapado HTML
 | (htmlspecialchars) se hace al MOSTRAR, no al guardar, para no
 | producir doble escapado.
 * --------------------------------------------------------------- */
$nombre   = trim((string) ($_POST['nombre'] ?? ''));
$email    = trim((string) ($_POST['email'] ?? ''));
$telefono = trim((string) ($_POST['telefono'] ?? ''));
$tipo     = trim((string) ($_POST['tipo'] ?? ''));
$id       = (int) ($_POST['id'] ?? 0);

$errores = [];

/* ---------------------------- VALIDACIONES ---------------------------- */

// Nombre: obligatorio, no vacío y máximo 100 caracteres
if ($nombre === '') {
    $errores['nombre'] = 'El nombre es obligatorio y no puede estar vacío.';
} elseif (mb_strlen($nombre) > 100) {
    $errores['nombre'] = 'El nombre no puede superar los 100 caracteres.';
}

// Email: obligatorio, formato válido
if ($email === '') {
    $errores['email'] = 'El email es obligatorio.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores['email'] = 'El email no tiene un formato válido.';
} elseif (mb_strlen($email) > 100) {
    $errores['email'] = 'El email no puede superar los 100 caracteres.';
}

// Teléfono: obligatorio y máximo 20 caracteres
if ($telefono === '') {
    $errores['telefono'] = 'El teléfono es obligatorio.';
} elseif (mb_strlen($telefono) > 20) {
    $errores['telefono'] = 'El teléfono no puede superar los 20 caracteres.';
}

// Tipo de contacto: obligatorio y debe existir en la tabla tipos_contacto
$tipoId = 0;
if ($tipo === '' || !ctype_digit($tipo)) {
    $errores['tipo'] = 'Debe seleccionar un tipo de contacto.';
} else {
    $tipoId = (int) $tipo;
    if (!$tiposRepo->existe($tipoId)) {
        $errores['tipo'] = 'El tipo de contacto seleccionado no existe.';
    }
}

/* --------------------------- ACCIONES --------------------------- */

if ($accion === 'crear') {
    // El email debe ser único
    if (!isset($errores['email']) && $contactosRepo->emailExiste($email)) {
        $errores['email'] = 'Ya existe un contacto registrado con ese email.';
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos'] = compact('nombre', 'email', 'telefono', 'tipo');
        header('Location: crear.php');
        exit;
    }

    try {
        $nuevoId = $contactosRepo->crear($nombre, $email, $telefono, $tipoId);
        $_SESSION['exito'] = "Contacto creado correctamente con el ID #{$nuevoId}.";
    } catch (PDOException $e) {
        $_SESSION['error'] = 'No se pudo guardar el contacto: ' . $e->getMessage();
    }

    header('Location: index.php');
    exit;
}

if ($accion === 'actualizar') {
    // Verificar que el contacto exista antes de editar
    if ($id <= 0 || !$contactosRepo->existe($id)) {
        $_SESSION['error'] = 'El contacto que intenta editar no existe.';
        header('Location: index.php');
        exit;
    }

    // El email debe ser único, excluyendo el propio contacto
    if (!isset($errores['email']) && $contactosRepo->emailExiste($email, $id)) {
        $errores['email'] = 'Ya existe otro contacto registrado con ese email.';
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos'] = compact('nombre', 'email', 'telefono', 'tipo');
        header('Location: editar.php?id=' . $id);
        exit;
    }

    try {
        $contactosRepo->actualizar($id, $nombre, $email, $telefono, $tipoId);
        $_SESSION['exito'] = 'Contacto actualizado correctamente.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'No se pudo actualizar el contacto: ' . $e->getMessage();
    }

    header('Location: index.php');
    exit;
}

$_SESSION['error'] = 'Acción no reconocida.';
header('Location: index.php');
exit;
