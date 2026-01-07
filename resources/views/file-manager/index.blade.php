<x-app-layout>
    <div x-data="fileManagerApp" class="h-full flex flex-col space-y-4">
 
        {{-- MESSAGE / ALERT --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg relative flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="heroicons:check-circle" class="text-xl text-green-500"></iconify-icon>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-green-500 hover:text-green-700 transition" title="Close">
                    <iconify-icon icon="heroicons:x-mark" width="20"></iconify-icon>
                </button>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div x-data="{ show: true }" x-show="show" x-transition class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg relative flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="heroicons:exclamation-circle" class="text-xl text-red-500"></iconify-icon>
                    <div>
                        @if(session('error'))
                            <p>{{ session('error') }}</p>
                        @endif
                        @if($errors->any())
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <button @click="show = false" class="text-red-500 hover:text-red-700 transition" title="Close">
                    <iconify-icon icon="heroicons:x-mark" width="20"></iconify-icon>
                </button>
            </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-center bg-white p-4 rounded-lg shadow gap-4">
            <div class="flex items-center gap-4 w-full md:w-auto">
                <h2 class="text-xl font-bold text-gray-800">File Manager</h2>
                
                <div class="hidden md:flex gap-2 text-sm">
                    <a href="{{ route('file-manager.index') }}" class="px-3 py-1 rounded {{ !request('type') ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-gray-100 text-gray-600' }}">All</a>
                    <a href="{{ route('file-manager.index', ['type' => 'image', 'folder_id' => $parentId]) }}" class="px-3 py-1 rounded {{ request('type') == 'image' ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-gray-100 text-gray-600' }}">Images</a>
                    <a href="{{ route('file-manager.index', ['type' => 'video', 'folder_id' => $parentId]) }}" class="px-3 py-1 rounded {{ request('type') == 'video' ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-gray-100 text-gray-600' }}">Video</a>
                </div>
            </div>

            <div class="flex gap-3 w-full md:w-auto justify-end">
                {{-- Bulk Delete Button --}}
                <div x-show="selectedItems.length > 0" x-transition class="flex items-center">
                    <form action="{{ route('file-manager.bulk-delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete ' + document.getElementById('bulkDeleteCount').innerText + ' items?');">
                        @csrf @method('DELETE')
                        <input type="hidden" name="items[]" x-model="selectedItems">
                        {{-- Lưu ý: x-model mảng vào input hidden không hoạt động trực tiếp như mong đợi với form submit mảng, ta sẽ xử lý bằng JS bên dưới hoặc dùng loop input --}}
                        <div id="bulkInputsContainer"></div>
                        <button type="button" onclick="submitBulkDelete()" class="flex items-center gap-1 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition shadow mr-2">
                            <iconify-icon icon="heroicons:trash"></iconify-icon> Delete (<span x-text="selectedItems.length" id="bulkDeleteCount"></span>)
                        </button>
                    </form>
                </div>

                <div class="flex bg-gray-100 p-1 rounded-lg">
                    <button @click="setView('grid')" :class="viewMode === 'grid' ? 'bg-white shadow text-blue-600' : 'text-gray-500'" class="p-2 rounded transition" title="Grid View">
                        <iconify-icon icon="heroicons:squares-2x2" width="20"></iconify-icon>
                    </button>
                    <button @click="setView('list')" :class="viewMode === 'list' ? 'bg-white shadow text-blue-600' : 'text-gray-500'" class="p-2 rounded transition" title="List View">
                        <iconify-icon icon="heroicons:list-bullet" width="20"></iconify-icon>
                    </button>
                </div>

                <button onclick="document.getElementById('createFolderModal').showModal()" class="flex items-center gap-1 bg-white border border-gray-300 px-4 py-2 rounded hover:bg-gray-50 transition">
                    <iconify-icon icon="heroicons:folder-plus" class="text-yellow-500"></iconify-icon> New Folder
                </button>

                <label for="uploadInput" class="flex items-center gap-1 bg-blue-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-700 transition shadow">
                    <iconify-icon icon="heroicons:arrow-up-tray"></iconify-icon> Upload
                    <input type="file" id="uploadInput" class="hidden" onchange="uploadFile(this)">
                </label>
            </div>
        </div>

        @if($parentId)
            <div class="bg-gray-50 px-4 py-2 rounded border flex items-center gap-2 text-sm">
                <a href="{{ route('file-manager.index') }}" class="text-blue-500 hover:underline flex items-center gap-1">
                    <iconify-icon icon="heroicons:home"></iconify-icon> Home
                </a>
                <span class="text-gray-400">/</span>
                <span class="font-semibold text-gray-700">Current Folder</span>
                
                <a href="{{ route('file-manager.index') }}" class="ml-auto text-gray-500 hover:text-black flex items-center gap-1">
                    <iconify-icon icon="heroicons:arrow-uturn-left"></iconify-icon> Back
                </a>
            </div>
        @endif

        <div class="bg-white p-4 rounded-lg shadow min-h-[500px]">
            
            @if($folders->count() > 0)
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Folders</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
                    @foreach($folders as $folder)
                        <div class="group relative bg-yellow-50 border border-yellow-200 rounded-lg p-3 hover:shadow-md transition">
                            {{-- Checkbox Folder --}}
                            <div class="absolute top-2 left-2 z-10">
                                <input type="checkbox" value="folder|{{ $folder->id }}" x-model="selectedItems" class="item-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" @click.stop>
                            </div>
                            <div class="flex flex-col items-center cursor-pointer" onclick="window.location.href='{{ route('file-manager.index', ['folder_id' => $folder->id]) }}'">
                                <iconify-icon icon="heroicons:folder-solid" class="text-yellow-500 text-5xl drop-shadow-sm"></iconify-icon>
                                <span class="mt-2 text-sm font-medium text-gray-700 truncate w-full text-center">{{ $folder->name }}</span>
                            </div>

                            <div class="absolute top-1 right-1 hidden group-hover:flex gap-1 bg-white/90 p-1 rounded shadow-sm backdrop-blur-sm">
                                <button type="button" onclick="openRename('{{ $folder->id }}', '{{ addslashes($folder->name) }}', 'folder')" class="p-1 text-blue-500 hover:bg-blue-100 rounded" title="Rename">
                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                </button>
                                <form action="{{ route('file-manager.delete') }}" method="POST" onsubmit="return confirm('Delete folder {{ $folder->name }}?');">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $folder->id }}">
                                    <input type="hidden" name="type" value="folder">
                                    <button type="submit" class="p-1 text-red-500 hover:bg-red-100 rounded" title="Delete">
                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Files</h3>
            
            <div x-show="viewMode === 'grid'" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($files as $file)
                    <div class="group bg-white rounded-lg border hover:shadow-lg transition relative flex flex-col overflow-hidden">
                        {{-- Checkbox File --}}
                        <div class="absolute top-2 left-2 z-10">
                            <input type="checkbox" value="file|{{ $file->id }}" x-model="selectedItems" class="item-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 shadow-sm">
                        </div>
                        <div class="h-32 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                            @if(str_contains($file->mime_type, 'image'))
                                <img src="{{ $file->getUrl() }}" class="object-cover w-full h-full transition group-hover:scale-105">
                            @else
                                @php
                                    $icon = 'heroicons:document';
                                    if(str_contains($file->mime_type, 'pdf')) $icon = 'bi:file-earmark-pdf';
                                    elseif(str_contains($file->mime_type, 'video')) $icon = 'bi:file-earmark-play';
                                    elseif(str_contains($file->mime_type, 'audio')) $icon = 'bi:file-earmark-music';
                                    elseif(str_contains($file->mime_type, 'zip') || str_contains($file->mime_type, 'rar')) $icon = 'bi:file-earmark-zip';
                                    elseif(str_contains($file->mime_type, 'sql') || str_contains($file->mime_type, 'text') || str_contains($file->mime_type, 'json')) $icon = 'bi:filetype-txt';
                                @endphp
                                <iconify-icon icon="{{ $icon }}" class="text-gray-400 text-6xl"></iconify-icon>
                            @endif
                        </div>

                        <div class="p-3 flex-1">
                            <p class="text-sm font-semibold text-gray-700 truncate" title="{{ $file->name }}">{{ $file->name }}</p>
                            <p class="text-xs text-gray-400 mt-1 flex justify-between">
                                <span class="uppercase">{{ $file->extension }}</span>
                                <span>{{ $file->human_readable_size }}</span>
                            </p>
                        </div>

                        <div class="border-t bg-gray-50 p-2 flex justify-around items-center">
                            <a href="{{ route('file-manager.download', $file->id) }}" class="text-gray-500 hover:text-blue-600" title="Download">
                                <iconify-icon icon="heroicons:arrow-down-tray"></iconify-icon>
                            </a>

                            @if(str_contains($file->mime_type, 'image'))
                                <button type="button" onclick="copyLink('{{ $file->getUrl() }}')" class="text-gray-500 hover:text-green-600" title="Copy Link">
                                    <iconify-icon icon="heroicons:link"></iconify-icon>
                                </button>
                            @endif
                            
                            <button type="button" onclick="openRename('{{ $file->id }}', '{{ addslashes($file->name) }}', 'file')" class="text-gray-500 hover:text-orange-500" title="Rename">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </button>

                           <button type="button"
                            @click="openEdit('{{ $file->id }}')"
                            class="text-gray-500 hover:text-indigo-600"
                            title="Edit Content">
                            <iconify-icon icon="heroicons:code-bracket"></iconify-icon>
                          </button>

                            <form action="{{ route('file-manager.delete') }}" method="POST" onsubmit="return confirm('Delete file?');" class="inline">
                                @csrf @method('DELETE')
                                <input type="hidden" name="id" value="{{ $file->id }}">
                                <input type="hidden" name="type" value="file">
                                <button type="submit" class="text-gray-500 hover:text-red-600 mt-1" title="Delete">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div x-show="viewMode === 'list'" class="overflow-x-auto rounded-lg border mt-10">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 w-4">
                                <input type="checkbox" @click="toggleAll()" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            </th>
                            <th class="px-4 py-3">File Name</th>
                            <th class="px-4 py-3">Size</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Last Modified</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <input type="checkbox" value="file|{{ $file->id }}" x-model="selectedItems" class="item-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 flex items-center gap-3">
                                    @if(str_contains($file->mime_type, 'image'))
                                        <img src="{{ $file->getUrl() }}" class="w-8 h-8 rounded object-cover border">
                                    @else
                                        <iconify-icon icon="heroicons:document" class="text-gray-400 text-xl"></iconify-icon>
                                    @endif
                                    {{ $file->name }}
                                </td>
                                <td class="px-4 py-3">{{ $file->human_readable_size }}</td>
                                <td class="px-4 py-3 uppercase">{{ $file->extension }}</td>
                                <td class="px-4 py-3">{{ $file->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('file-manager.download', $file->id) }}" class="text-gray-500 hover:text-blue-600" title="Download">
                                            <iconify-icon icon="heroicons:arrow-down-tray" width="18"></iconify-icon>
                                        </a>
                                        @if(str_contains($file->mime_type, 'image'))
                                            <button type="button" onclick="copyLink('{{ $file->getUrl() }}')" class="text-gray-500 hover:text-green-600" title="Copy Link">
                                                <iconify-icon icon="heroicons:link" width="18"></iconify-icon>
                                            </button>
                                        @endif
                                        <button type="button" onclick="openRename('{{ $file->id }}', '{{ addslashes($file->name) }}', 'file')" class="text-gray-500 hover:text-orange-600" title="Rename">
                                            <iconify-icon icon="heroicons:pencil-square" width="18"></iconify-icon>
                                        </button>
                                        
                                        @if(in_array(strtolower($file->extension), ['txt','sql','md','html','css','js','json','env']))
                                            <button @click="openEdit({{ $file->id }})" class="text-gray-500 hover:text-indigo-600" title="Edit Content">
                                                <iconify-icon icon="heroicons:code-bracket" width="18"></iconify-icon>
                                            </button>
                                        @endif

                                        <form action="{{ route('file-manager.delete') }}" method="POST" onsubmit="return confirm('Delete file?');" class="inline">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="id" value="{{ $file->id }}">
                                            <input type="hidden" name="type" value="file">
                                            <button type="submit" class="text-gray-500 hover:text-red-600 mt-1" title="Delete">
                                                <iconify-icon icon="heroicons:trash" width="18"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $files->appends(['folder_id' => $parentId, 'type' => request('type'), 'search' => request('search')])->links() }}
            </div>
        </div>
    </div>

    <dialog id="createFolderModal" class="p-6 rounded-lg shadow-xl border border-gray-100 w-96 backdrop:bg-gray-900/50">
        <form method="POST" action="{{ route('file-manager.create-folder') }}">
            @csrf
            <h3 class="font-bold text-lg mb-4 text-gray-800 flex items-center gap-2">
                <iconify-icon icon="heroicons:folder-plus"></iconify-icon> New Folder
            </h3>
            <input type="hidden" name="parent_id" value="{{ $parentId }}">
            <input type="text" name="name" class="border p-2 w-full rounded mb-4 focus:ring focus:ring-blue-200 focus:border-blue-400" placeholder="Enter folder name..." required autofocus>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('createFolderModal').close()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">Create</button>
            </div>
        </form>
    </dialog>

    <div id="renameModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-96 p-6">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Rename Item</h3>
            <form method="POST" action="{{ route('file-manager.rename') }}">
                @csrf
                <input type="hidden" name="id" id="renameInputId">
                <input type="hidden" name="type" id="renameInputType">
                <input type="hidden" name="parent_id" value="{{ $parentId }}">
                
                <label class="block text-sm font-medium text-gray-700 mb-1">New Name</label>
                <input type="text" name="new_name" id="renameInputName" class="border p-2 w-full rounded mb-4 focus:ring-blue-500 focus:border-blue-500" required>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRenameModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded shadow">Rename</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-2 md:p-6">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-6xl h-full md:h-[90vh] flex flex-col" @click.away="closeEditor()">
                <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-800 text-white rounded-t-lg">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <iconify-icon icon="heroicons:code-bracket-square" class="text-green-400"></iconify-icon> 
                        Editing: <span x-text="editingFileName" class="text-green-300 font-mono text-sm ml-2 bg-gray-700 px-2 py-1 rounded"></span>
                    </h3>
                    <button @click="closeEditor()" class="text-gray-400 hover:text-white transition"><iconify-icon icon="heroicons:x-mark" width="24"></iconify-icon></button>
                </div>

                <div class="flex-1 relative bg-gray-900">
                    <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center text-white z-20 bg-gray-900">
                        <iconify-icon icon="svg-spinners:180-ring-with-bg" width="40" class="mb-2"></iconify-icon>
                    </div>
                    {{-- Container cho Ace Editor --}}
                    <div id="aceEditor" class="absolute inset-0 w-full h-full"></div>
                </div>

                <div class="px-6 py-4 border-t bg-gray-100 rounded-b-lg flex justify-between items-center">
                    <span class="text-xs text-gray-500">Press Ctrl+S to save.</span>
                    <div class="flex gap-3">
                        <button type="button" @click="closeEditor()" class="px-4 py-2 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-100">Cancel</button>
                        <button type="button" @click="saveContent()" :disabled="isSaving" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center gap-2 disabled:opacity-50">
                            <span x-show="!isSaving"><iconify-icon icon="heroicons:check"></iconify-icon> Save</span>
                            <span x-show="isSaving"><iconify-icon icon="svg-spinners:180-ring-with-bg"></iconify-icon> Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    {{-- Hidden Form for Bulk Delete --}}
    <form id="bulkDeleteForm" action="{{ route('file-manager.bulk-delete') }}" method="POST" style="display: none;">
        @csrf @method('DELETE')
        <div id="bulkDeleteInputs"></div>
    </form>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.3/ace.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.3/ext-language_tools.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('fileManagerApp', () => ({
            viewMode: localStorage.getItem('fmViewMode') || 'grid',
            selectedItems: [],
            
            // Rename State
            renameModalOpen: true,
            renameId: '',
            renameType: '',
            renameName: '',

            // Editor State
            editModalOpen: true,
            editingFileId: null,
            editingFileName: '',
            isLoading: false,
            isSaving: false,
            editor: null,

            initApp() {
                console.log('File Manager Initialized');
            },

            setView(mode) {
                this.viewMode = mode;
                localStorage.setItem('fmViewMode', mode);
            },

            toggleAll() {
                const allCheckboxes = document.querySelectorAll('.item-checkbox');
                const allValues = Array.from(allCheckboxes).map(cb => cb.value);
                const allSelected = allValues.every(val => this.selectedItems.includes(val));
                this.selectedItems = allSelected ? [] : allValues;
            },

            // --- XỬ LÝ EDITOR (Quan trọng) ---
            initEditor() {
                if (!this.editor) {
                    this.editor = ace.edit('aceEditor');
                    this.editor.setTheme('ace/theme/monokai');
                    this.editor.setShowPrintMargin(false);
                    this.editor.setOptions({
                        fontSize: '14px',
                        enableBasicAutocompletion: true,
                        enableLiveAutocompletion: true
                    });
                    
                    // Binding phím tắt Ctrl+S
                    this.editor.commands.addCommand({
                        name: 'save',
                        bindKey: {win: 'Ctrl-S', mac: 'Command-S'},
                        exec: () => { this.saveContent(); }
                    });
                }
            },

            openEdit(id) {
                this.isLoading = true;
                this.editingFileId = id;
                this.editModalOpen = true; // Mở modal trước

                // Dùng $nextTick để đảm bảo DOM modal đã hiện ra rồi mới vẽ Editor
                this.$nextTick(() => {
                    this.initEditor();
                    this.editor.setValue('', -1); // Clear cũ
                    this.editor.resize(); // Fix lỗi editor bị méo khi ẩn/hiện
                    
                    fetch(`{{ url('/file-manager/content') }}/${id}`)
                        .then(res => {
                            if(!res.ok) throw new Error('Cannot load content');
                            return res.json();
                        })
                        .then(data => {
                            if(data.error) throw new Error(data.error);
                            
                            this.editingFileName = data.name;
                            
                            // Set Mode cho Ace
                            let mode = 'ace/mode/text';
                            const ext = data.extension;
                            const modeMap = {
                                'php': 'php', 'js': 'javascript', 'css': 'css', 
                                'html': 'html', 'json': 'json', 'sql': 'sql', 
                                'xml': 'xml', 'md': 'markdown', 'env': 'properties'
                            };
                            if(modeMap[ext]) mode = `ace/mode/${modeMap[ext]}`;
                            
                            this.editor.getSession().setMode(mode);
                            this.editor.setValue(data.content, -1);
                        })
                        .catch(err => {
                            alert(err.message);
                            this.editModalOpen = false;
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                });
            },

            closeEditor() {
                this.editModalOpen = false;
                this.editingFileId = null;
            },

            saveContent() {
                if(!this.editingFileId) return;
                this.isSaving = true;
                const content = this.editor.getValue();

                fetch(`{{ url('/file-manager/save-content') }}/${this.editingFileId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ content: content })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert('Saved successfully!');
                        this.closeEditor();
                    } else {
                        alert('Save failed');
                    }
                })
                .catch(err => alert('Error saving file'))
                .finally(() => {
                    this.isSaving = false;
                });
            }
        }));
    });

    function submitBulkDelete() {
        const container = document.getElementById('bulkDeleteInputs');
        container.innerHTML = '';
        
        const selected = document.querySelectorAll('.item-checkbox:checked');
        if(selected.length === 0) return;

        if(!confirm(`Are you sure you want to delete ${selected.length} items?`)) return;

        selected.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'items[]';
            input.value = cb.value;
            container.appendChild(input);
        });

        document.getElementById('bulkDeleteForm').submit();
    }

    function openRename(id, name, type) {
        document.getElementById('renameInputId').value = id;
        document.getElementById('renameInputType').value = type;
        document.getElementById('renameInputName').value = name;
        document.getElementById('renameModal').style.display = 'flex';
    }

    function closeRenameModal() {
        document.getElementById('renameModal').style.display = 'none';
    }

    function copyLink(url) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => alert('Link copied to clipboard!')).catch(err => alert('Failed to copy'));
        } else {
            // Fallback
            var textArea = document.createElement("textarea");
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                alert('Link copied to clipboard!');
            } catch (err) { alert('Unable to copy'); }
            document.body.removeChild(textArea);
        }
    }

    function uploadFile(input, autoRename = false) {
        if (input.files && input.files[0]) {
            const formData = new FormData();
            formData.append('file', input.files[0]);
            formData.append('folder_id', '{{ $parentId }}');
            formData.append('_token', '{{ csrf_token() }}');

            if (autoRename) {
                formData.append('auto_rename', 'true');
            }

            const btn = input.parentElement;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<iconify-icon icon="svg-spinners:180-ring-with-bg"></iconify-icon> Uploading...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            fetch('{{ route("file-manager.upload") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'conflict') {
                    if (confirm(data.message)) {
                        uploadFile(input, true);
                    } else {
                        btn.innerHTML = originalText;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                        input.value = '';
                    }
                } else if(data.success) {
                    window.location.reload();
                } else {
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                    btn.innerHTML = originalText;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload failed due to network error');
                btn.innerHTML = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        }
    }
</script>
</x-app-layout>
