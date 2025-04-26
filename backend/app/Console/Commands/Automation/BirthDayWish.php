<?php

namespace App\Console\Commands\Automation;

use App\Jobs\BirthdayWishEmailCustomer;
use App\Jobs\EmailSender;
use App\Jobs\WhatsappSender;
use App\Models\Customer;
use App\Models\Template;
use Illuminate\Console\Command;
use Carbon\Carbon;

class BirthDayWish extends Command
{
    protected $type = Template::BIRTHDAY_WISH;

    protected $fields = ['title', 'full_name', "whatsapp", "email", "company_id"];

    protected $signature = 'birthday:wish:customer';

    protected $description = 'Send birthday wishes to customer with WhatsApp numbers';

    protected $templates = [];

    protected $tags = [];

    public function handle()
    {
        $this->tags = Template::TAGS[$this->type];

        $today = Carbon::now()->format('m-d');

        $customers = Customer::whereNotNull('whatsapp')
            ->where(function ($query) {
                $query->where('whatsapp', 'like', '91%')
                    ->orWhere('whatsapp', 'like', '971%');
            })
            ->whereRaw("TO_CHAR(dob, 'MM-DD') = ?", [$today])
            ->get(['title', 'first_name', 'last_name', 'whatsapp', 'email', 'dob', "company_id"]);

        if (!count($customers)) {
            $this->info('No birthdays today.');
            return;
        }

        $this->templates = Template::whereActionId(["action_id" => $this->type])->orderBy("id", "desc")->get();

        if (!count($this->templates)) {
            $this->info('Template not found.');
            return;
        }

        $responses = [];

        foreach ($customers as $customerObject) {

            $customer = $customerObject->toArray();

            $fields = [];

            foreach ($this->fields as $field) {
                $fields[$field] = trim($customer[$field]);
            }

            if ($customer['whatsapp']) {
                $whatsappPayload = [
                    'recipient' => $customer['whatsapp'],
                    'text' => $this->prepareMessage($fields, "whatsapp"),
                    'company_id' => $customer['company_id'],
                ];
                WhatsappSender::dispatch($whatsappPayload);
            }

            if ($customer['email']) {
                $emailPayload = [
                    'recipient' => $customer['email'],
                    'text' => $this->prepareMessage($fields, "email"),
                    'company_id' => $customer['company_id'],
                ];
                EmailSender::dispatch($emailPayload);
            }

            $responses[] = ["whatsapp" => $whatsappPayload ?? "No Whatsapp Found", "email" => $emailPayload ?? "No Email found", "fields" => $fields];
        }

        $this->info(json_encode($responses, JSON_PRETTY_PRINT));;
    }

    function prepareMessage(array $fields, string $type): ?string
    {
        $template = collect($this->templates)->firstWhere('medium', $type);

        if (!$template) {
            return null;
        }

        $messageBody = $template->body ?? Template::DEFAULT_MESSAGES[$this->type];
        $replacedMessage = str_replace($this->tags, $fields, $messageBody);
        $finalMessage = preg_replace('/<p>(.*?)<\/p>/s', "$1\n", $replacedMessage);

        return trim(strip_tags($finalMessage));
    }
}
