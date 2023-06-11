$(document).ready(function () {
    var table = $('#table-list-account').DataTable();

    $('.register').click(function (e) { 
        e.preventDefault();
        
        $('.cancel').css('display', 'block');
        $('.register').css('display', 'none');

        $('.create-account>.form').css('height', '500px');
        $('.create-account>.form').css('animation', 'showForm 0.5s');
    });

    $('.cancel').click(function (e) {
        e.preventDefault();
        $('.cancel').css('display', 'none');
        $('.register').css('display', 'block');

        $('.create-account>.form').css('height', '0');
        $('.create-account>.form').css('animation', 'hideForm 0.5s');
    });

    $('#btn-register-account').click(function (e) { 
        e.preventDefault();
        
        var name = $('#name').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var password = $('#password').val();
        var repassword = $('#repassword').val();
        var username = $('#username').val();

        if(name == '' || email == '' || phone == '' || password == '' || repassword == '' || username == ''){
            swal("Thông báo!", "Vui lòng nhập đầy đủ thông tin", "warning");
            return;
        }

        if(password != repassword){
            swal("Thông báo!", "Mật khẩu không khớp", "warning");
            return;
        }

        $.ajax({
            url: './api/createAccount',
            type: 'POST',
            data: {
                name: name,
                email: email,
                phone: phone,
                password: password,
                username: username
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    swal("Thành công!", data.message, "success").then(function () {
                        // reload
                        location.reload();
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

    $('.btn-delete').click(function (e) {
        let id = $(this).attr('data-id');

        let data = {
            id: id,
        };

        var row = $(this).closest('tr');

        swal({
            title: "Bạn có chắc chắn muốn xóa?",
            text: "Sau khi xóa, bạn sẽ không thể phục hồi dữ liệu này!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((value) => {
            if (value) {
                $.ajax({
                    url: "./api/deleteAccount",
                    type: "POST",
                    data: data,
                    success: function (data) {
                        data = JSON.parse(data);
                        if (data.status == false) {
                            swal("Thông báo", data.message, "warning");
                            return;
                        }
                        swal({
                            title: "Thành công",
                            text: data.message,
                            icon: "success",
                        }).then((value) => {
                            table.row(row).remove().draw();
                        });
                    },
                    error: function (err) {
                        swal("Cảnh báo", "Có lỗi xảy ra", "error");
                    },
                });
            }
        });
    });

    

});