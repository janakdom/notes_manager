<?php

namespace App\Controller;

use App\Dto\NoteRequestDto;
use App\Enum\NotePriority;
use App\Service\NoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $notes = $this->noteService->findAllByPriority($priority);

        return $this->json($notes);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $note = $this->noteService->findOne($id);

        if ($note === null) {
            return $this->json([
                'code' => 'note_not_found',
                'message' => 'Poznámka nebyla nalezena',
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json($note);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        #[MapRequestPayload(acceptFormat: 'json', validationFailedStatusCode: Response::HTTP_BAD_REQUEST)]
        NoteRequestDto $noteRequestDto
    ): JsonResponse
    {
        $note = $this->noteService->create($noteRequestDto);
        return $this->json($note, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->json([]);
    }
}

