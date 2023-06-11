<?php
    // Get url request
    $request_url = $_SERVER['REQUEST_URI'];
    $request_url = explode('/', $request_url);

    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        $request_url = array_values(array_filter($request_url));
        array_shift($request_url);
    }
    session_start();
    require_once './BackEnd/config.php';
    require_once './BackEnd/database.php';
    require_once './BackEnd/Routes.php';

    $db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->dbConnection();
    $db->set_char('utf8');

    if (!$db->isConnected()) {
        require_once './Assets/Pages/404.php';
        return;
    }

    // Controller
    $routes = new Routes();
    
    // $routes->show();
    $routes->run();
