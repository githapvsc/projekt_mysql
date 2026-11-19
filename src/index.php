<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baza danych</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background-color: #f4f6f9;
            color: #1f2937;
            margin: 0;
            padding: 32px 16px;
        }

        .kontener {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        h1 {
            margin: 0 0 20px 0;
            font-size: 1.5rem;
            color: #111827;
        }

        .przyciski {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
        }

        button {
            padding: 10px 18px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #1f2937;
            background-color: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        button:hover {
            background-color: #e0e7ff;
            border-color: #a5b4fc;
            transform: translateY(-1px);
        }

        button.aktywny {
            background-color: #4f46e5;
            color: #ffffff;
            border-color: #4f46e5;
        }

        button.aktywny:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .tabela-wrapper {
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.92rem;
        }

        thead th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: 600;
            padding: 12px 14px;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap;
        }

        tbody td {
            padding: 11px 14px;
            border-bottom: 1px solid #f3f4f6;
            color: #1f2937;
        }

        tbody tr:nth-child(even) {
            background-color: #fcfcfd;
        }

        tbody tr:hover {
            background-color: #f3f4f6;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .komunikat {
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .komunikat-info {
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .komunikat-blad {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>

<div class="kontener">
    <h1>Baza danych uczniów</h1>

    <?php
    $akcja = $_POST['akcja'] ?? '';
    ?>

    <!-- Przyciski na górze -->
    <form method="post" class="przyciski">
        <button type="submit" name="akcja" value="wszyscy" class="<?= $akcja === 'wszyscy' ? 'aktywny' : '' ?>">
            Wszyscy użytkownicy
        </button>
        <button type="submit" name="akcja" value="miejscowosci" class="<?= $akcja === 'miejscowosci' ? 'aktywny' : '' ?>">
            Miejscowości
        </button>
        <button type="submit" name="akcja" value="hobby" class="<?= $akcja === 'hobby' ? 'aktywny' : '' ?>">
            Liczba osób według hobby
        </button>
    </form>

    <?php
    $conn = new mysqli(
        getenv("DB_HOST"),
        getenv("DB_USER"),
        getenv("DB_PASSWORD"),
        getenv("DB_NAME")
    );

    if ($conn->connect_error) {
        echo '<div class="komunikat komunikat-blad">Błąd połączenia: ' . htmlspecialchars($conn->connect_error) . '</div>';
    } else {
        $conn->set_charset("utf8mb4");
        $sql = "";
        $naglowki = [];

        switch ($akcja) {
            case "wszyscy":
                $sql = "SELECT * FROM uczniowie";
                $naglowki = [
                    "ID", "Imię", "Nazwisko", "Data urodzenia",
                    "Miejscowość", "Kod pocztowy", "Email", "Telefon", "Hobby"
                ];
                break;

            case "miejscowosci":
                $sql = "SELECT DISTINCT miejscowosc FROM uczniowie ORDER BY miejscowosc";
                $naglowki = ["Miejscowość"];
                break;

            case "hobby":
                $sql = "SELECT hobby, COUNT(*) AS liczba FROM uczniowie GROUP BY hobby ORDER BY liczba DESC";
                $naglowki = ["Hobby", "Liczba osób"];
                break;
        }

        if ($sql !== "") {
            $wynik = $conn->query($sql);

            if ($wynik && $wynik->num_rows > 0) {
                echo '<div class="tabela-wrapper">';
                echo '<table>';
                echo '<thead><tr>';
                foreach ($naglowki as $naglowek) {
                    echo '<th>' . htmlspecialchars($naglowek) . '</th>';
                }
                echo '</tr></thead>';
                echo '<tbody>';

                while ($wiersz = $wynik->fetch_assoc()) {
                    echo '<tr>';
                    foreach ($wiersz as $wartosc) {
                        $tekst = ($wartosc === null || $wartosc === '') ? '—' : htmlspecialchars((string)$wartosc);
                        echo '<td>' . $tekst . '</td>';
                    }
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<div class="komunikat komunikat-info">Brak wyników.</div>';
            }
        } else {
            echo '<div class="komunikat komunikat-info">Wybierz przycisk powyżej, aby wyświetlić dane.</div>';
        }

        $conn->close();
    }
    ?>
</div>

</body>
</html>
