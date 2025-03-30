import type { Answer, ApiResponse, Job } from '@/lib/apiTypes.ts'

export async function createAnswer(answer: Answer): Promise<{
  statusCode: number
  response: () => Promise<ApiResponse<{ id: string }>>
}> {
  const result = await fetch(baseUri() + `/api/answers`, {
    method: 'POST',
    body: JSON.stringify(answer),
  })

  return {
    statusCode: result.status,
    response: async () => (await result.json()) as unknown as ApiResponse<{ id: string }>,
  }
}

export function fetchJobs(
  limit: number,
  page: number,
): [() => void, () => Promise<ApiResponse<Job[]>>] {
  const controller = new AbortController()
  const fetchPromise = fetch(baseUri() + `/api/jobs?limit=${limit}&page=${page}`, {
    signal: controller.signal,
  })

  return [
    () => controller.abort(),
    async () => (await (await fetchPromise).json()) as unknown as ApiResponse<Job[]>,
  ]
}

export async function fetchJob(id: string): Promise<ApiResponse<Job>> {
  const fetchPromise = await fetch(baseUri() + `/api/jobs/${id}`)
  return (await fetchPromise.json()) as unknown as ApiResponse<Job>
}

function baseUri(): string {
  return import.meta.env.VITE_API_BASE_URI
}
