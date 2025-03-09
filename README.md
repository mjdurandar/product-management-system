# Product Management System

A Laravel-based product management system that integrates with multiple third-party APIs (Platzi and FakeStore) for product management. The system includes user roles (admin/user), product CRUD operations, and API integration.

## Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- Docker (optional, for containerized setup)
- MySQL/MariaDB

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd product-management-system
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Generate application key:
```bash
php artisan key:generate
```

7. Run database migrations:
```bash
php artisan migrate
```

8. Create storage link for public uploads:
```bash
php artisan storage:link
```

## Running the Application

### Using Local Development Server

1. Start the Laravel development server:
```bash
php artisan serve
```

2. Compile assets:
```bash
npm run dev
```

### Using Docker

1. Start the Docker containers:
```bash
docker-compose up -d
```

2. The application will be available at `http://localhost:8000`

## Testing

The application includes comprehensive tests for both the product CRUD operations and third-party API integrations.

Run all tests:
```bash
php artisan test
```

Run specific test suites:
```bash
# Run product CRUD tests
php artisan test tests/Feature/ProductControllerTest.php

# Run third-party API tests
php artisan test tests/Feature/ThirdPartyApiTest.php
```

## Features

1. User Management
   - Admin and User roles
   - Role-based access control

2. Product Management
   - Create, Read, Update, Delete (CRUD) operations
   - Image upload support
   - Duplicate product name prevention
   - Product listing and filtering

3. Third-Party API Integration
   - Platzi API integration
   - FakeStore API integration
   - Seamless switching between APIs

4. Security Features
   - Authentication
   - Authorization
   - CSRF protection
   - Input validation

## API Routes

### Admin Routes (requires admin role)
- `GET /product` - List all products
- `POST /product` - Create a new product
- `PUT /product/{id}` - Update a product
- `DELETE /product/{id}` - Delete a product
- `GET /categories` - Get product categories

### User Routes (requires authentication)
- `GET /available-products` - View available products
- `POST /available-products/{id}/claim` - Claim a product
- `GET /your-products` - View claimed products

## Coding Principles and Practices

### 1. Code Readability
- Write clear and understandable code
- Use meaningful variable and function names
- Add comments where necessary to explain complex logic
- Follow PSR-12 coding standards

### 2. DRY (Don't Repeat Yourself)
- Avoid code duplication by creating reusable functions and components
- Refactor repeated code into common functions or classes
- Use Laravel's built-in features for common functionality

### 3. SOLID Principles
- **Single Responsibility**: Each class has one specific purpose
- **Open/Closed**: Code is open for extension but closed for modification
- **Liskov Substitution**: Derived classes must be substitutable for their base classes
- **Interface Segregation**: Clients shouldn't depend on interfaces they don't use
- **Dependency Inversion**: Depend on abstractions, not concretions

### 4. KISS (Keep It Simple, Stupid)
- Keep code as simple as possible
- Avoid unnecessary complexity
- Use Laravel conventions and established patterns

### 5. YAGNI (You Aren't Gonna Need It)
- Only implement features that are currently needed
- Avoid speculative functionality
- Focus on current requirements

### 6. Testing
- Write comprehensive unit tests
- Include feature tests for critical paths
- Test both success and failure scenarios
- Mock external services in tests

### 7. Security Best Practices
- Validate all input data
- Use Laravel's built-in security features
- Implement proper authentication and authorization
- Protect against common web vulnerabilities

### 8. Performance
- Use eager loading to prevent N+1 queries
- Implement caching where appropriate
- Optimize database queries
- Follow Laravel performance best practices

### 9. Error Handling
- Use proper exception handling
- Log errors appropriately
- Provide meaningful error messages
- Fail gracefully when errors occur

### 10. Documentation
- Keep code documentation up to date
- Document complex business logic
- Include API documentation
- Maintain clear commit messages

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details
