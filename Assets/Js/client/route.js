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
    $('#list-column').select2();

    $(document).on('change', '#list-location', function () {
        let route = $(this).val();

        $.ajax({
            url: './api/getColumn',
            type: 'POST',
            data: {
                route: route
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    let column = data.data;
                    $('#list-column')
                        .find('option')
                        .remove();
                    $('#list-column').select2({data: column});
                    return;
                }
                swal("Thông báo!", data.message, "warning");
            },
            error: function (data) {
                swal("Thông báo!", "Đã có lỗi xảy ra", "error");
            }
        });
    });

    $(document).on('click', '#btn-search-detail-column', function () {
        let route = $('#list-location').val();
        let column = $('#list-column').val();

        $.ajax({
            url: './api/getDetailColumn',
            type: 'POST',
            data: {
                route: route,
                column: column
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    let detail = data.data;
                    $('#search-route').html(detail.route);
                    $('#search-column').html(detail.column);
                    $('#search-purpose').html(detail.purpose);
                    $('#search-type').html(detail.type);
                    $('#search-topographic').html(detail.topographic);
                    $('#search-distance').html(detail.distance);
                    $('#search-note').html(detail.note);
                    $('.list-img').html('');
                    detail.images.forEach(function (item) {
                        $('.list-img').append('<img src="./Assets/Images/Uploads/' + item + '" alt="">');
                    });

                    $('.btn-go').attr('href', 'https://www.google.com/maps/place/'+detail.location + "/data=!3m1!4b1!4m5!3m4!1s0x0:0xe098fa2570e3abd1!8m2!3d19.9499922!4d105.7976357");
                    $('#gmap_canvas').attr('src', "https://maps.google.com/maps?q="+detail.location +"&t=&z=13&ie=UTF8&iwloc=&output=embed");
                    
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
});