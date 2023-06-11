<div class="body">
    <div class="create-account">
        <div class="title">
            <span>Đăng ký chi nhánh</span>
            <button class="register">Đăng ký</button>
            <button class="cancel">Hủy</button>
        </div>
        <div class="form">
            <div class="form-group">
                <label for="username">Tài khoản</label>
                <input type="text" class="form-control" id="username" placeholder="Tài khoản">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" class="form-control" id="password" placeholder="Mật khẩu">
            </div>
            <div class="form-group">
                <label for="repassword">Nhập lại mật khẩu</label>
                <input type="password" class="form-control" id="repassword" placeholder="Nhập lại mật khẩu">
            </div>
            <div class="form-group">
                <label for="name">Tên chi nhánh</label>
                <input type="text" class="form-control" id="name" placeholder="Tên chi nhánh">
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="number" class="form-control" id="phone" placeholder="Số điện thoại">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Email">
            </div>
            <div class="form-group">
                <button id="btn-register-account">Đăng ký</button>
            </div>
        </div>
    </div>

    <div class="list">
        <table id="table-list-account" class="table is-striped">
            <thead>
                <tr>
                    <th>Tài khoản</th>
                    <th>Tên chi nhánh</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Số báo cáo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account) { ?>
                    <tr>
                        <td><?= $account['username'] ?></td>
                        <td><?= $account['name'] ?></td>
                        <td><?= $account['phone'] ?></td>
                        <td><?= $account['email'] ?></td>
                        <td><?= $account['report'] ?></td>
                        <td>
                            <button class="btn-delete" data-id="<?= $account['id'] ?>">Xóa</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>