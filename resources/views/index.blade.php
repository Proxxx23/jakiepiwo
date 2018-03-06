<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Jakie piwo mam kupić?</title>

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

            .changelog {
                position: absolute;
                right: 25px;
                top: 18px;
            }

            .changelog h3 {
                font-size: 14px;
                color: lime;
            }

            .changelog ul li {
                font-size: 14px;
                color: black;
            }

            .todo {
                position: absolute;
                right: 25px;
                top: 180px;
            }

            .todo h3 {
                font-size: 14px;
                color: lime;
            }

            .todo ul li {
                font-size: 14px;
                color: black;
            }

            }
        </style>
    </head>
    <body>
        <div class="flex-center">
            <div class="changelog">
                <h3>Changelog v. 0.1nightly</h3>
                <ul>
                    <li>Pełny algorytm z budowaniem "siły"</li>
                    <li>Logowanie błędów formularza do bazy</li>
                    <li>Obsługa błędów</li>
                    <li>Wstępna wersja bazująca na 10 stylach</li>
                    <li>Walidacja formularza</li>
                    <li>Zapis odpowiedzi do bazy</li>
                </ul>
            </div>

            <div class="todo">
                <h3>Pracuję nad</h3>
                <ul>
                    <li>Wykluczanie znanych użytkownikowi stylów</li>
                    <li>Niektóre pytania jako skala/suwak</li>
                    <li>Słone/kwaśne/Islay jako propozycja obok stylów</li>
                    <li>Barrel aged jako osobna część</li>
                    <li>Mail na życzenie</li>
                    <li>Wyświetlanie stylów z ostatniej wizyty</li>
                    <li>Najczęściej polecane style</li>
                    <li>Pula 10 polecanych piw na każdy styl</li>
                    <li>Logowanie błędów w jednym insercie DB</li>
                    <li>SKalowanie pytań (ważne > mniej ważne)</li>
                </ul>
            </div>
            <div class="content">
                <!-- Error handling  -->
                
                @if ($errors_count > 0)
                <h3>Błędy formularza</h3>
                    @foreach ($errors AS $error)
                        <p>{{$error}}</p><br/>
                    @endforeach
                @endif

                <div>
                    <form method="POST" action=" {{ action('StylePickerController@mix') }} ">
                    {{ csrf_field() }}

                    Username <input type="text" name="username"><br />
                    E-mail <input type="email" name="email"><br />
                    Newsletter <input type="checkbox" name="newsletter" value="Tak"><br />

                    @foreach ($questions as $index => $field)

                    <h3>{{$index}} {{$field['question']}}</h3>
                    
                        @if ($field['type'] === 1)  
                         @foreach ($field['answers'] AS $ans)
                           {{$ans}}<input type="radio" name="answer-{{ $index }}" value={{$ans}}>&nbsp;
                         @endforeach
                         
                        @else

                        TAK<input type="radio" name="answer-{{ $index }}" value="tak">&nbsp;
                        NIE<input type="radio" name="answer-{{ $index }}" value="nie">&nbsp;
                        @endif

                    @endforeach

                    <br /><input type="submit" name="send" value="Wyslij">
                    <br />Wyślij mi maila<input type="checkbox" name="sendMeAnEmail">

                    <h1>Mam do Ciebie dokładniejsze pytania</h1>
                        @foreach ($accurate_questions AS $index => $field)
                            <h3>{{ $field['question'] }} {{ $index+1 }}</h3>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
