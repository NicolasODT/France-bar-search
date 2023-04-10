<?php
require_once "config.php";

// On récupère l'id de la ville dans l'URL
$id = $_GET['id'] ?? '';

if (!empty($id) && is_numeric($id)) {
    $stmt = $pdo->prepare("SELECT * FROM villes WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de la ville</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body>
<div class="my-4">
    <canvas id="chart"></canvas>
</div>

    <div class="container">
        <h1 class="my-4">Détails de la ville</h1>
        <?php if (!empty($result)): ?>
            <table class="table">
                <tbody>
                    <?php foreach ($result as $key => $value): // $key = nom de la colonne (ex: id, nom, code_postal, ...) et $value = valeur de la colonne (ex: 1, Paris, 75000, ...)?> 
                        <tr>
                            <th><?= htmlspecialchars($key) ?></th>
                            <td><?= htmlspecialchars($value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-danger">Aucune ville trouvée.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybB5vE2b5Ww0KJo5+ElUf4vC/0gTeXQ2Q0zPd9Qro8fs4E+J4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>
</html>