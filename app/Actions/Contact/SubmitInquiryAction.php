<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\DTOs\Contact\InquiryData;
use App\Models\Inquiry;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewInquiryReceivedNotification;
use Illuminate\Support\Facades\Notification;

final readonly class SubmitInquiryAction
{
    public function handle(InquiryData $data): Inquiry
    {
        $inquiry = Inquiry::create([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'subject' => $data->subject,
            'message' => $data->message,
            'ip_address' => $data->ipAddress,
            'user_agent' => $data->userAgent,
        ]);

        $this->notifyAdmin($inquiry);

        return $inquiry;
    }

    private function notifyAdmin(Inquiry $inquiry): void
    {
        $recipient = Setting::get('admin_notification_email')
            ?? User::role('admin')->value('email');

        if (blank($recipient)) {
            return;
        }

        Notification::route('mail', $recipient)->notify(new NewInquiryReceivedNotification($inquiry));
    }
}
