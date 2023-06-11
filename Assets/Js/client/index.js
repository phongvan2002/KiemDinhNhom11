$(document).ready(function () {
    $('#list-route').select2();
    $('#list-station').select2();

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

    $('#distance').change(function (e) { 
        e.preventDefault();
        let distance = $(this).val();

        if (distance < 0)
        {
            $(this).val(Math.abs(distance));
        }
    });

    $('#distance').keyup(function (e) { 
        let distance = $(this).val();

        if (distance < 0)
        {
            $(this).val(Math.abs(distance));
        }
    });

    $(document).on('change', '#list-route', function () {
        let route = $('#list-route').val();

        $.ajax({
            url: './api/loadStation',
            type: 'POST',
            data: {
                route: route,
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    let station = data.data;
                    station = [
                        station.start.station_name,
                        station.end.station_name
                    ];
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

    $('#btn-search').click(function (e) { 
        e.preventDefault();
        
        let route = $('#list-route').val();
        let station = $('#list-station').val();
        let distance = $('#distance').val() == '' ? 0 : $('#distance').val();

        $.ajax({
            url: './api/search',
            type: 'POST',
            data: {
                route: route,
                station: station,
                distance: distance
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    swal("Thành công!", data.message, "success").then(function () {
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