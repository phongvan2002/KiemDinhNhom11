
$(document).ready(function () {
    var listImage = [];

    $(document).on('change', '.select-img-details', function () {
        listImage = $(this).prop('files');

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

    $('#select-branch').select2();

    $(document).on('change', '#select-branch', function () {
        const branch = $(this).val();

        $.ajax({
            url: './api/report/branch',
            type: 'POST',
            data: {
                branch: branch
            },
        success: function(data) {
            data = JSON.parse(data);

            if (data.status) {
                clearDataReportBranch();
                const reports = data.data.report;
                const count = data.data.count;
                paging(count);

                if (reports.length == 0) {
                    $('#list-report-branch').html(`
                    <div class="not_found">
                        <img src="./Assets/Images/search-file.png" alt="">
                        <span>Chưa có báo cáo!</span>
                    </div>
                    `);
                    return;
                }

                reports.forEach(report => {
                    $('#list-report-branch').append(`
                    <div class="content-item" data-id="${report.id}">
                        <div class="content">${report.content}</div>
                        <div class="detail"><span class="time">${report.time}</span></div>
                    </div>
                    `);
                });
            }
        },
        error: function(data) {
            swal("Thất bại!", "Đã có lỗi xảy ra", "error");
        }
        });
    });

    async function clearDataReportBranch() {
        $('#list-report-branch').html('');
        paging(0);
    }

    function paging(count, currentPage = 1) {
        count = parseInt(count);
        currentPage = parseInt(currentPage);

        $('.content_number-report').html(count);

        $('.paging').html('');

        if (count <= 0) {
            return;
        }

        var totalPage = Math.ceil(count / 20);
        const maxPage = 5;
        var startPage = currentPage - 2;
        var endPage = currentPage + 2;

        if (startPage <= 1) {
            startPage = 1;
            endPage = startPage + maxPage - 1;
        }

        if (endPage > totalPage) {
            endPage = totalPage;
            startPage = endPage - maxPage + 1;
        }

        if (startPage <= 1) {
            startPage = 1;
        }

        for (let i = startPage; i <= endPage; i++) {
            $('.paging').append(`
                <div class="paging-item ${ i == currentPage ? 'active' : ''}">${i}</div>
            `);
        }

        if (startPage > 1) {
            $('.paging').prepend(`
                <div class="paging-item" data-disable="true">...</div>
            `);
        }

        if (endPage < totalPage) {
            $('.paging').append(`
                <div class="paging-item" data-disable="true">...</div>
            `);
        }
    }

    $(document).on('click', '.paging>.paging-item', function () {
        const disable = $(this).data('disable');
        if (disable) {
            return;
        }

        const currentPage = $(this).text();
        const branch = $('#select-branch').val();

        $.ajax({
            url: './api/report/branch/paging',
            type: 'POST',
            data: {
                branch: branch,
                page: currentPage
            },
        success: function(data) {
            data = JSON.parse(data);

            if (data.status) {
                clearDataReportBranch();
                const reports = data.data.report;
                const count = data.data.count;
                paging(count, currentPage);

                if (reports.length == 0) {
                    $('#list-report-branch').html(`
                    <div class="not_found">
                        <img src="./Assets/Images/search-file.png" alt="">
                        <span>Chưa có báo cáo!</span>
                    </div>
                    `);
                    return;
                }

                reports.forEach(report => {
                    $('#list-report-branch').append(`
                    <div class="content-item" data-id="${report.id}">
                        <div class="content">${report.content}</div>
                        <div class="detail"><span class="time">${report.time}</span></div>
                    </div>
                    `);
                });
            }
        },
        error: function(data) {
            swal("Thất bại!", "Đã có lỗi xảy ra", "error");
        }
        });
    });
});