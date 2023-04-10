<?php
require_once "config.php";

$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? '';
$order = $_GET['order'] ?? 'asc';

// generate_sort_link() génère le lien pour trier les résultats 
function generate_sort_link($column, $current_sort_by, $current_order) {
    $new_order = ($current_order === 'asc') ? 'desc' : 'asc'; // ordre de tri inverse (asc => desc, desc => asc)
    $icon = ($current_sort_by === $column) ? ($current_order === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down') : 'fas fa-sort'; // icône de tri
    
    //sprintf() permet de générer une chaîne de caractères en remplaçant les %s par les valeurs passées en paramètre
    //on passe urlencode() sur les paramètres pour éviter les problèmes d'encodage
    //on retourne le lien généré
    return sprintf(
        '<a href="?search=%s&sort_by=%s&order=%s"><i class="%s"></i></a>',
        urlencode($_GET['search'] ?? ''),
        urlencode($column), // colonne sur laquelle on veut trier
        urlencode($new_order),  // ordre de tri
        $icon // icône de tri
    );
}

$valid_sort_columns = ['nom_simple', 'code_postal', 'code_commune', 'population_2010', 'densite_2010']; // colonnes sur lesquelles on peut trier les résultats
$valid_orders = ['asc', 'desc']; // ordres de tri possibles

// On récupère les villes correspondant à la recherche
if (!empty($search)) {
    $min_population = $_GET['min_population'] ?? '';
    $sql = "SELECT * FROM villes WHERE (nom_simple LIKE :search OR code_postal LIKE :search OR code_commune LIKE :search)";
    // On ajoute la condition sur la population si elle est renseignée et si elle est un nombre
    if (!empty($min_population) && is_numeric($min_population)) {
        $sql .= " AND (population_2010 >= :min_population)";
    }
    // On ajoute le tri si les paramètres sont valides (voir $valid_sort_columns et $valid_orders)
    if (in_array($sort_by, $valid_sort_columns) && in_array($order, $valid_orders)) {
        $sql .= " ORDER BY {$sort_by} {$order}";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);

    // On ajoute la valeur de la population si elle est renseignée et si elle est un nombre
    if (!empty($min_population) && is_numeric($min_population)) {
        $stmt->bindValue(':min_population', $min_population, PDO::PARAM_INT);
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche de villes et de départements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <h1 class="my-4">Recherche de villes et de départements</h1>
        <form action="index.php" method="get" class="mb-4">
    <div class="row">
        <div class="col-md-8">
            <input type="text" name="search" id="search" class="form-control" placeholder="Rechercher" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="min_population" id="min_population" class="form-control" placeholder="Population min" value="<?= htmlspecialchars($_GET['min_population'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </div>
    </div>
</form>


        <?php if (!empty($search) && !empty($results)): ?>
            <table class="table">
            <thead>
    <tr>
        <th>Nom <?= generate_sort_link('nom_simple', $sort_by, $order) ?></th>
        <th>Code postal <?= generate_sort_link('code_postal', $sort_by, $order) ?></th>
        <th>Code commune <?= generate_sort_link('code_commune', $sort_by, $order) ?></th>
        <th>Population 2010 <?= generate_sort_link('population_2010', $sort_by, $order) ?></th>
        <th>Densité 2010 <?= generate_sort_link('densite_2010', $sort_by, $order) ?></th>
        <th>Details</th>
    </tr>
</thead>


    <tbody>
        <?php foreach ($results as $result): ?>
            <tr>
                <td><?= htmlspecialchars($result['nom_simple']) ?></td>
                <td><?= htmlspecialchars($result['code_postal']) ?></td>
                <td><?= htmlspecialchars($result['code_commune']) ?></td>
                <td><?= htmlspecialchars($result['population_2010']) ?></td>
                <td><?= htmlspecialchars($result['densite_2010']) ?></td>
                <td><a href="details.php?id=<?= htmlspecialchars($result['id']) ?>">+</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
        <?php elseif (!empty($search)): ?>
            <div class="alert alert-danger">Aucun résultat trouvé.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybB5vE2b5Ww0KJo5+ElUf4vC/0gTeXQ2Q0zPd9Qro8fs4E+J4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

</body>
</html>


