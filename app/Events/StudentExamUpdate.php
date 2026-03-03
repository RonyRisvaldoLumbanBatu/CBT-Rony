<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// ShouldBroadcastNow artinya pesan ini dikirim DETIK INI JUGA tanpa antrean
class StudentExamUpdate implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $examId;

    public $studentName;

    public $statusMessage;

    public function __construct($examId, $studentName, $statusMessage)
    {
        $this->examId = $examId;
        $this->studentName = $studentName;
        $this->statusMessage = $statusMessage;
    }

    // Tentukan "Frekuensi Radio" (Channel) tempat pesan ini dipancarkan
    public function broadcastOn()
    {
        return new Channel('exam-monitor.'.$this->examId);
    }
}
