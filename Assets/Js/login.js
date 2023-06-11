$(document).ready(function () {
    $(document).on('click', '#btn-login', function () {
        let username = $('#username').val();
        let password = $('#password').val();
        
        if (username == '' || password == '')
        {
            swal("Thông báo!", "Tài khoản hoặc mật khẩu không được để trống", "warning");
            return;
        }

        $.ajax({
            url: './api/login',
            type: 'POST',
            data: {
                username: username,
                password: password
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    swal("Thành công!", "Đăng nhập thành công", "success").then(function () {
                        window.location.href = './';
                    });
                    return;
                }
                swal("Thông báo!", data.message, "warning");
                
            },
            error: function (data) {
                swal("Thông báo!", "Đã có lỗi xảy ra", "error");
            }
        });
    });
});