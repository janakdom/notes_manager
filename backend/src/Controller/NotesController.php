<?php

namespace App\Controller;

use App\Enum\NotePriority;
use App\Service\NoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/notes', name: 'api_notes_', format: 'json')]
final class NotesController extends AbstractController
{
    public function __construct(
        private readonly NoteService $noteService,
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $priority = null;

        $priorityParam = $request->query->get('priority');
        if(!empty($priorityParam)) {
            $priority = NotePriority::tryFrom($priorityParam);
            if(!$priority) {
                return $this->json([
                    'code' => 'invalid_priority_filter',
                    'message' => 'Invalid priority',
                ], 400);
            }
        }

        $notes = $this->noteService->findAllByPriority($priority);

        return $this->json($notes);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->json([]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->json([]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->json([]);
    }
}

