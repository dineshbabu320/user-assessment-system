<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RatingUpgradeRequest;

class RatingUpgradeController extends Controller
{
    public function index()
    {
        $requests = RatingUpgradeRequest::all();
        return view('admin.rating-upgrade-requests', compact('requests'));
    }

    public function markAsCompleted($id)
    {
        $request = RatingUpgradeRequest::findOrFail($id);
        $request->update(['completed' => true]);

        return redirect()->route('admin.ratingUpgradeRequests')->with('success', 'Request marked as completed.');
    }
}
