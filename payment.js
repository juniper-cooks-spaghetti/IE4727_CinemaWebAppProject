// payment.js
document.addEventListener('DOMContentLoaded', function() {
    const methods = document.querySelectorAll('.payment-method');
    const proceedBtn = document.getElementById('proceedBtn');
    const paymentMethodInput = document.getElementById('selected_payment_method');
    
    methods.forEach(method => {
        method.addEventListener('click', function() {
            // Remove selection from all methods
            methods.forEach(m => m.querySelector('.method-box').classList.remove('selected'));
            
            // Add selection to clicked method
            this.querySelector('.method-box').classList.add('selected');
            
            // Enable proceed button
            proceedBtn.disabled = false;
            
            // Set the hidden input value
            paymentMethodInput.value = this.dataset.method;
        });
    });
});