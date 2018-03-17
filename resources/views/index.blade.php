<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>The Gustator v0.4 nightly</title>

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

            .bottom-input {
                background-color: #FFE4E1;
            }

            .bottom-container {
                padding: 10px 10px 10px 10px;
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

            ​#question* {
                display:none;
                border:1px solid #F00;
                width:150px;
            }​

        </style>
    </head>
    <body>
        <div class="flex-center">
            <div class="content">
                <h1>The Gustator v0.4 nightly <a href="/changelog" target="_blank">(changelog)</a></h1>
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
                   
                @if ($lastvisit_name)<h3> Czołem, {{$lastvisit_name}}! Miło, że znów tu zaglądasz!</h3>@endif 
                <p>The Gustator to narzędzie, które na podstawie Twoich preferencji smakowych postara się określić jakie piwa powinny Ci zasmakować. Odpowiedz na wszystkie poniższe pytania, aby już nigdy nie mieć problemu z wyborem piwa w sklepie</p>

                    @foreach ($questions as $index => $field)
                    <h3>{{$index}}. {{$field['question']}} 
                        @if ($field['tooltip'] != '')
                            <img src="images/info-icon-16-16.png" class="tltp" style="cursor: help !important;" data-toggle="tooltip" data-placement="right" title="{{$field['tooltip']}}"></img>
                        @endif</h3>
                    
                        @if ($field['type'] === 1)  
                         @foreach ($field['answers'] AS $ans)
                            <label class="radio-inline">
                                <input type="radio" name="answer-{{ $index }}" value="{{$ans}}" autocomplete="off" required>{{$ans}}
                            </label>
                         @endforeach
                         
                        @else
                            <label class="radio-inline"><input type="radio" name="answer-{{ $index }}" value="tak" autocomplete="off" required>tak</label>
                            <label class="radio-inline"><input type="radio" name="answer-{{ $index }}" value="nie" autocomplete="off" required>nie</label>
                        @endif

                    @endforeach
                    <div class="bottom-input">
                        <div class="bottom-container">
                            <label><input type="text" class="form-control" placeholder="Imię" name="username" maxlength="25"><em>(opcjonalne)</em></label><br />
                            <label><input type="email" class="form-control" placeholder="Adres e-mail" name="email"><em>(opcjonalne)</em></label>
                            <div class="checkbox">
                                <label><input type="checkbox" name="sendMeAnEmail" value="Tak" disabled="disabled">Chcę otrzymać dodatkowego maila ze stylami i piwami wybranymi dla mnie <em>(nieaktywne)</em></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="newsletter" value="Tak">Chcę otrzymywać <a href="http://piwolucja.pl/newsletter/" target="_blank">piwny newsletter</a></label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="debug" value="Tak">Debug</label>
                            </div>
                            <input type="submit" class="btn btn-primary btn-lg btn-block" name="send" value="Wyślij">
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
