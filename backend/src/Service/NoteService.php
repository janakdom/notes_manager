<?php

namespace App\Service;

use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;

final class NoteService
{
    public function __construct(
        private readonly NoteRepository $noteRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}
}
