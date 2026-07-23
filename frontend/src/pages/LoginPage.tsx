import { type FormEvent, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../lib/auth'
import { ApiError } from '../lib/api'

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
      {/* ---------- Panel branding (kiri) ---------- */}
      <div className="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-slate-900">
        <div className="absolute inset-0 bg-gradient-to-br from-indigo-700 via-indigo-800 to-slate-900" />
        <div className="absolute -top-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl" />
        <div className="absolute bottom-[-6rem] right-[-4rem] w-96 h-96 bg-violet-500/20 rounded-full blur-3xl" />

        <div className="relative z-10 flex flex-col justify-between p-14 text-white w-full">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur border border-white/20 flex items-center justify-center">
              <CapIcon className="w-7 h-7" />
            </div>
            <span className="text-xl font-black tracking-tight">Ujian Online</span>
          </div>

          <div className="max-w-md">
            <h1 className="text-4xl font-black leading-tight tracking-tight">
              Sistem Ujian Berbasis Komputer
            </h1>
            <p className="mt-4 text-indigo-100/80 leading-relaxed">
              Platform ujian daring untuk lingkungan kampus — cepat, aman, dan tervalidasi
              penuh di sisi server.
            </p>

            <ul className="mt-8 space-y-4">
              <Feature title="Timer akurat" desc="Waktu ujian dihitung & dijaga server, bukan browser." />
              <Feature title="Anti-nyontek" desc="Urutan soal & opsi diacak per peserta." />
              <Feature title="Penilaian otomatis" desc="Skor objektif langsung setelah submit." />
            </ul>
          </div>

          <p className="text-xs text-indigo-200/60">
            &copy; {new Date().getFullYear()} Universitas Pintar Nusantara · Tahun Ajaran 2025/2026
          </p>
        </div>
      </div>

      {/* ---------- Panel form (kanan) ---------- */}
      <div className="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 bg-slate-50">
        <div className="w-full max-w-md">
          {/* Logo ringkas untuk layar kecil */}
          <div className="lg:hidden flex items-center gap-3 mb-8">
            <div className="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center text-white">
              <CapIcon className="w-6 h-6" />
            </div>
            <span className="text-lg font-black text-slate-900">Ujian Online</span>
          </div>

          <div className="mb-8">
            <h2 className="text-3xl font-black text-slate-900 tracking-tight">Selamat Datang</h2>
            <p className="text-slate-500 mt-1.5">Masuk ke akun Anda untuk melanjutkan.</p>
          </div>

          {error && (
            <div className="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl p-3.5">
              <WarnIcon className="w-5 h-5 shrink-0 mt-0.5" />
              <span className="text-sm font-medium">{error}</span>
            </div>
          )}

          <form onSubmit={onSubmit} className="space-y-5">
            <Field label="Email / NIM / NIP">
              <div className="relative">
                <UserIcon className="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                <input
                  value={identifier}
                  onChange={(e) => setIdentifier(e.target.value)}
                  required
                  autoFocus
                  className="w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 py-3 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none transition"
                  placeholder="mis. 2025001"
                />
              </div>
            </Field>

            <Field label="Kata Sandi">
              <div className="relative">
                <LockIcon className="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                <input
                  type={showPw ? 'text' : 'password'}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                  className="w-full rounded-xl border border-slate-300 bg-white pl-11 pr-11 py-3 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none transition"
                  placeholder="••••••••"
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
            </Field>

            <button
              type="submit"
              disabled={busy}
              className="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-600/20 transition active:scale-[0.99]"
            >
              {busy ? 'Memproses…' : 'Masuk ke Dasbor'}
              {!busy && <ArrowIcon className="w-5 h-5" />}
            </button>
          </form>

          <div className="mt-8 text-xs text-slate-500 bg-slate-100 border border-slate-200 rounded-xl p-3.5 leading-relaxed">
            <span className="font-bold text-slate-600">Akun demo (sandi: password123):</span>
            <br />
            Mahasiswa <b>2025001</b> · Dosen <b>budi@kampus.ac.id</b> · Admin{' '}
            <b>admin@kampus.ac.id</b>
          </div>
        </div>
      </div>
    </div>
  )
}

/* ---------- Sub-komponen kecil ---------- */

function Field({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <div>
      <label className="block text-sm font-bold text-slate-700 mb-1.5">{label}</label>
      {children}
    </div>
  )
}

function Feature({ title, desc }: { title: string; desc: string }) {
  return (
    <li className="flex items-start gap-3">
      <div className="mt-0.5 w-6 h-6 rounded-full bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
        <CheckIcon className="w-3.5 h-3.5" />
      </div>
      <div>
        <p className="font-bold text-white">{title}</p>
        <p className="text-sm text-indigo-100/70">{desc}</p>
      </div>
    </li>
  )
}

/* ---------- Ikon (SVG inline, tanpa dependensi) ---------- */

type IconProps = { className?: string }
const S = (p: IconProps & { children: React.ReactNode }) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.8} stroke="currentColor" className={p.className}>
    {p.children}
  </svg>
)
const CapIcon = (p: IconProps) => (
  <S {...p}><path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.44 60.44 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.64 50.64 0 0 0-2.658-.813A59.9 59.9 0 0 1 12 3.493a59.9 59.9 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.7 50.7 0 0 1 12 13.489a50.7 50.7 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.4 55.4 0 0 1 12 8.443m-7.007 11.55A5.98 5.98 0 0 0 6.75 15.75v-1.5" /></S>
)
const UserIcon = (p: IconProps) => (
  <S {...p}><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" /></S>
)
const LockIcon = (p: IconProps) => (
  <S {...p}><path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></S>
)
const ArrowIcon = (p: IconProps) => (
  <S {...p}><path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></S>
)
const CheckIcon = (p: IconProps) => (
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={3} className={p.className}><path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
)
const WarnIcon = (p: IconProps) => (
  <S {...p}><path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></S>
)
const EyeIcon = (p: IconProps) => (
  <S {...p}><path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></S>
)
const EyeOffIcon = (p: IconProps) => (
  <S {...p}><path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.48 10.48 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.774 3.162 10.066 7.498a10.52 10.52 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></S>
)
