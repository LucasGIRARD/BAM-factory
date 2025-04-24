<?php
if (!isset($_SESSION) || empty($_SESSION) || $_SESSION['bamColletifOK'] != TRUE) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php';
    header("Location: http://$host$uri/$extra");
}

$fullPath = parse_url('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], PHP_URL_PATH);



if ($fullPath[strlen($fullPath) - 1] != '/') {

    $position = strrpos($fullPath, '/', -1);

    $fullPath = substr($fullPath, 0, $position + 1);
}

$url = 'http://' . $_SERVER['HTTP_HOST'] . $fullPath;

include 'SQL.php';

$connection = openSQLConnexion();


$donneesSqlGeneral = select($connection, "SELECT
    S.id, S.name, S.title, S.bamVersion, S.status,
    GROUP_CONCAT(A.name, '<br>') AS alias, GROUP_CONCAT(CONCAT(H.date, ' - ', H.description), '<br>') AS historic
    FROM SITES AS S
    LEFT JOIN ALIAS AS A ON A.SITES_id = S.id
    LEFT JOIN HISTORIC AS H ON H.SITES_id = S.id
    GROUP BY S.id");


closeSQLConnexion($connection);

$status = array(0 => 'Pré-prod',1 => 'Production',2 => 'Archive');

$sites = array();

if (isset($donneesSqlGeneral) && !empty($donneesSqlGeneral)) {

    foreach ($status as $key => $value) {
        $sites[$key] = array();
    }

    foreach ($donneesSqlGeneral as $value) {
        array_push($sites[$value['status']], $value);
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>BAM FACTORY : L’usine des ponix</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="vendors/purecss/1.0/pure-min.css">
    <link rel="stylesheet" type="text/css" href="main.css">
</head>

<body>
    <nav>
        <ul>
            <li><a href="#creation">Création</a></li>
            <li><a href="#preprod">Pré-prod</a></li>
            <li><a href="#production">Production</a></li>
            <li><a href="#archive">Archive</a></li>
        </ul>
    </nav>

    <h1>BAM FACTORY</h1>

    <form id="creation" action="" method="post" class="pure-form">
        <fieldset>
            <legend><h2>Création</h2></legend>
            <div class="pure-g">

                <div class="pure-u-12-24">                    
                    <input type="text" name="" placeholder="Nom" class="size-full">
                </div>
                <div class="pure-u-8-24">                    
                    <input type="text" name="" placeholder="Titre" class="size-full">
                </div>
                <div class="pure-u-4-24">
                    <label for="">version :</label>
                    <select name="">
                        <option>1</option>
                        <option>2</option>
                    </select>
                </div>
                <div class="pure-u-1">
                    <table>
                        <caption><h3>Alias</h3><input type="submit" value="Ajouter" class="pure-button button-success"></caption>
                        <tr>
                            <td>
                                <input type="text" name="" class="size-full">
                            </td>
                            <td>
                                <form id="creation" action="" method="post">
                                    <input type="submit" value="supprimer"  class="size-full pure-button button-error">
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="pure-u-1 center">
                    <input type="submit" value="Créer" class="size-full pure-button pure-button-primary">
                </div>
            </div>

        </fieldset>
    </form>


    <?php
    foreach ($status as $key => $value) {
        ?>
        <table id="archive">
            <caption><h2><?php echo $value; ?></h2></caption>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Titre</th>
                <th>Version</th>
                <th>Alias</th>
                <th>Historique</th>
            </tr>
            <?php
            if (empty($sites[$key])) {
                echo '<tr><td colspan="6" class="center">Aucun site</td></tr>';
            } else {
                ?>
                <tr>
                    <?php
                    foreach ($sites[$key] as $value2) {
                        echo '<tr>
                        <td>', $value2['id'], '</td>
                        <td>', $value2['name'], '</td>
                        <td>', $value2['title'], '</td>
                        <td>', $value2['version'], '</td>
                        <td>', $value2['alias'], '</td>
                        <td>', $value2['historic'], '</td>
                        </tr>';
                    }
                    ?>
                </table>
                <?php }
            }
            ?>
        </body>
        </html>