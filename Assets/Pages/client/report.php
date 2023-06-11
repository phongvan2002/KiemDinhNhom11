<div class="body">
    <div class="container">
        <div class="container-title">
            <span>Báo cáo gần đây</span>
            <button id="btn-show-modal-report">Báo cáo</button>
        </div>
        <div class="container-content">
            <?php if (count($listReport) == 0): ?>
            <div class="not_found">
                <img src="./Assets/Images/search-file.png" alt="">
                <span>Chưa có báo cáo!</span>
            </div>
            <?php else: 
                foreach ($listReport as $report): ?>

                <div class="content-item" data-id="<?= $report['id'] ?>">
                    <div class="content"><?= $report['content'] ?></div>
                    <div class="time"><?= $report['time'] ?></div>
                </div>

            <?php endforeach; endif; ?>
        </div>

        <div class="modal modal-report hidden">
            <div class="modal-body">
                <div class="modal-title">
                    <span>Báo cáo</span>
                    <ion-icon name="close-outline" class="close-modal"></ion-icon>
                </div>
                <div class="modal-content">
                    <div class="form">
                        <div class="form-group">
                            <label for="content">Nội dung</label>
                            <textarea name="report" id="content-report"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="note">Ghi chú</label>
                            <textarea name="note" id="note-report"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Ảnh minh họa</label>
                            <input type="file" multiple="multiple" class="form-control select-img-details" id="image" name="image">
                            <div class="multi-image-small">
                                <label for="image" class="new-image-small">image</label>
                            </div>
                        </div>
                        <div class="btn">
                            <button id="btn-report">Báo cáo</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-show-report hidden">
            <div class="modal-body">
                <div class="modal-title">
                    <span>Báo cáo</span>
                    <ion-icon name="close-outline" class="close-modal"></ion-icon>
                </div>
                <div class="modal-content">
                    <div class="form">
                        <div class="form-group">
                            <label for="content">Nội dung</label>
                            <textarea name="report" id="content-show-report" disabled></textarea>
                        </div>
                        <div class="form-group">
                            <label for="note">Ghi chú</label>
                            <textarea name="note" id="note-show-report" disabled></textarea>
                        </div>
                        <div class="form-group">
                            <label>Ảnh minh họa</label>
                            <div class="multi-image-small-show">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal zoom-image hidden">
            <div class="modal-body">
                <div class="modal-title">
                    <span></span>
                    <ion-icon name="close-outline" class="close-modal"></ion-icon>
                </div>
                <div class="modal-content">
                    <img src="" alt="">
                </div>
            </div>
        </div>
    </div>
</div>