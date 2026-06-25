<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UpdateNote;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function updateNotes()
    {
        $notes = UpdateNote::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('pages.backend.support.update_notes', compact('notes'));
    }
}
