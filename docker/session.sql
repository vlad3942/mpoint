SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;


CREATE DATABASE session WITH TEMPLATE = template0 ENCODING = 'UTF8';
ALTER DATABASE session OWNER TO postgres;

\connect session

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 176 (class 3079 OID 11893)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2064 (class 0 OID 0)
-- Dependencies: 176
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- TOC entry 177 (class 1255 OID 41939)
-- Name: update_table_proc(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION update_table_proc() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
 NEW.Modified := NOW();
 
 RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_table_proc() OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 172 (class 1259 OID 41940)
-- Name: active_tbl; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE active_tbl (
    id integer NOT NULL,
    gid integer NOT NULL,
    data text,
    expiry timestamp without time zone DEFAULT now(),
    created date DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE active_tbl OWNER TO postgres;

--
-- TOC entry 173 (class 1259 OID 41950)
-- Name: active_tbl_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE active_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE active_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 2066 (class 0 OID 0)
-- Dependencies: 173
-- Name: active_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE active_tbl_id_seq OWNED BY active_tbl.id;


--
-- TOC entry 174 (class 1259 OID 41952)
-- Name: generated_tbl; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE generated_tbl (
    id integer NOT NULL,
    hash character varying(40) DEFAULT ''::character varying NOT NULL,
    ip character varying(15) NOT NULL,
    created date DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE generated_tbl OWNER TO postgres;

--
-- TOC entry 175 (class 1259 OID 41959)
-- Name: generated_tbl_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE generated_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE generated_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 2069 (class 0 OID 0)
-- Dependencies: 175
-- Name: generated_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE generated_tbl_id_seq OWNED BY generated_tbl.id;


--
-- TOC entry 1929 (class 2604 OID 41961)
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY active_tbl ALTER COLUMN id SET DEFAULT nextval('active_tbl_id_seq'::regclass);


--
-- TOC entry 1934 (class 2604 OID 41962)
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY generated_tbl ALTER COLUMN id SET DEFAULT nextval('generated_tbl_id_seq'::regclass);


--
-- TOC entry 1937 (class 2606 OID 41964)
-- Name: active_pk; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY active_tbl
    ADD CONSTRAINT active_pk PRIMARY KEY (id);


--
-- TOC entry 1940 (class 2606 OID 41966)
-- Name: generated_pk; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY generated_tbl
    ADD CONSTRAINT generated_pk PRIMARY KEY (id);


--
-- TOC entry 1944 (class 2606 OID 41968)
-- Name: hash_uq; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY generated_tbl
    ADD CONSTRAINT hash_uq UNIQUE (hash, ip);


--
-- TOC entry 1935 (class 1259 OID 41969)
-- Name: active_expiry; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX active_expiry ON active_tbl USING btree (expiry);


--
-- TOC entry 1938 (class 1259 OID 41970)
-- Name: active_uq; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX active_uq ON active_tbl USING btree (gid, enabled, expiry);


--
-- TOC entry 1941 (class 1259 OID 41971)
-- Name: generated_uq; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX generated_uq ON generated_tbl USING btree (id, ip, enabled);


--
-- TOC entry 1942 (class 1259 OID 41972)
-- Name: hash_enabled_uq; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX hash_enabled_uq ON generated_tbl USING btree (hash, ip, enabled);


--
-- TOC entry 1946 (class 2620 OID 41973)
-- Name: update_active; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_active BEFORE UPDATE ON active_tbl FOR EACH ROW EXECUTE PROCEDURE update_table_proc();


--
-- TOC entry 1947 (class 2620 OID 41974)
-- Name: update_generated; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_generated BEFORE UPDATE ON generated_tbl FOR EACH ROW EXECUTE PROCEDURE update_table_proc();


--
-- TOC entry 1945 (class 2606 OID 41975)
-- Name: a2g_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY active_tbl
    ADD CONSTRAINT a2g_fk FOREIGN KEY (gid) REFERENCES generated_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;

