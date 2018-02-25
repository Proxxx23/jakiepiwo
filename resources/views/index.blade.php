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
        </style>
    </head>
    <body>
        <div class="flex-center">
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
