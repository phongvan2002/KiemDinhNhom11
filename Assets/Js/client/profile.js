$(document).ready(function () {
    $('#btn-change-pass').click(function (e) { 
        e.preventDefault();
        
        let old_password = $('#old_pass').val();
        let new_password = $('#new_pass').val();
        let confirm_password = $('#confirm_pass').val();

        if (old_password == '' || new_password == '' || confirm_password == '') {
            swal("Thông báo!", "Mật khẩu không được để trống", "warning");
            return;
        }

        if (new_password != confirm_password) {
            swal("Thông báo!", "Mật khẩu mới và xác nhận mật khẩu không khớp", "warning");
            return;
        }

        $.ajax({
            url: './api/password',
            type: 'POST',
            data: {
                old_password: old_password,
                new_password: new_password
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    swal("Thành công!", data.message, "success").then(function () {
                        // clear data
                        $('#old_pass').val('');
                        $('#new_pass').val('');
                        $('#confirm_pass').val('');
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

    $('#btn-change-profile').click(function (e) {
        e.preventDefault();

        let name = $('#name').val();
        let email = $('#email').val();
        let phone = $('#phone').val();

        $.ajax({
            url: './api/profile',
            type: 'POST',
            data: {
                name: name,
                email: email,
                phone: phone
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    swal("Thành công!", data.message, "success").then(function () {
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