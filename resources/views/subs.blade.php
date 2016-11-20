<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="120; URL={{ URL::current() }}">
    <title>{{ number_format($subs, 0 , ',' , '.' ) }}</title>
</head>
<body>
    <h1>Subs now: <strong style="color: red;">{{ number_format($subs, 0 , ',' , '.' ) }}</strong></h1>
</body>
</html>