<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job done!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fafafa;
            color: #333;
            line-height: 1.5;
        }
        h1 {
            color: #2d89ef;
        }
        ul {
            background: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        li {
            margin: 6px 0;
        }
    </style>
</head>
<body>
<h1>Hi, {{ $name }}!</h1>

<p>Your collaborator CSV import job is done! 🎉</p>

@if (! empty($collaboratorsNotImported))
    <p>Unfortunately, some records couldn’t be imported. Check the list below:</p>
    <ul>
        @foreach ($collaboratorsNotImported as $index => $data)
            <li>
                <strong>{{ $data['name'] }}</strong> — {{ json_encode($data['reasons']) }}
            </li>
        @endforeach
    </ul>
@endif

<p>Thanks for using <strong>Collaborators API</strong>.</p>
<p>By <strong>Convenia ❤️</strong></p>
</body>
</html>
