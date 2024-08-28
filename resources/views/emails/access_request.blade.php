<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Request</title>
</head>
<body>
    <h3>Access Request DigiMAMS (Digital Maintenance & Asset Management System) </h3>
    <p>A new access request has been submitted with the following details:</p>
    <ul>
        <li><strong>Application:</strong>DigiMAMS (Digital Maintenance & Asset Management System) </li>
        <li><strong>Name:</strong> {{ $request['name'] }}</li>
        <li><strong>Email:</strong> {{ $request['email'] }}</li>
        <li><strong>Purpose:</strong> {{ $request['purpose'] }}</li>
    </ul>
</body>
</html>
