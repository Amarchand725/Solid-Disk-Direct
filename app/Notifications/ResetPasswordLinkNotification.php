<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class ResetPasswordLinkNotification extends Notification
{
    protected $token;
    protected $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = config('system.frontend_base_url') ?? 'http://localhost:5173/'; // Vue app URL
        $resetUrl = "{$frontendUrl}/reset-password?token={$this->token}&email={$this->email}";

        return (new MailMessage)
            ->subject('Reset Your Password')
            ->line('You requested a password reset.')
            ->action('Reset Password', $resetUrl)
            ->line('If you did not request this, no further action is required.');
    }
}
