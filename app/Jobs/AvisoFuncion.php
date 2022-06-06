<?php

namespace App\Jobs;

use App\View\Components\alert1;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mike4ip\ChatApi;

class AvisoFuncion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $cel;
    public $mens;

    public function __construct($cel, $mens)
    {
        $this->cel = $cel;
        $this->mens = $mens;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $api = new ChatApi(
            'yb7lq7jpotu31kgq', 
            'https://api.chat-api.com/instance361534' ); 

        $api->sendPhoneMessage($this->cel, $this->mens);
    }
}
