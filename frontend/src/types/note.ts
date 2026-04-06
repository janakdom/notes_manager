export type NotePriority = 'low' | 'medium' | 'high'

export interface Note {
	id: number
	title: string
	content: string
	priority: NotePriority
}
