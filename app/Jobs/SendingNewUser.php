<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\{
    NewUserMail
};
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\{
    Mail,
    Config
};

class SendingNewUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data, $firstName, $lastName, $email, $subject, $message;

    // protected $data;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        

        // Extract data from the array
        $firstName = $this->data['first_name'];
        $lastName = $this->data['last_name'];
        $email = $this->data['email'];
        $subject = $this->data['subject'];
        $message =  $this->data['message'];
        
        Mail::to($email)->send(new NewUserMail($this->data));
    }
}