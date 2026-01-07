@props(['fieldId'])

<div x-data="{ open: false, files: [], selectedUrl: '' }" class="w-full">
    <div class="flex gap-2">
        <input type="text" id="{{ $fieldId }}" name="{{ $fieldId }}" x-model="selectedUrl" class="border p-2 rounded w-full" readonly placeholder="Selected file URL...">
        <button type="button" @click="open = true; fetchFiles()" class="bg-gray-200 px-4 py-2 rounded">Select</button>
    </div>

    <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded w-3/4 h-3/4 overflow-y-auto">
            <div class="flex justify-between mb-4">
                <h3 class="text-xl font-bold">Select File</h3>
                <button type="button" @click="open = false" class="text-red-500">Close</button>
            </div>
            
            <div class="grid grid-cols-5 gap-4">
                <template x-for="file in files" :key="file.id">
                    <div @click="selectedUrl = file.original_url; open = false" class="cursor-pointer border p-2 hover:bg-blue-50">
                        <template x-if="file.mime_type.includes('image')">
                            <img :src="file.original_url" class="h-24 w-full object-cover">
                        </template>
                        <template x-if="!file.mime_type.includes('image')">
                            <div class="h-24 w-full bg-gray-100 flex items-center justify-center">FILE</div>
                        </template>
                        <p class="text-xs mt-1 truncate" x-text="file.file_name"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function fetchFiles() {
            fetch('{{ route("file-manager.index") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                document.querySelector('[x-data]').__x.$data.files = data.files.data;
            });
        }
    </script>
</div>