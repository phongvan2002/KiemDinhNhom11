<div class="body">
    <div class="change change-pass">
        <div class="title">
            Thay đổi mật khẩu
        </div>
        <div class="form">
            <div class="form-group">
                <label for="old_pass">Mật khẩu cũ</label>
                <input type="password" name="old_pass" id="old_pass" class="form-control">
            </div>
            <div class="form-group">
                <label for="new_pass">Mật khẩu mới</label>
                <input type="password" name="new_pass" id="new_pass" class="form-control">
            </div>
            <div class="form-group">
                <label for="confirm_pass">Nhập lại mật khẩu</label>
                <input type="password" name="confirm_pass" id="confirm_pass" class="form-control">
            </div>
            <div class="form-group">
                <button type="button" id="btn-change-pass" class="btn btn-primary">Thay đổi</button>
            </div>
        </div>
    </div>
    <div class="change change-profile">
        <div class="title">
            Thông tin khách hàng
        </div>
        <div class="form">
            <div class="form-group">
                <label for="name">Họ và tên</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= $profile['name'] ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= $profile['email'] ?>">
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="number" name="phone" id="phone" class="form-control" value="<?= $profile['phone'] ?>">
            </div>
            <div class="form-group">
                <button type="submit" id="btn-change-profile" class="btn btn-primary">Thay đổi</button>
            </div>
        </div>
    </div>
</div>