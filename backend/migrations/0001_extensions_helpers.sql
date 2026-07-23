-- Bagian 0 — Extensions & helper
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Mengisi updated_at otomatis (dipakai via trigger di migrasi 0008)
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
