// Pembungkus fetch tipis: menempelkan token, parsing JSON, dan melempar
// ApiError yang rapi saat status non-2xx. Path relatif (mis. /api/auth/login)
// diteruskan ke backend lewat proxy Vite.

const ACCESS_KEY = 'access_token'
const REFRESH_KEY = 'refresh_token'

export function getAccessToken(): string | null {
  return localStorage.getItem(ACCESS_KEY)
}

export function setTokens(access: string, refresh: string) {
  localStorage.setItem(ACCESS_KEY, access)
  localStorage.setItem(REFRESH_KEY, refresh)
}

export function clearTokens() {
  localStorage.removeItem(ACCESS_KEY)
  localStorage.removeItem(REFRESH_KEY)
}

export interface UserPublic {
  id: string
  role: string
  nama: string
  email: string
  nim_nip: string | null
}

export interface TokenPair {
  access_token: string
  refresh_token: string
  user: UserPublic
}

export class ApiError extends Error {
  status: number
  constructor(status: number, message: string) {
    super(message)
    this.status = status
  }
}

export async function apiFetch<T>(path: string, options: RequestInit = {}): Promise<T> {
  const headers = new Headers(options.headers)
  headers.set('Content-Type', 'application/json')
  const token = getAccessToken()
  if (token) headers.set('Authorization', `Bearer ${token}`)

  const res = await fetch(path, { ...options, headers })
  const text = await res.text()
  const data = text ? JSON.parse(text) : null

  if (!res.ok) {
    const message = (data && data.error) || 'Terjadi kesalahan'
    throw new ApiError(res.status, message)
  }
  return data as T
}

export function loginRequest(identifier: string, password: string) {
  return apiFetch<TokenPair>('/api/auth/login', {
    method: 'POST',
    body: JSON.stringify({ identifier, password }),
  })
}

export function fetchMe() {
  return apiFetch<UserPublic>('/api/auth/me')
}
