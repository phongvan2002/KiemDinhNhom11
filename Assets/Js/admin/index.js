$(document).ready(function () {

    $('#list-location').select2();
    $('#list-location').trigger('change');

    $('.create').click(function (e) { 
        e.preventDefault();
        
        $('.cancel').css('display', 'block');
        $('.create').css('display', 'none');
    
        $('.create-route>.form').css('height', '828px');
        $('.create-route>.form').css('animation', 'showForm 0.5s');
    });
    
    $('.cancel').click(function (e) {
        e.preventDefault();
        $('.cancel').css('display', 'none');
        $('.create').css('display', 'block');
    
        $('.create-route>.form').css('height', '0');
        $('.create-route>.form').css('animation', 'hideForm 0.5s');
    });

    $('.cancel-upload').click(function (e) { 
        e.preventDefault();
        $('.import-excel').css('display', 'none');
    });

    $('.import').click(function (e) { 
        e.preventDefault();
        $('.import-excel').css('display', 'flex');

        hideProcess();
        clearUploadingProcess();

    });

    $('#btn-create-route').click(function (e) { 
        e.preventDefault();
        
        let start = $('#start-point').val();
        let end = $('#end-point').val();
        let column = $('#column').val();
        let purpose = $('#purpose').val();
        let type = $('#type').val();
        let topographic = $('#topographic').val();
        let distance = $('#distance').val();
        let latitude = $('#latitude').val();
        let longitude = $('#longitude').val();
        let note = $('#note').val();

        if (start == '' || end == '' || column == '' || purpose == '' || type == '' || topographic == '' || distance == '' || latitude == '' || longitude == '') {
            swal("Thông báo!", "Vui lòng nhập đầy đủ thông tin", "warning");
            return;
        }

        $.ajax({
            url: './api/createRoute',
            type: 'POST',
            data: {
                start: start,
                end: end,
                column: column,
                purpose: purpose,
                type: type,
                topographic: topographic,
                distance: distance,
                latitude: latitude,
                longitude: longitude,
                note: note
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    swal("Thành công!", data.message, "success").then(function () {
                        // add new route to list
                        table.row.add([column, start + ' - ' + end, purpose, type, topographic, distance, `
                            <div class="btn">
                                <button id="btn-update-route" data-id="${data.data.id}">Cập nhật</button>
                                <button id="btn-delete-route" data-id="${data.data.id}">Xóa</button>
                            </div>
                        `]).draw();
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

    var data_excel = [];
    var percentDone = 0;

    $('#import-file-excel').change(function (e) { 
        e.preventDefault();

        // clear data_excel;
        data_excel = [];

        let file = $(this).prop('files')[0];

        // clear file input
        $(this).val('');

        // check file type .xlsx, .xls
        if (file.type != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type != 'application/vnd.ms-excel') {
            swal("Thông báo!", "Vui lòng chọn file excel", "warning");
            $('.file-name').css('display', 'none');
            $('.file-name').html('');
            return;
        }

        // show file name
        $('.file-name').css('display', 'flex');
        $('.file-name').html(file.name);

        // Lấy số dòng của file excel
        let reader = new FileReader();
        reader.onload = function (e) {
            try {
                let data = e.target.result;
                let workbook = XLSX.read(data, { type: 'binary' });
                let sheet_name_list = workbook.SheetNames;
                for (let i = 0; i < sheet_name_list.length; i++) {
                    let worksheet = workbook.Sheets[sheet_name_list[i]];
                    let data = XLSX.utils.sheet_to_json(worksheet);
                    setDataExcel(data, sheet_name_list[i]);
                }
            }
            catch (e) {
                $('.file-name').css('display', 'none');
                $('.file-name').html('');
                swal("Thông báo!", "Không thể đọc nội dung file do sai định dạng", "error");
            }
        }
        reader.readAsBinaryString(file);
    });

    function setDataExcel(data, sheet_name = '') {
        // insert sheet_name to data
        for (let i = 0; i < data.length; i++) {
            data[i].sheet_name = sheet_name;
        }
        // merge into data_excel
        data_excel = data_excel.concat(data);
    }

    $('.upload').click(function (e) { 
        e.preventDefault();
        
        if (data_excel.length == 0 || data_excel == undefined) {
            swal("Thông báo!", "Chưa có dữ liệu hoặc định dạng nội dung file không hợp lệ", "warning");
            return;
        }

        clearUploadingProcess();
        showProcess();

        addLogUploadingProcess('Đang xử lý dữ liệu...');

        setNumberLine(data_excel.length);
        // send data to server

        callback(0);
    });

    async function callback(index){
        if (index < data_excel.length) {
            let element = data_excel[index];

            let start_end = element['ĐZ'];
            // remove the character ĐZ
            start_end = start_end.replace('ĐZ', '');
            // split start and end
            let start = start_end.split('-')[0];
            let end = start_end.split('-')[1];
            // remove leading and trailing blanks
            start = start.trim();
            end = end.trim();

            var stt = element['STT'];
            var column = element['Số cột'];
            var purpose = element['Công dụng cột'];
            var type = element['Loại cột'];
            var topographic = element['Địa hình/Địa thế'];
            var distance = element['Khoảng Cách cột (m)'];
            var latitude = element['Vĩ độ'];
            var longitude = element['Kinh độ'];
            var note = element['Ghi chú'] == undefined ? '' : element['Ghi chú'];
            var sheet_name = element['sheet_name'];

            $.ajax({
                url: './api/createRoute',
                type: 'POST',
                data: {
                    start: start,
                    end: end,
                    column: column,
                    purpose: purpose,
                    type: type,
                    topographic: topographic,
                    distance: distance,
                    latitude: latitude,
                    longitude: longitude,
                    note: note
                },
                success: function (data) {
                    data = JSON.parse(data);

                    if (!data.status) {
                        addLogUploadingProcess('Sheet: ' + sheet_name + ' - STT: ' + stt + ' --> ' + data.message);
                    }
                    else {
                        table.row.add([column, start + ' - ' + end, purpose, type, topographic, distance, `
                            <div class="btn">
                                <button id="btn-update-route" data-id="${data.data.id}">Cập nhật</button>
                                <button id="btn-delete-route" data-id="${data.data.id}">Xóa</button>
                            </div>
                        `]).draw();
                    }
                    plusPercent();
                    return callback(index + 1);
                },
                error: function (data) {
                    addLogUploadingProcess('Đã có lỗi xảy ra');
                    plusPercent();
                    return callback(index + 1);
                }
            });
        }
    }
    
    function plusPercent() {
        percentDone++;
        setRatioUploadingProcess(parseInt(((percentDone+1) * 100) / data_excel.length));
        if (percentDone == data_excel.length) {
            addLogUploadingProcess('========== Đã hoàn thành ==========');
            swal("Thành công!", "Hoàn tất quá trình tải dữ liệu", "success");
        }
    }

    function setRatioUploadingProcess(percent) {
        $('.uploading-process>.show-ratio>.line').css('width', (100 - percent) + '%');
        $('.uploading-process>.show-ratio>.ratio').html(percent);
    }

    function addLogUploadingProcess(message) {
        let time = new Date();
        let time_now = time.getHours() + ':' + time.getMinutes() + ':' + time.getSeconds();
        $('#log-upload').val('[ ' + time_now + ' ] : ' + message + '\n' + $('#log-upload').val());
    }

    function clearUploadingProcess() {
        setRatioUploadingProcess(0);
        setNumberLine(0);
        $('#log-upload').val('');
        $('.file-name').css('display', 'none');
        $('.file-name').html('');
        percentDone = 0;
    }

    function setNumberLine(number) {
        $('.uploading-process>.number-line').html(number);
    }

    function showProcess() {
        $('.uploading-process').css('display', 'flex');
        $('.import-excel>label,.import-excel>span,.import-excel>.btn').css('display', 'none');
    }

    function hideProcess() {
        $('.uploading-process').css('display', 'none');
        $('.import-excel>label,.import-excel>span,.import-excel>.btn').css('display', 'flex');
    }

    var table = $('#table-route').DataTable();

    $('#table-route').css('width', '100%');

    $(document).on('click', '#btn-delete-route', function () {
        let id = $(this).attr('data-id');

        var row = $(this).closest('tr');
        
        swal({
            title: "Bạn có chắc chắn muốn xóa?",
            text: "Dữ liệu sẽ không thể phục hồi!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: './api/deleteRoute',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        if (data.status) {
                            swal("Thành công!", data.message, "success");
                            table.row(row).remove().draw();
                        } else {
                            swal("Thất bại!", data.message, "error");
                        }
                    },
                    error: function (data) {
                        swal("Thất bại!", "Đã có lỗi xảy ra", "error");
                    }
                });
            }
        });
    });

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

    $('.cancel-edit-route').click(function (e) { 
        e.preventDefault();
        
        $('.modal-edit-route').css('display', 'none');
    });

    $(document).on('click', '#btn-update-route', function () {
        let id = $(this).attr('data-id');

        rowSelected = $(this).closest('tr');

        // get data from server
        $.ajax({
            url: './api/getRouteByID',
            type: 'POST',
            data: {
                id: id
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    let route = data.data;
                    changeImage = false;

                    setDataModalUpdateRoute(route['id'], route['column'], route['start'], route['end'], route['purpose'], route['type'], route['topographic'], route['distance'], route['latitude'], route['longitude'], route['note'], route['images']);
                    $('.modal-edit-route').css('display', 'flex');
                }
                else {
                    swal("Thất bại!", data.message, "error");
                }
            },
            error: function (data) {
                swal("Thất bại!", "Đã có lỗi xảy ra", "error");
            }
        });
    });

    function setDataModalUpdateRoute(id, column, start, end, purpose, type, topographic, distance, latitude, longitude, note, images) {
        resetDataModalUpdateRoute();

        $('#id-edit').val(id);
        $('#column-edit').val(column);
        $('#start-point-edit').val(start);
        $('#end-point-edit').val(end);
        $('#purpose-edit').val(purpose);
        $('#type-edit').val(type);
        $('#topographic-edit').val(topographic);
        $('#distance-edit').val(distance);
        $('#latitude-edit').val(latitude);
        $('#longitude-edit').val(longitude);
        $('#note-edit').val(note);
        listImage = images;
        $('.multi-image-small').html('<label for="image" class="new-image-small">image</label>');
        for (let i = 0; i < listImage.length; i++) {
            $('.multi-image-small').append(`
                    <img src="./Assets/Images/Uploads/${listImage[i]['url']}" alt="">
            `);
        }
    }

    function resetDataModalUpdateRoute() {
        $('#id-edit').val('');
        $('#column-edit').val('');
        $('#start-point-edit').val('');
        $('#end-point-edit').val('');
        $('#purpose-edit').val('');
        $('#type-edit').val('');
        $('#topographic-edit').val('');
        $('#distance-edit').val('');
        $('#latitude-edit').val('');
        $('#longitude-edit').val('');
        $('#note-edit').val('');
        listImage = [];
        $('.multi-image-small').html('<label for="image" class="new-image-small">image</label>');
    }

    $('.update-edit-route').click(function (e) { 
        e.preventDefault();
        
        let id = $('#id-edit').val();
        let column = $('#column-edit').val();
        let start = $('#start-point-edit').val();
        let end = $('#end-point-edit').val();
        let purpose = $('#purpose-edit').val();
        let type = $('#type-edit').val();
        let topographic = $('#topographic-edit').val();
        let distance = $('#distance-edit').val();
        let latitude = $('#latitude-edit').val();
        let longitude = $('#longitude-edit').val();
        let note = $('#note-edit').val();
        let images = listImage;
        
        if (column == '' || start == '' || end == '' || purpose == '' || type == '' || topographic == '' || distance == '' || latitude == '' || longitude == '') {
            swal("Thất bại!", "Vui lòng nhập đầy đủ thông tin", "error");
            return;
        }

        // Add to FormData
        let formData = new FormData();
        formData.append('id', id);
        formData.append('column', column);
        formData.append('start', start);
        formData.append('end', end);
        formData.append('purpose', purpose);
        formData.append('type', type);
        formData.append('topographic', topographic);
        formData.append('distance', distance);
        formData.append('latitude', latitude);
        formData.append('longitude', longitude);
        formData.append('note', note);
        for (let i = 0; i < images.length; i++) {
            formData.append('images[]', images[i]);
        }

        // Update to server
        $.ajax({
            url: './api/updateRoute',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    swal("Thành công!", data.message, "success");
                    $('.modal-edit-route').css('display', 'none');
                    rowSelected.replaceWith(`
                        <tr>
                            <td>${column}</td>
                            <td>${start + ' - ' + end}</td>
                            <td>${purpose}</td>
                            <td>${type}</td>
                            <td>${topographic}</td>
                            <td>${distance}</td>
                            <td>
                                <div class="btn">
                                    <button id="btn-update-route" data-id="${id}">Cập nhật</button>
                                    <button id="btn-delete-route" data-id="${id}">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    `);
                }
                else {
                    swal("Thất bại!", data.message, "error");
                }
            },
            error: function (data) {
                swal("Thất bại!", "Đã có lỗi xảy ra", "error");
            }
        });
    });

    $(document).on('click', '.update-station', function () {
        changeBtnUpdateStation(false);
        changeStatusUpdateBox();
    });

    $(document).on('click', '.cancel-station', function () {
        changeBtnUpdateStation();
        changeStatusUpdateBox(false);
    });

    $('#list-location').change(function (e) { 
        e.preventDefault();
        
        let location = $('#list-location').val();

        if (location == '') {
            swal("Thất bại!", "Vui lòng chọn địa điểm", "error");
            return;
        }

        // Post to server : ./api/loadLocation
        $.ajax({
            url: './api/loadLocation',
            type: 'POST',
            data: {
                location: location
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    list = data.data.route;
                    // clear table-route
                    table.clear().draw();
                    // add new row to table-route
                    list.forEach(route => {
                        table.row.add([
                            route.column,
                            route.start + ' - ' + route.end,
                            route.purpose,
                            route.type,
                            route.topographic,
                            route.distance,
                            `
                                <div class="btn">
                                    <button id="btn-update-route" data-id="${route.id}">Cập nhật</button>
                                    <button id="btn-delete-route" data-id="${route.id}">Xóa</button>
                                </div>
                            `
                        ]).draw();
                    });
                    // notification
                    swal("Thành công!", data.message, "success");

                    resetUpdateBox();
                    let station = data.data.station;
                    setUpdateBox(station.start.name, station.end.name, station.start.station_name, station.end.station_name);
                }
                else {
                    swal("Thất bại!", data.message, "error");
                }
            },
            error: function (data) {
                swal("Thất bại!", "Đã có lỗi xảy ra", "error");
            }
        });
    });

    function changeBtnUpdateStation(param = true) {
        if (param) {
            // change class cancel-station to update-station
            $('#btn-update-station').removeClass('cancel-station');
            $('#btn-update-station').addClass('update-station');

            // change text cancel-station to update-station
            $('#btn-update-station').text('Cập nhật');
        }
        else {
            // change class update-station to cancel-station
            $('#btn-update-station').removeClass('update-station');
            $('#btn-update-station').addClass('cancel-station');
            
            // change text update-station to cancel-station
            $('#btn-update-station').text('Hủy');
        }
    }

    function changeStatusUpdateBox(param = true) {
        if (param) {
            // change height of update-box to 226px
            $('.update-box').css('height', '226px');
        }
        else {
            // change height of update-box to 0px
            $('.update-box').css('height', '0px');
        }
    }

    function resetUpdateBox() {
        $('#start-station').val('');
        $('#end-station').val('');

        changeBtnUpdateStation();
        changeStatusUpdateBox(false);
    }

    function setUpdateBox(start, end, start_station = '', end_station = '') {
        $('#start-station').val(start_station);
        $('#end-station').val(end_station);
        $('#lb-start-station').html(start);
        $('#lb-end-station').html(end);
    }

    $(document).on('click', '#btn-update-station-name', function () {
        let start = $('#start-station').val();
        let end = $('#end-station').val();
        let route = $('#list-location').val();

        if (start == '' || end == '' || route == '') {
            swal("Thất bại!", "Vui lòng nhập đầy đủ thông tin", "error");
            return;
        }

        // Post to server : ./api/updateStation
        $.ajax({
            url: './api/updateStation',
            type: 'POST',
            data: {
                start: start,
                end: end,
                route: route
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    swal("Thành công!", data.message, "success");
                    resetUpdateBox();
                }
                else {
                    swal("Thất bại!", data.message, "error");
                }
            },
            error: function (data) {
                swal("Thất bại!", "Đã có lỗi xảy ra", "error");
            }
        });
    });

    $.ajax({
        type: "method",
        url: "url",
        data: "data",
        dataType: "dataType",
        success: function (response) {
            
        }
    });
});