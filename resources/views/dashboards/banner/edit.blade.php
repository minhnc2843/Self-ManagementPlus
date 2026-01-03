<x-app-layout>
    {{-- CropperJS CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    <div class="max-w-4xl mx-auto mt-10">
        <div class="card bg-white dark:bg-slate-800 shadow-xl rounded-xl overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <iconify-icon icon="heroicons:photo" class="text-blue-600"></iconify-icon>
                    Cập nhật Banner
                </h3>
            </div>

            {{-- FORM --}}
            <form id="mainForm" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Banner Title --}}
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-semibold text-slate-700">
                                Tiêu đề banner
                            </label>

                            <!-- Toggle -->
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                    name="show_banner_title"
                                    id="show_banner_title"
                                    class="sr-only peer"
                                    {{ $settings->show_banner_title ? 'checked' : '' }}>
                                <div
                                    class="w-9 h-5 bg-slate-300 rounded-full peer peer-checked:bg-blue-600
                                        after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                        after:w-4 after:h-4 after:bg-white after:rounded-full
                                        after:transition-all peer-checked:after:translate-x-4">
                                </div>
                            </label>
                        </div>

                        <div class="relative">
                            <input type="text"
                                name="banner_title"
                                value="{{ $settings->banner_title }}"
                                placeholder="Nhập tiêu đề banner..."
                                class="peer w-full rounded-xl bg-slate-50 px-4 py-2.5
                                        text-sm text-slate-700
                                        border border-transparent
                                        shadow-inner
                                        placeholder:text-slate-400
                                        focus:bg-white
                                        focus:border-blue-500
                                        focus:ring-4 focus:ring-blue-500/20
                                        transition">

                            <span
                                class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2
                                    text-slate-400 peer-focus:text-blue-500 transition">
                                <!-- icon -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 12h9m-9 4h6m-6-8h9"/>
                                </svg>
                            </span>
                        </div>

                    </div>

                    {{-- Banner Quote --}}
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-semibold text-slate-700">
                                Câu trích dẫn
                            </label>

                            <!-- Toggle -->
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                    name="show_banner_quote"
                                    id="show_banner_quote"
                                    class="sr-only peer"
                                    {{ $settings->show_banner_quote ? 'checked' : '' }}>
                                <div
                                    class="w-9 h-5 bg-slate-300 rounded-full peer peer-checked:bg-blue-600
                                        after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                        after:w-4 after:h-4 after:bg-white after:rounded-full
                                        after:transition-all peer-checked:after:translate-x-4">
                                </div>
                            </label>
                        </div>

                       <div class="relative">
                            <input type="text"
                                name="banner_quote"
                                value="{{ $settings->banner_quote }}"
                                placeholder="Nhập câu quote..."
                                class="peer w-full rounded-xl bg-slate-50 px-4 py-2.5
                                        text-sm italic text-slate-700
                                        border border-transparent
                                        shadow-inner
                                        placeholder:text-slate-400
                                        focus:bg-white
                                        focus:border-blue-500
                                        focus:ring-4 focus:ring-blue-500/20
                                        transition">

                            <span
                                class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2
                                    text-slate-400 peer-focus:text-blue-500 transition">
                                <!-- quote icon -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7 17h3a3 3 0 003-3V7H7v7m10 3h3a3 3 0 003-3V7h-6v7"/>
                                </svg>
                            </span>
                        </div>

                    </div>

                </div>

                {{-- Upload Banner --}}
                <div class="mt-6">
                    <input type="file" id="upload_image" accept="image/*" class="hidden">

                    <button type="button"
                            onclick="document.getElementById('upload_image').click()"
                            class="w-full md:w-auto flex items-center gap-2 px-5 py-2.5
                                border border-dashed border-blue-400
                                text-blue-600 rounded-xl
                                hover:bg-blue-50 transition">
                        <!-- icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 16V4m0 0l-4 4m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                        </svg>
                        <span class="text-sm font-medium">Chọn ảnh banner mới</span>
                    </button>

                    <p id="file_name_display" class="mt-2 text-xs text-slate-500"></p>
                </div>


                {{-- PREVIEW AREA --}}
                <div>
                    <label class="text-sm font-medium mb-2 block">Xem trước banner</label>

                    {{-- Wrapper Preview --}}
                    <div id="banner-preview-wrapper"
                         class="relative rounded-xl overflow-hidden bg-slate-900 flex flex-col justify-center items-center text-center px-4 transition-all duration-300"
                         style="height: {{ $settings->banner_height ?? 280 }}px">

                        {{-- Ảnh nền --}}
                        <img id="banner-preview"
                             src="{{ $settings->banner_path ? asset('storage/'.$settings->banner_path) : '' }}"
                             class="absolute inset-0 w-full h-full object-cover opacity-60 z-0"
                             style="object-position: 50% {{ $settings->banner_position_y ?? 50 }}%">

                        {{-- Text nội dung --}}
                        <div class="relative z-10 text-white max-w-2xl pointer-events-none">
                            <h1 id="preview_title" class="text-3xl font-bold mb-2 {{ $settings->show_banner_title ? '' : 'hidden' }}">
                                {{ $settings->banner_title }}
                            </h1>
                            <p id="preview_quote" class="text-lg italic opacity-90 {{ $settings->show_banner_quote ? '' : 'hidden' }}">
                                {{ $settings->banner_quote }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Sliders Adjustment --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium">Chiều cao banner (px)</label>
                        <input type="range" min="200" max="500"
                               value="{{ $settings->banner_height ?? 280 }}"
                               id="bannerHeightRange"
                               class="w-full mt-2 cursor-pointer">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Vị trí ảnh Y (%)</label>
                        <input type="range" min="0" max="100"
                               value="{{ $settings->banner_position_y ?? 50 }}"
                               id="bannerPositionRange"
                               class="w-full mt-2 cursor-pointer">
                    </div>
                </div>

                {{-- Hidden Inputs để gửi lên server --}}
                <input type="hidden" id="banner_height" name="banner_height">
                <input type="hidden" id="banner_position_y" name="banner_position_y">
                <input type="hidden" id="banner_image_base64" name="banner_image_base64">

                <button type="submit"
                        class="px-6 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition shadow-lg">
                   <iconify-icon icon="heroicons:document-arrow-down"></iconify-icon> Save and Back
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL CROP --}}
    <div id="cropModal" class="fixed inset-0 z-[999] hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

        <div class="relative max-w-6xl mx-auto mt-20 bg-white rounded-lg p-6 shadow-2xl">
            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 h-[420px] bg-slate-100 overflow-hidden rounded border">
                    <img id="image_to_crop" class="max-w-full">
                </div>
                <div>
                    <p class="text-sm font-medium mb-2">Xem trước cắt</p>
                    <div class="preview-box w-full h-[200px] border rounded overflow-hidden bg-slate-100"></div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button onclick="closeModal()" class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50">Hủy</button>
                <button id="crop_and_save"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Cắt & Lưu
                </button>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT ĐẦY ĐỦ --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        // 1. Khai báo biến
        const upload_image = document.getElementById('upload_image');
        const modal = document.getElementById('cropModal');
        const imageToCrop = document.getElementById('image_to_crop');
        const previewImg = document.getElementById('banner-preview');
        const previewWrapper = document.getElementById('banner-preview-wrapper');

        // Text & Toggles Elements
        const titleInput = document.querySelector('input[name="banner_title"]');
        const quoteInput = document.querySelector('input[name="banner_quote"]');
        const titleCheck = document.getElementById('show_banner_title');
        const quoteCheck = document.getElementById('show_banner_quote');
        const previewTitle = document.getElementById('preview_title');
        const previewQuote = document.getElementById('preview_quote');

        // Ranges Elements
        const heightRange = document.getElementById('bannerHeightRange');
        const posRange = document.getElementById('bannerPositionRange');

        // Hidden Inputs
        const heightInput = document.getElementById('banner_height');
        const posInput = document.getElementById('banner_position_y');
        const base64Input = document.getElementById('banner_image_base64');

        let cropper;

        // 2. Thiết lập giá trị mặc định cho hidden inputs từ range
        heightInput.value = heightRange.value;
        posInput.value = posRange.value;

        // 3. Xử lý Live Preview cho TEXT (Title & Quote)
        titleInput.addEventListener('input', () => {
            previewTitle.innerText = titleInput.value;
        });

        quoteInput.addEventListener('input', () => {
            previewQuote.innerText = quoteInput.value;
        });

        // 4. Xử lý Ẩn/Hiện (Checkbox)
        titleCheck.addEventListener('change', () => {
            if(titleCheck.checked) previewTitle.classList.remove('hidden');
            else previewTitle.classList.add('hidden');
        });

        quoteCheck.addEventListener('change', () => {
            if(quoteCheck.checked) previewQuote.classList.remove('hidden');
            else previewQuote.classList.add('hidden');
        });

        // 5. Xử lý Range Sliders (Chiều cao & Vị trí)
        heightRange.addEventListener('input', () => {
            previewWrapper.style.height = heightRange.value + 'px';
            heightInput.value = heightRange.value;
        });

        posRange.addEventListener('input', () => {
            previewImg.style.objectPosition = `50% ${posRange.value}%`;
            posInput.value = posRange.value;
        });

        // 6. Xử lý Upload & Crop ảnh
        upload_image.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // Hiển thị tên file
            document.getElementById('file_name_display').innerText = file.name;

            // Load ảnh vào modal
            imageToCrop.src = URL.createObjectURL(file);
            modal.classList.remove('hidden');

            // Khởi tạo CropperJS
            setTimeout(() => {
                if(cropper) cropper.destroy();
                cropper = new Cropper(imageToCrop, {
                    viewMode: 1,
                    dragMode: 'move',
                    aspectRatio: NaN, // Tự do tỷ lệ
                    autoCropArea: 1,
                    preview: '.preview-box'
                });
            }, 100);
        });

        function closeModal() {
            modal.classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            upload_image.value = ''; // Reset input file để chọn lại ảnh cũ được
        }

        // Nút "Cắt & Lưu" trong Modal
        document.getElementById('crop_and_save').addEventListener('click', () => {
            if (!cropper) return;
            
            // Lấy ảnh đã cắt (Resize nhẹ để tối ưu)
            const canvas = cropper.getCroppedCanvas({ width: 1920, height: 1080 });
            const base64 = canvas.toDataURL('image/png');

            // Gán vào hidden input và update preview ngay lập tức
            base64Input.value = base64;
            previewImg.src = base64;

            closeModal();
        });

        // 7. Gửi Form (AJAX Submit)
        document.getElementById('mainForm').addEventListener('submit', (e) => {
            e.preventDefault();

            // Tạo object data
            const formData = {
                banner_title: titleInput.value,
                banner_quote: quoteInput.value,
                show_banner_title: titleCheck.checked, // true/false
                show_banner_quote: quoteCheck.checked, // true/false
                banner_height: heightInput.value,
                banner_position_y: posInput.value,
                banner_image_base64: base64Input.value
            };

            fetch('{{ route("dashboard.update-banner") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    // Nếu thành công, reload lại trang dashboard hoặc thông báo
                    window.location.href = '{{ route("dashboard") }}';
                } else {
                    alert('Lỗi: ' + response.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Đã có lỗi xảy ra, vui lòng thử lại.');
            });
        });
    </script>
</x-app-layout>