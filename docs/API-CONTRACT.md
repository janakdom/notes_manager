# Notes API Reference

## Bse URL
`/api`

## Content Type
`application/json`

## Model

### Note

```json
{
  "id": 1,
  "title": "Poznámka 1",
  "content": "Popis poznámky 2.",
  "priority": "medium",
  "createdAt": "2026-04-01T10:00:00Z",
  "updatedAt": "2026-04-01T10:15:00Z"
}
```

### Priority values
- `low`
- `medium`
- `high`

## Error response format

All error responses use the same structure

```json
{
  "code": "validation_failed",
  "message": "Validace selhala.",
  "fields": {
    "title": [
      "Název nesmí být prázdný."
    ]
  }
}
```

### Error codes

- `invalid_request`
- `invalid_priority_filter`
- `validation_failed`
- `note_not_found`
- `internal_error`

## Endpoints

### `GET /notes`
---

Return all notes

#### Query parameters

| name     | type   | Required | allowed vals            |
| ---------| ------ | --------- | ----------------------- |
| priority | string | no        | `low`, `medium`, `high` |

#### Success response

Status: `200`

```json
[
  {
    "id": 1,
    "title": "Poznámka 1",
    "content": "Popis poznámky 1.",
    "priority": "low",
    "createdAt": "2026-04-06T7:00:00Z",
    "updatedAt": "2026-04-06T7:00:00Z"
  },
  {
    "id": 2,
    "title": "Poznámka 2",
    "content": "Popis poznámky 2.",
    "priority": "medium",
    "createdAt": "2026-04-06T7:30:00Z",
    "updatedAt": "2026-04-06T7:30:00Z"
  }
]
```

#### Error responses

Status: `400 Bad Request`

```json
{
    "code": "invalid_priority_filter",
    "message": "Priorita musí být jedna z těchto hodnot: nízká, střední, vysoká"
}
```

Status: `500 Internal Server error`

```json
{
    "code": "internal_error",
    "message": "Vyskytla se neočekávaná chyba."
}
```

### `GET /notes/{id}`
---

Return one note

#### URL Parameters

| name | type    | Required |
| -----| ------- | -------- |
| id   | integer | yes      |

#### Success response

Status: `200 Ok`

```json
{
    "id": 1,
    "title": "Poznámka 1",
    "content": "Popis poznámky 1.",
    "priority": "low",
    "createdAt": "2026-04-06T7:00:00Z",
    "updatedAt": "2026-04-06T7:00:00Z"
  }
```

#### Error response

Status: `404 Not Found`

```json
{
    "code": "note_not_found",
    "message": "Poznámka nebyla nalezena."
}
```

Status: `500 Internal Server error`

```json
{
    "code": "internal_error",
    "message": "Vyskytla se neočekávaná chyba."
}
```

### `POST /notes`
---

Create a new note

#### Request body

| name     | type   | Required | Conditions        |
| -------- | ------ | -------- | ----------------- |
| title    | string | yes      | max: 255          |
| content  | string | yes      | not blank         |
| priority | string | yes      | low, medium, high |

#### Request body example

```json 
{
    "title": "Poznámka 3",
    "content": "Popis poznámky 3.",
    "priority": "high",
}
```

#### Success response

Status: `201 Created`

```json
{
    "id": 3,
    "title": "Poznámka 3",
    "content": "Popis poznámky 3.",
    "priority": "high",
    "createdAt": "2026-04-06T8:00:00Z",
    "updatedAt": "2026-04-06T8:00:00Z"
  }
```

#### Error responses

Status: `400 Bad Request`

```json
{
    "code": "invalid_request",
    "message": "Neplatný požadavek."
}
```

Status: `400 Bad Request`

```json
{
    "code": "validation_failed",
    "message": "Validace selhala.",
    "fields": {
        "title": [
            "Název nesmí být prázdný."
        ],
        "content": [
            "Obsah nesmí být prázdný."
        ],
        "priority": [
            "Priorita musí být jedna z těchto hodnot: nízká, střední, vysoká."
        ]
    }
}
```

Status: `500 Internal Server error`

```json
{
    "code": "internal_error",
    "message": "Vyskytla se neočekávaná chyba."
}
```


### `PUT /notes`
---

Updates an existing note

#### URL Parameters

| name | type    | Required |
| -----| ------- | -------- |
| id   | integer | yes      |

#### Request body

| name     | type   | Required | Conditions        |
| -------- | ------ | -------- | ----------------- |
| title    | string | yes      | max: 255          |
| content  | string | yes      | not blank         |
| priority | string | yes      | low, medium, high |

#### Request body example

```json 
{
    "title": "Poznámka 3",
    "content": "Nový popis poznámky 3.",
    "priority": "high",
}
```

#### Success response

Status: `200 Ok`

```json
{
    "id": 3,
    "title": "Poznámka 3",
    "content": "Popis poznámky 3.",
    "priority": "high",
    "createdAt": "2026-04-06T8:00:00Z",
    "updatedAt": "2026-04-06T8:00:00Z"
  }
```

#### Error responses

Status: `400 Bad Request`

```json
{
    "code": "invalid_request",
    "message": "Neplatný požadavek."
}
```

Status: `404 Not Found`

```json
{
    "code": "note_not_found",
    "message": "Poznámka nebyla nalezena."
}
```

Status: `400 Bad Request`

```json
{
    "code": "validation_failed",
    "message": "Validace selhala.",
    "fields": {
        "title": [
            "Název nesmí být prázdný."
        ],
        "content": [
            "Obsah nesmí být prázdný."
        ],
        "priority": [
            "Priorita musí být jedna z těchto hodnot: nízká, střední, vysoká."
        ]
    }
}
```

Status: `500 Internal Server error`

```json
{
    "code": "internal_error",
    "message": "Vyskytla se neočekávaná chyba."
}
```

## Validation rules

### Title    

- required
- string
- max 255 chars

### Content

- required
- string
- must not be blank

### Priority
- required
- sting
- allowed values: low, medium, high

## Data types

### Dates
- all dates are returned in UTC (by ISO)
