<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>The Gustator v.0.2 nightly - changelog</title>

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
                <h3>Changelog v. 0.2 nightly (14.03.2018)</h3>
                <h5>Zmiany</h5>
                <ul>
                    <li>Wstępny mechanizm prewencyjny dla występowania piwa jako polecane i odradzane jednocześnie</li>
                    <li>Jeśli stylowi z 4 lub 5 pozycji brakowało niewiele do "podium", jeden lub obydwa zostaną pokazane na 4 i 5 pozycji (na szaro, mniejszym fontem)</li> 
                    <li>Wyróżnianie piwa, które w znaczący sposób odbiegło punktami reszcie stawki (pozytywnie i negatywnie)</li>
                    <li>Pierwsze przymiarki do Bootstrap Forms</li>
                    <li>Zapis odpowiedzi użytkownika do bazy na jednym ID</li>
                </ul>
                <h5>Bugfixes</h5>
                <ul>
                    <li>Kliknięcie w treść odpowiedzi zaznacza odpowiedź</li>
                </ul>

                <h5>Planowane usprawnienia (według priotytetu)</h5> 
                <ul>
                    <li>BUGFIX: Style szokujące - siła negatywna w przypadku odpowiedzi "nie"</li>
                    <li>BIGFIX: Problemy z odpowiedziami dot. goryczki</li>
                    <Li>FEATURE: Usunięcie pytania o słone piwa - gose w ramach innych odpowiedzi</li>
                    <li>FEATURE: Walidacja formularza w JS</li>
                    <li>FEATURE: Zmiana paloności na przyprawy lub usunięcie pytania</li>
                    <li>BUGIFX: Próba zmiany algorytmu na pokazywanie stylów belgijskich</li>
                    <li>FEATURE: PolskiKraft API - Słowniki + wpięcie (kontroler)</li>
                    <li>FEATURE: Możliwość wykluczania przez użytkownika całych stylów, które już zna (na początek IPA)</li>
                    <li>FEATURE: Pole (0/1) "mail" w bazie logów</li>
                    <li>FEATURE: Uzupełnienie synergii pozytywnych i negatywnych</li>
                    <li>BUGFIX: Adnotacja o BA tylko dla piw, które faktycznie leżakuje się w beczkach</li>
                    <li>FEATURE: Mail powitalny z MailChimp + e-book (final welcome e-mail)</li>
                    <li>FEATURE: Pokazywanie tylko jednej nazwy stylu + dymek z nazwą alternatywną i PL</li>
                    <li>FEATURE: Zapisywanie pól formularza w razie błędów</li>
                    <li>FEATURE: Mail z wynikiem na życzenie</li>
                    <li>FEATURE: Przełącznik w mocy piw</li>
                    <li>FEATURE: Czy danych piw możesz napić się w okolicy? (OnTap API)</li>
                    <li>FEATURE: Wyświetlanie stylów z ostatniej wizyty (???)</li>
                    <li>FEATURE: Najczęściej polecane użytkowniom style</li>
                    <li>FEATURE: Facebook Tags, SEO oraz Twitter Cards</li>
                </ul>
            </div>
    </body>
</html>
