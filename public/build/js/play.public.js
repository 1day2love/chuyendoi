// Episode Manager v2 (Search + Sort + Toggle + Max List)
(function () {
  // --- Hook DOM theo đúng markup đang có ---
  const manager    = document.getElementById('episode-manager');        // khối control (search/sort/toggle)
  const listServer = document.getElementById('list-server');            // KHỐI CHÍNH: nơi CSS áp dụng grid-view
  if (!manager || !listServer) return;

  // Mỗi server-group chứa .episodes (danh sách <li><a>...</a></li>)
  const episodeLists = () => Array.from(listServer.querySelectorAll('.episodes'));
  const episodeItems = () =>
    episodeLists().flatMap(ul => Array.from(ul.querySelectorAll('li')));

  // Control elements
  const totalEl    = manager.querySelector('.total-episodes');
  const searchEl   = manager.querySelector('#episode-search');
  const sortEl     = manager.querySelector('#episode-sort');      // asc | desc
  const toggleBtn  = manager.querySelector('#view-toggle');
  const toggleIcon = toggleBtn?.querySelector('i');

  // --- Helper: lấy số tập từ <li> (bắt số trong text/slugs/data-*) ---
  function getEpisodeNumber(li) {
    const d = li.dataset || {};
    const rawNum = d.ep || d.episode || d.num || li.getAttribute('data-ep') || li.textContent || '';
    const m = String(rawNum).match(/(\d{1,4})/);
    const num = m ? parseInt(m[1], 10) : 9999;

    const text = (li.textContent || '').toLowerCase();

    const rest = text.replace(/tập|tap|episode|ep/g, '').replace(/[\s._\-:#]+/g, '').replace(/\d+/g, '');
    const hasLetter = /[a-zA-ZÀ-ỹ]/.test(rest);

     return (hasLetter ? 10000 : 0) + num;
  }

  // --- Total counter: đếm số li đang hiển thị ---
  function updateTotal() {
  if (!totalEl) return;

  const lists = episodeLists(); // mỗi UL .episodes
  let maxCount = 0;
  let maxVisible = 0;

  lists.forEach(ul => {
    const items = Array.from(ul.querySelectorAll('li'));
    const total = items.length;

    const visible = items.filter(li => {
      if (li.style.display === 'none') return false;
      return li.offsetParent !== null;
    }).length;

    // lấy UL có số tập nhiều nhất
    if (total > maxCount) {
      maxCount = total;
      maxVisible = visible;
    }
  });

  // Nếu có lọc search → hiển thị (xx tập đã lọc)
  if (maxVisible < maxCount) {
    totalEl.textContent = `${maxVisible} Tập (đã lọc)`;
  } else {
    totalEl.textContent = `${maxCount} Tập`;
  }
}
  // --- Search theo text (áp dụng trên toàn bộ #list-server) ---
  function onSearch() {
    const q = (searchEl?.value || '').trim().toLowerCase();
    episodeItems().forEach(li => {
      const text = (li.textContent || '').toLowerCase();
      li.style.display = !q || text.includes(q) ? '' : 'none';
    });
    updateTotal();
  }

  // --- Sort: gộp tất cả <li>, sắp xếp rồi gắn lại theo thứ tự ---
  function onSort() {
    const dir = (sortEl?.value || 'asc').toLowerCase(); // asc/desc
    // Gom hết items theo từng UL để giữ lại cấu trúc UL, nhưng reorder theo UL hiện tại
    episodeLists().forEach(ul => {
      const items = Array.from(ul.querySelectorAll('li'));
      items.sort((a, b) => {
        const na = getEpisodeNumber(a);
        const nb = getEpisodeNumber(b);
        if (Number.isFinite(na) && Number.isFinite(nb) && na !== nb) {
          return na - nb;
        }
        return (a.textContent || '').localeCompare(b.textContent || '', 'vi');
      });
      if (dir === 'desc') items.reverse();

      const frag = document.createDocumentFragment();
      items.forEach(li => frag.appendChild(li));
      ul.appendChild(frag);
    });
  }

  // --- Toggle view (list ↔ grid): toggle class TRÊN #list-server để ăn CSS ---
  function onToggleView() {
    listServer.classList.toggle('grid-view');
    // Đổi icon: fa-th <-> fa-th-list (nếu dùng Font Awesome)
    if (toggleIcon) {
      if (toggleIcon.classList.contains('fa-th')) {
        toggleIcon.classList.remove('fa-th');
        toggleIcon.classList.add('fa-th-list');
      } else {
        toggleIcon.classList.remove('fa-th-list');
        toggleIcon.classList.add('fa-th');
      }
    }
    // Active style cho nút
    toggleBtn?.classList.toggle('active');
    // (tuỳ chọn) nhớ trạng thái
    try { localStorage.setItem('episode_view_grid', listServer.classList.contains('grid-view') ? '1' : '0'); } catch {}
  }

  // --- Bind events ---
  searchEl?.addEventListener('input', onSearch);
  sortEl?.addEventListener('change', () => { onSort(); updateTotal(); });
  toggleBtn?.addEventListener('click', onToggleView);

  // --- Khởi tạo ---
  // Khôi phục view toggle (nếu đã lưu)
  
  onSort();
  onSearch();
  updateTotal();
})();
// ========================================
// Clipboard Copy Function
// ========================================

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('Đã copy link!');
            } else {
                alert('Đã copy link!');
            }
            toggleSharePopup();
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        if (typeof toastr !== 'undefined') {
            toastr.success('Đã copy link!');
        }
        toggleSharePopup();
    }
}

