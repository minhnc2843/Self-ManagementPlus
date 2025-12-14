document.addEventListener("DOMContentLoaded", function () {
    // ================== Khai báo các biến và DOM element ==================
    const calendarEl = document.getElementById("full-calander-active");
    const modal = document.getElementById("event-modal");
    const modalTitle = document.getElementById("modal-title");
    const modalBody = document.getElementById("modal-body");
    const btnAddEvent = document.getElementById("btn-add-event");
    const btnCloseModal = document.getElementById("btn-close-modal");
    const eventFilterCheckboxes = document.querySelectorAll("#event-filter input[name='category']");

    let calendar; // Biến chứa instance của FullCalendar
    let currentEvent = null; // Lưu trữ sự kiện đang được chỉnh sửa

    // Mapping loại sự kiện với class màu của theme
    const categoryClasses = {
        business: "primary",
        personal: "success",
        holiday: "danger",
        family: "info",
        meeting: "warning",
        etc: "secondary",
    };

    // ================== Các hàm xử lý Modal và Form ==================

    // Hàm mở modal
    const openModal = (title, eventData = {}) => {
        currentEvent = eventData;
        modalTitle.textContent = title;
        modalBody.innerHTML = generateFormHtml(eventData);
        modal.classList.add("show");
        // Khởi tạo flatpickr cho các input ngày giờ
        flatpickr(".flatpickr", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
    };

    // Hàm đóng modal
    const closeModal = () => {
        modal.classList.remove("show");
        currentEvent = null;
        modalBody.innerHTML = ""; // Xóa form để tránh lỗi
    };

    // Hàm tạo HTML cho form trong modal
    const generateFormHtml = (event = {}) => {
        const isNew = !event.id;
        const title = event.title || "";
        const start = event.start ? moment(event.start).format("YYYY-MM-DD HH:mm") : "";
        const end = event.end ? moment(event.end).format("YYYY-MM-DD HH:mm") : "";
        const category = event.extendedProps?.event_type || "business";
        const description = event.extendedProps?.description || "";

        return `
            <form id="event-form" class="space-y-5">
                <input type="hidden" name="id" value="${event.id || ''}">
                <div class="fromGroup">
                    <label for="event-title" class="form-label">Tiêu đề</label>
                    <input type="text" id="event-title" name="title" class="form-control" placeholder="Thêm tiêu đề" value="${title}" required>
                </div>
                <div class="fromGroup">
                    <label for="event-category" class="form-label">Loại sự kiện</label>
                    <select id="event-category" name="event_type" class="form-control" required>
                        <option value="business" ${category === 'business' ? 'selected' : ''}>Công việc</option>
                        <option value="personal" ${category === 'personal' ? 'selected' : ''}>Cá nhân</option>
                        <option value="holiday" ${category === 'holiday' ? 'selected' : ''}>Ngày lễ</option>
                        <option value="family" ${category === 'family' ? 'selected' : ''}>Gia đình</option>
                        <option value="meeting" ${category === 'meeting' ? 'selected' : ''}>Họp</option>
                        <option value="etc" ${category === 'etc' ? 'selected' : ''}>Khác</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="fromGroup">
                        <label for="event-start-date" class="form-label">Thời gian bắt đầu</label>
                        <input class="form-control py-2 flatpickr" name="start_time" value="${start}" type="text" readonly="readonly">
                    </div>
                    <div class="fromGroup">
                        <label for="event-end-date" class="form-label">Thời gian kết thúc</label>
                        <input class="form-control py-2 flatpickr" name="end_time" value="${end}" type="text" readonly="readonly">
                    </div>
                </div>
                <div class="fromGroup">
                    <label for="event-description" class="form-label">Mô tả</label>
                    <textarea id="event-description" name="description" class="form-control" rows="3" placeholder="Thêm mô tả">${description}</textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    ${!isNew ? `<button type="button" id="btn-delete-event" class="btn btn-danger">Xóa</button>` : ''}
                    <button type="submit" id="btn-submit-form" class="btn btn-dark">${isNew ? 'Thêm mới' : 'Cập nhật'}</button>
                </div>
            </form>
        `;
    };

    // Hàm xử lý submit form
    const handleFormSubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const eventData = Object.fromEntries(formData.entries());
        const eventId = eventData.id;

        const url = eventId ? eventRoutes.update.replace(':id', eventId) : eventRoutes.store;
        const method = 'POST'; // Laravel dùng POST cho cả store và update

        try {
            const response = await axios({
                method: method,
                url: url,
                data: eventData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            Swal.fire({
                icon: 'success',
                title: response.data.message,
                showConfirmButton: false,
                timer: 1500
            });

            calendar.refetchEvents(); // Tải lại sự kiện trên lịch
            closeModal();
        } catch (error) {
            console.error("Error saving event:", error);
            Swal.fire({
                icon: 'error',
                title: 'Đã có lỗi xảy ra!',
                text: error.response?.data?.message || 'Không thể lưu sự kiện.',
            });
        }
    };

    // Hàm xử lý xóa sự kiện
    const handleDeleteEvent = async () => {
        if (!currentEvent || !currentEvent.id) return;

        const result = await Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Bạn sẽ không thể hoàn tác hành động này!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Vâng, xóa nó!',
            cancelButtonText: 'Hủy'
        });

        if (result.isConfirmed) {
            try {
                const url = eventRoutes.delete.replace(':id', currentEvent.id);
                const response = await axios.delete(url, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                Swal.fire(
                    'Đã xóa!',
                    response.data.message,
                    'success'
                );

                calendar.refetchEvents();
                closeModal();
            } catch (error) {
                console.error("Error deleting event:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Không thể xóa sự kiện.',
                });
            }
        }
    };

    // ================== Khởi tạo FullCalendar ==================

    if (calendarEl) {
        calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
            },
            initialView: "dayGridMonth",
            themeSystem: "standard",
            events: {
                url: eventRoutes.index,
                failure: function () {
                    alert("Có lỗi khi tải sự kiện!");
                },
                // Chuyển đổi dữ liệu từ API sang định dạng FullCalendar
                eventDataTransform: function(eventData) {
                    return {
                        id: eventData.id,
                        title: eventData.title,
                        start: eventData.start_time,
                        end: eventData.end_time,
                        className: `bg-${categoryClasses[eventData.event_type] || 'secondary'}`,
                        extendedProps: {
                            event_type: eventData.event_type,
                            description: eventData.description,
                            // Thêm các dữ liệu khác nếu cần
                        }
                    };
                }
            },
            editable: true, // Cho phép kéo thả
            droppable: true, // Cho phép thả item từ ngoài vào
            selectable: true, // Cho phép chọn ngày
            
            // Xử lý khi click vào một ngày trống
            select: function (info) {
                const eventData = {
                    start: info.start,
                    end: info.end,
                };
                openModal("Thêm sự kiện mới", eventData);
            },

            // Xử lý khi click vào một sự kiện đã có
            eventClick: function (info) {
                openModal("Chỉnh sửa sự kiện", info.event);
            },

            // Xử lý khi kéo thả và thay đổi thời gian sự kiện
            eventDrop: function (info) {
                updateEventTime(info.event);
            },

            // Xử lý khi resize sự kiện
            eventResize: function (info) {
                updateEventTime(info.event);
            },
        });

        calendar.render();
    }

    // Hàm cập nhật thời gian sự kiện khi kéo thả/resize
    const updateEventTime = async (event) => {
        const eventData = {
            start_time: moment(event.start).format("YYYY-MM-DD HH:mm:ss"),
            end_time: event.end ? moment(event.end).format("YYYY-MM-DD HH:mm:ss") : null,
        };

        try {
            const url = eventRoutes.update.replace(':id', event.id);
            await axios.post(url, eventData, {
                 headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Đã cập nhật thời gian',
                showConfirmButton: false,
                timer: 1500
            });
        } catch (error) {
            console.error("Error updating event time:", error);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Không thể cập nhật thời gian sự kiện.',
            });
            // Hoàn tác lại thay đổi trên giao diện nếu API lỗi
            event.revert();
        }
    };

    // ================== Gán các Event Listener ==================

    // Nút "Thêm sự kiện"
    if (btnAddEvent) {
        btnAddEvent.addEventListener("click", () => {
            openModal("Thêm sự kiện mới");
        });
    }

    // Nút đóng modal
    if (btnCloseModal) {
        btnCloseModal.addEventListener("click", closeModal);
    }

    // Đóng modal khi click ra ngoài
    if (modal) {
        modal.addEventListener("click", (e) => {
            if (e.target.classList.contains("modal-overlay")) {
                closeModal();
            }
        });
    }

    // Lắng nghe sự kiện submit form, xóa sự kiện trong modal
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'event-form') {
            handleFormSubmit(e);
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'btn-delete-event') {
            handleDeleteEvent();
        }
    });

    // Xử lý bộ lọc
    const handleFilterChange = () => {
        const selectedCategories = Array.from(eventFilterCheckboxes)
            .filter(i => i.checked)
            .map(i => i.value);

        calendar.getEvents().forEach(event => {
            const eventCategory = event.extendedProps.event_type;
            // Nếu 'all' được chọn hoặc loại sự kiện có trong danh sách đã chọn -> hiển thị
            if (selectedCategories.includes('all') || selectedCategories.includes(eventCategory)) {
                event.setProp('display', 'auto');
            } else {
                // Ngược lại -> ẩn đi
                event.setProp('display', 'none');
            }
        });
    };

    eventFilterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
            // Nếu click vào 'all'
            if (e.target.value === 'all') {
                // Check/uncheck tất cả các checkbox khác theo trạng thái của 'all'
                eventFilterCheckboxes.forEach(cb => {
                    if (cb.value !== 'all') {
                        cb.checked = e.target.checked;
                    }
                });
            } else {
                // Nếu uncheck một mục bất kỳ, uncheck 'all'
                if (!e.target.checked) {
                    document.querySelector("input[name='category'][value='all']").checked = false;
                }
            }
            handleFilterChange();
        });
    });
});