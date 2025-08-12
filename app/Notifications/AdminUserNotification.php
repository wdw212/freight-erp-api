<?php
/**
 * 账号通知
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminUserNotification extends Notification
{
    use Queueable;

    public $title;

    public $content;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->title = $data['title'];
        $this->content = $data['content'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @param object $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => '测试消息标题',
            'content' => '测试消息内容'
        ];
    }
}
