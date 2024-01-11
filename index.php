<!DOCTYPE html>
<html lang="hu">
<head>
    <title>Vendégkönyv</title>
    <style>
		body{
			margin-left:50px;
			margin-right:50px;
			background-color: skyblue;
		}
		h1{
			text-align:center;
			color:white;
		}
        p{
            font-weight:bold ; 
            margin-top:20px;
            margin-left:400px;
        }
		form{
			color:white;
		}
        b{
            margin-left:500px;
        }
        table, tr, td {

            text-align:left;
            border:1px solid black;
            margin-left:300px;
        }
        #gomb{
            margin-left:90px;
        }
    </style>
</head>
<body>
<h1>Vendégkönyv</h1>
<hr>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $robi = robot();
    if (!$robi) {
        echo "<p>Hibás számot adott meg, kérjük próbálja újra!</p>";
    } else {
        if (!empty($_POST["name"]) && !empty($_POST["message"])) {
            $name = htmlspecialchars($_POST["name"]);
            $message = htmlspecialchars($_POST["message"]);
            $date = date("Y-m-d H:i:s");
            $csatolmany = Fileos();

            $file = 'tartalma.txt';
            $tart = file_get_contents($file);
            $a = count(explode("\n", trim($tart))) + 1;
            $tart = "<p><strong>{$date}</strong> - #{$a} {$name}: {$message}</p>\n";
            if ($csatolmany) {
                $tart .= "<p>Fájl: <a href='{$csatolmany}' target='_blank'>Letöltés</a></p>\n";
            }
            $tart .= "\n" . $tart;
            file_put_contents($file, $tart);
        }
    }
}

echo "<div>";
include 'tartalma.txt';
echo "</div>";
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
<b>Írj egy üzenetet!</b> 
<br>
<table>
    <tr>
        <td><label for="name">Név:</label></td>
        <td><input type="text" name="name" id="name" required></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><label for="message">Üzenet:</label></td>
        <td><textarea id="message" name="message" rows="4" cols="50" required></textarea></td>
    </tr>
    <tr>
        <td><label for="attachment">Csatolmány:</label></td>
        <td><input type="file" name="attachment" id="attachment"></td>
    </tr>
    <tr>
        <td><label for="captcha">Robot vagy? 3 + 5 = </label></td>
        <td><input type="text" name="captcha" id="captcha" required></td>
    </tr>
    <tr>
        <td><input type="submit" id="gomb" value="Küldés"></td>
    </tr>
</table>
</form>

<?php
function Fileos() {
    $feltolt = 'attachments/';

    if (!file_exists($feltolt)) {
        mkdir($feltolt, 0777, true);
    }

    $csatolmany = '';

    if (!empty($_FILES['attachment']['name'])) {
        $tFile = $feltolt . basename($_FILES['attachment']['name']);
        $fel = 1;
        $fileTipus= strtolower(pathinfo($tFile, PATHINFO_EXTENSION));

        if ($fileTipus != 'txt' && $fileTipus != 'pdf' && $fileTipus != 'doc') {
            echo 'Csak txt, pdf és doc fájlok engedélyezettek.';
            $fel = 0;
        }

        if (file_exists($tFile)) {
            echo 'A fájl már létezik.';
            $fel = 0;
        }

        if ($_FILES['attachment']['size'] > 100000) {
            echo 'A fájl mérete túl nagy.';
            $fel = 0;
        }

        if ($fel) {
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $tFile)) {
                $csatolmany = $tFile;
            } else {
                echo 'Hiba történt a fájl feltöltése során.';
            }
        }
    }

    return $csatolmany;
}

function robot() {
    $helyesv = 3 + 5;
    $tied = isset($_POST['captcha']) ? intval($_POST['captcha']) : 0;

    return $tied === $helyesv;
}
$filename = date("Ymd").".txt";

if (!file_exists($filename)) {
    $fp = fopen($filename ,"w");
    fwrite($fp, "0");
    fclose( $fp );
}
$fp = fopen($filename ,"r");
fclose( $fp );


print"Az oldalt eddig $name latogato latta." ;
?>
<hr>
</body>
</html>