CREATE TABLE IF NOT EXISTS reservations (
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

CREATE INDEX idx_reservations_status ON reservations(status);
CREATE INDEX idx_reservations_user_trip ON reservations(user_id, trip_id);
