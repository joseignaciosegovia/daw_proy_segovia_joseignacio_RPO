const clientSecret = document.getElementById('submit').dataset.secret;
const stripe = Stripe("pk_test_51TdSfNCW2BUFTJFItgpsndXlpipC2MUAvxLHZR0bPDp0MQHEIISDXOgtM2r3kC0eInyfLSPZZSMh72Bv5kbM9RJN003v4gQ9Wo");

async function iniciarPago() {
    const elements = stripe.elements({ clientSecret });
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    document.getElementById('submit').addEventListener('click', async (e) => {
        e.preventDefault();

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: window.location.origin + '/public/gracias.php',
            },
        });

        if (error) {
            document.getElementById('error-message').textContent = error.message;
        }
    });
}

iniciarPago();