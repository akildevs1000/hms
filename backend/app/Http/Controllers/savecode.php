<?php
//  2-21--2023
function storeBooking($request)
{
    try {
        return DB::transaction(function () use ($request) {
            $data = [];
            $data = $request->only(Booking::bookingAttributes());
            $data['booking_date'] = now();
            $data['payment_status'] = $request->all_room_Total_amount == $request->remaining_price ? '0' : '1';
            $data['remaining_price'] = (float) $request->total_price - (float) $request->advance_price;
            $data['grand_remaining_price'] = (int) $request->total_price - (float) $request->advance_price;
            $data['reservation_no'] = $this->getReservationNumber($data);

            $booked = Booking::create($data);

            if ($booked) {

                Food::create([
                    'booking_id' => $booked->id,
                    'breakfast' => $request->json('qty_breakfast'),
                    'lunch' => $request->json('qty_lunch'),
                    'dinner' => $request->json('qty_dinner'),
                ]);

                $transactionData = [
                    'booking_id' => $booked->id,
                    'customer_id' => $booked->customer_id ?? '',
                    'date' => now(),
                    'company_id' => $request->company_id ?? '',
                    'payment_method_id' => $booked->payment_mode_id,
                    'desc' => 'rooms booking amount',
                    'reference_number' => $request->reference_number,
                ];

                //Transaction
                $payment = new TransactionController();
                $payment->store($transactionData, $request->total_price, 'debit');

                if ($request->advance_price && $request->advance_price > 0) {
                    $transactionData['desc'] = 'advance payment';
                    $payment->store($transactionData, $request->advance_price, 'credit');
                }
                //End Transaction
                if ((float) $booked->advance_price == 0) {

                    if (($booked->paid_by && $booked->paid_by == 2) || ($booked->type != 'Walking' && $booked->type != 'Complimentary')) {

                        $agentsData = [
                            'booking_id' => $booked->id,
                            'customer_id' => $booked->customer_id ?? '',
                            'type' => $booked->type ?? '',
                            'source' => $booked->source,
                            'reference_no' => $booked->reference_no ?? '',
                            'amount' => $booked->total_price ?? '',
                            'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                            'company_id' => $request->company_id ?? '',
                            'is_paid' => $booked->paid_by == 1 ? 2 : 0,
                        ];
                        $payment = new AgentsController();
                        $payment->store($agentsData);

                        $paymentsData = [
                            'booking_id' => $booked->id,
                            'payment_mode' => 7,
                            'description' => 'booked from ' . $booked->source,
                            'amount' => $booked->remaining_price,
                            'type' => 'room',
                            'room' => $booked->rooms,
                            'company_id' => $request->company_id,
                            'is_city_ledger' => 1,
                        ];
                        $payment = new PaymentController();
                        $payment->store($paymentsData);
                    } else {
                        $agentsData = [
                            'booking_id' => $booked->id,
                            'customer_id' => $booked->customer_id ?? '',
                            'type' => 'Customer' ?? '',
                            'source' => $booked->source,
                            'reference_no' => $booked->reference_no ?? '',
                            'amount' => $booked->total_price ?? '',
                            'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                            'company_id' => $request->company_id ?? '',
                        ];
                        $payment = new AgentsController();
                        $payment->store($agentsData);

                        $paymentsData = [
                            'booking_id' => $booked->id,
                            'payment_mode' => 7,
                            'description' => 'booked from ' . $booked->source,
                            'amount' => $booked->remaining_price,
                            'type' => 'room',
                            'room' => $booked->rooms,
                            'company_id' => $request->company_id,
                            'is_city_ledger' => 1,
                        ];
                        $payment = new PaymentController();
                        $payment->store($paymentsData);
                    }
                } else {

                    if ($request->total_price >= $request->advance_price) {

                        $paymentsData = [
                            'booking_id' => $booked->id,
                            'payment_mode' => $booked->payment_mode_id,
                            'description' => 'advance payment',
                            'amount' => $booked->advance_price,
                            'type' => 'room',
                            'room' => $booked->rooms,
                            'company_id' => $request->company_id,
                            'is_city_ledger' => 0,
                        ];
                        $payment = new PaymentController();
                        $payment->store($paymentsData);
                    }

                    $paymentsData = [
                        'booking_id' => $booked->id,
                        'payment_mode' => 7,
                        'description' => 'pending payment',
                        'amount' => $booked->remaining_price,
                        'type' => 'room',
                        'room' => $booked->rooms,
                        'company_id' => $request->company_id,
                        'is_city_ledger' => 1,
                    ];
                    $payment = new PaymentController();
                    $payment->store($paymentsData);

                    $agentsData = [
                        'booking_id' => $booked->id,
                        'customer_id' => $booked->customer_id ?? '',
                        'type' => 'Customer' ?? '',
                        'source' => $booked->source,
                        'reference_no' => $booked->reference_no ?? '',
                        'amount' => $booked->total_price ?? '',
                        'agent_paid_amount' => $booked->advance_price ?? '',
                        'booking_date' => date('Y-m-d', strtotime($booked->created_at)) ?? '',
                        'company_id' => $request->company_id ?? '',
                    ];
                    $payment = new AgentsController();
                    $payment->store($agentsData);
                }

                if ($request->gst_number) {
                    (new TaxableController)->storeTaxableInvoice($booked);
                }
            }

            return $booked;

            return $this->response('Room Booked Successfully.', $booked, true);
        });
    } catch (\Throwable $th) {
        return $th;
        Logger::channel("custom")->error("BookingController: " . $th);
        return ["done" => false, "data" => "DataBase Error booking"];
    }
}