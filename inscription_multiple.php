<?php
// Informations de connexion à la base de données
$host = "ufhrougrobotup.mysql.db";
$dbname = "ufhrougrobotup";
$username = "ufhrougrobotup";
$password = "R0botUp2023";

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les informations du parent
    $parent_name = htmlspecialchars($_POST['parent-name']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);

    // Insérer les informations du parent dans la table `rbt2501_parents`
    $sql_parent = "INSERT INTO rbt2501_parents (parent_name, phone, email) 
                   VALUES (:parent_name, :phone, :email)";
    $stmt_parent = $pdo->prepare($sql_parent);
    $stmt_parent->execute([
        ':parent_name' => $parent_name,
        ':phone' => $phone,
        ':email' => $email
    ]);

    // Récupérer l'ID du parent inséré
    $parent_id = $pdo->lastInsertId();

    // Récupérer les informations des enfants
    $child_names = $_POST['child-name'];
    $ages = $_POST['age'];
    $activities = $_POST['activity'];
    $free_sessions = isset($_POST['free-session']) ? $_POST['free-session'] : [];

    // Insérer les informations des enfants dans la table `rbt2501_enfants`
    $sql_child = "INSERT INTO rbt2501_enfants (parent_id, child_name, age, activity, wants_free_session) 
                  VALUES (:parent_id, :child_name, :age, :activity, :wants_free_session)";
    $stmt_child = $pdo->prepare($sql_child);

    for ($i = 0; $i < count($child_names); $i++) {
        $stmt_child->execute([
            ':parent_id' => $parent_id,
            ':child_name' => htmlspecialchars($child_names[$i]),
            ':age' => htmlspecialchars($ages[$i]),
            ':activity' => htmlspecialchars($activities[$i]),
            ':wants_free_session' => in_array((string)$i, $free_sessions) ? 1 : 0
        ]);
    }

    echo "Inscription réussie pour le parent et ses enfants !";
}
