<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function page()
    {
        return view('calendar');
    }

    public function index()
    {
        try {
            $events = Event::get();
            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch events',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'color' => 'nullable'
        ]);

        $event = new Event();
        $event->title = $validatedData['title'];
        $event->description = $validatedData['description'] ?? null;
        $event->start_date = $validatedData['start_date'];
        $event->end_date = $validatedData['end_date'];
        $event->color = $validatedData['color'] ?? '#3788d8';
        $event->save();

        return response()->json($event, 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'color' => 'nullable'
        ]);

        $event = Event::findOrFail($id);

        $event->update($validatedData);

        return response()->json($event);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        $event->delete();

        return response()->json(null, 204);
    }
}
