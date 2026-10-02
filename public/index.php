<?php

require_once __DIR__ . '/../src/includer.php';

use App\Rainbow\lib\Rainbow;

$totalNb = Rainbow::getInstance()->getTotalCount();
$algos = Rainbow::getInstance()->getSupportedAlgos();

$searchedHash = '';
$results = [];
if (!empty($_REQUEST['search']) && !empty($_REQUEST['hash'])) {
    $searchedHash = trim(strval($_REQUEST['hash']));
    $results = Rainbow::getInstance()->searchHash($searchedHash);
}

?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title></title>
</head>

<body>
    <h1>Rainbow lookup table</h1>
    <p>
        Currently <?php echo $totalNb; ?> hashes to lookup within,
        with <?php echo count($algos); ?> supported algos
    </p>
    <form>
        <fieldset>
            <legend>Search for a hash</legend>
            <p>
                <input type="search"
                    name="hash"
                    value="<?php echo htmlspecialchars($searchedHash); ?>" />
                <input type="submit" name="search" value="Search" />
            </p>
        </fieldset>
    </form>
    <table id="searchResults">
        <caption>Hash search results (<?php echo count($results); ?>)</caption>
        <thead>
            <tr>
                <th>Algo</th>
                <th>Clear</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($results as $res) { ?>
            <tr>
                <td><?php echo htmlspecialchars($res['algo']); ?></td>
                <td><?php echo htmlspecialchars($res['clear']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>