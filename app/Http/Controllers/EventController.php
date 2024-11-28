<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{

    public function index(): JsonResponse
    {
        $events = Event::query()
            ->with('participants')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'result' => [$events]
        ]);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json([
            'result' => $event
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|unique:' . Event::class,
            'text' => 'required|string',
        ]);

        $event = Event::query()->make([
            'title' => $validated['title'],
            'text' => $validated['text'],
        ]);

        $event->creator()
            ->associate($request->user())
            ->save();

        return response()->json([
            'result' => [$event]
        ]);
    }

    public function participate(Event $event): JsonResponse
    {
        $alreadyExist = $event->participants()->where('user_id', Auth::id())->exists();

        if (!$alreadyExist) {
            $event->participants()->attach(Auth::id());
        }

        return response()->json([
            'errors' => $alreadyExist ? 'You are already participating' : null,
            'result' => $event
        ]);
    }

    public function cancellation(Event $event): JsonResponse
    {
        $event->participants()->detach(Auth::id());

        return response()->json([
            'result' => $event
        ]);
    }

    public function delete(Event $event): Response
    {
        if ($event->creator->id == Auth::id()) {
            $event->delete();
            return response('success', 200);
        }

        return response('not allowed for this user', 401);
    }

//    public function getParticipants(Event $event)
//    {
//        $participants = $event->participants;
//
//        return response()->json([
//            'result' => [$participants]
//        ]);
//    }
}
