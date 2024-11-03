document.addEventListener('DOMContentLoaded', function() {
    const userProfile = document.querySelector('.user-profile');
    let timeoutId;

    if (userProfile) {
        userProfile.addEventListener('mouseenter', function() {
            clearTimeout(timeoutId);
            const dropdown = this.querySelector('.dropdown-menu');
            dropdown.style.display = 'block';
            setTimeout(() => dropdown.style.opacity = '1', 0);
        });

        // Only trigger mouseleave if we're not hovering over the dropdown or profile
        userProfile.addEventListener('mouseleave', function(e) {
            // Check if we're moving to the dropdown menu
            const dropdown = this.querySelector('.dropdown-menu');
            const relatedTarget = e.relatedTarget;
            
            // If we're moving to the dropdown or one of its children, don't hide
            if (dropdown.contains(relatedTarget) || dropdown === relatedTarget) {
                return;
            }

            dropdown.style.opacity = '0';
            timeoutId = setTimeout(() => {
                dropdown.style.display = 'none';
            }, 200);
        });

        // Add listeners for the dropdown menu itself
        const dropdownMenu = userProfile.querySelector('.dropdown-menu');
        if (dropdownMenu) {
            dropdownMenu.addEventListener('mouseenter', function() {
                clearTimeout(timeoutId);
                this.style.display = 'block';
                this.style.opacity = '1';
            });

            dropdownMenu.addEventListener('mouseleave', function() {
                this.style.opacity = '0';
                timeoutId = setTimeout(() => {
                    this.style.display = 'none';
                }, 200);
            });
        }
    }
});