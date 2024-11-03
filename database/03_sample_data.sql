USE cinebox;

-- Insert sample movies
INSERT INTO `movies` (`MovieID`, `Title`, `Genre`, `Duration`, `Synopsis`, `ReleaseDate`, `thumbnail`, `poster`) VALUES
(1, 'Oppenheimer', 'Drama/History', 180, 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.', '2023-07-21', 'https://images7.alphacoders.com/131/1314905.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://picfiles.alphacoders.com/620/thumb-1920-620269.jpeg'),
(2, 'Barbie', 'Comedy/Fantasy', 114, 'Barbie and Ken are having the time of their lives in the colorful and seemingly perfect world of Barbie Land.', '2023-07-21', 'https://images8.alphacoders.com/133/1331131.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://avatarfiles.alphacoders.com/353/353669.jpg'),
(3, 'The Dark Knight', 'Action/Drama', 152, 'Batman confronts the mysterious and anarchistic Joker', '2008-07-18', 'https://picfiles.alphacoders.com/537/537201.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://picfiles.alphacoders.com/441/441965.jpg'),
(4, 'Inception', 'Sci-Fi/Action', 148, 'A thief who steals corporate secrets through dream-sharing technology', '2010-07-16', 'https://images8.alphacoders.com/490/490727.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://picfiles.alphacoders.com/451/451058.jpg');

-- Insert sample cinemas
INSERT INTO Cinemas (Name, Address) VALUES
('CineBox Central', '26 Street, 380381 Singapore'),
('CineBox North', '789 North Avenue, 380382 Singapore'),
('CineBox East', '456 East Road, 380383 Singapore');

-- Insert sample users
INSERT INTO Users (Username, Email, PasswordHash, PhoneNumber) VALUES
('john_doe', 'john@example.com', SHA2('password123', 256), '+6591234567'),
('jane_smith', 'jane@example.com', SHA2('password456', 256), '+6592345678');

-- Insert sample screenings
INSERT INTO Screenings (MovieID, CinemaID, ScreeningTime, TicketPrice) VALUES
(1, 1, '2024-11-01 14:00:00', 15.00),
(1, 1, '2024-11-01 18:00:00', 18.00),
(2, 1, '2024-11-01 15:00:00', 15.00),
(2, 2, '2024-11-01 16:00:00', 15.00);

-- Insert sample seats for CineBox Central (CinemaID = 1)
INSERT INTO Seats (CinemaID, Row, Number)
SELECT 1, char(65 + floor((num-1)/10)), ((num-1) % 10) + 1
FROM (
    SELECT @rownum:=@rownum+1 as num
    FROM (SELECT @rownum:=0) r,
         information_schema.columns LIMIT 50
) numbers;

-- Insert sample seats for CineBox North (CinemaID = 2)
INSERT INTO Seats (CinemaID, Row, Number)
SELECT 2, char(65 + floor((num-1)/10)), ((num-1) % 10) + 1
FROM (
    SELECT @rownum:=@rownum+1 as num
    FROM (SELECT @rownum:=0) r,
         information_schema.columns LIMIT 50
) numbers;

-- Insert sample bookings and booked seats
INSERT INTO Bookings (UserID, ScreeningID, BookingTime, TotalAmount) VALUES
(1, 1, '2024-10-25 10:00:00', 30.00),
(2, 2, '2024-10-25 11:00:00', 36.00);

-- Book some seats for the sample bookings
INSERT INTO BookedSeats (BookingID, SeatID) VALUES
(1, 1), -- First booking, first seat
(1, 2), -- First booking, second seat
(2, 11), -- Second booking, seat in row B
(2, 12); -- Second booking, another seat in row B
