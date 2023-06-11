$(document).ready(function () {
    var listImage = [];
    var changeImage = false;
    var rowSelected = null;

    $(document).on('change', '.select-img-details', function () {
        listImage = $(this).prop('files');
        changeImage = true;

        let img_arr = [];
        for (let i = 0; i < listImage.length; i++) {
            if (listImage[i].type.indexOf('image') > -1) {
                img_arr.push(listImage[i]);
            }
        }
        $('.multi-image-small').html('<label for="image" class="new-image-small">image</label>');

        for (let i = 0; i < img_arr.length; i++) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('.multi-image-small').append(`
                    <img src="${e.target.result}" alt="">
                `);
            }
            reader.readAsDataURL(img_arr[i]);
        }
    });

    $(document).on('click', '.close-modal', function () {
        const modal = $(this).closest('.modal');
        $(modal).addClass('hidden');
    });

    $(document).on('click', '#btn-report', function () {
        // Load image
        const images = $('#image').prop('files');
        const content = $('#content-report').val();
        const note = $('#note-report').val();
        
        var formData = new FormData();
        formData.append('content', content);
        formData.append('note', note);

        for (let i = 0; i < images.length; i++) {
            formData.append('images[]', images[i]);
        }
        
        $.ajax({
            url: './api/report',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                    data = JSON.parse(data);

                    if (data.status) {
                        clearModalReport();
                        $('.modal-report').addClass('hidden');
                        swal("Thành công!", data.message, "success")
                            .then((value) => {
                                location.reload();
                            });
                        return;
                    }

                    swal("Thất bại!", data.message, "warning");
            },
            error: function(data) {
                    swal("Thất bại!", "Đã có lỗi xảy ra", "error");
            }
        });
    });

    $(document).on('click', '#btn-show-modal-report',function () {
        $('.modal-report').removeClass('hidden');
    });

    function clearModalReport() {
        $('#content-report').val('');
        $('#note-report').val('');
        $('.multi-image-small').html('<label for="image" class="new-image-small">image</label>');
        $('#image').val('');
    }

    function clearModalShowReport() {
        $('#content-report').val('');
        $('#note-report').val('');
        $('.multi-image-small-show').html('');
    }

    $(document).on('click', '.content-item', function () {
        const id = $(this).data('id');
        clearModalShowReport();

        $.ajax({
            url: './api/report/detail',
            type: 'POST',
            data: {
                id: id
            },
        success: function(data) {
            data = JSON.parse(data);
            if (data.status) {
                const report = data.data;
                $('#content-show-report').val(report.content);
                $('#note-show-report').val(report.note);

                for (let i = 0; i < report.images.length; i++) {
                    $('.multi-image-small-show').append(`
                        <img src="./Assets/Images/Report/${report.images[i].url}" alt="">
                    `);
                }
                $('.modal-show-report').removeClass('hidden');
                return;
            }
            
            swal("Thất bại!", data.message, "warning");
        },
        error: function(data) {
            swal("Thất bại!", "Đã có lỗi xảy ra", "error");
        }
        });
    });

    $(document).on('click', '.multi-image-small-show>img', function () {
        $('.zoom-image>.modal-body>.modal-content>img').attr('src', $(this).attr('src'));
        $('.zoom-image').removeClass('hidden');
    });
});