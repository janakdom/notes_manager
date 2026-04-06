import type { NotePriority, Note } from '../types/note'

interface ApiErrorResponse {
	message?: string
}

export interface NotePayload {
	title: string
	content: string
	priority: NotePriority
}

/**
 * Base URL for the API
 */
const baseUrl = () =>
	`${import.meta.env.VITE_API_BASE_URL?.trim().replace(/\/$/, '')}/api/notes`


/**
 * Headers for JSON requests
 */
const jsonHeaders = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
}


/**
 * Parses the response from the API and throws an error if the response is not successful
 * @param response
 * @param errorMessage
 */
async function parseResponse<T>(response: Response, errorMessage: string): Promise<T> {
	if (!response.ok) {
		const body = await response.json().catch(() => null) as ApiErrorResponse | null
		throw new Error(body?.message ?? errorMessage)
	}
	return response.json() as Promise<T>
}

/**
 * Fetches notes from the API.
 */
export async function fetchNotes(priority?: NotePriority): Promise<Note[]> {
	const url = baseUrl() + (priority ? `?priority=${priority}` : '')
	const response = await fetch(url, { headers: { Accept: 'application/json' } })
	return parseResponse<Note[]>(response, 'Nepodařilo se načíst poznámky.')
}

/**
 * Creates a new note
 * @param data
 */
export async function createNote(data: NotePayload): Promise<Note> {
	const response = await fetch(baseUrl(), {
		method: 'POST',
		headers: jsonHeaders,
		body: JSON.stringify(data),
	})
	return parseResponse<Note>(response, 'Nepodařilo se vytvořit poznámku.')
}

/**
 * Updates an existing note
 * @param id
 * @param data
 */
export async function updateNote(id: number, data: NotePayload): Promise<Note> {
	const response = await fetch(`${baseUrl()}/${id}`, {
		method: 'PUT',
		headers: jsonHeaders,
		body: JSON.stringify(data),
	})
	return parseResponse<Note>(response, 'Nepodařilo se uložit poznámku.')
}
