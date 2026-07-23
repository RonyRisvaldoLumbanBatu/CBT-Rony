// Context autentikasi: menyimpan user aktif + token, menyediakan login/logout,
// dan memuat profil (/me) otomatis saat aplikasi dibuka bila token masih ada.

import { createContext, useContext, useEffect, useState, type ReactNode } from 'react'
import {
  clearTokens,
  fetchMe,
  getAccessToken,
  loginRequest,
  setTokens,
  type UserPublic,
} from './api'

interface AuthContextType {
  user: UserPublic | null
  loading: boolean
  login: (identifier: string, password: string) => Promise<void>
  logout: () => void
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<UserPublic | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    if (getAccessToken()) {
      fetchMe()
        .then(setUser)
        .catch(() => clearTokens())
        .finally(() => setLoading(false))
    } else {
      setLoading(false)
    }
  }, [])

  async function login(identifier: string, password: string) {
    const res = await loginRequest(identifier, password)
    setTokens(res.access_token, res.refresh_token)
    setUser(res.user)
  }

  function logout() {
    clearTokens()
    setUser(null)
  }

  return (
    <AuthContext.Provider value={{ user, loading, login, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth harus dipakai di dalam AuthProvider')
  return ctx
}
