<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $events = CalendarEvent::whereBetween('event_date', [
            $request->start_date,
            $request->end_date
        ])->get();

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_date' => 'required|date',
            'type' => 'required|in:available,unavailable,holiday',
            'reason' => 'nullable|string|max:500',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_recurring' => 'boolean',
            'recurring_days' => 'nullable|array',
        ]);

        $event = CalendarEvent::create($request->all());

        return response()->json([
            'message' => 'Calendar event created successfully',
            'event' => $event
        ]);
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $request->validate([
            'event_date' => 'required|date',
            'type' => 'required|in:available,unavailable,holiday',
            'reason' => 'nullable|string|max:500',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_recurring' => 'boolean',
            'recurring_days' => 'nullable|array',
        ]);

        $calendarEvent->update($request->all());

        return response()->json([
            'message' => 'Calendar event updated successfully',
            'event' => $calendarEvent
        ]);
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $calendarEvent->delete();

        return response()->json([
            'message' => 'Calendar event deleted successfully'
        ]);
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date);
        $dayOfWeek = strtolower($date->englishDayOfWeek);

        // Get available events for the specific date
        $availableEvents = CalendarEvent::where('event_date', $request->date)
            ->where('type', 'available')
            ->get();

        // Get recurring available events
        $recurringEvents = CalendarEvent::where('is_recurring', true)
            ->where('type', 'available')
            ->whereJsonContains('recurring_days', $dayOfWeek)
            ->get();

        $slots = [];

        // Process specific date events
        foreach ($availableEvents as $event) {
            if ($event->start_time && $event->end_time) {
                $slots = array_merge($slots, $this->generateTimeSlots($event->start_time, $event->end_time));
            } else {
                // All day available
                $slots = array_merge($slots, $this->generateTimeSlots('09:00', '17:00'));
            }
        }

        // Process recurring events
        foreach ($recurringEvents as $event) {
            if ($event->start_time && $event->end_time) {
                $slots = array_merge($slots, $this->generateTimeSlots($event->start_time, $event->end_time));
            } else {
                $slots = array_merge($slots, $this->generateTimeSlots('09:00', '17:00'));
            }
        }

        // Remove duplicates and sort
        $slots = array_unique($slots);
        sort($slots);

        // Remove booked slots
        $bookedSlots = Appointment::where('appointment_date', $request->date)
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('appointment_time')
            ->toArray();

        $availableSlots = array_diff($slots, $bookedSlots);

        return response()->json([
            'date' => $request->date,
            'available_slots' => array_values($availableSlots)
        ]);
    }

    private function generateTimeSlots($start, $end, $interval = '30 minutes')
    {
        $slots = [];
        $current = Carbon::parse($start);
        $end = Carbon::parse($end);

        while ($current < $end) {
            $slots[] = $current->format('H:i');
            $current->add($interval);
        }

        return $slots;
    }
}