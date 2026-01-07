<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="{{ route('file-manager.index') }}" 
           class="{{ request()->routeIs('file-manager.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
           📂 Files & Media
        </a>
        <a href="{{ route('assets.links') }}" 
           class="{{ request()->routeIs('assets.links') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
           🔗 Link Manager
        </a>
        <a href="{{ route('assets.clipboard') }}" 
           class="{{ request()->routeIs('assets.clipboard') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
           📋 Clipboard
        </a>
    </nav>
</div>