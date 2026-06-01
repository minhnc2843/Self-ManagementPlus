import jQuery from "jquery";

(function($) {
    "use strict";

    // 1. LOGIC SIDEBAR MENU (Sử dụng Event Delegation để tương thích Livewire SPA)
    // Bắt sự kiện click trên document thay vì element cụ thể
    $(document).on('click', '.sidebar-menu li a', function(e) {
        var $this = $(this);
        var checkElement = $this.next();
        var animationSpeed = 300;
        var subMenuSelector = '.sidebar-submenu';

        // Nếu click vào item có submenu
        if (checkElement.is(subMenuSelector) && checkElement.is(':visible')) {
            checkElement.slideUp(animationSpeed, function() {
                checkElement.removeClass('menu-open');
            });
            checkElement.parent("li").removeClass("active");
        }
        else if ((checkElement.is(subMenuSelector)) && (!checkElement.is(':visible'))) {
            var parent = $this.parents('ul').first();
            var ul = parent.find('ul:visible').slideUp(animationSpeed);
            ul.removeClass('menu-open');
            var parent_li = $this.parent("li");

            checkElement.slideDown(animationSpeed, function() {
                checkElement.addClass('menu-open');
                parent.find('li.active').removeClass('active');
                parent_li.addClass('active');
            });
        }

        // Ngăn chặn link mặc định nếu là submenu toggle (javascript:void(0))
        if (checkElement.is(subMenuSelector)) {
            e.preventDefault();
        }
    });

    // 2. Hàm khởi tạo trạng thái (Active menu, SimpleBar...)
    function initApp() {
        var currentPageUrl = window.location.href;
        
        // Reset active cũ
        $(".sidebar-menu li").removeClass("active");
        $(".sidebar-menu li a").removeClass("active");
        $(".sidebar-submenu").removeClass("menu-open").hide();

        // Tìm link active chính xác
        var $currentLink = $('.sidebar-menu a').filter(function() {
            return this.href === currentPageUrl;
        });

        // Fallback: Nếu không tìm thấy chính xác, thử tìm theo segment cuối (bỏ query string)
        if ($currentLink.length === 0) {
            let currentLinkParts = currentPageUrl.split("/");
            let lastPart = currentLinkParts[currentLinkParts.length - 1].split("?")[0];
            if(lastPart) {
                $currentLink = $('.sidebar-menu a[href*="' + lastPart + '"]').first();
            }
        }

        // Active item và mở menu cha
        if ($currentLink.length > 0) {
            $currentLink.addClass("active");
            $currentLink.parents('.sidebar-submenu').addClass('menu-open').show();
            $currentLink.parents('li').addClass('active');
        }
        
        // Re-init SimpleBar
        if($("#sidebar_menus").length > 0) {
             new SimpleBar($("#sidebar_menus")[0]);
        }
        if($("#scrollModal").length > 0) {
             new SimpleBar($("#scrollModal")[0]);
        }
        
        screenWidth();
    }

    function screenWidth() {
        if ($(window).width() < 1281) {
            $("#menuCollapse").hide();
            $(".app-header").addClass("margin-0");
            $(".site-footer ").addClass("margin-0");
            $("#content_wrapper").addClass("margin-0");
            $(".sidebarCloseIcon").show();
            $("#sidebar_type").hide();
            $("#bodyOverlay").addClass("block");
        } else {
            // $("#menuCollapse").show();
            $(".app-header").removeClass("margin-0");
            $(".site-footer").removeClass("margin-0");
            $("#content_wrapper").removeClass("margin-0");
            $(".sidebarCloseIcon").hide();
            $("#sidebar_type").show();
            $("#bodyOverlay").removeClass("block");
        }
    }

    screenWidth();
    $(window).resize(function() {
        screenWidth();
    });

    /*===================================
       Dark and light theme change
      =====================================*/
    let currentTheme = localStorage.getItem("theme");
    const themes = [{
            name: "dark",
            class: "dark",
            checked: false,
        },
        {
            name: "semiDark",
            class: "semiDark",
            checked: false,
        },
        {
            name: "light",
            class: "light",
            checked: false,
        },
    ];

    // Loop through themes and add event listener for changes
    themes.forEach((theme) => {
        const radioBtn = $(`#${theme.class}`);
        radioBtn.prop("checked", theme.name === currentTheme);
        radioBtn.on("change", function() {
            if (this.checked) {
                currentTheme = theme.name;
                localStorage.theme = theme.name;
                location.reload();
            }
        });
    });

    // Theme Change by Header Button
    $("#themeMood").on("click", function() {
        if (currentTheme === "light") {
            currentTheme = "dark";
        } else {
            currentTheme = "light";
        }
        localStorage.theme = currentTheme;
        location.reload();
    });

    $("#grayScale").on("click", function() {
        if ($("html").hasClass("grayScale")) {
            $("html").removeClass("grayScale");
            localStorage.effect = "";
        } else {
            $("html").addClass("grayScale");
            localStorage.effect = "grayScale";
        }
    });

    /*===================================
       Layout Changer
      =====================================*/
    // Sidebar Type Local Storage save
    if (localStorage.sideBarType == "extend") {
        $(".app-wrapper").addClass(localStorage.sideBarType);
    } else if (localStorage.sideBarType == "collapsed") {
        $(".app-wrapper").removeClass("extend").addClass("collapsed");
        $("#menuCollapse input[type=checkbox]").prop("checked", true);
    }
    // Header Area Toggle switch
    $("#sidebar_type").on("click", function() {
        if ($(".app-wrapper").hasClass("collapsed")) {
            $(".app-wrapper").removeClass("collapsed").addClass("extend");
            $("#menuCollapse input[type=checkbox]").prop("checked", false);
            localStorage.sideBarType = "extend";
        } else {
            $(".app-wrapper").removeClass("extend").addClass("collapsed");
            $("#menuCollapse input[type=checkbox]").prop("checked", true);
            localStorage.sideBarType = "collapsed";
        }
    });

    $(".sidebarOpenButton").on("click", function() {
        $(".app-wrapper").removeClass("collapsed").addClass("extend");
        $("#menuCollapse input[type=checkbox]").prop("checked", false);
        localStorage.sideBarType = "extend";
    });

    // Settings Area Toggle Switch
    $("#menuCollapse input[type=checkbox]").on("click", function() {
        if ($("#menuCollapse input[type=checkbox]").is(":checked")) {
            $(".app-wrapper").removeClass("extend").addClass("collapsed");
            localStorage.sideBarType = "collapsed";
        } else {
            $(".app-wrapper").removeClass("collapsed").addClass("extend");
            localStorage.sideBarType = "extend";
        }
    });

    // Menu Hide and show toggle
    $("#menuHide input[type=checkbox]").on("click", function() {
        if ($("#menuHide input[type=checkbox]").is(":checked")) {
            $(".sidebar-wrapper").addClass("menu-hide");
            $("#menuCollapse").hide();
            $(".app-header").addClass("margin-0");
            $(".site-footer").addClass("margin-0");
            $("#content_wrapper").addClass("margin-0");
        } else {
            $(".sidebar-wrapper").removeClass("menu-hide");
            $("#menuCollapse").show();
            $(".app-header").removeClass("margin-0");
            $(".site-footer").removeClass("margin-0");
            $("#content_wrapper").removeClass("margin-0");
        }
    });

    // Content layout toggle
    if (localStorage.contentLayout == "layout-boxed") {
        $("#boxed").prop("checked", true);
    } else {
        $("#fullWidth").prop("checked", true);
    }

    // Content layout Changing options
    $("#fullWidth").on("change", function() {
        $("html").removeClass("layout-boxed");
        localStorage.contentLayout = "layout-full";
    });
    $("#boxed").on("change", function() {
        $("html").addClass("layout-boxed");
        localStorage.contentLayout = "layout-boxed";
    });

    // Menu Layout toggle
    if (localStorage.menuLayout == "horizontalMenu") {
        $("#horizontal_menu").prop("checked", true);
    } else {
        $("#vertical_menu").prop("checked", true);
    }

    // Menu Layout Changing options
    $("#vertical_menu").on("change", function() {
        $("html").removeClass("horizontalMenu");
        localStorage.menuLayout = "";
    });
    $("#horizontal_menu").on("change", function() {
        $("html").addClass("horizontalMenu");
        localStorage.menuLayout = "horizontalMenu";
    });

    // Header Area styles

    // Check local storage and set Header Style
    if (localStorage.navbar == "floating") {
        $("#nav_" + localStorage.navbar).prop("checked", true);
    } else if (localStorage.navbar == "sticky top-0") {
        $("#nav_sticky").prop("checked", true);
    } else if (localStorage.navbar == "hidden") {
        $("#nav_" + localStorage.navbar).prop("checked", true);
    } else {
        $("#nav_static").prop("checked", true);
    }
    // Header Changing options
    $("#nav_floating").on("change", function() {
        $("html")
            .removeClass("nav-sticky")
            .removeClass("nav-hidden")
            .removeClass("nav-static")
            .addClass("nav-floating");
        localStorage.navbar = "floating";
    });
    $("#nav_sticky").on("change", function() {
        $("html")
            .removeClass("nav-floating")
            .removeClass("nav-hidden")
            .removeClass("nav-static")
            .addClass("nav-sticky");
        localStorage.navbar = "sticky";
    });
    $("#nav_static").on("change", function() {
        $("html")
            .removeClass("nav-floating")
            .removeClass("nav-hidden")
            .removeClass("nav-sticky")
            .addClass("nav-static");
        localStorage.navbar = "static";
    });
    $("#nav_hidden").on("change", function() {
        $("html")
            .removeClass("nav-floating")
            .removeClass("nav-static")
            .removeClass("nav-sticky")
            .addClass("nav-hidden");
        localStorage.navbar = "hidden";
    });

    // Footer Area
    // Check local storage and set Footer Style
    if (localStorage.footer == "sticky bottom-0") {
        $("#footer").addClass(localStorage.footer);
        $("#footer_sticky").prop("checked", true);
    } else if (localStorage.footer == "hidden") {
        $("#footer").addClass(localStorage.footer);
        $("#footer_hidden").prop("checked", true);
    } else {
        $("#footer").addClass("static");
        $("#footer_static").prop("checked", true);
    }
    // Footer Changing options
    $("#footer_static").on("change", function() {
        $("#footer")
            .removeClass("sticky bottom-0")
            .removeClass("hidden")
            .addClass("static");
        localStorage.footer = "static";
    });
    $("#footer_sticky").on("change", function() {
        $("#footer")
            .removeClass("static")
            .removeClass("hidden")
            .addClass("sticky bottom-0");
        localStorage.footer = "sticky bottom-0";
    });
    $("#footer_hidden").on("change", function() {
        $("#footer")
            .removeClass("sticky bottom-0")
            .removeClass("static")
            .addClass("hidden");
        localStorage.footer = "hidden";
    });

    // RTL and LTR
    // Direction Type Local Storage
    if (localStorage.dir == "rtl") {
        $("#rtl_ltr input[type=checkbox]").prop("checked", true);
        $("#offcanvas").removeClass("offcanvas-end");
        $("#offcanvas").addClass("offcanvas-start");
    }

    // Change Direction
    $("#rtl_ltr input[type=checkbox]").on("click", function() {
        if ($("#rtl_ltr input[type=checkbox]").is(":checked")) {
            $("html").attr("dir", "rtl");
            localStorage.dir = "rtl";
            location.reload();
        } else {
            $("html").attr("dir", "ltr");
            localStorage.dir = "ltr";
            location.reload();
        }
    });

    /* =============================
      Small Device Buttons function
      ===============================*/
    $(document).on("click", ".smallDeviceMenuController", function() {
        $(".sidebar-wrapper").addClass("sidebar-open");
        $("#bodyOverlay").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    $(document).on("click", ".sidebarCloseIcon, #bodyOverlay", function() {
        $(".sidebar-wrapper").removeClass("sidebar-open");
        $("#bodyOverlay").addClass("hidden");
        $("body").removeClass("overflow-hidden");
    });

    // Password Show Hide Toggle
    $("#toggleIcon").on("click", function() {
        let x = $(".passwordfield").attr("type");
        if (x === "password") {
            $(".passwordfield").prop("type", "text");
            $("#hidePassword").hide();
            $("#showPassword").show();
        } else {
            $(".passwordfield").prop("type", "password");
            $("#showPassword").hide();
            $("#hidePassword").show();
        }
    });

    // Getting the Current Year
    $("#thisYear").text(new Date().getFullYear());

    // Progress bar
    $(".progress-bar").animate({
            width: "40%",
        },
        2500
    );
    $(".progress-bar2").animate({
            width: "50%",
        },
        2500
    );
    $(".progress-bar3").animate({
            width: "60%",
        },
        2500
    );
    $(".progress-bar4").animate({
            width: "75%",
        },
        2500
    );
    $(".progress-bar5").animate({
            width: "95%",
        },
        2500
    );
    $(".progress-bar6").animate({
            width: "25%",
        },
        2500
    );

    /*===================================
       Plugin initialization
      =====================================*/
    
    // Chạy lần đầu
    initApp();

    // Chạy lại khi Livewire điều hướng (SPA)
    document.addEventListener('livewire:navigated', () => {
        initApp();
        // Reset trạng thái overlay nếu đang mở trên mobile
        $(".sidebar-wrapper").removeClass("sidebar-open");
        $("#bodyOverlay").addClass("hidden");
        $("body").removeClass("overflow-hidden");
    });

})(jQuery);