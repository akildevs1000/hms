<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class Mytime2CloudHealth extends Command
{
    protected $signature   = 'check:mytime2cloud-health';
    protected $description = 'Check Server health at mytime2cloud.com and send an email if it is down';

    public function handle()
    {
        $to  = 'francisgill1000@gmail.com.com';

        $url = 'https://mytime2cloud.com/health';

        try {
            $response = Http::withoutVerifying()->timeout(5)->get($url);

            if ($response->successful() && $response->json('status') === 'ok') {
                $message = "✅ Mytime2Cloud Server is UP.";
                // $this->sendDownEmail($to, "UP", $message);
            } else {
                $status  = $response->status();
                $message = "⚠️ Server is responding but not healthy.";
                $this->error($message);
                $this->sendDownEmail($to, $status, $message);
            }
        } catch (\Exception $e) {
            $message = "Exception: " . $e->getMessage();
            $this->error($message);
            $this->sendDownEmail($to, 'DOWN', $message);
        }
    }

    protected function sendDownEmail($to = 'your-email@example.com', $status = 'DOWN', $reason)
    {
        $subject = "🚨 Mytime2Cloud Server is $status";
        $message = <<<EOT
The Server at https://mytime2cloud.com/health is $status.

🛑 Reason: $reason

⏱ Checked at: {$this->now()}

— Laravel Health Monitor
EOT;

        Mail::raw($message, function ($mail) use ($to, $subject) {
            $mail->to($to)
                ->subject($subject);
        });

        $this->warn("📧 Alert email sent to $to");
    }

    protected function now()
    {
        return now()->toDateTimeString();
    }
}
