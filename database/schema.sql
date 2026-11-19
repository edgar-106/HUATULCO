CREATE DATABASE IF NOT EXISTS huatulco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE huatulco;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(160) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('guest', 'admin', 'activity_provider') NOT NULL DEFAULT 'guest',
    preferred_locale ENUM('es', 'en', 'fr') NOT NULL DEFAULT 'es',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS accommodations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    accommodation_type ENUM('casa', 'departamento', 'apartamento', 'habitacion') NOT NULL,
    description TEXT NULL,
    neighborhood VARCHAR(120) NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    capacity SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    nightly_rate DECIMAL(10,2) NOT NULL,
    cleaning_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    deposit_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    cover_image VARCHAR(500) NULL,
    virtual_tour_url VARCHAR(500) NULL,
    is_featured BOOLEAN NOT NULL DEFAULT FALSE,
    is_published BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accommodation_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    guest_name VARCHAR(160) NOT NULL,
    guest_email VARCHAR(190) NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guest_count SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    status ENUM('pending_payment', 'payment_submitted', 'confirmed', 'cancelled', 'expired') NOT NULL DEFAULT 'pending_payment',
    nightly_subtotal DECIMAL(10,2) NOT NULL,
    cleaning_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    deposit_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    penalty_amount DECIMAL(10,2) NULL COMMENT 'Configurable policy; amount/rule pending business definition',
    total_amount DECIMAL(10,2) NOT NULL,
    hold_expires_at DATETIME NOT NULL,
    payment_submitted_at DATETIME NULL,
    confirmed_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reservation_dates (accommodation_id, check_in, check_out, status),
    INDEX idx_reservation_expiry (status, hold_expires_at),
    CONSTRAINT fk_reservation_accommodation FOREIGN KEY (accommodation_id) REFERENCES accommodations(id),
    CONSTRAINT fk_reservation_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT chk_reservation_dates CHECK (check_out > check_in)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_receipts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reservation_id BIGINT UNSIGNED NOT NULL,
    payment_method ENUM('transfer', 'cash_deposit', 'other') NOT NULL DEFAULT 'other',
    receipt_path VARCHAR(500) NOT NULL,
    amount_reported DECIMAL(10,2) NULL,
    review_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_receipt_reservation FOREIGN KEY (reservation_id) REFERENCES reservations(id),
    CONSTRAINT fk_receipt_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id BIGINT UNSIGNED NULL,
    name VARCHAR(160) NOT NULL,
    category ENUM('naturaleza', 'cultura', 'deportes', 'social', 'otro') NOT NULL DEFAULT 'otro',
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_minutes SMALLINT UNSIGNED NULL,
    capacity SMALLINT UNSIGNED NULL,
    is_recurring BOOLEAN NOT NULL DEFAULT FALSE,
    is_published BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_provider FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activity_slots (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id BIGINT UNSIGNED NOT NULL,
    starts_at DATETIME NOT NULL,
    capacity SMALLINT UNSIGNED NULL,
    status ENUM('available', 'unavailable', 'cancelled') NOT NULL DEFAULT 'available',
    CONSTRAINT fk_slot_activity FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    INDEX idx_activity_slot (activity_id, starts_at, status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservation_activities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reservation_id BIGINT UNSIGNED NOT NULL,
    activity_slot_id BIGINT UNSIGNED NOT NULL,
    participant_count SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    status ENUM('requested', 'confirmed', 'cancelled') NOT NULL DEFAULT 'requested',
    CONSTRAINT fk_res_activity_reservation FOREIGN KEY (reservation_id) REFERENCES reservations(id),
    CONSTRAINT fk_res_activity_slot FOREIGN KEY (activity_slot_id) REFERENCES activity_slots(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    category ENUM('limpieza', 'transporte', 'despensa', 'otro') NOT NULL DEFAULT 'otro',
    description TEXT NULL,
    base_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS stay_service_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reservation_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    details JSON NULL,
    quantity SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    quoted_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('requested', 'accepted', 'prepared', 'completed', 'cancelled') NOT NULL DEFAULT 'requested',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_service_request_reservation FOREIGN KEY (reservation_id) REFERENCES reservations(id),
    CONSTRAINT fk_service_request_service FOREIGN KEY (service_id) REFERENCES services(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS guest_visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    reservation_id BIGINT UNSIGNED NOT NULL,
    checked_in_at DATETIME NOT NULL,
    checked_out_at DATETIME NULL,
    points_awarded INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_visit_reservation (reservation_id),
    CONSTRAINT fk_visit_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_visit_reservation FOREIGN KEY (reservation_id) REFERENCES reservations(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS loyalty_ledger (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    visit_id BIGINT UNSIGNED NOT NULL,
    points INT NOT NULL,
    reason VARCHAR(160) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_loyalty_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_loyalty_visit FOREIGN KEY (visit_id) REFERENCES guest_visits(id)
) ENGINE=InnoDB;

-- La disponibilidad se deriva de reservas activas (pending_payment, payment_submitted, confirmed).
-- Al crear un apartado, hold_expires_at = NOW() + INTERVAL 3 DAY.
-- Las reservas pending_payment vencidas deben cambiar a expired y liberar sus fechas.

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS failed_logins TINYINT UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS locked_until DATETIME NULL;

ALTER TABLE accommodations
    ADD COLUMN IF NOT EXISTS is_active BOOLEAN NOT NULL DEFAULT TRUE,
    ADD COLUMN IF NOT EXISTS bedrooms SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS bathrooms SMALLINT UNSIGNED NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS accommodation_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accommodation_id BIGINT UNSIGNED NOT NULL,
    path VARCHAR(500) NOT NULL,
    FOREIGN KEY (accommodation_id) REFERENCES accommodations(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activity_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    requested_date DATE NOT NULL,
    participants SMALLINT UNSIGNED NOT NULL,
    status ENUM('pending', 'confirmed', 'declined') NOT NULL DEFAULT 'pending',
    response VARCHAR(500) NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_activity_request (activity_id, user_id, requested_date),
    INDEX idx_activity_requests_status (status, requested_date),
    FOREIGN KEY (activity_id) REFERENCES activities(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
) ENGINE=InnoDB;

ALTER TABLE activities ADD COLUMN IF NOT EXISTS catalog_section
    ENUM('acuatica', 'aventura', 'cultura', 'aerea') NULL;

ALTER TABLE activities MODIFY COLUMN catalog_section
    ENUM('acuatica', 'aventura', 'cultura', 'aerea') NULL;

ALTER TABLE activity_requests
    ADD COLUMN IF NOT EXISTS requested_time TIME NULL,
    ADD COLUMN IF NOT EXISTS adults SMALLINT UNSIGNED NULL,
    ADD COLUMN IF NOT EXISTS children SMALLINT UNSIGNED NULL;
