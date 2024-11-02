# CineBox Database

A complete database solution for managing cinema operations including movies, screenings, bookings, and seats.

## Project Structure
```
IE4727_CINEMAWEBAPPPROJECT/
├── database/
│   ├── 01_create_database.sql
│   ├── 02_create_tables.sql
│   └── 03_sample_data.sql
│   └── readme_db.md
│   └── schema.md
```

## Setup Instructions

### Prerequisites
- MySQL/MariaDB
- XAMPP (optional)

### Installation Steps

1. Clone this repository:
   ```bash
   git clone https://github.com/yourusername/cinebox.git
   ```

2. Using MySQL Command Line:
   ```bash
   mysql -u root -p < scripts/01_create_database.sql
   mysql -u root -p cinebox < scripts/02_create_tables.sql
   mysql -u root -p cinebox < scripts/03_sample_data.sql
   ```

3. Using phpMyAdmin (XAMPP):
   - Open phpMyAdmin (usually at http://localhost/phpmyadmin)
   - Create a new database named 'cinebox'
   - Import the SQL files from the scripts folder in order

## Database Schema

The database consists of the following tables:
- Movies
- Cinemas
- Users
- Screenings
- Seats
- Bookings
- BookedSeats

For detailed schema information, see [schema.md](database/schema.md).

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
