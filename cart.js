document.addEventListener('DOMContentLoaded', function() {
    // Edit booking buttons
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            window.location.href = `edit-booking.php?id=${bookingId}`;
        });
    });

    // Checkout button
    const checkoutButton = document.querySelector('.checkout-button');
    if (checkoutButton) {
        checkoutButton.addEventListener('click', function() {
            window.location.href = 'particulars.php';
        });
    }

    // Optional: Add confirmation for editing/deletion
    function confirmAction(message) {
        return window.confirm(message);
    }

    // Optional: Handle booking deletion
    async function deleteBooking(bookingId) {
        if (!confirmAction('Are you sure you want to delete this booking?')) {
            return;
        }

        try {
            const response = await fetch('cart_backend.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_booking&booking_id=${bookingId}`
            });

            const data = await response.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error deleting booking: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while deleting the booking');
        }
    }
});