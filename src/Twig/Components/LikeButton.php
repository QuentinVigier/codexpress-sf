<?php

namespace App\Twig\Components;

use App\Entity\Like;
use App\Entity\Note;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class LikeButton
{
    public Note $note;
    public ?User $currentUser;

    public function __construct(private Security $security)
    {
        $this->currentUser = $this->security->getUser();
    }

    public function getLikesCount(): int
    {
        return $this->note->getLikes()->count();
    }

    public function hasUserLiked(): bool
    {
        if (!$this->currentUser) {
            return false;
        }
        
        return $this->note  // Notes courante
                        ->getLikes()  // Les likes de la note
                        ->exists(function($key, Like $like) {  // Recherche si l'utilisateur a déjà like la note
            //User qui possède le Like === User courant
            return $like->getCreator() === $this->currentUser;
        });
    }
}