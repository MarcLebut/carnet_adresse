<?php
// Je charge la classe Command (elle inclut déjà DBConnect, Contact et ContactManager).
require_once 'Command.php';

// J’instancie une seule fois l’objet qui contient la logique des commandes.
$command = new Command();

while (true) {
    // 1) Je lis ce que l’utilisateur tape (une ligne).
    $line = readline("Entrez votre commande : ");

    // 2) Je teste les commandes, une par une.

    // ---- list ----
    // Commande sans paramètre : j’affiche tous les contacts.
    if ($line === "list") {
        $command->list();

    // ---- help ----
    // Affiche la liste des commandes disponibles (bonus utile en CLI).
    } elseif ($line === "help") {
        $command->help();

    // ---- quit ----
    // Quitte proprement le programme.
    } elseif ($line === "quit") {
        echo "Au revoir 👋\n";
        break;

    // ---- detail <id> ----
    // Exemple : "detail 42" → on récupère l'id (42) avec une expression régulière.
    } elseif (preg_match('/^detail\s+(\d+)\s*$/', $line, $m)) {
        $id = (int)$m[1];     // $m[1] contient la 1re capture : l'id
        $command->detail($id);

    // ---- create <name>, <email>, <phone> ----
    // Exemple : "create Jean Dupont, jean@example.com, 0612345678"
    // On capte 3 morceaux séparés par des virgules (avec espaces tolérés).
    } elseif (preg_match('/^create\s+([^,]+)\s*,\s*([^,]+)\s*,\s*(.+)\s*$/', $line, $m)) {
        $name  = trim($m[1]); // nom (tout jusqu’à la 1re virgule)
        $email = trim($m[2]); // email (jusqu’à la 2e virgule)
        $phone = trim($m[3]); // tout le reste = téléphone
        $command->create($name, $email, $phone);

    // ---- modify <id>, <name>, <email>, <phone> ----
    // Exemple : "modify 5, Alice, alice@exemple.fr, 0611223344"
    // Même logique que create, mais avec un id au début.
    } elseif (preg_match('/^modify\s+(\d+)\s*,\s*([^,]+)\s*,\s*([^,]+)\s*,\s*(.+)\s*$/', $line, $m)) {
        $id    = (int)$m[1];
        $name  = trim($m[2]);
        $email = trim($m[3]);
        $phone = trim($m[4]);
        $command->modify($id, $name, $email, $phone);

    // ---- delete <id> ----
    // Exemple : "delete 12" → on récupère l'id (12) avec une regex.
    } elseif (preg_match('/^delete\s+(\d+)\s*$/', $line, $m)) {
        $id = (int)$m[1];
        $command->delete($id);

    // ---- commande non reconnue ----
    // Pour l’instant, on indique simplement quoi taper.
    } else {
        echo "Commande inconnue. Tapez 'help' pour l’aide.\n";
    }
}
