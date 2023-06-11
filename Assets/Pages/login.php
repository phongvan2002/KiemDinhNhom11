<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | <?= SITE_NAME ?></title>
    <base href="<?= SITE_URL ?>">

    <link rel="shortcut icon" href="./Assets/Images/logo.jpg" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800" rel="stylesheet">

    <!-- jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <!-- SweetAlert CDN -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- DataTables jQuery CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <!-- IonIcons CDN -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <link rel="stylesheet" href="./Assets/Css/login.css">
    <script src="./Assets/Js/login.js"></script>
</head>

<body>
    <div class="container">
        <div class="title">Đăng nhập</div>
        <div class="row">
            <input type="text" name="username" id="username" class="form-control" placeholder="Tài khoản">
        </div>
        <div class="row">
            <input type="password" name="password" id="password" class="form-control" placeholder="Mật khẩu">
        </div>
        <div class="row">
            <button id="btn-login">Đăng nhập</button>
        </div>
    </div>
</body>

</html>