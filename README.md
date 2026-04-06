# Notes Manager

Jednoduchá webová aplikace pro správu poznámek — vytváření, úprava a filtrování podle priority.

**Backend:** Symfony 8 / PHP 8.4, MariaDB  
**Frontend:** Vue 3 / TypeScript / Vite  
**Infrastruktura:** Docker Compose

## Spuštění

Potřebuješ Docker, Docker Compose a Make.

```bash
make setup   # build, závislosti, databáze, migrace — vše najednou
make up      # spuštění (pokud už byl proveden setup)
make down    # zastavení
```

| Služba      | URL                            |
|-------------|--------------------------------|
| Frontend    | http://localhost:5173           |
| Backend API | http://localhost:8880/api/notes |
| phpMyAdmin  | http://localhost:8881           |

Porty a DB přístupy jdou změnit v `docker/.env`.

Další příkazy lze získat spuštěním: `make` v rootu projektu.

## Designové volby

### Backend

- **Vrstvená architektura** — Controller → Service → Repository → Entity. Každá vrstva má jasnou zodpovědnost.
- **DTO + automatická validace** — `NoteRequestDto` s Symfony `MapRequestPayload` deserializuje a validuje request body automaticky, v controlleru žádný manuální parsing.
- **Enum** — `NotePriority` (`low`, `medium`, `high`) zajišťuje typovou bezpečnost, Doctrine ho persistuje jako string.
- **Globální exception handling** — `ExceptionSubscriber` vrací konzistentní JSON chyby (`validation_failed`, `note_not_found`, `invalid_request`, `internal_error`).

### Frontend

- **Composition API + composable** — veškerá logika (stav, API volání, filtrování) je v `useNotes`. Komponenty jsou čistě prezentační.
- **Oddělená API vrstva** — `notesApi.ts` zapouzdřuje `fetch` volání, komponenty nevolají API přímo.
- **Komponentová struktura** — `BasicLayout` → `NoteListView` → `NotesFilter`, `NoteForm`, `NotesList` → `NoteCard`. 
- Každá komponenta má vlstní scoped CSS, pro jednoduchost nebyl zvolen žádný CSS framework.

### Infrastruktura

4 Docker kontejnery: frontend (Node 24 + Vite dev server), backend (PHP 8.4 built-in server), db (MariaDB 11.8), phpmyadmin.

### API

REST API na `/api/notes`:

- `GET /api/notes` — seznam (volitelně `?priority=low|medium|high`)
- `GET /api/notes/{id}` — detail
- `POST /api/notes` — vytvoření (`title`, `content`, `priority`)
- `PUT /api/notes/{id}` — úprava

Detaily viz [docs/API-CONTRACT.md](docs/API-CONTRACT.md).
