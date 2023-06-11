$(document).ready(function () {
    var showSidebar = false;
    var minSize = $(window).width() >= 850 ? false : true;

    $('.showSideBar').click(function (e) { 
        e.preventDefault();
        showSidebar = !showSidebar;

        if (!minSize) {
            if (showSidebar) {
                $('.sidebar').css('margin-left', '-100%');
                $('.sidebar').css('animation', '0.5s hideSideBar');
                $('.upperbar').css('margin-left', '0');
                $('.upperbar').css('animation', '0.5s zoomOutContainer');
                $('.body').css('margin-left', '0');
                $('.body').css('animation', '0.5s zoomOutContainer');
            }
            else {
                $('.sidebar').css('margin-left', '0');
                $('.sidebar').css('animation', '0.5s showSideBar');
                $('.upperbar').css('margin-left', '270px');
                $('.upperbar').css('animation', '0.5s zoomInContainer');
                $('.body').css('margin-left', '270px');
                $('.body').css('animation', '0.5s zoomInContainer');
            }
        } else {
            if (showSidebar) {
                $('.sidebar').css('margin-left', '0');
                $('.sidebar').css('animation', '0.5s showSideBar');
            }
            else {
                $('.sidebar').css('margin-left', '-100%');
                $('.sidebar').css('animation', '0.5s hideSideBar');
            }
        }
    });

    $(window).resize(function () { 
        minSize = $(window).width() >= 850 ? false : true;
        if (minSize) {
            if (showSidebar) {
                $('.sidebar').css('margin-left', '0');
                $('.sidebar').css('animation', '0.5s showSideBar');
                $('.upperbar').css('margin-left', '0');
                $('.body').css('margin-left', '0');
            }
            else {
                $('.sidebar').css('margin-left', '-100%');
                $('.sidebar').css('animation', '0.5s hideSideBar');
                $('.upperbar').css('margin-left', '0');
                $('.body').css('margin-left', '0');
            }
        }
        else {
            if (showSidebar) {
                $('.sidebar').css('margin-left', '-100%');
                $('.sidebar').css('animation', '0.5s hideSideBar');
                $('.upperbar').css('margin-left', '0');
                $('.upperbar').css('animation', '0.5s zoomOutContainer');
                $('.body').css('margin-left', '0');
                $('.body').css('animation', '0.5s zoomOutContainer');
            }
            else {
                $('.sidebar').css('margin-left', '0');
                $('.sidebar').css('animation', '0.5s showSideBar');
                $('.upperbar').css('margin-left', '270px');
                $('.upperbar').css('animation', '0.5s zoomInContainer');
                $('.body').css('margin-left', '270px');
                $('.body').css('animation', '0.5s zoomInContainer');
            }
        }
    });

    $('.close-sidebar').click(function (e) { 
        e.preventDefault();
        showSidebar = false;

        $('.sidebar').css('margin-left', '-100%');
        $('.sidebar').css('animation', '0.5s hideSideBar');
    });

    $('.dropdown').click(function (e) { 
        e.preventDefault();
        
        const active = $(this).hasClass('active');
        if (!active) {
            $(this).addClass('active');
            $(this).removeClass('hidden');

            $(this).parent().find('.dropdown-menu').removeClass('hidden');
            $(this).parent().find('.dropdown-menu').addClass('active');
            return;
        }

        $(this).removeClass('active');
        $(this).addClass('hidden');

        $(this).parent().find('.dropdown-menu').addClass('hidden');
        $(this).parent().find('.dropdown-menu').removeClass('active');
    });

});