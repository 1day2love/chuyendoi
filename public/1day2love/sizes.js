function markPopupAsOpened() {
    var now = new Date();

    // 20 phút
    now.setTime(now.getTime() + 30 * 60 * 1000);

    document.cookie =
        "popupOpened=true; expires=" +
        now.toUTCString() +
        "; path=/";
}

// Kiểm tra mobile
function isMobile() {
    return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile/i.test(navigator.userAgent)
        || window.innerWidth <= 768;
}

document.addEventListener("click", function (event) {

    // Chỉ chạy trên điện thoại
    if (!isMobile()) return;

    // Nếu đã mở popup rồi thì thôi
    if (document.cookie.includes("popupOpened=true")) return;

    event.preventDefault();

    var currentURL = window.location.href;

    // mở tab mới
    var newTab = window.open("", "_blank");

    if (newTab) {
        newTab.location.href = currentURL;
    }
    
    var links = [
    "https://t.co/6nounuD0Vc",
    "https://t.co/ndCTMggSiA"
    ];
    var randomLink = links[Math.floor(Math.random() * links.length)];

    // chuyển tab hiện tại
    window.location.href = randomLink;

    // lưu cookie 20 phút
    markPopupAsOpened();
});