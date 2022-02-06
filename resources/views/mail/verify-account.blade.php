<!DOCTYPE html>
<html>
<head>
    <title>{{env('APP_NAME')}}</title>
</head>
<body>
    <h1>Hello {{$user->firstname." ". $user->lastname}},</h1>
    <p>You have registerd in {{ env('APP_NAME') }} with username: <b>{{$user->username}}</b>!</p>
    <p>Thank you for register your self in {{ env('APP_NAME') }}! </p>
    <p>Please use {{$user->otp}} to verify your account! </p>
    <p><a href="{{route('welcome')}}">Click Here to visit our site!</a> </p>
   
    <p>Thank you</p>
</body>
</html>