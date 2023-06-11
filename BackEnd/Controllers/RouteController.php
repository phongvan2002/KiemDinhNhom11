<?php

class RouteController extends HomeController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        if (!$this->isLogin() || !$this->isAdmin())
        {
            header('Location: ./login');
            return;
        }

        $listLocation = $this->getListLocation();

        $listRoute = array();
        $station = array();

        if (count($listLocation) > 0) {
            $listRoute = $this->getListRouteByStation($listLocation[0]);
            $station = $this->getStationName($listLocation[0]);
        }
        
        $this->loadLayout('Quản lý đường dây', array(
            '<link rel="stylesheet" href="./Assets/Css/admin/index.css">',
            '<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">',
            '<script src="./Assets/Js/admin/index.js"></script>',
        ));
        include_once './Assets/Pages/admin/index.php';
    }

    public function route()
    {
        if (!$this->isLogin() || $this->isAdmin())
        {
            header('Location: ./login');
            return;
        }

        $listLocation = $this->getListLocation();
        $listRoute = array();
        $listColumn = array();

        if (count($listLocation) > 0) {
            $listRoute = $this->getListRouteByStation($listLocation[0]);
            $listColumn = $this->getListColumn($listLocation[0]);
        }
        
        $this->loadLayout('Thông tin đường dây 110kV', array(
            '<link rel="stylesheet" href="./Assets/Css/client/route.css">',
            '<script src="./Assets/Js/client/route.js"></script>',
        ), 4);
        include_once './Assets/Pages/client/route.php';
    }

    public function routeT()
    {
        if (!$this->isLogin() || $this->isAdmin())
        {
            header('Location: ./login');
            return;
        }

        $listLocation = $this->getListLocation();
        $listRoute = array();
        $station = array();

        if (count($listLocation) > 0) {
            $listRoute = $this->getListRouteByStation($listLocation[0]);
            $station = $this->getStationName($listLocation[0]);
        }
        
        $this->loadLayout('Vị trí sự cố đường dây T', array(
            '<link rel="stylesheet" href="./Assets/Css/client/routeT.css">',
            '<script src="./Assets/Js/client/routeT.js"></script>',
        ), 5);
        include_once './Assets/Pages/client/routeT.php';
    }

    public function report()
    {
        // check if user is login
        if (!$this->isLogin() || !$this->isBranch())
        {
            header('Location: ./login');
            return;
        }

        $listReport = $this->getNewReportByBranchID($_SESSION['Member_ID'])['report'];

        $this->loadLayout('Báo cáo sự cố', array(
            '<link rel="stylesheet" href="./Assets/Css/client/report.css">',
            '<script src="./Assets/Js/client/report.js"></script>',
        ), 6);
        include_once './Assets/Pages/client/report.php';
    }

    public function API_CreateRoute(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập',
            ));
            return;
        } 
        if (!$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }
        // check if all fields are filled: start, end, column, purpose, type, topographic, distance, latitude, longitude, note
        if (!isset($_POST['start']) || !isset($_POST['end']) || !isset($_POST['column']) || !isset($_POST['purpose']) || !isset($_POST['type']) || !isset($_POST['topographic']) || !isset($_POST['distance']) || !isset($_POST['latitude']) || !isset($_POST['longitude']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }

        // assign to variable
        $start = $_POST['start'];
        $end = $_POST['end'];
        $column = $_POST['column'];
        $purpose = $_POST['purpose'];
        $type = $_POST['type'];
        $topographic = $_POST['topographic'];
        $distance = $_POST['distance'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $note = isset($_POST['note']) ? $_POST['note'] : '';

        // check is empty
        if (empty($start) || empty($end) || empty($column) || empty($purpose) || empty($type) || empty($topographic) || empty($distance) || empty($latitude) || empty($longitude))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }

        // check if start and end are the same
        if ($start == $end)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Địa điểm đi và đến phải khác nhau',
            ));
            return;
        }

        // check if start and end are valid
        $result = $this->db->select('route', array('start' => $start, 'end' => $end, 'column' => $column));
        if (count($result) > 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Địa điểm đi và đến của cột đã tồn tại',
            ));
            return;
        }

        // check if distance is valid
        if (!is_numeric($distance))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Khoảng cách phải là số',
            ));
            return;
        }
        if ($distance <= 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Khoảng cách phải lớn hơn 0',
            ));
            return;
        }

        // check if latitude and longitude are valid
        if (!is_numeric($latitude) || !is_numeric($longitude))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vĩ độ và kinh độ phải là số',
            ));
            return;
        }
        // convert latitude and longitude to float
        $latitude = floatval($latitude);
        $longitude = floatval($longitude);

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vĩ độ và kinh độ phải nằm trong khoảng [-90, 90] và [-180, 180]',
            ));
            return;
        }

        $author = $_SESSION['Member_ID'];

        // add to database
        $result = $this->db->insert('route', array(
            'start' => $start,
            'end' => $end,
            'column' => $column,
            'purpose' => $purpose,
            'type' => $type,
            'topographic' => $topographic,
            'distance' => $distance,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'note' => $note,
            'author' => $author,
        ));

        if ($result)
        {
            echo json_encode(array(
                'status' => true,
                'message' => 'Thêm tuyến đường thành công',
                'data' => array(
                    'id' => $result,
                )
            ));
        }
        else
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thêm tuyến đường thất bại',
            ));
        }
    }

    public function API_DeleteRoute(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập',
            ));
            return;
        } 
        if (!$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }

        // check if all fields are filled: id
        if (!isset($_POST['id']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }
        // assign to variable
        $id = $_POST['id'];
        // check if id is valid
        if (!is_numeric($id))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thông tin không hợp lệ',
            ));
            return;
        }
        if ($id <= 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thông tin không hợp lệ',
            ));
            return;
        }
        // check if id is exist
        $result = $this->db->select('route', array('id' => $id));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Tuyến đường không tồn tại',
            ));
            return;
        }

        // delete images of this route
        $result = $this->db->select('image', array('route_id' => $id));
        foreach ($result as $image)
        {
            // remove images in folder upload
            $path = './Assets/Images/Uploads/' . $image['url'];
        }
        // delete from database
        $result = $this->db->delete('image', array('route_id' => $id));

        // delete from database
        $result = $this->db->delete('route', array('id' => $id));
        if ($result)
        {
            echo json_encode(array(
                'status' => true,
                'message' => 'Xóa tuyến đường thành công',
            ));
        }
        else
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Xóa tuyến đường thất bại',
            ));
        }
    }

    public function API_GetRouteByID(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập',
            ));
            return;
        } 
        if (!$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }

        // check if all fields are filled: id
        if (!isset($_POST['id']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }
        // assign to variable
        $id = $_POST['id'];
        // check if id is valid
        if (!is_numeric($id))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thông tin không hợp lệ',
            ));
            return;
        }
        if ($id <= 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thông tin không hợp lệ',
            ));
            return;
        }
        // check if id is exist
        $result = $this->db->select('route', array('id' => $id));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Tuyến đường không tồn tại',
            ));
            return;
        }

        // get images from database
        $images = $this->db->select('image', array('route_id' => $id));
        // merge images and route to one array
        $result = array_merge($result[0], array('images' => $images));

        // return data
        echo json_encode(array(
            'status' => true,
            'message' => 'Lấy thông tin tuyến đường thành công',
            'data' => $result,
        ));
    }

    public function API_UpdateRoute(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập',
            ));
            return;
        } 
        if (!$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }

        // check if all fields are filled: id, column, start, end, purpose, type, topographic, distance, latitude, longitude
        if (!isset($_POST['id']) || !isset($_POST['column']) || !isset($_POST['start']) || !isset($_POST['end']) || !isset($_POST['purpose']) || !isset($_POST['type']) || !isset($_POST['topographic']) || !isset($_POST['distance']) || !isset($_POST['latitude']) || !isset($_POST['longitude']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }
        // check is empty
        if (empty($_POST['id']) || empty($_POST['column']) || empty($_POST['start']) || empty($_POST['end']) || empty($_POST['purpose']) || empty($_POST['type']) || empty($_POST['topographic']) || empty($_POST['distance']) || empty($_POST['latitude']) || empty($_POST['longitude']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }

        // assign to variable
        $id = $_POST['id'];
        $column = $_POST['column'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $purpose = $_POST['purpose'];
        $type = $_POST['type'];
        $topographic = $_POST['topographic'];
        $distance = $_POST['distance'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $note = isset($_POST['note']) ? $_POST['note'] : '';
        // check if id is valid
        if (!is_numeric($id))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thông tin không hợp lệ',
            ));
            return;
        }

        // check if distance is valid
        if (!is_numeric($distance))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Khoảng cách không hợp lệ',
            ));
            return;
        }
        // check if latitude is valid
        if (!is_numeric($latitude))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vĩ độ không hợp lệ',
            ));
            return;
        }
        // check if longitude is valid
        if (!is_numeric($longitude))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Kinh độ không hợp lệ',
            ));
            return;
        }

        // check if id is exist
        $result = $this->db->select('route', array('id' => $id));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Tuyến đường không tồn tại',
            ));
            return;
        }
        // check if column is exist
        $result = $this->db->select('route', array('column' => $column, 'id!=' => $id, 'start' => $start, 'end' => $end));
        if (count($result) > 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Tuyến đường này đã tồn tại',
            ));
            return;
        }

        // get list of images if exist
        $images = isset($_FILES['images']) ? $_FILES['images'] : array();
        $images_path = array();

        if (count($images) > 0)
        {
            $images_path = $this->uploadImages($images);
        }
        
        // update route
        $this->db->update('route', array(
            'column' => $column,
            'start' => $start,
            'end' => $end,
            'purpose' => $purpose,
            'type' => $type,
            'topographic' => $topographic,
            'distance' => $distance,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'note' => $note,
        ), array('id' => $id));

        // delete old images
        if (count($images_path) > 0)
        {
            $result = $this->db->select('image', array('route_id' => $id));
            foreach ($result as $item)
            {
                if (file_exists('./Assets/Images/Uploads/' . $item['url']))
                {
                    unlink('./Assets/Images/Uploads/' . $item['url']);
                }
            }

            $this->db->delete('image', array('route_id' => $id));

            foreach ($images_path as $image_path)
            {
                $this->db->insert('image', array(
                    'route_id' => $id,
                    'url' => $image_path,
                ));
            }
        }

        echo json_encode(array(
            'status' => true,
            'message' => 'Cập nhật thành công',
        ));
    }

    public function API_LoadRouteByLocation(): void
    {
        // check if user is logged in
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }
        // check if post is empty
        if (empty($_POST['location']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }
        // assign to variable
        $Location = $_POST['location'];
        $Location = explode('-', $Location);
        $Location = array_map('trim', $Location);

        // check if Location is exist
        $result = $this->db->select('route', array('start' => $Location[0], 'end' => $Location[1]));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Đường dây không tồn tại',
            ));
            return;
        }
        // get station of route
        $station = $this->db->select('station', array('route' => $Location[0] . ' - ' . $Location[1]));

        if (count($station) > 0)
        {
            $station = array(
                'start' => array(
                        'name' => $Location[0],
                        'station_name' => $station[0]['start'],
                    ),
                'end' => array(
                        'name' => $Location[1],
                        'station_name' => $station[0]['end'],
                    )
            );
        }
        else {
            $station = array(
                'start' => array(
                        'name' => $Location[0],
                        'station_name' => '',
                    ),
                'end' => array(
                        'name' => $Location[1],
                        'station_name' => '',
                    )
            );
        }
        
        echo json_encode(array(
            'status' => true,
            'message' => 'Đã tìm thấy danh sách đường dây',
            'data' => array(
                'station' => $station,
                'route' => $result,
            ),
        ));

    }

    public function API_LoadStation(): void
    {
        // check if user is logged in
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }
        // check if post is empty
        if (empty($_POST['route']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }
        // assign to variable
        $route = $_POST['route'];
        $route = explode('-', $route);
        $route = array_map('trim', $route);
        
        // check if Location is exist
        $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Tuyến đường dây không tồn tại',
            ));
            return;
        }

        $result = $this->db->select('station', array('route' => $route[0] . ' - ' . $route[1]));

        $station = array(
            'start' => array(
                    'name' => $route[0],
                    'station_name' => ''
                ),
            'end' => array(
                    'name' => $route[1],
                    'station_name' => ''
                )
        );

        if (count($result) > 0)
        {
            $station['start']['station_name'] = $result[0]['start'];
            $station['end']['station_name'] = $result[0]['end'];
        }

        echo json_encode(array(
            'status' => true,
            'data' => $station,
        ));
    }

    public function API_UpdateStation(): void
    {
        // check if user is logged in
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }
        // check if user is admin
        if (!$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }
        // check if post is empty
        if (empty($_POST['start']) || empty($_POST['end']) || empty($_POST['route']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }

        // assign to variable
        $start = $_POST['start'];
        $end = $_POST['end'];
        $route = $_POST['route'];

        // split route
        $route = explode('-', $route);
        $route = array_map('trim', $route);

        // check if Location is exist
        $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Đường dây không tồn tại',
            ));
            return;
        }

        // check if station is exist
        $result = $this->db->select('station', array('route' => $route[0] . ' - ' . $route[1]));

        if (count($result) == 0)
        {
            // insert station
            $this->db->insert('station', array(
                'route' => $route[0] . ' - ' . $route[1],
                'start' => $start,
                'end' => $end,
            ));
            echo json_encode(array(
                'status' => true,
                'message' => 'Đã thêm đường dây',
            ));
            return;
        }

        // update station
        $this->db->update('station', array(
            'start' => $start,
            'end' => $end,
        ), array('route' => $route[0] . ' - ' . $route[1]));

        echo json_encode(array(
            'status' => true,
            'message' => 'Đã cập nhật đường dây',
        ));
    }

    public function API_Search(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập',
            ));
            return;
        }  
        
        $route = isset($_POST['route']) ? $_POST['route'] : '';
        $station = isset($_POST['station']) ? $_POST['station'] : '';
        $distance = isset($_POST['distance']) ? $_POST['distance'] : 0;

        if ($route == '')
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng chọn địa điểm',
            ));
            return;
        }

        $route = explode('-', $route);
        $route = array_map('trim', $route);

        $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không tìm thấy tuyến đường',
            ));
            return;
        }

        // check if distance is number and >= 0
        if (!is_numeric($distance) || $distance < 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vị trí sự cố không hợp lệ',
            ));
            return;
        }

        $order = true;

        if ($station != '')
        {
            // check if station is exist
            $result = $this->db->select('station',"`route` = '$route[0] - $route[1]' AND (`start` = '$station' OR `end` = '$station')");
            if (count($result) == 0)
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Không tìm thấy trạm',
                ));
                return;
            }

            if ($station == $result[0]['end'])
            {
                $order = false;
            }
        }

        $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]), "`id` " . ($order ? 'ASC' : 'DESC'));
        $total = 0;
        $column = $result[0]['column'];
        $num = 0;
        
        foreach ($result as $key => $value)
        {
            if ($total + $value['distance'] > $distance)
            {
                break;
            }
            $total += $value['distance'];
            $column = $value['column'];
            $num = $key;
        }

        if ($num != 0 && count($result) > $num + 1)
        {
            if (abs($total - $distance) > abs($distance - ($result[$num + 1]['distance'] + $total)))
            {
                $num = $num + 1;
                $column = $result[$num]['column'];
                $total += $result[$num]['distance'];
            }
        }
        else {
            $total = $result[$num]['distance'];
        }

        $route = $result[$num];

        // get images of route
        $images = $this->db->select('image', array('route_id' => $route['id']));
        $images = array_column($images, 'url');

        $result = array(
            'route' => $route['start'] . ' - ' . $route['end'],
            'distance' => $total,
            'column' => $route['column'],
            'purpose' => $route['purpose'],
            'type' => $route['type'],
            'topographic' => $route['topographic'],
            'location' => $this->getURLGoogleMap($route['latitude'], $route['longitude']),
            'note' => $route['note'],
            'images' => $images,
        );

        echo json_encode(array(
            'status' => true,
            'message' => 'Đã tìm thấy tuyến đường',
            'data' => $result,
        ));
    }

    public function API_Search_T(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập',
            ));
            return;
        }  
        
        $branch = isset($_POST['branch']) ? $_POST['branch'] : '';
        $distance = isset($_POST['distance']) ? $_POST['distance'] : 0;
        $station = isset($_POST['station']) ? $_POST['station'] : '';

        if ($branch == '' || !is_array($branch))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng chọn tuyến đường dây',
            ));
            return;
        }

        // Check if branch is exist
        foreach ($branch as $key => $value)
        {
            if ($value['route'] == '')
            {
                continue;
            }
            $route = explode('-', $value['route']);
            $route = array_map('trim', $route);

            $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]));
            if (count($result) == 0)
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Không tìm thấy tuyến đường dây: ' . $route[0] . ' - ' . $route[1],
                ));
                return;
            }
        }

        if ($station == '') {
            $station == explode('-', $branch[0]['route'])[0];
        } else {
            $route = explode('-', $branch[0]['route']);
            $route = array_map('trim', $route);
            $result = $this->db->select('station', array('route' => $route[0] . ' - ' . $route[1]));
            $station = $result[0]['start'] == $station ? $route[0] : $route[1];
        }

        $result = array();

        // Get all route
        foreach ($branch as $key => $value)
        {
            if ($value['route'] == '')
            {
                continue;
            }
            $route = explode('-', $value['route']);
            $route = array_map('trim', $route);

            $order = $station == $route[0] ? true : false;

            $data = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]), "`id` " . ($order ? 'ASC' : 'DESC'));
            $total = 0;
            $column = $data[0]['column'];
            $num = 0;
            
            foreach ($data as $key2 => $value2)
            {
                if ($total + $value2['distance'] > $distance)
                {
                    break;
                }
                $total += $value2['distance'];
                $column = $value2['column'];
                $num = $key2;
            }
    
            if ($num != 0 && count($data) > $num + 1)
            {
                if (abs($total - $distance) > abs($distance - ($data[$num + 1]['distance'] + $total)))
                {
                    $num = $num + 1;
                    $column = $data[$num]['column'];
                    $total += $data[$num]['distance'];
                }
            }
            else {
                $total = $data[$num]['distance'];
            }
    
            $route = $data[$num];
    
            // get images of route
            $images = $this->db->select('image', array('route_id' => $route['id']));
            $images = array_column($images, 'url');
    
            $data = array(
                'branch_id' => $key,
                'route' => $route['start'] . ' - ' . $route['end'],
                'distance' => "$total",
                'column' => $route['column'],
                'purpose' => $route['purpose'],
                'type' => $route['type'],
                'topographic' => $route['topographic'],
                'location' => $this->getURLGoogleMap($route['latitude'], $route['longitude']),
                'note' => $route['note'],
                'images' => $images,
            );

            array_push($result, $data);
        }

        echo json_encode(array(
            'status' => true,
            'message' => 'Đã tìm thấy tuyến đường',
            'data' => $result,
        ));
    }

    public function API_GetColumnRoute(): void
    {
        // check if user is logged in
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }
        // check if post is empty
        if (empty($_POST['route']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }
        // assign to variable
        $route = $_POST['route'];
        $route = explode('-', $route);
        $route = array_map('trim', $route);
        // check if Location is exist
        $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Đường dây không tồn tại',
            ));
            return;
        }
        // get column route
        $column = array_column($result, 'column');
        echo json_encode(array(
            'status' => true,
            'data' => $column,
        ));
    }

    public function API_GetDetailColumn(): void
    {
        // check if user is logged in
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }
        // check if post is empty
        if (empty($_POST['route']) || empty($_POST['column']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }
        // assign to variable
        $route = $_POST['route'];
        $column = $_POST['column'];
        $route = explode('-', $route);
        $route = array_map('trim', $route);
        // check if Location is exist
        $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Đường dây không tồn tại',
            ));
            return;
        }

        // check if column is exist
        $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1], 'column' => $column));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Số cột không tồn tại',
            ));
            return;
        }

        $data = array();
        foreach ($result as $key => $value)
        {
            $data[$key]['route'] = $value['start'] . ' - ' . $value['end'];
            $data[$key]['column'] = $value['column'];
            $data[$key]['purpose'] = $value['purpose'];
            $data[$key]['type'] = $value['type'];
            $data[$key]['topographic'] = $value['topographic'];
            $data[$key]['distance'] = $value['distance'];
            $data[$key]['note'] = $value['note'];
            $data[$key]['location'] = $this->getURLGoogleMap($value['latitude'], $value['longitude']);

            // get images of route
            $images = $this->db->select('image', array('route_id' => $value['id']));
            $images = array_column($images, 'url');
            $data[$key]['images'] = $images;

        }

        echo json_encode(array(
            'status' => true,
            'message' => 'Đã lấy thông tin của cột',
            'data' => $data[0],
        ));
    
    }

    public function API_LoadBranch()
    {
        // check if user is logged in
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }
        if ($this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }

        $route = isset($_POST['route']) ? $_POST['route'] : '';
        $station = isset($_POST['station']) ? $_POST['station'] : '';
        // check if post is empty
        if (empty($_POST['route']) || empty($_POST['station']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }
        // assign to variable
        $route = explode('-', $route);
        $route = array_map('trim', $route);
        // check if Location is exist
        $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]));
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Đường dây không tồn tại',
            ));
            return;
        }
        // check if station is exist
        $result = $this->db->select('station', "`route` = '".$route[0]." - ".$route[1]."' AND (`start` = '$station' OR `end` = '$station')");
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Trạm không tồn tại',
            ));
            return;
        }

        $station = $result[0]['start'] == $station ? $route[0] : $route[1];
        $start = $route[0];
        $end = $route[1];

        $sql = "SELECT * FROM `route` WHERE (`start` = '$station' OR `end` = '$station') AND NOT (`start` = '$start' AND `end` = '$end') GROUP BY `end` ORDER BY `id` ASC";

        $result = $this->db->query($sql);
        $result = $this->db->getResultArray($result);
        $data = array();
        if (count($result) == 0) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không có tuyến đường nào',
            ));
            return;
        }
        foreach ($result as $key => $value)
        {
            $data[$key]['route']= $value['start'] . ' - ' . $value['end'];
        }

        echo json_encode(array(
            'status' => true,
            'message' => 'Đã lấy thông tin của cột',
            'data' => $data,
        ));
    }

    public function API_Report()
    {
        // check if user is logged in and is branch
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }

        if (!$this->isBranch() && !$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }

        $content = isset($_POST['content']) ? $_POST['content'] : '';
        $note = isset($_POST['note']) ? $_POST['note'] : '';

        // get list of images if exist
        $images = isset($_FILES['images']) ? $_FILES['images'] : array();
        $images_path = array();

        if (count($images) > 0)
        {
            $images_path = $this->uploadImages($images, 'Report');
        }

        // check if post is empty
        if (empty($content))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }

        $author = $_SESSION['Member_ID'];

        // insert to database
        $data = array(
            'content' => $content,
            'note' => $note,
            'author' => $author,
        );

        $result = $this->db->insert('note', $data);

        if ($result)
        {
            // get id of report
            $report_id = $result;

            // insert images to database
            foreach ($images_path as $key => $value)
            {
                $data = array(
                    'note_id' => $report_id,
                    'url' => $value,
                );
                $this->db->insert('image_note', $data);
            }

            echo json_encode(array(
                'status' => true,
                'message' => 'Đã gửi báo cáo',
            ));
        }
        else
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Có lỗi xảy ra',
            ));
        }
    }

    public function API_GetDetailReportByID()
    {
        // check if user is logged in and is branch
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }

        if (!$this->isBranch() && !$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }

        $id = isset($_POST['id']) ? $_POST['id'] : '';

        // check if post is empty
        if (empty($id))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }

        $result = $this->getDetailReportByID($id);
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Báo cáo không tồn tại',
            ));
            return;
        }

        echo json_encode(array(
            'status' => true,
            'data' => $result,
        ));
    }

    public function API_GetReportByBranchID()
    {
        // check if user is logged in and is branch
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }

        if (!$this->isBranch() && !$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }

        $branch_id = isset($_POST['branch']) ? $_POST['branch'] : '';

        // check if post is empty
        if (empty($branch_id))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }

        $result = $this->getNewReportByBranchID($branch_id);
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không có báo cáo nào',
            ));
            return;
        }

        echo json_encode(array(
            'status' => true,
            'data' => $result,
        ));
    }

    public function API_GetReportByBranchPaging()
    {
        // check if user is logged in and is branch
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập',
            ));
            return;
        }

        if (!$this->isBranch() && !$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này',
            ));
            return;
        }

        $branch_id = isset($_POST['branch']) ? $_POST['branch'] : '';
        $page = isset($_POST['page']) ? $_POST['page'] : 1;

        // check if post is empty
        if (empty($branch_id))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
            ));
            return;
        }

        $result = $this->getReportByBranchPaging($branch_id, $page);
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không có báo cáo nào',
            ));
            return;
        }

        echo json_encode(array(
            'status' => true,
            'data' => $result,
        ));
    }

    public function API_DeleteReport()
    {
        $result = $this->db->select("note", "`created_at` < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        if (count($result) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không có báo cáo nào',
            ));
            return;
        }

        foreach ($result as $value)
        {
            $image = $this->db->select("image_note", "`note_id` = " . $value['id']);
            if (count($image) > 0)
            {
                foreach ($image as $value2)
                {
                    unlink('./Assets/Images/Report/' . $value2['url']);
                    $this->db->delete('image_note', array('id' => $value2['id']));
                }
            }
            $this->db->delete('note', array('id' => $value['id']));
        }

        echo json_encode(array(
            'status' => true,
            'message' => 'Đã xóa ' . count($result) . ' báo cáo',
        ));
    }

    public function getListBranchUser()
    {
        $result = $this->db->select('member', "`role` = '2'");
        return $result;
    }

    public function getListRoute(): array
    {
        $result = $this->db->select('route');
        return $result;
    }

    public function getListRouteByStation($station): array
    {
        // split station and remove space
        $station = explode('-', $station);
        $station = array_map('trim', $station);

        $result = $this->db->select('route', array('start' => $station[0], 'end' => $station[1]));
        return $result;
    }

    public function getListColumn($route): array
    {
        // split route and remove space
        $route = explode('-', $route);
        $route = array_map('trim', $route);

        $result = $this->db->select('route', array('start' => $route[0], 'end' => $route[1]));

        if (count($result) == 0)
        {
            return array();
        }
        return array_column($result, 'column');
    }   

    public function getDetailReportByBranch($branch_id): array
    {
        $result = $this->db->select('note', array('author' => $branch_id), '`created_at` DESC');
        return $result;
    }

    public function getNewReportByBranchID($branch_id): array
    {
        $result = $this->db->select('note', array('author' => $branch_id), '`created_at` DESC');
        $count = count($result);
        $result = array_slice($result, 0, 20);
        $data = [];
        foreach ($result as $value) {
            $data[] = array(
                'id' => $value['id'],
                'content' => $value['content'],
                'time' => $this->TimeGapToNow($value['created_at']),
            );
        }
        return [
            'report' => $data,
            'count' => $count,
        ];
    }

    public function getReportByBranchPaging($branch_id, $page): array
    {
        $result = $this->db->select('note', array('author' => $branch_id), '`created_at` DESC');
        $count = count($result);
        $result = array_slice($result, ($page - 1) * 20, 20);
        $data = [];
        foreach ($result as $value) {
            $data[] = array(
                'id' => $value['id'],
                'content' => $value['content'],
                'time' => $this->TimeGapToNow($value['created_at']),
            );
        }
        return [
            'report' => $data,
            'count' => $count,
        ];
    }

    public function getDetailReportByID($id): array
    {
        $result = $this->db->select('note', array('id' => $id));
        if (count($result) == 0)
        {
            return array();
        }
        $images = $this->db->select('image_note', array('note_id' => $id));
        $result[0]['images'] = $images;
        return $result[0];
    }

    public function getNewReport(): array
    {
        $result = $this->db->select('note', null, '`created_at` DESC', 30);
        $data = [];
        foreach ($result as $value) {
            // get name author
            $author = $this->db->select('member', array('id' => $value['author']));
            $data[] = array(
                'id' => $value['id'],
                'content' => $value['content'],
                'time' => $this->TimeGapToNow($value['created_at']),
                'author' => $author[0]['name']
            );
        }
        return $data;
    }
}