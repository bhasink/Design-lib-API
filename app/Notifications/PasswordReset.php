<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as Notification;

class PasswordReset extends Notification
{


    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(config('app.client_url').'/password/reset/'.$this->token).'?email='.urlencode($notifiable->email);

        return (new MailMessage)
                    ->line('Password reset mail.')
                    ->action('Reset Password', $url)
                    ->line('No action!');
    }

    

}
