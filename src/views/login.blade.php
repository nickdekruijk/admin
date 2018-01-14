<!DOCTYPE html>
<html lang="en">
    <head>
        <title>LaraPages Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <style>
            body {font-family:'Open Sans',sans-serif;font-size:15px;-webkit-font-smoothing:antialiased;-moz-font-smoothing:grayscale;-moz-osx-font-smoothing:grayscale;background-color:#2c3340;color:#fff;margin:0;font-weight:300}
            label {display:block}
            section {display:block;width:300px;height:240px;position:absolute;top:50%;left:50%;margin:-120px 0 0 -150px;background-color:#178}
            header {background-color:#0cb}
            .logo {font-size:30px;display:block;float:left;margin-right:10px}
            h2 {font-weight:300;margin:0;padding:5px 10px}
            form {padding:10px}
            input {font:inherit;background:none;display:block;width:100%;box-sizing:border-box;outline:none;margin:5px 0 10px;padding:5px 10px;border:1px solid rgba(255,255,255,0.25);color:#fff;border-radius:4px}
            input:focus {background-color:rgba(255,255,255,0.25)}
            button {display:inline-block;border:1px solid rgba(255,255,255,0.25);color:#fff;font:inherit;padding:4px 10px;border-radius:4px;cursor:pointer;margin:10px 0 0;background-color:transparent}
            button:hover {background-color:#0cb}
            button i {margin-right:5px}
            section.error {animation:shake 0.5s cubic-bezier(.36,.07,.19,.97) both;transform:translate3d(0, 0, 0);backface-visibility:hidden;perspective:1000px}
            @keyframes shake { 
                10%, 90% {transform:translate3d(-1px, 0, 0)}
                20%, 80% {transform:translate3d(2px, 0, 0)}
                30%, 50%, 70% {transform:translate3d(-4px, 0, 0)}
                40%, 60% {transform:translate3d(4px, 0, 0)}
            }
        </style>
    </head>
    <body>
        <section {{ $errors->count()?'class=error':'' }}>
            <header>
                <h2>{!! config('larapages.logo') !!}</h2>
            </header>
            <form class="login" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <label for="email">{{ trans('larapages::base.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                <label for="password">{{ trans('larapages::base.password') }}</label>
                <input id="password" type="password" name="password" required>
                <button type="submit" class="button"><i class="fa fa-sign-in" aria-hidden="true"></i>{{ trans('larapages::base.login') }}</button>
            </form>
        </section>
    </body>
</html>