
<x-app-layout>
    <div class="space-y-8">
        <div>
        <x-breadcrumb :page-title="$pageTitle" :breadcrumb-items="$breadcrumbItems" />
        </div>
            
        <div class="dashcode-calender">
            
            <div class="grid grid-cols-12 gap-4">
                {{-- note --}}
                <div class="lg:col-span-4 col-span-12 space-y-5">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Notes</h4>
                  </div>
                  <div class="card-body p-6">
                    <div class="mb-12">
    <div id="dashcode-mini-calendar"><table class="zabuto-calendar"><thead><tr class="zabuto-calendar__navigation" role="navigation"><td class="zabuto-calendar__navigation__item--prev"><iconify-icon icon="heroicons-outline:chevron-left"><template shadowrootmode="open"><style data-style="data-style">:host{display:inline-block;vertical-align:0}span,svg{display:block}</style><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19l-7-7l7-7"></path></svg></template></iconify-icon></td><td class="zabuto-calendar__navigation__item--header" colspan="5"><span class="zabuto-calendar__navigation__item--header__title">2025 - December</span></td><td class="zabuto-calendar__navigation__item--next"><iconify-icon icon="heroicons-outline:chevron-right"><template shadowrootmode="open"><style data-style="data-style">:host{display:inline-block;vertical-align:0}span,svg{display:block}</style><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5l7 7l-7 7"></path></svg></template></iconify-icon></td></tr><tr class="zabuto-calendar__days-of-week"><th class="zabuto-calendar__days-of-week__item">Sun</th><th class="zabuto-calendar__days-of-week__item">Mon</th><th class="zabuto-calendar__days-of-week__item">Tue</th><th class="zabuto-calendar__days-of-week__item">Wed</th><th class="zabuto-calendar__days-of-week__item">Thu</th><th class="zabuto-calendar__days-of-week__item">Fri</th><th class="zabuto-calendar__days-of-week__item">Sat</th></tr></thead><tbody><tr class="zabuto-calendar__week--first"><td class="zabuto-calendar__day--empty"></td><td class="zabuto-calendar__day">1</td><td class="zabuto-calendar__day">2</td><td class="zabuto-calendar__day">3</td><td class="zabuto-calendar__day">4</td><td class="zabuto-calendar__day">5</td><td class="zabuto-calendar__day">6</td></tr><tr class="zabuto-calendar__week"><td class="zabuto-calendar__day--today"><span class="badge bg-slate-900 dark:bg-slate-700 text-white dark:text-slate-300">7</span></td><td class="zabuto-calendar__day">8</td><td class="zabuto-calendar__day">9</td><td class="zabuto-calendar__day">10</td><td class="zabuto-calendar__day">11</td><td class="zabuto-calendar__day">12</td><td class="zabuto-calendar__day">13</td></tr><tr class="zabuto-calendar__week"><td class="zabuto-calendar__day">14</td><td class="zabuto-calendar__day">15</td><td class="zabuto-calendar__day">16</td><td class="zabuto-calendar__day">17</td><td class="zabuto-calendar__day">18</td><td class="zabuto-calendar__day">19</td><td class="zabuto-calendar__day">20</td></tr><tr class="zabuto-calendar__week"><td class="zabuto-calendar__day">21</td><td class="zabuto-calendar__day">22</td><td class="zabuto-calendar__day">23</td><td class="zabuto-calendar__day">24</td><td class="zabuto-calendar__day">25</td><td class="zabuto-calendar__day">26</td><td class="zabuto-calendar__day">27</td></tr><tr class="zabuto-calendar__week--last"><td class="zabuto-calendar__day">28</td><td class="zabuto-calendar__day">29</td><td class="zabuto-calendar__day">30</td><td class="zabuto-calendar__day">31</td><td class="zabuto-calendar__day--empty"></td><td class="zabuto-calendar__day--empty"></td><td class="zabuto-calendar__day--empty"></td></tr></tbody></table></div>
