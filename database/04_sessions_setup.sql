-- Add remember token columns to Users table
ALTER TABLE Users
ADD COLUMN RememberToken VARCHAR(64) NULL,
ADD COLUMN TokenCreatedAt TIMESTAMP NULL,
ADD COLUMN LastLoginDate TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD INDEX idx_remember_token (RememberToken),
ADD INDEX idx_token_created (TokenCreatedAt);

-- Update existing users
UPDATE Users 
SET LastLoginDate = CURRENT_TIMESTAMP 
WHERE LastLoginDate IS NULL;