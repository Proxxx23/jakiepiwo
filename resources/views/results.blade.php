<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Result</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center">
            <div class="content">

                <div>
                    <h1>Hej, {{$username}}! Oto style dla Ciebie!</h1>

                    @for ($i = 0; $i < count($buythis); $i++)
                        @foreach ($buythis[$i] as $k => $v)

                            <p>{{$v->name}} 
                                @if ($v->name2 != '') / {{$v->name2}} @endif 
                                @if ($v->name_pl != '') / {{$v->name_pl}} @endif</p></br>

                        @endforeach
                    @endfor

                    <h1>Tych stylów powinieneś raczej unikać</h1>

                    @for ($i = 0; $i < count($avoidthis); $i++)
                        @foreach ($avoidthis[$i] as $k => $v)

                            <p>{{$v->name}} 
                                @if ($v->name2 != '') / {{$v->name2}} @endif 
                                @if ($v->name_pl != '') / {{$v->name_pl}} @endif</p></br>

                        @endforeach
                    @endfor

                    <legend>Czy chcesz to na maila?</legend>
                    <form action="StylePickerController@sendEmail">
                        <input type="email" name="email" disabled="disabled">&nbsp;
                        <input type="submit" name="mailMe" disabled="disabled" value="Wyślij">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
