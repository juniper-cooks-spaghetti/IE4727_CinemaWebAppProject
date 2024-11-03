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

UPDATE movies
SET poster = 
  CASE movieid
    WHEN 1 THEN 'https://picfiles.alphacoders.com/620/thumb-1920-620269.jpeg'
    WHEN 2 THEN 'https://avatarfiles.alphacoders.com/353/353669.jpg'
    WHEN 3 THEN 'https://picfiles.alphacoders.com/441/441965.jpg'
    WHEN 4 THEN 'https://picfiles.alphacoders.com/451/451058.jpg'
  END;
