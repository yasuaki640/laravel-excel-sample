<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Http\Controllers\UserController;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * Class ExportCompleted
 * @package App\Notifications
 */
class ExportCompleted extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    private string $fileName;

    /**
     * Create a new notification instance.
     *
     * @param string $fileName
     * @return void
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $s3 = Storage::disk(UserController::STORAGE_S3);

        return (new MailMessage)
            ->line('Export has been completed.')
            ->line('Please click link to download a exported file.')
            ->action('Download ' . $this->fileName, $s3->url($this->fileName));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
