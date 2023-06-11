<div class="body">
    <div class="import-excel">
        <input type="file" id="import-file-excel">
        <label for="import-file-excel" class="select-file">Chọn file . . .</label>
        <span class="file-name"></span>
        <div class="btn">
            <button class="upload">Tải lên</button>
            <button class="cancel-upload">Hủy</button>
        </div>
        <div class="uploading-process">
            <div class="number-line">0</div>
            <div class="show-ratio">
                <div class="ratio">0</div>
                <div class="line"></div>
            </div>
            <textarea name="" id="log-upload" cols="10" rows="5" disabled></textarea>
        </div>
    </div>
    <div class="create-route">
        <div class="title">
            <span>Thêm tuyến đường</span>
            <div class="btn">
                <button class="import">Thêm từ Excel</button>
                <button class="create">Thêm</button>
                <button class="cancel">Hủy</button>
            </div>
        </div>
        <div class="form">
            <div class="form-group">
                <label for="start-point">Điểm bắt đầu</label>
                <input type="text" class="form-control" id="start-point" placeholder="Nhập điểm bắt đầu">
            </div>
            <div class="form-group">
                <label for="end-point">Điểm kết thúc</label>
                <input type="text" class="form-control" id="end-point" placeholder="Nhập điểm kết thúc">
            </div>
            <div class="form-group">
                <label for="column">Số cột</label>
                <input type="number" class="form-control" id="column" placeholder="Nhập số cột">
            </div>
            <div class="form-group">
                <label for="purpose">Công dụng</label>
                <input type="text" class="form-control" id="purpose" placeholder="Nhập công dụng">
            </div>
            <div class="form-group">
                <label for="type">Loại cột</label>
                <input type="text" class="form-control" id="type" placeholder="Nhập loại cột">
            </div>
            <div class="form-group">
                <label for="topographic">Địa hình</label>
                <input type="text" class="form-control" id="topographic" placeholder="Nhập địa hình">
            </div>
            <div class="form-group">
                <label for="distance">Khoảng cách</label>
                <input type="number" class="form-control" id="distance" placeholder="Nhập khoảng cách">
            </div>
            <div class="form-group">
                <label for="latitude">Vĩ độ</label>
                <input type="number" class="form-control" id="latitude" placeholder="Nhập vĩ độ">
            </div>
            <div class="form-group">
                <label for="longitude">Kinh độ</label>
                <input type="number" class="form-control" id="longitude" placeholder="Nhập kinh độ">
            </div>
            <div class="form-group">
                <label for="note">Ghi chú</label>
                <textarea class="form-control" id="note" rows="3"></textarea>
            </div>
            <div class="form-group">
                <button id="btn-create-route">Thêm tuyến đường</button>
            </div>
        </div>
    </div>
    <div class="list-route">
        <div class="filter">
            <div class="choose">
                <select name="" id="list-location" style="width: 100%; height: 30px">
                    <?php foreach ($listLocation as $location) { ?>
                        <option value="<?= $location; ?>"><?= $location; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="btn"><button class="update-station" id="btn-update-station">Cập nhật</button></div>
        </div>
        <div class="update-box">
            <div class="form">
                <div class="form-group">
                    <label for="start-station" id="lb-start-station">Hậu Lộc</label>
                    <input type="text" class="form-control" id="start-station" placeholder="Nhập tên trạm" value="<?= count($station) != 0 ? $station['start'] : '' ?>">
                </div>
                <div class="form-group">
                    <label for="end-station" id="lb-end-station">Núi Một</label>
                    <input type="text" class="form-control" id="end-station" placeholder="Nhập tên trạm" value="<?= count($station) != 0 ? $station['end'] : '' ?>">
                </div>
                <div class="form-group">
                    <button class="btn-update-station" id="btn-update-station-name">Cập nhật</button>
                </div>
            </div>
        </div>
        <table id="table-route" class="display" style="width: 100%">
            <thead>
                <tr>
                    <td>Số cột</td>
                    <td>Đường dây</td>
                    <td>Công dụng</td>
                    <td>Loại cột</td>
                    <td>Địa hình</td>
                    <td>Khoảng cách</td>
                    <td>Thao tác</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listRoute as $route): ?>
                <tr>
                    <td><?= $route['column'] ?></td>
                    <td><?= $route['start'] . ' - ' . $route['end'] ?></td>
                    <td><?= $route['purpose'] ?></td>
                    <td><?= $route['type'] ?></td>
                    <td><?= $route['topographic'] ?></td>
                    <td><?= $route['distance'] ?></td>
                    <td>
                        <div class="btn">
                            <button id="btn-update-route" data-id="<?= $route['id'] ?>">Cập nhật</button>
                            <button id="btn-delete-route" data-id="<?= $route['id'] ?>">Xóa</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="modal-edit-route">
        <div class="container">
            <div class="title">
                <span>Cập nhật tuyến đường</span>
                <div class="btn">
                    <button class="update-edit-route">Cập nhật</button>
                    <button class="cancel-edit-route">Hủy</button>
                </div>
            </div>
            <div class="form">
                <input type="hidden" class="form-control" id="id-edit">
                <div class="form-group">
                    <label for="start-point">Điểm bắt đầu</label>
                    <input type="text" class="form-control" id="start-point-edit" placeholder="Nhập điểm bắt đầu">
                </div>
                <div class="form-group">
                    <label for="end-point">Điểm kết thúc</label>
                    <input type="text" class="form-control" id="end-point-edit" placeholder="Nhập điểm kết thúc">
                </div>
                <div class="form-group">
                    <label for="column">Số cột</label>
                    <input type="number" class="form-control" id="column-edit" placeholder="Nhập số cột">
                </div>
                <div class="form-group">
                    <label for="purpose">Công dụng</label>
                    <input type="text" class="form-control" id="purpose-edit" placeholder="Nhập công dụng">
                </div>
                <div class="form-group">
                    <label for="type">Loại cột</label>
                    <input type="text" class="form-control" id="type-edit" placeholder="Nhập loại cột">
                </div>
                <div class="form-group">
                    <label for="topographic">Địa hình</label>
                    <input type="text" class="form-control" id="topographic-edit" placeholder="Nhập địa hình">
                </div>
                <div class="form-group">
                    <label for="distance">Khoảng cách</label>
                    <input type="number" class="form-control" id="distance-edit" placeholder="Nhập khoảng cách">
                </div>
                <div class="form-group">
                    <label for="latitude">Vĩ độ</label>
                    <input type="number" class="form-control" id="latitude-edit" placeholder="Nhập vĩ độ">
                </div>
                <div class="form-group">
                    <label for="longitude">Kinh độ</label>
                    <input type="number" class="form-control" id="longitude-edit" placeholder="Nhập kinh độ">
                </div>
                <div class="form-group">
                    <label for="note">Ghi chú</label>
                    <textarea class="form-control" id="note-edit" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Ảnh minh họa</label>
                    <input type="file" multiple="multiple" class="form-control select-img-details" id="image" name="image">
                    <div class="multi-image-small">
                        <label for="image" class="new-image-small">image</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>