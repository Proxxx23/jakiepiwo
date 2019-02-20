<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Style piwne dla Ciebie - The Gustator v0.6 nightly</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Mina" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script>
            $(document).ready(function(){
                $('[data-toggle="tooltip"]').tooltip()
            });
        </script>
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

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .content {
                text-align: center;
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

            #optional_take, #optional_avoid {
                font-size: 18px;
                color: grey;
            }

            #take, #avoid {
                font-size: 22px;
                font-weight: bold;
            }

        </style>
    </head>
    <body>
        <div class="flex-center">
            <div class="content">

                <div>
                    @if ($_POST['debug'])
                    <p>DEBUG - Twoje odpowiedzi: <br />
                        @foreach ($answers AS $number => $value)
                        {{$number}} : {{$value}} |
                        @endforeach
                    </p>
                    @endif

                    <h1>@if ($username != '')Hej, {{$username}}!@endif Piwa w tych stylach powinny Ci zasmakować</h1>

                    @php
                        $countedBuyThis = count($buyThis);
                    @endphp

                    @for ($i = 0; $i < $countedBuyThis; $i++)
                        @foreach ($buyThis[$i] as $k => $v)

                            @if ($i < 3)
                            <p @if ($mustTake && $i == 0) id="mustTake" @else id="take" @endif>
                                {{$v->name}} 
                                @if ($v->name2 != '') / {{$v->name2}} @endif 
                                @if ($v->name_pl != '') / {{$v->name_pl}} @endif</p>

                                <p>
                                    @for ($u = 0; $u < 5; $u++)
                                        @if ($PKStyleTake[$i] != '')
                                            @if ($u == 0)<h5>Polecane piwa w tym stylu</h5>@endif
                                                @if ($PKStyleTake[$i][$u] != '')
                                                    <a href="{{$PKStyleTake[$i][$u]->web_url}}" target="_blank" title="Zobacz {{$PKStyleTake[$i][$u]->title}} na PolskiKraft.pl">{{$PKStyleTake[$i][$u]->title}}</a> z {{$PKStyleTake[$i][$u]->subtitle}} <img src="images/info-icon-16-16.png" class="tltp" style="cursor: help !important;" data-html="true" data-toggle="tooltip" data-placement="right" title="<img src='{{$PKStyleTake[$i][$u]->photo_thumbnail_url}}'' />"> <br />
                                                @endif
                                        @endif
                                    @endfor
                                </p>

                            @else
                            <p id="optional_take">{{$v->name}} 
                                @if ($v->name2 != '') / {{$v->name2}} @endif 
                                @if ($v->name_pl != '') / {{$v->name_pl}} @endif</p>

                    @for ($u = 0; $u <= 4; $u++)
                        @if ($PKStyleTake[$i][$u] != '')
                            @if ($u == 0)<h5>Polecane piwa w tym stylu</h5>@endif
                                <a href="{{$PKStyleTake[$i][$u]->web_url}}" target="_blank" title="Zobacz {{$PKStyleTake[$i][$u]->title}} na PolskiKraft.pl">{{$PKStyleTake[$i][$u]->title}}</a> z {{$PKStyleTake[$i][$u]->subtitle}} <img src="images/info-icon-16-16.png" class="tltp" style="cursor: help !important;" data-html="true" data-toggle="tooltip" data-placement="right" title="<img src='{{$PKStyleTake[$i][$u]->photo_thumbnail_url}}'' />"><br />
                            @endif
                        @endfor
                        <br />

                            @endif

                        @endforeach
                    @endfor

                    <!-- TODO: tylko pod piwa rzeczywiście starzone w BA -->
                    @if ($barrelAged === true)
                        <br /><br /><p>Ponieważ lubisz alkohole szlachetne, powinny zainteresować Cię piwa leżakowane w beczkach po trunkach takich jak whisky czy bourbon. Szukaj w sklepie piw z dopiskiem "barrel-aged" lub "BA" na etykiecie.</p>
                    @endif

                    <h1>Piwa w tych stylach raczej nie przypadną Ci do gustu</h1>

                    @php
                    $countedAvoidThis = count($avoidThis);
                    @endphp

                    @for ($i = 0; $i < $countedAvoidThis; $i++)
                        @foreach ($avoidThis[$i] as $k => $v)

                            @if ($i < 3)
                            <p @if ($mustAvoid && $i == 0) id="mustAvoid" @else id="avoid" @endif>{{$v->name}} 
                                @if ($v->name2 != '') / {{$v->name2}} @endif 
                                @if ($v->name_pl != '') / {{$v->name_pl}} @endif</p>
                            @else
                            <p id="optional_avoid">{{$v->name}} 
                                @if ($v->name2 != '') / {{$v->name2}} @endif 
                                @if ($v->name_pl != '') / {{$v->name_pl}} @endif</p>
                            @endif

                        @endforeach
                    @endfor<br /><br />

                    <legend>Czy chcesz to na maila? (nieaktywne)</legend>
                    <form action="MailController@sendEmail">
                        <input type="email" name="email" disabled="disabled">&nbsp;
                        <input type="submit" name="mailMe" disabled="disabled" value="Wyślij">
                    </form>
                    Odbierz za darmo piwnego e-booka <a href="http://piwolucja.pl/newsletter/" target="_blank">"15 pytań o piwo wraz z konkretnymi odpowiedziami"</a>
                </div>
            </div>
        </div>
    </body>
</html>
