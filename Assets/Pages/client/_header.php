<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | <?= SITE_NAME ?></title>
    <base href="<?= SITE_URL ?>">

    <link rel="shortcut icon" href="./Assets/Images/logo.jpg" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800" rel="stylesheet">

    <!-- jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <!-- SweetAlert CDN -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- DataTables jQuery CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <!-- IonIcons CDN -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Select2 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    
    <!-- Read file excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xls/0.7.4-a/xls.core.min.js"></script>  

    <link rel="stylesheet" href="./Assets/Css/client/header.css">
    
    <script src="./Assets/Js/client/header.js"></script>

    <?php foreach ($assets as $asset) {
        echo $asset;   
    } ?>

</head>
<body>
    <header>
        <div class="sidebar">
            <div class="logo">
                <a href="./">
                    <img src="./Assets/Images/logo.jpg" alt="">
                    <span><?= SITE_ACRONYMS ?></span>
                </a>
                <ion-icon name="close-circle-outline" class="close-sidebar"></ion-icon>
            </div>
            <div class="menu">
                <?php foreach ($links as $item): 
                        if ($item['link'] != '#'):
                    ?>
                    <a href="<?= $item['link'] ?>" <?= $page_number == $item['id'] ? 'class="active"' : '' ?>>
                        <ion-icon name="<?= $item['icon'] ?>"></ion-icon>
                        <span><?= $item['title'] ?></span>
                    </a>
                <?php else: ?>
                    <span class="sub <?= $page_number == $item['id'] ? 'active' : '' ?>">
                        <div class="dropdown">
                            <div class="item">
                                <ion-icon name="<?= $item['icon'] ?>"></ion-icon>
                                <span><?= $item['title'] ?></span>
                            </div>
                            <ion-icon name="chevron-back-outline"></ion-icon>
                        </div>
                        <div class="dropdown-menu">
                            <?php foreach ($item['sub'] as $sub): ?>
                                <a href="<?= $sub['link'] ?>" <?= ($page_number == $item['id']) && ($sub_number == $sub['id']) ? 'class="active"' : '' ?>>
                                    <ion-icon name="caret-forward-outline"></ion-icon>
                                    <span><?= $sub['title'] ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </span>

                <?php endif; endforeach; ?>
            </div>
        </div>
        <div class="upperbar">
            <div class="showSideBar"><ion-icon name="menu-outline"></ion-icon></div>
            <div class="creeping"><span><?= RUNNING_TEXT ?></span></div>
        </div>
    </header>