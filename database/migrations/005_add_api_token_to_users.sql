ALTER TABLE users
    ADD COLUMN IF NOT EXISTS api_token VARCHAR(64) DEFAULT NULL AFTER password;

CREATE INDEX idx_users_api_token ON users(api_token);
