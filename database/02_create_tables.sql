-- Drop tables if they exist (in correct order due to foreign key constraints)
DROP TABLE IF EXISTS BookedSeats;
DROP TABLE IF EXISTS Bookings;
DROP TABLE IF EXISTS Seats;
DROP TABLE IF EXISTS Screenings;
DROP TABLE IF EXISTS Movies;
DROP TABLE IF EXISTS Cinemas;
DROP TABLE IF EXISTS Users;

-- Create Movies table
CREATE TABLE Movies (
    MovieID INT AUTO_INCREMENT,
    Title VARCHAR(255) NOT NULL,
    Genre VARCHAR(100),
    Duration INT COMMENT 'Duration in minutes',
    Synopsis TEXT,
    ReleaseDate DATE,
    PRIMARY KEY (MovieID),
    INDEX idx_title (Title),
    INDEX idx_release_date (ReleaseDate),
    Thumbnail TEXT,
    Poster TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Cinemas table
CREATE TABLE Cinemas (
    CinemaID INT AUTO_INCREMENT,
    Name VARCHAR(255) NOT NULL,
    Address TEXT,
    PRIMARY KEY (CinemaID),
    INDEX idx_name (Name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Users table
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL,
    PhoneNumber VARCHAR(20),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (UserID),
    UNIQUE KEY unq_username (Username),
    UNIQUE KEY unq_email (Email),
    INDEX idx_email (Email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Screenings table
CREATE TABLE Screenings (
    ScreeningID INT AUTO_INCREMENT,
    MovieID INT NOT NULL,
    CinemaID INT NOT NULL,
    ScreeningTime DATETIME NOT NULL,
    TicketPrice DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (ScreeningID),
    FOREIGN KEY (MovieID) REFERENCES Movies(MovieID) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (CinemaID) REFERENCES Cinemas(CinemaID) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_screening_time (ScreeningTime),
    INDEX idx_movie_cinema (MovieID, CinemaID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Seats table
CREATE TABLE Seats (
    SeatID INT AUTO_INCREMENT,
    CinemaID INT NOT NULL,
    Row CHAR(1) NOT NULL,
    Number INT NOT NULL,
    PRIMARY KEY (SeatID),
    FOREIGN KEY (CinemaID) REFERENCES Cinemas(CinemaID) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY unq_seat_per_cinema (CinemaID, Row, Number),
    INDEX idx_cinema_seat (CinemaID, Row, Number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Bookings table
CREATE TABLE Bookings (
    BookingID INT AUTO_INCREMENT,
    UserID INT NOT NULL,
    ScreeningID INT NOT NULL,
    BookingTime DATETIME DEFAULT CURRENT_TIMESTAMP,
    TotalAmount DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (BookingID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (ScreeningID) REFERENCES Screenings(ScreeningID) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_user_booking (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create BookedSeats table
CREATE TABLE BookedSeats (
    BookingID INT NOT NULL,
    SeatID INT NOT NULL,
    PRIMARY KEY (BookingID, SeatID),
    FOREIGN KEY (BookingID) REFERENCES Bookings(BookingID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (SeatID) REFERENCES Seats(SeatID) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_seat_booking (SeatID, BookingID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
