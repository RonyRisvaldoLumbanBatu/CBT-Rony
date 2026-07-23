import { type FormEvent, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../lib/auth'
import { ApiError } from '../lib/api'

export default function LoginPage() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const [identifier, setIdentifier] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [busy, setBusy] = useState(false)

  async function onSubmit(e: FormEvent) {
    e.preventDefault()
    setError(null)
    setBusy(true)
    try {
      await login(identifier, password)
      navigate('/dashboard')
    } catch (err) {
      setError(err instanceof ApiError ? err.message : 'Gagal terhubung ke server')
    } finally {
      setBusy(false)
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-100 p-4">
      <div className="w-full max-w-sm bg-white rounded-2xl shadow-lg border border-slate-200 p-8">
        <h1 className="text-2xl font-black text-slate-900 mb-1">Ujian Online</h1>
        <p className="text-sm text-slate-500 mb-6">Masuk untuk melanjutkan.</p>

        {error && (
          <div className="mb-4 text-sm bg-red-50 text-red-700 border border-red-200 rounded-lg p-3">
            {error}
          </div>
        )}

        <form onSubmit={onSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-semibold text-slate-700 mb-1">
              Email / NIM / NIP
            </label>
            <input
              value={identifier}
              onChange={(e) => setIdentifier(e.target.value)}
              required
              autoFocus
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/30 outline-none"
              placeholder="mis. 2025001"
            />
          </div>
          <div>
            <label className="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi</label>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              className="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/30 outline-none"
              placeholder="••••••••"
            />
          </div>
          <button
            type="submit"
            disabled={busy}
            className="w-full bg-teal-600 hover:bg-teal-700 disabled:opacity-60 text-white font-bold py-2.5 rounded-lg transition"
          >
            {busy ? 'Memproses…' : 'Masuk'}
          </button>
        </form>

        <p className="mt-6 text-xs text-slate-400 text-center">
          Contoh: NIM <b>2025001</b> / dosen <b>budi@kampus.ac.id</b> — sandi <b>password123</b>
        </p>
      </div>
    </div>
  )
}
