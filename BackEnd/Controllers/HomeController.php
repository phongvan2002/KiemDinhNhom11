<?php

class HomeController
{
    public $db;

    public function __construct()
    {
        $this->db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $this->db->dbConnection();
    }

    public function index(): void
    {
        if (!$this->isLogin())
        {
            header('Location: ./login');
            return;
        }
        if ($this->isAdmin())
        {
            header('Location: ./admin');
            return;
        }
        if ($this->isBranch())
        {
            header('Location: ./report');
            return;
        }

        $listLocation = $this->getListLocation();
        $station = array();
        if (!empty($listLocation))
        {
            $station = $this->getStationName($listLocation[0]);
        }

        $this->loadLayout('Vị trí sự cố', array(
            '<link rel="stylesheet" href="./Assets/Css/client/index.css">',
            '<script src="./Assets/Js/client/index.js"></script>'
        ));
        include_once './Assets/Pages/client/index.php';
    }

    public function contact(): void
    {
        $this->loadLayout('Liên hệ', array(
            '<link rel="stylesheet" href="./Assets/Css/client/contact.css">',
        ), 2);
        include_once './Assets/Pages/client/contact.php';
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ./login');
    }

    public function isLogin(): bool
    {
        if (isset($_SESSION['Member_ID'])) {
            // check id in database
            $result = $this->db->select('member', array('id' => $_SESSION['Member_ID']));
            if (count($result) > 0) {
                return true;
            }
            header("Location: ./logout");
        }
        return false;
    }

    public function isClient(): bool
    {
        if (!$this->isLogin())
        {
            return false;
        }
        $result = $this->db->select('member', array('id' => $_SESSION['Member_ID']));
        if ($result[0]['role'] == 0) {
            return true;
        }
        return false;
    }

    public function isAdmin(): bool
    {
        if (!$this->isLogin())
        {
            return false;
        }
        $result = $this->db->select('member', array('id' => $_SESSION['Member_ID']));
        if ($result[0]['role'] == 1) {
            return true;
        }
        return false;
    }

    public function isBranch(): bool
    {
        if (!$this->isLogin())
        {
            return false;
        }
        $result = $this->db->select('member', array('id' => $_SESSION['Member_ID']));
        if ($result[0]['role'] == 2) {
            return true;
        }
        return false;
    }

    public function getListLocation(): array
    {
        $result = $this->db->query('SELECT `start`, `end` FROM `route` GROUP BY `start`, `end`');
        $result = $this->db->getResultArray($result);
        $list = array();
        // add start and end to list
        for ($i = 0; $i < count($result); $i++)
        {
            $list[] = $result[$i]['start'] . ' - ' . $result[$i]['end'];
        }
        // duplicate filter
        // $list = array_unique($list);
        return $list;
    }

    public function loadLayout($title = '', $assets = array(), $page_number = 0, $sub_number = 0): void
    {
        $links = array();
        if ($this->isAdmin())
        {
            $links = array(
                [
                    'title' => 'Quản lý đường dây',
                    'link' => './admin',
                    'icon' => 'map-outline',
                    'id' => 0
                ],
                [
                    'title' => 'Quản lý tài khoản',
                    'link' => './admin/account',
                    'icon' => 'people-outline',
                    'id' => 2
                ],
                [
                    'title' => 'Quản lý chi nhánh',
                    'link' => '#',
                    'icon' => 'git-branch-outline',
                    'id' => 4,
                    'sub' => [
                        [
                            'title' => 'Báo cáo',
                            'link' => './admin/branch',
                            'icon' => 'add-outline',
                            'id' => 1
                        ],
                        [
                            'title' => 'Danh sách chi nhánh',
                            'link' => './admin/branch/account',
                            'icon' => 'list-outline',
                            'id' => 2
                        ]
                    ]
                ],
                [
                    'title' => 'Thông tin cá nhân',
                    'link' => './profile',
                    'icon' => 'person-outline',
                    'id' => 1
                ],
                [
                    'title' => 'Đăng xuất',
                    'link' => './logout',
                    'icon' => 'log-out-outline',
                    'id' => 3
                ]
            );
        }
        else if ($this->isClient()) {
            $links = array(
                [
                    'title' => 'Vị trí sự cố',
                    'link' => './',
                    'icon' => 'home-outline',
                    'id' => 0
                ],
                [
                    'title' => 'Thông tin đường dây 110kV',
                    'link' => './route',
                    'icon' => 'map-outline',
                    'id' => 4
                ],
                [
                    'title' => 'Vị trí sự cố đường dây T',
                    'link' => './route/t',
                    'icon' => 'navigate-outline',
                    'id' => 5
                ],
                [
                    'title' => 'Thông tin khách hàng',
                    'link' => './profile',
                    'icon' => 'person-outline',
                    'id' => 1
                ],
                [
                    'title' => 'Liên hệ',
                    'link' => './contact',
                    'icon' => 'help-circle-outline',
                    'id' => 2
                ],
                [
                    'title' => 'Đăng xuất',
                    'link' => './logout',
                    'icon' => 'log-out-outline',
                    'id' => 3
                ]
            );
        }
        else {
            $links = array(
                [
                    'title' => 'Báo cáo sự cố',
                    'link' => './report',
                    'icon' => 'bug-outline',
                    'id' => 6
                ],
                [
                    'title' => 'Thông tin khách hàng',
                    'link' => './profile',
                    'icon' => 'person-outline',
                    'id' => 1
                ],
                [
                    'title' => 'Liên hệ',
                    'link' => './contact',
                    'icon' => 'help-circle-outline',
                    'id' => 2
                ],
                [
                    'title' => 'Đăng xuất',
                    'link' => './logout',
                    'icon' => 'log-out-outline',
                    'id' => 3
                ]
            );
        }

        include_once './Assets/Pages/client/_header.php';
    }

