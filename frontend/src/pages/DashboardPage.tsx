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

  return (
    <div className="min-h-screen bg-slate-100">
      <header className="bg-white border-b border-slate-200">
        <div className="max-w-4xl mx-auto px-6 h-16 flex items-center justify-between">
          <span className="font-black text-slate-900">Ujian Online</span>
          <button
            onClick={logout}
            className="text-sm font-semibold text-slate-600 hover:text-red-600 transition"
          >
            Keluar
          </button>
        </div>
      </header>

      <main className="max-w-4xl mx-auto px-6 py-10">
        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
          <p className="text-sm text-slate-500">Selamat datang,</p>
          <h1 className="text-2xl font-black text-slate-900">{user.nama}</h1>
          <div className="mt-4 inline-flex items-center gap-2 bg-teal-50 text-teal-700 px-3 py-1 rounded-full text-sm font-bold">
            {roleLabel[user.role] ?? user.role}
          </div>

          <dl className="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
              <dt className="text-slate-500">Email</dt>
              <dd className="font-semibold text-slate-800">{user.email}</dd>
            </div>
            <div>
              <dt className="text-slate-500">NIM / NIP</dt>
              <dd className="font-semibold text-slate-800">{user.nim_nip ?? '-'}</dd>
            </div>
          </dl>

          <p className="mt-8 text-xs text-slate-400">
            Placeholder dashboard — alur ujian (buat soal, kerjakan, nilai) menyusul di Fase 1.
          </p>
        </div>
      </main>
    </div>
  )
}
