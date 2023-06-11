$(document).ready(function () {
    $('#close-zoom-image').click(function (e) { 
        e.preventDefault();
        $('.zoom-image').css('display', 'none');
    });

    $('#rotate-image').click(function (e) { 
        e.preventDefault();
        
        var image = $('#image-show');
        var angle = image.data('angle');
        angle = angle + 90;
        image.data('angle', angle);
        image.css('transform', 'rotate(' + angle + 'deg)');
    });

    $(document).on('click', '.list-img>img', function () {
        let src = $(this).attr('src');
        let image = $('#image-show');
        image.attr('src', src);
        image.data('angle', 0);
        image.css('transform', 'rotate(0deg)');
        $('.zoom-image').css('display', 'flex');
    });

    $('#list-location').select2();
    $('#list-station').select2();

    $('#list-branch-2').select2();
    $('#list-branch-3').select2();

    $('.branch>.item').click(function (e) { 
        e.preventDefault();
        
        const branch = $(this).data('branch');

        $('.branch>.item').removeClass('active');
        $('.result').addClass('hidden');

        $(this).addClass('active');
        $('.result[data-branch="'+branch+'"]').removeClass('hidden');
    });

    var changeBranch = false;

    $(document).on('change', '.choose>label>input', function () {
        if (this.checked)
            $(this).parent().find('i').addClass('hide');
        else
            $(this).parent().find('i').removeClass('hide');
    });

    $(document).on('change', '#list-location', function () {
        let route = $(this).val();
        changeBranch = true;

        $.ajax({
            url: './api/loadStation',
            type: 'POST',
            data: {
                route: route
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    let station = [data.data.start.station_name, data.data.end.station_name];
                    station = station.filter(function (el) {
                        return el != '';
                    });

                    $('#list-station')
                        .find('option')
                        .remove();
                    $('#list-station').select2({data: station});
                    return;
                }
                swal("Thông báo!", data.message, "warning");
            },
            error: function (data) {
                swal("Thông báo!", "Đã có lỗi xảy ra", "error");
            }
        });

        
    });

    $(document).on('click', '#btn-find-branch', function () {
        let route = $('#list-location').val();
        changeBranch = false;

        $.ajax({
            url: './api/getBranch',
            type: 'POST',
            data: {
                route: route,
                station: $('#list-station').val()
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    const branch = $.map(data.data, function (value) {
                        return value.route;
                    });

                    $('#list-branch-2')
                        .find('option')
                        .remove();
                    $('#list-branch-2').select2({data: branch});

                    $('#list-branch-3')
                        .find('option')
                        .remove();
                    $('#list-branch-3').select2({data: branch});
                    return;
                }
                $('#list-branch-2')
                    .find('option')
                    .remove();
                $('#list-branch-2').select2({data: []});

                $('#list-branch-3')
                    .find('option')
                    .remove();
                $('#list-branch-3').select2({data: []});
            },
            error: function (data) {
                swal("Thông báo!", "Đã có lỗi xảy ra", "error");
            }
        });
    });

    $(document).on('click', '#btn-search-detail', function () {
        if (changeBranch) {
            swal("Thông báo!", "Vui lòng nhấn 'Lọc' để cập nhật tuyến đường dây", "warning");
            return;
        }
        clearData();
        var branch = [
            {
                route : $('#list-location').val(),
            },
            {
                route : '',
            },
            {
                route : '',
            }
        ];

        const station = $('#list-station').val();

        if (!$('#cb-station-2').is(':checked')) {
            branch[1].route = $('#list-branch-2').val();
        }

        if (!$('#cb-station-3').is(':checked')) {
            branch[2].route = $('#list-branch-3').val();
        }

        const distance = $('#distance').val();

        $.ajax({
            url: './api/search_t',
            type: 'POST',
            data: {
                branch: branch,
                distance: distance,
                station: station
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    let detail = data.data;

                    detail.forEach(value => {
                        setData(value);
                    });
                    
                    swal("Thành công!", data.message, "success");
                    return;
                }
                swal("Thông báo!", data.message, "warning");
            },
            error: function (data) {
                swal("Thông báo!", "Đã có lỗi xảy ra", "error");
            }
        });
    });

    function clearData() {
        for (let i = 1; i <= 3; i++) {
            $('.branch>.item[data-branch="'+i+'"]>i').addClass('fa-close');
            $('.branch>.item[data-branch="'+i+'"]>i').removeClass('fa-check');

            $('.result[data-branch="'+i+'"]>.row>#search-route').html('');
            $('.result[data-branch="'+i+'"]>.row>#search-column').html('');
            $('.result[data-branch="'+i+'"]>.row>#search-purpose').html('');
            $('.result[data-branch="'+i+'"]>.row>#search-type').html('');
            $('.result[data-branch="'+i+'"]>.row>#search-topographic').html('');
            $('.result[data-branch="'+i+'"]>.row>#search-distance').html('');
            $('.result[data-branch="'+i+'"]>.row>#search-note').html('');
            $('.result[data-branch="'+i+'"]>.row>.list-img').html('');
        }
    }
    
    function setData(value) {
        $(`.branch>.item[data-branch="${value.branch_id+1}"]>i`).removeClass('fa-close');
        $(`.branch>.item[data-branch="${value.branch_id+1}"]>i`).addClass('fa-check');

        $(`.result[data-branch="${value.branch_id+1}"]>.row>#search-route`).html(value.route);
        $(`.result[data-branch="${value.branch_id+1}"]>.row>#search-column`).html(value.column);
        $(`.result[data-branch="${value.branch_id+1}"]>.row>#search-purpose`).html(value.purpose);
        $(`.result[data-branch="${value.branch_id+1}"]>.row>#search-type`).html(value.type);
        $(`.result[data-branch="${value.branch_id+1}"]>.row>#search-topographic`).html(value.topographic);
        $(`.result[data-branch="${value.branch_id+1}"]>.row>#search-distance`).html(value.distance);
        $(`.result[data-branch="${value.branch_id+1}"]>.row>#search-note`).html(value.note);
        $(`.result[data-branch="${value.branch_id+1}"]>.row>.list-img`).html('');
        value.images.forEach(function (item) {
            $(`.result[data-branch="${value.branch_id+1}"]>.row>.list-img`).append('<img src="./Assets/Images/Uploads/' + item + '" alt="">');
        });
        $(`.result[data-branch="${value.branch_id+1}"]>.row>.btn-go`).attr('href', 'https://www.google.com/maps/place/'+value.location + "/data=!3m1!4b1!4m5!3m4!1s0x0:0xe098fa2570e3abd1!8m2!3d19.9499922!4d105.7976357");
        $(`.result[data-branch="${value.branch_id+1}"]>.mapouter>.gmap_canvas>#gmap_canvas`).attr('src', "https://maps.google.com/maps?q="+value.location +"&t=&z=13&ie=UTF8&iwloc=&output=embed");
    }
});