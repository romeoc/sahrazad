CREATE TABLE products (
    id BIGINT NOT NULL PRIMARY KEY,
    original_data text NOT NULL,
    processed_data text NULL,
    is_imported BIT DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    imported_at TIMESTAMP NULL DEFAULT NULL
)