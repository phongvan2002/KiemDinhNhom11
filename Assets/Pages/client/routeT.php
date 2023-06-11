<div class="body">
    <div class="zoom-image">
        <div class="show-image">
            <img src="" alt="" id="image-show" data-angle="0">
            <div class="btn">
                <ion-icon name="reload-outline" id="rotate-image"></ion-icon>
                <ion-icon name="close-outline" id="close-zoom-image"></ion-icon>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="search">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <b>Tuyến đường dây 1</b>
                        <select name="" id="list-location" style="width: 100%; height: 30px">
                            <?php foreach ($listLocation as $location) { ?>
                                <option value="<?= $location; ?>"><?= $location; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <b>Trạm 110kV</b>
                        <select name="" class="station" id="list-station" style="width: 100%">
                        <?php 
                            if (count($station) > 0) {
                                echo '<option value="'.$station['start'].'">'.$station['start'].'</option>';
                                echo '<option value="'.$station['end'].'">'.$station['end'].'</option>';
                            } 
                        ?>
                    </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <b>&nbsp;</b>
                        <button id="btn-find-branch">Lọc</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <b>Tuyến đường dây 2</b>
                        <select name="" id="list-branch-2" style="width: 100%; height: 30px;">
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <b>Chọn</b>
                        <div class="choose">
                            <label for="cb-station-2">
                                <i class="fa fa-check"></i>
                                <input type="checkbox" id="cb-station-2">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <b>Tuyến đường dây 3</b>
                        <select name="" id="list-branch-3" style="width: 100%; height: 30px">
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <b>Chọn</b>
                        <div class="choose">
                            <label for="cb-station-3">
                                <i class="fa fa-check"></i>
                                <input type="checkbox" id="cb-station-3">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <b>Vị trí sự cố</b>
                        <input type="number" id="distance" placeholder="Vị trí sự cố (mét)">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <b>&nbsp;</b>
                        <button id="btn-search-detail">Tìm kiếm</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="branch">
            <div class="item active" data-branch="1"><span>Tuyến 1</span><i class="fa fa-close"></i></div>
            <div class="item" data-branch="2"><span>Tuyến 2</span><i class="fa fa-close"></i></div>
            <div class="item" data-branch="3"><span>Tuyến 3</span><i class="fa fa-close"></i></div>
        </div>
        <div class="result" data-branch="1">
            <div class="row">
                <b>Đường dây: </b>
                <span id="search-route"></span>
            </div>
            <div class="row">
                <b>Số cột: </b>
                <span id="search-column"></span>
            </div>
            <div class="row">
                <b>Công dụng cột: </b>
                <span id="search-purpose"></span>
            </div>
            <div class="row">
                <b>Loại cột: </b>
                <span id="search-type"></span>
            </div>
            <div class="row">
                <b>Địa hình: </b>
                <span id="search-topographic"></span>
            </div>
            <div class="row">
                <b>Khoảng cách: </b>
                <span class="distance" id="search-distance">0</span>
            </div>
            <div class="row">
                <b>Ghi chú: </b>
                <span id="search-note"></span>
            </div>
            <div class="row">
                <b>Hình ảnh:</b>
                <div class="list-img">
                </div>
            </div>
            <div class="row">
                <b>Địa điểm: </b>
                <a href="https://www.google.com/maps/place/" class="btn-go">Đi tới</a>
            </div>
            <div class="mapouter">
                <div class="gmap_canvas">
                    <iframe width="100%" height="500px" id="gmap_canvas" src="https://maps.google.com/maps?q=h%C3%A0%20n%E1%BB%99i&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                </div>
            </div>
        </div>
        <div class="result hidden" data-branch="2">
            <div class="row">
                <b>Đường dây: </b>
                <span id="search-route"></span>
            </div>
            <div class="row">
                <b>Số cột: </b>
                <span id="search-column"></span>
            </div>
            <div class="row">
                <b>Công dụng cột: </b>
                <span id="search-purpose"></span>
            </div>
            <div class="row">
                <b>Loại cột: </b>
                <span id="search-type"></span>
            </div>
            <div class="row">
                <b>Địa hình: </b>
                <span id="search-topographic"></span>
            </div>
            <div class="row">
                <b>Khoảng cách: </b>
                <span class="distance" id="search-distance">0</span>
            </div>
            <div class="row">
                <b>Ghi chú: </b>
                <span id="search-note"></span>
            </div>
            <div class="row">
                <b>Hình ảnh:</b>
                <div class="list-img">
                </div>
            </div>
            <div class="row">
                <b>Địa điểm: </b>
                <a href="https://www.google.com/maps/place/" class="btn-go">Đi tới</a>
            </div>
            <div class="mapouter">
                <div class="gmap_canvas">
                    <iframe width="100%" height="500px" id="gmap_canvas" src="https://maps.google.com/maps?q=h%C3%A0%20n%E1%BB%99i&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                </div>
            </div>
        </div>
        <div class="result hidden" data-branch="3">
            <div class="row">
                <b>Đường dây: </b>
                <span id="search-route"></span>
            </div>
            <div class="row">
                <b>Số cột: </b>
                <span id="search-column"></span>
            </div>
            <div class="row">
                <b>Công dụng cột: </b>
                <span id="search-purpose"></span>
            </div>
            <div class="row">
                <b>Loại cột: </b>
                <span id="search-type"></span>
            </div>
            <div class="row">
                <b>Địa hình: </b>
                <span id="search-topographic"></span>
            </div>
            <div class="row">
                <b>Khoảng cách: </b>
                <span class="distance" id="search-distance">0</span>
            </div>
            <div class="row">
                <b>Ghi chú: </b>
                <span id="search-note"></span>
            </div>
            <div class="row">
                <b>Hình ảnh:</b>
                <div class="list-img">
                </div>
            </div>
            <div class="row">
                <b>Địa điểm: </b>
                <a href="https://www.google.com/maps/place/" class="btn-go">Đi tới</a>
            </div>
            <div class="mapouter">
                <div class="gmap_canvas">
                    <iframe width="100%" height="500px" id="gmap_canvas" src="https://maps.google.com/maps?q=h%C3%A0%20n%E1%BB%99i&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>