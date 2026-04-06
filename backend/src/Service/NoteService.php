<?php

namespace App\Service;

use App\Entity\Note;
use App\Enum\NotePriority;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;

final class NoteService
{
    public function __construct(
        private readonly NoteRepository $noteRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    /**
     * Find all notes by priority
     *
     * @param NotePriority|null $priority
     * @return array
     */
    public function findAllByPriority(?NotePriority $priority): array
    {
        if($priority === null ){
            return $this->noteRepository->findAll();
        }

        return $this->noteRepository->findBy(['priority' => $priority]);
    }

    /**
     * Find note by id
     *
     * @param int $id
     * @return Note|null
     */
    public function findOne(int $id): ?Note
    {
        return $this->noteRepository->find($id);
    }
}
