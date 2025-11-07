<?php
// J’inclus ce dont j’ai besoin. require_once évite les doubles inclusions.
require_once 'DBConnect.php';
require_once 'Contact.php';
require_once 'ContactManager.php';

/**
 * Classe Command
 * --------------
 * Mon rôle : contenir la logique d’exécution des commandes.
 * Ici je commence simple, avec la commande `list`.
 */
class Command
{
    /**
     * list()
     * ------
     * Je vais chercher tous les contacts via ContactManager,
     * puis j’affiche chaque contact avec sa méthode toString().
     * Je ne prends pas de paramètre et je ne retourne rien.
     */
    public function list()
    {
        // Je crée ma connexion et mon manager de contacts
        $db = new DBConnect();
        $manager = new ContactManager($db);

        // Je récupère les objets Contact
        $contacts = $manager->findAll();

        // Si la liste est vide, je le dis simplement 
        if (!$contacts) {
            echo "Aucun contact.\n";
            return;
        }

        // J’affiche chaque contact proprement
        foreach ($contacts as $contact) {
            echo $contact->toString() . "\n";
        }
    }

    /**
     * detail($id)
     * -----------
     * Je récupère UN contact par son identifiant et je l’affiche.
     * Si l’id n’existe pas, je le signale.
     */
    public function detail($id)
    {
        // 1) Accès aux données
        $db = new DBConnect();
        $manager = new ContactManager($db);

        // 2) Recherche du contact
        $contact = $manager->findById((int) $id);

        // 3) Affichage ou message si introuvable
        if ($contact === null) {
            echo "Contact #$id introuvable.\n";
            return;
        }

        echo $contact->toString() . "\n";
    }

    /**
     * create($name, $email, $phone)
     * -----------------------------
     * Je crée un nouveau contact en base.
     * Pour rester simple (comme dans le cours), je crée puis j’affiche un message de confirmation.
     */
    public function create($name, $email, $phone)
    {
        // 1) Accès aux données
        $db = new DBConnect();
        $manager = new ContactManager($db);

        // 2) Insertion
        $contact = $manager->create($name, $email, $phone);

        // 3) Confirmation minimale
        echo "Contact créé (#" . $contact->getId() . ").\n";
    }

     /**
     * delete($id)
     * -----------
     * Je supprime un contact par son identifiant.
     * J’indique si la suppression a réellement eu lieu.
     */
    public function delete($id)
    {
        // 1) Accès aux données
        $db = new DBConnect();
        $manager = new ContactManager($db);

        // 2) Suppression
        $ok = $manager->delete((int) $id);

        // 3) Message utilisateur
        if ($ok) {
            echo "Contact #$id supprimé.\n";
        } else {
            echo "Contact #$id introuvable.\n";
        }
    }

    /**
     * modify($id, $name, $email, $phone)
     * ----------------------------------
     * Je mets à jour un contact existant avec les nouvelles informations.
     * Pour rester simple (dans l’esprit du cours), je demande les 3 champs.
     * - Si l’id n’existe pas, j’affiche un message.
     * - Sinon, je confirme et j’affiche le contact mis à jour.
     */
    public function modify($id, $name, $email, $phone)
    {
        $id    = (int) $id;
        $name  = trim($name);
        $email = trim($email);
        $phone = trim($phone);

        $db = new DBConnect();
        $manager = new ContactManager($db);

        // J’essaie de faire la mise à jour ; si l’id n’existe pas, update renverra null
        $updated = $manager->update($id, $name, $email, $phone);

        if ($updated === null) {
            echo "Contact #$id introuvable : aucune mise à jour effectuée.\n";
            return;
        }

        echo "Contact #$id mis à jour.\n";
        echo $updated . "\n"; // __toString() affiche le contact
    }
    
    public function help()
    {
        echo "Commandes disponibles :\n";
        echo "  list                                   Afficher tous les contacts\n";
        echo "  detail <id>                            Afficher un contact par son id\n";
        echo "  create <name>, <email>, <phone>        Créer un contact\n";
        echo "  delete <id>                            Supprimer un contact par son id\n";
        echo "  modify <id>, <name>, <email>, <phone>  Mettre à jour un contact\n";
        echo "  quit                                   Quitter le programme\n";
    }
}