<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>The Gustator v0.7 nightly - changelog</title>

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

            .changelog {
                position: absolute;
                right: 25px;
                top: 18px;
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
    <body><div class="changelog">
                <h3>Changelog v0.7 nightly (20.02.2019 r.)</h3>
                <h5>Najnowsze zmiany i funkcje</h5>
                <ul>
                    <li>Przepisanie całego kodu na PHP 7.3 + ReactJs (w trakcie)</li>
                </ul>
                <h5>Bugfixes</h5>
                <ul>
                    <li>-</li>
                </ul>

                <h5>Planowane usprawnienia (według priotytetu)</h5> 
                <ul>
                    <li>FEATURE: Zapisywanie pól formularza w razie błędów i walidacja jQuery (v 0.1 beta)</li>
                    <li>FEATURE: Ile razy The Gustator pomógł odwiedzającym? (v 0.1 beta)</li>
                    <li>FEATURE: Facebook Tags, SEO oraz Twitter Cards (v0.1 beta)</li>
                    <li>FEATURE: Rotacyjne artykuły o piwie pod wynikami (v0.1 beta)</li>
                    <li>FEATURE: Mail z wynikiem na życzenie (v0.1 beta)</li>
                    <li>FEATURE: Pole (0/1) "mail" w bazie logów (v0.1 beta)</li>
                    <li>FEATURE: Dalsze prace z PK API</li>
                    <li>FEATURE: Ratebeer API dla stylów, których nie ma w PK</li>
                    <li>FEATURE: Pokazywanie tylko jednej nazwy stylu + dymek z nazwą alternatywną i PL</li>
                    <li>FEATURE: Czy danych piw możesz napić się w okolicy? (OnTap API)</li>
                    <li>FEATURE: Krótkie opisy stylów</li>
                </ul>
            </div>
    </body>
</html>
