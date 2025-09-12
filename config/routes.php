<?php
// config/routes.php

return [
  'GET' => [
    '/'            => ['AuthController', 'index'],
    '/auth/login'  => ['AuthController', 'showLoginForm'],
    '/auth/register' => ['AuthController', 'showRegisterForm'],
    '/auth/logout' => ['AuthController', 'logout'],
    
    '/admin/dashboard' => ['Admin\\DashboardController', 'index'],


    '/admin/users'     => ['Admin\\UsersController', 'index'],
    '/admin/users/search' => ['Admin\\UsersController', 'search'],
    '/admin/users/create' => ['Admin\\UsersController', 'create'],
    '/admin/users/edit/(\d+)' => ['Admin\\UsersController', 'edit'],
    '/admin/users/export/excel' => ['Admin\\UsersController', 'exportExcel'],
    '/admin/users/export/pdf'   => ['Admin\\UsersController', 'exportPDF'],

    '/admin/categorias'   => ['Admin\\CategoriasController', 'index'],
    '/admin/categorias/create' => ['Admin\\CategoriasController', 'create'],
    '/admin/categorias/edit/(\d+)' => ['Admin\\CategoriasController', 'edit'],
    '/admin/categorias/search' => ['Admin\\CategoriasController', 'search'],
    '/admin/categorias/export/excel' => ['Admin\\CategoriasController', 'exportExcel'],
    '/admin/categorias/export/pdf'   => ['Admin\\CategoriasController', 'exportPDF'],

    '/admin/senas'   => ['Admin\\SenasController', 'index'],
    '/admin/senas/create' => ['Admin\\SenasController', 'create'],
    '/admin/senas/edit/(\d+)' => ['Admin\\SenasController', 'edit'],
    '/admin/senas/search' => ['Admin\\SenasController', 'search'],
    '/admin/senas/export/excel' => ['Admin\\SenasController', 'exportExcel'],
    '/admin/senas/export/pdf'   => ['Admin\\SenasController', 'exportPDF'],

    '/admin/tests'   => ['Admin\\TestsController', 'index'],
    '/admin/tests/create' => ['Admin\\TestsController', 'create'],
    '/admin/tests/edit/(\d+)' => ['Admin\\TestsController', 'edit'],
    '/admin/tests/search' => ['Admin\\TestsController', 'search'],
    '/admin/tests/export/excel' => ['Admin\\TestsController', 'exportExcel'],
    '/admin/tests/export/pdf'   => ['Admin\\TestsController', 'exportPDF'],

    '/admin/desbloqueos'   => ['Admin\\DesbloqueosController', 'index'],
    '/admin/desbloqueos/create' => ['Admin\\DesbloqueosController', 'create'],
    '/admin/desbloqueos/edit/(\d+)' => ['Admin\\DesbloqueosController', 'edit'],
    '/admin/desbloqueos/search' => ['Admin\\DesbloqueosController', 'search'],
    '/admin/desbloqueos/export/excel' => ['Admin\\DesbloqueosController', 'exportExcel'],
    '/admindes/bloqueos/export/pdf'   => ['Admin\\DesbloqueosController', 'exportPDF'],

    '/admin/progreso' => ['Admin\\ProgresoCategoriaController', 'index'],
    '/admin/progreso/user/(\d+)' => ['Admin\\ProgresoCategoriaController', 'userProgress'],
    '/admin/progreso/export/excel' => ['Admin\\ProgresoCategoriaController', 'exportExcel'],
    '/admin/progreso/export/pdf'   => ['Admin\\ProgresoCategoriaController', 'exportPDF'],
  

    '/usuarios/main'   => ['User\\MainController', 'index'],
    '/usuarios/perfil' => ['User\\PerfilUserController', 'index'],
    '/usuarios/categorias' => ['User\\CategoriasUserController', 'index'],
    '/usuarios/senas/(\d+)' => ['User\\SenasUserController', 'index'],
    '/usuarios/test' => ['User\\TestUserController', 'index'],
    '/usuarios/test/iniciar/(\d+)' => ['User\\TestUserController', 'iniciarTest'], // Nueva ruta para iniciar el test

  ],

  'POST' => [
    '/auth/login'    => ['AuthController', 'login'],
    '/auth/register' => ['AuthController', 'register'],
    '/auth/logout' => ['AuthController', 'logout'],

    '/admin/users/store'        => ['Admin\\UsersController', 'store'],
    '/admin/users/update/(\d+)' => ['Admin\\UsersController', 'update'],
    '/admin/users/delete/(\d+)' => ['Admin\\UsersController', 'delete'],

    '/admin/categorias/store'        => ['Admin\\CategoriasController', 'store'],
    '/admin/categorias/update/(\d+)' => ['Admin\\CategoriasController', 'update'],
    '/admin/categorias/delete/(\d+)' => ['Admin\\CategoriasController', 'delete'],

    '/admin/senas/store'        => ['Admin\\SenasController', 'store'],
    '/admin/senas/update/(\d+)' => ['Admin\\SenasController', 'update'],
    '/admin/senas/delete/(\d+)' => ['Admin\\SenasController', 'delete'],

    '/admin/tests/store'        => ['Admin\\TestsController', 'store'],
    '/admin/tests/update/(\d+)' => ['Admin\\TestsController', 'update'],
    '/admin/tests/delete/(\d+)' => ['Admin\\TestsController', 'delete'],

    '/admin/desbloqueos/store'        => ['Admin\\DesbloqueosController', 'store'],
    '/admin/desbloqueos/update/(\d+)' => ['Admin\\DesbloqueosController', 'update'],
    '/admin/desbloqueos/delete/(\d+)' => ['Admin\\DesbloqueosController', 'delete'],

    '/admin/progreso/reset/(\d+)' => ['Admin\\ProgresoController', 'resetUserProgress'],
    
    '/usuarios/test/enviarResultado' => ['User\\TestUserController', 'enviarResultado'],

    '/usuarios/perfil/editarNombre' => ['User\\PerfilUserController', 'editarNombre'],
    '/usuarios/perfil/cambiarPassword' => ['User\\PerfilUserController', 'cambiarPassword'],
    '/usuarios/senas/registrarProgreso' => ['User\\SenasUserController', 'registrarProgreso'],
    ]

];
