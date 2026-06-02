const clientSecret = document.getElementById('submit').dataset.secret;
const stripe = require('stripe')(process.env.STRIPE_PUBLIC_KEY);

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