<?php
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST['question'];
    $reponse = $_POST['reponse'];
    $message_succes = $_POST['message_succes'];
    $message_echec = $_POST['message_echec'];

    $stmt = $conn->prepare("INSERT INTO questions (question, reponse, message_succes, message_echec) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $question, $reponse, $message_succes, $message_echec);

    if ($stmt->execute()) {
        echo "<div class='flex justify-center items-center flex-col bg-green-200 border border-green-500 text-green-700 px-4 py-3 rounded-md my-4' role='alert'>
                <p class='font-bold'>Question ajoutée avec succès. ID de la question: " . $stmt->insert_id . "</p>
                <p><a href='repondreQuestion.php?id=" . $stmt->insert_id . "' class='text-blue-600 hover:underline'>Lien pour répondre à cette question</a></p>
              </div>";
    } else {
        echo "<div class='bg-red-200 border border-red-500 text-red-700 px-4 py-3 rounded-md my-4' role='alert'>
                <p class='font-bold'>Erreur : " . $stmt->error . "</p>
              </div>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Ajouter une Question</title>
</head>

<body class="bg-gray-100 dark:bg-gray-800">
    <h2 class="mt-10 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white text-center">Ajouter une question</h2>
    <form method="post" action="ajouterQuestion.php" class="my-6 flex flex-col justify-center items-center w-50">
        <label for="question" class="!text-left block mb-2 text-sm font-medium text-gray-900 dark:text-white">Question</label>
        <input type="text" name="question" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 w-80 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"><br>
        <label for="reponse" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Réponse</label>
        <input type="text" name="reponse" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 w-80 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"><br>
        <label for="question" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Message de bonne réponse</label>
        <input type="text" name="message_succes" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 w-80 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"><br>
        <label for="question" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Message de mauvaise réponse</label>
        <input type="text" name="message_echec" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 w-80 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"><br>
        <input type="submit" value="Ajouter la question" class="cursor-pointer text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
    </form>
</body>

</html>