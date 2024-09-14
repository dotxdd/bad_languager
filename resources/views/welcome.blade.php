<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        /* Basic TailwindCSS reset and custom styles */
        body {
            font-family: 'Figtree', sans-serif;
            background-color: white;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #F0F8FF;
        }

        .container {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            align-items: center;
        }

        h1 {
            font-family: 'Press Start 2P', cursive;
            color: #00008B;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .btn {
            display: block;
            padding: 1rem;
            background-color: #00008B;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            width: 100%;
            max-width: 200px;
            text-align: center;
        }

        .btn:hover {
            background-color: #1E90FF;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>bad_languager</h1>
    <a href="/register" class="btn">Utworz konto</a>
    <a href="/login" class="btn">Zaloguj sie</a>
</div>
</body>
</html>
