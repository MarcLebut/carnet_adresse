<?php
// J'ai besoin de la classe Contact pour instancier mes objets.
require_once 'Contact.php';

/**
 * Classe ContactManager
 * ---------------------
 * Mon rôle : parler à la base via DBConnect et me charger de récupérer
 * les contacts. Ici je transforme chaque ligne SQL en objet Contact.
 */
class ContactManager
{
    /** Je garde la connexion DB. */
    private $db;

    /** On me donne l’objet DBConnect à la construction. */
    public function __construct(DBConnect $db)
    {
        $this->db = $db;
    }

    /**
     * findAll()
     * ---------
     * Je retourne maintenant un tableau d’objets Contact (et non plus un tableau associatif).
     * Chaque objet Contact représente une ligne de la table `contact`.
     * Je fais aussi un var_dump de ce que je retourne pour vérifier immédiatement.
     */
    public function findAll()
    {
        // 1) Je récupère mon PDO
        $pdo = $this->db->getPDO();

        // 2) Je lis toutes les colonnes utiles
        $sql = "SELECT id, name, email, phone_number FROM contact";
        $requete = $pdo->query($sql);

        // 3) Je récupère les lignes sous forme de tableaux associatifs
        $lignes = $requete->fetchAll(PDO::FETCH_ASSOC);

        // 4) Je transforme chaque ligne en objet Contact
        $contacts = [];
        foreach ($lignes as $ligne) {
            $contacts[] = new Contact(
                isset($ligne['id']) ? (int) $ligne['id'] : null,
                $ligne['name']        ?? null,
                $ligne['email']       ?? null,
                $ligne['phone_number']?? null
            );
        }

        // 🔎 Test immédiat exigé par la consigne :
        // Je vérifie que ce que je retourne n’est ni null ni vide.
        //var_dump($contacts);

        // 5) Je renvoie le tableau d’objets Contact
        return $contacts;
    }

    /**
     * findById($id)
     * -------------
     * Je lis UN contact par son identifiant.
     * - Si je trouve une ligne, je renvoie un objet Contact.
     * - Sinon, je renvoie null (contact introuvable).
     */
    public function findById($id)
    {
        // 1) Je récupère PDO.
        $pdo = $this->db->getPDO();

        // 2) Requête paramétrée pour chercher par id (sécurisée).
        $sql = "SELECT id, name, email, phone_number
                FROM contact
                WHERE id = :id";

        // 3) Je prépare + j’exécute la requête avec l’id fourni.
        $requete = $pdo->prepare($sql);
        $requete->execute([':id' => $id]);

        // 4) Je lis une seule ligne.
        $ligne = $requete->fetch(PDO::FETCH_ASSOC);

        // 5) Si rien trouvé → je renvoie null.
        if (!$ligne) {
            return null;
        }

        // 6) Sinon, je construis l’objet Contact correspondant.
        $contact = new Contact(
            isset($ligne['id']) ? (int)$ligne['id'] : null,
            $ligne['name'] ?? null,
            $ligne['email'] ?? null,
            $ligne['phone_number'] ?? null
        );

        // (Phase de test éventuelle)
        // var_dump($contact);

        return $contact;
    }

    /**
     * create($name, $email, $phone_number)
     * ------------------------------------
     * Je crée un nouveau contact en base, puis je renvoie l’objet Contact
     * correspondant (avec l’id auto-incrémenté récupéré).
     */
    public function create($name, $email, $phone_number)
    {
        // 1) Je récupère mon PDO
        $pdo = $this->db->getPDO();

        // 2) Requête paramétrée d’insertion.
        $sql = "INSERT INTO contact (name, email, phone_number)
                VALUES (:name, :email, :phone)";

        $requete = $pdo->prepare($sql);

        // 3) J’exécute avec les valeurs fournies.
        $requete->execute([
            ':name'  => $name,
            ':email' => $email,
            ':phone' => $phone_number,
        ]);

        // 4) Je récupère l’id généré par MySQL.
        $id = (int)$pdo->lastInsertId();

        // 5) Je construis l’objet Contact final (complet).
        $contact = new Contact($id, $name, $email, $phone_number);

        // (Phase de test éventuelle)
        // var_dump($contact);

        return $contact;
    }

    /**
     * delete($id)
     * -----------
     * Je supprime un contact par son id.
     * - Je renvoie true si au moins une ligne a été supprimée.
     * - Je renvoie false si aucun contact ne correspondait à cet id.
     */
    public function delete($id)
    {
        // 1) Récupérer PDO.
        $pdo = $this->db->getPDO();

        // 2) Requête paramétrée de suppression.
        $sql = "DELETE FROM contact WHERE id = :id";

        $requete = $pdo->prepare($sql);
        $requete->execute([':id' => $id]);

        // 3) rowCount() me dit combien de lignes ont été impactées.
        return $requete->rowCount() > 0;
    }

    /**
     * update($id, $name, $email, $phone_number)
     * -----------------------------------------
     * Je mets à jour un contact existant.
     * - Si l’id n’existe pas : je renvoie null.
     * - Sinon : je renvoie le Contact mis à jour.
     */
    public function update($id, $name, $email, $phone_number)
    {
        // 1) Je récupère mon PDO
        $pdo = $this->db->getPDO();

        // 2) Vérifier que le contact existe
        $existant = $this->findById($id);
        if ($existant === null) {
            return null; // rien à mettre à jour
        }

        // 3) Mettre à jour
        $sql = "UPDATE contact
                SET name = :name, email = :email, phone_number = :phone
                WHERE id = :id";

        $requete = $pdo->prepare($sql);
        $requete->execute([
            ':name'  => $name,
            ':email' => $email,
            ':phone' => $phone_number,
            ':id'    => $id,
        ]);

        // 4) Relire et renvoyer l’objet à jour
        return $this->findById($id);
    }
}
