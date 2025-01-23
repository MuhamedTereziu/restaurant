# Restaurant Reservation Management System

This project is a **Restaurant Reservation Management System** that allows administrators and users to manage table reservations, orders, and general restaurant operations efficiently.

## Features

- **User Authentication**
  - Admin and user login functionality.
  - Session management to ensure secure access.

- **Table Management**
  - Add, update, and delete restaurant tables.
  - Real-time status updates of table availability.

- **Order Management**
  - Place new orders.
  - View and update existing orders.

- **User Management**
  - Manage user accounts and roles.

- **Invoice Generation**
  - Generate invoices for completed orders.

## Technologies Used

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** Microsoft SQL Server

## Running
- **Running through my domain**  *keep in mind its not 100% active*
    - https://db.mtereziu.xyz/restaurant/index.html
    - **Demo email:** *demo@demo.com*
    - **Demo password:** *demo*
## Installation

Follow these steps to set up the project locally:

1. Clone the repository:
   ```sh
   git clone https://github.com/MuhamedTereziu/restaurant.git
   ```

2. Navigate to the project directory:
   ```sh
   cd restaurant
   ```

3. Set up the database:
   - Import the SQL file provided in the `database/` directory into Microsoft SQL Server.

4. Configure the database connection:
   - Update the `config.php` file with your database credentials.

5. Start a local PHP server:
   ```sh
   php -S localhost:8000
   ```

6. Access the application in your web browser:
   ```
   http://localhost:8000
   ```

## Project Structure

```
restaurant/
│-- config.php            # Database connection settings
│-- index.html            # Landing page
│-- login.php             # User authentication
│-- dashboard.php         # Admin dashboard
│-- manage_tables.php     # Table management
│-- manage_orders.php     # Order management
│-- reserve.php           # Table reservations
│-- logout.php            # Logout functionality
└-- database/             # SQL scripts
```

## Usage

1. **Admin Login:** Use provided credentials to access the dashboard.
2. **Reserve Table:** Users can book tables via the reservation page.
3. **Manage Orders:** Admin can add, update, and delete orders.

## Screenshots

Include screenshots of the login page, dashboard, and other functionalities for better visualization.

## Contributing

Feel free to contribute by submitting pull requests. Ensure your code follows the project guidelines.

## License

This project is licensed under the MIT License.

## Contact

For any inquiries, contact:
- **Email:** sk3pp@pm.me
- **LinkedIn:** [Muhamed Tereziu](https://www.linkedin.com/in/muhamedtereziu/)
