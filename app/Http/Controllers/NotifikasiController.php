<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Fetch notification list JSON for real-time header polling.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifikasi()->latest()->take(10)->get();
        $unreadCount = $user->notifikasi()->where('is_read', false)->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Tandai notifikasi sebagai dibaca (Read).
     */
    public function markAsRead(Request $request, Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $notifikasi->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($notifikasi->link) {
            return redirect($notifikasi->link);
        }

        return redirect()->back();
    }

    /**
     * Tandai semua notifikasi milik user sebagai dibaca.
     */
    public function markAllAsRead(Request $request)
    {
        Auth::user()->notifikasi()->where('is_read', false)->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
