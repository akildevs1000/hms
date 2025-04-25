<?php

namespace App\Console\Commands;

use App\Jobs\BirthdayWishEmailCustomer;
use App\Jobs\BirthdayWishWhatsappCustomer;
use App\Models\Customer;
use App\Models\Template;
use App\Models\WhatsappClient;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class BookingCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday wishes to customer with WhatsApp numbers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $whatsapp = "971554501483";
        $email = "francisgill1000@gmail.com";

        $company_id = 1;


        $fields = [
            "title" => "Mr.",
            "full_name" => "Francis Gill",
            "check_in" => date("Y-m-d"),
            "check_out" => date("Y-m-d"),
        ];
        $templates = Template::whereActionId(["action_id" => Template::BOOKING_CREATE])->orderBy("id", "desc")->get();

        if (!count($templates)) {
            $this->info('Template not found.');
            return;
        }

        $responses = [];

        $arr = $this->prepareMessage($templates, $fields);

        if ($arr["whatsapp"]) {
            $whatsappPayload = [
                'recipient' => $whatsapp,
                'text' => $arr["whatsapp"],
                'clientId' => $this->getClient($company_id),
            ];
            BirthdayWishWhatsappCustomer::dispatch($whatsappPayload);

            $responses[] = ["whatsapp" => $whatsappPayload];
        }

        if ($arr["email"]) {
            $emailPayload = [
                'recipient' => $email,
                'text' => $arr["email"],
            ];

            BirthdayWishEmailCustomer::dispatch($emailPayload);

            $responses[] = ["email" => $emailPayload];
        }

        $this->info(json_encode($responses, JSON_PRETTY_PRINT));;
    }

    function prepareMessage($templates, $fields, $room_type = "Castle")
    {

        $whatsapp = null;
        $email = null;

        foreach ($templates as $key => $template) {

            $messageBody = $template->body ?? $this->defaultMessage();

            if ($template->medium == "whatsapp") {

                $whatsapp = str_replace(
                    ['[title]', '[full_name]', '[from_date]', '[to_date]', '[room_type]'],
                    [
                        $fields['title'],
                        $fields['full_name'],
                        date('d-M-Y', strtotime($fields['check_in'])),
                        date('d-M-Y', strtotime($fields['check_out'])),
                        $room_type
                    ],
                    $messageBody
                );


                $whatsapp = str_replace(
                    ['[title]', '[full_name]', '[from_date]', '[to_date]', '[room_type]'],
                    [
                        $fields['title'],
                        $fields['full_name'],
                        date('d-M-Y', strtotime($fields['check_in'])),
                        date('d-M-Y', strtotime($fields['check_out'])),
                        $room_type
                    ],
                    $messageBody
                );

                $whatsapp = preg_replace('/<p>(.*?)<\/p>/s', "$1\n", $whatsapp); // Convert <p> to new lines

                $whatsapp = strip_tags($whatsapp); // Ensure no remaining tags

            }

            if ($template->medium == "email") {

                $email = str_replace(
                    ['[title]', '[full_name]'],
                    [
                        $customer->title,
                        $customer->full_name,
                    ],
                    $messageBody
                );

                $email = preg_replace('/<p>(.*?)<\/p>/s', "$1\n", $email); // Convert <p> to new lines

                $email = strip_tags($email); // Ensure no remaining tags

            }
        }

        return ["whatsapp" => trim($whatsapp), "email" => trim($email)];
    }


    function getClient($company_id)
    {
        return "RS_1_1745417458638";
        $clientId = WhatsappClient::where("company_id", $company_id)->value("accounts")[0]["clientId"] ?? "RS_1_1745417458638";
        return $clientId;
    }

    function defaultMessage()
    {
        return "🎉 Happy Birthday, [title]. [full_name]! 🎂\n\n"
            . "Wishing you a day filled with happiness, laughter, and all the things you love the most!\n"
            . "May this year bring you success, good health, and countless joyful moments.\n\n"
            . "Enjoy your special day! 🥳\n"
            . "Regards, Mytime2Cloud";
    }
}
