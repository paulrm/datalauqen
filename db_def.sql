-- Table: public.test

-- DROP TABLE public.test;

CREATE TABLE public.test
(
    fecha date NOT NULL,
    valor numeric(5, 1),
    CONSTRAINT test_pkey PRIMARY KEY (fecha)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.test
    OWNER to postgres;
	
	

-- FUNCTION: public.merge_db(date, numeric)

-- DROP FUNCTION public.merge_db(date, numeric);

CREATE OR REPLACE FUNCTION public.merge_db(
	key date,
	data numeric)
RETURNS void
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE 
    ROWS 0
AS $BODY$

BEGIN
    LOOP
        -- first try to update the key
        -- note that "a" must be unique
        UPDATE test SET valor = data WHERE fecha = key;
        IF found THEN
            RETURN;
        END IF;
        -- not there, so try to insert the key
        -- if someone else inserts the same key concurrently,
        -- we could get a unique-key failure
        BEGIN
            INSERT INTO test(fecha,valor) VALUES (key, data);
            RETURN;
        EXCEPTION WHEN unique_violation THEN
            -- do nothing, and loop to try the UPDATE again
        END;
    END LOOP;
END;

$BODY$;

ALTER FUNCTION public.merge_db(date, numeric)
    OWNER TO postgres;

