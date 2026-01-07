<?php

namespace App\Http\Controllers;
use App\Models\Folder;
use App\Models\User;
use App\Models\Link;
use App\Models\Clipboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DigitalAssetController extends Controller
{
    // --- LINKS ---
    public function indexLinks(Request $request)
    {
        $query = Auth::user()->hasMany(Link::class);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('url', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $links = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('file-manager.links', compact('links'));
    }

    public function storeLink(Request $request)
    {
        $request->validate(['title' => 'required', 'url' => 'required|url']);
        Auth::user()->hasMany(Link::class)->create($request->except('_token'));
        return back()->with('success', 'Link saved successfully');
    }

    public function updateLink(Request $request, $id)
    {
        $link = Auth::user()->hasMany(Link::class)->findOrFail($id);
        $link->update($request->except('_token'));
        return back()->with('success', 'Link updated');
    }

    public function destroyLink($id)
    {
        Auth::user()->hasMany(Link::class)->findOrFail($id)->delete();
        return back()->with('success', 'Link deleted');
    }

    // --- CLIPBOARD ---
    public function indexClipboard(Request $request)
    {
        $query = Auth::user()->hasMany(Clipboard::class);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $clipboards = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('file-manager.clipboards', compact('clipboards'));
    }

    public function storeClipboard(Request $request)
    {
        $request->validate(['title' => 'required', 'content' => 'required']);
        Auth::user()->hasMany(Clipboard::class)->create($request->except('_token'));
        return back()->with('success', 'Content saved to clipboard');
    }

    public function updateClipboard(Request $request, $id)
    {
        $item = Auth::user()->hasMany(Clipboard::class)->findOrFail($id);
        $item->update($request->except(['_token', '_method']));
        return back()->with('success', 'Clipboard updated');
    }

    public function destroyClipboard($id)
    {
        Auth::user()->hasMany(Clipboard::class)->findOrFail($id)->delete();
        return back()->with('success', 'Clipboard deleted');
    }
}