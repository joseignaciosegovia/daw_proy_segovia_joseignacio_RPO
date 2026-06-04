#!/bin/bash
# Inyectar variables de entorno en Apache
printenv | grep -E "STRIPE" | while IFS='=' read -r key value; do
    echo "SetEnv $key $value" >> /etc/apache2/apache2.conf
done

# Arrancar Apache
exec apache2-foreground