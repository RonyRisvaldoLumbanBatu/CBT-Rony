import { Navigate, Route, Routes } from 'react-router-dom'
import { type ReactNode } from 'react'
import { useAuth } from './lib/auth'
import LoginPage from './pages/LoginPage'
import DashboardPage from './pages/DashboardPage'

// Bungkus rute yang butuh login. Kalau belum login -> lempar ke /login.
function Protected({ children }: { children: ReactNode }) {
  const { user, loading } = useAuth()
  if (loading) return <div className="min-h-screen grid place-items-center text-slate-500">Memuat…</div>
  if (!user) return <Navigate to="/login" replace />
  return <>{children}</>
}

export default function App() {
  const { user } = useAuth()
  return (
    <Routes>
      <Route
        path="/login"
        element={user ? <Navigate to="/dashboard" replace /> : <LoginPage />}
      />
      <Route
        path="/dashboard"
        element={
          <Protected>
            <DashboardPage />
          </Protected>
        }
      />
      <Route path="*" element={<Navigate to="/dashboard" replace />} />
    </Routes>
  )
}
