<!doctype html>
<html lang="ru">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="{{ URL::asset('/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="https://use.fontawesome.com/releases/v5.0.2/css/all.css" rel="stylesheet">
        @yield('styles')
        <link rel="stylesheet" href="{{ URL::asset('/css/style.css') }}">
        <title>Heals</title>
    </head>

    <body>
        @include('templates.partials.navigation')
        <div class="container">
            @include('templates.partials.alerts')
            @yield('content')
        </div>
        <br>
        <footer class="footer">
            <div class="container">
                <span class="text-muted">
                    <a href="https://github.com/hetg">
                        <i class="fab fa-github-square"></i>
                    </a>
                    <a href="https://www.facebook.com/denis.popovich.372">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/denis-popovich/">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </span>
                <br>
                <span>
                    Copyright &copy; 2016-{{ date('Y') }} Heals Network<sup><i class="fas fa-trademark"></i></sup>, All Rights Reserved.
                </span>
            </div>
        </footer>

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="{{ URL::asset('/js/jquery.scrollTo-min.js') }}"></script>
        <script type="text/javascript">
            $(function (){
                setTimeout(function (){
                    $(".alert").slideToggle(400,function (){
                        $(".alert").detach();
                    });
                },2000);
            });
            $('li.open > a').on('click',function () {
                $('.notification > i').css('color','#07589c');
            });
        </script>
        @yield('scripts')
    </body>



</html>