<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $quotes = [
        [
            'id' => 1,
            'quote' => 'The only way to do great work is to love what you do.',
            'author' => 'Steve Jobs'
        ],
        [
            'id' => 2,
            'quote' => 'Innovation distinguishes between a leader and a follower.',
            'author' => 'Steve Jobs'
        ],
        [
            'id' => 3,
            'quote' => 'Stay hungry, stay foolish.',
            'author' => 'Steve Jobs'
        ],
        [
            'id' => 4,
            'quote' => 'The future belongs to those who believe in the beauty of their dreams.',
            'author' => 'Eleanor Roosevelt'
        ],
    ];

    // Get a random quote
    $randomQuote = $quotes[array_rand($quotes)];

    return view('hirabook', ['quote' => $randomQuote]);
});


// Route::get('/generate', function () {
//     // Create personal access client
//     $output = [];
//     Artisan::call('passport:client', [
//         '--personal' => true,
//         '--no-interaction' => true
//     ]);
//     $clientOutput = Artisan::output();

//     // Generate encryption keys if they don't exist
//     Artisan::call('passport:keys', [
//         '--force' => true
//     ]);
//     $keysOutput = Artisan::output();

//     \Log::info('Passport installation output: ' . $clientOutput);

//     return [
//         'message' => 'Passport keys and client generated successfully',
//         'keys_output' => trim($keysOutput),
//         'client_output' => trim($clientOutput)
//     ];

// });
