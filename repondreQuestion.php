<?php
include 'db.php';
include 'header.php';

$question_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$question_info = null;
$message = "";
$afficher_formulaire = true;

if ($question_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $question_info = $result->fetch_assoc();
    } else {
        $message = "Question introuvable.";
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $question_info) {
    $reponse_utilisateur = $_POST['reponse'];
    $reponse_correcte = $question_info['reponse'];

    $stmt = $conn->prepare("UPDATE questions SET nombre_tentatives = nombre_tentatives + 1 WHERE id = ?");
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $stmt->close();

    if (strtolower($reponse_utilisateur) == strtolower($reponse_correcte)) {
        $message = $question_info['message_succes'];
        $afficher_formulaire = false;

        $stmt = $conn->prepare("UPDATE questions SET nombre_succes = nombre_succes + 1 WHERE id = ?");
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $message = $question_info['message_echec'];
    }
}

$sql = "SELECT id, question FROM questions";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Répondre à une Question</title>
</head>

<body class="bg-gray-100 dark:bg-gray-800">
    <div class="flex flex-col justify-center items-center">
        <h2 class="mb-4 mt-10 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">Liste des questions</h2>
        
        <?php if ($result->num_rows > 0) : ?>
            <ul class="list-disc text-left">
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <li class="mb-2">
                        <?php echo htmlspecialchars($row['question']); ?>
                        <a href="repondreQuestion.php?id=<?php echo $row['id']; ?>" class="ml-2 text-blue-500 hover:underline">Répondre</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p class="mt-4">Pas de questions trouvées.</p>
        <?php endif; ?>

        <?php if ($question_info && $afficher_formulaire) : ?>
            <div class="mt-8">
                <h2 class="text-2xl font-bold mb-2">Répondre à la Question</h2>
                <p class="mb-4">Question : <?php echo htmlspecialchars($question_info['question']); ?></p>
                <form method="post" action="repondreQuestion.php?id=<?php echo $question_id; ?>" class="flex items-center">
                    <label for="reponse" class="mr-2">Réponse :</label>
                    <input type="text" name="reponse" id="reponse" required class="border p-2 rounded-md">
                    <button type="submit" class="ml-4 cursor-pointer text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Valider</button>
                </form>
            </div>
        <?php endif; ?>

        <p class="mt-4 text-red-500"><?php echo $message; ?></p>
    </div>
</body>

</html>