<?php
/**
 * Classe Contact
 * ----------------
 * Je représente UN contact en mémoire (mêmes champs que la BDD).
 * Objectif de cette mise à jour : ajouter __toString()
 * pour que l'objet s'affiche directement avec echo.
 */
class Contact
{
    /** Identifiant (peut être null tant que je ne suis pas inséré en base) */
    private ?int $id = null;

    /** Nom */
    private ?string $name = null;

    /** Email */
    private ?string $email = null;

    /** Numéro de téléphone */
    private ?string $phone_number = null;

    /**
     * Constructeur
     * Je peux recevoir toutes mes valeurs à la création.
     * Si rien n'est donné, je reste à null.
     */
    public function __construct(?int $id = null, ?string $name = null, ?string $email = null, ?string $phone_number = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone_number = $phone_number;
    }

    // =======================
    // Getters
    // =======================
    public function getId(): ?int        { return $this->id; }
    public function getName(): ?string   { return $this->name; }
    public function getEmail(): ?string  { return $this->email; }
    public function getPhoneNumber(): ?string { return $this->phone_number; }

    // =======================
    // Setters
    // =======================
    public function setId(?int $id): void                { $this->id = $id; }
    public function setName(?string $name): void         { $this->name = $name; }
    public function setEmail(?string $email): void       { $this->email = $email; }
    public function setPhoneNumber(?string $phone): void { $this->phone_number = $phone; }

    // =======================
    // Affichage
    // =======================

    /**
     * Représentation "humaine" d'un contact pour affichage rapide.
     * Exemple: "3 | Jean Dupont | jean@exemple.com | 0612345678"
     */
    public function toString(): string
    {
        $id    = $this->id !== null ? (string)$this->id : 'null';
        $name  = $this->name ?? '';
        $email = $this->email ?? '';
        $phone = $this->phone_number ?? '';
        return "{$id} | {$name} | {$email} | {$phone}";
    }

    /**
     * __toString()
     * ------------
     * Méthode "magique" PHP : si j'utilise l'objet dans un echo,
     * PHP appellera automatiquement cette méthode.
     * => echo $contact;  // affichera le même rendu que toString()
     *
     * On délègue à toString() pour garder un seul format centralisé.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
