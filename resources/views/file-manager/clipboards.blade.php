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
        <h2 class="text-xl font-bold">My Clipboard</h2>
        <div class="flex gap-2">
            <form action="{{ route('assets.clipboard') }}" method="GET" class="flex gap-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search content..." class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded border border-gray-300">🔍</button>
            </form>
            <button onclick="document.getElementById('addClipModal').showModal()" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">+ Add Note</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($clipboards as $item)
            <div class="bg-yellow-50 p-4 rounded shadow border-l-4 border-yellow-400 hover:shadow-md transition flex flex-col h-64">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-lg truncate">{{ $item->title }}</h3>
                    <div class="flex gap-1">
                         <button onclick="editClip({{ $item }})" class="text-gray-400 hover:text-blue-500">✏️</button>
                         <form action="{{ route('assets.clipboard.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this note?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-400 hover:text-red-500">🗑️</button>
                         </form>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto bg-white p-2 rounded text-sm text-gray-700 whitespace-pre-wrap font-mono mb-2 border">{{ $item->content }}</div>
                <button onclick="copyContent(`{{ base64_encode($item->content) }}`)" class="w-full bg-gray-200 hover:bg-gray-300 py-1 rounded text-sm font-medium flex justify-center items-center gap-2">
                    <iconify-icon class="nav-icon" icon="heroicons:square-2-stack"></iconify-icon> Copy Content
                </button>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $clipboards->links() }}</div>

    {{-- Modal Clipboard --}}
    <dialog id="addClipModal" class="p-6 rounded shadow-lg border w-[600px] backdrop:bg-gray-900/50">
        <form method="POST" action="{{ route('assets.clipboard.store') }}" id="clipForm">
            @csrf
            <div id="clipMethodField"></div>
            <h3 class="font-bold text-lg mb-4" id="clipModalTitle">New Clipboard Item</h3>
            
            <div class="space-y-3">
                <input type="text" name="title" id="clipTitle" placeholder="Title (e.g., API Key, Code Snippet)" class="w-full border p-2 rounded" required>
                <textarea name="content" id="clipContent" rows="10" placeholder="Paste your content here..." class="w-full border p-2 rounded font-mono text-sm" required></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeClipModal()" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
            </div>
        </form>
    </dialog>

    <script>
        function copyContent(base64Str) {
            // Decode base64 to handle newlines and special chars safely inside onclick
            const text = atob(base64Str);
            navigator.clipboard.writeText(text).then(() => alert('Content copied to clipboard!'));
        }

        function editClip(item) {
            document.getElementById('clipModalTitle').innerText = 'Edit Item';
            document.getElementById('clipForm').action = '/storage/clipboard/' + item.id;
            document.getElementById('clipMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            document.getElementById('clipTitle').value = item.title;
            document.getElementById('clipContent').value = item.content;
            
            document.getElementById('addClipModal').showModal();
        }

        function closeClipModal() {
            document.getElementById('addClipModal').close();
            setTimeout(() => {
                document.getElementById('clipForm').reset();
                document.getElementById('clipModalTitle').innerText = 'New Clipboard Item';
                document.getElementById('clipForm').action = '{{ route("assets.clipboard.store") }}';
                document.getElementById('clipMethodField').innerHTML = '';
            }, 200);
        }
    </script>
</x-app-layout>