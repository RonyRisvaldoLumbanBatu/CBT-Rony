import { useAuth } from '../lib/auth'

const roleLabel: Record<string, string> = {
  superadmin: 'Super Admin',
  admin: 'Admin',
  dosen: 'Dosen',
  mahasiswa: 'Mahasiswa',
}

export default function DashboardPage() {
  const { user, logout } = useAuth()
  if (!user) return null

  const initials = user.nama
    .split(' ')
    .slice(0, 2)
    .map((s) => s[0])
    .join('')
    .toUpperCase()

  return (
    <div className="min-h-screen bg-slate-100">
      {/* Navbar */}
      <header className="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div className="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <div className="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
              <CapIcon className="w-5 h-5" />
            </div>
            <span className="font-black text-slate-900">Ujian Online</span>
          </div>
          <div className="flex items-center gap-3">
            <div className="hidden sm:flex items-center gap-2.5">
              <div className="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-black">
                {initials}
              </div>
              <span className="text-sm font-semibold text-slate-700">{user.nama}</span>
            </div>
            <button
              onClick={logout}
              className="text-sm font-semibold text-slate-500 hover:text-red-600 transition px-3 py-1.5 rounded-lg hover:bg-red-50"
            >
              Keluar
            </button>
          </div>
        </div>
      </header>

      <main className="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        {/* Kartu sambutan */}
        <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 to-indigo-800 p-6 sm:p-8 text-white shadow-lg shadow-indigo-600/20">
          <div className="absolute -top-16 -right-10 w-56 h-56 bg-white/10 rounded-full blur-2xl" />
          <div className="relative">
            <span className="inline-flex items-center gap-1.5 bg-white/15 border border-white/20 px-3 py-1 rounded-full text-xs font-bold">
              {roleLabel[user.role] ?? user.role}
            </span>
            <h1 className="mt-3 text-2xl sm:text-3xl font-black tracking-tight">
              Halo, {user.nama.split(' ')[0]} 👋
            </h1>
            <p className="mt-1.5 text-indigo-100/80 text-sm">
              Selamat datang kembali di sistem ujian online.
            </p>
          </div>
        </div>

        {/* Info akun */}
        <div className="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
          <InfoCard label="Nama Lengkap" value={user.nama} />
          <InfoCard label="Email" value={user.email} />
          <InfoCard label="NIM / NIP" value={user.nim_nip ?? '-'} />
        </div>

        {/* Placeholder area fitur */}
        <div className="mt-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center">
          <div className="mx-auto w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-3">
            <ClipboardIcon className="w-7 h-7" />
          </div>
          <p className="font-bold text-slate-800">
            {user.role === 'mahasiswa' ? 'Daftar ujian akan tampil di sini' : 'Kelola ujian & soal akan tampil di sini'}
          </p>
          <p className="text-sm text-slate-400 mt-1">
            Alur ujian (buat soal, kerjakan dengan timer, penilaian) sedang dikerjakan di Fase 1.
          </p>
        </div>
      </main>
    </div>
  )
}

function InfoCard({ label, value }: { label: string; value: string }) {
  return (
    <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
      <p className="text-xs font-semibold text-slate-400 uppercase tracking-wide">{label}</p>
      <p className="mt-1 font-bold text-slate-800 truncate">{value}</p>
    </div>
  )
}

type IconProps = { className?: string }
const CapIcon = (p: IconProps) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.8} stroke="currentColor" className={p.className}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.44 60.44 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.64 50.64 0 0 0-2.658-.813A59.9 59.9 0 0 1 12 3.493a59.9 59.9 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.7 50.7 0 0 1 12 13.489a50.7 50.7 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.4 55.4 0 0 1 12 8.443m-7.007 11.55A5.98 5.98 0 0 0 6.75 15.75v-1.5" />
  </svg>
)
const ClipboardIcon = (p: IconProps) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.6} stroke="currentColor" className={p.className}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.42 48.42 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.25 2.25 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
  </svg>
)
