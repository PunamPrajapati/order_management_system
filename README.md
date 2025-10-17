#### Set Up Instruction: ####
#### Clone the Repository:
git clone https://github.com/PunamPrajapati/order_management_system.git

#### Install Dependencies:
composer install

#### Create Environment File:
copy .env.example to .env

#### Generate App Key:
php artisan key:generate

#### Run Migrations:
php artisan migrate

#### Install Sanctum:
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

#### Serve the Application:
php artisan serve

#### API will be available at: 
http://localhost:8000/api

#### Authentication:
All protected endpoints require a Bearer Token in the header:
Authorization: Bearer <token>

Tokens are issued via the Login API.

#### Notes:
1. Default order status: pending
2. Once an order is completed, it cannot be updated.
3. Completed order can be cancelled.
4. total_amount is automatically calculated from order_items.

#### Project Structure Overview:
app/
 ├── Http/
 │    ├── Controllers/
 │    │     ├── UserController.php
 │    │     └── OrderController.php
 │    ├── Requests/
 |    │     ├── FilterOrderRequest.php   
 │    │     ├── LoginRequest.php
 │    │     ├── RegisterRequest.php
 │    │     ├── OrderRequest.php
 │    │     ├── OrderStatusRequest.php
 │    └── Resources/
 │          ├── UserResource.php
 │          └── OrderResource.php
 ├── Services/
 │    ├── UserService.php
 │    └── OrderService.php
 └── Models/
      ├── User.php
      └── Order.php

#### API Documentation:

#### API Url (apiUrl): http://localhost:8000/api
### User Registration
    Url: {{apiUrl}}/register
    Method: POST
    Request Body:
                {
                "name": "Punam Prajapati",
                "email": "punam@example.com",
                "password": "secret123"
                }

### User Login
    Url: {{apiUrl}}/login
    Method: POST
    Request Body:
                {
                "email": "punam@example.com",
                "password": "secret123"
                }

### User Logout
    Url: {{apiUrl}}/logout
    Method: POST
    Headers: 
        Authorization: Bearer <token>

### User Order List
    Url: /api/orders?page=1&perPage=10&status=processing
    Method: GET
    Headers: 
        Authorization: Bearer <token>
    Params:
            page:1
            per_page:10
            status:processing

### Order Detail
    Url: {{apiUrl}}/orders/{orderId}
    Method: GET
    Headers: 
        Authorization: Bearer <token>

### Create Order
    Url: {{apiUrl}}/orders
    Method: POST
    Headers: 
        Authorization: Bearer <token>
    Request Body:
            {
                "customer_name" : "Punam",
                "order_items" : [{ "product_id": 3, "quantity": 4, "price": 800 },
                                { "product_id": 7, "quantity": 6, "price": 250.20 }]
            }

### Update Order
    Url: {{apiUrl}}/orders/{orderId}
    Method: PUT
    Headers: 
        Authorization: Bearer <token>
    Request Body:
            {
                "customer_name" : "Punam Updated",
                "order_items" : [{ "product_id": 3, "quantity": 6, "price": 800 },
                                { "product_id": 7, "quantity": 6, "price": 250.20 }]
            }

### Update Order Status
    Url: {{apiUrl}}/orders/{orderId}/status
    Method: PATCH
    Headers: 
        Authorization: Bearer <token>
    Request Body:
            {
                "status" : "processing"
            }

### Delete Order
    Url: {{apiUrl}}/orders/{orderId}
    Method: DELETE
    Headers: 
        Authorization: Bearer <token>

