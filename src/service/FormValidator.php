<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;

final class FormValidator
{    //service conteni deux propriere : donne du formulaire qui vient du post 
    //les error de validation est detecte
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
    //retour n e les erreur pour afficher a la vue
    public function getErrors(): array
    {
        return $this->errors;
    }
    //si une erreur exist
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    //ku dans la classe ajoute dans le tableau l'erreur le champ et on met le champs 
    //si il y'a qu'quel que chose il met erreur
    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }
    // methode qui va verifie si le champs n'est pas vide
    //veirife si y'a des trou , passe les champs , un message 
    //la valeur , il va me cherche dans les data le champs correspondant les donne c 'est dans le post 
    //post s'appuie post name pas de name tombe en get
    //il va faire un trim car un text on nettoyer la variable si la valeur est vide alors tu rajoute erreor
    public function required(string $field, string $message = 'Ce champ est obligatoire.'): self
    {
        $value = trim((string)($this->data[$field] ?? ''));
        if ($value === '') {
            $this->addError($field, $message);
        }
        return $this;
    }
    //verifie le minimum longueur si la valeur est valeur
    //si inferieuir $min il est vide
    public function minLength(string $field, int $min, string $message): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }
        $value = trim((string)($this->data[$field] ?? ''));
        if ($value !== '' && mb_strlen($value) < $min) {
            $this->addError($field, $message);
        }
        return $this;
    }
    //calculer la longueur maxim peut limiter le commentaire
    //si il '
    //mb strlen retour la taille d'une chaine  de caractere
    public function maxLength(string $field, int $max, string $message): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }
        //si la valeur n'est pas vide et la longueur de la chaine de la valeur est superieur 
        //je lui demande de rajouter une erreur 
        $value = trim((string)($this->data[$field] ?? ''));
        if ($value !== '' && mb_strlen($value) > $max) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function email(string $field, string $message = 'Email invalide.'): self
    {
        //determiner si la variable est nul
        if (isset($this->errors[$field])) {
            return $this;
        }
        //verifie si l'adressse email 
        $value = trim((string)($this->data[$field] ?? ''));
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function phoneMinDigits(string $field, int $minDigits, string $message): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }
        $value = trim((string)($this->data[$field] ?? ''));
        $digits = preg_replace('/\D+/', '', $value);
        if ($digits === null || strlen($digits) < $minDigits) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function dateNotPast(
        string $field,
        string $messageInvalid = 'Date invalide.',
        string $messagePast = 'La date doit être égale ou postérieure à aujourd\'hui.'
    ): self {
        if (isset($this->errors[$field])) {
            return $this;
        }
        $value = trim((string)($this->data[$field] ?? ''));
        if ($value === '') {
            return $this;
        }
        try {
            $bookingDate = new DateTimeImmutable($value);
            $today = (new DateTimeImmutable('today'))->setTime(0, 0);
            if ($bookingDate < $today) {
                $this->addError($field, $messagePast);
            }
        } catch (\Throwable) {
            $this->addError($field, $messageInvalid);
        }
        return $this;
    }
}
