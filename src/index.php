<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Baza danych</title>

    <style>
        .przyciski {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        button {
            padding: 10px 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Przyciski na górze -->
<form method="post" class="przyciski">
    <button type="submit" name="akcja" value="wszyscy">Wszyscy użytkownicy</button>
    <button type="submit" name="akcja" value="miejscowosci">Miejscowości</button>
    <button type="submit" name="akcja" value="hobby">Liczba osób według hobby</button>
    
</form>
<?php
$conn = new mysqli(
    getenv("DB_HOST"),
    getenv("DB_USER"),
    getenv("DB_PASSWORD"),
    getenv("DB_NAME")
);

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$sql = "";

if (isset($_POST['akcja'])) {
    switch ($_POST['akcja']) {

        case "wszyscy":
            $sql = "SELECT * FROM uczniowie";
            break;

        case "miejscowosci":
            $sql = "SELECT DISTINCT miejscowosc FROM uczniowie";
            break;

        case "hobby":
            $sql = "SELECT hobby, COUNT(*) AS liczba FROM uczniowie GROUP BY hobby";
            break;

        
    }
}

if ($sql != "") {
    $wynik = $conn->query($sql);

    if ($wynik && $wynik->num_rows > 0) {
        if ($_POST['akcja'] == "wszyscy") {
        echo "<table border='1'>";
        echo "<tr>
                <th>id</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Data urodzenia</th>
                <th>Miejscowość</th>
                <th>Kod pocztowy</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Hobby</th>
              </tr>";

        while ($wiersz = $wynik->fetch_assoc()) {
            echo "<tr>";
            foreach ($wiersz as $wartosc) {
                echo "<td>" . htmlspecialchars($wartosc ?? "") . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
    } else {

            while ($wiersz = $wynik->fetch_assoc()) {
                foreach ($wiersz as $wartosc) {
                    echo htmlspecialchars($wartosc ?? "") . "  ";
                }
                echo "<br>";
            } 
    }
    }else {
        echo "Brak wyników.";
    }
}

$conn->close();
?>

</body>
</html>
