<?php

class MemberController extends HomeController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        
    }

    public function login(): void
    {
        if ($this->isLogin())
        {
            header('Location: ./');
            return;
        }
        include_once './Assets/Pages/login.php';
    }

    public function profile(): void
    {
        if (!$this->isLogin())
        {
            header('Location: ./login');
            return;
        }
        $profile = $this->getProfile();
        $this->loadLayout('Thông tin khách hàng', array(
            '<link rel="stylesheet" href="./Assets/Css/client/profile.css">',
            '<script src="./Assets/Js/client/profile.js"></script>',
        ), 1);
        include_once './Assets/Pages/client/profile.php';
    }

    public function branch_manager(): void
    {
        if (!$this->isLogin())
        {
            header('Location: ./login');
            return;
        }
        $route = new RouteController();
        $listReport = $route->getNewReport();
        $listBranch = $route->getListBranchUser();
        $listReportBranch = [];
        $paging  = 1;
        if (count($listBranch) != 0)
        {
            $newReportBranch = $route->getNewReportByBranchID($listBranch[0]['id']);
            $listReportBranch = $newReportBranch['report'];
            $count = $newReportBranch['count'];
        }

        $this->loadLayout('Quản lý chi nhánh', array(
            '<link rel="stylesheet" href="./Assets/Css/admin/branch.css">',
            '<script src="./Assets/Js/admin/branch.js"></script>',
        ), 4, 1);
        include_once './Assets/Pages/Admin/branch_manager.php';        
    }

    public function branch_account(): void
    {
        if (!$this->isLogin())
        {
            header('Location: ./login');
            return;
        }

        $accounts = $this->getListAccountBranch();

        $this->loadLayout('Danh sách chi nhánh', array(
            '<link rel="stylesheet" href="./Assets/Css/admin/account.css">',
            '<script src="./Assets/Js/admin/branch_account.js"></script>',
        ), 4, 2);
        include_once './Assets/Pages/Admin/branch_account.php';        
    }

    public function API_Login(): void
    {
        if ($this->isLogin()){
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn đã đăng nhập'
            ));
            return;
        }
        // check username and password is isset
        if (!isset($_POST['username']) || !isset($_POST['password'])) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thiếu tài khoản hoặc mật khẩu'
            ));
            return;
        }

        $username = $_POST['username'];
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            echo json_encode(array('status' => false, 'message' => 'Tài khoản hoặc mật khẩu không được để trống'));
            return;
        }

        $password = md5($password);
        $result = $this->db->select('member', array('username' => $username, 'password' => $password));
        if (count($result) == 0) {
            echo json_encode(array('status' => false, 'message' => 'Tài khoản hoặc mật khẩu không đúng'));
            return;
        }
        $_SESSION['Member_ID'] = $result[0]['id'];
        echo json_encode(array('status' => true, 'message' => 'Đăng nhập thành công'));
        return;
    }

    public function getProfile(): array
    {
        if (!$this->isLogin())
        {
            return array();
        }
        $result = $this->db->select('member', array('id' => $_SESSION['Member_ID']));
        return $result[0];
    }

    public function accountAdmin(): void
    {
        if (!$this->isLogin() || !$this->isAdmin())
        {
            header('Location: ./login');
            return;
        }
        $accounts = $this->getListAccount();

        $this->loadLayout('Quản lý tài khoản', array(
            '<link rel="stylesheet" href="./Assets/Css/admin/account.css">',
            '<script src="./Assets/Js/admin/account.js"></script>',
        ), 2);
        include_once './Assets/Pages/admin/account.php';
    }

    public function getListAccount(): array
    {
        if (!$this->isLogin() || !$this->isAdmin())
        {
            return array();
        }
        $result = $this->db->select('member', array('role' => 0));
        return $result;
    }

    public function getListAccountBranch(): array
    {
        if (!$this->isLogin() || !$this->isAdmin())
        {
            return array();
        }
        $result = $this->db->select('member', array('role' => 2));
        foreach ($result as $key => $value) {
            $note = $this->db->select('note', array('author' => $value['id']));
            $result[$key]['report'] = count($note);
        }
        return $result;
    }

    public function API_ChangeProfile(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập'
            ));
            return;
        }
              
        if (!isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['email'])) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thiếu thông tin'
            ));
            return;
        }

        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];

        if (empty($name) || empty($phone) || empty($email)) {
            echo json_encode(array('status' => false, 'message' => 'Thông tin không được để trống'));
            return;
        }

        // check email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array('status' => false, 'message' => 'Email không hợp lệ'));
            return;
        }

        // check phone is valid
        if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            echo json_encode(array('status' => false, 'message' => 'Số điện thoại không hợp lệ'));
            return;
        }

        $result = $this->db->update('member', array(
            'name' => $name,
            'phone' => $phone,
            'email' => $email
        ), array('id' => $_SESSION['Member_ID']));

        if ($result) {
            echo json_encode(array('status' => true, 'message' => 'Thay đổi thông tin thành công'));
            return;
        } else {
            echo json_encode(array('status' => false, 'message' => 'Thay đổi thông tin thất bại'));
            return;
        }
    }

    public function API_ChangePassword(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập'
            ));
            return;
        }
              
        if (!isset($_POST['old_password']) || !isset($_POST['new_password'])) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thiếu thông tin'
            ));
            return;
        }
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        if (empty($old_password) || empty($new_password)) {
            echo json_encode(array('status' => false, 'message' => 'Thông tin không được để trống'));
            return;
        }
        $old_password = md5($old_password);
        $new_password = md5($new_password);
        $result = $this->db->select('member', array('id' => $_SESSION['Member_ID'], 'password' => $old_password));
        if (count($result) == 0) {
            echo json_encode(array('status' => false, 'message' => 'Mật khẩu cũ không đúng'));
            return;
        }
        $result = $this->db->update('member', array('password' => $new_password), array('id' => $_SESSION['Member_ID']));
        if ($result) {
            echo json_encode(array('status' => true, 'message' => 'Thay đổi mật khẩu thành công'));
            return;
        } else {
            echo json_encode(array('status' => false, 'message' => 'Thay đổi mật khẩu thất bại'));
            return;
        }
    }

    public function API_CreateAccount(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập'
            ));
            return;
        }
        if (!$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện chức năng này'
            ));
            return;
        }
              
        if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['email'])) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thiếu thông tin'
            ));
            return;
        }

        $username = $_POST['username'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $role = $_POST['role'] ?? 0;

        if (empty($username) || empty($password) || empty($name) || empty($phone) || empty($email)) {
            echo json_encode(array('status' => false, 'message' => 'Thông tin không được để trống'));
            return;
        }
        // check email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array('status' => false, 'message' => 'Email không hợp lệ'));
            return;
        }
        // check phone is valid
        if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            echo json_encode(array('status' => false, 'message' => 'Số điện thoại không hợp lệ'));
            return;
        }

        // check if role is valid
        if (!in_array($role, [0, 1, 2])) {
            echo json_encode(array('status' => false, 'message' => 'Quyền không hợp lệ'));
            return;
        }
        $result = $this->db->select('member', array('username' => $username));
        if (count($result) > 0) {
            echo json_encode(array('status' => false, 'message' => 'Tài khoản đã tồn tại'));
            return;
        }
        $result = $this->db->insert('member', array(
            'username' => $username,
            'password' => md5($password),
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'role' => $role
        ));
        if ($result) {
            echo json_encode(array('status' => true, 'message' => 'Tạo tài khoản thành công'));
            return;
        } else {
            echo json_encode(array('status' => false, 'message' => 'Tạo tài khoản thất bại'));
            return;
        }
    }

    public function API_DeleteAccount(): void
    {
        if (!$this->isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn chưa đăng nhập'
            ));
            return;
        }
        if (!$this->isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện chức năng này'
            ));
            return;
        }
              
        if (!isset($_POST['id'])) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thiếu thông tin'
            ));
            return;
        }
        $id = $_POST['id'];
        // check id is number
        if (!is_numeric($id)) {
            echo json_encode(array('status' => false, 'message' => 'ID không hợp lệ'));
            return;
        }

        // check id is exist
        $result = $this->db->select('member', array('id' => $id));
        if (count($result) == 0) {
            echo json_encode(array('status' => false, 'message' => 'Không tìm thấy tài khoản'));
            return;
        }
        if ($result[0]['role'] == 2) {
            $notes = $this->db->select('note', array('author' => $id));
            if (count($notes) > 0) {
                foreach ($notes as $note) {
                    $images = $this->db->select('image_note', array('note_id' => $note['id']));
                    if (count($images) > 0) {
                        foreach ($images as $image) {
                            unlink('./Assets/Images/Report/' . $image['url']);
                            $this->db->delete('image_note', array('id' => $image['id']));
                        }
                    }
                    $this->db->delete('note', array('id' => $note['id']));
                }
            }
        }

        $result = $this->db->delete('member', array('id' => $id));

        if ($result) {
            echo json_encode(array('status' => true, 'message' => 'Xóa tài khoản thành công'));
            return;
        } else {
            echo json_encode(array('status' => false, 'message' => 'Xóa tài khoản thất bại'));
            return;
        }
    }
}