</div>
<ul class="divide-y divide-slate-100 dark:divide-slate-700">
        <li class="block py-[10px]">
        <div class="flex space-x-2 rtl:space-x-reverse">
            <div class="flex-1 flex space-x-2 rtl:space-x-reverse">
                <div class="flex-none">
                    <div class="h-8 w-8">
                        <img src="images/svg/sk.svg" alt="" class="block w-full h-full object-cover rounded-full border hover:border-white border-transparent">
                        <div id="dashcode-mini-calendar"></div>
                    </div>
                </div>
                <div class="flex-1">
                    <span class="block text-slate-600 text-sm dark:text-slate-300 mb-1 font-medium">
                        Meeting with client
                    </span>
                    <span class="flex font-normal text-xs dark:text-slate-400 text-slate-500">
                        <span class="text-base inline-block mr-1">
                            <iconify-icon icon="heroicons-outline:video-camera"><template shadowrootmode="open"><style data-style="data-style">:host{display:inline-block;vertical-align:0}span,svg{display:block}</style><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 10l4.553-2.276A1 1 0 0 1 21 8.618v6.764a1 1 0 0 1-1.447.894L15 14M5 18h8a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2"></path></svg></template></iconify-icon>
                        </span>
                        Zoom meeting
                    </span>
                </div>
            </div>
            <div class="flex-none">
                <span class="block text-xs text-slate-600 dark:text-slate-400">
                    01 Nov 2021
                </span>
            </div>
        </div>
    </li>
        <li class="block py-[10px]">
        <div class="flex space-x-2 rtl:space-x-reverse">
            <div class="flex-1 flex space-x-2 rtl:space-x-reverse">
                <div class="flex-none">
                    <div class="h-8 w-8">
                        <img src="images/svg/path.svg" alt="" class="block w-full h-full object-cover rounded-full border hover:border-white border-transparent">
                    </div>
                </div>
                <div class="flex-1">
                    <span class="block text-slate-600 text-sm dark:text-slate-300 mb-1 font-medium">
                        Design meeting (team)
                    </span>
                    <span class="flex font-normal text-xs dark:text-slate-400 text-slate-500">
                        <span class="text-base inline-block mr-1">
                            <iconify-icon icon="heroicons-outline:video-camera"><template shadowrootmode="open"><style data-style="data-style">:host{display:inline-block;vertical-align:0}span,svg{display:block}</style><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 10l4.553-2.276A1 1 0 0 1 21 8.618v6.764a1 1 0 0 1-1.447.894L15 14M5 18h8a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2"></path></svg></template></iconify-icon>
                        </span>
                        Skyp meeting
                    </span>
                </div>
            </div>
            <div class="flex-none">
                <span class="block text-xs text-slate-600 dark:text-slate-400">
                    01 Nov 2021
                </span>
            </div>
        </div>
    </li>
    </ul>


                    <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($events as $event)
                            <li class="block py-[10px]">
                                <div class="flex space-x-2 rtl:space-x-reverse">
                                    <div class="flex-1 flex space-x-2 rtl:space-x-reverse">
                                        <div class="flex-none">
                                            <div class="h-8 w-8">
                                                <img src="{{ asset($event['image']) }}" alt="" class="block w-full h-full object-cover rounded-full border hover:border-white border-transparent">
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <span class="block text-slate-600 text-sm dark:text-slate-300 mb-1 font-medium">
                                                {{ $event['title'] }}
                                            </span>
                                            <span class="flex font-normal text-xs dark:text-slate-400 text-slate-500">
                                                <span class="text-base inline-block mr-1">
                                                    <iconify-icon icon="heroicons-outline:video-camera"></iconify-icon>
                                                </span>
                                                {{ $event['type'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-none">
                                        <span class="block text-xs text-slate-600 dark:text-slate-400">
                                            {{ $event['date'] }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="block py-[10px] text-center">
                                <span class="text-slate-500 dark:text-slate-400 text-sm">No events to display.</span>
                            </li>
                        @endforelse
                    </ul>
                  </div>
                </div>
              </div>
                {{-- end notes  --}}
                <div class="col-span-12 lg:col-span-3 card p-6">
                    <button class="btn btn-dark block w-full add-event">
                        add event
                    </button>
                    <div class="block py-4 text-slate-800 dark:text-slate-400 font-semibold text-xs uppercase mt-4">
                        FILTER
                    </div>
                    <ul class="space-y-2">
                        <li>
                            <div class="checkbox-area primary-checkbox">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="hidden" name="category" checked value="all">
                                    <span class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative
                                            transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                        <img src="images/icon/ck-white.svg" alt="" class="h-[10px] w-[10px] block m-auto opacity-0">
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">All</span>
                                </label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox-area primary-checkbox">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="hidden" name="category" checked value="business">
                                    <span class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative
                                            transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                        <img src="images/icon/ck-white.svg" alt="" class="h-[10px] w-[10px] block m-auto opacity-0">
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">Business</span>
                                </label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox-area success-checkbox">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="hidden" name="category" checked value="personal">
                                    <span class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative
                                            transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                        <img src="images/icon/ck-white.svg" alt="" class="h-[10px] w-[10px] block m-auto opacity-0">
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">personal</span>
                                </label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox-area danger-checkbox">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="hidden" name="category" checked value="holiday">
                                    <span class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative
                                            transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                        <img src="images/icon/ck-white.svg" alt="" class="h-[10px] w-[10px] block m-auto opacity-0">
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">Holiday</span>
                                </label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox-area info-checkbox">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="hidden" name="category" checked value="family">
                                    <span class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative
                                            transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                        <img src="images/icon/ck-white.svg" alt="" class="h-[10px] w-[10px] block m-auto opacity-0">
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">family</span>
                                </label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox-area warning-checkbox">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="hidden" name="category" checked value="meeting">
                                    <span class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative
                                            transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                        <img src="images/icon/ck-white.svg" alt="" class="h-[10px] w-[10px] block m-auto opacity-0">
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">meeting</span>
                                </label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox-area info-checkbox">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="hidden" name="category" checked value="etc">
                                    <span class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative
                                            transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                        <img src="images/icon/ck-white.svg" alt="" class="h-[10px] w-[10px] block m-auto opacity-0">
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">etc</span>
                                </label>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-span-12 lg:col-span-9 card p-6">
                    <div id="full-calander-active"></div>
                </div>
            </div>
        </div>
        <!-- start add event modal -->
        <div class=" addmodal-wrapper " id="addeventModal">
            <div class=" modal-overlay"></div>
            <!-- opacity -->
            <div class="modal-content">
                <div class="flex min-h-full justify-center text-center p-6 items-start ">
                    <div class="w-full transform overflow-hidden rounded-md bg-white dark:bg-slate-800 text-left align-middle shadow-xl
                            transition-alll max-w-xl opacity-100 scale-100">
                        <div class="relative overflow-hidden py-4 px-5 text-white flex justify-between bg-slate-900 dark:bg-slate-800 dark:border-b
                                dark:border-slate-700">
                            <h2 class="capitalize leading-6 tracking-wider font-medium text-base text-white">Event</h2>
                            <button class="text-[22px] close-event-modal">
                                <iconify-icon icon="heroicons:x-mark"></iconify-icon>
                            </button>
                        </div>
                        <!-- end modal header -->
                        <div class="px-6 py-8">
                            <form id="add-event-form" class="space-y-5">
                                <div class="fromGroup">
                                    <label for="event-title" class=" form-label">Title:</label>
                                    <input type="text" id="event-title" name="event-title" placeholder="Add Title" class="form-control" required></div>
                                <div class="fromGroup">
                                    <label for="event-start-date" class=" form-label">Start Date</label>
                                    <input class="form-control py-2 startdate" id="event-start-date" type="text"></div>
                                <div class="fromGroup">
                                    <label for="event-end-date" class=" form-label">End Date</label>
                                    <input class="form-control py-2 enddate" id="event-end-date" type="text"></div>
                                <div class="fromGroup">
                                    <label for="event-category" class="form-label">Category:</label>
                                    <select id="event-category" name="event-category" required class="form-control">
                                        <option value="">Select a category</option>
                                        <option value="business">Business</option>
                                        <option value="personal">Personal</option>
                                        <option value="holiday">Holiday</option>
                                        <option value="family">Family</option>
                                        <option value="meeting">Meeting</option>
                                        <option value="etc">Etc</option>
                                    </select>
                                </div>
                                <div class="text-right">
                                    <button type="submit" id="submit-button" class="btn btn-dark">Add Event</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        @vite(['resources/js/plugins/flatpickr.js'])
        @vite(['resources/js/plugins/fullcalendar.js'])
        @vite(['resources/js/custom/calander-init.js'])
    @endpush
</x-app-layout>
