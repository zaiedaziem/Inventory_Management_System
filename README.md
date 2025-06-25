# Inventory Management System

## Overview
The Inventory Management System is a web application designed to help businesses manage their inventory, orders, products, suppliers, and users efficiently. The application is structured into a backend and a frontend, allowing for a clear separation of concerns and scalability.

## Project Structure
```
inventory-management-system
├── backend
│   ├── public
│   │   └── index.php
│   ├── src
│   │   └── App
│   │       ├── Controllers
│   │       ├── Middleware
│   │       ├── Models
│   │       ├── Services
│   │       └── db.php
│   ├── composer.json
│   └── composer.lock
├── frontend
│   ├── public
│   │   └── index.html
│   └── src
│       ├── components
│       ├── router
│       │   └── index.js
│       ├── shared
│       │   └── store.js
│       ├── views
│       │   ├── Admin
│       │   ├── Inventory
│       │   ├── Orders
│       │   ├── Products
│       │   ├── Suppliers
│       │   └── Users
│       ├── App.vue
│       └── main.js
```

## Setup Instructions

### Backend
1. Navigate to the `backend` directory.
2. Run `composer install` to install the necessary dependencies.
3. Configure your database connection in `src/App/db.php`.
4. Start the backend server using your preferred method (e.g., PHP built-in server).

### Frontend
1. Navigate to the `frontend` directory.
2. Install the required packages using `npm install` or `yarn install`.
3. Start the frontend development server using `npm run serve` or `yarn serve`.

## Features
- **User Management**: Admins can manage users and their roles.
- **Inventory Management**: Track products and their quantities.
- **Order Management**: Manage customer orders and their statuses.
- **Supplier Management**: Keep track of suppliers and their contact information.

## Technologies Used
- PHP for the backend
- Vue.js for the frontend
- MySQL for the database

## Contributing
Contributions are welcome! Please open an issue or submit a pull request for any enhancements or bug fixes.

## License
This project is licensed under the MIT License.