# Entity relationship summary

## Entities

- `users`: stores account data, role, encrypted password, optional API token hash and soft-delete state.
- `trips`: stores bookable journeys managed by admins.
- `reservations`: connects users with trips and tracks seats, role, notes and reservation state.
- `password_resets`: stores temporary password recovery tokens.

## Relationships

- `users (1) -> (N) reservations`
- `trips (1) -> (N) reservations`
- `users.email (1) -> (N) password_resets.email`

## Business rules

- A user can have at most `3` active reservations at the same time.
- A user cannot have two active reservations for the same trip.
- A user cannot reserve two trips departing at the exact same datetime.
- Active reservation seats cannot exceed trip available seats.
- Reservations can only be edited or cancelled more than `2` hours before departure.