    public function uploadImages($files, $folder = ''): array
    {
        $images = array();

        foreach ($files['name'] as $key => $image)
        {
            $image_path = $this->uploadImage($image, $files['tmp_name'][$key], $folder);
            if ($image_path)
            {
                $images[] = $image_path;
            }
        }
        return $images;
    }

    public function uploadImage($image_name, $image_tmp_name, $folder = '')
    {
        $image_name = explode('.', $image_name);
        $image_name = md5($image_name[0]) . '.' . $image_name[1];
        $randomString = $this->randomString(5);
        if ($folder != '')
            $image_path = "./Assets/Images/$folder/" . time() . '_' . $randomString . '_' . $image_name;
        else $image_path = './Assets/Images/Uploads/' . time() . '_' . $randomString . '_' . $image_name;

        if (move_uploaded_file($image_tmp_name, $image_path))
        {
            return time() . '_'. $randomString . '_' . $image_name;
        }
        return false;
    }

    public function getURLGoogleMap($latitude, $longitude): string
    {
        $position = $this->convertLatitudeLongitude($latitude, $longitude);
        $position = $position[0] . '+' . $position[1];
        return $position;
    }

    public function convertLatitudeLongitude($longitude, $latitude): array
    {
        // convert DMM to DMS
        $longitude = $this->convertDMMtoStringDMS($longitude) . 'N';
        $latitude = $this->convertDMMtoStringDMS($latitude) .'E';
        // convert to URL
        $longitude = $this->convertStringToURL($longitude);
        $latitude = $this->convertStringToURL($latitude);

        return array($longitude, $latitude);
    }

    public function convertDMMtoStringDMS($DMM): string
    {
        $DMS = "";
        $D = floor($DMM);
        $M = floor(($DMM - $D) * 60);
        $S = (($DMM - $D) * 60 - $M) * 60;
        $S = round($S * 100) / 100;

        $DMS = $D . "°" . $M . "'" . $S . '"';

        return $DMS;
    }

    public function convertStringToURL($str): string
    {
        $str = str_replace('°', '%C2%B0', $str);
        $str = str_replace('"', '%22', $str);
        return $str;
    }

    public function getStationName($location): array
    {
        // split location and remove space
        $location = explode('-', $location);
        $location = array_map('trim', $location);
        
        $result = $this->db->select('station', array('route' => $location[0] . ' - ' . $location[1]));
        if (count($result) == 0)
        {
            return array();
        }
        return $result[0];
    }

    public function TimeGapToNow($time)
    {
        $now = new DateTime();
        $time = new DateTime($time);
        $diff = $now->diff($time);
        $gap = '';
        if ($diff->y > 0)
        {
            $gap .= $diff->y . ' năm ';
        }
        if ($diff->m > 0 && empty($gap))
        {
            $gap .= $diff->m . ' tháng ';
        }
        if ($diff->d > 0 && empty($gap))
        {
            $gap .= $diff->d . ' ngày ';
        }
        if ($diff->h > 0 && empty($gap))
        {
            $gap .= $diff->h . ' giờ ';
        }
        if ($diff->i > 0 && empty($gap))
        {
            $gap .= $diff->i . ' phút ';
        }
        if ($diff->s > 0 && empty($gap))
        {
            $gap .= $diff->s . ' giây ';
        }
        if (empty($gap))
            $gap = 'vừa xong';
        else $gap .= 'trước';
        // Change $time format to d-m-Y H:i:s
        $time = $time->format('d-m-Y H:i:s');
        $gap = $time . ' (' . $gap . ')';
        return $gap;
    }

    public function randomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}