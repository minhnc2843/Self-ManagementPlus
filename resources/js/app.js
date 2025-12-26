import "./bootstrap";
import jQuery from "jquery";
import "tw-elements";
import SimpleBar from "simplebar";
import "simplebar/dist/simplebar.css";
import "animate.css";
import ResizeObserver from "resize-observer-polyfill";
import "country-select-js";
import "iconify-icon";
import Swal from "sweetalert2";
import tippy from "tippy.js";
import "tippy.js/dist/tippy.css";
import cleave from 'cleave.js';
import validate from "jquery-validation";

window.$ = jQuery;
window.jQuery = jQuery;
window.SimpleBar = SimpleBar;
window.ResizeObserver = ResizeObserver;
window.Swal = Swal;
window.tippy = tippy;
window.cleave = cleave;
window.validate = validate;

// Dynamic Import cho FullCalendar
if (document.querySelector('.calendar-element') || document.querySelector('#calendar')) {
    Promise.all([
        import('@fullcalendar/core'),
        import('@fullcalendar/daygrid'),
        import('@fullcalendar/timegrid'),
        import('@fullcalendar/list')
    ]).then(([{ Calendar }, dayGridPlugin, timeGridPlugin, listPlugin]) => {
        window.Calendar = Calendar;
        window.dayGridPlugin = dayGridPlugin.default;
        window.timeGridPlugin = timeGridPlugin.default;
        window.listPlugin = listPlugin.default;
        // Khởi tạo calendar tại đây hoặc bắn event để file js khác bắt
    });
}

// Dynamic Import cho Chart.js và ApexCharts
if (document.querySelectorAll('canvas').length > 0 || document.querySelector('.apex-charts')) {
    import('chart.js').then((Chart) => {
        window.Chart = Chart.default;
    });
    import('apexcharts').then((ApexCharts) => {
        window.ApexCharts = ApexCharts.default;
    });
}

// Dynamic Import cho Leaflet
if (document.querySelector('#map') || document.querySelector('.map-container')) {
    import('leaflet').then((leaflet) => {
        window.leaflet = leaflet.default;
    });
}

// Dynamic Import cho Dragula
if (document.querySelector('.dragula-container')) {
    import('dragula/dist/dragula').then((dragula) => {
        import('dragula/dist/dragula.css');
        window.dragula = dragula.default;
    });
}

// Dynamic Import cho DataTable
if (document.querySelector('.data-table')) {
    import('datatables.net-dt').then((DataTable) => {
        window.DataTable = DataTable.default;
    });
}

import.meta.glob(["../images/**"]);
// Đảm bảo bạn đã import Echo và cấu hình trong bootstrap.js
// Đoạn này lắng nghe kênh private của user hiện tại

// Lấy User ID từ thẻ meta trong head (cần thêm thẻ này vào file blade layout nếu chưa có: <meta name="user-id" content="{{ auth()->id() }}">)
const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');

if (userId) {
    window.Echo.private('App.Models.User.' + userId)
        .notification((notification) => {
            console.log('Có thông báo mới:', notification);

            // 1. Cập nhật số lượng thông báo trên chuông (Badge)
            updateNotificationCount();

            // 2. Hiển thị Toast/Popup nhỏ góc màn hình (Dùng thư viện Toastify hoặc SweetAlert mà theme bạn có)
            if(window.Swal) {
                const Toast = window.Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                
                Toast.fire({
                    icon: 'info',
                    title: notification.message
                });
            }

            // 3. (Tùy chọn) Append HTML vào danh sách dropdown thông báo nếu người dùng đang mở nó
            prependNotificationToDropdown(notification);
        });
}

function updateNotificationCount() {
    // Tìm element hiển thị số đếm và tăng lên 1
    const badges = document.querySelectorAll('.notification-badge'); // Thay class theo theme của bạn
    badges.forEach(badge => {
        let count = parseInt(badge.innerText) || 0;
        badge.innerText = count + 1;
        badge.style.display = 'block';
    });
}

function prependNotificationToDropdown(data) {
    const list = document.querySelector('#notification-dropdown-list'); // ID của UL danh sách thông báo
    if(list) {
        const html = `
            <li>
                <a href="${data.url}" class="block px-4 py-2 hover:bg-gray-100">
                    <div class="text-sm font-medium text-gray-900">${data.message}</div>
                    <div class="text-xs text-gray-500">Vừa xong</div>
                </a>
            </li>
        `;
        list.insertAdjacentHTML('afterbegin', html);
    }
}