import { useAuth } from '../lib/auth'

const NAMA_INSTANSI = 'Universitas Pintar Nusantara'

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
    .replace(/^(Dr\.?|Prof\.?|Ir\.?|S\.?Kom\.?)\s*/i, '')
    .split(' ')
    .slice(0, 2)
    .map((s) => s[0])
    .join('')
    .toUpperCase()

  return (
    <div className="min-h-screen bg-slate-100">
      {/* Header */}
      <header className="bg-white border-b border-slate-200">
        <div className="max-w-5xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded bg-blue-900 flex items-center justify-center">
              <CapIcon className="w-4.5 h-4.5 text-white" />
            </div>
            <div className="leading-tight">
              <p className="text-sm font-bold text-slate-800">{NAMA_INSTANSI}</p>
              <p className="text-[11px] text-slate-500 -mt-0.5">Sistem Ujian Online</p>
            </div>
          </div>
          <div className="flex items-center gap-3">
            <div className="hidden sm:flex items-center gap-2">
              <div className="w-7 h-7 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[11px] font-bold">
                {initials}
              </div>
              <span className="text-sm text-slate-700">{user.nama}</span>
            </div>
            <button
              onClick={logout}
              className="text-sm text-slate-500 hover:text-slate-800 border border-slate-300 rounded-md px-3 py-1.5 hover:bg-slate-50 transition"
            >
              Keluar
            </button>
          </div>
        </div>
      </header>

      <main className="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <div className="flex items-center justify-between flex-wrap gap-2">
          <div>
            <h1 className="text-xl font-bold text-slate-900">
              Selamat datang, {user.nama}
            </h1>
            <p className="text-sm text-slate-500 mt-0.5">
              Anda masuk sebagai <span className="font-semibold text-slate-700">{roleLabel[user.role] ?? user.role}</span>.
            </p>
          </div>
        </div>

        {/* Info akun */}
        <div className="mt-6 bg-white border border-slate-200 rounded-lg">
          <div className="px-5 py-3 border-b border-slate-200">
            <h2 className="text-sm font-bold text-slate-700">Informasi Akun</h2>
          </div>
          <dl className="divide-y divide-slate-100">
            <Row label="Nama Lengkap" value={user.nama} />
            <Row label="Peran" value={roleLabel[user.role] ?? user.role} />
            <Row label="Email" value={user.email} />
            <Row label="NIM / NIP" value={user.nim_nip ?? '-'} />
          </dl>
        </div>

        {/* Placeholder area fitur */}
        <div className="mt-6 border border-dashed border-slate-300 rounded-lg bg-white px-6 py-10 text-center">
          <p className="text-sm font-semibold text-slate-700">
            {user.role === 'mahasiswa'
              ? 'Daftar ujian yang tersedia akan tampil di sini.'
              : 'Menu kelola ujian & bank soal akan tampil di sini.'}
          </p>
          <p className="text-sm text-slate-400 mt-1">
            Alur ujian (buat soal, kerjakan dengan timer, penilaian otomatis) sedang dikerjakan pada Fase 1.
          </p>
        </div>
      </main>
    </div>
  )
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <div className="px-5 py-3 grid grid-cols-3 gap-4">
      <dt className="text-sm text-slate-500">{label}</dt>
      <dd className="col-span-2 text-sm font-medium text-slate-800 break-words">{value}</dd>
    </div>
  )
}

const CapIcon = (p: { className?: string }) => (
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.8} stroke="currentColor" className={p.className}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.44 60.44 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.64 50.64 0 0 0-2.658-.813A59.9 59.9 0 0 1 12 3.493a59.9 59.9 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.7 50.7 0 0 1 12 13.489a50.7 50.7 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.4 55.4 0 0 1 12 8.443m-7.007 11.55A5.98 5.98 0 0 0 6.75 15.75v-1.5" />
  </svg>
)
