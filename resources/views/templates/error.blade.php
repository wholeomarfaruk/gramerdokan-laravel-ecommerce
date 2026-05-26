
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Error - Seldom Fashion</title>
    <style>
    body {
        font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
        font-size: 16px;
        line-height: 24px;
        color: #333;
        background-color: #fff;
    }

    .alert {
        margin: 20px auto;
        max-width: 400px;
        padding: 15px;
        border: 1px solid #c31515;
        border-radius: 5px;
    }

    .alert-heading {
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 10px;
        color: #c31515;
    }

    .list-group {
        margin-bottom: 20px;
    }

    .list-group-item {
        padding: 5px 10px;
        border-bottom: 1px solid #ddd;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }
</style>


</head>
<body>
<div class="alert alert-danger" role="alert">
    <h4 class="alert-heading">{{ __('Error') }}</h4>
    <ul class="list-group">
        @foreach ($errors as $error => $message)
            <li class="list-group-item">{{ $error }}: {{ $message }}</li>
        @endforeach
    </ul>
</div>
</body>
</html>
