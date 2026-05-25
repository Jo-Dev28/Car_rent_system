-- database.sql
CREATE DATABASE IF NOT EXISTS car_rental_db;
USE car_rental_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Vehicles table
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT DEFAULT 2023,
    transmission VARCHAR(20) DEFAULT 'Automatic',
    fuel_type VARCHAR(20) DEFAULT 'Petrol',
    seats INT DEFAULT 5,
    rental_price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'assets/images/default-car.jpg',
    availability BOOLEAN DEFAULT 1,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_no VARCHAR(20) UNIQUE NOT NULL,
    user_id INT,
    vehicle_id INT NOT NULL,
    pickup_date DATE NOT NULL,
    return_date DATE NOT NULL,
    total_days INT,
    total_price DECIMAL(10,2),
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

-- Testimonials table
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100),
    user_image VARCHAR(255),
    rating INT DEFAULT 5,
    comment TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Newsletter subscribers
CREATE TABLE subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact messages
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin user (password: admin123)
INSERT INTO admins (username, email, password) VALUES 
('admin', 'admin@velocityrentals.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample vehicles
INSERT INTO vehicles (brand, model, year, transmission, fuel_type, seats, rental_price, image, description) VALUES
('Tesla', 'Model S Plaid', 2024, 'Automatic', 'Electric', 5, 149.99, 'assets/images/tesla-s.jpg', 'Luxury electric sedan with 1020 hp, 0-60 in 1.99s'),
('BMW', 'X7 M60i', 2024, 'Automatic', 'Petrol', 7, 189.99, 'assets/images/bmw-x7.jpg', 'Full-size luxury SUV with V8 engine'),
('Mercedes-Benz', 'S-Class', 2024, 'Automatic', 'Petrol', 5, 199.99, 'assets/images/mercedes-s.jpg', 'The ultimate luxury sedan'),
('Porsche', '911 Turbo S', 2024, 'Automatic', 'Petrol', 4, 299.99, 'assets/images/porsche-911.jpg', 'Iconic sports car with 640 hp'),
('Audi', 'Q8 e-tron', 2024, 'Automatic', 'Electric', 5, 159.99, 'assets/images/audi-q8.jpg', 'Electric luxury SUV'),
('Range Rover', 'Sport', 2024, 'Automatic', 'Diesel', 5, 229.99, 'assets/images/rangerover.jpg', 'British luxury SUV'),
('Ferrari', 'SF90 Stradale', 2024, 'Automatic', 'Hybrid', 2, 499.99, 'assets/images/ferrari-sf90.jpg', '986 hp hybrid supercar'),
('Lamborghini', 'Urus', 2024, 'Automatic', 'Petrol', 5, 399.99, 'assets/images/urus.jpg', 'Super SUV');

-- Insert sample testimonials
INSERT INTO testimonials (user_name, user_email, rating, comment, is_approved) VALUES
('Michael Chen', 'michael@email.com', 5, 'Absolutely incredible service! The Tesla Model S was immaculate and the pickup process was seamless. Highly recommend Velocity Rentals!', 1),
('Sarah Williams', 'sarah@email.com', 5, 'Best car rental experience I''ve ever had. The Porsche 911 made my birthday weekend unforgettable. Professional staff and premium vehicles.', 1),
('David Kim', 'david@email.com', 4, 'Great selection of luxury cars. The booking process was easy and customer support was responsive. Will definitely use again.', 1),
('Emma Thompson', 'emma@email.com', 5, 'Drove the Range Rover for a road trip. Perfect condition, clean, and comfortable. Five stars!', 1)