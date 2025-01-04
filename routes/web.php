<?php

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
