<div class="body">
    <div class="container">
        <div class="container-title">
            <span>Báo cáo gần đây</span>
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
                    <div class="detail"><span class="author"><?= $report['author'] ?></span><span class="time"><?= $report['time'] ?></span></div>
                </div>

            <?php endforeach; endif; ?>
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

    <div class="container">
        <div class="container-title">
            <span>Báo cáo chi nhánh</span>
            <div class="select-branch">
                <select name="" id="select-branch" style="width: 100%">
                    <?php foreach ($listBranch as $branch): ?>
                        <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="content_number-report"><?= $count ?></div>
        <div class="container-content" id="list-report-branch">
            <?php if (count($listReport) == 0): ?>
            <div class="not_found">
                <img src="./Assets/Images/search-file.png" alt="">
                <span>Chưa có báo cáo!</span>
            </div>
            <?php else: 
                foreach ($listReportBranch as $report): ?>

                <div class="content-item" data-id="<?= $report['id'] ?>">
                    <div class="content"><?= $report['content'] ?></div>
                    <div class="detail"><span class="time"><?= $report['time'] ?></span></div>
                </div>

            <?php endforeach; endif; ?>
        </div>
        <div class="paging">
            <?php
                $totalPage = ceil($count / 20);

                $startpage = 1;
                $endpage = 1;
                if ($totalPage > 5) {
                    $endpage = 5;
                } else {
                    $endpage = $totalPage;
                }

                for ($i = $startpage; $i <= $endpage; $i++) { ?>
                    <div class="paging-item <?= $i == 1 ? 'active' : '' ?>"><?= $i ?></div>
                <?php }

                if ($totalPage > 5) { ?>
                    <div class="paging-item" data-disable="true">...</div>
                <?php }
            ?>
        </div>
    </div>
</div>