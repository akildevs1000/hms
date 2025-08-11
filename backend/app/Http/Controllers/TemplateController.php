<?php
namespace App\Http\Controllers;

use App\Jobs\EmailSender;
use App\Jobs\WhatsappSender;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function templateTypes()
    {
        // $customer = BookedRoom::where("status_id", BookedRoom::CHECKED_IN)->with("customer")->get();

        // $checkIn  = false;
        // $checkOut  = false;

        // if ($customer->checkin !== null && $customer->checkout == null) {
        //     $checkIn = true;
        // } else if ($customer->checkin !== null && $customer->checkout !== null) {
        //     $checkOut = true;
        // }

        return [
            ["id" => 10, "name" => 'On Booking'],
            ["id" => 1, "name" => 'Inquery Create'],
            ["id" => 2, "name" => 'Quotation Create'],
            ["id" => 3, "name" => '1 Day before arrival'],
            ["id" => 4, "name" => 'On arrival date'],
            ["id" => 5, "name" => 'When customer arrived'],
            ["id" => 6, "name" => 'On checkout date checkout reminder'],
            ["id" => 7, "name" => 'After checkout'],
            ["id" => 8, "name" => 'Birthday wish'],
            ["id" => 9, "name" => 'Festival message'],
        ];
    }
    /**
     * Display a listing of the resource for dropdown.
     *
     * @return \Illuminate\Http\Response
     */
    public function dropDown()
    {
        return Template::orderByDesc("name")
            ->where([
                "company_id" => request("company_id", 0),
                "medium"     => request("medium", "email"),
            ])
            ->get();
    }

    /**
     * Display a listing of the resource with pagination.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Template::orderByDesc("id")->where([
            "company_id" => request("company_id", 0),
            "medium"     => request("medium", "email"),
        ])->paginate(100);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Template $template)
    {
        $data = $request->validate($template::validateFields);

        return Template::create($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Template $template)
    {
        $data = $request->validate($template::validateFields);

        $template->update($data);

        return $template;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function destroy(Template $template)
    {
        $template->delete();

        return response()->noContent();
    }

    public function sendMessage(Request $request, Template $template)
    {
        $data = $request->validate($template::validateFields);

        $fields = [
            "title"          => "Mr",
            "full_name"      => "Test User",
            "from_date"      => date('d-M-y H:i'),
            "to_date"        => date('d-M-y H:i'),
            "room_type"      => "Castle",
            "room_no"        => rand(200, 210),
            "reservation_no" => rand(100, 9999),
            "company_id"     => $data["company_id"],
        ];

        if (! $request->recipient) {
            return false;
        }

        $payload = [
            'recipient'  => $request->recipient,
            'text'       => (new Controller)->trimMessage($fields, $data["action_id"], $data["body"]),
            "company_id" => $data["company_id"],
            "mediaUrl" => "https://backend.myhotel2cloud.com/vouchers/voucher_3_427.pdf",
        ];

        if ($data["medium"] == "whatsapp") {

            WhatsappSender::dispatch($payload);
        }

        if ($data["medium"] == "email") {

            EmailSender::dispatch($payload);
        }

        info($payload);

        return true;
    }
}
