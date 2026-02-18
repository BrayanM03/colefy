<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema Colefy para colegios y escuelas">
    <meta name="author" content="Colefy">
    <meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="<?php echo STATIC_URL; ?>img/icons/icon-48x48.png" />
	<link rel="canonical" href="https://demo-basic.adminkit.io/icons-feather.html" />
    <link rel="canonical" href="https://demo-basic.adminkit.io/pages-blank.html" />

    <title><?= $titulo_vista ?> | Colefy</title>

    <link href="<?php echo STATIC_URL; ?>css/app.css" rel="stylesheet">
    <link href="<?php echo STATIC_URL; ?>css/ui.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <?php if (isset($css_especificos)): ?>
        <?php foreach ($css_especificos as $url): ?>
            <link rel="stylesheet" href="<?php echo $url; ?>"></link>
        <?php endforeach; ?>
    <?php endif; ?>
</head>