// ========================================
// Share Popup Toggle
// ========================================

function toggleSharePopup() {
    const popup = document.getElementById('sharePopup');
    if (!popup) return;

    const isHidden = popup.style.display === 'none' || popup.style.display === '';
    popup.style.display = isHidden ? 'block' : 'none';

    // Khi popup mở thì thêm listener tạm
    if (isHidden) {
        const handleOutsideClick = (e) => {
            const btn = document.querySelector('.share-btn');
            if (!popup.contains(e.target) && !btn.contains(e.target)) {
                popup.style.display = 'none';
                document.removeEventListener('click', handleOutsideClick);
            }
        };
        // Hoãn 1 tick để không bắt click của chính nút
        setTimeout(() => document.addEventListener('click', handleOutsideClick), 0);
    }
}

// ========================================
// Scroll to Section
// ========================================

function scrollToSection(selector) {
    $('html, body').animate({
        scrollTop: $(selector).offset().top - 100
    }, 500);
}

// ========================================
// Episode Manager Initialization
// ========================================

function initEpisodeManager() {
    const totalEpisodes = $('.episodes li').length;
    
    $('.total-episodes').text(totalEpisodes + ' Tập');
    
    if (totalEpisodes <= 0) {
        $('#episode-manager').hide();
        $('#fab-episodes-btn').hide();
        $('#inline-quality-btn').hide();
    }
    
    setTimeout(function() {
        const episodeList = $('#nav-episodes').html();
        if (episodeList && episodeList.find('li:visible').length > 0) {
            $('#episode-server').show();
            $('#inline-quality-btn').show();
        }
    }, 100);
    
    $('#list-server .server-option li').text(totalEpisodes + ' Tập');
}

// ========================================
// Server View Toggle
// ========================================

$(document).on('click', '.server-option', function() {
    $(this).removeClass('active');
    $('.server-option').removeClass('disabled');
    
    if ($('.server-option').hasClass('disabled')) {
        $(this).find('i').removeClass('fa-th-list').addClass('fa-th');
        $(this).attr('title', 'Grid view');
    } else {
        $(this).find('i').removeClass('fa-th').addClass('fa-th-list');
        $(this).attr('title', 'List view');
    }
});

// Reverse episode list on click
$(document).on('click', '.btn-link-reverse', function() {
    const container = $(this).parent();
    const episodes = container.find('.episodes');
    
    if (container === 'reverse') {
        episodes.reverse();
    }
    
    $.each(episodes, function(index, episode) {
        container.find('.episodes').append(episode);
    });
});

// ========================================
// Cookie Functions
// ========================================

function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(cname) {
    const name = cname + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) != -1) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
// Hiển thị / ẩn menu FAB inline
function toggleInlineFAB() {
    const menu = document.getElementById('inlineFabMenu');
    const btn = document.getElementById('inlineFabBtn');
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
        btn.classList.add('active');
    } else {
        menu.style.display = 'none';
        btn.classList.remove('active');
    }
}
// Bật / tắt chế độ tự chuyển tập
function toggleAutoNextInline() {
    window.autonext = !window.autonext;
    const label = document.getElementById('inline-autonext-text');
    if (label) label.innerText = 'Auto: ' + (autonext ? 'ON' : 'OFF');
}

// Bật / tắt chế độ đèn (Dark mode xem phim)
function toggleLamp() {
    var elements = document.querySelectorAll('.power-lamp');
    var textLabels = document.querySelectorAll('.text-lamp-inline');

    elements.forEach(function(el) {
        if (el.classList.contains('off')) {
            // Bật đèn → gỡ overlay
            el.classList.remove('off');

            // Xóa tất cả overlay hiện có
            document.querySelectorAll('#background_lamp').forEach(o => o.remove());

        } else {
            // Tắt đèn → thêm 2 overlay
            el.classList.add('off');

            for (let i = 0; i < 2; i++) {
                let overlay = document.createElement('div');
                overlay.id = 'background_lamp';
                document.body.appendChild(overlay);
            }
        }
    });
    // Đổi chữ hiển thị
    textLabels.forEach(function(label) {
        label.textContent = (label.textContent === 'Tắt đèn') ? 'Bật đèn' : 'Tắt đèn';
    });
}

// Tắt quảng cáo thủ công
function removeAds() {
    // Các class hoặc ID của phần tử quảng cáo
    var adSelectors = ".ad-container, .ad-banner, div.gnarty-offads, .qc-banner, .qc-box";
    // Xoá toàn bộ quảng cáo tìm thấy
    document.querySelectorAll(adSelectors).forEach(function(el) {
        el.remove();
    });
    // Nếu có popup QC (ví dụ id="closeAds"), xoá luôn
    var popup = document.getElementById("closeAds");
    if (popup) popup.remove();
    // Xoá luôn chính nút “Tắt QC” (class .off-ads hoặc có onclick chứa removeAds)
    document.querySelectorAll(".off-ads, [onclick*='removeAds']").forEach(function(btn) {
        btn.remove();
    });
}