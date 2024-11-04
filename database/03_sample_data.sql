USE cinebox;

-- Insert sample movies
INSERT INTO `movies` (`MovieID`, `Title`, `Genre`, `Duration`, `Synopsis`, `ReleaseDate`, `thumbnail`, `poster`, `Featured`) VALUES
(1, 'Oppenheimer', 'Drama/History', 180, 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.', '2023-07-21', 'https://images7.alphacoders.com/131/1314905.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://picfiles.alphacoders.com/620/thumb-1920-620269.jpeg', 'TRUE'),
(2, 'Barbie', 'Comedy/Fantasy', 114, 'Barbie and Ken are having the time of their lives in the colorful and seemingly perfect world of Barbie Land.', '2023-07-21', 'https://images8.alphacoders.com/133/1331131.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://avatarfiles.alphacoders.com/353/353669.jpg', 'TRUE'),
(3, 'The Dark Knight', 'Action/Drama', 152, 'Batman confronts the mysterious and anarchistic Joker', '2008-07-18', 'https://picfiles.alphacoders.com/537/537201.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://picfiles.alphacoders.com/441/441965.jpg', 'TRUE'),
(4, 'Inception', 'Sci-Fi/Action', 148, 'A thief who steals corporate secrets through dream-sharing technology', '2010-07-16', 'https://images8.alphacoders.com/490/490727.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://picfiles.alphacoders.com/451/451058.jpg', 'TRUE');
(5, 'Parasite', 'Thriller/Dark Comedy', 129, 'A poor family schemes to become employed by a wealthy family', '2019-10-03', 'https://images3.alphacoders.com/109/1090097.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://images3.alphacoders.com/109/1090098.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500'),
(6, 'Spirited Away', 'Animation/Fantasy', 125, 'A young girl wanders into the spirit world and must find a way to free her parents', '2001-07-20', 'https://images5.alphacoders.com/108/1087762.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://images4.alphacoders.com/108/1087763.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500'),
(7, 'The Matrix', 'Sci-Fi/Action', 136, 'A computer hacker learns that the world he lives in is not what it seems', '1999-03-31', 'https://images4.alphacoders.com/105/1055425.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://images6.alphacoders.com/105/1055426.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500'),
(8, 'The Shawshank Redemption', 'Drama/Crime', 142, 'A man is sentenced to life in Shawshank prison for the murder of his wife, but he never gives up hope', '1994-09-23', 'https://images7.alphacoders.com/129/1299840.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://images4.alphacoders.com/129/1299841.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500'),
(9, 'The Lord of the Rings: The Fellowship of the Ring', 'Fantasy/Adventure', 208, 'A hobbit named Frodo inherits the One Ring and sets out on a quest to destroy it', '2001-12-19', 'https://images1.alphacoders.com/123/1237925.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500', 'https://images4.alphacoders.com/123/1237926.jpg?auto=compress&cs=tinysrgb&dpr=1&w=1500');

-- Insert sample cinemas
INSERT INTO Cinemas (Name, Address) VALUES
('CineBox Central', '26 Street, 380381 Singapore'),
('CineBox North', '789 North Avenue, 380382 Singapore'),
('CineBox East', '456 East Road, 380383 Singapore');

-- Insert sample users with initialized timestamp fields
INSERT INTO Users (Username, Email, PasswordHash, PhoneNumber, RememberToken, TokenCreatedAt, CreatedAt, UpdatedAt, LastLoginDate) VALUES
('john_doe', 'john@example.com', SHA2('password123', 256), '+6591234567', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('jane_smith', 'jane@example.com', SHA2('password456', 256), '+6592345678', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- Insert sample screenings
INSERT INTO Screenings (MovieID, CinemaID, ScreeningTime, TicketPrice) VALUES
(1, 1, '2024-11-01 14:00:00', 15.00),
(1, 1, '2024-11-01 18:00:00', 18.00),
(1, 1, '2024-11-20 11:30:00', 10.00),
(1, 1, '2024-11-20 15:30:00', 10.00),
(1, 2, '2024-11-20 18:30:00', 10.00),
(2, 1, '2024-11-01 15:00:00', 15.00),
(2, 2, '2024-11-01 16:00:00', 15.00),
(3, 1, '2024-11-21 12:00:00', 15.00),
(3, 2, '2024-11-21 14:00:00', 18.00),
(4, 1, '2024-11-22 16:00:00', 15.00),
(4, 2, '2024-11-22 18:00:00', 18.00),
(5, 1, '2024-11-23 12:00:00', 15.00),
(5, 2, '2024-11-23 14:00:00', 18.00),
(6, 1, '2024-11-24 16:00:00', 15.00),
(6, 2, '2024-11-24 18:00:00', 18.00),
(7, 1, '2024-11-25 12:00:00', 15.00),
(7, 2, '2024-11-25 14:00:00', 18.00),
(8, 1, '2024-11-26 16:00:00', 15.00),
(8, 2, '2024-11-26 18:00:00', 18.00),
(9, 1, '2024-11-27 12:00:00', 15.00),
(9, 2, '2024-11-27 14:00:00', 18.00);

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

-- Insert sample bookings and booked seats (with CartID)
INSERT INTO Bookings (CartID, UserID, ScreeningID, BookingTime, TotalAmount) VALUES
('cart123', 1, 1, '2024-10-25 10:00:00', 30.00),
('cart123', 1, 2, '2024-10-25 10:00:00', 36.00);

-- Book some seats for the sample bookings
INSERT INTO BookedSeats (BookingID, SeatID) VALUES
(1, 1), -- First booking, first seat
(1, 2), -- First booking, second seat
(2, 11), -- Second booking, seat in row B
(2, 12); -- Second booking, another seat in row B