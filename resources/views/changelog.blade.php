<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>The Gustator v0.3 nightly - changelog</title>

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
                <h3>Changelog v0.3 nightly (15.03.2018 r.)</h3>
                <h5>Najnowsze zmiany i funkcje</h5>
                <ul>
                    <li>Walidacja formularza po stronie HTML-a</li>
                    <li>Między stylem proponowanym a odradzanym musi być 125% marginesu w punktacji. W innym wypadku styl odradzany nie zostanie odradzony</li>
                    <li>Osoba dopisująca się do newslettera otrzymuje mail potwierdzający oraz e-booka</li>
                </ul>
                <h5>Bugfixes</h5>
                <ul>
                    <li>Wzmocnienie znaczenia pytania o piwa ciemne/jasne</li>
                    <li>Poprawki dla pytania o kolor piwa (większy nacisk)</li>
                    <li>Style szokujące - siła negatywna w przypadku odpowiedzi "nie" nie będzie budowana</li>
                    <li>Poprawka budowania siły (pusta tablica tworzona w przypadku odpowiedzi "bez znaczenia")</li>
                    <li>Poprawka wpływu pytania o goryczkę (wyrównanie punktacji)</li>
                    <li>W niektórych pytaniach, w przypadku wybrania odpowiedzi "tak", algorytm nie buduje siły negatywnej dla piw przypisanych do niezaznaczonej odpowiedzi</li>
                    <li>Odpowiedź "bez znaczenia" nie buduje siły negatywnej z niezaznaczonych odpowiedzi</li>
                    <li>Pytanie o paloność zostało zastąpione pytaniem o przyprawowość</li>
                    <li>Połączenie pytania o gęstość i moc w jedno (zwiększenie znaczenia)</li>
                    <li>Usunięcie pytania o piwo słone</li>
                    <li>Style palone zawierają się w pytaniu o czekoladowość (jeśli są też palone)</li>
                    <li>Style belgisjkie pokazują się teraz częściej</li>
                    <li>Kalibracja kolorków (bardzo polecany/odradzany styl)</li>
                </ul>

                <h5>Planowane usprawnienia (według priotytetu)</h5> 
                <ul>
                    <li>FEATURE: Jeśli style z pierwszych pozycji mają nieznaczne różnice punktowe, pokazuj je losowo</li>
                    <li>FEATURE: Zmiana układu pytań (od ogółu do szczegółu)</li>
                    <li>FEATURE: Ograniczenie listy stylów do tych, które występują często w piwach z Polski (usunięcie m.in. english porteru)</li>   
                    <li>FEATURE: Inny układ pytań o smaki</li>
                    <li>FEATURE: PolskiKraft API - Słowniki + wpięcie (kontroler)</li>
                    <li>FEATURE: Możliwość wykluczania przez użytkownika całych stylów, które już zna (na początek IPA)</li>
                    <li>FEATURE: Pole (0/1) "mail" w bazie logów</li>
                    <li>FEATURE: Uzupełnienie synergii pozytywnych i negatywnych</li>
                    <li>BUGFIX: Adnotacja o BA tylko dla piw, które faktycznie leżakuje się w beczkach</li>
                    <li>FEATURE: Pokazywanie tylko jednej nazwy stylu + dymek z nazwą alternatywną i PL</li>
                    <li>FEATURE: Zapisywanie pól formularza w razie błędów</li>
                    <li>FEATURE: Mail z wynikiem na życzenie</li>
                    <li>FEATURE: Przełącznik w mocy piw</li>
                    <li>FEATURE: Czy danych piw możesz napić się w okolicy? (OnTap API)</li>
                    <li>FEATURE: Najczęściej polecane użytkowniom style</li>
                    <li>FEATURE: Facebook Tags, SEO oraz Twitter Cards</li>
                </ul>
            </div>
    </body>
</html>
