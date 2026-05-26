<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use Illuminate\Http\Request;

class MailLogController extends Controller
{
    public function index(Request $request)
    {
        $query = MailLog::latest('sent_at');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('to_email', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%")
                  ->orWhere('mailable_class', 'like', "%{$request->search}%");
            });
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('admin.mail-log.index', compact('logs'));
    }

    public function show(MailLog $mailLog)
    {
        return view('admin.mail-log.show', compact('mailLog'));
    }

    /**
     * Render the raw HTML body for iframe preview.
     */
    public function preview(MailLog $mailLog)
    {
        return response($mailLog->html_body ?? '<p>Aucun contenu HTML disponible.</p>')
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }

    public function destroy(MailLog $mailLog)
    {
        $mailLog->delete();
        return redirect()->route('admin.mail-log.index')->with('success', 'Entrée supprimée.');
    }
}
