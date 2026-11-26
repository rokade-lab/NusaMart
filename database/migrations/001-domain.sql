CREATE TABLE buyers (
 id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
 username text NOT NULL UNIQUE, password_hash text NOT NULL, active boolean NOT NULL DEFAULT true
);
CREATE TABLE products (
 id text PRIMARY KEY, name text NOT NULL, description text NOT NULL,
 price bigint NOT NULL CHECK(price >= 0), currency text NOT NULL DEFAULT 'IDR_DUMMY' CHECK(currency = 'IDR_DUMMY'), active boolean NOT NULL DEFAULT true
);
CREATE TABLE carts (
 id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY, buyer_id bigint NOT NULL REFERENCES buyers(id),
 status text NOT NULL DEFAULT 'active' CHECK(status IN ('active','consumed')), version bigint NOT NULL DEFAULT 1 CHECK(version > 0)
);
CREATE UNIQUE INDEX buyer_active_cart ON carts(buyer_id) WHERE status='active';
CREATE TABLE cart_items (
 cart_id bigint NOT NULL REFERENCES carts(id) ON DELETE CASCADE, product_id text NOT NULL REFERENCES products(id),
 quantity integer NOT NULL CHECK(quantity BETWEEN 1 AND 20), PRIMARY KEY(cart_id,product_id)
);
CREATE TABLE orders (
 id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY, buyer_id bigint NOT NULL REFERENCES buyers(id), cart_id bigint NOT NULL UNIQUE REFERENCES carts(id),
 total bigint NOT NULL CHECK(total >= 0), currency text NOT NULL DEFAULT 'IDR_DUMMY' CHECK(currency='IDR_DUMMY'),
 status text NOT NULL DEFAULT 'queued' CHECK(status IN ('queued','processing','completed','failed')),
 created_at timestamptz NOT NULL DEFAULT now(), updated_at timestamptz NOT NULL DEFAULT now()
);
CREATE TABLE order_items (
 order_id bigint NOT NULL REFERENCES orders(id), product_id text NOT NULL, name text NOT NULL,
 price bigint NOT NULL CHECK(price>=0), quantity integer NOT NULL CHECK(quantity BETWEEN 1 AND 20),
 total bigint NOT NULL CHECK(total=price*quantity), PRIMARY KEY(order_id,product_id)
);
CREATE TABLE checkout_requests (
 buyer_id bigint NOT NULL REFERENCES buyers(id), checkout_key text NOT NULL CHECK(length(checkout_key) BETWEEN 1 AND 128),
 cart_id bigint NOT NULL REFERENCES carts(id), cart_version bigint NOT NULL CHECK(cart_version>0),
 fingerprint text NOT NULL, order_id bigint NOT NULL REFERENCES orders(id), result jsonb NOT NULL,
 PRIMARY KEY(buyer_id,checkout_key)
);
CREATE TABLE jobs (
 id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY, order_id bigint NOT NULL UNIQUE REFERENCES orders(id),
 state text NOT NULL DEFAULT 'queued' CHECK(state IN ('queued','processing','completed','failed')),
 attempts integer NOT NULL DEFAULT 0 CHECK(attempts BETWEEN 0 AND 3), available_at timestamptz NOT NULL DEFAULT now(),
 lease_until timestamptz, lease_token text, error_code text CHECK(error_code IS NULL OR error_code ~ '^[A-Z_]{1,64}$')
);
CREATE INDEX ready_jobs ON jobs(state,available_at);
CREATE TABLE buyer_reports (
 buyer_id bigint PRIMARY KEY REFERENCES buyers(id), completed_count bigint NOT NULL CHECK(completed_count>=0),
 completed_total bigint NOT NULL CHECK(completed_total>=0), updated_at timestamptz NOT NULL DEFAULT now(), generation_id text NOT NULL
);
CREATE TABLE seed_versions (version text PRIMARY KEY, installed_at timestamptz NOT NULL DEFAULT now());
