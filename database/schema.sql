CREATE DATABASE IF NOT EXISTS travel_booking_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE travel_booking_system;

DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS trips;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    api_token VARCHAR(64) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE trips (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    origin VARCHAR(120) NOT NULL,
    destination VARCHAR(120) NOT NULL,
    departure_at DATETIME NOT NULL,
    vehicle VARCHAR(100) NOT NULL,
    available_seats INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'published',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE reservations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    trip_id INT UNSIGNED NOT NULL,
    reservation_date DATETIME NOT NULL,
    seats_reserved INT UNSIGNED NOT NULL,
    travel_role ENUM('passenger', 'driver') NOT NULL DEFAULT 'passenger',
    notes VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'cancelled', 'completed') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_reservation_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_reservation_trip FOREIGN KEY (trip_id) REFERENCES trips(id)
);

CREATE TABLE password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL
);

CREATE TABLE activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT UNSIGNED DEFAULT NULL,
    description VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_activity_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_deleted_at ON users(deleted_at);
CREATE INDEX idx_users_api_token ON users(api_token);
CREATE INDEX idx_trips_departure_at ON trips(departure_at);
CREATE INDEX idx_trips_route ON trips(origin, destination);
CREATE INDEX idx_reservations_status ON reservations(status);
CREATE INDEX idx_reservations_user_trip ON reservations(user_id, trip_id);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at);
CREATE INDEX idx_activity_logs_action ON activity_logs(action);

INSERT INTO users (full_name, email, password, phone, role, deleted_at, created_at, updated_at) VALUES
('System Admin', 'admin@travelbooking.local', '$2y$10$.kiA5C92TFGrGOYa85/Kn.8NKPqgVMDHJbqyPYH3lIBl67SB057.a', '+34 600 111 222', 'admin', NULL, NOW(), NOW()),
('Demo User', 'user@travelbooking.local', '$2y$10$ZMRSlzh45A0Vedop9Q66H.xz/PyUOgkB5HQ0HhiBadLIMnU1/T9ba', '+34 600 333 444', 'user', NULL, NOW(), NOW());

INSERT INTO trips (name, description, origin, destination, departure_at, vehicle, available_seats, image_path, status, created_at, updated_at) VALUES
('Madrid to Valencia Weekend Ride', 'Shared intercity trip with luggage space and direct route.', 'Madrid', 'Valencia', DATE_ADD(NOW(), INTERVAL 3 DAY), 'Toyota Corolla', 4, 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80', 'published', NOW(), NOW()),
('Barcelona Coastal Escape', 'Morning departure with scenic route and one stop.', 'Barcelona', 'Tarragona', DATE_ADD(NOW(), INTERVAL 5 DAY), 'Seat Leon', 3, 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=900&q=80', 'published', NOW(), NOW()),
('Seville to Granada Student Trip', 'Affordable trip focused on students and light luggage.', 'Seville', 'Granada', DATE_ADD(NOW(), INTERVAL 7 DAY), 'Renault Clio', 4, 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=900&q=80', 'published', NOW(), NOW());

INSERT INTO reservations (user_id, trip_id, reservation_date, seats_reserved, travel_role, notes, status, created_at, updated_at) VALUES
(2, 1, NOW(), 1, 'passenger', 'Window seat preferred', 'active', NOW(), NOW()),
(2, 2, NOW(), 1, 'driver', 'Will bring small luggage', 'active', NOW(), NOW());
