import { type FormEvent, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../lib/auth'
import { ApiError } from '../lib/api'

const NAMA_INSTANSI = 'Universitas Pintar Nusantara'
const TAHUN_AJARAN = '2025/2026'

export default function LoginPage() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const [identifier, setIdentifier] = useState('')
  const [password, setPassword] = useState('')
  const [showPw, setShowPw] = useState(false)
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
    <div className="min-h-screen flex bg-white">
      {/* ---------- Panel identitas (kiri) — datar, satu warna ---------- */}
      <div className="hidden lg:flex lg:w-[45%] bg-blue-900 text-white flex-col justify-between px-12 py-10">
        <div className="flex items-center gap-3">
          <div className="w-11 h-11 rounded bg-white flex items-center justify-center">
            <CapIcon className="w-6 h-6 text-blue-900" />
          </div>
          <div className="leading-tight">
            <p className="font-bold">{NAMA_INSTANSI}</p>
            <p className="text-xs text-blue-200">Sistem Ujian Online</p>
          </div>
        </div>

        <div className="max-w-sm">
          <p className="text-xs font-semibold tracking-[0.2em] text-blue-300 uppercase">
            Computer-Based Test
          </p>
          <h1 className="mt-3 text-3xl font-bold leading-snug">
            Ujian Berbasis Komputer
          </h1>
          <p className="mt-4 text-sm text-blue-100/80 leading-relaxed">
            Pelaksanaan ujian yang terjadwal, aman, dan tervalidasi di sisi server.
            Silakan masuk menggunakan akun yang diberikan oleh admin.
          </p>
        </div>

        <p className="text-xs text-blue-300/70 border-t border-white/10 pt-4">
          &copy; {new Date().getFullYear()} {NAMA_INSTANSI} · Tahun Ajaran {TAHUN_AJARAN}
        </p>
      </div>

      {/* ---------- Panel form (kanan) ---------- */}
      <div className="w-full lg:w-[55%] flex items-center justify-center p-6 sm:p-10">
        <div className="w-full max-w-sm">
          {/* Header ringkas untuk layar kecil */}
          <div className="lg:hidden flex items-center gap-3 mb-8">
            <div className="w-10 h-10 rounded bg-blue-900 flex items-center justify-center">
              <CapIcon className="w-5 h-5 text-white" />
            </div>
            <div className="leading-tight">
              <p className="font-bold text-slate-800 text-sm">{NAMA_INSTANSI}</p>
              <p className="text-xs text-slate-500">Sistem Ujian Online</p>
            </div>
          </div>

          <h2 className="text-2xl font-bold text-slate-900">Masuk</h2>
          <p className="text-sm text-slate-500 mt-1 mb-6">
            Gunakan Username/NIM/NIP dan kata sandi Anda.
          </p>

          {error && (
            <div className="mb-5 flex items-start gap-2.5 bg-red-50 border border-red-200 text-red-700 rounded-md px-3.5 py-3">
              <WarnIcon className="w-5 h-5 shrink-0" />
              <span className="text-sm">{error}</span>
            </div>
          )}

          <form onSubmit={onSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">
                Username / NIM / NIP
              </label>
              <input
                value={identifier}
                onChange={(e) => setIdentifier(e.target.value)}
                required
                autoFocus
                className="w-full rounded-md border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none"
                placeholder="mis. 2025001"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
              <div className="relative">
                <input
                  type={showPw ? 'text' : 'password'}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                  className="w-full rounded-md border border-slate-300 px-3.5 py-2.5 pr-10 text-slate-900 placeholder-slate-400 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none"
                  placeholder="Kata sandi"
                />
                <button
                  type="button"
                  onClick={() => setShowPw((v) => !v)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                  tabIndex={-1}
                  aria-label="Tampilkan sandi"
                >
                  {showPw ? <EyeOffIcon className="w-5 h-5" /> : <EyeIcon className="w-5 h-5" />}
                </button>
              </div>
            </div>

            <button
              type="submit"
              disabled={busy}
              className="w-full bg-blue-700 hover:bg-blue-800 disabled:opacity-60 text-white font-semibold py-2.5 rounded-md transition"
            >
              {busy ? 'Memproses…' : 'Masuk'}
            </button>
          </form>

          <div className="mt-6 pt-5 border-t border-slate-200 text-xs text-slate-500 leading-relaxed">
            <span className="font-semibold text-slate-600">Akun uji coba</span> (sandi: password123):
            Mahasiswa <span className="font-mono">2025001</span>, Dosen{' '}
            <span className="font-mono">budi@kampus.ac.id</span>, Admin{' '}
            <span className="font-mono">admin@kampus.ac.id</span>.
          </div>
        </div>
      </div>
    </div>
  )
}

type IconProps = { className?: string }
const CapIcon = (p: IconProps) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.8} stroke="currentColor" className={p.className}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.44 60.44 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.64 50.64 0 0 0-2.658-.813A59.9 59.9 0 0 1 12 3.493a59.9 59.9 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.7 50.7 0 0 1 12 13.489a50.7 50.7 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.4 55.4 0 0 1 12 8.443m-7.007 11.55A5.98 5.98 0 0 0 6.75 15.75v-1.5" />
  </svg>
)
const WarnIcon = (p: IconProps) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.8} stroke="currentColor" className={p.className}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
  </svg>
)
const EyeIcon = (p: IconProps) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.8} stroke="currentColor" className={p.className}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
    <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
  </svg>
)
const EyeOffIcon = (p: IconProps) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.8} stroke="currentColor" className={p.className}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.48 10.48 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.774 3.162 10.066 7.498a10.52 10.52 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
  </svg>
)
