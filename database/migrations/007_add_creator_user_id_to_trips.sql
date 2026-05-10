ALTER TABLE trips
ADD COLUMN creator_user_id INT UNSIGNED NULL AFTER status,
ADD CONSTRAINT fk_trip_creator_user FOREIGN KEY (creator_user_id) REFERENCES users(id) ON DELETE SET NULL;

CREATE INDEX idx_trips_creator_user_id ON trips(creator_user_id);
