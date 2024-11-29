<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SendMessageApiTest extends TestCase
{
    /**
     * Test the send message API for status only.
     *
     * @return void
     */
    public function test_send_message_api_status_only()
    {
        // Mock request payload
        $payload = [
            "number" => "971554501483",
            "type" => "text",
            "message" => "Helsdfsdflo",
            "instance_id" => "674973D1CE41D",
            "access_token" => "67496f1b26e95"
        ];

        // Fake the HTTP response
        Http::fake([
            'https://demo.betablaster.in/api/send' => Http::response(['status' => 'success'], 200),
        ]);

        // Make an HTTP POST request to the API
        $response = Http::post('https://demo.betablaster.in/api/send', $payload);

        // Assert the request was sent
        Http::assertSent(function ($request) use ($payload) {
            return $request->url() === 'https://demo.betablaster.in/api/send' &&
                $request->data() == $payload;
        });

        // Assert the response
        $this->assertEquals(200, $response->status());
        $this->assertEquals("success", $response['status']);
    }
}
