<x-app-layout>
    <x-storage-nav />
    
    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Saved Links</h2>
        <div class="flex gap-2">
            <form action="{{ route('assets.links') }}" method="GET" class="flex gap-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search links..." class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded border border-gray-300">🔍</button>
            </form>
            <button onclick="document.getElementById('addLinkModal').showModal()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">+ Add Link</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($links as $link)
            <div class="bg-white p-4 rounded shadow border hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <h3 class="font-bold text-lg truncate pr-2"><iconify-icon class="nav-icon mr-2" icon="heroicons:link"></iconify-icon>{{ $link->title }}</h3>
                    <div class="flex gap-1">
                         <button onclick="editLink({{ $link }})" class="text-gray-500 hover:text-blue-500">✏️</button>
                         <form action="{{ route('assets.links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Delete this link?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-500 hover:text-red-500">🗑️</button>
                         </form>
                    </div>
                </div>
                <p class="text-gray-500 text-sm mb-2 truncate">{{ $link->description }}</p>
                <div class="bg-gray-100 p-2 rounded flex justify-between items-center">
                    <a href="{{ $link->url }}" target="_blank" class="text-blue-600 text-sm truncate w-5/6 hover:underline">{{ $link->url }}</a>
                    <button onclick="copyToClipboard('{{ $link->url }}')" class="text-gray-500 hover:text-green-600" title="Copy"> <iconify-icon class="nav-icon" icon="heroicons:square-2-stack"></iconify-icon></button>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="mt-4">{{ $links->links() }}</div>

    {{-- Add/Edit Modal (Dùng chung đơn giản hóa) --}}
    <dialog id="addLinkModal" class="p-6 rounded shadow-lg border w-[500px] backdrop:bg-gray-900/50">
        <form method="POST" action="{{ route('assets.links.store') }}" id="linkForm">
            @csrf
            <div id="methodField"></div> {{-- Chứa @method('PUT') khi edit --}}
            <h3 class="font-bold text-lg mb-4" id="modalTitle">New Link</h3>
            
            <div class="space-y-3">
                <input type="text" name="title" id="linkTitle" placeholder="Title" class="w-full border p-2 rounded" required>
                <input type="url" name="url" id="linkUrl" placeholder="https://example.com" class="w-full border p-2 rounded" required>
                <textarea name="description" id="linkDesc" placeholder="Description (Optional)" class="w-full border p-2 rounded"></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeLinkModal()" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </dialog>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => alert('URL Copied!'));
        }

        function editLink(link) {
            document.getElementById('modalTitle').innerText = 'Edit Link';
            document.getElementById('linkForm').action = '/storage/links/' + link.id;
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            document.getElementById('linkTitle').value = link.title;
            document.getElementById('linkUrl').value = link.url;
            document.getElementById('linkDesc').value = link.description;
            
            document.getElementById('addLinkModal').showModal();
        }

        function closeLinkModal() {
            document.getElementById('addLinkModal').close();
            // Reset form for next 'Add' click
            setTimeout(() => {
                document.getElementById('linkForm').reset();
                document.getElementById('modalTitle').innerText = 'New Link';
                document.getElementById('linkForm').action = '{{ route("assets.links.store") }}';
                document.getElementById('methodField').innerHTML = '';
            }, 200);
        }
    </script>
</x-app-layout>