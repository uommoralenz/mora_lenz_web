<?php

namespace App\Http\Controllers\Public_;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public const SUBJECTS = [
        'general' => 'General Inquiry',
        'collaboration' => 'Collaboration',
        'membership' => 'Membership',
        'event' => 'Event Inquiry',
        'other' => 'Other',
    ];

    public function store(Request $request): RedirectResponse
    {
        // Five submissions per IP per hour is plenty for a real visitor and
        // keeps a bot from filling the table.
        $key = 'contact:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()
                ->withInput()
                ->with('contact_error', 'Too many messages sent. Please try again later.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'subject' => ['required', 'string', 'in:'.implode(',', array_keys(self::SUBJECTS))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot: a real visitor never fills a hidden field.
            'website' => ['nullable', 'size:0'],
        ], [
            'website.size' => 'Your message could not be sent.',
        ]);

        RateLimiter::hit($key, 3600);

        $message = ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'ip_address' => $request->ip(),
        ]);

        $this->notify($message);

        return back()->with('contact_success', "Thanks {$message->name}! Your message has been received.");
    }

    /**
     * Email the club, if mail is configured. The message is already safely in
     * the database at this point, so a mail failure must never surface as an
     * error to the visitor — it is logged instead.
     */
    protected function notify(ContactMessage $message): void
    {
        if (! config('moralenz.contact.mail_enabled')) {
            return;
        }

        try {
            $subjectLabel = self::SUBJECTS[$message->subject] ?? $message->subject;

            Mail::raw(
                "New message from the Mora Lenz website\n\n"
                ."From:    {$message->name} <{$message->email}>\n"
                ."Subject: {$subjectLabel}\n"
                ."Sent:    {$message->created_at->format('Y-m-d H:i')}\n\n"
                .Str::of($message->message)->trim()."\n",
                function ($mail) use ($message, $subjectLabel) {
                    $mail->to(config('moralenz.contact.to'))
                        ->replyTo($message->email, $message->name)
                        ->subject("Contact Form: {$subjectLabel}");
                }
            );
        } catch (\Throwable $e) {
            Log::warning('Contact email could not be sent.', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
