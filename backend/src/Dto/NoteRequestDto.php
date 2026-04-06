<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class NoteRequestDto
{
    #[Assert\NotBlank(message: 'Název nesmí být prázdný.')]
    #[Assert\Length(max: 255, maxMessage: 'Název nesmí být delší než 255 znaků.')]
    public string $title = '';

    #[Assert\NotBlank(message: 'Obsah nesmí být prázdný.')]
    public string $content = '';

    // #[Assert\NotBlank(message: 'Priorita musí být jedna z těchto hodnot: nízká, střední, vysoká.')]
    #[Assert\Choice(
        choices: ['low', 'medium', 'high'],
        message: 'Priorita musí být jedna z těchto hodnot: nízká, střední, vysoká.',
    )]
    public string $priority = 'low';
}
