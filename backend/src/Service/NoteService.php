<?php

namespace App\Service;

use App\Enum\NotePriority;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;

final class NoteService
{
    public function __construct(
        private readonly NoteRepository $noteRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function findAllByPriority(?NotePriority $priority): array
    {
        if($priority === null ){
            return $this->noteRepository->findAll();
        }

        return $this->noteRepository->findBy(['priority' => $priority]);
    }
}
