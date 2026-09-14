<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Public_\ContactController;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $messages = ContactMessage::query()
            ->when($request->boolean('unread_only'), fn ($q) => $q->where('is_read', false))
            ->orderByDesc('created_at')
            ->paginate(min(100, max(10, (int) $request->query('per_page', 25))));

        return response()->json([
            'data' => collect($messages->items())->map(fn ($m) => $this->present($m)),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'unread' => ContactMessage::where('is_read', false)->count(),
            ],
        ]);
    }

    public function update(Request $request, ContactMessage $message): JsonResponse
    {
        $data = $request->validate([
            'is_read' => ['required', 'boolean'],
        ]);

        $message->update(['is_read' => $data['is_read']]);

        return response()->json(['data' => $this->present($message)]);
    }

    public function destroy(ContactMessage $message): JsonResponse
    {
        $message->delete();

        return response()->json(['message' => 'Message deleted.']);
    }

    protected function present(ContactMessage $message): array
    {
        return [
            'id' => $message->id,
            'name' => $message->name,
            'email' => $message->email,
            'subject' => $message->subject,
            'subject_label' => ContactController::SUBJECTS[$message->subject] ?? $message->subject,
            'message' => $message->message,
            'is_read' => (bool) $message->is_read,
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }
}
