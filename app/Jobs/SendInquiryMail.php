<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\{
    AdminInquiryMail,
    InquiryMail
};
use App\Models\Taxonomy;
use Illuminate\Support\Facades\{
    Mail
};

class SendInquiryMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
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
        Mail::to($this->data->email_address)->queue(new InquiryMail($this->data));
        // Mail::to(['marjoevelasco.dbmanila@gmail.com'])->queue(new AdminInquiryMail($this->data));    
        $record = Taxonomy::select('email_recipients', 'type', 'name')
        ->where('type', $this->data->contact_type && $this->data->contact_type === 'lead' ? Taxonomy::TYPE_FORM_PAGE : Taxonomy::TYPE_INQUIRY)
        ->where('name', $this->data->type)->first();
        // \Log::info($record);
        $emails = $record ? $record->email_recipients : null;
        if ($emails) {
            foreach ($emails as $email) {
                Mail::to([$email])->queue(new AdminInquiryMail($this->data));
            }
        } 
        // Mail::to(['info@cebulandmasters.com'])->queue(new AdminInquiryMail($this->data));
        
    }
}