<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::orderBy('remind_at')->get();
        return view('reminders.index', compact('reminders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'remind_at' => 'required|date'
        ]);

        Reminder::create([
            'title' => $request->title,
            'message' => $request->message,
            'remind_at' => $request->remind_at,
            'is_snoozed' => false,
            'snooze_minutes' => null
        ]);

        return redirect()->back()->with('success', 'Reminder Created Successfully!');
    }

    public function snooze($id)
    {
        $reminder = Reminder::findOrFail($id);
        
        // Snooze for 5 minutes - explicitly cast to integer
        $minutes = 5;
        
        $reminder->update([
            'remind_at' => Carbon::now()->addMinutes((int) $minutes), // Explicit integer casting
            'is_snoozed' => true,
            'snooze_minutes' => $minutes
        ]);

        return redirect()->back()->with('success', "Reminder Snoozed for {$minutes} Minutes!");
    }

    public function snoozeCustom(Request $request, $id)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1|max:60'
        ]);

        $reminder = Reminder::findOrFail($id);
        
        // Explicitly cast to integer
        $minutes = (int) $request->minutes;
        
        $reminder->update([
            'remind_at' => Carbon::now()->addMinutes($minutes),
            'is_snoozed' => true,
            'snooze_minutes' => $minutes
        ]);

        return redirect()->back()->with('success', "Reminder Snoozed for {$minutes} Minutes!");
    }

    public function delete($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->delete();
        
        return redirect()->back()->with('success', 'Reminder Deleted Successfully!');
    }

    public function markAsCompleted($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->delete();
        
        return redirect()->back()->with('success', 'Reminder Marked as Completed!');
    }
}