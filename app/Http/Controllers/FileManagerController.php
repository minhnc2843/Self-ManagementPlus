<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileManagerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $parentId = $request->query('folder_id');
        $type = $request->query('type'); 
        $search = $request->query('search');

        // Query Folders
        $folderQuery = $user->folders()->where('parent_id', $parentId);
        if ($search) {
            $folderQuery->where('name', 'like', '%' . $search . '%');
        }
        $folders = $folderQuery->orderBy('name')->get();

        // Query Files
        $query = $user->media()->where('custom_properties->folder_id', $parentId);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($type === 'image') {
            $query->where('mime_type', 'LIKE', 'image/%');
        } elseif ($type === 'video') {
            $query->where('mime_type', 'LIKE', 'video/%');
        } elseif ($type === 'audio') {
            $query->where('mime_type', 'LIKE', 'audio/%');
        }

        $files = $query->orderBy('created_at', 'desc')->paginate(40);

        if ($request->ajax()) {
            return response()->json([
                'folders' => $folders,
                'files' => $files
            ]);
        }

        return view('file-manager.index', compact('folders', 'files', 'parentId'));
    }

    public function createFolder(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $user = Auth::user();
        $parentId = $request->parent_id ?: null;

        // Kiểm tra xem thư mục đã tồn tại chưa
        if ($user->folders()->where('parent_id', $parentId)->where('name', $request->name)->exists()) {
            return back()->with('error', 'Folder "' . $request->name . '" already exists in this location.');
        }

        $user->folders()->create([
            'name' => $request->name,
            'parent_id' => $parentId,
        ]);

        return back()->with('success', 'Folder created successfully');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', 
        ]);

        $user = Auth::user();
        $folderId = $request->input('folder_id');
        $file = $request->file('file');
        
        // Lấy tên gốc và đuôi
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        // Kiểm tra file đã tồn tại chưa
        $exists = $user->media()
            ->where('custom_properties->folder_id', $folderId)
            ->where('name', $originalName)
            ->exists();

        if ($exists && !$request->has('auto_rename')) {
            return response()->json(['status' => 'conflict', 'message' => "File '{$originalName}.{$extension}' already exists. Do you want to rename it automatically (e.g., {$originalName}1.{$extension})?"]);
        }

        // Nếu có cờ auto_rename hoặc chưa tồn tại thì xử lý tên
        $fileName = ($exists && $request->has('auto_rename')) ? $this->generateUniqueFileName($user, $folderId, $originalName, $extension) : $originalName;
        $fullName = $fileName . '.' . $extension;

        $media = $user->addMedia($file)
            ->usingName($fileName) // Tên hiển thị (không đuôi)
            ->usingFileName($fullName) // Tên file vật lý (có đuôi)
            ->withCustomProperties(['folder_id' => $folderId])
            ->toMediaCollection('user_files');

        return response()->json(['success' => true, 'media' => $media]);
    }

    // Hàm phụ trợ: Tạo tên unique (file(1), file(2)...)
    private function generateUniqueFileName($user, $folderId, $name, $extension)
    {
        $newName = $name;
        $counter = 1;

        // Kiểm tra xem trong DB đã có file này ở thư mục này chưa
        while ($user->media()
            ->where('custom_properties->folder_id', $folderId)
            ->where('name', $newName) // Spatie lưu name không có extension mặc định, nhưng ta nên check kỹ
            ->exists()) {
            
            $newName = $name . $counter; // Đổi từ name(1) thành name1 theo yêu cầu
            $counter++;
        }

        return $newName;
    }

public function rename(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'type' => 'required|in:folder,file',
            'new_name' => 'required|string|max:255', // Tên mới không bao gồm đuôi file
        ]);

        $user = Auth::user();
        
        if ($request->type === 'folder') {
            // Xử lý parent_id null nếu là root
            $parentId = $request->parent_id ?: null;
            // Check trùng folder
            $exists = $user->folders()
                ->where('parent_id', $parentId) 
                ->where('name', $request->new_name)
                ->where('id', '!=', $request->id)
                ->exists();
                
            if($exists) return back()->withErrors(['rename' => 'Folder name "' . $request->new_name . '" already exists in this location.']);

            $folder = $user->folders()->findOrFail($request->id);
            $folder->update(['name' => $request->new_name]);

        } else {
            $media = $user->media()->findOrFail($request->id);
            $folderId = $media->getCustomProperty('folder_id');

            // Check trùng file
            $exists = $user->media()
                ->where('custom_properties->folder_id', $folderId)
                ->where('name', $request->new_name)
                ->where('id', '!=', $request->id)
                ->exists();

            if($exists) return back()->withErrors(['rename' => 'File name "' . $request->new_name . '" already exists in this location.']);

            $media->name = $request->new_name;
            // Lưu ý: Không đổi file_name vật lý để tránh lỗi đường dẫn, chỉ đổi tên hiển thị
            $media->save();
        }

        return back()->with('success', 'Renamed successfully');
    }
    public function delete(Request $request)
    {
        $id = $request->id;
        $type = $request->type; 

        if ($type === 'folder') {
            $folder = Auth::user()->folders()->findOrFail($id);
            $folder->delete(); 
        } else {
            $media = Auth::user()->media()->findOrFail($id);
            $media->delete();
        }

        return back()->with('success', 'Deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'string', // Format: "type|id"
        ]);

        $user = Auth::user();
        $count = 0;

        foreach ($request->items as $item) {
            [$type, $id] = explode('|', $item);

            try {
                if ($type === 'folder') {
                    $user->folders()->where('id', $id)->delete();
                } elseif ($type === 'file') {
                    $user->media()->where('id', $id)->delete();
                }
                $count++;
            } catch (\Exception $e) {
                // Bỏ qua lỗi nếu không tìm thấy hoặc không xóa được để tiếp tục các item khác
                continue;
            }
        }

        return back()->with('success', "Deleted $count items successfully");
    }

    public function download($id)
    {
        $media = Auth::user()->media()->findOrFail($id);
        return response()->download($media->getPath(), $media->file_name);
    }
   public function getContent($id)
    {
        $media = Auth::user()->media()->findOrFail($id);
        
        $editableExts = ['txt', 'sql', 'md', 'html', 'css', 'js', 'json', 'env', 'log', 'php', 'xml', 'yml', 'yaml'];
        $ext = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $editableExts)) {
            return response()->json(['error' => 'This file type (' . $ext . ') is not editable via web.'], 422);
        }

        if ($media->size > 1024 * 1024) { 
            return response()->json(['error' => 'File is too large to edit (Max 1MB).'], 422);
        }

        if (file_exists($media->getPath())) {
            $content = file_get_contents($media->getPath());
            return response()->json([
                'content' => $content, 
                'name' => $media->file_name,
                'extension' => $ext
            ]);
        }

        return response()->json(['error' => 'File not found on disk'], 404);
    }

    // 5. LƯU NỘI DUNG FILE
    public function saveContent(Request $request, $id)
    {
        $request->validate(['content' => 'nullable|string']);
        
        $media = Auth::user()->media()->findOrFail($id);
        $content = $request->input('content') ?? '';

        file_put_contents($media->getPath(), $content);
        
        clearstatcache();
        $media->size = filesize($media->getPath());
        $media->save();

        return response()->json(['success' => true]);
    }
}