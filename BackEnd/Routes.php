<?php

class Routes
{
    private $routes = array();

    public function __construct()
    {

        $this->get('/', 'HomeController::index');

        $this->get('login', 'MemberController::login');
        $this->post('api/login', 'MemberController::API_Login');

        $this->get('logout', 'HomeController::logout');
        
        $this->get('profile', 'MemberController::profile');
        $this->post('api/profile', 'MemberController::API_ChangeProfile');
        $this->post('api/password', 'MemberController::API_ChangePassword');

        $this->get('admin', 'RouteController::index');
        $this->post('api/createRoute', 'RouteController::API_CreateRoute');
        $this->post('api/deleteRoute', 'RouteController::API_DeleteRoute');
        $this->post('api/getRouteByID', 'RouteController::API_GetRouteByID');
        $this->post('api/updateRoute', 'RouteController::API_UpdateRoute');


        $this->get('admin/account', 'MemberController::accountAdmin');
        $this->post('api/createAccount', 'MemberController::API_CreateAccount');
        $this->post('api/deleteAccount', 'MemberController::API_DeleteAccount');

        $this->get('contact', 'HomeController::contact');

        $this->post('api/search', 'RouteController::API_Search');
        $this->post('api/search_t', 'RouteController::API_Search_T');

        $this->post('api/loadLocation', 'RouteController::API_LoadRouteByLocation');
        $this->post('api/loadStation', 'RouteController::API_LoadStation');
        $this->post('api/updateStation', 'RouteController::API_UpdateStation');

        $this->get('route', 'RouteController::route');
        $this->post('api/getColumn', 'RouteController::API_GetColumnRoute');
        $this->post('api/getDetailColumn', 'RouteController::API_GetDetailColumn');

        $this->get('route/t', 'RouteController::routeT');
        $this->post('api/getBranch', 'RouteController::API_LoadBranch');

        $this->get('admin/branch', 'MemberController::branch_manager');
        $this->get('admin/branch/account', 'MemberController::branch_account');
        $this->get('report', 'RouteController::report');

        $this->post('api/report', 'RouteController::API_Report');
        $this->post('api/report/detail', 'RouteController::API_GetDetailReportByID');
        $this->post('api/report/branch', 'RouteController::API_GetReportByBranchID');
        $this->post('api/report/branch/paging', 'RouteController::API_GetReportByBranchPaging');

        $this->get('api/auto_report', 'RouteController::API_DeleteReport');

        return $this;
    }

    public function get($url, $function)
    {
        $this->routes['GET'][] = array(
            'url' => $url,
            'function' => $function
        );
        return $this;
    }

    public function post($url, $function)
    {
        $this->routes['POST'][] = array(
            'url' => $url,
            'function' => $function
        );
        return $this;
    }

    public function put($url, $function)
    {
        $this->routes['PUT'][] = array(
            'url' => $url,
            'function' => $function
        );
        return $this;
    }

    public function delete($url, $function)
    {
        $this->routes['DELETE'][] = array(
            'url' => $url,
            'function' => $function
        );
        return $this;
    }

    // public function group($url, ...$params)
    // {
    //     echo '<br>'.$url.'<br>';
    //     for ($i = 0; $i < count($params); $i++) {
    //         $callback = $params[$i];
    //         $this->group_routes = $url;
    //         $callback($this);
    //     }
    // }

    public function show()
    {
        echo '<br><br><pre>';
        print_r($this->routes);
        echo '</pre><br><br>';
    }

    public function run()
    {
        // Get url request
        $request_url = $_SERVER['REQUEST_URI'];
        $request_url = explode('/', $request_url);
        
        if (!function_exists('str_contains')) {
            function str_contains(string $haystack, string $needle): bool
            {
                return '' === $needle || false !== strpos($haystack, $needle);
            }
        }

        // remove the element containing the ? inside
        $request_url = array_values(array_filter($request_url, function ($value) {
            return !str_contains($value, '?');
        }));

        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            $request_url = array_values(array_filter($request_url));
            array_shift($request_url);
        }

        $request_url = implode('/', $request_url);
        if ($request_url == '' || $request_url == '/') {
            $request_url = '/';
        }
        else {
            // Xóa ký tự / nếu ở vị trí đầu tiên
            $request_url = ltrim($request_url, '/');
        }

        // Get type of request
        $request_method = $_SERVER['REQUEST_METHOD'];
        $request_method = strtoupper($request_method);

        // Controller
        if (!isset($this->routes[$request_method])) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Method not allowed'
            ));
            return;
        }

        $routes = $this->routes[$request_method];
        $route_found = false;

        foreach ($routes as $route) {
            if ($route['url'] == $request_url) {
                $route_found = $route['function'];
                break;
            }
        }
        if ($route_found == false) {
            require_once './Assets/Pages/404.php';
            return;
        }

        // $function = $routes[$route_found]['function'];
        
        $controller = explode('::', $route_found)[0];
        $method = explode('::', $route_found)[1];
        


        if (file_exists('./BackEnd/Controllers/' . $controller . '.php')) {

            if (!class_exists('HomeController')) {
                require_once './BackEnd/Controllers/HomeController.php';
            } 

            require_once './BackEnd/Controllers/HomeController.php';
            require_once './BackEnd/Controllers/RouteController.php';
            require_once './BackEnd/Controllers/MemberController.php';

            $controller = new $controller();
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                echo json_encode(array('status' => false, 'message' => 'Method not found'));
                http_response_code(404);
            }
        } else {
            echo json_encode(array('status' => false, 'message' => 'Controller not found'));
            http_response_code(404);
        }
        

    }
}