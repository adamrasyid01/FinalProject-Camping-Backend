<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $campingSiteId = $request->camping_site_id;

        if (!$user->bookmarks()->where('camping_site_id', $campingSiteId)->exists()) {
            $user->bookmarks()->attach($campingSiteId);

            // Ambil data yang baru ditambahkan
            $bookmark = $user->bookmarks()->where('camping_site_id', $campingSiteId)->first();

            return ResponseFormatter::success($bookmark, 'Bookmark added');
        }

        return ResponseFormatter::error(null, 'Bookmark already exists', 409);
    }


    public function destroy($campingSiteId)
    {
        $user = auth()->user();
        $user->bookmarks()->detach($campingSiteId);

        return ResponseFormatter::success(null, 'Bookmark removed');
    }

    public function index()
    {
        return auth()->user()->bookmarks()->get();
    }
}
