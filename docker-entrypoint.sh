#!/bin/bash
# Inyectar variables de entorno en Apache
echo "SetEnv STRIPE_SECRET_KEY ${STRIPE_SECRET_KEY}" >> /etc/apache2/apache2.conf
echo "SetEnv STRIPE_PUBLIC_KEY ${STRIPE_PUBLIC_KEY}" >> /etc/apache2/apache2.conf

# Arrancar Apache
apache2-foreground