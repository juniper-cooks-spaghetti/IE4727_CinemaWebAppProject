-- Create database
CREATE DATABASE IF NOT EXISTS cinebox;
USE cinebox;

-- Set character set
SET NAMES utf8mb4;
SET time_zone = "+00:00";

-- Create Users table
CREATE TABLE Users (
    UserID int(11) NOT NULL AUTO_INCREMENT,
    Username varchar(50) NOT NULL,
    Email varchar(255) NOT NULL,
    PasswordHash varchar(255) NOT NULL,
    PhoneNumber varchar(20) DEFAULT NULL,
    CreatedAt timestamp NOT NULL DEFAULT current_timestamp(),
    UpdatedAt timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    RememberToken varchar(64) DEFAULT NULL,
    TokenCreatedAt timestamp NULL DEFAULT NULL,
    LastLoginDate timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (UserID),
    UNIQUE KEY unq_username (Username),
    UNIQUE KEY unq_email (Email),
    KEY idx_email (Email),
    KEY idx_remember_token (RememberToken),
    KEY idx_token_created (TokenCreatedAt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Cinemas table
CREATE TABLE Cinemas (
    CinemaID int(11) NOT NULL AUTO_INCREMENT,
    Name varchar(255) NOT NULL,
    Address text DEFAULT NULL,
    PRIMARY KEY (CinemaID),
    KEY idx_name (Name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Movies table
CREATE TABLE Movies (
    MovieID int(11) NOT NULL AUTO_INCREMENT,
    Title varchar(255) NOT NULL,
    Genre varchar(100) DEFAULT NULL,
    Duration int(11) DEFAULT NULL COMMENT 'Duration in minutes',
    Synopsis text DEFAULT NULL,
    ReleaseDate date DEFAULT NULL,
    Thumbnail text DEFAULT NULL,
    Poster text DEFAULT NULL,
    Featured tinyint(1) DEFAULT 0,
    PRIMARY KEY (MovieID),
    KEY idx_title (Title),
    KEY idx_release_date (ReleaseDate),
    KEY idx_featured (Featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Screenings table
CREATE TABLE Screenings (
    ScreeningID int(11) NOT NULL AUTO_INCREMENT,
    MovieID int(11) NOT NULL,
    CinemaID int(11) NOT NULL,
    ScreeningTime datetime NOT NULL,
    TicketPrice decimal(10,2) NOT NULL,
    PRIMARY KEY (ScreeningID),
    KEY CinemaID (CinemaID),
    KEY idx_screening_time (ScreeningTime),
    KEY idx_movie_cinema (MovieID,CinemaID),
    CONSTRAINT screenings_ibfk_1 FOREIGN KEY (MovieID) REFERENCES Movies (MovieID) ON UPDATE CASCADE,
    CONSTRAINT screenings_ibfk_2 FOREIGN KEY (CinemaID) REFERENCES Cinemas (CinemaID) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Seats table
CREATE TABLE Seats (
    SeatID int(11) NOT NULL AUTO_INCREMENT,
    CinemaID int(11) NOT NULL,
    Row char(1) NOT NULL,
    Number int(11) NOT NULL,
    PRIMARY KEY (SeatID),
    UNIQUE KEY unq_seat_per_cinema (CinemaID,Row,Number),
    KEY idx_cinema_seat (CinemaID,Row,Number),
    CONSTRAINT seats_ibfk_1 FOREIGN KEY (CinemaID) REFERENCES Cinemas (CinemaID) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Bookings table
CREATE TABLE Bookings (
    BookingID int(11) NOT NULL AUTO_INCREMENT,
    UserID int(11) NOT NULL,
    ScreeningID int(11) NOT NULL,
    BookingTime datetime DEFAULT current_timestamp(),
    TotalAmount decimal(10,2) NOT NULL,
    payment_status enum('pending','success') DEFAULT 'pending',
    PRIMARY KEY (BookingID),
    KEY idx_user_booking (UserID),
    KEY fk_bookings_screening (ScreeningID),
    CONSTRAINT fk_bookings_screening FOREIGN KEY (ScreeningID) REFERENCES Screenings (ScreeningID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_bookings_user FOREIGN KEY (UserID) REFERENCES Users (UserID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create BookedSeats table
CREATE TABLE BookedSeats (
    BookingID int(11) NOT NULL,
    SeatID int(11) NOT NULL,
    PRIMARY KEY (BookingID,SeatID),
    KEY idx_seat_booking (SeatID,BookingID),
    CONSTRAINT bookedseats_ibfk_2 FOREIGN KEY (SeatID) REFERENCES Seats (SeatID) ON UPDATE CASCADE,
    CONSTRAINT fk_bookedseats_booking FOREIGN KEY (BookingID) REFERENCES Bookings (BookingID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data into Cinemas
INSERT INTO Cinemas (Name, Address) VALUES
('CineBox Central', '26 Street, 380381 Singapore'),
('CineBox North', '789 North Avenue, 380382 Singapore'),
('CineBox East', '456 East Road, 380383 Singapore');

-- Insert sample data into Movies
INSERT INTO Movies (Title, Genre, Duration, Synopsis, ReleaseDate, Thumbnail, Poster, Featured) VALUES
('Oppenheimer', 'Drama/History', 180, 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.', '2023-07-21', 'https://images7.alphacoders.com/131/1314905.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://picfiles.alphacoders.com/620/thumb-1920-620269.jpeg', 1),
('Barbie', 'Comedy/Fantasy', 114, 'Barbie and Ken are having the time of their lives in the colorful and seemingly perfect world of Barbie Land.', '2023-07-21', 'https://images8.alphacoders.com/133/1331131.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://avatarfiles.alphacoders.com/353/353669.jpg', 1),
('The Dark Knight', 'Action/Drama', 152, 'Batman confronts the mysterious and anarchistic Joker', '2008-07-18', 'https://picfiles.alphacoders.com/537/537201.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://picfiles.alphacoders.com/441/441965.jpg', 1),
('Inception', 'Sci-Fi/Action', 148, 'A thief who steals corporate secrets through dream-sharing technology', '2010-07-16', 'https://images8.alphacoders.com/490/490727.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://picfiles.alphacoders.com/451/451058.jpg', 1);

-- Insert sample data into Users
INSERT INTO Users (Username, Email, PasswordHash, PhoneNumber) VALUES
('john_doe', 'john@example.com', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '+6591234567'),
('jane_smith', 'jane@example.com', 'c6ba91b90d922e159893f46c387e5dc1b3dc5c101a5a4522f03b987177a24a91', '+6592345678');

-- Insert sample data into Screenings
INSERT INTO Screenings (MovieID, CinemaID, ScreeningTime, TicketPrice) VALUES
(1, 1, '2024-11-22 14:00:00', 15.00),
(1, 1, '2024-11-22 18:00:00', 18.00),
(2, 1, '2024-11-29 15:00:00', 15.00),
(2, 2, '2024-11-29 16:00:00', 15.00);

-- Insert sample data into Seats for first cinema
INSERT INTO Seats (CinemaID, Row, Number)
SELECT 1, char(ascii('A') + floor((num-1)/10)), ((num-1) % 10) + 1
FROM (
    SELECT @row := @row + 1 as num
    FROM (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) t1,
    (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4) t2,
    (SELECT @row:=0) t3
    LIMIT 50
) numbers;
