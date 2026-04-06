<?php

namespace App\Service;

use App\Dto\NoteRequestDto;
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

    /**
     * Create a new note
     *
     * @param NoteRequestDto $noteDto
     * @return Note
     */
    public function create(NoteRequestDto $noteDto): Note
    {
        $newNote = new Note();
        $this->assignDtoToEntity($noteDto, $newNote);

        $this->entityManager->persist($newNote);
        $this->entityManager->flush();

        return $newNote;
    }

    /**
     * Assign data from DTO to an entity
     *
     * @param NoteRequestDto $sourceDto
     * @param Note $targetEntity
     * @return void
     */
    public function assignDtoToEntity(NoteRequestDto $sourceDto, Note $targetEntity): void
    {
        $targetEntity->setTitle($sourceDto->title);
        $targetEntity->setContent($sourceDto->content);
        $targetEntity->setPriority(NotePriority::from($sourceDto->priority));
    }
}
