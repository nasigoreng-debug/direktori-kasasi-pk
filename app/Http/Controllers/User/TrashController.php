<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Upload;

class TrashController extends Controller
{
    public function index()
    {
        $uploads = Upload::onlyTrashed()
            ->where('user_id', Auth::id())
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('user.trash.index', compact('uploads'));
    }

    public function restore($id)
    {
        $upload = Upload::onlyTrashed()
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $upload->restore();

        return redirect()->route('user.trash.index')
            ->with('success', 'Putusan berhasil dipulihkan!');
    }

    public function forceDelete($id)
    {
        $upload = Upload::onlyTrashed()
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        // Hapus file dari storage
        if (Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        // Hapus permanen dari database
        $upload->forceDelete();

        return redirect()->route('user.trash.index')
            ->with('success', 'Putusan berhasil dihapus permanen!');
    }
}
