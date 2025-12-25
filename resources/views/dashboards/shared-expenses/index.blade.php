<x-app-layout>
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <div>
                <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                    Quản lý chi tiêu chung
                </h4>
            </div>
            <a href="{{ route('expense-groups.create') }}" class="btn inline-flex justify-center btn-dark">
                <span class="flex items-center">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons-outline:plus"></iconify-icon>
                    <span>Tạo nhóm mới</span>
                </span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($groups as $group)
            <div class="card">
                <div class="card-body p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h5 class="text-slate-900 font-medium text-lg">{{ $group->name }}</h5>
                        <div class="text-xs text-slate-500">{{ $group->members_count }} thành viên</div>
                    </div>
                    <p class="text-slate-500 text-sm mb-4">{{ $group->description ?? 'Không có mô tả' }}</p>
                    <div class="flex -space-x-2 overflow-hidden mb-6">
                        @foreach($group->members->take(4) as $member)
                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white" 
                                 src="{{ $member->avatar ?? asset('images/all-img/user.png') }}" 
                                 alt="{{ $member->name }}">
                        @endforeach
                    </div>
                    <a href="{{ route('expense-groups.show', $group->id) }}" class="btn btn-outline-dark btn-sm w-full block text-center">
                        Xem chi tiết
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>