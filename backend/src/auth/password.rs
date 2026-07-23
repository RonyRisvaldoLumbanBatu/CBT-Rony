//! Hashing & verifikasi password dengan Argon2 (argon2id).
//!
//! Kita TIDAK pernah menyimpan password asli — hanya hash-nya. Salt acak
//! dibuat per password, dan tertanam di dalam string hash PHC yang disimpan.

use crate::error::{AppError, AppResult};
use argon2::{
    password_hash::{rand_core::OsRng, PasswordHash, PasswordHasher, PasswordVerifier, SaltString},
    Argon2,
};

/// Hash password plaintext menjadi string PHC untuk disimpan di DB.
pub fn hash_password(plain: &str) -> AppResult<String> {
    let salt = SaltString::generate(&mut OsRng);
    let hash = Argon2::default()
        .hash_password(plain.as_bytes(), &salt)
        .map_err(|e| AppError::Internal(format!("gagal hash password: {e}")))?
        .to_string();
    Ok(hash)
}

/// Verifikasi password plaintext terhadap hash tersimpan. `false` bila salah.
pub fn verify_password(plain: &str, hash: &str) -> bool {
    match PasswordHash::new(hash) {
        Ok(parsed) => Argon2::default()
            .verify_password(plain.as_bytes(), &parsed)
            .is_ok(),
        Err(_) => false,
    }
}
