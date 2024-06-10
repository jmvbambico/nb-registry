# NB Registry

NB Registry is a demonstration of a microservices architecture, built using the Symfony framework and powered by RabbitMQ for message queuing. This project showcases the implementation of various microservices patterns and practices, providing a practical example for developers interested in developing scalable and resilient applications.

### Prerequisites

- Docker
- Docker Compose

### Installation

To get NB Registry up and running, follow these steps:

1. Clone this repository:

   ```bash
   git clone git@github.com:jmvbambico/nb-registry.git
   ```

2. Navigate to the root directory of the project and run Docker Compose to build and start the containers:

   ```bash
   cd nb-registry
   docker compose up --build
   ```

### Testing

Unit and Integration tests can be run for both services through the following:

    ```bash
    cd notifications_service
    php bin/phpunit tests
    cd ../users_service
    php bin/phpunit tests
    ```
- more details can be found on each test at each service' respective `tests` directory

### Usage

1. Assuming that your docker has finished building the containers and is up and running, the following URL's should be made available to you:

- Users Service: http://localhost
- Notifications Service: http://localhost:8080
- RabbitMQ: http://localhost:15672 (guest:guest)

2. To populate the users, refer to this endpoint specification:

    ```
    POST: http://127.0.0.1/user
    Content-Type: application/json
    Body:
    {
        "email": "fubar@foo.com",
        "firstName": "Jane",
        "lastName": "Doe"
    }
    ```

- A confirmation response should be expected and you may verify the data by opening `/users_service/var/data.db` on any SQLite client
- The endpoint will also fire an event to RabbitMQ which you can verify at their management console (http://localhost:15672)

3. The notifications service is set to automatically consume the RabbitQ events queue. You may verify this by visiting: http://localhost:8080/logs

- This actually reads the `users_registered.txt` file located in `/notifications_service/public/` assuming that a message event has already been consumed at least once.

### Architecture

```
NB Registry/
├── docker-compose.yml
├── users_service/
│   ├── src/
│   │   ├── Controller/
│   │   └── Entity/
│   ├── var/
│   │   └── data.db
│   └── Dockerfile
├── notifications_service/
│   ├── public/
│   │   └── users_registered.txt
│   ├── src/
│   │   ├── Controller/
│   │   └── Service/
│   └── Dockerfile
└── rabbitmq/
```

- `docker-compose.yml`: Defines the services, networks, and volumes for the application.
- `users_service/`: Contains the user management microservice, including source code, SQLite database, and Dockerfile.
- `notifications_service/`: Houses the notifications microservice, including source code, log file for registered users, and Dockerfile.
- `rabbitmq/`: Contains the RabbitMQ service, used for message queuing between services.