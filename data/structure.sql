CREATE TABLE products (
    id BIGINT NOT NULL PRIMARY KEY,
    external_id int NULL,
    original_data text NOT NULL,
    processed_data text NULL,
    is_imported BIT DEFAULT FALSE,
    last_status_change TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    imported_at TIMESTAMP NULL DEFAULT NULL
)