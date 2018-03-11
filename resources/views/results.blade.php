<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Style piwne dla Ciebie - deGUSTATOR v.0.1 nightly</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Mina" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Mina', sans-serif;
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
                    <h1>@if ($username != '')Hej, {{$username}}!@endif Piwa w tych stylach powinny Ci zasmakować</h1>

                    @for ($i = 0; $i < count($buythis); $i++)
                        @foreach ($buythis[$i] as $k => $v)

                            <p>{{$v->name}} 
                                @if ($v->name2 != '') / {{$v->name2}} @endif 
                                @if ($v->name_pl != '') / {{$v->name_pl}} @endif</p></br>

                        @endforeach
                    @endfor

                    <!-- TODO: tylko pod piwa rzeczywiście starzone w BA -->
                    @if ($barrel_aged === true)
                        <p>Ponieważ lubisz alkohole szlachetne, powinny zainteresować Cię piwa leżakowane w beczkach po trunkach takich jak whisky czy bourbon. Szukaj w sklepie piw z dopiskiem "barrel-aged" lub "BA" na etykiecie.</p>
                    @endif

                    <h1>Piw w tych stylach powinieneś raczej unikać</h1>

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
                    Odbierz za darmo piwnego e-booka <a href="http://piwolucja.pl/newsletter/" target="_blank">"15 pytań o piwo wraz z konkretnymi odpowiedziami"</a>
                </div>
            </div>
        </div>
    </body>
</html>
