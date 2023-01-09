<?php

return [

    'whatsapp-bot-api' => [
        'whatsappSessionFieldName' => env('WHATSAPP_API_SESSION_FIELD_NAME', ''),
        'whatsappSession' => env('WHATSAPP_API_SESSION', ''),
        'base_uri' => env('WHATSAPP_API_BASE_URL', ''),
        'mapMethods' => [
            'sendMessage' => 'sendText',
            'sendDocument' => 'sendFile',
        ]
    ],
];


// # config/whatsapp-notification-channel/services.php

// 'whatsapp-bot-api' => [
//     'whatsappSessionFieldName' => env('WHATSAPP_API_SESSION_FIELD_NAME', ''), //Session field name
//     'whatsappSession' => env('WHATSAPP_API_SESSION', ''), // session value
//     'base_uri' => env('WHATSAPP_API_BASE_URL', ''), //  Your venom base url api
//     'mapMethods' => [
//         'sendMessage' => 'sendText',
//         'sendDocument' => 'sendFile',
//     ], /* If you want to change the methods that will be called in the api, you can map them here, example: sendMessage will be replaced by the sendText method of the api */
// ],