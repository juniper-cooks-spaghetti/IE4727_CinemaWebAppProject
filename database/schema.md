# Database Schema Documentation

## Tables Structure

### Movies
| Column | Type | Description |
|--------|------|-------------|
| MovieID | INT | Primary key, auto-increment |
| Title | VARCHAR(255) | Movie title |
| Genre | VARCHAR(100) | Movie genre |
| Duration | INT | Duration in minutes |
| Synopsis | TEXT | Movie description |
| ReleaseDate | DATE | Release date |
| Thumbnail | TEXT | URL of movie thumbnail |
| Poster | TEXT | URL of movie poster |

### Cinemas
| Column | Type | Description |
|--------|------|-------------|
| CinemaID | INT | Primary key, auto-increment |
| Name | VARCHAR(255) | Cinema name |
| Address | TEXT | Physical address |

### Users
| Column | Type | Description |
|--------|------|-------------|
| UserID | INT | Primary key, auto-increment |
| Username | VARCHAR(50) | Unique username |
| Email | VARCHAR(255) | Unique email address |
| PasswordHash | VARCHAR(255) | Hashed password |
| PhoneNumber | VARCHAR(20) | Contact number |
| RememberToken | VARCHAR(64) | Token for "Remember Me" functionality |
| TokenCreatedAt | TIMESTAMP | When remember token was created |
| CreatedAt | TIMESTAMP | Account creation time |
| UpdatedAt | TIMESTAMP | Last update time |
| LastLoginDate | TIMESTAMP | Last successful login |

### Screenings
| Column | Type | Description |
|--------|------|-------------|
| ScreeningID | INT | Primary key, auto-increment |
| MovieID | INT | Foreign key to Movies |
| CinemaID | INT | Foreign key to Cinemas |
| ScreeningTime | DATETIME | Show time |
| TicketPrice | DECIMAL(10,2) | Price per ticket |

### Seats
| Column | Type | Description |
|--------|------|-------------|
| SeatID | INT | Primary key, auto-increment |
| CinemaID | INT | Foreign key to Cinemas |
| Row | CHAR(1) | Seat row (A-Z) |
| Number | INT | Seat number |

### Bookings
| Column | Type | Description |
|--------|------|-------------|
| BookingID | INT | Primary key, auto-increment |
| CartID | VARCHAR(36) | Groups bookings in same transaction |
| UserID | INT | Foreign key to Users |
| ScreeningID | INT | Foreign key to Screenings |
| BookingTime | DATETIME | Time of booking |
| TotalAmount | DECIMAL(10,2) | Total booking amount |

### BookedSeats
| Column | Type | Description |
|--------|------|-------------|
| BookingID | INT | Part of composite primary key |
| SeatID | INT | Part of composite primary key |

## Relationships

- A Movie can have multiple Screenings
- A Cinema can have multiple Screenings and Seats
- A User can have multiple Bookings
- A Booking belongs to one User and one Screening
- Multiple Bookings can share the same CartID (same transaction)
- A Booking can have multiple BookedSeats
- A Seat can be booked multiple times (different screenings)

## Indexes

### Movies
- PRIMARY KEY (MovieID)
- INDEX idx_title (Title)
- INDEX idx_release_date (ReleaseDate)

### Cinemas
- PRIMARY KEY (CinemaID)
- INDEX idx_name (Name)

### Users
- PRIMARY KEY (UserID)
- UNIQUE KEY unq_username (Username)
- UNIQUE KEY unq_email (Email)
- INDEX idx_email (Email)
- INDEX idx_remember_token (RememberToken)
- INDEX idx_token_created (TokenCreatedAt)

### Screenings
- PRIMARY KEY (ScreeningID)
- INDEX idx_screening_time (ScreeningTime)
- INDEX idx_movie_cinema (MovieID, CinemaID)

### Seats
- PRIMARY KEY (SeatID)
- UNIQUE KEY unq_seat_per_cinema (CinemaID, Row, Number)
- INDEX idx_cinema_seat (CinemaID, Row, Number)

### Bookings
- PRIMARY KEY (BookingID)
- INDEX idx_user_booking (UserID)
- INDEX idx_cart (CartID)

### BookedSeats
- PRIMARY KEY (BookingID, SeatID)
- INDEX idx_seat_booking (SeatID, BookingID)