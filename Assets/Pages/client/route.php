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
            <div class="col">
                <div class="form-group">
                    <b>Tuyến đường dây</b>
                    <select name="" id="list-location" style="width: 100%; height: 30px">
                        <?php foreach ($listLocation as $location) { ?>
                            <option value="<?= $location; ?>"><?= $location; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <b>Số cột</b>
                    <select name="" class="column" id="list-column">
                        <?php foreach ($listColumn as $column) { ?>
                            <option value="<?= $column; ?>"><?= $column; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <b>&nbsp;</b>
                    <button id="btn-search-detail-column">Tìm kiếm</button>
                </div>
            </div>
        </div>
        <div class="result">
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