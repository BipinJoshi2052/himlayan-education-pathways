<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class NewInquiryReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Inquiry $inquiry) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inquiry = $this->inquiry;

        return (new MailMessage)
            ->subject('New inquiry: '.($inquiry->subject ?: 'Contact form submission'))
            ->greeting('New contact form submission')
            ->line('From: '.$inquiry->name.' <'.$inquiry->email.'>')
            ->when($inquiry->phone, fn ($mail) => $mail->line('Phone: '.$inquiry->phone))
            ->line('Message:')
            ->line($inquiry->message)
            ->action('View in admin panel', route('admin.inquiries.show', $inquiry))
            ->line('Received from IP '.$inquiry->ip_address.' at '.$inquiry->created_at->format('Y-m-d H:i'));
    }
}
