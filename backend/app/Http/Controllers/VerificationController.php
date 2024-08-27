<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Verification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function getVerifyInfo($id = 0)
    {
        return Verification::where("company_id", $id)->first();
    }

    public function verifyBooking(Request $request, $booking_id)
    {
        $customerId = Booking::where("id", $booking_id)->value("customer_id") ?? 0;

        $verification = Verification::where("company_id", $request->company_id)->first();

        $payload = [
            "captured_photo" => $verification->captured_photo,
            "id_frontend_side" => $verification->id_frontend_side,
            "id_backend_side" => $verification->id_backend_side,
            "sign" => $verification->sign,
        ];

        $updated = Customer::where("id", $customerId)->update($payload);

        if(!$updated) {
            return 0;
        }

        $verification->delete();

        return 1;

    }

    public function verifyCustomer()
    {
        try {
            $customer = [];

            if (request('captured_photo')) {
                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('captured_photo')));
                $imageName = "captured_photo-" . time() . ".png";
                $publicDirectory = public_path("captured_photo");
                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory);
                }
                file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                $customer["captured_photo"] = $imageName;
            }

            if (request('id_frontend_side')) {
                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('id_frontend_side')));
                $imageName = "id_frontend_side-" . time() . ".png";
                $publicDirectory = public_path("id_frontend_side");
                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory);
                }
                file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                $customer["id_frontend_side"] = $imageName;
            }

            if (request('id_backend_side')) {
                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('id_backend_side')));
                $imageName = "id_backend_side-" . time() . ".png";
                $publicDirectory = public_path("id_backend_side");
                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory);
                }
                file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                $customer["id_backend_side"] = $imageName;
            }

            if (request('sign')) {
                $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('sign')));
                $imageName = "sign-" . time() . ".png";
                $publicDirectory = public_path("sign");
                if (!file_exists($publicDirectory)) {
                    mkdir($publicDirectory);
                }
                file_put_contents($publicDirectory . '/' . $imageName, $base64Image);

                $customer["sign"] = $imageName;
            }

            $customer["company_id"] = request('company_id');

            return Verification::create($customer);
        } catch (\Exception $e) {
            return $this->getMessage();
        }
    }
}