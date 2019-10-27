## Requirements

It requires `docker` and `docker-compose`.

## Installation

In project directory run command:

```bash
docker-compose -f infrastructure/docker-compose.yml up -d
```

To install project you have to run this:

```bash
docker-compose -f infrastructure/docker-compose.yml exec php /bin/bash -c "wait-for.sh mysql:3306 && composer install --no-interaction"
```

Next you can visit:

```
http://localhost
```
