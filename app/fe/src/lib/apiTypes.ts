// nullable fields on purpose so that we can catch validation errors from API
// instead of mimicking the same kind of validation here
export type Answer = {
  job_id: number
  email?: string
  name?: string
  phone?: string
  cover_letter?: string
}

export type Pagination = {
  entries_from: number
  entries_to: number
  entries_total: number
  entries_sum: number
}

export type ApiResponse<T> = {
  payload: T
  meta: {
    code: string
    duration: number
    message: string
  } & Pagination
}

export type Job = {
  job_id: number
  active: boolean
  title: string
  description: string
  salary: {
    is_range: boolean
    is_min_visible: boolean
    is_max_visible: boolean
    min: number
    max: number
    currency: 'CZK' | 'USD' | 'EUR'
    unit: 'month' | 'hour' | 'manday'
    visible: boolean
    note: string
  }
  employment: {
    name: string
  }
}
