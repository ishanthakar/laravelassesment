<!DOCTYPE html>
<html>
<head>
    <title>{{env('APP_NAME')}}</title>
</head>
<body>
    <h1>Hello,</h1>
    <p>This is an official invite from {{ env('APP_NAME') }} to get registered! </p>
    <p>Click the following link to get registered! </p>
    <p><a href="{{route('welcome')}}">Click Here</a> </p>
   
    <p>Thank you</p>
</body>
</html>