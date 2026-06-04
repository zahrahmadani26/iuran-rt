<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PembayaranNotification extends Notification
{
    use Queueable;
    
    protected $pembayaran;
    
    public function __construct($pembayaran)
    {
        $this->pembayaran = $pembayaran;
    }
    
    public function via($notifiable)
    {
        return ['database'];
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Status pembayaran iuran anda: ' . $this->pembayaran->status,
            'pembayaran_id' => $this->pembayaran->id,
            'tanggal_bayar' => $this->pembayaran->tanggal_bayar
        ];
    }
}