<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Import siswa massal dari Excel (dipakai admin).
 * Transaction penuh: satu baris rusak = seluruh import dibatalkan.
 */
class SiswaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            // Lookup kelas sekali di awal (case-insensitive by name)
            $classrooms = Classroom::all()->keyBy(fn ($c) => mb_strtolower($c->name));

            foreach ($rows as $index => $row) {
                // Abaikan baris kosong
                if (empty($row['nama']) && empty($row['username'])) {
                    continue;
                }

                $baris = $index + 2; // +2: heading di baris 1

                $username = strtolower(trim((string) ($row['username'] ?? '')));
                $password = trim((string) ($row['password'] ?? ''));
                $namaKelas = trim((string) ($row['kelas'] ?? ''));

                if (empty($row['nama']) || $username === '') {
                    $this->gagal($baris, 'kolom nama dan username wajib diisi');
                }

                if (strlen($password) < 8) {
                    $this->gagal($baris, 'password minimal 8 karakter');
                }

                if (User::where('username', $username)->exists()) {
                    $this->gagal($baris, "username '{$username}' sudah dipakai");
                }

                $classroom = null;
                if ($namaKelas !== '') {
                    $classroom = $classrooms->get(mb_strtolower($namaKelas));
                    if (! $classroom) {
                        $this->gagal($baris, "kelas '{$namaKelas}' tidak ditemukan. Buat dulu di menu Kelas & Jurusan");
                    }
                }

                $email = strtolower(trim((string) ($row['email'] ?? '')));
                if ($email === '') {
                    $email = $username.'@cbt.local'; // siswa tanpa email tetap bisa dibuat
                }

                if (User::where('email', $email)->exists()) {
                    $this->gagal($baris, "email '{$email}' sudah dipakai");
                }

                $user = new User([
                    'name' => trim((string) $row['nama']),
                    'username' => $username,
                    'nis' => trim((string) ($row['nis'] ?? '')) ?: null,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'plain_password' => $password, // untuk kartu peserta
                    'role' => 'siswa',
                    'classroom_id' => $classroom?->id,
                ]);
                $user->email_verified_at = now();
                $user->save();
            }
        });
    }

    private function gagal(int $baris, string $alasan): never
    {
        throw ValidationException::withMessages([
            'importFile' => "Baris {$baris} gagal: {$alasan}. Tidak ada siswa yang diimport.",
        ]);
    }
}
