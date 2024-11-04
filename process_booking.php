<?php
require_once 'auth.inc.php';
require_once 'cart_backend.php';
require_once 'db_config.php';

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
                // Handle new booking creation
                if (!isset($_POST['screening_id']) || !isset($_POST['seats']) || !isset($_POST['total_amount'])) {
                    throw new Exception("Missing required booking information");
                }

                $conn = new mysqli($servername, $username_db, $password_db, $dbname);
                if ($conn->connect_error) {
                    throw new Exception("Connection failed: " . $conn->connect_error);
                }

                try {
                    $conn->begin_transaction();

                    // Insert new booking
                    $stmt = $conn->prepare("INSERT INTO Bookings (UserID, ScreeningID, BookingTime, TotalAmount) VALUES (?, ?, NOW(), ?)");
                    $stmt->bind_param("iid", $_SESSION['user_id'], $_POST['screening_id'], $_POST['total_amount']);
                    $stmt->execute();
                    $booking_id = $conn->insert_id;

                    // Insert booked seats
                    $stmt = $conn->prepare("INSERT INTO BookedSeats (BookingID, SeatID) VALUES (?, ?)");
                    foreach ($_POST['seats'] as $seat_id) {
                        $stmt->bind_param("ii", $booking_id, $seat_id);
                        $stmt->execute();
                    }

                    $conn->commit();
                    $_SESSION['message'] = "Booking confirmed successfully!";
                    $_SESSION['message_type'] = "success";

                } catch (Exception $e) {
                    $conn->rollback();
                    throw $e;
                } finally {
                    $stmt->close();
                    $conn->close();
                }
                break;

            case 'delete':
                if (isset($_POST['booking_id'])) {
                    deleteBooking($_POST['booking_id'], $_SESSION['user_id']);
                    $_SESSION['message'] = "Booking successfully deleted";
                    $_SESSION['message_type'] = "success";
                }
                break;
                
            case 'edit':
                if (isset($_POST['booking_id'])) {
                    header("Location: seats.php?edit_booking=" . $_POST['booking_id']);
                    exit();
                }
                break;

            default:
                throw new Exception("Invalid action specified");
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
}

// All actions redirect back to cart page
header("Location: cart.php");
exit();