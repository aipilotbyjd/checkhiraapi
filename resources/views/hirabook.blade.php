<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote of the Day</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }

        .quote-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            text-align: center;
        }

        .quote {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .author {
            font-style: italic;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="quote-container">
        <div class="quote">
            "{{ $quote['quote'] }}"
        </div>
        <div class="author">
            - {{ $quote['author'] }}
        </div>
    </div>
</body>

</html>
