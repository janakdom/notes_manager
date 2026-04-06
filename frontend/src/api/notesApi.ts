import type { NotePriority, Note } from '../types/note'

interface ApiErrorResponse {
	message?: string
}

/**
 * Builds the URL for fetching notes from the API,
 */
function buildNotesUrl(): string {
	const baseUrl = import.meta.env.VITE_API_BASE_URL?.trim()
	return `${baseUrl.replace(/\/$/, '')}/api/notes`
}


/**
 * Fetches notes from the API.
 */
export async function fetchNotes(priority?: NotePriority): Promise<Note[]> {
	const response = await fetch(buildNotesUrl() + (priority ? `?priority=${priority}` : ''), {
		headers: {
			Accept: 'application/json',
		},
	})

	if (!response.ok) {
		const errorBody = await response.json().catch(() => null) as ApiErrorResponse | null

		throw new Error(errorBody?.message ?? 'Nepidařilo se načíst poznámky.')
	}

	return response.json() as Promise<Note[]>
}
