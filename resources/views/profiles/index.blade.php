<x-app-layout>
    {{-- CSS Cropper --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    <div class="space-y-5 profile-page">
        <div class="profiel-wrap px-[35px] pb-10 md:pt-[84px] pt-10 rounded-lg bg-white dark:bg-slate-800 lg:flex lg:space-y-0
                space-y-6 justify-between items-end relative z-[1]">
            
            {{-- BACKGROUND BANNER --}}
            <div class="bg-slate-900 dark:bg-slate-700 absolute left-0 top-0 md:h-1/2 h-[150px] w-full z-[-1] rounded-t-lg overflow-hidden">
                <div class="absolute top-3 right-4 z-10 flex flex-col space-y-1.5 max-w-[50%] md:max-w-[40%]">
                    
                    @if(auth()->user()->status)
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-[10px] md:text-xs font-medium bg-black/30 text-white backdrop-blur-md border border-white/10 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-green-400 mr-2 animate-pulse"></span>
                            {{ auth()->user()->status }}
                        </div>
                    @endif

                    @if(auth()->user()->profile_description)
                        <p class="text-xs md:text-sm text-slate-200 italic text-right line-clamp-2 drop-shadow-md font-light leading-snug">
                            "{{ auth()->user()->profile_description }}"
                        </p>
                    @endif
                    
                </div>
            </div>
            
            <div class="profile-box flex-none md:text-start text-center">
                <div class="md:flex items-end md:space-x-6 rtl:space-x-reverse">
                    <div class="flex-none">
                        <div class="md:h-[186px] md:w-[186px] h-[140px] w-[140px] md:ml-0 md:mr-0 ml-auto mr-auto md:mb-0 mb-4 rounded-full ring-4
                                ring-slate-100 relative">
                            <img id="mainProfileImage" src="{{ auth()->user()->getFirstMediaUrl('profile-image') ?:
                            Avatar::create(auth()->user()->name)->setDimension(400)->setFontSize(240)->toBase64() }}" alt="" class="w-full h-full object-cover rounded-full">
                            <a href="javascript:void(0)"
                                class="absolute right-2 h-8 w-8 bg-slate-50 text-slate-600 rounded-full shadow-sm flex flex-col items-center
                                    justify-center md:top-[140px] top-[100px]">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </a>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-2xl font-medium text-slate-900 dark:text-slate-200 mb-[3px]">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-sm font-light text-slate-600 dark:text-slate-400 capitalize mb-2">
                            {{ auth()->user()->roles()->first()?->name }}
                        </div>

                    </div>
                </div>
            </div>
            
            {{-- Phần thống kê bên phải giữ nguyên --}}
            <div class="profile-info-500 md:flex md:text-start text-center flex-1 max-w-[516px] md:space-y-0 space-y-4">
                <div class="flex-1">
                    <div class="text-base text-slate-900 dark:text-slate-300 font-medium mb-1">$32,400</div>
                    <div class="text-sm text-slate-600 font-light dark:text-slate-300">Total Balance</div>
                </div>
                <div class="flex-1">
                    <div class="text-base text-slate-900 dark:text-slate-300 font-medium mb-1">200</div>
                    <div class="text-sm text-slate-600 font-light dark:text-slate-300">Board Card</div>
                </div>
                <div class="flex-1">
                    <div class="text-base text-slate-900 dark:text-slate-300 font-medium mb-1">3200</div>
                    <div class="text-sm text-slate-600 font-light dark:text-slate-300">Calender Events</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            {{-- Cột Info bên trái giữ nguyên --}}
            <div class="lg:col-span-4 col-span-12">
                <div class="card h-full">
                    <header class="card-header">
                        <h4 class="card-title">Info</h4>
                    </header>
                    <div class="card-body p-6">
                        <ul class="list space-y-8">
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:envelope"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">EMAIL</div>
                                    <a href="mailto:{{ auth()->user()->email }}" class="text-base text-slate-600 dark:text-slate-50">{{ auth()->user()->email }}</a>
                                </div>
                            </li>
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:phone-arrow-up-right"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">PHONE</div>
                                    <a href="tel:{{ auth()->user()->phone }}" class="text-base text-slate-600 dark:text-slate-50">{{ auth()->user()->phone ?: 'N/A' }}</a>
                                </div>
                            </li>
                            <li class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                    <iconify-icon icon="heroicons:map"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">LOCATION</div>
                                    <div class="text-base text-slate-600 dark:text-slate-50">
                                        {{ auth()->user()->city ?? 'N/A' }}
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Form Edit bên phải --}}
            <div class="lg:col-span-8 col-span-12">
                <div class="card ">
                    <header class="card-header">
                        <h4 class="card-title">Edit Profile</h4>
                    </header>
                    <div class="card-body px-5 py-6">
                        @if (session('message'))
                            <x-alert :message="session('message')" :type="'success'" /><br />
                        @endif

                        <form action="{{ route('profiles.update', auth()->user()) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div class="input-area">
                                    <label for="name" class="form-label">{{ __('Name') }}</label>
                                    <input name="name" type="text" id="name" class="form-control" value="{{ auth()->user()->name }}" required>
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div class="input-area">
                                    <label for="email" class="form-label">{{ __('Email') }}</label>
                                    <input name="email" type="email" id="email" class="form-control" value="{{ auth()->user()->email }}" required>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div class="input-area">
                                    <label for="phone" class="form-label">{{ __('Phone') }}</label>
                                    <input name="phone" type="tel" id="phone" class="form-control" value="{{ auth()->user()->phone }}">
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                                <div class="input-area">
                                    <label for="postcode" class="form-label">{{ __('Post Code') }}</label>
                                    <input name="post_code" type="text" id="post" class="form-control" value="{{ auth()->user()->post_code }}">
                                    <x-input-error :messages="$errors->get('post_code')" class="mt-2" />
                                </div>
                                <div class="input-area">
                                    <label for="state" class="form-label">{{ __('State / City') }}</label>
                                    <input name="city" type="text" id="state" class="form-control" value="{{ auth()->user()->city }}">
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>
                                <div class="input-area">
                                    <label for="country" class="form-label">{{ __('Country') }}</label>
                                    <input name="country" type="text" id="country" class="form-control" value="{{ auth()->user()->country }}">
                                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                                </div>

                                {{-- [NEW] INPUT CHO STATUS --}}
                                <div class="input-area">
                                    <label for="status" class="form-label">Câu nói tâm đắc / Trạng thái</label>
                                    <input name="status" type="text" id="status" class="form-control" 
                                           placeholder="Ví dụ: Đang tập trung cao độ..." 
                                           value="{{ auth()->user()->status }}">
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                {{-- [NEW] INPUT CHO DESCRIPTION --}}
                                <div class="input-area">
                                    <label for="profile_description" class="form-label">Giới thiệu bản thân</label>
                                    <textarea name="profile_description" id="profile_description" rows="3" class="form-control" 
                                              placeholder="Mô tả ngắn về bạn...">{{ auth()->user()->profile_description }}</textarea>
                                    <x-input-error :messages="$errors->get('profile_description')" class="mt-2" />
                                </div>

                                {{-- PHOTO UPLOAD --}}
                                <div class="input-area sm:col-span-2">
                                    <label for="upload_avatar" class="form-label">{{ __('Photo') }}</label>
                                    <input type="file" id="upload_avatar" name="photo" accept="image/*" class="form-control p-[0.565rem] pl-2">
                                    <input type="hidden" name="avatar_base64" id="avatar_base64">
                                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-dark mt-3">{{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CROPPER GIỮ NGUYÊN --}}
    <div id="cropModal" class="fixed inset-0 z-[999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:w-full sm:max-w-4xl">
                    <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Cắt ảnh đại diện</h3>
                    </div>
                    <div class="px-4 py-4 sm:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="col-span-2 h-[350px] bg-slate-100 rounded-lg overflow-hidden">
                                <img id="image_to_crop" class="max-w-full block" src="">
                            </div>
                            <div class="col-span-1 flex flex-col items-center justify-center space-y-4">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Xem trước</p>
                                <div class="preview-box w-[150px] h-[150px] rounded-full overflow-hidden border-2 border-blue-500 shadow-lg"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-900/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                        <button type="button" id="crop_btn" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:w-auto">Xác nhận</button>
                        <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Hủy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
        <script>
            const inputImage = document.getElementById('upload_avatar');
            const modal = document.getElementById('cropModal');
            const imageToCrop = document.getElementById('image_to_crop');
            const hiddenInput = document.getElementById('avatar_base64');
            const mainProfileImage = document.getElementById('mainProfileImage');
            let cropper = null;

            inputImage.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const file = files[0];
                    const url = URL.createObjectURL(file);
                    imageToCrop.src = url;
                    modal.classList.remove('hidden');
                    if (cropper) { cropper.destroy(); }
                    setTimeout(() => {
                        cropper = new Cropper(imageToCrop, {
                            aspectRatio: 1,
                            viewMode: 1,
                            preview: '.preview-box',
                            autoCropArea: 1,
                        });
                    }, 100);
                }
            });

            function closeModal() {
                modal.classList.add('hidden');
                if (cropper) { cropper.destroy(); cropper = null; }
            }

            document.getElementById('crop_btn').addEventListener('click', function() {
                if (!cropper) return;
                const canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
                const base64data = canvas.toDataURL('image/png');
                hiddenInput.value = base64data;
                if(mainProfileImage) { mainProfileImage.src = base64data; }
                closeModal();
            });
        </script>
    @endpush
</x-app-layout>