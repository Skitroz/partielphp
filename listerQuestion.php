<?php
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['supprimer']) && intval($_POST['supprimer']) > 0) {
        $id_suppression = intval($_POST['supprimer']);

        $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->bind_param("i", $id_suppression);
        $stmt->execute();
        $stmt->close();
    }
}

$ordre_tri = isset($_GET['tri']) && in_array($_GET['tri'], ['asc', 'desc']) ? $_GET['tri'] : 'asc';
$sql_ordre = $ordre_tri === 'asc' ? "ASC" : "DESC";

$sql = "SELECT id, question, nombre_tentatives, nombre_succes FROM questions ORDER BY (nombre_succes / IF(nombre_tentatives = 0, 1, nombre_tentatives)) $sql_ordre";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Liste des Questions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-800">
    <div class="flex justify-center items-center flex-col mt-10">
        <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">Liste des questions</h1>

        <form action="listerQuestion.php" method="get" class="flex items-center gap-4 mt-2">
            <select name="tri" class="w-44 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="asc" <?php echo $ordre_tri === 'asc' ? 'selected' : ''; ?>>Taux de réussite croissant</option>
                <option value="desc" <?php echo $ordre_tri === 'desc' ? 'selected' : ''; ?>>Taux de réussite décroissant</option>
            </select>
            <input type="submit" value="Trier" class="cursor-pointer text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
        </form>

        <?php if ($result->num_rows > 0) : ?>
            <table class="text-center w-full px-[100px] text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-10">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Question</th>
                        <th scope="col" class="px-6 py-3">Taux de Réussite</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 ">
                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['question']); ?></td>
                        <td class="px-6 py-4">
                            <?php
                            $taux = $row['nombre_tentatives'] > 0 ? ($row['nombre_succes'] / $row['nombre_tentatives']) * 100 : 0;
                            echo round($taux, 2) . '%';
                            ?>
                        </td>
                        <td class="px-6 py-4">
                            <form method="post" action="listerQuestion.php">
                                <input type="hidden" name="supprimer" value="<?php echo $row['id']; ?>">
                                <input type="submit" value="Supprimer" class="cursor-pointer text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>Pas de questions trouvées.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </div>
</body>

</html>