--
-- PostgreSQL database dump
--

-- Dumped from database version 9.2.2
-- Dumped by pg_dump version 9.3.5
-- Started on 2014-11-17 14:27:04 CET

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;


--
-- TOC entry 14 (class 2615 OID 20082)
-- Name: system; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE OR REPLACE FUNCTION Nextvalue(varchar) RETURNS integer LANGUAGE plpgsql
AS $BODY$
DECLARE
	-- Declare aliases for input
	sequence ALIAS FOR $1;
	num INT4;
BEGIN
	EXECUTE 'SELECT Nextval('''|| sequence || ''')' INTO num;

	RETURN num;
END;
$BODY$;

CREATE OR REPLACE VIEW Public.DUAL AS SELECT E'Provides compatibility with Oracle when selecting from functions.\nUse "SELECT [FUNCTION] FROM DUAL" rather than "SELECT [FUNCTION]"';

GRANT SELECT ON TABLE Public.DUAL TO postgres;


CREATE SCHEMA system;


ALTER SCHEMA system OWNER TO postgres;

SET search_path = system, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 217 (class 1259 OID 10765916)
-- Name: card_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE card_tbl (
  id integer NOT NULL,
  name character varying(50),
  logo bytea,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  "position" integer,
  minlength integer,
  maxlength integer,
  cvclength integer
);


ALTER TABLE system.card_tbl OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 10765925)
-- Name: card_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE card_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.card_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3148 (class 0 OID 0)
-- Dependencies: 218
-- Name: card_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE card_tbl_id_seq OWNED BY card_tbl.id;


--
-- TOC entry 246 (class 1259 OID 13042031)
-- Name: cardprefix_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE cardprefix_tbl (
  id integer NOT NULL,
  cardid integer NOT NULL,
  min bigint,
  max bigint,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.cardprefix_tbl OWNER TO postgres;

--
-- TOC entry 245 (class 1259 OID 13042029)
-- Name: cardprefix_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE cardprefix_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.cardprefix_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3150 (class 0 OID 0)
-- Dependencies: 245
-- Name: cardprefix_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE cardprefix_tbl_id_seq OWNED BY cardprefix_tbl.id;


--
-- TOC entry 219 (class 1259 OID 10765927)
-- Name: cardpricing_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE cardpricing_tbl (
  id integer NOT NULL,
  pricepointid integer,
  cardid integer,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.cardpricing_tbl OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 10765933)
-- Name: cardpricing_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE cardpricing_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.cardpricing_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3153 (class 0 OID 0)
-- Dependencies: 220
-- Name: cardpricing_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE cardpricing_tbl_id_seq OWNED BY cardpricing_tbl.id;


--
-- TOC entry 221 (class 1259 OID 10765935)
-- Name: country_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE country_tbl (
  id integer NOT NULL,
  name character varying(100),
  currency character(3),
  minmob character varying(15),
  maxmob character varying(15),
  channel character varying(10),
  priceformat character varying(18),
  decimals integer,
  addr_lookup boolean DEFAULT false,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  doi boolean DEFAULT false,
  maxbalance integer,
  mintransfer integer,
  symbol character varying(3),
  add_card_amount integer,
  max_psms_amount integer,
  min_pwd_amount integer,
  min_2fa_amount integer
);


ALTER TABLE system.country_tbl OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 10765943)
-- Name: country_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE country_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.country_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3155 (class 0 OID 0)
-- Dependencies: 222
-- Name: country_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE country_tbl_id_seq OWNED BY country_tbl.id;


--
-- TOC entry 223 (class 1259 OID 10765945)
-- Name: depositoption_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE depositoption_tbl (
  id integer NOT NULL,
  countryid integer NOT NULL,
  amount integer,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.depositoption_tbl OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 10765951)
-- Name: depositoption_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE depositoption_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.depositoption_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3157 (class 0 OID 0)
-- Dependencies: 224
-- Name: depositoption_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE depositoption_tbl_id_seq OWNED BY depositoption_tbl.id;


--
-- TOC entry 225 (class 1259 OID 10765953)
-- Name: fee_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE fee_tbl (
  id integer NOT NULL,
  typeid integer NOT NULL,
  fromid integer NOT NULL,
  toid integer NOT NULL,
  minfee integer,
  basefee integer,
  share double precision,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.fee_tbl OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 10765959)
-- Name: fee_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE fee_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.fee_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3160 (class 0 OID 0)
-- Dependencies: 226
-- Name: fee_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE fee_tbl_id_seq OWNED BY fee_tbl.id;


--
-- TOC entry 227 (class 1259 OID 10765961)
-- Name: feetype_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE feetype_tbl (
  id integer NOT NULL,
  name character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.feetype_tbl OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 10765967)
-- Name: feetype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE feetype_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.feetype_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3163 (class 0 OID 0)
-- Dependencies: 228
-- Name: feetype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE feetype_tbl_id_seq OWNED BY feetype_tbl.id;


--
-- TOC entry 229 (class 1259 OID 10765969)
-- Name: flow_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE flow_tbl (
  id integer NOT NULL,
  name character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.flow_tbl OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 10765975)
-- Name: flow_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE flow_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.flow_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3165 (class 0 OID 0)
-- Dependencies: 230
-- Name: flow_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE flow_tbl_id_seq OWNED BY flow_tbl.id;


--
-- TOC entry 231 (class 1259 OID 10765977)
-- Name: iprange_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE iprange_tbl (
  id integer NOT NULL,
  countryid integer NOT NULL,
  min bigint,
  max bigint,
  country character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.iprange_tbl OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 10765983)
-- Name: iprange_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE iprange_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.iprange_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3167 (class 0 OID 0)
-- Dependencies: 232
-- Name: iprange_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE iprange_tbl_id_seq OWNED BY iprange_tbl.id;


--
-- TOC entry 265 (class 1259 OID 14351989)
-- Name: postalcode_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE postalcode_tbl (
  id integer NOT NULL,
  stateid integer NOT NULL,
  code character varying(10),
  city character varying(50),
  latitude real,
  longitude real,
  utc_offset integer,
  vat real DEFAULT 0.0,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.postalcode_tbl OWNER TO postgres;

--
-- TOC entry 264 (class 1259 OID 14351987)
-- Name: postalcode_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE postalcode_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.postalcode_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3169 (class 0 OID 0)
-- Dependencies: 264
-- Name: postalcode_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE postalcode_tbl_id_seq OWNED BY postalcode_tbl.id;


--
-- TOC entry 233 (class 1259 OID 10765985)
-- Name: pricepoint_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE pricepoint_tbl (
  id integer NOT NULL,
  countryid integer,
  amount integer,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.pricepoint_tbl OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 10765991)
-- Name: pricepoint_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE pricepoint_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.pricepoint_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3172 (class 0 OID 0)
-- Dependencies: 234
-- Name: pricepoint_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE pricepoint_tbl_id_seq OWNED BY pricepoint_tbl.id;


--
-- TOC entry 235 (class 1259 OID 10765993)
-- Name: psp_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE psp_tbl (
  id integer NOT NULL,
  name character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.psp_tbl OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 10765999)
-- Name: psp_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE psp_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.psp_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3174 (class 0 OID 0)
-- Dependencies: 236
-- Name: psp_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE psp_tbl_id_seq OWNED BY psp_tbl.id;


--
-- TOC entry 237 (class 1259 OID 10766001)
-- Name: pspcard_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE pspcard_tbl (
  id integer NOT NULL,
  cardid integer NOT NULL,
  pspid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.pspcard_tbl OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 10766007)
-- Name: pspcard_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE pspcard_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.pspcard_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3176 (class 0 OID 0)
-- Dependencies: 238
-- Name: pspcard_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE pspcard_tbl_id_seq OWNED BY pspcard_tbl.id;


--
-- TOC entry 239 (class 1259 OID 10766009)
-- Name: pspcurrency_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE pspcurrency_tbl (
  id integer NOT NULL,
  countryid integer NOT NULL,
  pspid integer NOT NULL,
  name character(3),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.pspcurrency_tbl OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 10766015)
-- Name: pspcurrency_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE pspcurrency_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.pspcurrency_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3178 (class 0 OID 0)
-- Dependencies: 240
-- Name: pspcurrency_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE pspcurrency_tbl_id_seq OWNED BY pspcurrency_tbl.id;


--
-- TOC entry 241 (class 1259 OID 10766017)
-- Name: shipping_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE shipping_tbl (
  id integer NOT NULL,
  name character varying(50),
  logourl character varying(100),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.shipping_tbl OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 10766023)
-- Name: shipping_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE shipping_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.shipping_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3180 (class 0 OID 0)
-- Dependencies: 242
-- Name: shipping_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE shipping_tbl_id_seq OWNED BY shipping_tbl.id;


--
-- TOC entry 263 (class 1259 OID 14351971)
-- Name: state_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE state_tbl (
  id integer NOT NULL,
  countryid integer NOT NULL,
  name character varying(50),
  code character varying(5),
  vat real DEFAULT 0.0,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.state_tbl OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 14351969)
-- Name: state_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE state_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.state_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3183 (class 0 OID 0)
-- Dependencies: 262
-- Name: state_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE state_tbl_id_seq OWNED BY state_tbl.id;


--
-- TOC entry 243 (class 1259 OID 10766025)
-- Name: type_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE type_tbl (
  id integer NOT NULL,
  name character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.type_tbl OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 10766031)
-- Name: type_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE type_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.type_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3186 (class 0 OID 0)
-- Dependencies: 244
-- Name: type_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE type_tbl_id_seq OWNED BY type_tbl.id;


--
-- TOC entry 249 (class 1259 OID 13141804)
-- Name: urltype_tbl; Type: TABLE; Schema: system; Owner: postgres; Tablespace:
--

CREATE TABLE urltype_tbl (
  id integer NOT NULL,
  name character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE system.urltype_tbl OWNER TO postgres;

--
-- TOC entry 248 (class 1259 OID 13141802)
-- Name: urltype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: postgres
--

CREATE SEQUENCE urltype_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE system.urltype_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3188 (class 0 OID 0)
-- Dependencies: 248
-- Name: urltype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: postgres
--

ALTER SEQUENCE urltype_tbl_id_seq OWNED BY urltype_tbl.id;


--
-- TOC entry 2874 (class 2604 OID 20275)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY card_tbl ALTER COLUMN id SET DEFAULT nextval('card_tbl_id_seq'::regclass);


--
-- TOC entry 2932 (class 2604 OID 20276)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY cardprefix_tbl ALTER COLUMN id SET DEFAULT nextval('cardprefix_tbl_id_seq'::regclass);


--
-- TOC entry 2878 (class 2604 OID 20277)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY cardpricing_tbl ALTER COLUMN id SET DEFAULT nextval('cardpricing_tbl_id_seq'::regclass);


--
-- TOC entry 2884 (class 2604 OID 20278)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY country_tbl ALTER COLUMN id SET DEFAULT nextval('country_tbl_id_seq'::regclass);


--
-- TOC entry 2888 (class 2604 OID 20279)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY depositoption_tbl ALTER COLUMN id SET DEFAULT nextval('depositoption_tbl_id_seq'::regclass);


--
-- TOC entry 2892 (class 2604 OID 20280)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY fee_tbl ALTER COLUMN id SET DEFAULT nextval('fee_tbl_id_seq'::regclass);


--
-- TOC entry 2896 (class 2604 OID 20281)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY feetype_tbl ALTER COLUMN id SET DEFAULT nextval('feetype_tbl_id_seq'::regclass);


--
-- TOC entry 2900 (class 2604 OID 20282)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY flow_tbl ALTER COLUMN id SET DEFAULT nextval('flow_tbl_id_seq'::regclass);


--
-- TOC entry 2904 (class 2604 OID 20283)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY iprange_tbl ALTER COLUMN id SET DEFAULT nextval('iprange_tbl_id_seq'::regclass);


--
-- TOC entry 2942 (class 2604 OID 14351995)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY postalcode_tbl ALTER COLUMN id SET DEFAULT nextval('postalcode_tbl_id_seq'::regclass);


--
-- TOC entry 2908 (class 2604 OID 20284)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY pricepoint_tbl ALTER COLUMN id SET DEFAULT nextval('pricepoint_tbl_id_seq'::regclass);


--
-- TOC entry 2912 (class 2604 OID 20285)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY psp_tbl ALTER COLUMN id SET DEFAULT nextval('psp_tbl_id_seq'::regclass);


--
-- TOC entry 2916 (class 2604 OID 20286)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY pspcard_tbl ALTER COLUMN id SET DEFAULT nextval('pspcard_tbl_id_seq'::regclass);


--
-- TOC entry 2920 (class 2604 OID 20287)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY pspcurrency_tbl ALTER COLUMN id SET DEFAULT nextval('pspcurrency_tbl_id_seq'::regclass);


--
-- TOC entry 2924 (class 2604 OID 20288)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY shipping_tbl ALTER COLUMN id SET DEFAULT nextval('shipping_tbl_id_seq'::regclass);


--
-- TOC entry 2937 (class 2604 OID 14351977)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY state_tbl ALTER COLUMN id SET DEFAULT nextval('state_tbl_id_seq'::regclass);


--
-- TOC entry 2928 (class 2604 OID 20289)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY type_tbl ALTER COLUMN id SET DEFAULT nextval('type_tbl_id_seq'::regclass);


--
-- TOC entry 2936 (class 2604 OID 20290)
-- Name: id; Type: DEFAULT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY urltype_tbl ALTER COLUMN id SET DEFAULT nextval('urltype_tbl_id_seq'::regclass);


--
-- TOC entry 2948 (class 2606 OID 20328)
-- Name: card_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY card_tbl
ADD CONSTRAINT card_pk PRIMARY KEY (id);


--
-- TOC entry 2997 (class 2606 OID 20329)
-- Name: cardprefix_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY cardprefix_tbl
ADD CONSTRAINT cardprefix_pk PRIMARY KEY (id);


--
-- TOC entry 2951 (class 2606 OID 20330)
-- Name: cardpricing_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY cardpricing_tbl
ADD CONSTRAINT cardpricing_pk PRIMARY KEY (id);


--
-- TOC entry 2953 (class 2606 OID 20331)
-- Name: cardpricing_uq; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY cardpricing_tbl
ADD CONSTRAINT cardpricing_uq UNIQUE (pricepointid, cardid);


--
-- TOC entry 2955 (class 2606 OID 20332)
-- Name: country_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY country_tbl
ADD CONSTRAINT country_pk PRIMARY KEY (id);


--
-- TOC entry 2958 (class 2606 OID 20333)
-- Name: depositoption_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY depositoption_tbl
ADD CONSTRAINT depositoption_pk PRIMARY KEY (id);


--
-- TOC entry 2960 (class 2606 OID 20334)
-- Name: depositoption_uq; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY depositoption_tbl
ADD CONSTRAINT depositoption_uq UNIQUE (countryid, amount);


--
-- TOC entry 2962 (class 2606 OID 20335)
-- Name: fee_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY fee_tbl
ADD CONSTRAINT fee_pk PRIMARY KEY (id);


--
-- TOC entry 2964 (class 2606 OID 20336)
-- Name: fee_uq; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY fee_tbl
ADD CONSTRAINT fee_uq UNIQUE (typeid, fromid, toid);


--
-- TOC entry 2966 (class 2606 OID 20337)
-- Name: feetype_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY feetype_tbl
ADD CONSTRAINT feetype_pk PRIMARY KEY (id);


--
-- TOC entry 2969 (class 2606 OID 20338)
-- Name: flow_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY flow_tbl
ADD CONSTRAINT flow_pk PRIMARY KEY (id);


--
-- TOC entry 2972 (class 2606 OID 20339)
-- Name: iprange_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY iprange_tbl
ADD CONSTRAINT iprange_pk PRIMARY KEY (id);


--
-- TOC entry 2974 (class 2606 OID 20340)
-- Name: iprange_uq; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY iprange_tbl
ADD CONSTRAINT iprange_uq UNIQUE (min, max);


--
-- TOC entry 3005 (class 2606 OID 14351998)
-- Name: postalcode_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY postalcode_tbl
ADD CONSTRAINT postalcode_pk PRIMARY KEY (id);


--
-- TOC entry 2976 (class 2606 OID 20341)
-- Name: pricepoint_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY pricepoint_tbl
ADD CONSTRAINT pricepoint_pk PRIMARY KEY (id);


--
-- TOC entry 2978 (class 2606 OID 20342)
-- Name: pricepoint_uq; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY pricepoint_tbl
ADD CONSTRAINT pricepoint_uq UNIQUE (countryid, amount);


--
-- TOC entry 2980 (class 2606 OID 20343)
-- Name: psp_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY psp_tbl
ADD CONSTRAINT psp_pk PRIMARY KEY (id);


--
-- TOC entry 2983 (class 2606 OID 20344)
-- Name: pspcard_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY pspcard_tbl
ADD CONSTRAINT pspcard_pk PRIMARY KEY (id);


--
-- TOC entry 2985 (class 2606 OID 20345)
-- Name: pspcard_uq; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY pspcard_tbl
ADD CONSTRAINT pspcard_uq UNIQUE (cardid, pspid);


--
-- TOC entry 2987 (class 2606 OID 20346)
-- Name: pspcurrency_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY pspcurrency_tbl
ADD CONSTRAINT pspcurrency_pk PRIMARY KEY (id);


--
-- TOC entry 2989 (class 2606 OID 20347)
-- Name: pspcurrency_uq; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY pspcurrency_tbl
ADD CONSTRAINT pspcurrency_uq UNIQUE (countryid, pspid);


--
-- TOC entry 2991 (class 2606 OID 20348)
-- Name: shipping_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY shipping_tbl
ADD CONSTRAINT shipping_pk PRIMARY KEY (id);


--
-- TOC entry 3002 (class 2606 OID 14351980)
-- Name: state_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY state_tbl
ADD CONSTRAINT state_pk PRIMARY KEY (id);


--
-- TOC entry 2994 (class 2606 OID 20349)
-- Name: type_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY type_tbl
ADD CONSTRAINT type_pk PRIMARY KEY (id);


--
-- TOC entry 2999 (class 2606 OID 20350)
-- Name: urltype_pk; Type: CONSTRAINT; Schema: system; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY urltype_tbl
ADD CONSTRAINT urltype_pk PRIMARY KEY (id);


--
-- TOC entry 2949 (class 1259 OID 10766191)
-- Name: card_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX card_uq ON card_tbl USING btree (upper((name)::text));


--
-- TOC entry 2956 (class 1259 OID 14347228)
-- Name: country_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX country_uq ON country_tbl USING btree (upper((name)::text));


--
-- TOC entry 2967 (class 1259 OID 10766193)
-- Name: feetype_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX feetype_uq ON feetype_tbl USING btree (upper((name)::text));


--
-- TOC entry 2970 (class 1259 OID 10766194)
-- Name: flow_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX flow_uq ON flow_tbl USING btree (upper((name)::text));


--
-- TOC entry 3006 (class 1259 OID 14352004)
-- Name: postalcode_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX postalcode_uq ON postalcode_tbl USING btree (latitude, longitude, code, lower((city)::text));


--
-- TOC entry 2981 (class 1259 OID 10766195)
-- Name: psp_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX psp_uq ON psp_tbl USING btree (upper((name)::text));


--
-- TOC entry 2992 (class 1259 OID 10766196)
-- Name: shipping_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX shipping_uq ON shipping_tbl USING btree (upper((name)::text));


--
-- TOC entry 3003 (class 1259 OID 14351986)
-- Name: state_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX state_uq ON state_tbl USING btree (countryid, upper((code)::text));


--
-- TOC entry 2995 (class 1259 OID 10766197)
-- Name: type_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX type_uq ON type_tbl USING btree (upper((name)::text));


--
-- TOC entry 3000 (class 1259 OID 13141813)
-- Name: urltype_uq; Type: INDEX; Schema: system; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX urltype_uq ON urltype_tbl USING btree (lower((name)::text));


--
-- TOC entry 3019 (class 2606 OID 20644)
-- Name: cardprefix2card_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY cardprefix_tbl
ADD CONSTRAINT cardprefix2card_fk FOREIGN KEY (cardid) REFERENCES card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3007 (class 2606 OID 20649)
-- Name: cardpricing2card_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY cardpricing_tbl
ADD CONSTRAINT cardpricing2card_fk FOREIGN KEY (cardid) REFERENCES card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3008 (class 2606 OID 20654)
-- Name: cardpricing2pricepoint_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY cardpricing_tbl
ADD CONSTRAINT cardpricing2pricepoint_fk FOREIGN KEY (pricepointid) REFERENCES pricepoint_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3009 (class 2606 OID 20659)
-- Name: depositoption2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY depositoption_tbl
ADD CONSTRAINT depositoption2country_fk FOREIGN KEY (countryid) REFERENCES country_tbl(id) ON UPDATE CASCADE;


--
-- TOC entry 3010 (class 2606 OID 20664)
-- Name: fee2fromcountry_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY fee_tbl
ADD CONSTRAINT fee2fromcountry_fk FOREIGN KEY (fromid) REFERENCES country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3011 (class 2606 OID 20669)
-- Name: fee2tocountry_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY fee_tbl
ADD CONSTRAINT fee2tocountry_fk FOREIGN KEY (toid) REFERENCES country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3012 (class 2606 OID 20674)
-- Name: fee2type_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY fee_tbl
ADD CONSTRAINT fee2type_fk FOREIGN KEY (typeid) REFERENCES feetype_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3013 (class 2606 OID 20679)
-- Name: iprange2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY iprange_tbl
ADD CONSTRAINT iprange2country_fk FOREIGN KEY (countryid) REFERENCES country_tbl(id) ON UPDATE CASCADE;


--
-- TOC entry 3021 (class 2606 OID 14351999)
-- Name: postalcode2state_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY postalcode_tbl
ADD CONSTRAINT postalcode2state_fk FOREIGN KEY (stateid) REFERENCES state_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3014 (class 2606 OID 20684)
-- Name: pricepoint2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY pricepoint_tbl
ADD CONSTRAINT pricepoint2country_fk FOREIGN KEY (countryid) REFERENCES country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3015 (class 2606 OID 20689)
-- Name: pspcard2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY pspcard_tbl
ADD CONSTRAINT pspcard2country_fk FOREIGN KEY (cardid) REFERENCES card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3016 (class 2606 OID 20694)
-- Name: pspcard2psp_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY pspcard_tbl
ADD CONSTRAINT pspcard2psp_fk FOREIGN KEY (pspid) REFERENCES psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3017 (class 2606 OID 20699)
-- Name: pspcurrency2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY pspcurrency_tbl
ADD CONSTRAINT pspcurrency2country_fk FOREIGN KEY (countryid) REFERENCES country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3018 (class 2606 OID 20704)
-- Name: pspcurrency2psp_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY pspcurrency_tbl
ADD CONSTRAINT pspcurrency2psp_fk FOREIGN KEY (pspid) REFERENCES psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3020 (class 2606 OID 14351981)
-- Name: state2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: postgres
--

ALTER TABLE ONLY state_tbl
ADD CONSTRAINT state2country_fk FOREIGN KEY (countryid) REFERENCES country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3146 (class 0 OID 0)
-- Dependencies: 14
-- Name: system; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA system FROM PUBLIC;
REVOKE ALL ON SCHEMA system FROM postgres;
GRANT ALL ON SCHEMA system TO postgres;
GRANT USAGE ON SCHEMA system TO postgres;


--
-- TOC entry 3147 (class 0 OID 0)
-- Dependencies: 217
-- Name: card_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE card_tbl FROM PUBLIC;
REVOKE ALL ON TABLE card_tbl FROM postgres;
GRANT ALL ON TABLE card_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE card_tbl TO postgres;


--
-- TOC entry 3149 (class 0 OID 0)
-- Dependencies: 246
-- Name: cardprefix_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE cardprefix_tbl FROM PUBLIC;
REVOKE ALL ON TABLE cardprefix_tbl FROM postgres;
GRANT ALL ON TABLE cardprefix_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE cardprefix_tbl TO postgres;


--
-- TOC entry 3151 (class 0 OID 0)
-- Dependencies: 245
-- Name: cardprefix_tbl_id_seq; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON SEQUENCE cardprefix_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE cardprefix_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE cardprefix_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE cardprefix_tbl_id_seq TO postgres;


--
-- TOC entry 3152 (class 0 OID 0)
-- Dependencies: 219
-- Name: cardpricing_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE cardpricing_tbl FROM PUBLIC;
REVOKE ALL ON TABLE cardpricing_tbl FROM postgres;
GRANT ALL ON TABLE cardpricing_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE cardpricing_tbl TO postgres;


--
-- TOC entry 3154 (class 0 OID 0)
-- Dependencies: 221
-- Name: country_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE country_tbl FROM PUBLIC;
REVOKE ALL ON TABLE country_tbl FROM postgres;
GRANT ALL ON TABLE country_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE country_tbl TO postgres;


--
-- TOC entry 3156 (class 0 OID 0)
-- Dependencies: 223
-- Name: depositoption_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE depositoption_tbl FROM PUBLIC;
REVOKE ALL ON TABLE depositoption_tbl FROM postgres;
GRANT ALL ON TABLE depositoption_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE depositoption_tbl TO postgres;


--
-- TOC entry 3158 (class 0 OID 0)
-- Dependencies: 224
-- Name: depositoption_tbl_id_seq; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON SEQUENCE depositoption_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE depositoption_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE depositoption_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE depositoption_tbl_id_seq TO postgres;


--
-- TOC entry 3159 (class 0 OID 0)
-- Dependencies: 225
-- Name: fee_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE fee_tbl FROM PUBLIC;
REVOKE ALL ON TABLE fee_tbl FROM postgres;
GRANT ALL ON TABLE fee_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE fee_tbl TO postgres;


--
-- TOC entry 3161 (class 0 OID 0)
-- Dependencies: 226
-- Name: fee_tbl_id_seq; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON SEQUENCE fee_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE fee_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE fee_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE fee_tbl_id_seq TO postgres;


--
-- TOC entry 3162 (class 0 OID 0)
-- Dependencies: 227
-- Name: feetype_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE feetype_tbl FROM PUBLIC;
REVOKE ALL ON TABLE feetype_tbl FROM postgres;
GRANT ALL ON TABLE feetype_tbl TO postgres;
GRANT SELECT ON TABLE feetype_tbl TO postgres;


--
-- TOC entry 3164 (class 0 OID 0)
-- Dependencies: 229
-- Name: flow_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE flow_tbl FROM PUBLIC;
REVOKE ALL ON TABLE flow_tbl FROM postgres;
GRANT ALL ON TABLE flow_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE flow_tbl TO postgres;


--
-- TOC entry 3166 (class 0 OID 0)
-- Dependencies: 231
-- Name: iprange_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE iprange_tbl FROM PUBLIC;
REVOKE ALL ON TABLE iprange_tbl FROM postgres;
GRANT ALL ON TABLE iprange_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE iprange_tbl TO postgres;


--
-- TOC entry 3168 (class 0 OID 0)
-- Dependencies: 265
-- Name: postalcode_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE postalcode_tbl FROM PUBLIC;
REVOKE ALL ON TABLE postalcode_tbl FROM postgres;
GRANT ALL ON TABLE postalcode_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE postalcode_tbl TO postgres;


--
-- TOC entry 3170 (class 0 OID 0)
-- Dependencies: 264
-- Name: postalcode_tbl_id_seq; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON SEQUENCE postalcode_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE postalcode_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE postalcode_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE postalcode_tbl_id_seq TO postgres;


--
-- TOC entry 3171 (class 0 OID 0)
-- Dependencies: 233
-- Name: pricepoint_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE pricepoint_tbl FROM PUBLIC;
REVOKE ALL ON TABLE pricepoint_tbl FROM postgres;
GRANT ALL ON TABLE pricepoint_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE pricepoint_tbl TO postgres;


--
-- TOC entry 3173 (class 0 OID 0)
-- Dependencies: 235
-- Name: psp_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE psp_tbl FROM PUBLIC;
REVOKE ALL ON TABLE psp_tbl FROM postgres;
GRANT ALL ON TABLE psp_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE psp_tbl TO postgres;


--
-- TOC entry 3175 (class 0 OID 0)
-- Dependencies: 237
-- Name: pspcard_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE pspcard_tbl FROM PUBLIC;
REVOKE ALL ON TABLE pspcard_tbl FROM postgres;
GRANT ALL ON TABLE pspcard_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE pspcard_tbl TO postgres;


--
-- TOC entry 3177 (class 0 OID 0)
-- Dependencies: 239
-- Name: pspcurrency_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE pspcurrency_tbl FROM PUBLIC;
REVOKE ALL ON TABLE pspcurrency_tbl FROM postgres;
GRANT ALL ON TABLE pspcurrency_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE pspcurrency_tbl TO postgres;


--
-- TOC entry 3179 (class 0 OID 0)
-- Dependencies: 241
-- Name: shipping_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE shipping_tbl FROM PUBLIC;
REVOKE ALL ON TABLE shipping_tbl FROM postgres;
GRANT ALL ON TABLE shipping_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE shipping_tbl TO postgres;


--
-- TOC entry 3181 (class 0 OID 0)
-- Dependencies: 242
-- Name: shipping_tbl_id_seq; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON SEQUENCE shipping_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE shipping_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE shipping_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE shipping_tbl_id_seq TO postgres;


--
-- TOC entry 3182 (class 0 OID 0)
-- Dependencies: 263
-- Name: state_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE state_tbl FROM PUBLIC;
REVOKE ALL ON TABLE state_tbl FROM postgres;
GRANT ALL ON TABLE state_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE state_tbl TO postgres;


--
-- TOC entry 3184 (class 0 OID 0)
-- Dependencies: 262
-- Name: state_tbl_id_seq; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON SEQUENCE state_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE state_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE state_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE state_tbl_id_seq TO postgres;


--
-- TOC entry 3185 (class 0 OID 0)
-- Dependencies: 243
-- Name: type_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE type_tbl FROM PUBLIC;
REVOKE ALL ON TABLE type_tbl FROM postgres;
GRANT ALL ON TABLE type_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE type_tbl TO postgres;


--
-- TOC entry 3187 (class 0 OID 0)
-- Dependencies: 249
-- Name: urltype_tbl; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON TABLE urltype_tbl FROM PUBLIC;
REVOKE ALL ON TABLE urltype_tbl FROM postgres;
GRANT ALL ON TABLE urltype_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE urltype_tbl TO postgres;


--
-- TOC entry 3189 (class 0 OID 0)
-- Dependencies: 248
-- Name: urltype_tbl_id_seq; Type: ACL; Schema: system; Owner: postgres
--

REVOKE ALL ON SEQUENCE urltype_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE urltype_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE urltype_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE urltype_tbl_id_seq TO postgres;


-- Completed on 2014-11-17 14:39:42 CET



--
-- TOC entry 10 (class 2615 OID 20079)
-- Name: client; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA client;


ALTER SCHEMA client OWNER TO postgres;

SET search_path = client, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 180 (class 1259 OID 10765729)
-- Name:Client.account_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE Client.account_tbl (
  id integer NOT NULL,
  clientid integer NOT NULL,
  name character varying(50),
  mobile character varying(15),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  markup character varying(5)
);


ALTER TABLE client.account_tbl OWNER TO postgres;

--
-- TOC entry 181 (class 1259 OID 10765735)
-- Name:Client.account_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE Client.account_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.account_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3110 (class 0 OID 0)
-- Dependencies: 181
-- Name:Client.account_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE Client.account_tbl_id_seq OWNED BY Client.account_tbl.id;


--
-- TOC entry 182 (class 1259 OID 10765737)
-- Name: cardaccess_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE cardaccess_tbl (
  id integer NOT NULL,
  clientid integer NOT NULL,
  cardid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  pspid integer NOT NULL,
  countryid integer
);


ALTER TABLE client.cardaccess_tbl OWNER TO postgres;

--
-- TOC entry 183 (class 1259 OID 10765743)
-- Name: cardaccess_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE cardaccess_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.cardaccess_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3113 (class 0 OID 0)
-- Dependencies: 183
-- Name: cardaccess_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE cardaccess_tbl_id_seq OWNED BY cardaccess_tbl.id;


--
-- TOC entry 184 (class 1259 OID 10765745)
-- Name: Client.client_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE Client.client_tbl (
  id integer NOT NULL,
  countryid integer NOT NULL,
  flowid integer NOT NULL,
  name character varying(50),
  username character varying(50),
  passwd character varying(50),
  logourl character varying(255),
  cssurl character varying(255),
  callbackurl character varying(255),
  accepturl character varying(255),
  cancelurl character varying(255),
  maxamount integer,
  lang character(2) DEFAULT 'gb'::bpchar,
  smsrcpt boolean DEFAULT true,
  emailrcpt boolean DEFAULT true,
  method character varying(6) DEFAULT 'mPoint'::character varying,
  terms text,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  mode integer DEFAULT 0,
  auto_capture boolean DEFAULT true,
  send_pspid boolean DEFAULT true,
  store_card integer DEFAULT 0,
  iconurl character varying(255),
  show_all_cards boolean DEFAULT false,
  max_cards integer DEFAULT (-1),
  identification integer DEFAULT 7,
  CONSTRAINT client_chk CHECK ((((method)::text = 'mPoint'::text) OR ((method)::text = 'PSP'::text)))
);


ALTER TABLE Client.client_tbl OWNER TO postgres;

--
-- TOC entry 185 (class 1259 OID 10765764)
-- Name: Client.client_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE Client.client_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE Client.client_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3116 (class 0 OID 0)
-- Dependencies: 185
-- Name: Client.client_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE Client.client_tbl_id_seq OWNED BY Client.client_tbl.id;


--
-- TOC entry 261 (class 1259 OID 14333316)
-- Name: ipaddress_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE ipaddress_tbl (
  id integer NOT NULL,
  clientid integer NOT NULL,
  ipaddress character varying(20),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE client.ipaddress_tbl OWNER TO postgres;

--
-- TOC entry 260 (class 1259 OID 14333314)
-- Name: ipaddress_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE ipaddress_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.ipaddress_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3119 (class 0 OID 0)
-- Dependencies: 260
-- Name: ipaddress_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE ipaddress_tbl_id_seq OWNED BY ipaddress_tbl.id;


--
-- TOC entry 186 (class 1259 OID 10765766)
-- Name: keyword_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE keyword_tbl (
  id integer NOT NULL,
  clientid integer NOT NULL,
  name character varying(50),
  standard boolean DEFAULT false,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE client.keyword_tbl OWNER TO postgres;

--
-- TOC entry 187 (class 1259 OID 10765773)
-- Name: keyword_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE keyword_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.keyword_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3122 (class 0 OID 0)
-- Dependencies: 187
-- Name: keyword_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE keyword_tbl_id_seq OWNED BY keyword_tbl.id;


--
-- TOC entry 188 (class 1259 OID 10765775)
-- Name: merchantaccount_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE merchantaccount_tbl (
  id integer NOT NULL,
  clientid integer NOT NULL,
  pspid integer NOT NULL,
  name character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  username character varying(50),
  passwd character varying(50),
  stored_card boolean
);


ALTER TABLE client.merchantaccount_tbl OWNER TO postgres;

--
-- TOC entry 189 (class 1259 OID 10765781)
-- Name: merchantaccount_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE merchantaccount_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.merchantaccount_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3125 (class 0 OID 0)
-- Dependencies: 189
-- Name: merchantaccount_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE merchantaccount_tbl_id_seq OWNED BY merchantaccount_tbl.id;


--
-- TOC entry 190 (class 1259 OID 10765783)
-- Name: merchantsubaccount_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE merchantsubaccount_tbl (
  id integer NOT NULL,
  accountid integer NOT NULL,
  pspid integer NOT NULL,
  name character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE client.merchantsubaccount_tbl OWNER TO postgres;

--
-- TOC entry 191 (class 1259 OID 10765789)
-- Name: merchantsubaccount_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE merchantsubaccount_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.merchantsubaccount_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3128 (class 0 OID 0)
-- Dependencies: 191
-- Name: merchantsubaccount_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE merchantsubaccount_tbl_id_seq OWNED BY merchantsubaccount_tbl.id;


--
-- TOC entry 192 (class 1259 OID 10765791)
-- Name: product_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE product_tbl (
  id integer NOT NULL,
  keywordid integer NOT NULL,
  name character varying(50),
  quantity integer DEFAULT 1,
  price integer,
  logourl character varying(255),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE client.product_tbl OWNER TO postgres;

--
-- TOC entry 193 (class 1259 OID 10765798)
-- Name: product_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE product_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.product_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3131 (class 0 OID 0)
-- Dependencies: 193
-- Name: product_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE product_tbl_id_seq OWNED BY product_tbl.id;


--
-- TOC entry 194 (class 1259 OID 10765800)
-- Name: shipping_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE shipping_tbl (
  id integer NOT NULL,
  shippingid integer NOT NULL,
  shopid integer NOT NULL,
  cost integer,
  free_ship integer,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE client.shipping_tbl OWNER TO postgres;

--
-- TOC entry 195 (class 1259 OID 10765806)
-- Name: shipping_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE shipping_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.shipping_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3134 (class 0 OID 0)
-- Dependencies: 195
-- Name: shipping_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE shipping_tbl_id_seq OWNED BY shipping_tbl.id;


--
-- TOC entry 196 (class 1259 OID 10765808)
-- Name: shop_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE shop_tbl (
  id integer NOT NULL,
  clientid integer NOT NULL,
  keywordid integer NOT NULL,
  del_date boolean,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE client.shop_tbl OWNER TO postgres;

--
-- TOC entry 197 (class 1259 OID 10765814)
-- Name: shop_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE shop_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.shop_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3137 (class 0 OID 0)
-- Dependencies: 197
-- Name: shop_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE shop_tbl_id_seq OWNED BY shop_tbl.id;


--
-- TOC entry 198 (class 1259 OID 10765816)
-- Name: surepay_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE surepay_tbl (
  id integer NOT NULL,
  clientid integer,
  resend integer,
  notify integer,
  email character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE client.surepay_tbl OWNER TO postgres;

--
-- TOC entry 199 (class 1259 OID 10765822)
-- Name: surepay_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE surepay_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.surepay_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3140 (class 0 OID 0)
-- Dependencies: 199
-- Name: surepay_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE surepay_tbl_id_seq OWNED BY surepay_tbl.id;


--
-- TOC entry 251 (class 1259 OID 13141843)
-- Name: url_tbl; Type: TABLE; Schema: client; Owner: postgres; Tablespace: 
--

CREATE TABLE url_tbl (
  id integer NOT NULL,
  urltypeid integer NOT NULL,
  clientid integer NOT NULL,
  url character varying(255),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE client.url_tbl OWNER TO postgres;

--
-- TOC entry 250 (class 1259 OID 13141841)
-- Name: url_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: postgres
--

CREATE SEQUENCE url_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE client.url_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 3142 (class 0 OID 0)
-- Dependencies: 250
-- Name: url_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: postgres
--

ALTER SEQUENCE url_tbl_id_seq OWNED BY url_tbl.id;


--
-- TOC entry 2868 (class 2604 OID 20254)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY Client.account_tbl ALTER COLUMN id SET DEFAULT nextval('account_tbl_id_seq'::regclass);


--
-- TOC entry 2872 (class 2604 OID 20255)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY cardaccess_tbl ALTER COLUMN id SET DEFAULT nextval('cardaccess_tbl_id_seq'::regclass);


--
-- TOC entry 2885 (class 2604 OID 20256)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY Client.client_tbl ALTER COLUMN id SET DEFAULT nextval('Client.client_tbl_id_seq'::regclass);


--
-- TOC entry 2926 (class 2604 OID 20257)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY ipaddress_tbl ALTER COLUMN id SET DEFAULT nextval('ipaddress_tbl_id_seq'::regclass);


--
-- TOC entry 2893 (class 2604 OID 20258)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY keyword_tbl ALTER COLUMN id SET DEFAULT nextval('keyword_tbl_id_seq'::regclass);


--
-- TOC entry 2897 (class 2604 OID 20259)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY merchantaccount_tbl ALTER COLUMN id SET DEFAULT nextval('merchantaccount_tbl_id_seq'::regclass);


--
-- TOC entry 2901 (class 2604 OID 20260)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY merchantsubaccount_tbl ALTER COLUMN id SET DEFAULT nextval('merchantsubaccount_tbl_id_seq'::regclass);


--
-- TOC entry 2906 (class 2604 OID 20261)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY product_tbl ALTER COLUMN id SET DEFAULT nextval('product_tbl_id_seq'::regclass);


--
-- TOC entry 2910 (class 2604 OID 20262)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY shipping_tbl ALTER COLUMN id SET DEFAULT nextval('shipping_tbl_id_seq'::regclass);


--
-- TOC entry 2914 (class 2604 OID 20263)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY shop_tbl ALTER COLUMN id SET DEFAULT nextval('shop_tbl_id_seq'::regclass);


--
-- TOC entry 2918 (class 2604 OID 20264)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY surepay_tbl ALTER COLUMN id SET DEFAULT nextval('surepay_tbl_id_seq'::regclass);


--
-- TOC entry 2922 (class 2604 OID 20265)
-- Name: id; Type: DEFAULT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY url_tbl ALTER COLUMN id SET DEFAULT nextval('url_tbl_id_seq'::regclass);


--
-- TOC entry 2928 (class 2606 OID 20299)
-- Name: account_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY Client.account_tbl
ADD CONSTRAINT account_pk PRIMARY KEY (id);


--
-- TOC entry 2930 (class 2606 OID 20300)
-- Name: account_uq; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY Client.account_tbl
ADD CONSTRAINT account_uq UNIQUE (clientid, mobile);


--
-- TOC entry 2933 (class 2606 OID 20301)
-- Name: cardaccess_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY cardaccess_tbl
ADD CONSTRAINT cardaccess_pk PRIMARY KEY (id);


--
-- TOC entry 2935 (class 2606 OID 20302)
-- Name: cardaccess_uq; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY cardaccess_tbl
ADD CONSTRAINT cardaccess_uq UNIQUE (clientid, cardid, pspid);


--
-- TOC entry 2937 (class 2606 OID 20303)
-- Name: client_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY Client.client_tbl
ADD CONSTRAINT client_pk PRIMARY KEY (id);


--
-- TOC entry 2965 (class 2606 OID 20304)
-- Name: ipaddress_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ipaddress_tbl
ADD CONSTRAINT ipaddress_pk PRIMARY KEY (id);


--
-- TOC entry 2939 (class 2606 OID 20305)
-- Name: keyword_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY keyword_tbl
ADD CONSTRAINT keyword_pk PRIMARY KEY (id);


--
-- TOC entry 2942 (class 2606 OID 20306)
-- Name: merchantaccount_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY merchantaccount_tbl
ADD CONSTRAINT merchantaccount_pk PRIMARY KEY (id);


--
-- TOC entry 2946 (class 2606 OID 20307)
-- Name: merchantsubaccount_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY merchantsubaccount_tbl
ADD CONSTRAINT merchantsubaccount_pk PRIMARY KEY (id);


--
-- TOC entry 2948 (class 2606 OID 20308)
-- Name: merchantsubaccount_uq; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY merchantsubaccount_tbl
ADD CONSTRAINT merchantsubaccount_uq UNIQUE (accountid, pspid);


--
-- TOC entry 2950 (class 2606 OID 20309)
-- Name: product_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY product_tbl
ADD CONSTRAINT product_pk PRIMARY KEY (id);


--
-- TOC entry 2952 (class 2606 OID 20310)
-- Name: shipping_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY shipping_tbl
ADD CONSTRAINT shipping_pk PRIMARY KEY (id);


--
-- TOC entry 2954 (class 2606 OID 20311)
-- Name: shop_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY shop_tbl
ADD CONSTRAINT shop_pk PRIMARY KEY (id);


--
-- TOC entry 2956 (class 2606 OID 20312)
-- Name: surepay_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY surepay_tbl
ADD CONSTRAINT surepay_pk PRIMARY KEY (id);


--
-- TOC entry 2958 (class 2606 OID 20313)
-- Name: surepay_uq; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY surepay_tbl
ADD CONSTRAINT surepay_uq UNIQUE (clientid);


--
-- TOC entry 2961 (class 2606 OID 20314)
-- Name: url_pk; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY url_tbl
ADD CONSTRAINT url_pk PRIMARY KEY (id);


--
-- TOC entry 2963 (class 2606 OID 20315)
-- Name: url_uq; Type: CONSTRAINT; Schema: client; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY url_tbl
ADD CONSTRAINT url_uq UNIQUE (urltypeid, clientid);


--
-- TOC entry 2931 (class 1259 OID 10766187)
-- Name: accountname_uq; Type: INDEX; Schema: client; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX accountname_uq ON Client.account_tbl USING btree (clientid, upper((name)::text));


--
-- TOC entry 2959 (class 1259 OID 13141864)
-- Name: client_url_uq; Type: INDEX; Schema: client; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX client_url_uq ON url_tbl USING btree (clientid, lower((url)::text));


--
-- TOC entry 2940 (class 1259 OID 10766188)
-- Name: keyword_uq; Type: INDEX; Schema: client; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX keyword_uq ON keyword_tbl USING btree (clientid, upper((name)::text));


--
-- TOC entry 2943 (class 1259 OID 13411468)
-- Name: merchantaccount_storedcard_uq; Type: INDEX; Schema: client; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX merchantaccount_storedcard_uq ON merchantaccount_tbl USING btree (clientid, pspid, stored_card);


--
-- TOC entry 2944 (class 1259 OID 13411470)
-- Name: merchantaccount_uq; Type: INDEX; Schema: client; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX merchantaccount_uq ON merchantaccount_tbl USING btree (clientid, pspid) WHERE (stored_card IS NULL);

--
-- TOC entry 2966 (class 2606 OID 20414)
-- Name: account2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY Client.account_tbl
ADD CONSTRAINT account2client_fk FOREIGN KEY (clientid) REFERENCES Client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2967 (class 2606 OID 20419)
-- Name: cardaccess2card_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY cardaccess_tbl
ADD CONSTRAINT cardaccess2card_fk FOREIGN KEY (cardid) REFERENCES system.card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2968 (class 2606 OID 20424)
-- Name: cardaccess2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY cardaccess_tbl
ADD CONSTRAINT cardaccess2client_fk FOREIGN KEY (clientid) REFERENCES Client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2969 (class 2606 OID 20429)
-- Name: cardaccess2psp_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY cardaccess_tbl
ADD CONSTRAINT cardaccess2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2970 (class 2606 OID 20434)
-- Name: client2country_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY Client.client_tbl
ADD CONSTRAINT client2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2971 (class 2606 OID 20439)
-- Name: client2flow_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY Client.client_tbl
ADD CONSTRAINT client2flow_fk FOREIGN KEY (flowid) REFERENCES system.flow_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2985 (class 2606 OID 20444)
-- Name: ipaccess2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY ipaddress_tbl
ADD CONSTRAINT ipaccess2client_fk FOREIGN KEY (clientid) REFERENCES Client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2972 (class 2606 OID 20449)
-- Name: keyword2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY keyword_tbl
ADD CONSTRAINT keyword2client_fk FOREIGN KEY (clientid) REFERENCES Client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2973 (class 2606 OID 20454)
-- Name: merchantaccount2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY merchantaccount_tbl
ADD CONSTRAINT merchantaccount2client_fk FOREIGN KEY (clientid) REFERENCES Client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2974 (class 2606 OID 20459)
-- Name: merchantaccount2psp_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY merchantaccount_tbl
ADD CONSTRAINT merchantaccount2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2975 (class 2606 OID 20464)
-- Name: merchantsubaccount2account_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY merchantsubaccount_tbl
ADD CONSTRAINT merchantsubaccount2account_fk FOREIGN KEY (accountid) REFERENCES Client.account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2976 (class 2606 OID 20469)
-- Name: merchantsubaccount2psp_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY merchantsubaccount_tbl
ADD CONSTRAINT merchantsubaccount2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2977 (class 2606 OID 20474)
-- Name: product2keyword_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY product_tbl
ADD CONSTRAINT product2keyword_fk FOREIGN KEY (keywordid) REFERENCES keyword_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2978 (class 2606 OID 20479)
-- Name: shipping2shipping_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY shipping_tbl
ADD CONSTRAINT shipping2shipping_fk FOREIGN KEY (shippingid) REFERENCES system.shipping_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2979 (class 2606 OID 20484)
-- Name: shipping2shop_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY shipping_tbl
ADD CONSTRAINT shipping2shop_fk FOREIGN KEY (shopid) REFERENCES shop_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2980 (class 2606 OID 20489)
-- Name: shop2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY shop_tbl
ADD CONSTRAINT shop2client_fk FOREIGN KEY (clientid) REFERENCES Client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2981 (class 2606 OID 20494)
-- Name: shop2keyword_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY shop_tbl
ADD CONSTRAINT shop2keyword_fk FOREIGN KEY (keywordid) REFERENCES keyword_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2982 (class 2606 OID 20499)
-- Name: surepay2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY surepay_tbl
ADD CONSTRAINT surepay2client_fk FOREIGN KEY (clientid) REFERENCES Client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2983 (class 2606 OID 20504)
-- Name: url2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY url_tbl
ADD CONSTRAINT url2client_fk FOREIGN KEY (clientid) REFERENCES Client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2984 (class 2606 OID 20509)
-- Name: url2urltype_fk; Type: FK CONSTRAINT; Schema: client; Owner: postgres
--

ALTER TABLE ONLY url_tbl
ADD CONSTRAINT url2urltype_fk FOREIGN KEY (urltypeid) REFERENCES system.urltype_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3108 (class 0 OID 0)
-- Dependencies: 10
-- Name: client; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA client FROM PUBLIC;
REVOKE ALL ON SCHEMA client FROM postgres;
GRANT ALL ON SCHEMA client TO postgres;
GRANT USAGE ON SCHEMA client TO postgres;


--
-- TOC entry 3109 (class 0 OID 0)
-- Dependencies: 180
-- Name:Client.account_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE Client.account_tbl FROM PUBLIC;
REVOKE ALL ON TABLE Client.account_tbl FROM postgres;
GRANT ALL ON TABLE Client.account_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE Client.account_tbl TO postgres;


--
-- TOC entry 3111 (class 0 OID 0)
-- Dependencies: 181
-- Name:Client.account_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE Client.account_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE Client.account_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE Client.account_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE Client.account_tbl_id_seq TO postgres;


--
-- TOC entry 3112 (class 0 OID 0)
-- Dependencies: 182
-- Name: cardaccess_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE cardaccess_tbl FROM PUBLIC;
REVOKE ALL ON TABLE cardaccess_tbl FROM postgres;
GRANT ALL ON TABLE cardaccess_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE cardaccess_tbl TO postgres;


--
-- TOC entry 3114 (class 0 OID 0)
-- Dependencies: 183
-- Name: cardaccess_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE cardaccess_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE cardaccess_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE cardaccess_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE cardaccess_tbl_id_seq TO postgres;


--
-- TOC entry 3115 (class 0 OID 0)
-- Dependencies: 184
-- Name: Client.client_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE Client.client_tbl FROM PUBLIC;
REVOKE ALL ON TABLE Client.client_tbl FROM postgres;
GRANT ALL ON TABLE Client.client_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE Client.client_tbl TO postgres;


--
-- TOC entry 3117 (class 0 OID 0)
-- Dependencies: 185
-- Name: Client.client_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE Client.client_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE Client.client_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE Client.client_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE Client.client_tbl_id_seq TO postgres;


--
-- TOC entry 3118 (class 0 OID 0)
-- Dependencies: 261
-- Name: ipaddress_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE ipaddress_tbl FROM PUBLIC;
REVOKE ALL ON TABLE ipaddress_tbl FROM postgres;
GRANT ALL ON TABLE ipaddress_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE ipaddress_tbl TO postgres;


--
-- TOC entry 3120 (class 0 OID 0)
-- Dependencies: 260
-- Name: ipaddress_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE ipaddress_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE ipaddress_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE ipaddress_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE ipaddress_tbl_id_seq TO postgres;


--
-- TOC entry 3121 (class 0 OID 0)
-- Dependencies: 186
-- Name: keyword_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE keyword_tbl FROM PUBLIC;
REVOKE ALL ON TABLE keyword_tbl FROM postgres;
GRANT ALL ON TABLE keyword_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE keyword_tbl TO postgres;


--
-- TOC entry 3123 (class 0 OID 0)
-- Dependencies: 187
-- Name: keyword_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE keyword_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE keyword_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE keyword_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE keyword_tbl_id_seq TO postgres;


--
-- TOC entry 3124 (class 0 OID 0)
-- Dependencies: 188
-- Name: merchantaccount_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE merchantaccount_tbl FROM PUBLIC;
REVOKE ALL ON TABLE merchantaccount_tbl FROM postgres;
GRANT ALL ON TABLE merchantaccount_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE merchantaccount_tbl TO postgres;


--
-- TOC entry 3126 (class 0 OID 0)
-- Dependencies: 189
-- Name: merchantaccount_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE merchantaccount_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE merchantaccount_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE merchantaccount_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE merchantaccount_tbl_id_seq TO postgres;


--
-- TOC entry 3127 (class 0 OID 0)
-- Dependencies: 190
-- Name: merchantsubaccount_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE merchantsubaccount_tbl FROM PUBLIC;
REVOKE ALL ON TABLE merchantsubaccount_tbl FROM postgres;
GRANT ALL ON TABLE merchantsubaccount_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE merchantsubaccount_tbl TO postgres;


--
-- TOC entry 3129 (class 0 OID 0)
-- Dependencies: 191
-- Name: merchantsubaccount_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE merchantsubaccount_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE merchantsubaccount_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE merchantsubaccount_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE merchantsubaccount_tbl_id_seq TO postgres;


--
-- TOC entry 3130 (class 0 OID 0)
-- Dependencies: 192
-- Name: product_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE product_tbl FROM PUBLIC;
REVOKE ALL ON TABLE product_tbl FROM postgres;
GRANT ALL ON TABLE product_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE product_tbl TO postgres;


--
-- TOC entry 3132 (class 0 OID 0)
-- Dependencies: 193
-- Name: product_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE product_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE product_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE product_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE product_tbl_id_seq TO postgres;


--
-- TOC entry 3133 (class 0 OID 0)
-- Dependencies: 194
-- Name: shipping_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE shipping_tbl FROM PUBLIC;
REVOKE ALL ON TABLE shipping_tbl FROM postgres;
GRANT ALL ON TABLE shipping_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE shipping_tbl TO postgres;


--
-- TOC entry 3135 (class 0 OID 0)
-- Dependencies: 195
-- Name: shipping_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE shipping_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE shipping_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE shipping_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE shipping_tbl_id_seq TO postgres;


--
-- TOC entry 3136 (class 0 OID 0)
-- Dependencies: 196
-- Name: shop_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE shop_tbl FROM PUBLIC;
REVOKE ALL ON TABLE shop_tbl FROM postgres;
GRANT ALL ON TABLE shop_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE shop_tbl TO postgres;


--
-- TOC entry 3138 (class 0 OID 0)
-- Dependencies: 197
-- Name: shop_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE shop_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE shop_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE shop_tbl_id_seq TO postgres;
GRANT SELECT,UPDATE ON SEQUENCE shop_tbl_id_seq TO postgres;


--
-- TOC entry 3139 (class 0 OID 0)
-- Dependencies: 198
-- Name: surepay_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE surepay_tbl FROM PUBLIC;
REVOKE ALL ON TABLE surepay_tbl FROM postgres;
GRANT ALL ON TABLE surepay_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE surepay_tbl TO postgres;


--
-- TOC entry 3141 (class 0 OID 0)
-- Dependencies: 251
-- Name: url_tbl; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON TABLE url_tbl FROM PUBLIC;
REVOKE ALL ON TABLE url_tbl FROM postgres;
GRANT ALL ON TABLE url_tbl TO postgres;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE url_tbl TO postgres;


--
-- TOC entry 3143 (class 0 OID 0)
-- Dependencies: 250
-- Name: url_tbl_id_seq; Type: ACL; Schema: client; Owner: postgres
--

REVOKE ALL ON SEQUENCE url_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE url_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE url_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE url_tbl_id_seq TO postgres;


-- Completed on 2014-11-17 14:27:08 CET

--
-- PostgreSQL database dump
--

-- Dumped from database version 9.2.2
-- Dumped by pg_dump version 9.3.5
-- Started on 2014-11-17 15:26:16 CET

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = system, pg_catalog;

--
-- TOC entry 3142 (class 0 OID 10765916)
-- Dependencies: 217
-- Data for Name: card_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO card_tbl VALUES (0, 'System Record', NULL, '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', false, NULL, NULL, NULL, NULL);
INSERT INTO card_tbl VALUES (10, 'SMS', '\377\330\377\340\000\020JFIF\000\001\001\001\001,\001,\000\000\377\333\000C\000\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\377\333\000C\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\001\377\300\000\021\010\000s\000\264\003\001!\000\002\021\001\003\021\001\377\304\000\037\000\000\001\005\001\001\001\001\001\001\000\000\000\000\000\000\000\000\001\002\003\004\005\006\007\010\011\012\013\377\304\000\265\020\000\002\001\003\003\002\004\003\005\005\004\004\000\000\001}\001\002\003\000\004\021\005\022!1A\006\023Qa\007"q\0242\201\221\241\010#B\261\301\025R\321\360$3br\202\011\012\026\027\030\031\032%&''()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz\203\204\205\206\207\210\211\212\222\223\224\225\226\227\230\231\232\242\243\244\245\246\247\250\251\252\262\263\264\265\266\267\270\271\272\302\303\304\305\306\307\310\311\312\322\323\324\325\326\327\330\331\332\341\342\343\344\345\346\347\350\351\352\361\362\363\364\365\366\367\370\371\372\377\304\000\037\001\000\003\001\001\001\001\001\001\001\001\001\000\000\000\000\000\000\001\002\003\004\005\006\007\010\011\012\013\377\304\000\265\021\000\002\001\002\004\004\003\004\007\005\004\004\000\001\002w\000\001\002\003\021\004\005!1\006\022AQ\007aq\023"2\201\010\024B\221\241\261\301\011#3R\360\025br\321\012\026$4\341%\361\027\030\031\032&''()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz\202\203\204\205\206\207\210\211\212\222\223\224\225\226\227\230\231\232\242\243\244\245\246\247\250\251\252\262\263\264\265\266\267\270\271\272\302\303\304\305\306\307\310\311\312\322\323\324\325\326\327\330\331\332\342\343\344\345\346\347\350\351\352\362\363\364\365\366\367\370\371\372\377\332\000\014\003\001\000\002\021\003\021\000?\000\376\376(\240\002\212\000(\240\002\212\000(\240\002\212\000\370\317\366\336\375\270~\013\376\302\237\007\365O\212_\026\265\265\206w\037\331\376\022\360\255\214o\177\342?\027\370\216\351d\217I\320\364=\036\333u\356\241\177\251]\252\333Y[B\201\256\247&1$Q\245\305\305\277\360O\373\177\376\337\177\265\317\307\177\023]k\032\317\355\001\361_\341\017\306[_\022\266\251\341\377\000\205\377\000\003\274m6\213\340\357\204\036\026\264\015t</\361*}\002#q\361\017\342\204\341\026_\022\331Xkp\370S\300v\326\322xzd\326uYu\233-\007\327\313\260\261\251\031\326\253\005(\267\313N.\372\355\314\337\336\322\345w\331\3671\2536\264O]\337u\252\265\277\253Z\377\000/\335\277\370"''\374\027\017W\370\300t\177\331S\366\326\3616\233\037\306(!\026\177\016~)=\275\276\231\245\374P\261\264\012\015\255\372\304\320\333X\370\252\312\003\276\372\301a\205g\267O\267i\2605\2147\277`\376\261\325\225\325]\0302\260\005X\034\202\017B\017px\357\315qc({\012\322\212MBZ\303M\254\222k\357w\371\364*\224\371\340\266\272\321\333\275\226\2775\250\352+\224\320(\240\002\212\000(\240\002\212\000(\240\002\212\000(\240\002\212\000+\343\017\333\213\366\342\3705\373\011\374\031\326\376*\374U\326\241[\345\204\331\3703\301\226e\256\274M\343\217\022\335\267\331\264\177\017\370{G\266\337\177\251\352\032\235\373Cimmg\023\313$\262rb\206;\213\210.\234%Rp\247\005yNQ\212^M\244\337\311]\374\204\332J\357D\217\340_\366\231\375\251\177h\277\333\013\366\210\0361\3615\275\346\275\373Cx\232}KI\370u\360\252\3237\336\036\375\224<;w(\202E\264\270gm9\3768\335i0\243\370\323\306\302\013K?\204\366\222\334h\232<\266\336*\212\362\343\302?\242\037\262\227\354\257\340\237\331\327B\274\267\271\275\323\274W\361K_\263\202\357\305\276!\270\212\335\256 \265\220\3414\235\016\332P\327\226^\035\216\355%\375\364\337\351\032\275\3525\335\333''\225gc\247\375v\032\224iB\2358YF\021\214b\264\367\247\356\374\256\335\337v\354\267\320\362\261\265}\311$\322\234\255)[E\030\256T\256\336\235[~Z[[\237\015\376\333\377\000\261[\351\372\266\245\361\303\340e\234\342\362\306\376\337_\361\227\204<=0\264\324\364;\350\007\333a\361\227\204\032\311\242\3244\275J\332\346\021\250\355\261\006\366+\334jzf$K\210f\375\341\377\000\202+\177\301i-\276&\331\370s\366Y\375\251\374Mo\017\304\033X\032\307\341\307\304\375U\205\215\237\217\354\355m\022e\321\365f\015%\225\207\2144\370\241\234\336i\353$P\352\026\261\266\275\243F\272Q\277\260\360\376Y\246\016U(\265(\272u)\362\324I\305\2515%\027\263{J6i\255\032z+mX\032\352QZ\335\244\240\355v\276\316\267vzk\243W\327\313_\352\235\035%E\222''Y#u\016\216\214\031\035X\006VWRAV\004\020FA\004c\212}|\231\352\005\024\000Q@\005\024\000Q@\005\024\000Q@\005\024\000Q@\037\034~\333\377\000\266\377\000\301/\3307\340\227\210>3\374g\327\322\302\313O\216;]\007A\264\214\336\353\336''\327\257\232Ht\235\023D\322\242\2229\365\015CQ\270\216H\355-#x\232\344\3039\363\240\267\267\273\273\265\376\002?j\357\332\257\366\212\375\262\277h\333/\030\370\352\003\251\376\320:\216\255\251i\037\006\376\026h\332\204\222x\177\366\\\360\326\245\004\250E\333\304\326\372f\241\361\372k\031\256\037\305~''\271\232\357O\370;a`\226:T\266\2764\264g\370\177\353\345\224.\345]\256\360\246\326\353\341r~M\354\237\335\256\246\025\246\222\263i-\345~\311\246\227\315\376G\350\037\354\227\373-\370k\366t\360\270\276\272k}s\342\207\211,\240O\030x\234;\317\024d?\332?\260\264\03768\336\327E\266\234\2172V\216+\275b\346\004\275\276\010\261X\331\330\335\370\257\341\037\354\017\032/\304\354\332\351w\021\311\023h\377\000\024\355t\3739u\357\207Z\205\324v\232l\372\027\212\304im\250\370\267\340\377\000\210Lp\375\263J\271\274\225\2747\253\334I\252}\246\303L\267\261\272\321\275<dj*\021\23595R\214\243Qk\253Q\224y\223\265\223\323\243\323E\330\362\360\365\241V\273U"\234j\267\007{uq\345\267f\332i=\276g\242|;\327m5\255w]:\315\272h\336<\267\323\254\342\361g\207"\232[\2352\351.dd\217\304>\033\275a\366m\177\302\372\244\226\3564\335R\330H\321\0216\237|a\275\202U\237\363;\366\303\375\220n\374\027\252^|p\370)\247]Cc\005\354\032\357\213\374#\241\\\334\351\332\216\221\177ev\232\204\036/\360e\306\237\266\357L\274\323\357P_\225\323\317\231c0\222\346\030nl''\3244\351\3759\346\0171\206\036\273\214}\334=\032Z&\357\354\241\030{\376k\225G_\3629\351\303\352x\211\322\274\243y\244\334\235\343i\3628\312..\311=\236\313^\232\243\372\036\377\000\2024\377\000\301e,>.i\376\031\375\232\277io\023\330\257\3043\014\232w\303?\036\334\010\254m\274w\245\351\020\302\262X\353\021\261\216-7\305\372M\254\266\215\342\035)\013Z\333\244\320j\332$\227^\031\274\211\364\237\351\355YYU\224\202\254\003)\004`\2022\010\307PA\317\025\361\370\374?\325\361\022KXT\367\343\321+\245x\374\233\371u=\372SS\202ku\243]\232\376\267\352:\212\3424\012(\000\242\200\012(\000\242\200\012(\000\242\200\012\370\347\366\332\375\267>\013~\302\337\006\365\237\213\037\027|Akh\361\306l\274''\341X]\256<C\343?\023]$\251\243xw\303\372M\262\313\250jz\236\261|\213caki\003\275\305\323\205g\202\336;\253\273W\030\271\3121\212\274\244\322In\325\365\373\223n\357D&\322Wz$\177\237\307\355]\373[~\321_\266G\355\025\037\217|co\251\337||\325>\335\243|.\370A\243j\266\372\247\205?e\375\002g1\264\320\314\222\315\247\335\374|\277\323 {\237\025x\307\355V\272w\302K\033\331\264m-,\374_i$\376\007\373\243\366Y\375\232|5\373?xz[\273\231\240\327\376!\353\360\307?\211\374S41\227\267r\213$\232.\215$\211\347\333\350\326\367\033\244\222Y\030\\j\267 ^^\204T\263\263\261\372\314\015\027\030\250r\362\323\245\030\352\264\367\224c}[\267\233_\025\357s\301\307Wr\222\204n\334\344\264WZ''\004\227{=\272]\266}si\252\3031\362\325\316\344\012\312Y]\004\250\304\2014l\312\004\260\264\233\220J\204\200\313\264\236Fm\\O\005\3143\333\\\306\2276\327\020\311o=\274\351\034\320\315\014\312\31143F\341\243\2229\221\2327\211\324\253\2432\260`\330=K\222\264#:rS\204\232\213\323g\033sE\255wW\327\317f\267\340\237=\032\212\022N2\213\213\223\354\237,\242\326\232j\357\327T~z~\320~\025\370\235\360\276\353\302\2768\370w\257^\017\207\236\011\324\365-Z\015>\306\306\352MS\341\370\277\206\021yc\251K\016\244-\374G\360\212hah\247\320\256t\241''\203\343He\206\375t\210\241\223A\372+\341/\306\335\023\342\346\206\333\222\327L\361M\205\272\377\000\302A\341\217\265\255\331\212)\037\313\213T\323$uF\3244\015C!\255\257\004y\206Gk\013\325\216\356&V\340\300\337\015\213\236\022ZS\253\357Rw\272\332\034\311h\367M\246\272Z\326;1M\342p\264\2611\272\235\037r\252W\327\341\212\223O\265\364kK?3\363\333\366\245\375\2325/\207\332\315\347\306o\202\326\372\215\256\234/c\327|Y\341o\016]I\246jZ\036\247e+\334\332x\317\301\227V\321N\332V\261\244\334\274\227\366\362\333\333Lt\371\332v\373%\366\221y\253h\367\177\321\207\374\021\323\376\013%a\361KN\360\277\354\343\373H\370\202\326\017\027\303dt\357\207_\023u\022\2266>>\203O\265\206it\275MRY\355\364\217\031iP\270\217\\\360\354\363,\261\004mwD\223P\360u\335\205\375\265\346x_kE\305\177\022\233\347\203\266\262^\3572\272\362\327E\337mS\355\300W\347\212\325k\356\316=T\242\242\223j\332\266\264\323\313\320\376\236Q\322TI#u\2229\025]\035\0302:0\014\254\254\011\014\254\010 \202A\007#\216i\365\362\207\252\024P\001E\000\024P\001E\000\024P\001E\000|e\373p\376\334?\006\277a\037\203:\307\305\237\212\372\2703\247\227\247\370O\302\032r\375\273\304\3762\361-\373\375\233G\320|?\243B\342\367T\324u\033\326H-lm\025\256n\337z\303\205\212y\240\377\000??\332\337\366\274\375\241?l\017\217\377\000\360\227x\336\366O\022|i\324\265G\207\341g\303=%\323U\360\247\354\257\243\337en-\264\373\245\377\000\211n\247\361\330\331-\215\277\214|{\025\234v?\012a\323\256t\217\014\352S\353\321\313\253xc\325\313\260\374\322U\245\033\255U?D\340\333\336\376\363\367U\226\311\264\314+N\336\357KsK\256\211\253/+\376W\321\237Z\376\314\177\263\227\207>\003h\002\352\357\354z\337\304\035^\022<A\342\225\267 \305\014\214\223\015\027Hk\200\322Z\3516\355\0324\316\004S\352wQ\013\313\265]\266\326\266\177\266\237\260\347\354i\256\376\323:\344>5\361|wZ/\300\357\017j\221\245\374\257\035\325\235\377\000\304\213\273Vg\270\320<=u\037\222\361h\220L\211o\342\015z\011\225\343\0226\231\245\226\324~\321s\245{9\206!a0J\020\264j\325\350\336\277c\231\333w\243\336\353_C\311\301\323x\234d\253\311''J\227,\242\236\334\336\352IF\3337\3576\356\325\265?a\177j/\330o\341\347\306\377\000\207\232N\223\340\275;F\370\177\343\277\001i\020\351\337\0175\2557N\212\327M\213L\261\211\226\333\301\372\345\265\244j\363\370frO\222\310\262\334\350\267r>\243g\035\302M\251i\372\237\363y\342\355\007\305\237\017|M\254\370\033\307\272\025\377\000\205\374[\341\373\246\264\325t]B/.Te\371c\274\265\221wE{\247^ \023\330jV\2175\235\355\264\221\334\333\\M\014\261\315''\235\223b\271jK\015>f\247\2547iIr\253.\327J\335\327N\211\357\231a\271\343\032\253\342V\215G\3357\024\235\255\366Z\276\226\321[\253;\037\025\374:}3\300Z\017\211\243\032\226\264\272\264\027\247[\212\035#\355\236\037\263\26473\303\034\303S\206I\341\223N\271\262\227J\333{w\012Yj\027\332\216\247\242\303$z\277\2065\355>\333\371\320\375\273\276\022~\320\177\263o\213|?\373Q\376\313~)\324l<\003\340h\221|K\360\327N\212\376\372\323\302\366\367w3\313\254\352\227:J4\266\372\367\303\315N?\263\332\353:u\300\023xADw\272@\264\322c\027~\037\364q\027\251\207\366\321\213\215\\5nd\343d\371T\240\245\0355\321\245\315\345{j\265\343\302\270\322\251\354j$\351\326\212\204\256\325\271\244\241f\267N\352\373j\332l\373\313\366L\375\260\274\005\373^|:\177\020hq\235\017\305\272/\221\246\370\373\300\327\223\303qu\241\352R\300\255\366\213\031A#S\360\346\244\014\217\245j\201\021\345\011%\235\3446\327\326\3270/\315\177\264/\354\377\000\250|<\326\356\376-|%\267\276\376\314:\225\246\263\342_\010hwWZ]\345\215\375\234\222Im\342\217\011\336\351\271\273\321\265\3152Y\346\271\261\273\263\212V\262\222[\324h\257t\253\355OD\324\267\205e\211\243\014Do\360\245$\372\351\036o-\035\365kf\374\230\350\322\226\036\255Jm\350\264_\336o\226Q}\265V\177=\257\241\375-\377\000\301\034\177\340\2616\037\026t\317\014~\316_\264W\211\255\033\307\011eoc\340\017\2107\3676\2266\3363\262\323\355\2047\026:\215\264\246!\245\370\243H\021\302|C\241G%\304:dS\332k:M\326\243\341MR\302\366\327\372qGI\025d\215\225\321\325]\035\010eta\225ee$2\2609R\016\0109\025\362\330\354?\260\256\354\275\312\236\3744\262ZF\361^\215\236\345\031\363\301;\352\264~\252\335>k\277\250\352+\214\324(\240\002\212\000(\240\002\212\000+\343/\333\207\366\341\3701\373\011|\032\325\276+\374Z\326\012L\300i\276\017\360\226\233\023\352\036&\361\237\212/CC\242\370{@\321\355\267^\352:\226\253{\262\322\306\322\3363%\335\313\010Q\243Q,\320\3358J\255HR\205\234\352J1I\273n\354\337]\225\336\335\004\332J\357D\217\363\356\375\257\277k\337\332\013\366\264\375\241&\361\217\213\347\272\324?h\033\373\255_@\360G\202\264\035b\332\373\302?\263_\204\357\340\226\312\343C\320\356\254\345\227M\275\370\315{\246Ky\027\217\276 E}qa\340+\031\246\360\207\203\346\213S\203S\325l\375\367\366m\370\013\341\317\202\032+^J\361j\2767\325\355!\036"\327\234\345b\033\274\327\322\364\225p\015\266\235\004\315\222\343l\367\357\022\\^1\021[\303k\365\270jQ\243\010E[\226\232\212Kkr\250\353\245\223\275\233{j\337\313\312\305\327qq\212~\375I.kt\217\270\222\272\337M\274\357\320\375\272\375\203\177a\355\177\366\255\326 \361\327\215\242\276\321\177g\275\026\372\346\336\372\372\013\227\260\325\276"k\032|\212\262x\177\303\322"4\360\350v\363\221\037\210<A\037\224\001\216\343H\321\347:\230\273\272\322?\250\377\000\017\350\032/\205tM''\303~\034\323,\364m\013B\323\254\364\235#J\323\340Kk-;M\260\201-\254\354\355\240\214\004\212\013x#H\343P8U\344\222I?7\216\304\274V&\244\323N\021j\020Kk%\033\277;\265\177\370s\273\013K\330\322\212\267+\227\277%\347%\027g\351kz\334\330\310\31623\351\336\276''\375\263\277c\177\013~\324\336\017\373U\243[\370\177\342\357\205\264\353\261\340/\0303I\025\273H\315\366\221\341\317\024,\021L\367\276\033\277\234:\027\026\363\336h\263\334\313\250i\252\353.\241a\251sS\233\2478\316-\247\031)+y;\233\312*Qq\222\272j\315\037\315\274:\357\215\276\003x\333\305_\014~"xN\332\337\304\032\025\316\255\247k\236\032\326\332e\206\317P\325|?}\240\377\000mi\327\372z\334\275\346\221\254h\372\250\023]i\202I\365=\002\350I\241j:N\254t\235v\302\227\306_\207\237\360\213\352Iw\242\351\327K\341Mj=>\326\312+\353i\254\345\233[\233JK\255wG\3234\275R\366\363W\275\203L\221\212\352\366\311.\263\377\000\010~\241\177\007\203<A\254\336k\266\206\353P\372\312\030\252u=\225G\360\342 \2438KT\244\2255&\265\321\267w\256\373\336\372\277\016\265)SJ6\275JR\213\205\272\3018\270\353\262\325\332\313\253\326\310\374c\361\007\354\357\341\337\331\247_\325>"\374\017\321-\274+\341\275OV\270\361\026\2764\377\0002y|''\252]7\2214\237d\232Fk\277\207\227\260\312!\274\320\343\177#\303h\323\\\331\307o\245\205\237F\366\317\370h\177\013\335xN\352\367\304\227\372_\205o\354- >!MN\3524\323\320\\\342(\256t\211n\033\376&v\032\214\207m\201\215d\271\017*X\315\011\274\001$\303\015(\3411U0\322mQ\251\357\323o[i\013\336\313T\323m>\305Ts\255N\235JZ\3158FI_\232-\362$\322\353f\332k^\353\241\371\347\257k\236\022\264\3615\317\216\276\005x\271o\322+\353O\020\370\203\303:]\335\346\211\342\013\013\335"\375\256\354<[\341\230\365\033\024\275\262\325t{\306\226{\035j\326\306\356\013Y.\245\323\265\013]GK\3265M\037]\376\303?\340\216?\360W\3353\343.\211\341\317\331\357\343\367\210 \217\307\226\332l\260\370\007\306\2676\326\332}\237\216\264\315"ha\271\202\342(n\347M+\305Z-\275\336\236|Q\341\251\235\244\323^\356\327W\320\037T\360f\241\246\3520\347\214\245\034^\035\272O\232P|\360q[\355x\335ox\357\246\367z\356uag8\276Z\211\305\244\223\346\321\264\371Te\346\255u\331;.\232\177K\212\301\200e!\225\206A\007 \203\310 \216\010"\226\276l\364B\212\000(\240\002\212\000(\240\017\214\377\000m\357\333{\340\367\3541\360{T\370\237\361?UYuI\202\351\276\005\3606\234\222\337\370\253\307\276+\276\221l\364O\015xkC\262Yu=_S\325\2659m\354,\354\354-\346\270\236\342uUT\211g\236\017\363\336\375\262\377\000l\377\000\216\377\000\265\217\307\213\317\031\370\276\346\343V\370\323\2546\243\244\370G\300\0263\307\251\370[\366Y\360\375\364\302\326\343E\360\335\344R\334i\367\277\032\365\0352(!\370\201\361\016\315m\255|\001o-\367\203\274%"j\177\332\372\255\267\251\226\320\274\325i\351y(R{\351x7%\326\355\350\255\272N\306\025\246\242\255ueiK\272I\253}\357\362=\013\340\017\301\275\007\341\016\214\327\022\274:\217\213u8C\353Z\363"\207\2162\025\216\237`\314Y\340\260\204\256\\n\017u 3\314\007\356\343\207\367\017\376\011\357\373\004x\233\366\270\326\355\374y\343\333}k\302\377\000\263\256\203v\222I\252\254Mi\177\361cP\262\275\021]xk\302\363\310\321\317\017\207`\222\336\342\337\304~&\2029\025$F\321\364\227mM\256\256toG1\304*\030~H\331\316\267\272\265wI8\3635n\313N\326O\256\253\206\204=\275e9\253\252rSM\355\314\234Tb\323\352\355\3703\372\316\360\357\2074\037\010\350z_\206\2741\244i\332\007\207\364K84\355#E\322,\355\364\3753L\260\266A\035\275\235\215\225\254q[\333[\302\200,qE\032\250\344\340\222I\332\257\232Z$\273\036\250Q@\037\014~\333\177\261/\204\177kO\006\307=\245\305\277\204\376.xV\332\352_\004x\332+X\312\334\223\024\222\017\012\370\253\313\217\3557\336\027\324.J1(\315w\242\3353j:zL\222j\032v\247\374\310[\370\327\342o\354\371\361C^\360\247\305\335+X\360\377\000\216|9\240\311\340}J\307\304\362>\245}\341\235\002\352{K\265\233\303\327\022\334J\322xkS\323|\333D\270\360\276\255\246M}\341\275kQ\213\303~ \322nom\265X\275,\272\245\346\3507oi\254\035\355i\350\255\327\342Zh\233\355\255\216,];\250\325I^\032O\3162qZ\253ko\370{\255\274\223\343F\2175\376\210\277\021\274=r\372\227\206\374L\372\205\375\365\275\336\247\341}GY\203Ok\333\033\004\324\365\2704''\265\212\027\325n\365[\031uk\013-7R\213\303\027\232\276\235\240\370\247\\>!\2726\263~\006~\323^\037\263\322~,x{D3\334Z\370;P\320\316\251\245i\355*C\247\331jw7\272\244z\206\227\2450X\305\264/%\256\234\253lX\246\237\035\374\020Y\210\255L\020\247~2\232\251\205\205F\334gN~\317\255\371\\a\031E_]/\243\265\227U\241\313\204\224\251b\244\264\224jZJ\353O\371v\356\274\371\276\025{\332\351\351s\347\035p\315\341\277\025xN\363\302\251\010\361\014z\305\213\351\332~\236/6^\\9\2606\266\262Z\337\263\\\025\272\271\272\227E\275\211\312\332\352q\264\361F\222Z\311\346O\366_\2144mO\341\237\210\377\000\3417\360D\267\320\330\377\000iZ\352s\305\246\336K\247\352z.\257a3O\245\370\213\303\272\254)%\316\211\342M"\345\344\270\320\265\270\022WY\245\271\323\265\033mGI\324\265}\037]\214\2662N\264Sn\012Qj.\315)5\026\355\332\356\332i\245\337\257^"J\025\251\267nYE\306Ok\245%\327uk\374\277/\354k\376\010\375\377\000\005\203\321\2767\350\372\007\300_\217\236!\323\255\276!C\005\315\277\203<m-\274\332N\235\343\333\0356\326\322[\210\276\313,\327v\372?\212\264t\2368\374Q\341\024\324/$\323\031\323\304:5\306\241\340\255OM\324`\376\222\221\222TY\020\254\210\352\031\035Heta\225ea\220U\201\004\020pA\315y\270\374?\325\361\022K\340\251\357\307\262\272W\216\232h\337C\242\224\224\243\272\272\323O+k\363\275\376c\350\2563@\242\200\012(\000\257\214\177n\177\333\217\340\317\354\025\360?_\370\315\361wV*\226\213\025\207\206<-\247\251\274\361\027\213\374I\250\273\333\350\272\006\207\245D\302\353Q\324u;\305\362,\354\355\307\237t\3110\210\254p\\\317n\343\031NQ\204Sr\224\243\024\226\372\264\237\334\256\376Bm$\333\331\037\347\237\373h\376\332_\036?j\257\217\027\2762\361\375\377\000\366\217\306\233\373\253\255;\300\276\022\322\365\031\356\374?\373,xkQI\342\275\360\227\207\246\200\301\247j\177\033.\354e\026?\021\376!Cm<~\007\206\316_\010\370&\365o\241\3255ky>\012\374,\321\376\030i+s0\206\367\304\327\260\243j\272\263\016b\\\357\373\025\243>|\253H\230*\263\215\255+\242\313/*\002\375>\032\222\212\212\323\226\2049v\332v\213m\332\337\015\266\356\337\235\374\272\365\\\223V\326R\277ge\312\227\331\273\331\351\336\353K\237\272\037\360M\037\370''\257\210?l\315z\337\342w\3048/\264/\331\217\303:\304\326\367\263$\363\351\372\307\305\355kM\220}\247\303^\036\236\035\2276~\024\263\271\013o\342\237\021A,3J\342m\017B\233\373Uu\013\377\000\017\177a\376\033\360\336\203\341\015\007G\360\277\205\364\215?@\360\357\207\364\333-\037D\321t\253X\254t\335/K\323\255\343\265\261\260\262\263\205\022\033k[[x\222\030a\211U\0225\001@\257\007\031]\3421\022\237\330\217\273\004\277\226\321\274\277\355\346\256wa\351*t\242\232\264\244\224\347\336\362\214l\236\277ei\353\255\2236$\226(P\311,\211\022\002\001y\035QAf\012\240\263\0202\314\312\2523\222\304\001\222@\256_V\361\277\205\364+&\3245\255b\303J\263Y\232\017\264jW\226\226\020\264\312\357\031\201%\273\236\030\336\360<n\207NW:\222\310\014Of\262\203\030\361\261\271\236_\227A\317\031\213\241A\355\010T\251\030\324\251+''\311N2j\363i\247f\326\215=\2656n\332\331\377\000N\335\177\341\257\246\3669\270\376,h\027\201\016\221\242\370\343[\363@h\036\303\301>"\206\316t`\0329#\325u[\0357H\021\310\204I\034\222j\021\243F\312\341\260A\244\233\342k\332\374\327\237\017~#\333\333\257\337\271\217E\32254OS\344h\232\376\247{(\035\232\336\326`\303\224,\010''\343\247\342\026\012?Zq\341\376''\255\014,\025EV\226\003\005*x\224\345\312\326\026R\314\342\3528\374O\235SJ)\273\267\241\234\352\250Y\362T\222w\3261\272In\335\332\321w\362e\337\016\374T\360?\211\357\033L\323u\217''VC"\276\217\253Z\335h\232\2724,Vp\332f\255\025\235\3500\260e\227\367\007iV\034\342\276+\377\000\202\201\376\300\236\014\375\265\376\036\213\275\032\353L\360g\307\037\014Z\244\237\017\376%5\264\317\034\326\360\274\327\015\340\377\000\030%\227\372F\245\341ME\256.<\266ho/|7\177p\332\266\227o:\311\251\351Z\277\322\344<I\226\347\264ibp5\235:\313\337\236\013\021*0\307\341\334\034o\355\360\364\253U\344\263kU9F\356\334\327\272)J5\023Vvki+]i\371]|\366?\220\275''\305\2360\375\235\276(x\237\341_\306o\006I\246x\243\300\323\370\243\302\372\307\207\265\373d\273\227\302:\257\211t\257\354{\277\026h\226w\220]\351^''\360\366\251\246Km~\372l\266\327\0327\214t\031"\032}\375\234\232\205\256\266\276W\373Z\376\315_\015|Y\243\235{C\277\264\361O\303\331\355\264\355wH\232\333]\206\177\032\370j\313Z\273\237@>%\201\241\320t\007\217\303z\247\211t\335C@\267\223P\360\346\225*j60A\251\370k\373\036\347\301\236%\361?\350Q\2341\020\247)\177\016\274\024e\035\027-D\342\333\275\357v\356\356\265[\336\332\277/\223\331\324W\225\247BQqW\370\240\371]\275,\222\275\373\253^\347\346U\277\300\217\013|9\326\241\361V\237\177\254kb\305\222H\265\015j{k\213\317\016]E#\274Z\304V\266\266\266vz\215\214HV+\344\270\202K\233\030\303\337\302|\226\236m?\326?\341(\262\326c\275\323\347\232\302\342\362\330-\276\247imq\005\324f)b\014\257\345\253\311"\301s\024\312\3029\325%\013(G\014\012I&x[\341q50\355\335TJt\246\235\324\237-5k\357\257{h\326\335M1\037\276\243\032\326i\303\335\234[\367}\347\013;m\245\335\377\000\035\321\341\207X\324~\030\370\232\337\304\336\021\325\257\241\322\342\326\355\356\211\3215\231\264\335OC\327\364\311nb\260\274\323\365[Fy\364\177\022i\027\0277\221h\232\267\2273D\3677\272&\261g\250i\272\246\247\245k\277\332\247\374\021\337\376\013\023c\361\326\303C\375\237\276?\352\326\360|F\262\321m\327\302~5y!\265\323<{c\246\010,\365\011#\265vY4o\020\350\346k''\361_\205\235\246\217G\027\326Z\236\201y\253\370Z\376\322\372-\263\034;\255FQ\224\\+Q\264\224d\2555~F\341$\325\342\334Zv\332\317w{\006\016\252v\327D\224\035\373\247\025\177+\331\255n\265mj\364\376\226U\325\225X0!\200 \202\010 \214\202\010\340\2029\004pA\004qE|\261\351\216\242\200\012(\003\342\357\333\217\366\344\3707\373\011|\034\325\276(\374R\325\222MRT\032\177\201|\013\247\207\275\361_\217\374W} \263\320\2743\341\235\022\320K\251j\332\236\257\251\313oaiiao4\363M7\012\220\307q<\037\347y\373i~\333_\035\177j\357\216\332\207\304\177\034j\272\225\337\306;\347\324t\277\005x\007I\325\026\343\301\377\000\262\366\205y9\202M\027\303\222\333\311%\246\257\361\306\377\000J\212;\177\033|@\266\226\316\303\3001\336\352>\023\360\254GX\212\343[\322\275|\266\206\223\304I]\353\012Z^\327pn^\272\331y\333C\032\262\3327Ii)^\332E5\335\255\377\000\253\354r_\011>\034i\177\017l^\362\345\242\275\361\025\352+\352:\214\252\204[\340nkkvp\0328#o\235\330\222\3629gv#hO\334\337\370&O\374\023[\305\377\000\266\357\210\340\370\223\361*\337^\360\207\354\265\341\253\370\244\227Y\212''\261\324~5j\332u\377\000\225{\341\037\011]L\321\317m\341{i-\256\255<U\342\353X\345\011*6\207\242\310uv\275\275\320\272q\225\276\257AS\213\264\352\2536\255{''\027''e\327\242{]u8p\360\366\325\224\347nH\270\311\365\273N6\216\253\317[v\353\271\375\253x_\302\336\033\360O\207\264\177\011\370CB\322|3\341\217\017i\366\332V\207\240hV\026\332^\221\244\351\266q\210\255lt\375>\3168m\255-`\214\005\216\030cD\003<\022I+\177\257\331\330\307r\376]\355\321\266\200\\4VVs\\\\\\F\336W\224l`U\022j\013)\224/\235f\263\333\302U\376\3254\001r~g\021RT\351NQ\370\222vv\272O\323\253\354\266\357}\217V\372\335\371_\323\376\030\363\235Q|O\251<\362\336\352i\360\377\000\303\340I+\255\231\323\256\274Qxeg\225\321\357.\243\324<9\245\211\204\204^\033\013\015[Xf\310\263\326-\245\333;\362zv\231\242\351\363\301\253xC\341\266\243\342]h"\333\247\213<M<\266\306$+\267\020\370\237\307S\352\2367\376\311 \006\266\217F\264\325,\204\005~\313$\251\261\237\362|\313\021R\246%\252\271\\\263:\261\223\226\022\233\245\355\225:\256\311\316w\370y\226\234\327rm\245\033\017\225J\335]\333\265\342\264\321kue\242vn\326mr\245\251\3221\370\313!\022\305\027\303[8\231w\375\206V\361N\250\361\207\031\021.\2453i\221\261\214\035\242w\320\220K\267\3146\260\3561&{x\377\000[\360\255\355\215\257\304O\013\215"\313Q\2716V\276/\360\325\352\353\332\004wL\306;t\325V\347C\321u\235\026k\346\001\242Ht\355R\302\026p\203T\2264\373Cc\207\316x\317,\305\341q\231\336G\205\241\220a*$\325\012\025#[\226\267-''*\274\372%\010\352\254\343in\254i\032\021i\2718\277u5\361?\205\255\371\343\323f\223\323\266\203\276#\3745\360\357\304=,5\362\177ex\202\332T>\035\361-\242Mo\253Yj\326\322\312,\222\035PA\005\325\305\244\257\022\265\305\257\222\3262\304\355\345Mp\206)\002\374\001\361\206\267\342\337\011\315\037\211\312\037\024x_S\324<)\3426H\326\004\237T\321\344\2067\271X\021\210\204\310\262\023,XQ\024\346h\342\337\022\254\217\216\033#\303d\036)a3\254\273\035\355p\034e\227b\243\365H\324R\245F\266\032\2141\216Q\246\256\241''\027\265\333Z\335\275Y\313R\254cR\2358\266\234\333RV\370\234b\244\234\237Wf\365\353\361]\266|o\377\000\005*\377\000\202nx+\366\347\360\022kZ\014\326\036\012\375\242\274\013\246^\377\000\302\264\361\371\214Ee\253\240\022\\\257\201<}\344C,\372\217\204u+\274\375\236\361c\237Q\360\255\365\314\332\246\226\227\026\327:\316\215\255\177\031V?\023\3763\376\313?\020<Q\360W\342\306\211\342=\013Y\3606\275\024\036<\3705\256j\377\000d\262\222\367O\177\355k%\265\226[]wM\177\010\370\245\256\255\037\\\275\360\334j\2369\360.\251\250h\226\272\364ZG\211\346\324e\375\377\000,\253\027)a\247\360\324\264\242\333\321M8\333\177\346J\327\352\354\273\337\233\027M\331U\216\216>\354\374\343''\025\257~[z\333O5\362N\257\256-\324\367\027\012\260\333\254\363\313(\202\333z[\333\371\216[\310\201\031\335\322\030\376\354*\322\273,civa\270\372\227\200\274}\341o\030\370_O\370\037\361Z\374\351^\034\262\272\277\271\370a\343h\024,\377\000\017u\335Nss.\231|\242E\206_\007j\327R\274WI(K=\012\336I\234D\351\344Ma\352c)MS\247Z\213J\256\036p\251M\245v\324e\036h\255v{\353\276\327\320\347\303\324N\243\2475zu\227.\257T\337.\326\336\366\272}\335\265\335\374\205\343\217\203\367\377\000\003\374g\342\217\007x\222)m\246\274\267\263\273\203Fk\223w\240\335hW~x\323\365\275"\031\306\353\3153[e\270\236\333P\271\215\256\036\330&\232\314-\364\353{{\177\026\321u\277\020x;\305*\376\023\326\265\033\033\3756\357N\3264\035_L\271\232\337[\360\356\275m4\362\351\027V7\260\223-\276\271\243\263\244\332e\374;\256d\261\272\226\303X\202\347O\324.\306\257\333_1\251\230\325\2066\242\204jT\247J\023J\351I\302\224#&\326\316nI\312[\357d\322Z\3058{*\262\247k8\245\033\244\366\\\255>\366w\273}\365\277S\373\215\377\000\2021\177\301S~6\376\320_\261\252j\177\032<0\332\347\217\276\033\374R\361\207\302\215K_\202\302\376\325\265\273_\016i~\027\326t\353\353\253 Yl\257R\323\304\261\330\335\333\2532\371\326m(\331\346\371Q\225\363U(\323\366\223\333\342{([\363=(M\362\307X\374+\244\273/#\372r\242\274\343`\257\212\277n\277\333\253\340\257\354\015\360K[\370\301\361wX_:1\036\235\341\017\007i\333/<S\343o\023\337\277\331\264\177\017\370wF\216E\274\325u\035B\361\226+{;U3O\266R\205"\206\342x*1s\224c\024\333\224\224t\351v\225\366{^\373\011\264\225\336\211\037\347Q\373i~\333\177\036?k\277\216\272\227\304_\210\332\221\275\370\255{<\366?\017\274\037\244\352S\337x{\366b\360\266\240\222\305s\341\275\012KQ\016\235\250\374h\324,''\212\313\307\336;\206\336a\340\230\255\237\302\236\021\272\217S]F\373N\363\377\000\206_\016\364\337\001i\313sr\221\\k\323\304~\331{\201\373\205o\235\355\240,F\310\223\000H\303\012\345C\036Ac\364\330z^\306\234i+\331E;\264\257\314\222\273\275\267\3744\362<\312\363m>\262\250\322I.\212\312\327\267\365\315~\207\356/\374\022\307\376\011\211\342\277\333\253\304\366\277\026>$A{\341\237\3317\301\336#\026\232\255\314\202\367N\326~8j\272Y\022\336xG\301\323\300\366\327V\276\021\265\233\311\264\361o\214\255\347\204\0376}\003\303r\317\255C\252\336xs\373]\210\374,\370\011\360\336\323O\205\374\031\360\273\341\217\303\257\017Zi\366V\322\336\350~\020\360\227\205|?\245\333\010,m\215\326\243s\246h\2725\214Q\304\260\3055\365\325\245\267\230G\233p\035\331\317\201\213\256\353\326\234\257\356\305\362\305''ued\376\366\257\376Z\235\270z~\316\232VI\313\336\225\272\266\243\273\362\266\237\360\017\301o\370+\217\374\024k\341\007\217\377\000d\217\036|;\375\206\177\340\241\237\004<=\373D\317\255\370SR\323\346\370y\361\373\302\236\032\327\265\217\011iZ\377\000\227\342\235\017\302\177\025t\377\000\020\330xg\301\272\365\314o\025\334\032\266\251\342m6\336\346\303I\272\261\022\264\027\357r~\217\377\000\202\037\330~\321\326\337\260G\201\344\375\247\376#\247\304\257\032j>8\361\244\376\022\327l\376)\370_\343]\355\277\303\326\373\035\327\207\264\255W\3427\206u\317\026x_\\h\256\242\3615\325\245\224\336''\361[h\272F\241\240\332]_h\272\314wZN\227\312\322\222q\222\272j\337\2127?`\027D\265\272KY\345\212\316\355\304Q\311\024\327I=\334I\220\031^+[\251\033t\305v\223{4\306\351\3372J\031\230\212\370\357\343\307\374\024C\366''\375\226\374s\246\374/\370\365\373K|2\370o\343\355FM>s\341}kYy\265m&\317Y\227f\221{\342Hl\255\265\250|%c\250\306V\343O\272\361\024\336\037\265\275\266\2229l\024\244\211\030\344\303`p\324k\312|\320\247:\252\320\273\214]\325\237\272\344\325\332\262\321;\353\242\3349\243\037\212\375l\322o]7\266\266\337\252\353\255\264>\324\266\274\261\277\264\265\276\262\270\266\276\263\276\267\212\356\326\356\326X.-\256\255n"Y\240\272\266\232''x\356m\256"e\226\011\355\332Xf\211\226H\235\321\225\216]\323X\244S\011\336\332H\245y\032\352;\315\210\031w\026\217\314k\220\270\216\337 E\022\307\205\012\006\376\244\371\271\2063\017G\333\3413LV\035\340\2618z\364\245K\020\322\225I\270)R\215\013\305sMK\336\224d\365\262\325n\252U\355OV\332\333}\026\261\2673z+\2555\263od\365G\347&\273\252|w\377\000\205\344\226vZ\217\214[B\177\032i\321\3511\351F\354\370J\343\302\3277w\032\204/qg\247\010tg\205t\247\2029\3565\010\356\356\014\250D\362M\264\314\337N\376\316\210\023\304\037\032\226\335\025,\027\342N\257\014\036R\004\210][yV\372\202\306\021V\020\253r\204(\210\005\333\217\225F\024\177\036x)\217\342l\327\213\260\325\263\352\325=\206[\306\274[\226e\264\2539),\0342\271N\222\247\031+($\324\234`\324V\366\275\217\237R\253,\306\222\224\224\240\247;h\226\351\266\222\265\322M\335\3054\225\365Wh\372\232\277)\277\340\250\037\360K\337\207\377\000\267\377\000\303\303\253x~M\017\341\337\3555\340\353(\307\303_\213\263X\314R\362\316\332I\356$\360\007\217\277\263\224\335\352\376\013\325Z\342\340[N\326\372\206\241\341-Js\254h\326\327V\363kZ\036\273\375\305\0318\311I=b\323^\253S\334\222R\213\214\225\342\323\277\241\374\001\374O\360\247\304?\203\237\020\374c\360\177\342\377\000\204\265o\207\377\000\024<\003\252\334h\236(\360\246\265\003[\313\014\366\354\313\006\255\245J\340C\252\370\177W\204%\376\213\255\330\275\306\233\252i\263\333_\330\\]Y\335Z\335M\344\327\372\306N\025\306@\340\236T\375\355\303\236=~\\u\0079c\203\365T+\373jP\237t\256\264i5\030\251E\352\337[\331\364\227C\301p\225''8j\371\032q\266\356*\322\213\276\233;}\3157\320\347\274}\361?\305^3\036\034\3225]Q\265\253\317\010\350\217\341\315\017Q\271\323\364\311uM\027@\271\275:\210\321\345\326c\262\217W\326\302]\3135\326\217a\254\336\352V\372(\270\270\222\332\033K[\261g\177\366G\354c\373\034\374P\370\347\361kB\370C\360\267\303v~%\370\317\342+\013mST\276\324\355$\324\374+\3739\370KU\226<|I\370\231\020O#Q\361\266\245\004\267\027~\000\370w}:\\j3(\361?\213#\266\360\315\2740k\323''O\017N\244\225\241\012Qi]\3319I\306\321\212w\325''w\273\327K[^\352R\225E\031\277\212\252\212\276\334\260J\011\277&\332QM.\232t?\320\217\366@\375\202>\012\376\310\237\003<3\360c\302ZS\352\211\246Mw\254\370\217\304\232\225\325\345\306\265\342\357\027\353\002\011<C\342\255v\371\346Iu\015cY\273\204\\^^J\221\357!"\267\202\322\316\033kH\012\371\251V\251&\344\344\325\365\267c\320Q\212IYh\222\331t>\344\242\263(\370\223\366\362\375\274\276\010\177\301?\276\010\353\037\030>1k~\\\304\177fx3\301\372lf\377\000\305\0367\361U\342H\2327\207|=\243@M\346\243\250\352wi\366{KxP\011\245\014\036X \216\342\346\337\374\350?l\357\333_\366\201\375\257\276?\337|L\370\227{p~3\317>\253\241x\033\300\332&\245\015\357\205?f\317\010_\302\326s\350\032\004\366m-\236\243\361\233R\262\232\352\017\035x\342\013\211-\374\023m4\236\025\360\251\203W\212\366\357G\364\362\352>\373\255%\242j\020}\334\234[k\345\177\225\316z\362\321Au\325\371(\264\322\371\376\207\023\360\363\341\246\233\360\373M\023\316\321\\\353\267\021y\227\367\216r\226\330@L\026\314UDq .\031\360\0163\222rK~\327\177\301-\377\000\340\226\0362\375\274\374Oc\361G\342\265\206\261\341?\330\363@\277\274\216\367V\267\272\227E\361\017\306\315oI\237\310\227\302\336\013\235c7\226\336\016\266\324\026K\177\026x\302\334B$k[\277\017xz\357\373qo\357\274;\335\230W\3664ySJu\037*\327d\232m\257;ig\325\333S\216\204}\275w''\360SJ_>h\244\222[]\267''\331\037\334\377\000\205<#\341\237\002\370W@\360G\2034M;\303\036\023\360\266\213\247\370{\303\232\006\213m\025\216\231\242h\272M\244V\032n\233\247Z\304\242+{[+H"\202\010\325p\250\20399''\371\344\377\000\202\311\376\304?\011?jo\212\177\000\376\027\267\210>2x\263\366\222\375\246> 6\207\360\307\303\027\037\026<O''\301\237\201?\014\276\032A\341\333\377\000\217\177\264\015\237\303\021v\276\035\323.|-\340\353=3A\373\025\206\243\242O\343\017\210^<\320\344\273K\233\213\215VM?\347\226\211.\307\250\177\016_\267O\205\276\007\370\037\366\266\370\373\360\337\366w\264\232\337\341/\303\317\034k\337\017\374-&\263\254\037\024j:\365\277\303M0\370?\304\2765\236\353Pw\270\272O\023j\351\252kZU\256\216a\266\265\227P\323t\253(WX\270\266\321\353\372P\377\000\202\022\377\000\301;\177l_\013\3703\341\007\355\341\360\257\366\251\360\347\301\357\204\037\021\374C\254\311\361g\340\247\214\364\035G\304\032\027\304o\205\036\012\361\326\253\341\217\022K\342&\270\324\233\303\327Z\205\366\235\341\353\333\277\016\370\332\364\330\353\272\024\322Gka\254i\332I{x\300?\243O\212\337\360ZO\370%\357\301+\306\321\374s\373c|/\273\325\255\235\255.,\276\037\037\023|_xo I\376\321kuw\360\267\303\336-\265\263\270\202K;\270gMJ[\031c\232\336T\2328\234m\257\3433\376\0125\341\257\331g\376\012\031\373x\370\233\366\203\375\226?m\317\201\226:g\3079\374\016<]\240~\320\366\377\000\031\376\0017\303\035{\303\036\017\320<\010\260^x\267\306?\016\037\300~-\360?\211\354\374;m\256\235&\337\304:\206\243c\252j\372\2146:&\237\022C#\344\361X,>#\013O\031B\245o\254\316T\3508AIR\250\271_4\233^\352kE\262z\337di\010\306J|\333(\335z\335\177^]\255s\373\223\370}\360\337\306^\014\375\233>\001|,\3703\361OC\327\374\033\360\367\340\277\302\377\000\207\332W\304\333S\375\265/\2154\217\007\370#C\360\345\217\213\264mJ\013\275b\315\355u\353}:\015N\033\346\325/!xo\005\377\000\366\215\306\232\223jC\346_\215\237\015|]\340;}/V\361O\304k\377\000\026j\272\355\335\3346P_I\252Eq,v\260\013\233\251\336\326Y\254!\013\033\315ok&\346\217m\345\334\021\010\3263#\305\374Y\364\224\341>:\303Q\306q\226\017\210\026\007(\341\374F\023\031C\016\2615)\271\373z\260\243(N\021\\\262j\015]7\263\323K#\3473\012\265c\012\322R\264i\244\322\367\265I\3067\367cf\3276\355\335-5\351\343\236\031\326|W\244\352\266''\301\332\256\257e\250\335\336Ai\004zF\241\250\333\015GP\324n\240\262\210Mmq}\252[\264P\255\310yLJ\213\034i!\202\022\025#?\242\237\2624\336(\323\355\374]\341\357\023\370S\305:5\333j\227>":\276\271\247\334\333\332\352W:\243Y\213\365[\311\322(\356n\332\361f\234\033U\226\336Hw\311\034\305@\025\371\017\321\273\031\306\271\317\2128\\\303\0377[\206V/\037\210\303\316\233\251*_^\304e\352\235i\266\357\016g\004\265\335\332\307\016KVx\212\352\244\257(\307\231+\267d\344\222\272\350\236\327\332\351$}\241Y\272\276\257\245x\177J\324\265\315sR\260\321\264m\036\306\357T\325\265mR\362\337O\323t\3156\302\011.\257\265\015B\372\356H\255l\254l\355\242\226\346\356\356\346X\340\266\2029&\232D\215\031\207\372l}m\257\247}\017\363\257\377\000\202\332\177\301C>\020\377\000\301A\277i\037\007_|\014\360\226\235\007\303\257\200v:\367\2044\337\216\023\330K\247\370\323\343\035\315\336\240e\274\206\300\231\327g\302\235&\346\031\245\360\254:\255\223j\327\027:\246\273\252"ii\255Oa\007\342\256\251\257Oqp\332v\230\312nA\215n''\330]l\222E\033B\202\012\313}*\020m\340l\244H\351st\0366\206\336\353\3512\352\023\205\005)6\271\345\315m\271b\343\036V\272\335\244\374\2755<\372\262\214\252G\225_\226<\256N\367i\250\3355\331?\227\311\037r~\305\377\000\262\007\305\377\000\332\013\343>\205\360G\340o\207\355\265\177\216w\326\332o\210\365\235g\304\232c\352\276\004\375\237\274\025\250\311\366\246\370\207\361\024\335\346\317V\361\306\245j\337l\360\037\200\357\205\301\324\245\234x\213\304\321\307\243\303\035\266\253\376\207\377\000\260O\354\011\360_\366\004\370Em\360\367\341\226\237>\241\342mfC\255\374J\370\215\255\316\332\227\213\376"x\313P\305\316\273\342?\020\352\363\257\332o.u\015A\345\234\207m\221\247\223\014I\025\274\020A\027\237\231\327\346\234p\360\3220nu-\326O\221\306\372\356\354\233\323\241\325F+\225\311\257y\244\243\345\024\243f\274\236\266\337w\262\261\367M\025\345\233\005|?\373y~\336\337\004\177`\037\202\332\307\305\177\213Z\324rj\216\213\247x\023\300\032i\027\276/\370\205\342\333\342`\321<1\341}\016\334\266\243\252\352z\265\361\216\322\332\336\322\027b\314\322H\320Z\303sun\343\0279F1W\224\244\222^M\244\337\311?\323\250\233I6\366G\371\312~\331\277\266w\307\377\000\3333\366\203\324>%\374E\273\272\277\370\271\252I\250\351^\006\370icq\375\245\341o\331s\303w\223-\273\350\236\035\270\022=\215\347\306}CO\267\206?\037x\372\326\332\332?\004\011.|3\341\267\203V\212\346\347A\255\360\337\341N\225\360\313J3\334\371\027\236%\272\207v\241\251\225\214,\000\200\315ify1\303\0262\314\315\231\0303\2663\265~\237\017N4\251\252q^\354\022W{\363(\306\376\255_W\335>\247\227V\2674\245\025~y[T\354\225\355\247\223\266\215i\273\321\037\265\237\360K\237\370%\207\211\177n\277\025Y\374Y\370\263g\253\370S\366J\360\246\255\023N\355\035\336\233\252\374{\325\264\353\267\027\236\024\360\255\342\265\275\315\237\201\255.m\336\323\305\376.\262q4\323\011|;\341\331\277\265\033S\324\3745\375\304\370W\302\276\031\360?\206\364?\007x7A\322</\341O\015ivz/\207\2749\240\351\366\272V\213\242\351\032|\011mc\246\351\232m\224P\332YYZ\333\306\221Aoo\024qF\212\002\250\025\340b\353:\365\345;\373\261\\\260Kkiw\257V\327M,\216\374=5J\224ce\315+NM6\3274\243\035\023{\250\332\327\357s~\277\016\274#w\3613\342\327\355\237\377\000\005Y\375\242\276\036i\353\342?\210_\263\227\301\217\017~\305\237\262~\213u\024VQ_\370\262\303\341\346\253\361\307\342\016\225u\375\270m|=t\276&\370\321\342O\004\351-x\267\3267sh\376\021\263\207S\271\213N\202\306k\256cc\362\377\000\343\257\374\033\370~\037\377\000\301(\217\303O\205\236\007\323>,\376\334v\276>\370m\361\247\305\3762\322\226\322mc\306\232\363O\251xW\304^\005\360~\277\254\336\331.\233\340O\002xS\307\276(\271\322l\004\342\343\306:\226\207c\343mb-?\304Z\342Z\370w\365#\342\257\374\021\003\341\017\307\217\331\337\366\177\375\234\274\177\373C~\323\276\015\370g\360?\340\257\201>\027\177\302\257\3701\361\013\302>\030\370K\342\335\177\302vf\343T\370\213\342\237\007x\223\341\217\212\254\374W\342\177\022x\226[\235j\352\367_\212\341\255%\271\222M4YH\025@\007\363y\377\000\005\247\377\000\202T\376\303_\360M?\200?\017u_\204~%\370\355\343\017\215?\026\276)\352\2726\201\007\304\237\036\370GQ\322-<\025\341\037\015\334\352~2\325\256\023\301\337\016\274.n%\323\265\275S\300zY]NY\365+\251<Kx\367\005\333t\222\1771h\302\342\037\236\3429\3427P\204\226A\006\344\226o\266L\367RDV\366\326_2\332\316]@\230\255U\355\355\346\207\354\367\323\312p\024\2458\306N\235*5*4\224%Z7\366n\351\271A\364\225\225\274\366&JO\341v\357\346\256\277\341\317\355\307\376\011\037\373\014\377\000\301d\277a/\214\376\011\360\276\257\027\201\265_\330\357\342 \213V\370\225\341\013\377\000\2130k\032O\204\3555\2151&\273\327\274!\243k\267\343\307\276\031\370\234\232\225\324w\276(\263\360\335\256\217\240\370\234\351:v\217\254yp\351zM\346\217\373\251\361w\303z\337\306\337\2161\370\017C\271\271m#\302\0326\235c\254k\227\012\217\036\232\367\320\313\177q7\223\015\274\020\303\250j1<\013$\021\333[D\227\226\026\220l\206\020\315\027\363\257\322\037\207\361\\Q\301\371g\013\303\025\214\2163\211\263\232Y{t\246\375\204\032\207\265\203\250\355\245%(\273\354\357\370y\331\2158\313\015*J7\251Y\250''k\244\333M\267\247\303{7\325\351\272\325y\317\300\037\003j\027\037\033\243\261\273\205$_\002\315\256\336\352\252a\2268\332\357N7zE\2746\250"h\306\242uD\206\3728fh-\305\234o*\\\274\252\226\357\372\317cf-Q\211%\244\221\231\231\267I\321\233 2\274\322\240a\334\305\262<\344G\032&\026\276[\350\233\3025\270{\203s\030\343\243*\265h\347\271\215\034-Z\216\362\246\260\325%\203\251w-[\223\243>[+(4\344\323i><\213\015*\024f\245t\324\265\336\317\335N-j\265\326W\323Em\257c?\304\336''\360\367\203<?\256x\263\305\272\346\221\341\257\014xkH\277\327\274C\342\035{P\265\322\264]\017E\322\255f\276\3255}_R\276\226\013=;L\323\354\255\346\273\274\276\273\232+kkxe\226iQ\021\230\177\000\177\360Y\217\370-V\277\373sk\276%\375\231\377\000g-N\353\303\237\261\327\207u\337\263\353\3767\323\347\324t\357\020~\322Z\216\216\370\337"H\226w\032O\302[MI$\227K\321f\207\355^(\226\322\313]\325\336 l\364\215\033\372\367\007E\327\255\030_\335\272sku\010\312\027\277\370\233\345\332\326\275\317r\244\3258J]tI?\373v\357K\365z]tg\363\337s\250M<\213\244i;U\321V\031e\210F\321\351\221\224\006\030\341\213\346G\274t*m\255J\030\343B.n\024\302a\206\357\352\357\331\353\340G\215\374u\343\377\000\015\3745\370qac\250|T\3275/\015i\332\206\245\253D5\035\037\340\255\217\215\256\242\261\360\327\210\374Eg4\260\305\342\217\213^2\275\271\013\360\237\341\227\332\333Q\326\265H\337\304>#\267\265\360\345\243\311u\364\365\253F\2159;\244\251\305$\345\350\222Zm\243V]\366\334\342\245\006\332Vz\275\355\366__F\257\241\376\212\237\360L?\370''\327\204\277\340\237\177\000\341\360\006\236\261\352>7\361N\241''\212\274\177\342;\347\263\326\274O\252x\213R\202\330\337\311\342?\032\375\216\337S\361n\261=\314r\336j\232\234\355\026\232\267\227\017c\240\330Yh\226:|G\364\242\276BM\312R\224\235\334\233o\372\364H\357J\311.\311/\270(\2443\341O\370(\017\374\024\003\340\177\374\023\317\340v\257\361w\342\346\256n5K\200\332O\303\357\207\332A\216\363\306?\021<eu\023\256\213\341\217\014h\250\342\357Q\277\324\256\374\270Q"M\210\246I\246\222\033hn''\207\374\343\177k\217\333\033\366\201\375\263~?\337|K\370\227y.\273\361\323[\270m7\300\036\025\321\365\243\252x''\366\\\360\225\344E%\360\277\202\315\236\335:\377\000\342\315\335\223\242\374@\370\224\321\377\000\305(\313q\240xYmu\013G\324\264\337O.\241)JU\271[p\367i\247k6\334]\367\276\232\357m\264]L+M+E\354\365\226\372%f\276\377\000\323C\250\370I\360oJ\370S\243\254\223\210/\374Y\177\012\235SV\332XC\223\277\354\026\033\371\206\332"\025Y\202\254\223\262\011%\033\266\242u\032\372\265\324k\032$\023\030\256-\356\232\322\360\\\033\013\365\267\231e{\015@ZOkx\372}\352\241\206\351mn-\256|\226-\014\321\310\005{3\245zS\246\256\245(\332\367\267\274\354\267]ox\267\263z;\036M9(\324S\222R\\\311\273\253\331][Kkd\277\003\373\320\377\000\202]\376\336\237\003\177l\217\201\332V\203\360\363\303\332?\302_\036\374!\321tO\011\370\327\340E\231\262\264\207\301\266\232}\205\275\216\227\251\370*\316\334\306u\017\206\367\353\011\267\360\376\245\035\264RY\264\017\244\352qCy\004r]\376\236\327\3128\2707\026\254\343\243^\207\270\232i5\263I\257F\256\277\000\257\310\257\330\206\352\323\341\237\355\207\377\000\005B\375\234\365\250\344\266\3275\257\3323\303\237\265\367\207$\236W\373G\211\274\015\361\377\000\341W\303\357\014_x\243J\272\274\235\222\323M\360W\211~\033j>\014\325-\004VwZm\306\2365K[{\233\035\177B\273\325\020\317\325UC9Kvhav\030\020;\302JF\212\344\233{hf\375\315\355\223\037\2634\3213A"''\232\035\203,k\273\036|\270\362\252\207b\345\027\356\251\332>U\340|\253\320p8\003\201\322\200?\212\277\370*O\300\177\213_\360V_\370,\336\223\373"\3746\277\3274\217\206?\262\247\302\337\005\332\374U\370\204\227\023\276\203\360\352\303\305\272\204\276>\361\277\212\364\265\032Y\322\323\342\027\210\023Z\360w\302\337\010\350\362\255\340\325\265\037\011\035O]\277\322<;\341\337\023x\207F\374\226\360\307\374\023M|/\377\000\005\271\360\367\374\023\366\017\355O\020x\027\303\037\037\274!\250]j\332\342\333jW\272\317\300\230\264+/\216fM{V\323RM!\257\365/\204\232j\351\223\261{y\236\376\372\353N\273\267\260\272\236(\034\003\375"\374M\342\237\016xWD\276\361\017\210\365\373\037\017\350Zz\243\335\337\352\332\215\266\207\247ZKx\304A\035\355\345\354\226ok<\367\022-\2741\315wj./$\216\302!\366\311\022\334\323\360W\206<;c\014\376%\321,\356-\347\361\211_\020jW:\216\233\252\351Z\305\343j\221\233\270#\324\364\335r\033]oK\236\316\033\225\205\264\355^\336\337T\261\220Im\250[\301w\024\321\217;\035\227a\361\325\262\372\325\343\031O/\305\254f\037\232<\334\265a\011EI-\035\3272\352\275z9\224#;6\257\313\252\362\325_\374\276eO\016|4\321\274/\342\357\031x\267KG\216\367\306\327:e\336\244\031\377\000sm6\231\011@\366\221*\356\002\376L\313|\206H\303\310\314\301\233\201]\236\277\257\350\276\025\3215o\022\370\227Y\322\274?\341\355\007O\274\325\365\275s[\276\265\322\264}\037J\323\355\344\273\277\324\365MN\376\342\336\313O\323\354mb\226\346\362\366\362xmm\255\343\222i\345\216$g\034\0349\222\303"\313\352`(S\245B\022\314\263,]\243\016e''\215\306N\274g\356\316*\334\262Z4\245\321\250\265\312\210\323\205=#\263\263n\332\336\332\366\273[v},\177\236\267\374\026\207\376\013e\342/\333\323\304\272\307\354\347\3735\353Z\357\204\277c_\013\3527\332O\214|A\004\207L\324\377\000i\355cJ\325\025\240\324$\362\200\276\323\376\021X\334X\305s\341\375\022K\245\270\361K\310\272\317\210\254\242\232-3K\320\177\2379/g\270\221t\215\037d\022C\034q\313q\014Jm\364\330v\240\216\004\210\001\033\336<L\015\255\251VH\223m\305\302\030|\230n\276\373/\240\350Q\347\222r\253Y\247kr\351%\027\024\226\266\266\355\352\373\336\327|Uj:\263Q\275\240\236\357\266\212\375\364\327M~V?A\177cO\330\317\343\007\355\033\361\203\303_\003>\010\370tj?\026\274Eom\252\353>*\324\364\325\325\274\023\3739\370KQr\337\360\262\376''\254\201\341\324<a\252[-\325\307\303\337\207\327\317\005\356\261w\344x\237\\1xj\010\277\265?\320+\376\011\307\377\000\004\306\370K\373\003\370\005ll\243\266\361\237\304\335N\356\363S\361\037\304\035R?\355\035gU\326u\035\203Y\3615\376\261}o\026\245\255x\303\3052\305\366\337\021x\233PH\356\226)!\360\336\207o\244x_N\264\323\033\014\312\262\322\204Z\223N\365^\216\363\264\033\333\245\364[\350\235\267:\251A(\251\264\323zE5\264R\212_7\273\272\276\372\367\375?\242\274\203P\242\200?\311\373\366\334\375\266\3769\376\330\277\0375o\214\277\026\357\344\237\342\235\374z\256\211\340\177\207:|\351{\341o\331\237\302w\345\242\271\360\347\207''/\366]C\342\326\251\246[ \361\377\000\216\222+h\274''\013]x\177E6\267v\263M\341\377\000\007\370I\255I\360\237Y\213U\263\212\035RK\225\026\272\302\3548\272\265wC\366}7\314\000\302\366\356\013Z\316R9o$\221\376\326\251\014\260\333Y\375=\025\032\024\243J-sSQ\346O\272I\312\375\256\256\275O>M\325\346oE7\245\272\253+[\344\375-t~\201\331x\243M\361\035\201\3244\273\250\347\214K=\255\304[\200\232\322\372\326V\202\362\312\356 w[\336ZN\217\014\360\276\035]{\202\254po\246PX\216\270\300_\\\362z\364\311\344q\352{\361\322\246\247i\307i$\327}\264<\365x\276\322\213\266\276V\177\217_\324\324\370M\361\233\342g\354\355\361o\301\177\035>\014\370\215\374/\361+\300:\207\333t{\327Y''\322\365\213)\207\227\252xS\305:|2\301\375\255\341_\020Y\264\332v\255\247\274\261;[\314\323ZOmy\025\275\304\177\336\327\374\023\177\376\012Q\360\217\376\012\015\360\302=GG\270\323<\033\361\323\302:}\262\374`\370->\240e\326<-~d\026\277\333\372\011\271Hg\361\007\200\365\231\214W\0326\277f\267\021\332\375\262\035\037X{mb''\205\374\034\312\207%UU&\225M\373s$\225\327e-\354\374\217W\011;\323\345mh\356\274\242\324]\237]\033\267\247\343\372M_\006\376\327\377\000\262\177\213~(k\036\014\375\241\377\000g\017\030X|)\375\260\276\011i\336 \262\370e\343=V\311\365\037\003x\373\301\336#\362n|U\3603\343W\207\255\344\267>*\370W\342\353\373[]f\034K\036\273\340\217\032\331\351\2367\360\226\243\247j\266\227K}\346\235g\207x\177\376\012[g\360\276i|1\373s\376\317?\034\377\000d\257\023Z4\020\\\370\264xS\305\237\032\377\000f\315wz\250\207U\360\207\306\337\206\236\030\324\333F\262\2751\274\355\244|E\320<\007s\243\333<v\267\360\307r\2270\307\356\3727\374\024\363\376\011\317\253\330\303uo\373s\376\311v\344\307&\373\035O\343\367\302\335\007W\215\355\316\311\241>\035\326|Ug\256G*H\254\206\327\354/4N\246\031\024J6\220\0174\370g\361\323\366!\370{\342\237\212\236-\375\235\355<C\361/\\\370\367\343y\276%|I\327\377\000g\337\204\177\031>;M\343\337\031\3526:~\213a\251k>3\370}\340\237\025xW\303\332V\237c\243\305c\245[k\332\366\213\341\010-/\356\265H\257 \373^\2425\036\016\333\366}\370\215\361;\366\266\237\366\324\370y\373:i\037\003\276-_\374\034\223\340b\374X\375\247<E\245x\243[\267\360Y\361\025\217\210%\3274/\331\303\340\356\261\251\3517\276 \361\035\224g@O\020x\257\343\267\303\215\177A\360\356\225\243i\272\347\203\365\211n\365m\036\314\003\355\257\000~\314\336\036\360\377\000\211t\337\210\277\021|K\342/\215\277\025\264\324-\247x\363\342+Y<^\025{\233o&\362/\206\336\000\320\240\323~\035|.\3361l\372\247\203<;e\342M_M\011o\342\275\177\304\232\242O\256^}.\210\221\242\307\032*F\212\021\021\024*"(\001UU@UU\000\000\240\000\000\000\014Q\375\177_r\003''\304\036 \321<)\241k>''\361.\255\246\350>\035\360\366\227\250kz\356\271\254^\333\351\272F\215\243iV\262\337jz\256\251\250\336I\015\246\237\247i\326POw}{u,V\326\266\320\3114\322$h\314?\317\033\376\013w\377\000\005\257\325\377\000o\375w^\375\227?fmf\353G\375\212\2745\255\332\017\025x\342\3155\015''^\375\245|E\240\334\011\225\025.E\265\336\231\360\177G\325\341K\235+L\236\326\033\357\024\352V\026\232\376\244\321$Zf\235\244tai{Z\261\213\275\227\275;\177,e\027\253\376\363\322\313\317S\032\363\344\203\356\364Z\333\252\277\340\177=\241\346\225\343\322\264\265\010\321"\305,\221"\030\264\330|\264\362\221"\332\310\367N\233>\315nP\307\034dMp\276O\225\005\327\334\377\000\262\307\354\301\361\023\342\277\304\217\013\374*\370O\242\246\277\361{X\223J\273\272K\335\006\373\305\236\036\3703\341\317\020E%\325\207\217\374\177\245\332\253\267\210\374s\257F\322^\374.\370PY\265\037\027_$\236*\361j\351\276\001\323n\2565\277\240\225X\321\204\252M\351N-EZ\373\264\277\013hr\302<\322\323W+]\365\345\272\273\371''\367\332\347\372(\377\000\3010\177\340\237\036\015\377\000\202~|\007\217\300\372\\)w\343\237\030^\217\026\374F\361\025\341\266\324\274O\256\370\257S\266\202M^\377\000\305\336+S-\327\212\274Kw}\347M\253\352q\315\006\205\034\202\0153\303z]\206\213\246X\371\277\245u\363U&\352NS{\311\337S\320Z$\272%e\350\202\212\200\012(\003\374\216\377\000c\037\013\370s\\\327\374Aq\255\350\232f\262\366\332\026\2754Q\352\326p\352V\276e\215\367\201\226\333\314\262\274I\254\347D\376\325\276g\212x$\212g\222)''I$\264\264h+|L\323\254\264/\027\374AM\032\3354\304\323.\2748t\324\262\335n\232y\326o\364H\257\332\311ce[Vd\276\271\026\255\000\215\264\342\350\372q\264x`h\375w9\272\265o&\357\243\277kD\346\212J\013E\247-\274\264d_\001\265\033\350<m\253\351\220\334\311\016\2324\313\025\026\021m\216\315B\015K\313+l\200B\255\020\202$\211\325\003\305\032\371q\262\306\314\247\352\253\366`\001\311\311\007<\3478\316:\375y\365\3439\300\257G\014\357O\376\336k\362<\334BJ\256\235b\357\362\225\227\334\216^\357\241=rH9\347\214\347\241\343>\375{g\025\330\374\007\370\277\361/\340\037\355\005\360c\342\207\301\357\030j\276\003\361\335\257\304_\013xg\373\177G6\355%\347\206\374K\257\331\351z\367\207\365k\033\350.\364\275oD\325,\245h\256\264\255b\306\372\305\335b\270\020-\314\020M\036x\350\247\206\253t\237,b\343\344\371\255\247\365c\257\015\374Kt\345zm\333\372\364\323c\375F\240fxcf9fPI\300\031?@\000\374\205K_6z\001E\000\024P\001E\000\177"_\360v\307\306\357\213>\002\375\237\277e\257\204\236\012\361\357\210|-\360\363\343\257\305?\032\350\177\027\2747\241]\256\233\017\217\264/\013\351^\024\277\321t\035z\376\3268\365Y\264\030/u[\313\253\275\026\033\3704\275Vsk&\255i|\332~\236m\177\210\033\374Xiwb\311R\331l\364\331\232\331"\215\0268|\233v\362\202G\267\313\010\233F\020\251L\014\025#\212\365\362\344\275\235I[^{_\311F6^\232\337\315\352\3658q:\324\212{v\365\265\377\000#\327>\022\301oai\343]r\013kW\324\274)\360\257\342\207\2154)n\355\240\324 \266\3617\206\374\015\256\353\2726\251sc\177\035\315\206\250\366\232\265\235\265\343\333j\326\327\326W\217\037\227}ms\013\311\033\377\000z\237\360n\237\302_\207:\177\354\275\246|N\267\360\256\236\377\000\020\374C\341\217\206\0364\361\007\214\257$\274\324|C\256\370\307\342\237\302\355\003\305\236>\361f\263\251j\0277W\032\247\211\274C\253\352w\221\317\257_<\372\255\236\205$~\025\323.\354\274/mk\243\302\263)5N\232N\312S\325w\263V4\303\245\315-6V^I\245\177\353s\372;\242\274\223\250(\240\002\212\000\377\331', '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true, 0, NULL, NULL, NULL);
INSERT INTO card_tbl VALUES (11, 'My Account', 'GIF89a\264\000s\000\367\000\000\000\000\000\026\033\027\026\032\032\034\035\035\036\036 \035 \036\035""!\035\036"\036 " \036"""$&(#(&%++*,,''/1+.0''0/.0.&01+33.68/87.891./2344693:;:;<6>@:=@7@?6@A;CD>FH?HG?IJBEFBFHDHGCKMILMFNPIMQGPOFPQJRTNVXOXWNXYPSUQVYRZ\\XX\\U^`Z^aV`bZbe]ei_hg^hj`bdaeibjmhfgjlmempimqgpofpsjrumuynxzrsuqvzrz}y|~t|\201x~\203{\177\210v\200\203z\201\205|\203\212\177\210\216\202\204\205\200\206\213\202\211\215\210\207\214\211\213\215\205\214\222\207\216\230\210\216\224\207\220\227\213\220\223\211\220\225\211\224\226\216\221\222\215\221\225\216\224\226\211\221\231\213\224\233\213\225\236\214\222\232\214\223\235\216\224\231\216\225\235\222\224\225\220\223\230\221\225\231\220\226\236\226\227\233\222\230\235\225\231\232\225\231\234\227\235\235\231\227\230\231\233\234\216\226\240\217\230\241\221\227\240\221\230\240\224\232\242\224\233\245\225\234\241\226\235\245\231\236\244\231\237\250\230\240\243\231\240\245\235\242\243\235\242\245\237\244\246\234\243\253\236\247\260\236\250\262\240\236\237\240\243\244\241\245\250\240\246\256\245\246\250\242\250\252\241\250\256\244\251\251\245\251\254\247\254\257\251\247\250\251\254\255\244\253\263\246\256\270\250\253\260\251\253\267\251\255\261\250\256\266\255\256\262\254\256\264\252\257\270\254\261\264\254\262\272\262\263\264\260\263\270\261\266\271\261\265\276\265\266\270\264\267\275\263\270\274\271\274\276\257\266\300\262\266\301\265\273\302\267\272\311\270\273\302\271\273\307\271\275\301\271\275\305\275\276\302\274\277\304\271\276\310\272\300\302\273\300\305\274\300\302\275\301\305\277\304\307\275\302\311\301\303\304\300\303\310\301\305\311\301\306\314\305\306\310\304\306\314\306\310\307\304\311\315\311\314\316\306\311\320\312\315\323\313\317\330\314\320\317\315\321\323\316\322\331\321\323\324\320\323\331\320\323\334\321\325\331\322\325\335\325\326\332\324\327\335\325\330\335\333\331\332\331\332\335\331\334\333\330\334\336\335\336\337\326\333\341\332\335\343\335\341\344\336\342\350\341\343\345\340\342\350\342\342\354\342\345\351\344\343\350\345\345\352\345\347\354\343\350\354\345\350\352\346\351\355\350\347\355\351\352\356\351\354\356\355\355\356\347\354\360\354\355\361\356\360\357\356\361\363\360\356\357\360\357\364\361\360\356\360\361\362\361\362\365\361\364\363\361\364\366\364\365\366\364\366\370\366\370\366\366\370\372\370\366\367\371\367\370\371\371\372\371\372\374\372\374\373\372\375\376\374\372\373\374\372\375\375\374\372\376\376\376\000\000\000\000\000\000!\371\004\001\000\000\377\000,\000\000\000\000\264\000s\000\000\010\377\000\377\011\034H\260\240\301\203\010\023*\\\310\260\241\303\207\020#J\234H\261\242\305\213\0303j\334\310\261\243\307\217 C\212\344\330N\333\261\\\257@]J\264\207\015\2320a\330\304\021\364\010T\256h\340F\352\334\311\023\241=r\311h]R\244\210\020\037>x\352\220\031\303%K\226)R\2444i\262\244\352\222(pXU\353\311\265+Gn\301Bq\232D\226\220Q\244i\322\214q\232%\252\324\251T\255\036Yr$\312\245f^\363\352m(.\030+R\241&Q\232d\326h\2374m\356\254u\0325\312\324%J\216H>\022y\011\022#F\256J2\266\267s\347h\264@\241\012\325iR\247\301\205\361\244\331\342\304\010\017\0346h\320\210A\303F\216\037B2W\315\214\031\311\021\314\230\243\320\362L\234''\265W\254P\241:u\212Ri\263o\214\314\010\001\241A\203\007\024*P\270\260!\204w\357$P\240\377\230\221#\267\221#\227\317\003\037\342\204T\361\367\036\253\321\262\005k\271\251N\2266=\262\222#\203\001\003\0124\340\200\003\020l\260\302\015?\014\221\204\021A\354@\003\013\342\251 \036\012*\320p\333eB\374\006\334\017Q\370\002\337\207\026\211\363\013-\264\264\242\\(\241hR\305\015\031\0240\300\177!\014\201\005$\277\\c\016:\355\344\230#:<\232SM0\234\304Q\205\017+\254\320\202\013\026f8\004pF\374@\204\025\304\200(\245C\355\020\223\013\211&\236\022\212$C8\240\200\002\004,\320C\037\311\200\343\2159\340t\303M\232\335t\003\016\233\336\274\331\2156hj\243M1\23441\203\013|\276\200[\021F\000*\004n[\2143\345\241\007m\303\213.\272\230\250\334%?@@\200\002\014\310@\0075\324T\243\2515\326d\203M6\325`#*\247\242\226*j\250\330h\232i1\204\030\361\302\013H\352\377 \204\020C\0141\353\017I\014\203\350\256\3774\343\213.\270\300R\337(N`p\200\002\020(\361J2\314`\352\354\263\320F+\255\263\3150S\214*N\320\360*\015\270\315\232\033n\211\360*%;\304\370\002\214-\266\264BJ\025\034 @\000\007T\270\262L0\312P\243L3\323\344\333\314\276\323\340\253/\277\375\356\353\257\300\31403\2152\005\003C\014-h\344\360\202l\011\362\360\003nC0q\215\270\357\201\223\313\271\255\320\202J%)\014\200\200\003N\240\233K0\310 SL1\312\254\354\362\3130\307\374\3621+\323\\L2\305\374JK,V\310f\341\304\203N\374C0\030{\306\215\271\266\370B\013+Wx\251\300\014\225\324''K0\302\004C\265\325Xg\255\365\326\\s\315\261\211\230,1ClB\377\340\303\304\245\024\235W<\317 m\013*\231\310\240\300\000\036l\321I''\234\240b\0130|\363\377\035\214\271}\007.\370\340\203\007\023\270\271\177\333r\367$\230\264\221C\016\026\352Pv\016\222\250\315\025\333\300\344\342\013\272\217h\240\300\0017$R\210!\204\204\262h\322\214\232\314\350\352\254\267\356:\353\271\244\256\313\257\254\323BVQ\204(a\203\015:H\256\303m>\204k\371N\324\370b\274-\254\260\341\000\002\013(\321\007\036Fub\313|\350\316\027;\272\330g\257\375\366\333\317G\342\364\350\266b\213,\255\370a\024\036|Ta\333\343\023\367\236\003#\303\217T\374\346\323W\241\000\002\031P\221F\035m\2041I+\255\200\005\000\003h"a)\347\200\010L\240\002\021(\013T\000\360\200\000$\037*\004\330\2066\330!-R\310\301\015r\320\273\336\361 \007\216\210\037H\256\261\013`\350bzL\270\037\007\234\300\226,\020b\2010\214\241\014g\210\300V\204\242\015l\261\202\024~\307\301\036>\016\024"\354\310\321\377L8='' `\000%\210\202\024\224\030\205<\204BKPD\021\212\264\364D\346X\361\212X\314\242\025\245XE*ji4\241@\005&\226\310\304&\374\340q\217\373\340\015z\020\245 f\344\035\300\320\031-\250\200\000\005\224\300*MPB\026\356\326\211S`\2024\200\354D \007)\310B\022\362\220\206Ld \005\031\012B\300\305*<\374\035\016\330w17Z$\036\3048\227-p\001\206/q 2y\\B\023Ls\267M\360\361\224\250L\245*W\311\312\273a\342\224\257\304Ce\254\322\276\307\331\246\006K\230\207%+\322\214\275M\257\017\237\323\300\020\252"\231%\020\202,d\301\0042\227\311\314f:\363\231\3204\3153\2630K\314\374@\222h\274A\027v9\221h\030\017\026\262 E\006\024\220\201 \320e2h`\246"\242\311\316v\272\323\231M\320\220\206\204F\003\034\300\346\006i\343\346C\310A\377DZ\274b\006\012X\300\017~s\204"\034\241\011\205\231DQ\010C\230\3028\364\241\020\215\250D''z\314\20664\017\230!\350\021\204\360\301\037\354nwC@\207>\035b\014t\275\302\025I\250c\017\230t\2366P\364\2450\215\251L\011!\005\226\032aP\277\373(\015\3240R\206T#\027\265\260E*\200\231\000\024\010\252IF\220\002\037\314\262T>\364\201\251N%D\037\374\320\207\245\022B\017L\225jU\245j\325<\000\342\252R5\214\037\250z\026\246>\365(V=\212Q\010\261\207\215\006jC\023\333\235\317<\324\323\204\214(9\253H\001\002$0\204"|\353<h\310\003R\216\242\007\364\241o\260|\310\203\037\370\320\006\350\341A\017|\320\2055\036\221\207>\354\201\030\363\230G5\004\321\006A\364\241\031\340\330\303Q\004\013\275\304\032\026}U\315\003\372 \233\330<d!7\260\275\351\304r Wn\377\325\025!\317\240\336(\260@\000\002\334\300[\262\225BaU\233\007\325\342\341\270\305=.\036*\210\2767\244!\017o\360\352<\362a\014c\230\243\037\330\355\207=\214\201\331|\324b\017hP\256j\304\213\024\361\232W\271J\360\226z\007\025\033\237q\342\266\005i\307|\300\206\001\005\030U\007C\000\332\021\320\220\206\361\246\345\271\377\355\357r\217+\340\376\246\341\015q\240E>\264\233]~\364\303\301\013FG\034\302\253\3346\010\230\300\252\271\203\205\375\273\334\306\276\366V\270\001\232\020|&\203\034\210\024\276\002!F\272\224s\203\0048`b>\310/n\244`\0063\244\341\014j\261q\032l<\2061\244e\01456\203\217\325R\2071\260\201\015\340\310\356\203\033\214\335O\330\330\014g\000\362\030\332\000\344-\250E->\026\362\217\323"d3\034\241lB\033\024\344d\343\202\312\2418\036\017D\205\037\026\240\200\024\\S\377\010\222\363\201\020\266\320\343:\013y-R6C\026|Lg\265d\001\307=\326\202\031&\241\344B\367\203\032[(r\224\273Lg>\233a\013]\326s\216\355\334\226k\202\371\0077\325\026\304P\374\017j\260"\214\235\250A\200z \347\333\\\263\011{\316\302\026\234\262\352,\350\231-^`\265\252SM\3531l!\032\206fp"\266\240\205\026f\301\013\255\2165\253[\275\030^;E\330\327\354`\262g\0059$\315`8\360uEr:A\210\200\242\340\203>p\237\016\244\340kXOB\330o \305$Z\235\207I\370\272\327Y\240\303<r\375\213V\373z\013\337v\312\036Pan\247\224\333)V\2305[\216\360;\036\232\372\2461\210\001\254\226\000_m\264\2029\241\310A\2008\330o\366U!\013\017\317B\276\245\220\357jh\303\023\267Xw>\244\201\361|\220\302\012 \3077\310\255\340\213%gw\036[\377\310w\310\253\240\362Ly\242\024\367\300\35650>\217Y\344[\342\370\226x\006\321\310\363Y\341\340\005{r\001g\352\032\014P\234\2424\032\030\200\011\036\247\203I>\356\010\024\257\302\024\252\020\025\220W\201\020\013\306\256=\260\213\217~\230\303\012U\010{T\302.u6d\275\0371\247\005\330\271\255\303\266\200\275\017\271\306.:\2660v\252\213]\352B\3409\0321\375\003\027\300\312\005b\250+<H\321\011Sl\302\012\0050\200\205l\000\233\365)\321-cwK\025\222\221\353|\020\202\352U\207<\267AQ\212[\250\342\026\264\020\203\330!\337\226%&#\346J\336:((\256y\310\037a\222\266\261\247mr\3630>\321\000\036=\245\006(\\\211\002\003l\240\275\037\265\215\346\037?\366(\204\001\365J\256F\024\210\337z)\370\242\031\313x\0061\232\301\011''H\001*\255\257\202\032r\255|\346\267\276\011r\215\215\377\\\007\252\003\277\363i\026=\245E''61\211=,\300\000H\012\177l\214\260\374&(\321\372PX>\031sah:,\377\377\000\370x\353\226]\370@\014Q\360\004M\340\004\3267\025R\340\004\313\367\013\205v\017p\020\005\016\030\200\000\270C\265\221\201\262\221\003\230\301''|\302\004#\025\017\353G\026F`\000\016\3403(H\003\014\230\200N\300\202M\360\026Q`\005g\327\017\321\340\030\024\250\2008\210\203X`h\006\210\200K\364\202\216\021\025M`\005\361\240d\311\260\200R\221\2038h\177?\220\202\262a\003\230A\003\036H\003\361\240O\324\267\011\224\300\010(\340\000\033\3600\257\002+3\240\202\011\010\027d\270\004\015\230\200\254p\015jx\015l\240\200d\370\206T!\011\222\340\011t\350\011l\260\004\013\010\027\012(J\024\310\011\332\240\206\325\020\007N Jp\370\206G\240-^\3700\334\322$|\322\002*\3770t\273\224\013\3547\011\177\320\000\014\200\002R\370\0020\200$/\220\003\2058\206o\310\011\304@\014\334\025\007U\361\211/\030\014\361`\017\366p\017\253\210\016yB\025\217\024\212\2448\212w\030\005\204\370\211U\001+\260\002\003\274\250\003\277\321\210-\260\010\372T\032\204\021\005\326\341\201\346\307''B\240\213y\364\030KP\015J\306\011\271\370\206N\360\010\360\020w\375p\015jp\212\262(\213K@y\331%\011\242\244\004SqP\361T\206K\240-\313\010+4 \031/\240\002GB\005\334\324\016e\001\010:\320\000\025\240\214\312h\004\316H\210K\300\004\311\300\212\3600\017\211\300\004\242\344\215G\340\004\263\240\215\331u\017}\340\215Sa\216J\360\013\361\000\017\367`\220\336\010\220qq\212K\220\003\374\330''\347!\205G\322\003\334t\015\015\325\007(0\001\035\340\002*\340\201/\351\002V1\223\036\031JK\000\007\276\377\360\013\277\020\014\277\3003s1\223N\260\013\3750\203\271\226\221\021I\025sA\025t\240\223\276@\014\277\300\012U\200\216\011\231\220\035\271\004?\020\223.\351\002,\340\002\344\307''/\371\016\273d\014\205\221\007\332\021\002\021"!*\360\2224I\223p\301\004\020hh\357\200\010\035\331\004\015\351\220J\226\017\371\020\221\247\350\004\267p\227\330\005\017\324\210\004\036\311\226K`\004\024r\226\024b\003V)\217\342\001\211A\244\013g\221\005\024@\001\023R\231(\360\002k\211GP\200\207\322h\017\016\226][\327\017\236\260\004P\320\004\210`\227\240\231]\360`\005U\341\004\306\020w\373p\017\2670\223s)\027\225)!\342A\003\205y\230.\340\011\273D\012\243\025\005\025\260\001\207y\233\272\231\231\304\344\004\325\360\231\332\330\011I\000\006E\210\232\251\267\215\203\330\014\014\026w\367@\215U\031O3\211\233\226\351\002\222\341\002\207\377)\007\273\204\011HQ\007G0\234\2269!9\200\234VQrZ\227k[\227\017ap\015\361i\227\241\231]\240\340\011\021Xy{@\233U\271\004\342\271\236*0\027.)!\364hI\204\260\\g \004\033\360\001\023\342\235(\320\236\356)\010DI\224\330E\015\242\240\215?q\015\344\240\215\361\240\006\355\000\017\354@\016\342 \016\341\200\015\332`q\327\240\015\334P\015\220\303;\230\206GK\360\002\353)\036\231\021\217\342Q\004\273\024\007w\260c9\320\001fy\243?\200\004\340(J\007\025\005\327Uy\015\226\017j\300\016\206\306\017\237)\010K\000\012\367\271`\361\200\016\340\360\010o`q\334\300\015s\202\015\340\240\015\326\340\015\336P\015\334p\011\226Y!<\220\0314P\234(\340\210(\320\214o\232\233\273\304\006j\261\0057\320\001"\260\246\342\361\003S\331\221J\320\227\271\026\005\247\031\232\275\000s\332\230\017\200\377\260\004\227`\016\363I\242-\012\016\026\347\003\307\240\015sB\0152Y\015\336p\242\330`\016\327\360\015J \036-\000\247\342\351\001%\260\236B\020\0059\200\002"0\002.`I\350\200\006u\260j4\340\035\024\342\235\022\002\250x$\230M\340\005\205\026\232\332\260\004_\220\013\332\320\017\361`\005P\032w\276\300\006FZ\025Q\340\010c\372\245\336\240\015\340p\015\254\340\004\325Z\016\265\200\0026\360\010\276\000\016\334\260\015\334 \016\332\220\014\342\021\036\272J\001\003\260\000\031P\231-\300!: \036!\300\002\226d\016a\220\005\275\366\002\270\032!\225\371\003q\201\220TQ\221\206&a\2424\210Jp\015\271@\012q\227\017\2270\223G@$Np\015k"\016\327 \016k\302\015>\300\014\3250\252\225i\014\342`\016\030;\016\303\272\236\3411N\3072\000\031p\002\177\312\037(\200\253\367\272jKd\003"@\001\016\377\300\001&P\234*\300\253\011\271\231Q\020\235\331\225\017\217P\025\276\020\014\263\000\007\363\200\005\350\300d\331U\015V1\011\304\300\012\211`\004\217`\016.Z\261\342 \246\217\320\007N\200\002.\340\010\244\200\010L \016\340`\016\345J\266\234\000\247\245\212\002(\233\262\006\260\262C`\005?\360\035( \253l1\0054P\263\003p\000\015\260\001\251\372\247\233I\223\216\220k\254i\241M{\232q''\011MpS\340p\242r\362&"k\266\341\200\261\332\360\003\342\001\005\030k\016\232\213&"\033\262\3320\240\336\231\002\032\200\000\007\200\000ut\000\003\240\0009\200\005> \002\336q\002\226\364\016`\027\005P\200\003(\3409\245\233\272\030\020\002n&\230\203i\235\331\245\017\330\325\215K0\0131\227\017\222\000\015\323\211]y\371\246-\340\013\334\240\245f+\262\345\212&eK\266U\020\223\211@''\221\273\271";\016U\377\000\272-p$\031@\272\346\3739\012\020\004[\240\003\336\321\035\226\024\017a\347\030\255\252\001\007\220\000\246K\272\001u\2344\371\016\206\226\017\311`\025Q\340\011\343\240\006\030j\017\354 \016\2400\276|\342\006\301\360\275''\332\271!k\016\343`\016\234 \0331i\005\315\200\016\336\253\301\346\000\012+\340\002\216(\217*0N\237S\272\244K\000F\020\006:\000\002\035@\234\273T\005\216\321\004\255\312\001&l\272\011P\272\034\340\00260PU\021\006A\213]\016\306\017\327\020\010q\021\005\177\360\220\361\320\016\232\373\300\002\012\302Gr\004\276\340\275!;\301\3420\016\344`\0142@\014V\340\2016\320\014\232K\016\346\320\016![\015+\340\265 \354\222\031P\272\007\240\306\0110\000NP\0064\320\001\037\260\001\366jIV\320\202M\240\003*\020\002jl\272k\254\000\032\340\201\333B\007\355\020\017\361\220\017\250\027s\367\377\220\017\340\300\011~\005\012\205\354\275\222\374\015\202P~|\362\301\202\260\304\222\274\271=\300\012\350\360\015\220@\003+`\005\032\334\0168r#\346\320w\374(\001\011p?\366K\272\007P\005x\340\002\033P\3139\260Kf\020J<\200\002%`\302\256|,\031\020\222\232\360\245\334\340\242\024[\314\304\214\0157\360l\346\360\015\233\274\311\271\340\0043 \003|\222\013\317\034\262\355\260\005l\220Y\367\260\005.\020\004\357\300#Jl\312\357\020\276\374\350\000\366\253\000\351|,m\300\007"P\313\024\200\004\273\324\007\351(\004(\220\002\367\343\307k\214?}\362*3\300\012\304<\275n\322\007\234\260\014\324\340\011{B\015a\374\314\022\234#!K\016\244\340\201a\240\271\034\214\312\232+\011?P\015\350\320\014= \003q\200\016\341\234\304I\374\016q\300\217\000u\303\010\360\312\011\220\001}\220\007\033p\001\332\201\005\273D\216s\377a\004-\220\002\313\003\313\367s\000\016\020t|2\003Q\254\271\335\200&k\022\004\202,\033\025\275\271\034\334#a\254\016\361`\016 \011xL\255\304\232\233#\276 \205R\350\002Q\360\015\355`\017\357 \322\360\360\016\216\340w/ p.P\302\346[G%\320\007fP\001\332A\001\220\260K\254\000\031t\341\002+@\001\307\262\306\366{,a\250\2103`]\017\274\271\343\320\014\222\360\005\357H\010IP\312:b\312=B\014a\320\300\355\000\016\2400\003\017#\0127b\312\250\374\324>2\003a\020\003F\020\014\354p\221\360P\017I\\\310\357\220\010_\250-\370\374\312\007\260\000G\224\0034\265\0356\333Fnd\014J@L\242\334.\367\273\306\307\262\002\262\361*.`\315\013M\321\024m\0154 \006\244\320\004\350P\332\213\235#\357\360\010H\262A\016\3630J`\016Il\3359\362\324;\342\002\315\360\002]\377\360\016\363\200\221\360P\310\206\234Yq\220\202||,\370K\272Q0\011B0\231\015@\001\347\260K\337P\025\267\015\222&\200\276)]G\004p\231(x\011T]\312U}\321\276@\012T\360\325\213\315\334\355\200\006\262A\033\232\026\005\343\360\0169B\242\022>\341\246L\003\325\300\011\271p\221\363\340\016\231U\336\360\200\016U\240\21040\003\036\360\307\365k\272\012@\007\223`\003\0240\001\024\300\001\3724\210\222\241\312\006\240\342\352=\000\034\220\202Q\300\016!\335\3349\302\016\350\300\012Q\360\016\343\375\016H\336\016Hn\344\361\260\014\2400\010\220\323\005\315\020\326K^\345\3600\322\350@\003\313}\017W\356\212\361\220Y\357`\017Y\0129\016N\003\313\303\333\245\253\000\033\3008!`\263\016\020\004\372\324\007\352q\004/p\327\371\254\342\012\200\001\355%\033=P\015\343\235\335"]\332\357\360\013I\360\325\360p\350\210\377\216\350\205\\\310\207H\012F\236\350\210.\322J~\0159p\017\256\250]\361\220\221\031\231\017\363\220\2355\260\3474p?\372}DC`\236\024`\211+\256O\263p\333#\351\0024\274\333*^\033{\216\010\350p\345_m\017a\235\304\367\360\016P\215\016bn\310\300\016\354_=\017\357\020\005?P\012\301\236\354\005)\346\363\340\013N`\017\037n\017\210\234Yb~\017\350`\004\300g\00320)\352\215\277[\220''\017`\211\014p\013\372\004\016Jp\036HP~(\320\355)]\272*\300x\214G\003>P\015\363`\310\365\000\017\362\200\017\357 \017\034\256\004\325p\221\311\016\354\270^\220\340]\357\001\017\354\207\036\017b\215\010\255x\017\322>\017[7\332\3750\017\223P\033\032\204\0034\220\337iM\272\020\300\011\2350\003\250n\000\270\247OUP\230\277A\0032\260\000y\235\0007\254\346\026\002\033\2645\004\321\377P\204\253h\017\362\300\212\370\340\360\222@\012\355`\360\007\217\353\355\220\017\265~\360\001?\017L \015[\347\360\241I\017\3305\017\254\220\003\030\037|\236\243\342*^\003\234p\011\326\001 5\320S\245\020\031\031"\033\036PG\352\374%\013\267;\264\205F\264\320\351\363\211\017\2548\361\350\200\005\231\025\367r\037\336\226\376\345\323U\220s\237Y\356\300\345\367\320\014;\310\017\366 \274\332e\351\256\330\007\264\305x\032\3648\0150)\350{,[\200\012J\320\000\377Q\000f\246O\3410\004\223\241\312\012P\000\237\303\371\0030\000%\346t\331\024\006\322\350\360\252\211]}`\014\235N\370\254\237\0175\017\355\231\316\372\254\217v\206<\010\315\320u\3279\237\277\240;zg\0037p\326"37_\002\001\241@\012!\260\371\251\353\016u\265\006\230\261$\262a\001_\322\000_\302\000\013\300\001h$\372\032\344\003N\260\010\301\377\340\241\360`\017\342\360\014\243\351\353\254X\376\254\010\361\254\350\212Jo\376\345_\357}o\0045\262\016\362 \017\347\000\015\264 \347L\207F\353\223\003\036`\000\322\037 \000\241@\201\022Rm\014\030\030P \311?\206\015\035>\204\030Q\342D\212\025+\022\033r\344\210\020\0354\\P\000\0112\002\310\0349t\224D\231R\245I\223^\346\315\353Wo\337\275{\366\354\321\254y\263_M\2326}\336\023\227$\307\217\225EK\236,\011RB\310\011\033B\261Ba\240\300\000\005\303,^\305\232Uk\304yN\214\010\371J\203F\210\012\025,l\330p\241\302\013\224:\334\026=\211T\307\215\037t\362\305\353\267\257\236\275~;\355\355\353\213\363\257>\276\374\352\3353\347d\250Q\226qS\352p\221\366\002\205\262\033\232\240zd@@B\031[=\177\006\375\360\021\330\2156h\274\350\020\242\003\210\017\252Q\374p\0337\366\377l\331:`\347\360\021''_\315\2766{\363\355\007<xp\276\340\224\364\220\013\233\366[\332&E\204\010\261\001\372\006\021\241\\\375\240j@A\244\320\335\275_\275fD\274\220\037bU@G!B\275\010\226\312\227\3336y\373\307\374\034Q~\365\356\233\177''~\276\370\372\275#\205(\345\210z/6\001\335\372\301\006\365BH/\004\022\274\210\205\224\006\250R\200\203\357.\304\020\242>\206\000\313\210\034\304BA\005\024F\034\221\006\003c\363!E\037\020t/.\037p\213\302\227{\362\321\257F\340\306\001e\210\371`\223\317$\004W|\017\266\027H\024Q\205\034b\251%\007\012\007\240#C''1\014\317\210"\204\020\342\303\027\\\010\321\005\025\266\034jG/w\264\215\305\035y\240\317\007#\020\331\345\232\276h\354\347\256gH\011c\010\037~Xq>\333x\004\363K\004\341sAK-QX!\216\\2S\200\000\005\377\034 \347IE\275\353\343+*\313\263\241OI\373\244\341KK\365\274\323R\025\177P"\212*\234PbN=\177 /\323K/\325\301\206\025T\350s\005\027~\360%\227\024\014\025\210\212Eo\005\015\235#\246$/\007\033"\235\264OSO%V\007\362\210E6\331b\203u\341\006Vr\211\302P\004*\204\007Wk\267\312\205C#\350\263\362\005o]x\241Rb\217\235\217\\K\315E\026]t\317-\267\274\323\\8\355\205Ix\311d\001\201\0248\340\226k\367\315\252\012\362z\245\241[\261\302\315\341QR\251$\027\341\203\017F\370\337\206\313m\370\321\210!\216X\341G;\022+\336-|\361E\006\201\0268 \010~G\266h\031R\177(\3426\3232\026\313\006\206\027V\270\335\223\277Tw\335\231w4\370\324c\205\370\3654\032l8B\227`\260\020\010\201\0030\330\206d\245''bd>QQ2\255g\033\377\012\266\270b\253\257\306:\353\254\177\330\366\303_?\324\201\224`\020q\000_\201\270[:m\210\254 \225\007\267\217\372\332\327\370\264\246\273n\273\0336\002>_\177\025\202\024b8q\200\252\004\006\350Am\303\035"''o;\201D*%\206%\276;\362\310IU\311\207N\202a\205\203\242+t\347p\317\377if\2100\201\274\315\307\271\253\226<u\255\225+)\305N8NA\001\252\016\310\300\230\317?g\205G\306\007\014Sf\325\177\337z@!P\001\306\027\024\212>\240\252\333o\247\303\007\267y\350rO/+~\374q\340\257\256>\317%P\011F\326\315\0218d\371\345\303x\013\245\235i\236\370z\273O>\377\344&l\351\336\343\242\021XH\374\345\267p\036\372\243pV_u\212\255G\230\031\200\021\014Q\034\017\001\323\032\200\255\354\267\274xPa%\020\023\217\243\034\366\260\376am[\010\023O\023:1\300\37788`Z\010T\303\002\027\270\005\225\270,b\021\374\012\305*\210=\256u\310\010V\200_0\234\200\200\262\231\015\013"\024!\035nP\222\251\331\3006.D\241\301Vh5\256\211\347\010FX\002!|\021\014Z\334@ \007\200\242\002n\210C\021zb\207\277\272A\300H\205\302#\216g\202\025\304\231\021\215\320\004A\320\202cU\200\200\002@\226<\014x\202\212T$\006Qh\200\203\225\015%\202\032Y\302\021!w=\353\031\261\011r\260E.\200\301\211\020\334+y\011\340\2003\336\370FqD\341WQ3M\016\270\270\204% A\202*\004\340\004\333\265\255\203\035a\011Q \004+t\341\013R\344\240\000\367z"\020\322\261HV\202\342g+\313X\301\272H\311%4a<\374\313Y\271R\37082yR\012w\270D+\314x\211\037 \004\225\010\340\200\033Y\271LqTa\006>\213A\014\304R\260<\342\3771\012Qh\302\022\322\267E!\356\314\010R\030\003\037&\201\212V\264\302\026l\210\312\000hU\264 $j\231\357\374\307,~P\203\030\274 \232W\372\231\016\220x\004$x\262\011R\000\325F\326\205\260#D!\013y \304$:\321\011T\310\342\022K\360\200\002\016\222\220{\225@_\360\304\250;\350\220\261p]\011\\\341\312\010%\263\331\204(H\241\012U\220BJ\245`\205,\214\341\016\204\200\251B\031\212\212FDA\005\015`\200T\002@\241C\315\201\036\030\005*C\314\301\210\017\311`\006\036\235\324\013|\210\304\224\262t\013c@\303\033\360\260\007@\020\242\021\223h\004\037\252p\004\033x\000\002\016\300\251\002\012P\200\203T\210\016\352\010jZ\031"\017O\224\007\\\223bU\237\266\004\256\251\375\020a\251rAt\320R\001\010P\000\254\022\225\012UR\240L\265\026\226!\276\210\202G\2025\327?\221hD\203\317\011\301\007\320\262W\220@\240\0018m\200f\032\300\004\333\031\326\263\0151\207(\226 \203\024\270\240\005!\022Q\210H4\002\350\204\000\004 \230,H& \201\006\200\325\000\016\350\201''\334\371Y\3366d\034\304\030\204\003W\000(\307>\207\265\222\235l\005*\013\201\023\004\001\016\275`Go\245;\021v\030\303\023p\360W\244F\320\032\351\240\300\00598B\0278A\214vL\327\274\347Eoz\325\273^\364\006\004\000;', '2009-06-06 14:40:14.626574', '2009-06-07 09:56:12.376347', true, -1, NULL, NULL, NULL);
INSERT INTO card_tbl VALUES (12, 'Switch', 'GIF89a\264\000s\000\367\000\000\000\000\000\001\004\002\002\007\004\004\001\001\005\005\005\002\010\004\003\014\007\005\010\006\002\014\010\004\015\010\010\010\010\015\016\015\002\020\012\003\023\014\002\024\015\004\021\013\005\023\014\005\025\016\004\032\021\005\035\023\017\033\026\020\020\020\025\025\025\027\036\033\030\030\030\035\035\035\003!\025\006"\026\005$\026\005&\031\007(\033\006*\034\006,\036\010+\035\0050 \0054$\0069''\006>*\0100 \0105%\0109&\010;(\011=*\026;/\0314+\030:.\025;0\030?1!!!%%%(((---111888===\010A-\013E/\011H/\010G0\011H1\011N6\007R7\007V9\007]>\012Q5\010T7\010R8\011U9\012W<\015R8\011Y;\012Y=\012]?\020H3\032D5\007_@\014_@\007eD\006mI\011bB\012bD\012fF\014bB\011iG\012iH\011mJ\011nL\007rM\007wO\011qK\011rM\012tO\007vP\007~U\011vP\014vQ\011yQ\011{T\013\177W\016\177X4OD1[L(dN@@@DDDKKKSSSYYY^^^```eeegihhhhmmmpppuuuxxx~~~\007\202W\007\205[\006\211]\007\214_\011\200V\010\203Y\011\204Y\010\206\\\010\212^\007\217a\011\216a\003\225c\001\226d\005\221b\005\223d\004\225c\005\225e\006\227h\000\230c\000\230e\005\230e\010\220a\010\223f\010\226f\012\226h\015\226i\011\230h\014\230i\021\227j\026\225l\023\231k\022\232l\026\231n\030\233o\027\231p\031\233p\030\235r\035\232q\035\235r\036\240u!\237v!\240u''\240w%\242y+\240z)\242|)\244|.\244~2\245\177.\246\2006\246\2001\250\2019\246\202:\250\205<\251\205<\254\211B\252\211@\255\211A\255\214D\254\212L\256\215E\261\216K\260\217R\266\226W\266\231U\270\231Z\267\232[\271\233`\251\217a\272\235r\246\224g\273\241l\275\243|\273\246g\300\244k\300\245o\301\250p\301\246t\303\253{\304\256\177\310\261\203\203\203\214\214\214\202\220\212\222\222\222\234\234\234\245\245\245\253\253\253\264\264\264\274\274\274\200\303\255\203\312\265\207\313\270\213\315\267\214\315\270\222\317\275\223\321\276\225\322\301\231\325\306\235\324\302\236\326\306\243\330\307\244\331\311\252\332\314\260\334\317\262\336\322\263\340\324\273\341\326\275\343\331\302\302\302\314\314\314\324\324\324\332\332\332\303\346\335\305\350\336\313\347\341\313\352\341\322\355\345\325\356\350\326\360\350\331\361\353\332\363\354\335\361\355\343\343\343\353\353\353\340\364\356\346\366\362\351\366\364\354\370\366\355\372\370\364\364\364\363\373\372\371\371\371\371\372\374\372\375\375\375\375\375\000\000\000\000\000\000!\371\004\001\000\000\377\000,\000\000\000\000\264\000s\000\000\010\377\000\377\011\034(\260\316\2313\000\022*\\\310\260\241\303\207\020#J\234H\261\242\305\213\030\031\3268#\207\240G\21752\212\034I\262\244\311\223"1|\364\250\000@\2056+c\312\234\271\022\032\253O\232r\352\334\311\263\247\317\237@\203\012\375\351I\0254\232H\223z\224\003#!\235\225\010\001\300TJ\025)&A\211\260&\332\312\265\253\327\257`\303\212\035Kv\254\245\252he\322I(\343\243\313\264p?B*K\267\256\335\273e\037\305\335+P\006\2005\004[\362\335\313\011\257\341\303\210\307\212\032\034W\260\300\220S\031\243ML\271\362aA\222\341\276\375\007@e\346\252\256\264Z\256\214\325P#C\242Go\225\365\271*\302\177k\341\264\246z)\265\352\261XK\343\251\322#\305\207\011\021\202K\350p\202\007\225;\206\270\332\266\353i6U\000\007\0018W*\351\266]Az\200p(@1\200\004\034[\222#\377\3264=i\347\246\345\221V\267Nv\320\223\016\001FJ\360\341\3470\371\3643+(\304?s=\373\257\202\030\262\204\004(!\300\003 \313\215u\037\177+\265\224\020\2031\371\367\237r[l\200\322B\016H!\036]\013BH\320B\036~$\341\177\202\014\262C|\0272dB\037uu\030"g\373\2758\320\210\354\335\301A\212\017Ap\005\2072\016\004b\217\377\320x\333\025\017\340\010Q\000H$\330\225\213!\376\330\243\220\226\011R\005wFF$\304\206`1\351\241\2232BYY\025\004T9\321\020J&\242%\204\\\276\350eb`P\231Q\000\005\024\200bFRd\225%\2200>\010\344\232\207\341\001\301E\011\214p\004\027~\024b\210!yt\001\305\015\004Z\024\200\027a\235\311`\232!\362y\227 \204|`Q\010Q\014b\347V\266\031\342\205\012\002T\024A\036w\002I\251\207\226\336\005DE\034h\377\021\210]\206\360\201\302\234\021\241\220j\217\253B\330j]b\270\011\221\000C\020rY\025\015H4\301\026\273\312\330+\203\277\226e\210\010\023=\000)e}t\360P\000;x\332\354\213\317\362\027-YZL$\301\036\243\015\022BC\032\200\241 \236\341\3427\256X\202\200 Q\004|\3346\210\266\011\011\000\304 e\232\011o\214O\216\006\006\256\015\025p\355m}$\273A\030\001o%)\177\361\2467/\200\202\250 \021\021$VQD!\021K<\260\236\005W6H\002\021\205\200\345\204!O\214_\305\345]\374\225\023\022}1\241a.\247\007\363t2{\2451D&\334\214\363\310\322\355Y\231!\023Dd\205\320x\345\\\336\316\316\365\314\225\037\010/\004\301\312L\223\345\364tP\317&\365VWDTB\326wm\355\\\327\255}\235\310\017\021AAZ\310-\022\215\247\332;Dd\007e\233h\003\213jf\317\377\206\366gj\227\000Q\001\213 f\210-\360\360\203\316\\\226\365\335\332\337\231}-\310\010\0201\200uY\202p\322\015?\375\360\303\317.pk-\267\321\211\011B\355C\016\\N\257"\273\320\3039\347\375\260\343H\343\243\227\234\030\345\250\253\036\326''\336\300\336\317\357\235\337B\273\252\004wY\031\011\020!\340-YX\365R\217\357\300\377\036\216\356q\023O\262\361\224\351p$\252ui\023\375\367\375\344\023Je\216\177\006\271dj\033\021\021\263u\205b\017\370\321\023C~\355\330''F3DC\\g\015\374\300\213\023z\244\364SSe\370P5\205|\340.\246\200\336\367\356Q\011\312\224/3\347c\214\332\014\021\201a\325\247.\206P\007\377:\327\012\007\006\260R\226AAD\200p\227hl\220\037\300\360\240\365\212f\273\3044!"\021X^Yh\241\300\350QC\205\274*\236\000+\223\007a5\244\010v\351D\015\377\201\347\015\034:K\207 \264\314\011"b\002\352u\205\021\371\330\240\377\022\363@\311Dp0j\333J\026\036r\000#8\261+\206\200\307\006\325q\010*~\220U\243\021\204\205\030\022\2021\340E\020b\344\037;\276(\226*2\346\212|\311\342V\246\260\020\003 \201\216^\011\343\006\327\361\277\257\330q0x\334\213\036\023a\010\017$D\004\371:\214!\356\261At\000\362[MB"\032U\003\206\010<A\021\211\341D\024\371\367\215Bz\345\220|Id\\\026\311\310?|\3520\263\330`?\256aDpi\322Wd+\241,\345g\306\025\316-\227\030<\307\011gQ\313L^o\207\300\304\015)F\011\277|h\242\230[\272%\264\222\211\233D\230\220\177\374P\307%1\031\315c&\221\232a\331\304\3636\030\215\371\371\222tu1e:\023A\015Y\362\203\025\352\\\322\031q\231NRt"\236dQ\0053\341\267L\016PB\023M\322\024W] A\214{\200cv\326\251\204\006O\310\313\177N*\240\362\302\334)\316\301\271}D\003\237`\201\0047\366!\313zdbx9\364\346&\305\022\211b\344\303w\371\030\006F\273r\210j\0141z\322\030\015*\367\377\242J\270\254i\025\302\004_>\212\241\210\225F\002\033\262\374\035=\012\003\322#\212\224\236_yD4\366\371=~h\243\201\210\001\2058:\347Na\254\224+3\215KM\323\002\245hp4\250\354\240\3056\023\001\011_\324#\250\277;\007B\213j\313\243N3K\357@k\370\276\361\212\344D,\022\271P\307K\243\307\217{\224\3426Y\325\014D-&\226X0u\203\371H\0071R\321\010\333\010\202\022\257\240F\342\344\332\217}\200\016\260\363|+\200\022\341\213\303\272\263\036\347\340F5\242a\215n\250\203\222\224\375\035?\250q\325SfV\240bQD4\366\232\332\332\376n\033\215`O`\323\262U\264\254I\020\212\230\006mm\213V~p#\022\240\302\3549[\030\026C\024C\037\304\215.?\254\201\334\377\354\026-\275\255\012\237\260\242\210\\\274/\272\251\315\0071\306j\227\353V%\273T\211\226)\026\012\336\377\240\266\343\025L3\357s\006\033\263\273@\302\030\250m\257N\257a\211WZ\367\265\021\305\313(\270\341Y\342\362\003\034\253\350Jk\001\270\334\372\335\305\020\250\310\306wk\373\272|t\303\025\344\035\017\200\011{\231Dp"\030\342(p3\321Q\214O$\002\021\311\224\257R\320K\235(i\242\026\324\000\207;N\372:~\344c\036\342\250F.<\221a\266\032\223\205\016\246\214V\014\361\010M\204\202\024\241\350\004%\002\261`\207R\214\276<\003\247\224\261\272\341\372NY\312*6\017\224\243ve,W9\312]\246f\226\221\302\342\244\2602\314<j02\321L\2661\323\244\314H\231\304\241\346L\347:\333\371\316x\316\263\236\367\314\347<o\342\313\316Q\002\002\006M\350B\033\372\320\210N\264\242\027\315\350F''\232\002\200\236M\013\304Di\223, \322\255\231t\2457\235\221K\2539D\232\346\264\250)\342\351\220\002\377\371E\241\216\210\002\0260j\207X\200\0063\320OIJm\324S\203:"\031@\2069\364\241\017y<\303\015a\222\0013\220\261\020a3C\326\000\230\0013\230\301\35250\203\015\000\310\300\262\247Mmf\003\200\000m \007t\373\201\017g\320@!\312V\006Ch\260l\007-\204\326m\265\265\207R\315\020\033\310\003\037\317@\0062\232\201\217~\034\003\0001\350\207<\024B\200g\374\316\006\012iF?\226\221\020g\364\303\015\000P\003\257y\335\217\205\353\303\034\000P@3\364\201\017f\320\001\031\344\3406\032\022"\207~<\203!t\350G9\034\202\356\037\343\211\335\374\336uT\022\222\2016\204\251\002\370\320\207\254\331\000\2748$d\006\274\216AB\344\321\217\031\\{\001@?F?\224\001t\240\003\000\031\372(GS\022B\2009\364c\034\011\021\370\275\027"pf\220\034\323\237a\301C,\300md7\204\347\377M\261@<\3641\216\201''\204\031f\3077\267\303\264\020\203C[!0\210\271\317\031r\006\266\233\343\340\031\310{\006`\020\217~\300\341\352\237\366\220\326\035\242\200\276\313c\034\3148F\0332\260\220r\364#$\312\350\2073\326\320\017r\340\233\342K\247<\324\031\322w\277(\304\015\222\217\010\006\266\015\277o7\244\344\335T7\204\006\357\2203d<z\370P\203B\374\255\206\032\360Z\0063\340\266\002\226\221\366\243\367\203\330\013\201\201\276\331\236\020d\364\243\016\021I\203\276\225\301|\346\033\034\037\254>=\3263\303\372\207`\340\014k\240\3038z\035}\336\307!\343\367V@\275\323\200\017|0>!e\227\375B\324\340q\206D~\016\021\021z3\030"t\313\003\336\324x\252~\360u\276\020\005\374n\351B\327w\361 k\216\327w\342\226\020\342\327\017K\247\020\3067u\012\341tV\307\020h\340 \006''\007\014\341\377o\007(}\201\267z\016a\003\370\020\017\352\227\020\312\007q\011\001\007\300\263\006\001\367;\346\247\0204\320\017\361@|\011\341oi\300\0202\020sowmHG\016a\302s+wm<w\203\014\201z\000\205\001\252\307 \372\307t\314\000]\346\300\014\313\360\014\0247\203\011\301~\036G|B7t\013\001z\316\300\020\004 \017\372@\204\364\367;\345\320\014\316\320w\370\260q\371&s\305\366;\236\247\201=B\204!\201''J\360\020\004\260\006\317Po\334\366\014!\241\0202\360\014\317`z\011\201\006\343\360\014\347\307q\277\306\02000\016\363\347\020k\360z\334\326\014\236g\003\317\020\201\012\001\210\316\000\203\347\006/\025\200\202xb\006\022A\000{\027}\255\246\020\013\020\0030`\211"\221\001\360B\003\234\241\212=\242\013\006\020\212\260\330\020\262\321#\026\370\024\372\201''E\020\213\272\010\000B\370P>\242\000@\362v\013(0\000\273\330j\007\200''M\321\021\002\201\202\254(#\277\220\004\007P\214\234v\001x\202\202mA\020DX\001x\302\013e\260\002.\360\215\340\030\216\3428\216\344X\216\346x\216\3508\216+\260\216d\220\014x\342\027\300\370\021y\010\030xR\217\366\330\032k\001\000\327\030\023\346\266\200\322\370\217\000\211\021k\010\000\3128\023r0\220\001\231\220\012)\02100\213\036\021\020\000;', '2012-06-28 12:51:12.364958', '2012-06-28 12:54:14.20365', true, 10, NULL, NULL, NULL);
INSERT INTO card_tbl VALUES (13, 'Solo', 'GIF89a\264\000s\000\367\000\000\000\000\000\003\001\004\004\001\003\005\002\005\006\002\010\013\004\013\010\010\010\021\006\021\025\010\025\033\012\033\020\020\020\030\030\030\037\014  \013\037"\014"*\017+-\020-0\022/3\02247\02498\0257<\025<   (((000888@\027?A\027AF\030FK\032JO\033PP\034OS\035S[\037[] \\`!_c"cj$jq''nq&qt(uy''x{*{@@@XXX```hhhpppxxx\177\177\177\177+\200\203-\202\207-\210\211.\206\212.\212\2140\214\2170\222\2201\216\2221\221\2221\224\2252\225\2264\225\2273\230\2268\227\2268\230\2302\226\2304\226\2347\227\2312\231\2312\234\2315\231\2326\234\2356\232\235>\227\2329\232\2329\234\232<\232\233<\234\2349\232\2349\234\234<\232\235=\235\236?\240\240>\235\240?\240\235B\230\236@\236\235E\236\236B\241\237H\241\240@\237\240D\237\241D\241\241I\242\242H\244\243M\241\243M\244\244J\242\244J\244\245M\242\245M\245\250O\250\246Q\245\245U\245\247Q\250\250S\246\250R\250\250U\251\252Y\251\253X\254\252\\\251\253]\254\254[\253\254Z\254\255_\251\255]\255\256a\256\257h\256\257b\260\260d\257\261h\257\260c\260\260e\260\262f\264\262i\261\262i\264\262l\261\263o\264\264k\262\264j\264\264m\262\264m\264\262q\263\263q\264\263t\263\265q\265\266u\266\266s\270\270u\270\271z\267\270}\266\271y\271\272|\271\274z\274\274|\272\274~\274\272\200\270\273\200\274\273\205\273\276\201\273\276\201\275\275\207\275\277\213\277\277\203\300\301\202\276\300\205\276\300\212\277\300\205\300\301\211\301\302\214\301\303\215\304\304\213\302\304\215\302\305\216\304\303\220\303\305\220\303\305\221\305\307\224\306\310\225\307\310\224\310\310\231\307\311\231\311\312\234\312\313\235\314\314\233\313\314\233\314\314\235\312\314\235\314\311\240\312\314\240\313\315\241\315\316\245\316\316\250\316\320\244\317\321\251\317\320\245\320\321\251\321\323\253\324\322\254\322\322\254\324\325\252\324\324\255\323\325\256\324\326\262\325\327\267\330\330\262\326\330\264\327\330\262\330\330\265\330\330\270\327\331\271\331\333\273\334\332\275\332\332\276\334\334\270\333\334\272\334\334\275\332\335\276\334\333\300\333\335\301\335\335\304\336\337\305\340\340\304\337\340\311\337\340\305\340\343\313\343\346\321\345\347\323\350\350\324\346\350\323\350\350\325\350\352\331\352\353\334\353\353\337\354\354\333\353\355\333\355\354\334\352\354\335\354\356\341\356\357\343\360\360\344\356\361\345\361\362\350\361\363\351\364\363\355\363\363\354\364\364\352\362\364\352\364\364\354\362\365\355\365\367\357\370\366\360\366\367\363\370\370\362\367\371\365\371\372\370\372\372\372\374\373\374\372\372\374\374\374\371\372\374\372\374\374\374\372\376\376\375\000\000\000\000\000\000\000\000\000!\371\004\001\000\000\377\000,\000\000\000\000\264\000s\000\000\010\377\000\377\011\034(0\306\212\025\000\022*\\\310\260\241\303\207\020#J\234H\261\242\305\213\030\031fX\361\202\240G\217\0312\212\034I\262\244\311\223"\027|\364h\000\200\202\026+c\312\234\271\222Q\025!=r\352\334\311\263\247\317\237@\203\012\005\312\204\020\315\243H=\276\260\220\020\306J\204\000`&\235z\264\011\221\253X\263j\335\312\265\253\327\257`\303\032!\242D\026\325\2631a$\274\360\321%\332\267\037\201\204\235K\267\256\335\260E\340\352\025x\001\000\013\202-\367\352\275r\267\260\341\303^\215@\021\0147\260\300\220R\031\237EL\271\262a!\237$\243u\373\017\200J\315T\353X\036M\032\254\031\320T\021\376S\353\002\365T2\245c\313\276\312\304\365T\000\007\001\330NJx\266o\313\265w\037\365\314T\370\321\336\277\261\032\261\322\205\314\232\347k\314t\261\242dlr\332\306i*P\230}&r\331\313\327\004\377\2124\252\225.b\310\240ASf\354W,S\221\006\261\321b}v\360\356+[&\304\037\363\373h#[\350\261\011-\317p\323N>\374$\250\340\202\366\274\323M4\271x\322G\027\365\221v\037\177\004-\204\341G\376Q\246\304\032\226\360r\315;\366$H\0179\330X\223\2141\305\024cL3\326`C\316<\012\276\203\0150\236\260\341\204\021\025"v\341\206\235q\007\344@\035\032\246\004\033\246Ds\216>\374\320\323\0152\260t\242\310\036s\254A\306\225d\244\001\207\036\212t\262\2121\334\304\303\217>\350\\\263\012\035J\214\366\343\206\032\016)P\221v\031a\206)#\362S\3178\301H\270\206\026H\210\025\205\031z\\\242K7\365\360\023\0176\261\260\321cakb\330\246\233p\322\325D$\326\274\323\3445\250\0002\206\023w\215e\004\022b\350!\31254\306sM(XP\326(\177\217\016\031iXf\360\377B\216\235\332\2102\207\026\244E\301F''\327\320\303\017:\306\270\341\243\233\002\265\012\344\253]\361(H\257\277\2622\207\025<\2226V\024k\234B\216>\365`\323\310\242s\255\212\237\261\033"\313\225\022\237\2143\3465~p\301\355\177V\320\241L\241\345\254\002\005\243\304\006\271\037\261\342f\345\003\024\261\244\323\344,d\034q\335UFxa\212\257\356\350\302\305]\336v\007.\206\371b\025E.\357\354\003\217$Q\014\254U\024\204\234\323d0W\024QW\303\331=\314_\304D@\221\013\215\347\370\361\204\021>h\234\325\021t\210\263\017= \217\\\257\311\370ElD,\357\350C\016\035\353jl\304\032\334\030\312K\306\335\356,$\244s\0351\012:\373\014]\264\314B\244\221\364;\260\244\031\026\311\306\361\334\235\270F b\3569\202\214%\304h<\264\255\203\016;\264-\226\021t\230[N&WcG\254\330\331\211\377\313\2065\374\274S\311\313\226\355`\303\014(\214\360\001\007\034t \202\0113\330\260C\314^)q\010:\374`\243G\336`\013\307\267q\310^\221\013=\364\264\302\205\310\224\3410\203\010\022\0340@C\001\024\000\001\010*\330\300\203WZ|BO=\301x\001V\347\273}.\334\253FDrm4kT\246\203\012\035$P\321\001\033\240p\203Wd\000S\017:\242<\361\025\360\266\011\277\333\253k<\303\0179\207\344\375\225\0175\210\340\300\353\027!\000\202\014\267oe\304\035\332\360s\215\035\333;}/\324]E\201\312;\364`\305\302\016\023\004\025l\240\000#\031@\004N\020\277\215\211\002\200\261\260\202W\270\347\032\357\331\006NF\230\003\340\256A\264\303\360\000\005\022 \200I\022P\202\006f%|\374\340F\332\272BA\324X\3205p\212\202)\342\021\017S0\2550A@\001\004\330\367\220\002\370\260\000<t\010\011\377\211@9\254(\301\022\351\240G,R\305\225\026\202\346\205\250\201\023\033\242a\2779 F\005!|\010\002 \260\201\016\200\340\213\033\220@\002\020\350\220\006\250@~k@F\346\356\300B\375\351\006_\343\212D;\350\201\012\011\032\346\006\035 \343B\004\200\200\012\220@\0059\260\216\017x0\203\022p`}\015\021\200\004\246\247\025%x\342\035\363\370\304\015\263\342D\315@\0214E\362\202.R8\010\017\222\000\001\211|\000\011\030\331\225\033\224\240\002zLH\001*@\312\254\320\341\032\374@\206\031\232\350\306z\025\311\016\334\320\0070\272p\230\031H \210\000\010\200\004R\340\203"&k\006\0358@B\004\300\200\020\314\240+V\240E=\3161\210EUR2\227\324L\207\216H\217wx\302|\\\341A\010\224\311\020\011\310\300.6\340\200\017+p\002\035|\345\021\344\240\307(&\25177eS2\035\342\302-2\307\377\207^R\240!\016PA\017\356\202\314\021<\023,l\240\006?\214\001\033\255\\\2231\367dL\207\376\026K^\032\246\004\316[H\001H`B\272\010\301\006\356\014K\024z\261\217n\320a+\017\025LD\005\323!=\234C\211^\273\313\016@\220J\000H\300\006\0033\302(\000X>\207\326\022\216Y1\202#\362\201\216N\034\206\006\025`\310\000H\260\003\215)\302\033\372\360\004=S\272\227\225\356\305?V0\005?\272\321I\303\250\340\001\014I\300\0315\366\312}\324b\200X\241\252^\254:\230\240r!\027U<\314\0112\252\220\012\3404,\314Y\002b\310@Ed\224\301\247{{\232\253\264B\006c\360#\032\015-L\011@\271\220\016\264\222+F`C0\256\321\317\303@\301\260\327H\036%\177\312?\254\254A\032\013\305\225aH@N\205\200 \007_\211\202%\306\201-Z\330\2610J\360\205>\264\301\006\300\377\332S\260\307\322\312\032`\031\214\230\336\205\264\013\011\000\010B\252\025!D\026\031\205J\0207*[\030\037H\223\034V\334l`\3677\330\254\300!i\300\360m]\212\260X\2068\266+f\200\345\202\364a\012N\025F\010\264\240\3079\242\233V\316V\027+l\310ev\345\352\000\206\330\265+T@\005\202\026d\0145\\\206\025\3628\007\376\244{[\352\3466+l\250\337|\275\012\201\260\216u+B\240\303\254\026\324\015\346\332\245\007\322\\\257m\207\304V\270\370g\267\374\350\355al\260\201 \022\200\243]\011C0\026\324\244F\254\255S\265\250\007t7\014\244\016\277\305?j\350\353\026<H\323r\322\240\177\236`1?F\241\335\271(\001\030\263\255-\2019\214\333p\021\326\260\210E\314\011\352\273\020\004<X+FP\204\257\026\364\012\264\326\305\012\230\325l{\247\373\306\316^E\237q\035\261\006\3307\200\004t\340\377\240\\\031\004\215\026\224\013/\323\205\257\374 Fb\353\311d\003;9+Q\030\305V\017A\031\0228\357\000\022(\001q\271\002\010K\321\331\316sy%?hag\265\302\305\306h\361\217\022\032A\217\242R\306\006\032x\000\010f\320Q,\037b\313\012\242ta\032a.\251\322\230MM\206\330V\364@\216z\320\242\310vQA;\303\342\004M0iA\251\240\247Xf\030\217D\364\310\322o\301\364Y:\264\006\300!\003\0142\343\002-X\254\017M\2003+V@27\330;\346\002\227\371\275X\2216?e\306\006l\260\370\034\204\276\213\006C\274g>\3278\326''\333\212\022"\321\315Q\\\0331O\260\004\252\023d\015n\317\245x\347\260\207$Q\352\336\003k\305\016\331\340\0070\276\360\357{+\307\010h\000\034\213\317z\227h\312\330\017\326,\370\237\265\322\205\\\350\203\253`\341\221\033Nq\322\212\257b\337\277\241\202\204\303\211 ia\240\201\226d\266%d\033\221\304U\010\033\320\236\300\206;\204\321\356\304\220e\022\230c\2613\304L\027r\305\203\036\236\320\036\301c\016\324\255\254a\031\366+\271\374\330\300\014v$H\036\300\230eX|\340\004G\224C\310EUz]P\310\30162\335\314Yq\202''\216~\212\233\2576\271&zF\007\275R\004+\264"\350,\016F\317\301\242\204M\220\310\346f\367\266\314!;\007\205bC\352Y\341\204\220\355D\016V\234aQF\210\202#\264\201r\345\366\324.S\334\0076\370 0\230\377\013\276\351\362\016E\320T\2751\250/~\232\310\360\004\036\242#\007G\324"\034\362X\374\257>a\336\272\004\232t\253\260J\340\373\374m\203s\305\014jD\307\345\263\022\006\361.~\037\365 \035\351\352\201\017\331\007\216\0257\377\312\374\304\221\346\335\277\333\317\262\376\012\253\355\007\207h)g\014\327\200\273\363\307\337\254\350''\006\015\310\330\3079.q5do\006\336=\003K\023\322k\017\212oE\305\225''\277\202\204F\373\302p\341\024\372@\017\277\000i\257\346(\36076!w\006\317\220|\237 \005\\a\005\345\222\177\343\027\017\326\300\007F\200:\266\347\010\357P\017e\367;\032\227}`1\010\333@T\213\240{Au\004n\320\013\361\364k\316\227\017\353\260\015\245\2002\312\361\004~0\016\367 \016\216`>\356w\026\312F\025\371b\004\2350+\344\220\010\265\247\025N@\007\261p\015\344\340\016\364\300\202\366\020\017\377\3500\016\321\320\011\026\005[z\2204\350p\012\270\266t\237\207v^\341\004\251\260$\337\200\010[H\004\000\302\007\236\220\0130b\015\326`\014\264`\011lP\206a\241\004|`n\357\340Z\340\264\203T\321\203S\2012V\300\012\350 4\216`~\004c\\@\000\004>\360b\224\361\004\207\300\015\373\360\016gumzx\033\007\3307va\005\253p\016\373\320\016\237 \0062\203\025[P\011_\007\211U\3304g\007nsa\005\2510+\364\220\013s\260r\206\021Y\254`)\350\000\013\004\230?\247\350{Eg\011\346\226\017\327\340\010\024\222\034W\240\010\316P(\335 \012Q\260r\223\230\024|\310\033\261\310\007\310\320\204\347\240\013\202\360Z\245\021\005z@\013\344`\017\365 \015\212P\035\364\222\213\033W\030f\260\012\346R\017\332\020\013|`\215\224\201\215\253\300,\345@\013p0,\342\010\202\206a\005\212\020\014\377\226B\017\331\240\013\217`\006r(}a\240\010\267p\015b2\017\312\360\010\267H\027\313\210\024\315\210\0242\010Yf`\011\316\260\217\344\360\014\261\240\010i\220\214A\345}Y\241\004fp\010\253\200\014\343\340+\3630\015\236\260\006\260X\200\254R\211\2403\032K\000"\301\020t\3640\016\327`\014\256\220\011\2030\007\322\221\005]\320\005f\000\007\203`\011\253\020\014\3260\016b\0228\313\260\011l@\210:S\217\361F\032 Y\010\357\350h\372\020\017\347\340\015\332\200\015\327\320\225]\251\015\340p\016\357\260_\364\200\015\264\200\010j@8j\362\201QY\032F\300\005k0\010\246\200\014\336 \201\013r"\312\200\012\207\320\006\\\220&+i}\260\206}m)\033J \005b\260\006v\240\010\243@\013\277p\014\317\360\014\307\000\014\264`\012\216p\007VB\037\212\030\033\0159\034.9<F\243\004Q\300\005\242\371\223\377\242\311\005Q\240\226f\370\033\233I\023\017y\034\237\250\034\004\363\227\207\261\2323\321\2324\021\221\257i\037l\031\177\271\331\233Kv}\2757\216\276\231\233\264)\023\266\351\035\237\030\004\303\371\233\201\031\234\366X\030F\340\004S\300\005Z\020\005;\202eK@\005\\\200\005\034\331\221Ec\004\323\311\005T\320y\206Q\2341q\2342\201\233Fd\006\215@\013\301\020\014\261\320\010hpCO@\006\215\200\013\321\220\227nP{\037B\007f\000y\\@\007#)\015\274P\010\013\351\201P\311\233\260u\010\332\320\016\344\320\015\335p\016\207RMD\020\005}\020\015\361\360\016\350\360\016\370p\016\333r\025\305G\017\267\220\231fh\006\261\200\016\235\306\016\244\2430\260h\236+\201\236\375q\030t\220\016\346`\012s@\006!\251\013\310\240\010H\240\004\210\200\015\355`\015\253\320\011\251\320+\357P[Gs\016\347\260\011[a\006\377\372\330\015\271\320\011\236\240\217\365\000\013G\240\214\273\211\200\206\221\013\367\020\014\025\262\004v4EwH\006\236\022^\370\300\013f\350\007\372\000\016l\004h\253\220\016\025\370\004\312\251\004\2530Md\240\234v\341\242m\321\231\337s\030\233t\015\2150\010v\320\006d0/d\261S\301\360\237Yq\011w\342\003QP\012\366S\212,\207\015\345p\0101\265\034\033\232ny\232\245\226h\030\207`.\352\305\015\323\000\014\226\000\033a0\014\357`\011=B\007\323\324\003Xp\014\364`\014="\012\354\240wA\205\004\330\020\017\215\020\216]\210\212u\241\004\217\240\013\317p\015\332\020OY\210\005q`\015\3460|W\341\010\034H\004^0\016\357\220\012\025\322\003\261@\017\253\300\216Q@\016\357\200x\014\311\251/i$D\300\004j0\007\201\340\010\277\300\201s\320\006\324\300\016*\227\025P\200\014\371 \012F\360\006\352\205\010\377Z\341\003\327\252\013\276\223\025\210P\017\333\300\216\246\330\253\272\370o|\220\012\212J0\205P\226v\300\005\273\2003\011\313\005\244\360\016\3420\006\363\266Ug\240\025A\220\011\350\320\015\207`^FP\207\361\360M\274\312{\203g\027K\000\014\363\000\015\231\300\006]\020\006w\320\0138\243.\221`\016\350\020\014\221 \010\220\240\013J\012\011A01\374p\016\253\220\012D\233\012\221\020\006k\020\015\364p\015\2400\010\207P\244\365\240\013;V\262\300y\262ua\004\203p\015\371@\016\327\360\014\321p\015\350`\015\233c\004]\020\206''\302\015\361\204\015\221\360\004>\300\005\260\224\0174\024\267\310\260\006Kp\010\275\202\016\334\000\016\361\300\016\0003\233N\263\000\3169\230t\021\004K\360\006\240\200\014\330\020\016\335 \015\2520\007^C\266\216 "\330\240\014\246\240\007\325\341\003O\020\012\253\260\271\234\233\012\212\200+O\240\007\360\254 \015\330P\015\266\200\010S[\236\365\002\270!a\265q\022\005e0\007w`\231\364a\204d\020\007q\220\006\352\202\025E0\235Z\360\273\300K8<\002\227U\342\005\2629\256;\243\000,\020\270\370At\225q\274\260)?\226q\005;\203\001\235a\275nB\011\313\351\233\206@,/\000\000N\261\035\365\022\220\333[\032>\200\004N3\020\000`\000\304\262\012\345\2531>`\004f\341&L\321\021\002\261\274\330;$\254\000\216\357;\033E\260\030\304\262\274lA\020\200\253\000\365r\011g`\005M\000\005\014\334\300\016\374\300\020\034\301\022<\301\024\\\301\017\314\004L\020\005g\300\011\365\322\027\354\373\021!\341\027\3652\302$\354\032j\001\000\003\034\023\372\001\000L\201\022.\374\3020\034\303%\321\027\012a\2773\361\0024,\303:\274\303<\334\303\026\320\032\037\021\020\000;', '2012-06-28 12:51:12.364958', '2012-06-28 12:54:27.411566', true, 11, NULL, NULL, NULL);
INSERT INTO card_tbl VALUES (14, 'Delta', 'GIF89a\264\000s\000\367\000\000\000\000\000\006\006\017\032\032\032\021\023.\021\024/"#856BH\017\020H\020\020K !L+,OF\001OF\004OG\012QOBPKKQPJQQLPQQQQRRRQQRR5;\2215<\2218>\2229@\223:A\224<B\224>D\226@F\226AF\230BH\230DJ\231FL\232HN\233IO\234JP\235LQ\235NT\236QW\237OT\240PV\240RX\241TZ\241V\\\243W]\244X^\244Z`\245\\a\246^d\247^d\250af\251bh\251dj\252fl\254hm\255kp\256lq\256os\260ot\261qu\261sx\262ty\263uz\264w|\265y}\266{\200\267|\200\267~\202\270\177\204\271\341//\342/0\34200\34245\343:;\344?@\345AB\345DE\345GH\346IJ\346QR\347TU\350Z\\\350]^\351bb\351cd\351ff\353mm\353pp\354yy\357\213\177\366\332\000\367\333\004\367\334\005\367\333\010\367\334\011\367\334\015\370\333\000\370\334\014\367\335\020\367\336\025\367\336\030\370\335\025\370\336\032\370\337\035\370\340!\370\340&\370\340(\371\341-\370\3411\371\3436\371\3439\371\344>\371\344C\371\345F\371\345K\371\346N\371\347P\372\346T\372\347X\372\350[\372\350_\372\351b\372\352f\372\351i\372\353n\372\353r\373\353v\373\354z\373\354|\201\205\272\205\212\274\207\214\276\210\213\275\211\215\276\212\216\300\214\217\300\215\221\301\220\223\302\220\224\302\222\225\304\224\227\305\225\230\305\230\233\307\233\236\310\235\241\312\240\243\313\241\244\313\241\245\314\244\247\315\246\251\316\250\253\317\251\254\317\252\255\320\254\257\321\256\261\323\257\262\324\261\263\324\262\265\325\264\267\325\266\270\327\270\272\330\272\274\331\274\276\332\275\300\333\355\201\202\356\205\205\357\216\216\360\220\221\360\223\224\361\235\235\362\240\240\363\247\247\363\252\252\364\263\264\365\267\267\365\275\275\373\355\200\373\356\205\373\356\210\373\357\214\374\357\221\373\360\226\374\360\230\374\361\235\373\362\243\374\363\251\374\363\254\374\364\261\374\365\266\375\365\272\375\366\277\300\303\335\303\305\336\304\306\336\311\313\337\307\311\340\310\312\340\312\314\342\314\315\343\316\320\344\320\322\345\322\324\346\325\326\347\325\326\350\327\330\351\330\331\351\333\334\353\334\335\353\334\336\354\337\340\355\366\300\301\367\307\307\367\311\311\371\326\326\372\335\336\375\366\302\375\367\304\375\367\310\375\370\316\376\370\321\376\371\325\376\371\331\376\372\335\341\342\356\343\344\357\343\344\360\345\346\360\351\351\362\351\352\364\355\355\365\357\360\366\372\343\342\373\345\345\374\353\353\374\354\353\376\373\341\376\373\345\376\374\352\376\374\356\360\360\366\362\363\370\363\364\370\365\366\371\375\362\361\375\364\363\376\367\366\376\370\367\376\375\362\376\375\365\370\370\372\372\372\373\372\372\374\376\375\372\376\376\376\000\000\000!\371\004\001\000\000\377\000,\000\000\000\000\264\000s\000\000\010\377\000\377\375\003@\260\240\301\203\010\023*\\\310\260\241\303\207\020#J\234H\361\240@\201\0253j\334\310\261\243\307\215\027\007\002\020``\200\311\223(S\252\\\311\262\245\313\2270c\312\234I\263&\315\000\004C\022\254\260\257R\207\013@\203\012\035J\264\250\321\243H\223*]\312\264\251\323\247L\007\344\274\270\323\237?i8\240j\335\312\265\253\327\257Z\245\002\320\011\240\202U\177\3746\201\000\313\266\255\333\267o\305\2225{\326\3378\037p\363\352\335\3137\250\\\252e\353\236\0255\242\257\341\303\210\233\376\305\030X\260UsE0$\236L9\361b\221t\035[\025\226\242\262\347\317q\2472\316\254\331\037<C\222A\253^\375\364r\325\322u\221\261`M\273\266Q\327\215a\237\235\347h\203\355\337\265q\223\326m\225\031\014\340\310A\013''\356\230\037%\016\311\243[\026\215\231\271\346h7\244k\347\273\334zsM\037\266\213\377w\333\335\273\343q=\306\253\367Z\336\274\343P"\326\313oM\375\265{\315\345\206\244\236\317\377h\373\373\216\001cB\177\004\022\365\037\200\202\231\203Z\201\014\036\210\240`\306\254\220\230 \316Tha\205\315\000\023I\014D\255\320\314\205 Z\330L"@\335pa3*\024%C\210,b\030\204P.4\363a\205\314HH\337X\200\015\367\240c\3630\342\233a\2160\027\014\011\026\004\005\203w\225\000\025\204`/\024u\203{\207\010U\211c\221@\345\340\216\202%s\\_A2\347\314Z@\035i]\222\027\000\301\244\223P\006\305\3018\216\201\363\243b\365\345\206%l\372D\002\335^]2\367\211\221\202\3513\317\237\200\376Ye\231g\022\365\244yQ\002\305Ci9\3348\327\234\326=c\003\236\202\025\301C\020\236\360S\227>)^ \346Y\203\224 \352\250\242\206Gh]M\022\245\202&\254\262\312\346Y\372\264\377\312\352\244@\221RZ(\216\346\010\251u\374dr\347[yZEBP\213\010\266H\230\202\375\220\224\231\250.5J]\346\0305\302<g!\363\252?\363\200\031U\234:\356Z\3328<\300\025\254?\303\002\345\2019u\001\203l]\211\270\340\356\273.\330x\352Y\251&\365\354Y\321\026\225\010\273\230\3245\210SWz\253\231''!\2705n\271@\035S\0274\353\022\007\216\006@1K\257\263\320\032\325L]+\340PW2\000s+\260y\344\274\310\326\301B\331z\3268\020\177\252\333\303\021\027jo\305D\275P\2273\031|\200\256U\374\270\000''\216\243}\354\036)%\200ErP\242\3245\216o*\303\306\362\274V\325\213\324\275\217\025eI]\224\000U\364Y\222\354\374\250\317\346\2313\310~[\015\015\2240u\201#Y\322\2408\242\366\332\216\374\333r\263JA\355O\276B\255Y\027\255D\224\375kR\001s\377]\0321\235\206-\030\302\032\200S\2271\015[\245,R\0227M1\276D\365 \3308\340\200C\216`\215.\325\267\337\232\315\263\010\304Z\211}\350YU{\232\354\262.?\015s\311\336\211\262-\317\325q~\0372[>5\356\014&\300@\310\315g\325\220\270?\203\240 \374\360(\244\0204\323\376\\\302\366\332D\014%7\335@M\353]\266\232{,\373}\363@\362&S\343\362\243\251c\304\360Y\227\367\344\227\377\314\333\304!.\324\363C\355[W\240\177\012\3466\337\326_\177\2373\27675ni\344\234 >s\340@\237n\216\341\274\325\001\305\031\351\372\200\002\025\010\002i\324E\031\325\203\235}\354\347\036}XboH\331\237c\2341\233\3779L\200\260!\340\372\014\230\264\371\005ejg\311\231R6GA\330\204#sIYD<\344A\303\032\306\303\034\314\340\204\01720\024\027\304c\2065\014\242\015\377\231\001\224\036\010\361\210\362\210G0\206\362\011 \312#\034B\211\004<h\030\017xtf(7\010b<\034\261\302\372\265\020@\233(\030R4\340\2012\232\321\003\030\034\012\006\316\310F6\336i\215mdc\0070\270\201\016\230\361''j\372\300\035\215\342\306.JPN_\274\3178\026\307\240\303\2600\220\272\021\305\361\012\311\035/"\022@\346\020\004\330\030\031\232?v\353\221\356\021\306\200(\011\227CbR7\363@\004\3508\311\026O~r\200:#%XLyJ\330\360f\224\252\334\012+[\011\233f\314\000(E\212e\256zFKH\351c\022x\324e\307,\331KoI#;\302\324\232\256\212\351\255L\2101\231\364#&3\275%\016\274@\023)\263\234&q@\201\260k\016%\233\332\324\0159\2100Io\2023\234\272\021\2207\205rNt\302&\036\206\340\341:q\003\201v\330\363\236\370\314\247>\367\311\262\317~\372\363\237\000\325\3470RyM\3340\000\014`\020\203B\027\312\320\206:\364\241\020\215\250D''JQ\205\202\201\014\357\230G#\344\231L\334,`\013 \015\251HGJ\322\222\232\364\244(M\251JG\372\205w\024G\006\320\304\215\003\366\300\205\225\332\364\2468\315)I\275\340R\253\324)\215\224\\\216/\324\240\323\242\032\365\250 \345\251`\242\221?Uv\007\037~\370\002R\247J\325\223*\3251\226\320VP\275\230\2156T\365\253_\275j\233\302\305\311\377\344#\026R\005\253Z\213*V\315|\342\231\015r\244U\270\001\207\265\332\365\246m\325\0149\204P\316\371\377\\)\037\265\000\303]\007k\325\236\022\007hq\225fi\276!\007\302:V\244y\205\015<\276F\240\315\365\003\027ex\254c#\253\033bt\320\257r\325\014;\354\240\331\301rV7\362P\004G\325c\312]\240\241\264k=-q\224Q\273\361\260\262\035z\250)l\253*[\342d\017\250\311\311f/\322\000\3220\354\366\250\275\365\022\015l\033Z\346\340\243\017i=.[\015\273#K\230J:\355\304\206W\245\253\323\344z\007\034:\320N;\375\201\217@D\227\273+\365\256w\370\321\211\020\344\0228\343\265\3126\336\200^\233\252\327<\345\010B_W\023_\253\344\203\026\202\255/J\357\353\036E\302\267\271\010\362F\034\004\\X\277\235C\222\266\351o]\372q\01320x\247\324\365\2310\344\305_\004\357\350\033u\2700d3\354\263x$b\265\237\221\260ftq\006\021o\201\300;J\006AS\354\3419\265#\377\017]\2700\214w4\217G\000\327\2205\206T/^+\340\035c\311\2264V\254\354\336\301\207\034\243\327\310Xr\316\007\336\213\030\0253\007\033lx2\211)(\015\030V9N\011\240B\025\306L\3462\233\371\314hNs\025\250\240\005@8y\267P\206TZ\256k\030\334\034\340\010G@\202\236\367\314\347>\373\371\317\200>\202\025\374\261\0157\030\027\266q\336\0259~\260\337J\222\005\001x\3063\240''M\351?\013\332\277\263\020\003\242\267\214\310PtS/\270y@*\222 \351J\233\232\322\227>K7\352\252\331D\013\314\034\315k\344\037)\340\017kL\241\324\247\3165\237S}\226~\330\302\014\217u\365\307\204\021\270NZ\357\036\253P\002\256u}j^\327\345\033t\330,\247?y\032\024\227\322\213\327\260\002\263\231\355\354\011\347\002\330w\0256\327\216\321\002c+\331\037\375h\005\023"\275mT\017\0326\355\377\300\203n\325*n\256\361f{_qP:\260\200\204<\267;\320\357\326\015/\210\014\326z\373m\0310\275\366\271\353\362\212&\260\373\337\273\016\270n\230\374f\252\032\334o\374\210D0\273\322\267ud\241\337\020\217\270y|\261\206\260N{\232\330\311w\220\247\361\204\220\357\271\333\304y\307\037*n\324\213\313n\037\231\240\263\225\202\354\017z\230\202\324\376n7\314\231\243\215\355"\367\344\341\004\027WLY\215(\004}\333CgN>d!\206Cw\027\351\350\004E|v\276p\335\324\003\025@\207\272\304\357\303\015\372N\327\235\304\011\031\327\267\346\036kH\341\351\315\036;\200j1\206\253\243\2359\352T&/\357c\017U(\241\337p\0078\244\2741\207\234\332\034\221\347(\204\265o\303\363\322\\\203\012\017w\367\256.\013\356\364b\375\356V1F\261\375\323\370\322\364\203\025K\010\274\237\243\216\240v\334a\336\003\276<\345\346\261\265\010|\033\250\363\260A\307\025@>i\322?H\027\004o\360\352\275C[l\302^7\256`\202\344}\346\216\334\246~\367\346\231\207\235\030\337\365\007\251\343\343\221\327\263\355\261Dr\335#\337;\321\240\3257\177\317\034X8a\331\375\226\273\300\336\341\007\324\207\364\360\305\264\240\007\266\337|,\321\243\024Ixy\370\355\327U\014_\037@\342\330\001;\271\357\035j@!\317F\200g\342\3473gu^\350\247Mo\005\024\004\300\177\336A\017\247\000t\323''0\363u~\252w\177\216\241v\270!\000\022\260\201\034\330\201\036\370\201 \010\202\012p\000$\230\000!x\202(\230\202\023\020\001\015\260\000.\030\001)\030\20328\2034X\203\034X\0008\361G\037\261\203<\330\203>\370\203\005A\026@8\204DX\204Fh\020\002\021\020\000;', '2012-06-28 12:51:12.364958', '2012-06-28 12:54:46.539318', true, 12, NULL, NULL, NULL);
INSERT INTO card_tbl VALUES (1, 'American Express', '\377\330\377\340\000\020JFIF\000\001\001\001\000`\000`\000\000\377\341\000\026Exif\000\000II*\000\010\000\000\000\000\000\000\000\000\000\377\333\000C\000\010\006\006\007\006\005\010\007\007\007\011\011\010\012\014\024\015\014\013\013\014\031\022\023\017\024\035\032\037\036\035\032\034\034 $.'' ",#\034\034(7),01444\037''9=82<.342\377\333\000C\001\011\011\011\014\013\014\030\015\015\0302!\034!22222222222222222222222222222222222222222222222222\377\300\000\021\010\000s\000\264\003\001"\000\002\021\001\003\021\001\377\304\000\037\000\000\001\005\001\001\001\001\001\001\000\000\000\000\000\000\000\000\001\002\003\004\005\006\007\010\011\012\013\377\304\000\265\020\000\002\001\003\003\002\004\003\005\005\004\004\000\000\001}\001\002\003\000\004\021\005\022!1A\006\023Qa\007"q\0242\201\221\241\010#B\261\301\025R\321\360$3br\202\011\012\026\027\030\031\032%&''()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz\203\204\205\206\207\210\211\212\222\223\224\225\226\227\230\231\232\242\243\244\245\246\247\250\251\252\262\263\264\265\266\267\270\271\272\302\303\304\305\306\307\310\311\312\322\323\324\325\326\327\330\331\332\341\342\343\344\345\346\347\350\351\352\361\362\363\364\365\366\367\370\371\372\377\304\000\037\001\000\003\001\001\001\001\001\001\001\001\001\000\000\000\000\000\000\001\002\003\004\005\006\007\010\011\012\013\377\304\000\265\021\000\002\001\002\004\004\003\004\007\005\004\004\000\001\002w\000\001\002\003\021\004\005!1\006\022AQ\007aq\023"2\201\010\024B\221\241\261\301\011#3R\360\025br\321\012\026$4\341%\361\027\030\031\032&''()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz\202\203\204\205\206\207\210\211\212\222\223\224\225\226\227\230\231\232\242\243\244\245\246\247\250\251\252\262\263\264\265\266\267\270\271\272\302\303\304\305\306\307\310\311\312\322\323\324\325\326\327\330\331\332\342\343\344\345\346\347\350\351\352\362\363\364\365\366\367\370\371\372\377\332\000\014\003\001\000\002\021\003\021\000?\000\367\372(\242\2009\237\033\377\000\310&\017\372\356?\364\026\256%\015v\23698\322-\377\000\353\340\177\350-\\B5{x\037\340\243\344\363g\376\324\375\021iMJ\015VV\251\003WKG\024ebb\334Tli\245\251\214\324$9J\343\\\325y\017\025#5W\221\252\3229\346\312\362\032\253)\253\016y\252\262\232\332''$\331\003TMR5D\325dDa\250\232\2445\033\032\015\242D\325\033T\215Q5&m\021\206\2434\363L5\2235Df\232z\323\2154\3243DZ\265\377\000T\177\336\242\213_\365G\353Eb\3677[\037NQE\025\363\307\331\034\257\217\0164{s\377\000O\003\377\000Aj\340\321\353\271\370\200q\242\333\237\372x\037\372\013U\013?\011\351\361\351V\367:\235\353\333I*\202AuU\031\344\016G\\W\263\205\253\012t\023\227V\317\226\3140\365+\343$\241\321#\234\017O\337]JxOK\272\206C\247\352-4\213\351"\260\037\\\012\222\177\013\350\266\214\026\343R\222&# <\210\011\037\210\255>\267J\366\327\3561Yn"\327\322\335\356\216K}4\275t\327\276\031\262\376\312\236\357M\273{\206\210\023\303+\003\216\243\201\327\025\016\225\341\313K\235\0315\015B\351\355\325\316T\206\012\002\364\004\344w\252X\232|\274\337"\036\006\277?%\272_}-\352s.\365]\332\273x|-\240\335K\345A\252\274\262\021\235\251*\023\371b\241\223\303>\033\215\331$\326\212\272\234\0253\240 \376T\3262\225\355\257\334L\262\314CW\272\267\2528gj\252\3075\337\037\014xY\316?\267y=\274\370\377\000\302\237u\340\257\016\331\310#\272\325\344\201\310\310Y%E$z\362*\3266\222\323_\271\230<\247\021-S\215\275Q\347,j&\257B\377\000\204S\302D\343\376\022\017\374\230\217\374*x>\034\351So\225uIe\266#\344h\312\361\353\223\3104\336>\212\336\353\344\312\206Q\211n\321\263\371\243\314\232\243j\364\353_\001xn\374\270\264\325\346\234\240\313\010\245\215\261\365\300\252\355\360\363F\261E:\246\273\345\261\347\357$c\360\335\234\321\365\3727\266\267\364f\213)\304%}-\336\350\363F\250\315z\235\267\303\357\014\337\211\015\256\2634\342>\\\3054m\267\353\201\307CU\342\360?\204.&Xa\361\003I#\234*%\304D\261\364\003\025/\035K\317\3565Y]}6\373\321\346\006\2435\352\347\341\206\221ko<\372\216\245<Q#\234I\275\025Bg\345\311#\2578\250\255\376\037xR\376S\015\226\275$\323\355$,sF\344{\340\016\225\017\033I\367\373\215\026Y]h\355\177S\312\2154\327\253\267\303=\012\307O\206m_U\232\326B\002\273\031QS~9\000\221\355\305ex\243\300\272>\221\341f\325\364\353\373\213\237\231\002\022\352\310\300\234g\201R\261t\344\354\272\216Y}hE\311\333E}\316\036\327\375Q\377\000z\212K_\365g\375\352+G\271\204v>\235\242\212+\347\217\2621\265\3750\352\247O\204\214\302\227BI\177\335\012\334~''\003\361\256[\304\367\015\256x\252\327F\200\3468\230+\021\331\217,\177\001\375k\265\325/\343\322\364\313\213\3311\266$-\217S\330~''\025\346\032\026\235\342\015Fi\265m5\325egeiY\200$\236N2=\353\322\301\257u\316N\312:+\367g\203\232\265\316\251A6\346\323\225\267\345F\276\226\347\303^5\222\305\311\026\323\037,\022{\036P\376|~u/\217pu{\025=\032<\177\343\325\217\257\351>"\206\025\324uWY\004xA"\270%rx\350\007\177\347G\2105_\355_\354[\262\177xa\333 \364`\330?\343]p\247\315V\025SOF\235\273\330\363*Vp\241W\017(\270\352\232O{6mxb\341\364\177\021\335h\267''\344\225\212\256z\026\035\017\342\277\322\245\361\245\311\222k\035\006\315FX\251(\275\007e\037\314\376T\317\037X\311mqi\254\333e]X#\260\354G*\177\247\345P\370B)u\337\021]k\227j1\031\371G`\344`\001\364_\346+\025\312\322\305v_\216\307Ks\213yr\352\364\177\335z\262\267\204\341\026\3365\222\3306\357)eL\372\340\342\263V-2\343\305z\202j\323\2646\336l\247z\234\035\333\270\035\017\275i\370l\377\000\305\306\274\037\355\317\377\000\241V\\\007I\377\000\204\273Q\376\332?\350\276l\270\306\357\275\273\217\273\317\255t\335\373I=~\025\266\377\000#\213\225{\030GKs\313}\272o\344k\246\227\340s*\005\325&,Xm\033\217''?\356\324\037\020\225\037\304\366\011#m\215\240@\3078\300.\3315n3\360\375fFGm\341\201_\365\335s\305S\370\205\032\315\342\255:&''k\302\212q\327\005\330V4\233u\343~m\236\377\000\241\327Z\011a&\222\206\361\370_\346]\237\302\276\016\020\273\177l\205 \023\237\265#c\360\3075\237\360\344\334\375\263QT,l\304\007w\367w\347\217\307\031\255\231>\035hM#[\307yt\267\0337\200]I\003\246q\216\231\254\357\207\272\205\302K\250\350\3622\2641F\322/\030\303\003\203\371\347\275K\250\247B|\262r\333seE\323\305Sr\202\206\377\000\016\267\362f\177\200\257\277\263l\365\353\334\002`\266W\000\367#v?Zg\205\274.|c%\336\247\252\335\316TI\264\224#s\2662y=\000\310\342\233\340k6\324\264\357\020\331\247\337\232\325Us\375\357\233\037\255M\340o\025\331xv\013\275;U\022C\231\267\206\330N\326\300\005H\034\216\202\266\255\314\235GK\342\323\326\3261\303(8\321\215o\203\336\364\275\331\243\342\015>\303\300\276\032\275\217O\232sq\251\355\204y\214\011\0123\2220\007bG\342+\222\324|9q\242\370kG\327\243.\223\310\373\337\375\217\342\214\376B\257x\226\366o\035x\256+M#\367\260\307\036\330w\374\240\361\226c\236\236\237\205h^\370o\307\327\366-ess\014\266\314\0001\231T\0169\035\275\25387N1\347\222M\352\357\367\032\324\214kJ^\312\015\305i\033m}\333:\037\021\352\221\353?\013nu\010\361\211\241BG\367[z\202?\003\232\362\373(\357\264+m7\304\326\215\224\363\331\017\240e\376\023\354\300\237\326\264\264\255Y\342\360o\210t\033\202U\320\011cS\330\207P\353\374\217\347]\177\201\364\2305\277\206\323i\367#\344\232i\000lr\255\221\206\036\340\324i\207\203Ono\301\243G|eH\264\355._\3052\037\210\272\235\276\263\340\035;P\266l\3055\3120\365S\265\262\017\270<T\032\277\037\004\254\277\334\213\377\000C\256\023P\233P\322\255.\3745x\277,WB\\\023\367X\0022=\230\020\177\001]\336\261\377\000$F\313\375\330\277\364:\227O\331\250Em\314Tk:\262\251&\265\345\327\324\363K_\365g\353E%\257\372\243\365\242\273\036\347\231\035\217\247\250\242\212\371\343\354\214O\022\334\351\026\372j\177m+=\263\310\024(\014r\330$t\372W)\252\370\273K\264\320>\303\341\326\222\027\3360B\025\330\271\311 \236\375\277\032\320\370\233\306\201k\377\000_C\377\000Aj\362\300\325\354\340p\320\251MNM\357\267O\270\371\214\327\035R\215yS\202J\353{k\257\231\350\032\037\214,\246\321\347\261\361\024\222O\271\260\013)m\352{\034z\021S\015C\300JA\026\315\301\310\371$\343\365\257:\335K\272\272\336\012\027n-\253\366v<\305\231\325\345Q\234c+uj\354\365K\257\032xr\375e\264\273\337%\243\252\236bnNNG\257\030\006\231i\343\017\015ii\025\265\202\274v\345\313I\210\333\216:\363\311$\342\270\317\012\3501\370\216\376{i.\036\021\034[\367*\203\236@\307?Z\350\333\300:J\261V\327\202\2608 \354\004\037\316\271*Q\302\323~\316M\372\177H\364\350\342q\365\322\255\010\307\327K\372n]\203\304~\016\265\324\036\376\024\221.\234\261i<\267\311''\257\265T\233S\360\024\363I4\266\314\322H\305\235\274\2719''\222z\323?\341\001\321\377\000\350`_\315?\306\240\275\370y\037\366|\263\351z\217\333&\217\221\036\006\033\333 \365\244\276\255\177\216K\357\377\000"\245\365\356[:Pikk''\370\\\234_\374>\004\021h\331\007\217\222O\361\253w\336%\360^\243w\035\325\342I,\361\000\021\314N0\001\310\351\357X\2727\200Z\363M\373n\251t\326!\271Ee\031\003\325\263\323>\225l\370\003F\377\000\241\205\1774\377\000\032rXnmg&\327\257\371\0057\215p\272\245\004\236\273%\370\\\277\377\000\011\306\206<D\327\276|\276I\264\021g\3129\335\274\236\237J\216\313\304\276\010\323n%\270\264\216H\245\225J\273\010\234\344\023\223\326\251\377\000\302\274\322\034\355O\020\251c\323\356\037\353J\237\013\240\212\027{\315cb\253d:\306\002\355\355\234\236\015CXK[\231\257\277\374\215S\314\033\273\204_^\232~%\2753\305>\010\321\236G\323\322H\032@\003\225\211\316@\351\326\251\315\342\317\004\353M\347j\272[$\375\013\030\262O\374\011NO\343L\377\000\205u\242\037\371\231\007\346\237\343Q\267\303\013[\214}\203^IH#r\225\007\216\374\203\375(\377\000e\27774\257\337_\362\037\373o*\217$m\333O\363.\351\336&\360\036\215;\334\351\360\27432\024,"rH\353\216~\202\271\275\027\342\006\241\007\211~\325\251\336L\372|\214\373\341\352#\007\246\007\267\037\205m\317\360\303I\267}\223\370\201\242b3\265\302)\307\342j#\360\333C\377\000\241\231\1778\377\000\306\205,5\235\333w\357\250\245\014m\343d\243n\211\245\367\352Is\255|7\273\272\226\346{Wy\245b\3227\225 \334O^\206\254\177\302y\341}\027C\232\333@\017\034\213\227\212#\013\355,Or}j\244\237\011\255\345\201$\262\326\213\206<\273F\012\355\356F\017&\243?\014\264E\341\374L\273\207_\270?\255C\372\273\321\311\277\277\374\215W\327"\333P\212o\256\237\346Z\324<M\360\373Z\235/5+Y$\271(\025\211\211\362=\216\336\0163Y\376+\361g\206\357<\026\332.\216\322.\326O*3\023\000\0246O&\254\217\204\366\0276\3625\226\272f\221A\013\205R\273\261\300$\032\204|,\322\341\033.\374J\2110\373\312\025\027\037\201l\322O\016\232\367\236\235\002K\027$\323\204u\335\351\376g\235\332\377\000\252?Z+\322\342\370o\241\306\270\036%S\316z\307\3764V\357\025N\377\000\360\031\312\260\025\255\262\373\321\352tQEx\307\322\234G\305\003\217\017\332\377\000\327\320\377\000\320\032\274\2475\352\237\024\316<=i\377\000_c\377\000@j\362`\325\3649o\360\027\253>;:W\305?DM\272\227uC\272\227uw\236G)\336\37409\326\357?\353\333\377\000f\025\224\226Zv\241\343\035N\015J\360Z[\371\3237\231\270\017\233\177\003\232\322\370\\s\256^\377\000\327\267\376\314+2\030t\233\237\032\352q\3533\230m<\351\216\360\333~m\374s\371\327\236\335\253\324~Kc\331\214o\205\242\254\276''\276\3373n?\011xJiR(\374E\276G`\252\253,d\222z\016\225Y\227P\370w\257\206\033\247\323g8\364\016\277\321\307\371\353Z\026\326^\001\264\273\206\346-T\371\220\270\221s)# \344v\252> \326\256\274m\254G\242\350\353\2335}\306B8b:\271\364Q\333\326\262\214\247)r\312\356\026\327\231X\351\235:t\341\315\004\225K\256^W{\372\215\324\265\033\377\000\210\032\322i\332ph\264\350\216\342Xp\007\367\333\337\320Vv\255\341\3738<qk\242D]-\337\312\215\234r\307#\226\347\275^\323oo~\036x\201\354o\327\314\323\356\010&E_\274;:\375;\217\376\265W\361L1k_\021!\267\206\340\010\256\304!fO\230`\250\344z\325\301\270O\226\032C\225\331\376\276\246u"\247O\236\246\2659\222i\374\364^F\306\253\360\327L\261\322\256\256\223Q\235Z\030\331\301\227n\334\201\234\036\005c\350\232\235\325\337\303\337\020\332N\357$V\350\206"\307;C\036G\323\217\326\266\037\341t\013\376\273^\220/pb\003\371\265hj\272>\231\242\3749\325!\323\034J\214\231\222m\341\213\266\3409#\216=+\237\333\305\245\027.f\332\351kju\375Vq\223\232\207"QwW\275\3649O\012\370?H\327tsw{\250\275\264\276k&\305t\034\014s\315gx\203L\213\302\376 \265M\017R{\211\210\016\245\030\026F\316\002\235\275s\351Z>\025\360F\237\257\350-\251^_\313m\266VC\215\241@\030\347''\353PkzS|=\361&\237sg0\272V\033\300\2321\221\203\202?\300\214\032\335T\275W\036k\357\245\264\373\316WF\324#7\013-=\344\365\365\261c\342Di?\214\264\344\273a\022\311o\022\312\377\000\334\005\333''\360\251\217\203\374\015\377\000CZ\377\000\337\350\377\000\302\242\370\225\344K\343m<\\6\333w\202/1\263\214!v\311\374\252\347\3667\303O\372\013\277\375\377\000?\341X\2515J\026mi\321\035\016\012U\352]E\353\325\330o\215\344\0327\201\364m7H\274i\264\351Y\303N\254?x\007 \0228\306I\374\253;M\360\217\204n\364\373y\247\361BG;\306\032H\313"\354c\324`\363\301\342\273\273\237\370D!\360}\225\245\355\304Rh\357\362\333\274\205\211$g\220@\310#\236k\212\276\321\376\034\013Y\244\203Y\270\022\004%\025\034\2678\343\202\274\376u\235:\217\227\225]j\365\266\346\325\350\245>g\312\325\226\215\355\247Cn\367K\217\301\277\017\265Y\264=A\356\005\303!\363\324\251\332\011\012H+\355\\w\207<;\341\275[M\373F\253\342\025\263\273g`bfU\300\354r\335s[\037\014\256a\207I\327\177\265\012\215\035V?4J2\200\234\203\307\270\307\351S\\i\037\014f\221\2355y!\007\370c\225\260?54\324\234\034\240\357{\356\221.1\250\241Q$\225\266o\317r\335\237\303\237\015\313\006\373\177\020\231c''\357#\306E\025\3472\307i\035\345\322YH\322\332\254\314!\221\327\014\313\330\232*\375\235O\347\374\014~\261Ei\354\327\336}/E\024W\220}\031\302|U8\360\355\247\375}\217\375\001\253\3117W\254\374W?\361NZ\177\327\330\377\000\320\032\274\213u{\371s\375\302>K8_\355O\321\022n\245\335Qn\243uw\334\362\271N\317\341\376\265a\243j\327Sj\027\002\010\336\015\212\305I\311\334\0168\006\267n?\341\\\335]Kq5\333\031er\356A\230d\223\223\332\274\3034\233\275\353\226xe9\271\2514\337fw\322\306\312\2355I\3022K\272\271\351~G\303O\371\372o\373\352o\360\251\377\000\341&\360\257\2064\253\217\370Gq5\334\247\200U\372\366,\314:\017A^[\272\223uC\302''\244\246\332\354\331k0\224u\2058\305\367KS\324-\274[\341\337\022\350im\342\202\261\334\304\335B\267\314\177\274\245G\036\342\226\322_\207V7\260]\301z\302hX2\022f8#\330\212\362\322\324\322i<$uQ\223K\265\364)f3vs\204[]Z\327\363:\257\037j\326:\307\210\226\346\302q<\002\005M\301H\371\2019\352\007\250\256\207\303\032\367\206\223\301\003F\326/D{\331\374\310\302\276p[#\225\025\346d\323KUK\017\027MS\273\320\210c''\032\262\253eyo\330\365k\315w\302\026\036\021\276\322\264{\341\373\320Yc+!%\211\031\345\207\265Z\326\365\217\000x\206Xe\324u\0171\341R\250TJ\270\007\350=\253\307sM&\261\372\244o~gs\243\373FmY\3026\323Ki\247\314\365\355cQ\370w\256\335%\315\375\361\222T\214F\012\211W\345\004\236\303\336\263\376\315\360\263\376~\237\376\372\233\374+\3143I\232\026\027\225YM\375\340\361\334\316\356\234o\351\377\000\004\366\013\235G\341\325\336\217i\245M~M\245\241-\022\2010 \234\347\234d\365\254\306\207\341dcw\237$\230\376\020g\347\364\2570&\222\245am\264\237\3367\216r\325\323\217\334{%\277\211\374\005s\240M\244\223\366+\027m\246\017)\325\234\002\016\354\250=O\251\317\025\226m~\025\016~\324\347\376\005?\370W\227\346\222\222\303%\264\237\3367\217r\2674"\355\344z\345\266\265\360\336\312/&\336\300J\200\375\363l\316I\372\2774W\227\332\377\000\252?\357QR\360\321\276\357\357-c\245m#\037\270\372OP\325lt\250D\267\327Q@\204\341K\236X\372\001\324\237\2453N\326t\375Y\031\254.\342\237g\336\012~e\372\203\310\256WV2\276\257\256O\031s\177n\366\220\332\340\341\222''e\334T\340\343q.\013c\265Cnn\241\325le\234\267\366\222j\215i\206p\354\326\346 \314\013\0007\201\303\002G\035+\205QN7\276\277\360.z\357\023%;[O\3706\337\364\027\342\311\307\206\355?\353\354\177\350\017^?\232\365\357\213|xj\317\376\277\007\376\200\365\343\271\257S\000\377\000r\217\0136_\355/\321\022n\245\335Q\346\214\327m\3172\304\233\250\335Q\346\214\321p\260\374\322f\231\2323J\343\260\354\322\023M\315&i\\v\024\232Bi3HM+\225asM\315\024\224\256;\013M4\023IR0\240\322QJ\345\005\024Q@\027-\177\325\037\367\250\242\327\375Q\377\000z\212\207\271\242\330\372J\347I\262\272\276\212\366X\230\\\304\245\026X\345h\333i\376\022T\214\217c\305$ZE\224Z\201\276\021\273\335l\330$\222Wr\253\350\273\211\333\370b\212+\301\346\225\255s\353\371#{\330\343~.\177\310\263g\377\000_\203\377\000@z\361\272(\257g\003\374\024|\316k\376\362\375\020R\321Ev\036hQE\024\000\224QE\000%%\024R\030\224QE!\211Hh\242\220\304\2444QC\030QE\024\206\024QE\000\\\265\377\000T\177\336\242\212*\036\346\213c\377\331', '2008-02-22 18:41:52.024002', '2012-12-21 19:00:18.847331', true, 6, 15, 19, 4);
INSERT INTO card_tbl VALUES (2, 'Dankort', 'GIF89a\264\000s\000\367\000\000\000\000\000#\037 1-.>;;?;<MIJZWXhefvst\355\034$\356*2\3578?\360GM\362U[\363ci\364qv\220\217\217\236\235\235\254\253\253\272\271\271\365\200\204\366\215\221\367\233\237\370\252\255\371\270\272\310\307\307\372\306\310\374\324\326\343\343\343\375\343\344\361\361\361\376\361\361\377\377\377\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000!\371\004\001\000\000\377\000,\000\000\000\000\264\000s\000\000\010\377\000\377\011\034H\260\240\301\203\010\023*\\\310\260\241\303\207\020#J\234H\261\242\305\213\0303j\334\310\261\243\307\217 C\212\034I\262\244\311\223(S\252\\\311\262\245\313\2270c\312\024\030\001\201\001\001\001r\352\334\311\263\247\317\237@\203\012\035J\264\250\321\243\001\004\030@\020\001$\204\001H\243J\235J\265\252U\237\003 lDp\265\253\327\257`\255"\270\010\001\347N\245\020$dX\313\266\255\333\267p\343\312\235K\267\256\335\273x\363f\220\000\341&O\001Z''\026\340\211`\002\210\303\210\023+^\314\270\261\343\307\220#K\236L\271\262\343\011\\w\026\220h6''\002\016\226C\213\036M\272\264i\307\0342\347\024\000\2613\201\014\247c\313\236M\333t\006\002:Y7\354\214\300C\355\337\300\203\013\367\240Z\267B\336\302\223+_N\272\270B\003:\0210\237N\275:c\325\006\020F\320y\300\272\367\357\314\017\350\377lj\320,\001\337\340\323\253\237\355\001wR\203\252aO\356`\301\301\202\004\370\363\353\337\317\277\277\377\377\000\006(\340\200\004\026\330\037\003\015T\260\001e\031DWPt\363=`\340\204\024Vh\341\205\030\342\267\000\006\223\251F\020\004:\201\006\331\007\024dh\342\211(\246H`\003\035D\306\201N\201\375\003U\000\335A\266\301}*\346\250\343\216\030*\240Ad\342\0050\300@:\031\366\330\006\012\360\250\344\222L\002x\001d\023\350D\223N6&\331\344\225X.\271\340c\343\375\003b\000\006<\366\201\225Y\226i\346\211\012|\360\030t\001\214\365%\004\2179p\346\234tZ\350\300c_f\367\245\004\216iP\347\237\200\016\370cc\022\254\346eN\3621&g\240\2146\232\337\003\2165\230\323\241\001$\252X\007\216f\332\250\232\214I\032\000\245\226&v\201\246\244\376\311a\247R~\031*b\022\226\352\252\231\0254\377\346)\250\2155\360\352\255X\336\211\352\244\2526\206\353\257L6 k\252\210\372\012l\201\0124\240\354\262\3146\353\254\263\014\020\210\340\263\324V\373l\002\302\356\372i\257\214i\340\355\267\337\256\007\202\006\027P`k\201\331\306\346\247\200\351\322\366\201\204\355&6+\267\342\216\206\301\0038\002\030oi\030\010\310\000\247\356F\213\355\260\274\026[\257i\032\234\353\337\276\244U\020`\232\277} \360\300\332\322z\360i\032\344\273\037\303\2439\014\340\226\001\353\307\361\274\006_l\332\273\375q,\332\242\376=Y\233\304\033\023\274m\311&\233\326\252~\024\314\2460\177\220\276<q~#\023[i\315\262\335\214_\254\262\355\254\037\003\021\377\014\264\314\026\023}\232\321H\307\346\337\002\000\313\006s\312P\323+ui[''P\365i\375)\000\262\326N\213\3345\315_\223\266A~c\233\326\237\313\263\205\315u\305^\267MZ\211\011\014\377\2121\1779\207\254\357\332C\353}r\222~#\274\237\256\264\031\2750\341\253\032\036Z\211\211\227\266.~\377\326\346\370\343x\263-yh\230VN\332\250\370\231\255\371\212\220[\327\201\267-N\227yl\036\367]\033\337\003\006]p\341\311)\275\337\002\016\\\220umq\227\3461\335\262\221\216z\347\270\013G\240\002\026\004\327zl\022\366<\233\361\307/Fr\362\301\031\030x\333\0150^<\205\266\317\214=p\023\212n2\005\277\233\326/\370\251''w9\201\336\177\256~\205\341G\035\334\373\265\353\235\376\324\354#\0379m\370cW\333:\020\277\330\330-\177\376S\216\005&D\274\232\371\351T\264A\222\201\352\227\267\337\304n@Lk\233\237\024\360\274\351M\260}\302\271`\200^\367\265u\251\2544\233\373\017\005=W\033\226\005\250\001\373S\327\367\360\323<\237UO1\327\373\237\316\004\324;\341`\300|\242\211\035\007N\177#A\001&09\033\370\226\005*\300D&^@\0031\234\215\003\200\030\232\013f\2606\324S!\010\345g\231\320\311F\204\301\213\015\355\264xD.ZFBQ\264\214\010\023p\266\331\244-fe4\343|\360\263\303\375\\\2216\035 \323\335\254\377''4\035\312QQtLZ\177\266\007\300\301\305\361\217\217\271\\\035\371C\305\322,\220s|\274\235\037\021\211\030\201\235\3202\272\323P\032I\223B\212ER|\223\244$\355.Y\231L\342Gz\202\203\343''\355G\311\305P\217\224\224\011\020\004\361\250G\374\254p|\255<\014\376\012H\232\207mr4\001\274e(\315\230E\261\315\206\207\301\311\2420s\251\230G\356''\214\243\031P\003gc\264e2\023\004\037p\241~\240)\232\001\015\0218\012\263f.+PK\3754\32227\024\2348\377\370\201\012h\254?\347\254L\201j\030\261$\255S~\037\270\027\201\332H\232\000\002\250\203\021L\326\026\323\263:p\031T\211\024x#\200f\343O\025\036\364\241\020\215\250\022\357\211\230cY4G\024=L9/\312Q\012\241R^}\254UGGj!h\346\2601\235$\251J[6P\3048s\2450\375\017@\021sR\377\306`*\2468\345\317\035q\030R\221\346\364\247\011\230&M\205\306\247\3064\024\250\035\305\232c\012\365\2367=\306\224H\275\250P\021\223''J\205\3111y\214\252Jy\231\0306\215e;9\201\314Q\265z+\0226\246K\377(\022d\212I\326\262n2J\223\022\310\214j\224\310\215\266US0\004RN\206$\220/\005@Db\202\352]\001\305<\311\274(''1J\253g&\223\260\301:j\001\026\370\345a<D\220\370Pf\003\025h\300;\221e\255\316z\366\263\240\015\255hC[\201\013\314\3641\236\032KA\314\203\236k\272\2662\3551\224A\300J\243\327\332v2A\012\000y\014\302\2466\335\366\267\215\301\316q \004\334\342\202\3009\013\341Mk\215{M\342\344\306!\256\031&s\015w\233\347>\2443m\002\354t\315\230\232\263Hd0;)\314v\345\207\031\236lf"e\371\213\001\322\242\227\366\272`\367\275\360\215\357{\371\342\227\263$v"\252\011\213~\367\313_\260\2506#O\351\257\200\007L`\240d\005$51\200\202\027\314\340\006;\370\301\020\216\260\204''L\341\012[\370\302\030n0Sf\302\341\016{\370\303 \016\261\210GL\342\022\233\370\304(N\261\212W\314\342\026\273\370\3050\2161H\002\002\000;', '2008-02-22 18:41:52.024002', '2012-12-21 19:00:18.847331', true, 1, 16, 16, 3);
INSERT INTO card_tbl VALUES (3, 'Diners Club', '\377\330\377\340\000\020JFIF\000\001\001\001\000`\000`\000\000\377\341\000nExif\000\000II*\000\010\000\000\000\001\000i\207\004\000\001\000\000\000\032\000\000\000\000\000\000\000\001\000\206\222\002\000:\000\000\000,\000\000\000\000\000\000\000CREATOR: gd-jpeg v1.0 (using IJG JPEG v62), quality = 75\012\000\377\333\000C\000\010\006\006\007\006\005\010\007\007\007\011\011\010\012\014\024\015\014\013\013\014\031\022\023\017\024\035\032\037\036\035\032\034\034 $.'' ",#\034\034(7),01444\037''9=82<.342\377\333\000C\001\011\011\011\014\013\014\030\015\015\0302!\034!22222222222222222222222222222222222222222222222222\377\300\000\021\010\000s\000\264\003\001"\000\002\021\001\003\021\001\377\304\000\037\000\000\001\005\001\001\001\001\001\001\000\000\000\000\000\000\000\000\001\002\003\004\005\006\007\010\011\012\013\377\304\000\265\020\000\002\001\003\003\002\004\003\005\005\004\004\000\000\001}\001\002\003\000\004\021\005\022!1A\006\023Qa\007"q\0242\201\221\241\010#B\261\301\025R\321\360$3br\202\011\012\026\027\030\031\032%&''()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz\203\204\205\206\207\210\211\212\222\223\224\225\226\227\230\231\232\242\243\244\245\246\247\250\251\252\262\263\264\265\266\267\270\271\272\302\303\304\305\306\307\310\311\312\322\323\324\325\326\327\330\331\332\341\342\343\344\345\346\347\350\351\352\361\362\363\364\365\366\367\370\371\372\377\304\000\037\001\000\003\001\001\001\001\001\001\001\001\001\000\000\000\000\000\000\001\002\003\004\005\006\007\010\011\012\013\377\304\000\265\021\000\002\001\002\004\004\003\004\007\005\004\004\000\001\002w\000\001\002\003\021\004\005!1\006\022AQ\007aq\023"2\201\010\024B\221\241\261\301\011#3R\360\025br\321\012\026$4\341%\361\027\030\031\032&''()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz\202\203\204\205\206\207\210\211\212\222\223\224\225\226\227\230\231\232\242\243\244\245\246\247\250\251\252\262\263\264\265\266\267\270\271\272\302\303\304\305\306\307\310\311\312\322\323\324\325\326\327\330\331\332\342\343\344\345\346\347\350\351\352\362\363\364\365\366\367\370\371\372\377\332\000\014\003\001\000\002\021\003\021\000?\000\367\372(\244\240\005\242\212(\000\244\245\254}B\356i\357\027K\263}\262\262\207\236Q\377\000,c\311\037\367\323`\205\036\304\236\230"\325\201b]D\371\355ok\037\332''_\276\001\302G\376\363v=8\034\367\306\015?\354\263L\243\355\027R\037U\207\367k\377\000\305~\265-\255\234\026v\353\005\274a#\\\340g''''\251$\362I\356O&\253\352Z\306\237\243\331\265\326\241{\005\254\000\343|\256\000''\320z\237n\264\326\256\321\023\260\255\243\331\277\336\023\237\255\314\237\374UP\273\360\246\233u\033.oab0\032\013\331\220\217\311\361[Q\310$\215dS\225p\012\237Pj\255\336\247ea,\021^]\303\003\3341H\226W\013\274\216p3\324\323\213\235\375\320\262<\227\305\237\014\374]n\222]\370o\305Z\265\322\216~\311qx\342O\370\013\003\203\364 }k\306\356|O\342\373;\211-\256u\315b\031\243m\256\217u e>\204f\276\310\030<\340W\235\374O\370qo\342\2751\357l!X\365\213u%\030q\347\200>\341\365''\261\365\257S\003\217\212\222\205h\246\273\231\316\035Q\363\307\374&\236)\333\267\376\022-[\037\365\371''\370\324\251\343\257\026\241W_\022\352\231\036\267n\177Bk\002H\332)\0327R\256\247\005OPs\323\353L\257\244Xz-]E}\306Wg\243\350\337\032\274a\246\312\242\346\356-F\021\325.b\031\374\031@9\372\223^\323\340\317\212\272''\213\312\332\344\330\352''\000[L\303\347?\3547C\364\353\355_''\363\351RE+\304\352\350\305]NA\007\220k\217\023\225\321\253\026\342\254\306\246\321\367.I\247\327\223|"\370\216|E\001\3215i\263\251\302\231\212V\353:\016\243\352?Q\370\327\254W\313V\243*3p\221\321\026\232\270\264RQY\014Z))h\000\242\212(\000\242\212(\000\242\212L\320\004s\314\226\360\3114\214\026(\324\263\023\330\016I\254\217\015F\357\247\177hN\270\270\277o\264\277\260a\362/\340\233G\3275\037\215$)\341\015D+`\313\030\207?\357\260O\353[\261\306\261\242\242\200\025@\000\016\302\253\354\213\251\215\342\257\022Z\370O\303\367:\265\347)\020\302F\016\014\216xU\036\371\3753\351_%\370\217\305:\247\212\365\206\3245;\203#\026"8\301\371"RxU\025\352\277\2646\247/\235\243iJH\207k\334\270\354[\356\257\345\206\374\353\303\324\235\300\367\257\243\312p\261\215/j\325\3331\234\233v>\337\323G\374J\3553\377\000<S\377\000A\025\344\037\264G\374\201\364A\377\000M\344?\370\350\257`\323\177\344\031i\353\344\247\376\202+\307?h\247\333\246hI\334\315)\374\202\377\000\215y9z\276.7\356\315%\360\231\337\007\376&\\\013\350|5\255\334\031a\230\354\263\236C\226F\355\033\036\340\366\367\372\214{\351Q\212\370b)\244\202d\226''d\2226\014\214\247\225#\241\025\366\266\203\250\035[\303\332n\242\303\006\352\326)\310\364\334\240\377\000Z\350\315\360\261\24558\351qSwVg\316\177\033|4\232''\214\376\335\002l\267\324\220\314\000\350$\007\016\007\350\337\360*\363\032\372+\366\205\265G\360\246\227w\264n\212\373\312\007\32021?\372\000\257\235s^\326YU\324\303&\367Z\031\315Y\205\024Q]\344\027\364\235R\347F\325m\265\013I\012\\[\310\262!\367\035\253\354\335\027T\207Z\321l\265;\177\365W0\254\240zdg\037\207J\370\220\034W\324\277\004o\032\357\341\265\264n\333\276\315q,#\236\203;\277\366j\360\263\312)\301T[\232S\336\307\243R\322R\327\316\033\205\024Q@\005\024Q@\005\024Q@\005%-\030\240\016o\307H\355\340\255M\243\316\350c\023\361\350\214\037\377\000e\255\370eI\242I\020\356GP\312}A\351E\304\021\\[I\004\250\0369\024\243\251\356\017\004W=\340\373\207\213M}\026\341\211\273\322\\Z\276z\274`f7\3722m\374A\364\252\336\036\204\365<\257\366\207\322\345\363t}]A1a\355\234\366\007\357/\347\363~U\341\243\250\257\264\274O\341\333?\024\370~\353I\275_\335\314\277+\216\261\270\345X{\203\376y\257\223<U\340\375W\302\032\263X\352P\220\204\376\346u\031I\207\252\237\350zw\257\242\312qp\366^\312OTg8\273\334\373\022\300cO\267\003\264K\374\253\305\177h\337\370\366\360\367\373\367\037\312:\366\253.,`\037\364\315G\351^)\373E\035\320x{\270\337q\223\350q\037\377\000^\274\254\275\245\214\217\317\362._\011\341QD\363L\221F\233\344v\012\252:\226''\201\365=+\355m\007Om+\303\332n\234NM\255\254P\223\352U@\376\225\342\177\010~\030\\\233\370<K\256\300\320\303\021\017gl\343\014\357\331\330v\003\267\257^\235}\3638\357]\031\276*5f\241\007{\012\232\261\343\337\2645\342''\205\364\253"\337<\327\246P=B#\003\372\270\257\235A\305zO\306\237\024G\257\370\320\332[I\272\327LS\000 \344\0313\363\221\372\017\370\015y\265{\031e''O\014\223\352g=\305\244\242\227\025\350\022\003\255}E\360:\315\255\276\033\305#\003\213\233\251e_\246B\177\354\225\363-\215\224\372\205\365\275\225\254fK\211\344X\243E\352Y\216\000\374\315}\241\341\335\036=\003\303\266\032TD\025\265\205c$\177\023\001\363\037\304\344\327\211\235UJ\234i\365\271\2455\255\315JZ)+\346\215\205\242\222\226\230\005\024Q@\005\024Q@\005\024Q@\006+\234\327,.\240\277\207]\322\223\314\274\205|\271\255\263\217\265C\335s\375\345<\251\365\310\350\325\321\322\025\007\250\246\235\235\304Q\3235K]^\302;\3139<\310\337#\030\303)\035U\201\344\021\334\032\222\377\000N\262\325-Z\326\376\322\033\250\033\254s u?\201\357Y\327\332\020k\346\324t\353\206\260\277`<\307U\335\024\370\351\346''\001\261\3206C\001\336\221umN\327\344\324\264\231\010\037\362\361b|\344?U\341\307\320\003\365\247n\261\003h(U\001F\000\340\012\253w\245i\367\327\026\367\027vP\\MlK@\362\240c\0218\311\\\364<\016ER\377\000\204\237K\037\353&\226\036\274Om$g\362e\007\364\254\375C\307\3325\214l\311\036\243v\352?\325\332\351\3639?\216\334~\264F3\276\203\271\323\340z~5\346\037\025~&\301\341\213\0314\215.U\223Y\2350J\234\213e?\304\337\355\036\303\361<py_\025\374X\361^\246\222Zx{\303\372\206\237\021\371L\362\3333\314G\323\030_\326\274\206m\037\\\226g\222m;Qy]\2673<\016K\037Rq\315z\370,\2719)\326i/S9O\2423K\263\261f%\230\222I<\222i\265\240\332&\252\204n\323/W>\2600\317\351B\350Z\263\375\315*\371\276\226\356\177\245}\012\251\013n\214\254\314\372v\017\327=+\260\321\276\026x\307Zu\021h\263\333Fz\313x<\225\037\367\327''\360\006\275\237\301\037\0064\317\016M\035\376\2572\352Z\202|\312\012\342\030\217\260?x\373\237\312\271q\031\215\032+{\277!\2506c\374\032\370o-\206\337\023\3530\024\271u\377\000C\201\306\014j\177\215\207\251\354;\016{\214{f\005&\321\351N\257\225\304W\235y\272\2237I$\024QEb0\242\222\226\200\012(\242\200\012(\242\200\012\342\374I\343\211\374?\342]3E\032K]K\2511[v\212p9\316>`W\216\271\357]\246Ey=\261\377\000\204\227\366\200\270\230|\326\332\005\237\226\247\250\363\030`\217\256]\277\357\232\004\316\273T\361\201\360\361\212MwL\236\326\311\330/\333aq4Q\223\300\3631\206_\256\010\256\214\316\036\333\316\207\367\312Szl<8\307\030=9\254O\034\305o7\200\365\344\271\000\304,''|\267b\020\220G\270 W\235\370s\305\032\246\207\360\002\317V\267x\232\362\0260\333\254\361\227\363\177|UT`\216\331\037\2051\\\365\035\003Q\272\325th//t\331t\353\2112Z\326f\313G\311\003?\\V\247\025\301\370\263\304z\277\207\342\360\305\252]Y\245\326\241p\220\336<\261p\024.e\221Np\000\367\007\255C\342\177\022x\217O\273\320&\262\026\326\366\232\206\247\015\240\265\236"g\225\030\234\2719\302\016>\356\011\344\022G"\225\207s\320\262(\300\366\256sQ\361\004\203_\213\303\332R\305.\244\361\371\363<\2311\332C\331\230\014\026$\360\250\010\317R@\254\204\361\026\245o\343MKGK\330\356\254l\364\303sqs,#\026\223n\341\016\315\271\005y\333\327\336\200\271\334\234t\300\243\203^k\240\353\3365\361''\203\264\253\373\030\264\341yq:\275\324\222\002\251\034%\216\025\027\234\235\230c\223\374X\034\364\321\322|Q\252x\227K\327\346\322\336\010\265-:\362[h\254\245M\300\354\3067\362\016[\007\004\020\006z\034\034\226\013\235\327\025\215\257x\223L\360\345\274R_\314C\314\336\\\020D\273\345\235\377\000\272\2129''\245jK2\333\333\264\3238\011\032\226v\364\307Z\362o\205\342_\033\370\217V\361\356\250\031\300\230\332i\220\277"\010\300\311 z\340\201\237R\336\264\001\350\221\337k\227\021\254\321\350\326\360\251\345R\352\364\254\200\177\264\0227\000\375\011\254\225\361\233\334i\236"\271[\011\354\037DV\023\033\305\005Z@\245\310]\255\312\343n\016G\336\034Wc\221\353^y\361AG\374#\261\350\226`%\317\210\365\030m\035\227\256\0167\271\377\000\200 \037\215\000t\336\021\324\265\015g\302Zn\247\252E\0247wp\211\2328A\012\252\334\257RO\335\307z\335\310\365\256\012\367^\326t\377\000\210\036\037\360\325\235\265\224ze\314r\227S\270\312\221D\2747\007\012\011\300\003\234\343\236\270\032\032\357\211n\255<e\341\357\017X,-5\363\3115\321\221I\362\355\321s\221\2020X\360\011\310\342\213\000\377\000\021x\222\377\000I\361\027\207\364\233;kyN\253p\321\261\221\233r"\015\316\300\016\270\036\365\325d\036\206\274\272[\253\315g\343]\342\330@\222\235\027M\020,\263\177\252\206iH%\2169''o\033G\\\036GZ\332\360\367\2115\206\361\306\243\341]ql\244\236\013U\275\267\271\263\215\221^2\333Hefl6Oc@\\\355\251i)h\030QE\024\000QE!\240\012\327\327\260i\326\023\336\\\310\022\010\020\310\354\307\030\000d\327\227\374\026\226\031\364\255w\304wS\304\267:\226\240\357!g\034(\344g\323\226o\310WS\343\337\025\017\011i\326\367\023\351\021\3526\367\023\210\004~h\015\274\202G\312T\2028\353\232\335\263\323\255Z\335\036\343I\263\202r2\321\242\253\355''\266v\214\323\026\354\341|iy{\343\270O\205\2741\373\313Y\244_\355\035QA\362b@\331\330\255\321\330\361\320\373w8\217\304\266Z~\237\251\370\027\301\310\351\015\205\274\346\355\374\303\200\313\002\222\271\365,\304\234z\327\247(U\\.\000\035\207AUg\323\354.o-\356.m-\246\272\203&\031$\215Y\343\317R\244\362:\216\224\202\307\236^[E\342\357\215\026\2514fK/\016\331\254\315\033/K\211pW#\327\030?U\253\232\355\345\275\347\306-\002\302yQSN\262\226\355P\236d\226F\021\205\003\271\030\317\266\015w\311\0041\315$\261\305\032\311&7\270P\031\261\323''\276\005E\366\033?\267\375\273\354\320\375\260''\226''\362\307\231\260\022v\356\306q\311\343=\350\013\036K\340\341ew\361\023\306\351\254jW\026\272\201\275\302\302.\214\036d\000\260\\`\203\214c\241\356\015t\2763\026\032_\303=F\333A\206\004\376\320"\316\037$`M$\314\020\234\377\000\021 \237\233\222q\326\272\253\357\016\350\232\225\342]\337\350\372}\325\312\017\226Y\255\321\330~$g\322\257\313o\004\301\026Xcq\033\007@\300\035\254:\021\364\240,b\331G\246x\037\302\372u\213\310#\266\200\305j\256N2\314\300n9\365$\261\372\232\345\365])\364?\214\032\036\247\2441Q\255,\260\352V\352~W\021\250"\\v#\214\237Q\376\321\317\241\\Z\301u\037\225q\014r\241\376\031\0240\375j+m2\312\316f\226\332\316\010de\012^8\300;GA\221\330q\305\001a\367\366b\377\000M\272\263v*\267\020\264E\207`\300\214\376\265\345\177\006\357\323\303\326\367\376\010\3266\332k\026\227n\361\307!\307\237\033c\346B~\367 \364\354G\275z\366G\255`x\233L\321\2574\211\2575M\026\035QmbiR?!e\220\2003\204\317 \237j\000\320\274\324`\262\330\256\301\246\227\375T+\313\310}\207\327\277@:\342\270}N\352\035K\343.\235\014\362(\267\320\254\032\340\251\3477\023\260EP;\235\274\212\352|3c\244\305\244\301{\247h\311\246}\2565\225\3430\210\345\031\031\303\343\234\217\255Z\376\302\322\216\264u\237\354\373o\355-\233\015\321\214y\233}7u\034`}8\240\016\023J\325m.\3761\370\232\376\342t\3634\353ht\333Hw\015\362n\371\337j\365''w\034v\250\274;-\273|[\361N\257\250\335\251\232\322\010,\343B\371\021\345\014\216\2521\300P\274\237f=\015z\034Z6\233\016\2516\245\026\237j\232\204\300\011.V%\0228\3069lg\377\000\324)\260\350\272e\266\245>\243oam\035\355\307\023\\$@;\217Fld\320\007%\360\266<\370j\373\304\267D$\332\335\344\327\3223\234l\217%P\023\350\000\317\343W\2743\247\275\367\2125\177\026\315\031D\273D\264\261R0M\272u\177`\355\222\007\240\036\265\273\027\207\364\250p"\261\211\020\022DJ1\020''\277\227\367s\357\214\326\213\356\012\3011\273\007\000\364\240\011(\256C\301\336*\274\361\035\215\355\355\315\2541\301\035\364\226\326\315\011$H\210p_''\266A\364\351]u\003\026\212(\240\002\222\226\223 \002I\300\240\017-\361\271:\367\305\217\011xx|\320\332\026\324''\035F\001\310\007\376\370\307\374\012\266\246\361m\364\037\021.40\266\363X\333\331\013\202\261D|\366\225\216\0261\226\307#\346\316\007\000\223\212\301\360\024\320\370\213\342\227\213\274B\262\243\254\005l\255\310 \345\001\301#\330\371c\237sV\276\035\330\307\254x\217\304^4\230ok\273\266\266\262c\3740\247\312\030}p\007\374\004\372\323#\251\255\247\370\203_\177\210\362\350\027\261\330\375\223\373;\355\230\2001hN\375\241Y\211\371\211\366\002\251\370J][W\361\207\210uK\235I$\262\262\272:|\011\344\340\000\230g\013\317\034\220\011\347\245A\341\335Z\327\376\022\017\035x\216iQ\232\011Z\335c\007,\261[G\363\037`\307''>\265\223g$\326\037\001/\345\263\224I\251\\\331\313y;Fr@\225\211$\221\320\354?_\226\200\271\332\331\353\363\370\206\033\253\3156\342;M\036\006d\027\256\201\214\345~\363\256~P\203\035Ns\333\030\347\226O\037\353\317\341-\007PX\254V\347S\324\276\306\032X\234\007\213-\373\3607|\277*\347\234\365\374*\367\202\364\315\006\353\341\366\211\347\337\311}e\366t\037g\226a\345\371\204|\310Q@\017\363da\203\032\223^\323c\327\376''x\177Ld\006\313F\265}BH\300\371K\026\013\022\376\005r=\201\240z\232Rj\336&\212\353V\275\272\263\262\264\321-\354\214\326\262H\305\244f\000\222\\\0026\200\240\361\324dry\252Z7\2135\315o\301pj\020\332X\246\245-\273\\\310\3223\255\265\272\022Jn9$\222\240\034\016\334\234q\227|Y\275\232\017\002\315\247\332\377\000\307\346\255<z|\013\236\255#s\377\000\216\202?\032\253\342\333X<\023\360z\347K\260\300\177\263\245\214m\234\027y0\205\263\353\202M \324\324\360\317\214\032\373\341\315\247\2125\270\322\331\344\215\236D\205N\017\316Qv\202I\313``g\222i<Q\342\015c\303\276\014\274\361\003Aj%\2057\375\215\3018\334B\200\\\036X\026\004\340`\362?\332\254\015I\254\254\374C\360\377\000\302\336t\177\331\320\011&,N\022Y \217\021\340\367;\262\336\347\025\037\217\265\230\374C\257\370g\303\026O\034\266\027z\217\231w8`Q\204\030v\214\036\375F}\016\007\2550-\370\336\377\000\304Si\236\032\321\355n\355\354\357\265\271R\013\305H\011*\2737LT\226\341@\343\035}\030\032\326\361n\273\255xv\323E\212\310\330\\_\352\027\321\331mx\035T\356\311,\000|\200\240w&\263\036\376\327Y\370\303\034\246U6\232\036\230\031I\317\372\371\333\003\003\276S\030\365\310\25159\343\325>1\351\226\262:\213m\013O\222\355\363\377\000=\245!\024}B\362>\264\206t\232\237\210\022\337\\\266\320\254c\027\032\254\351\3472\222BA\0208\363\034\366\031\340\001\367\217\034\014\221GQ\361\025\346\213\342\275\027J\276\373<\326\372\277\233\024R\306\214\215\024\250\003s\2269S\234\014c\036\365\207\360\375d\270\370\201\343\313\353\321\376\230\267\261\333 <\355\201T\224\307\240a\203\365\250\274Ot/\274g\375\271\215\332G\204m.''\226O\341\226\351\223\375Z\372\225\000d\366''\024\001\243\240\370\333P\3255\035r\325\354\240\225\254\357\232\312\315-\230\346vO\365\214\344\375\325\\\256N8\335\201\222@3\370;\305\032\246\263}\342+mj\336\302\337\373"\350@&\265v1\260\301$\022\335\327\003''\201\317J\312\370Q\243/\207~\037\377\000nj\014M\336\240\257\250\334\312\334\220\207\346\037A\217\230\375O\245r\213xm\276\022[\231\347\020\\\370\253XG\275\224\034yq\334H\304\222}\322<{f\200=B\015j\367Q\322''\326,\222\010lB4\266\337hC\272\341\000''y\301\001\025\260q\3018 \237J\303>7\273\223\340\365\347\213n\355\343\264\236Ky^\336$$\355\313\024\213$\365$\355?\215A\361W\304+\244\3702\347G\321\302=\355\312\245\240H\210\002\3367;F}28\003\251\344\216\024\325_\025[\332\030\374\027\340\213iQ\255\276\326\246\344\377\000\017\223h\233\234\037\251\306}\301\364\240\016\307\301:(\320<\027\244i\216\200I\015\262\231s\377\000=\030ns\377\000}\026\256\212\243\215\326H\325\324\345Xd\037j\222\201\205\024Q@\005U\270\206+\230\274\231\342\216X\244_\235$@\312\337Ph\242\200+[\350\0325\214\236u\246\223co/M\361[\242\234s\334\017sV\255m-\354\343\373=\254\021A\002}\330\342@\2123\311\340QE\002ex\264\2156\334]\305\015\205\254q\334\345\347U\211@\225\2339-\307\315\237z}\236\233c\247\332}\212\312\316\336\336\325x\020\305\030U\347\257\003\326\212)\210\253\247xkB\322n\336\343N\321\354m''s\265\244\202\005F#\2562\007L\325\337\262\333\305pn\343\267\211nf\012\222J\020nu\031\300''\270\344\321E"\204\270\323\354\357\236\011n\255 \236Kw\363!i#\014ca\320\251=\015E\250iv\032\315\231\264\324\254\340\272\267\334\255\345J\201\227 \360qE\024\300\203R\360\316\207\255E\014\032\226\225iu\025\266\014+,@\371\177OJI\2749\242\336\0331q\245Y\310\266\014V\325Z\021\266\021\201\367GA\320QE $]\013I\377\000\204\205\365\177\354\353o\355\037-S\355^X\3631\214c=z\014}8\246&\205\245>\256u\246\323\355\316\246#\011\366\242\203~\006@\347\351\307\322\212(\001/|;\245_]5\364\366\203\355F=\2554n\321\263\201\3201R7\017c\221V.4\2156M%\264\306\261\2676\014\273M\267\226<\262=6\364\305\024S\021e\354\355\336\311\254Z\0246\246?(\305\217\227f1\217\246+&\177\011x|\370\177\373\014\351\026\277\331\233\301\3738L.Kr}s\317^\264Q@\021\237\007xq\364\250\264c\243Z\01587\235\344,{Wz\221\206\343\222}\373\367\253\027\276\031\321/.t\373\211\364\273g\222\304\225\265%\006"\007\250\003\247a\364\242\212@l/\177n)\324Q@\302\212(\240\017\377\331', '2008-02-22 18:41:52.024002', '2012-12-21 19:00:18.847331', true, 5, 14, 19, 3);
INSERT INTO card_tbl VALUES (4, 'EuroCard', 'GIF89a\264\000s\000\304\037\000\340\234\013\212\221\257\323(\025\354\352\357\332Q\010\367\341\263(,\\\242\006!T]\212\350\252\256\250y\034\361\315\177{^/D\026H\306\312\330\344\200\210\254\261\305UGB\333LW\371\351\332\362\304\307\217blt\0175\324\202\011\355\270E\2232P\263\224\224\377\377\377\314\000\020\350\236\000\021\037^\000\000\000!\371\004\001\000\000\037\000,\000\000\000\000\264\000s\000\000\005\377\340''\216di\236h\252\256l\353\276p,\253^m\337x\256\357|\357\377\300\240pH,\352D\306\244r\311l:\233\310\247tJ\255Zs\321\253v\313\355b?\336\260x\014\005\223\317\350\364\321\254n\273\303\331v\243a\251\333\357\363\367-\022a\370\377\200|zqct\007\007\034\211\212\213\214\211\007\026\015d\006}\012\000\035\227\230\231\232\035\027\012\014\021i\204]\026\210\215\246\247\213\217\221Z\006\014\027\233\260\261\232\236\006c\242W\015\245\250\273\274\217\265R\255\257\262\303\304\234\021\277\\\267St\274\315\316\216\253K\021\012\305\325\305\000\012\310W\312N\006\026\317\340\316\007\321D\323\326\347\325\331Z\334K\314\341\357\315\343C\006\324\350\366\305\352U\354I\337\360\376\315\026\200\320\273G\360\032\003}l\234\344\372\307\260\031\271\034\021,\025\2348\354\202\2662R\3725\334\210\352\200\216\201\024C\016;\370d\237\020]\034S\3776\222g\303\2000\2210a\345cb\362G\003\2258Q\255\212\020\263g,\000\027\215\324\354q3\247\321F\026\030\370\\\012\013\324\222\241;\212\036\235\232\210\000\323\253\232\234&\201\232C*\325\243V\261\212\275\244\265\010\327\033^\277\346\014;vl\331!gk\244U\253\222m[\267B\023\006\231K\227\243\200\273\200;\004\375\021\267/X\002\210\023''\016\334\023(\221\263(\015\323\375\313\030\346\205\307z}h\224l\330n\345\211\012\340f\346\301\227\263\332\227\237''\276\355\001\325\264k\312\251'':\0062t\263\353\177\212s\353F\214:\366\275\320\264Gw\275\275\321\263\357\266\253\327\000\211L\374\335\361\312\227\011\013G\333\234\241\361\347x}\230\254\316\020;t\351>Js\347u\335\273\330\3447\3661\037?\331<\272\350<\330\211g\377\265\267\373a\350k\260\263M\277o\371\373\233\000\267\003;\375\335\006\2405\203\3517\335\377|\005."\300\203\020F(\341\204\023\332w\340&\371q\263^\203\215\\\310\230\200_\360\300a3\260yxW\202\31208\342\177La\340"\200\350)\303\237\000\017\324h\343\003\0228x\343\215\002\364e\241O\000,0\301\006D\0229\301\002\0300\026\344\002L. \021I!\352\300\234\004EVI\301"\017TYe\216t\215\025\244\226`.\020\330\002ZJ\004\037\016\3124\322@\000`np%\007\036\015\320\346\006SeY%\205x\3469!&\02780g\225I\236\010\201\226\231\0148\332\\\036\370\011\346\233\026 0\347\004GY\220@\225\220\032\365\012\003\212\376Id\240^&Ze\001\231$wKZq\266\371\346\232s\276\251\210\004\022\324(A\217\250\010\300*\216\254&\342i\221\011\234"+\255\341H\320\311\240\232\026\231\011\000.2\211\201D\325pz\011\261\230\204\246\245\230\230\210:\232m\036\245\012\247\377\007r\266\371\000\007\255R@d\266\337n\313\310\003C\266Ym\233\\r\353-\245\271.Be\225\334\016\031\200\243`\016\020\300\275\020\3109A\007\030\024P.\230\312\222Y$\250\002\007\212\301\277G2`\300\263\231@\231\336h\2215j\255\304s\346h\347\237\024\300\312\301\277`N@1\230\212\\\274(\254\026\210L\301\273\003\030\220i\221\001x\240\215\006b\026\020,\250\0350\260.\221\005\310\274\301\276\000\350\254\245\003\364\026\251,\2106\334\022Y\003Aky%\252\025{\000\254\246\333\036p\263\251H\2679@"\223j\372\246\007l\266\011A\322Dj\360\022-\012\\\006\356\237\307\336j\244\320\012\234\035\354,\312\345pt\327DfJA\251\337j\351\321\312s^\0156\230\0170\2554\007\025\004Kd\216N\317\211\000\3358\303\002\0006\036\030\276shn\027\251\300\323\222krf\321\2435\242\366\006tS\360\361\377\000+Op.\351\367\006\300\367\006\021\254\356\300\353\016L A\342`&`\201\333\020\004\200\371\006\011\354=''\266Z*\253\011\003\364\276\216\357\352\316\2669\301\337\232\322\\h\22480\2027\221\241\013\316\270\233\001!\020\324\365\010\360\215\300\015\327\256\014t\005\031po\003\343\003\234\013\246\003\013S\212\237\001\332\347p=\361s\252\256\345\000\332#P9\264\317\243\331y*\221\253R\320\034\000<"\201\355\001\220 E)\036Q\003-uOo\2078\304\265\264\244\001:4`e\214\362\000\330,\320\276*\261O\203\014\223\005\037\024P\211K\\\340 `\213\300\337 \000?0U\340\030 \014a\377\036\246\003F|\214H\035\304_\221\200\246\245tq`W\024\240\200\003v\2472\245i\214\0037\334@\324:H$ZI\000\001\033\004\333\000\350`\200\353\011O\026\030`\222\277^\247\245\010\\\317\001\307\270\036\264\242\343\266\377+\306-z\213\250Z\221\006\020@\352\261\354z\\\222\300\324~\267;"%\000Vj,R\216\222\030\254<\332\321VV,\306\301f\306\265\340\011\206o\015\373\334\006\220\205\2113\202/\215\214#`\225\300e\200\335%Bd\177" \363\016\307\001\301\021)\021\236\014\326\004B\311\245B\002\212\030>k\236"\001p\031-\355\253Y\212\204\205#m\300\210\317\265\254M\011\370\034\244\336e\270\\\306\020]\264\373\026 %G$\012(R\021\246\024\3320\004f8\015\024\320H5c\342\006\234\327\201\320\254\214\232\215\204\336\015t1=\320\265\261J\025\370&\366\326\227:-!\260\006\2334&\006mU\307?9\363l\252J\346\246dQ\270\237\341\213\202\322\004\025\375\252\304\277j\346s\023\233S\220\224\024\241>"\205\263M\031\220\246\006\314W\003i\356\261\241\001\250\2348\243&O\240A\361\242\027\225@Ay\207L\260a\377\023\023\014\300\034\376l\000\266\012\200ML*\324\0225\367Y\244~^\202h\002\225\233"\222\030\020\267\331\016l\272\263R)\222\310%\226\024\261J\322\244(\372\356@T$\036\260\243\000\203\205\001\316\346$\262\\/\245\247\204j\221^I\026\276]\021\246\036\270\205F\3748\200\200\360M\002\241| \256\024qA-I/\022`\003[\216<\242V]\371q\003\351\212\204\333& <\014\210\021\023K\245T\025\011%\030qn P\027\250##/\3410\316\351@*\236\024\335\347\256&\317\015\350R\021\273\033\200\000\022\340\303\274V\351z''{\2424\225\2501Y\0051\224\213\010\310\365\214\244E7~\312\204|+\200.\311\362L\322. \225\302\302\320,\345\202\314\225\235se\271Z\254"''@\201\312\235\314J\225s\300h\251\247\3109\345R\244\000\264\254\246\364\347J\330\332\261\2654;\310p\347\364\321\016Hk\007\377\216h\255F?''\200n\022p\272`\272\227\341\3465\247pn\322\234\237S\225\255\032\333&\004\264\263M\316\014ah6\373''P\365\253\000\2012\324\016\020\261\321\355f\012R\033u\246r5\225S\255\271lN\002@\247\341\016Z\245v\021\264\006\357-\322/\353\005\316\223\342\225\275s\212\331\004r\306\011\375\352\340\033I\014_\221\266\225\304\300Mx\207\214\023\253q\005\020\211\341"\021\235\225\253RB\315\311\010\002\220\204\271\312\253\005\363\012L$\251\316\363\2455\030n sv\244\232yx8\015\250\342\2754@\321\305\005\340\001=Jr\352\236\334\335\206B\000v_\323`\352\036\000?\325\301\356u\271\213@"\002\242\301/k\200Kd\326\362\227\207\030\000\011pp\312Pf\304\245Hze,\273\0108\356\3052(\234\0143\025\246\016fYAg\235\205\013\212?7\265\003Lb\355liI\216~x\204\266\240\244\377Nvq\360\015\264<\372\260\233I\263\015\216\026\025D\220\203/|J\216K\200|\003z\200H)8\340\311&\024VjO\354!\026(\232\316\206\374\021\301Y;\242\326\263\256uGp\035\016\343\220\360\327\233\300\006\011\177\204\216_\023;@\361Y\320\210\310c\242\273dh:\036X\366.J\324\354\253$(\253\320\346\217\264\025q\354jS\004\253\206%\315\266O\301"o\253\2065\320\366\200\255\227m\356\245\004\324\177\341\031\267)\004\260\233z\357\246\335\201F\367\017\344\235\023j\233\373\335\360\2167\277S\322\355\013\345''\334>X\367\300I\204o>\005\007\010*Z\370.\006k\356\203#\\3\022\257\312\0056\316\361\216{|\343\015\177\251\251\017\336\232\214\373;\344\304\000J\331H\250\357\275d\234\003\345Fy\250<0\354\302\006<\010\332\036w\301e. ?h''\3352\315\270\314\211\001\360d\027\341\345\002\370x\307\377Q>\033!\304%\342\003\227\271\305\027m\223\227\233b\3470\312K\022\240.m\254\337g\352G.\002\3279\344u\367\200=\354b\267z\331\315sv\264\247]\342Ii8\000\332\356\366\267\363{''\024\277P\323\225\020\227\375\216\233%\015]\373gf\362\024\240\013!\347\354\271t\253Mds\276\033^\010\013i\320CR\235\367\343Xd\012}\3078}4\315\003\220`\007\000\215\247\311\343\211\340\015\356\000\036\010\3468\016\341\245\220y\210#\376+\247\037B\352+\263z\314\217~\353\257\317I\354\2150{/\325\236\012\255''B\344\217\342\213)\004\003+\027\200a\027\202o\004R\250D\025[8>Lha\213\333/C\201\357\200>\031(Q\371Xt\342\023\241\260>.\350@T<L\276\015|\000\204\372?A\367u\210_\017\360\217\177\313\345O\377\372c\306\376\370\317\377\374\365\317\177\3753\277\377\000\270\005\377\027\200\027\004\010|\357W\200\010\210\0203\260\200\014\330\200\016\370\200\020\010\201!\000\000;', '2008-02-22 18:41:52.024002', '2012-12-21 19:00:18.847331', false, 8, 16, 16, 3);
INSERT INTO card_tbl VALUES (5, 'JCB', 'GIF89a\264\000s\000\367\000\000\003X\230&\232L\377\377\377$+r\250+<E\250G\310#@<\213\277\343\030B\252jq\255\333\243\353Wv\350\241\256\237\245\304\260\316\345\012\213GV\264D\357\316\323Dh\235"B\203\350v\214\351\363\367\312\347\314\207.7\321\337\352O\255p\002p\265}\303\210\335\264\272\301\207\216\363\354\352\377\377\377\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000,\000\000\000\000\264\000s\000\000\010\377\000\005\010\034H\260\240A\201\036*8p\320\300\301\201\003\022\036B\224\030\361a\305\211\026)JLx\260\243\000\017\034\030tH@\262d\002\012\013P\252L\311r\245\313\005\035\026x\354ha\203\315\0148s\352\314\260\201\347M\233=\201\372\014\252`\246\321\243\006+xp`\241\001\007\211P\243J\235\032U)R\201\021*0\340@\301d\311\005\011\300\212\015Kv\254\331\223\035\256\016\254\271\241AO\234\001r\306\315\340\326f\335\266@\357\326\265\240\266oA\245\016$H\030 \001\200\341\303\210\023+\326\000\200\261\343\306\215\005T8\032\241C\007\002\0272c\326\234\371\202\201\317\237\021\200\016m@\264\351\322\245Q"\265`\001\347\203\327\260_\007\230M\233v\206\270\270o\353\206\220\2017\357\242~\373z\300 x\002\200\001\307\017\0337\274\034@\363\345\032$8\226\336\370\300c\253\036-0\270\260\371\002\002\357\340\277\213\377\367^\232\274\370\362\010\322\227V\017\366h\202\330\360e?\0100\0376\204\000\367\363\343\357]\300w\377\336\300\005wU\005\026\0106\330q\007\032\206\234\202\207-x\334c\020B\346\030G\0361`\001w\235u\247\241g\244u\210\032i\247!\240\232Q\033\304\007[m\263\3157\333n,\372\326\333\213\277\011x\225\003\310\325\230\234\203\212\345hX\204<\002\200\235A\035l\327\335x\337\031\000\236\221\246\231\247d\210\250\211\326\236G\256\231(\037}T\326\247\337\225\375e\011\201\226\001\3128\223\005\010\326\230\240\216:\366\310#\205\006Y\330\031g\234a\326\335\207\243\301\011b\223#v\024\245\224(\242\250[nq\271\350g\006]zi\020\006`\032g#r\317)\227#t\022Z''au?\022$d\206\341UJ\344\222\350ej\032{\011t\224@\211R\236X_\225\364]\211\337~[\246\372\037\004\201\012J\220\005\025\020\377&\346\254\015\326\252\330\000f>\252\001\232\257\256\211!\233\334\031\211!h\242yXl\223t\312tPO\241\236He\236,\366\011\343\2641\272z\220`\003\034z#\231d\346z\235\007\005e\265afE\036i.\223\347\241\213\354\223\0059\320ll\244\216\372\200\251\252\326\313\245\265\005Y@\343\004\264\312Z\030\267\213\351*\260\006\222\025d\231\257\224\002\373\346h\3072\214\254iu\022D\337\273\262\345i\033\237-R\013(\276\0041\225\355\266\206&\307\234\242#\227\254\201\243\020R\027]\244\002P\320@\233\030^*3\246\351z\247^zNvj\020\305\360\305\373\032\275\370i\311\237o\255\272\032Q\266\262\206\2514\203L\213\374\340\300\272\362\352\301\270\276R\215\031\261X\033\373p\304\002\260\306s\305*>\233\033o\322\376\351b\321\202b\253\255\266\000\007\354\255\217\340"T\201\302\235\225k\267g47i\336\303\006\260\377+\220\327_\307[%\320\366\326\213\266\2275\362\253t\322m''\366\366\204\223\375\335\001\302kn\350fgrf\215u\210\\\333\364u\330\026\357)\272\331/\036.\240\005\037#\272\255\352%;G\262\353\272\242\334\350\217.[m\351\3555''\251{\223\234\026t''\305\202[y\352\225C\013\315*\307\002\355\333\357\201\214''\346\340\363\2177\206\246e\335U\236p\260\234i~\354\366[+;\320\357\357\246\030\272\264ek\\-\307\034\244\276\372\266\215#\026\375\217$\321M.\336w#\371\360\336\352\346\\P}\201\313;\370\360\250\322\222\000\217\207<\032\205i0\314C`\373\016\023=\202EN\000\361\243\234\374~\305!\3159\014]\\KQ\340,\266"\214\225\357O\246\013N\003\324g\243\2209(Q\255\203\216\312T&;\370%\300v3\223\231\246\226t\263\336\011D)\237\203\227\274~\006\300\374\014\355\207\004\344\230\362\377\026\227\300\347\331\312i\270\202\032\204\320D\022\333M\360jZ\323\032\347\274\227\020\015\362,Oa\213\226\371\246\025B\277\214pm\353[\340\216\224\010\031\027N\360RH\242\337\014w\247.v\341pbW\364\237\360\3505\300{\025p\000\374*\342\322\304\330@&&@\202t\273\\f2\347\2419A\214\212\025\260"\3608\350\301\214\201\020y\002\370brL8\311\3275\0071\214r\214\3544\351B\030\342\316Rk\324\324z\364wC\017\360/\216\301#\\\361R\005 H\0161i\260\034\223\021o\325\307\0076\221rV\023\026\024\341\304=C\246\006\221\212\014\237\330j\243E\322\235\015\222\351\003#\333\370HF\306\230\221\202C\242\2374\355\027"\374\361\215\224\2221%\034\201''\307R\365\260\216\206s%\036c9\253\177-\260\201\005\023H\004%\350\304\012\026rs\335\033H\025\267)L\016\212nl[\354b_$\251\377-J\246\320\222;\332dcV\330IJ].\206\240\314\035zj\210M\034\346pJ\247$\234\361V\245O\265\274R\217\315\233%\022k9\220[\002\313zt\323\236H\247(\317D\322\263Y\342K)>?\250\261\212^\205\237\353\263\221\030\233Y\306\270A\360\217\320\254\3334\317%J6\362\315\215\332\374\\\360\274I\307\302\365\307\245HQ\036F\307tN\232\356\312\2268e\247A)H\310\314a\020\230''\015\025#\033\311\322c"\017\246\224d\035\354\306\212B\010\011\364d\005\245\033B\211\024\312t\215\262o:\313\346)\271\231\312\036\006\260^\255\274\243\342\310\271G\304h4\211\217\363c\365\256\247!]V\260\227\274\214\247\334\202\211\322a^\354E]\345"2I\030\323\231:\025n\035\305)\325\352\307\323j\346o]q}\243P\2739\257\036\256r\200H=\212\001\375ENs\266\017\235P\005dN\201\025\247\206\377\301\223\244\0031\251P\031yOGJ\366\253\224U\235L\311\012\320\201NG\223\337\312\254''\327J3\032n\252\241\246|\250\034yH/ RT\234{]\352_\217\270\240\300B\265\235\266\023\251/q\373\221\012H\267b\216\205\354\026\275\3121\230*\263m''\243\312Tj\232\331\220^\200$.\263\220~-\020\001\376\372W$\014\240\200\200\301\362Y\277\011 .\376\233\213n\340"\227\0063\370\233F\015"\276\224\272<\034qK\276S\201\\}qy\337\004X\206\003\035\260@\210#@b\017D\300\003!\266\020LXr\333_\372\3566z\332\211\214g\334`|\222.\265F\001\353\372P8V\000`\340*\372\202\224M=\312\235\006P\300\0026\025TV\372\033\340\005\330\020\222\177\361\200\002\024\260\201\004\004\015\257\022\266\326E+\314\335\303\360%\251\026\320\260:_\270&\017G@\000g\266\226\007<\240b\012\034\022\377\312\006I\210\002\330\022Y\034\317$\231\225\035.\231\036h\224 ;s\3108e@\005\322\014g\201\004\0115\006.tA\024\220\037\324b\227\257\376j\333\227U\353\000\307\244\263kiI\262Z\326\\b4\033\305BoV\264Gn\362H\340\202\321\237\304e\316\217W\343\200\201\376\310B\274\272JB\372\273d\226\025$$O\026\265A\334\342\037\242=\232\210\264j\232\203&m\024\246\21098''\366\360\260<\264b\355|d \014`@\250\2152\347\025\321x\003\261\246IkJ\335\336\340\206\221[|\376R\253\377,#\376\032Y\215\351:\011\005,\224\225\201\034\032\2333\231\262\212\034\354\023[\037\2045\205\263\263G(\014\354\306\021{&\306\226^\270\221\202b\014\275\311\266s2@\007N\014j\027\037\305s\357\302\011R\224\322RH\352\270\237\305u\316\252\217\342\347\003\330{&\011\241\300''\331h\263\357\0349\002\270\377\206\367\250\033\000\274\014\020\274\002X\326wG\266\334\357[yyFa\026\270_"H\000\303\212\227o \342\232\235@\325\263a\376\273#\036\2508\362\360\374^\013\347h\3404\0317f\257"\222\371\245\221\232z\373\354i\022\275,\242\353\220TG7\010\276\035}\307\003\266V\3223\252td\240\336\221\220L\260\266q\032\257\303I\344ug\241(\354\371\332\366o\273}j\366\245Z\343@\226\372\307\017B\022\346\222\\w\014\205+R\332B\261*#\205\327@\224\371Ah\316Wa\337\034\314\307v\017\231C\032\305\304\222w&<i\374\006\026\337\023n\343\313\275\225\005\030\333\305.xMw\244\277\230\031\317\325\233\353S&q]\3545\321K\225\3332\345\2530\032\234\2227\010\277+\0170\274\013?\347O]\315\344\246z\360\270[U\261\242N\010klRg\213\253/\254\177\277\344\306\373<n\217\273\376 j\032y\377x\332\212\277\\\027Z)\323\267r\341\362*\304q\366;\243G4~\273\220\237\355{o\007\232>\357<\367>_\3509\317\231\30176YM\3474{\026x\364u\024\026r5\347\202nYwM\240\245k]c\001S\006\027\021\026|\3552N\332\345Z:"\177\035\243v\016\244| EU\016\023w\371#t\007!}J\221\202\011\261\202\253\227/\244\266w\247\347m\250\306c\023\260}\342\346j\337g\020\3755?kE~8\363V\267W\020\214\247CSRe-(7\237\302JY\346*\224\247G\226g\030\0348\020\001\227|GabOtX?wUH\021zE\367,6!ko\301^18\200N\247\030G\270\026\255\247\026\234e7=\325\206[\027W3\001q=\263C\243\267\032\026\020Nz\005iLE&Q\230<\364w\206\003a9T\205p\317\307\177\035!\207\360R\033\257Q\207\034\247w\347\3773\206\225\204q\377\004\031j\321q\203g\020.cx>\310;*w\210,\027\037\202\023\000\365w\020\015p]y\370~{\3104\004\203s\231g\024\324\223!\371\227XR\224,[Xw`\203"\317\266xbh-\311\344mz\246\030\015\320\000j\361\020\007\250\026\205\267S\350bMm\004\207\243f\213\335\024\000}\261\001v\204<\260\322\020\030XN:\362\020j\241\024\020ri\233\026U\013cAt\302p\0260mq\350\214v\027\000H\265\001\273h-J\021\214\307\36113\250\034\332(k\025\320B9\350\021\011\361G1\264F\351q\001\013\300_)\247x\017\367\211Z5\037HU\212\276&jL\301\020\007\020lO\323\000\243x\020\017\261D\200x\020!\026?\273\264=1\021m\271(\000\321f\216\036\361\1774\206\023\033\320{\003"\0000\010e\004\302\020\015P\030\277\330\030\015p\211\005A\214\311u:\014\377\020?\324\244$\002\266\025\355&\020!\201h\314\330\021\023\270`Fi\023(\371rx\010\201\271\005+NI 4\271kg\362@"\366\221WAb''\226\225&\366i\3458wL\231/\012\260\222_\351%JQ\221\261SP\014\300\224\003\031\204\2426e17\226\370\222\0206\0315\337u\001}\350*!\351\225c)\201J\3071\036\020\214\004\202<\303\321\000o\363L\004@\022!q\227W\311_\002&\224_9\031\323\327hK\211/\025\320\020\260\202<-\331L\227\266N\011\320\000>\011e''\006\023"\251h\322W\023\276e\201\004\301O\006\022\214\034\020\214\015\011+\303\001\233\257\371\222\022\321\030\023\300#\004\005h\202h\022\371\245\035\376Ek\277\251\0351\201\022d\361\203?h`\017f\224\012\266\234\242\003\027\237\322\000s\201%\275\306~\023\346~\326\351\204H\304]\0345fT\023\202\033\222\205\016("\210\377t^a\003:\344\263^\245c}5\2024`4S\357\003hW8{l\350VZ\347\230\245D\236\244UT\300\367h\332eY\333yS\262%\210\315G\202pG^\363\264[\366\324H\306\2641\246\026\211 \223qe\325(<\342}\312EX\342\307V\012E\237O&Z\375SW\325\265~K((M\010\221~\245\235N%X\034\2460\261\210X\373G\213\213\225Ux\342X\035\244^\013\372\210\274\350\213~\327T\205\011\237\263\265\200\362y?\365\011]s\025>\371iZ\021vT\342dvE\244\201\360\345T\233\031U)\332\243pG\240Z\230[\214\245U\274\245\240\371\244\236;\346w4H2\321q\\\325q\2237\265\\\027\352\203\211\007T\347\3453\245\365\241\253b\212\355\227]\\\366\204\033u\242uy\205\234''\213\236\347\242\345u\2451\252\210\217E6\350i\243\256\202z\355\311L;\272a\233U)?\377\232\241\312h\237r\005\243&2Tm\012a\373\231\207\375\351\236v\272aR\0053\003:\245-&\236%\345\247\223\232\245\275\265\240\2509\020\027\367\240\223\230\030\2314\246g\251\233\026jx\264\327S\033\032T\035*G\252\004\242\251\232<\3279\247\331IK\233\312\235QZX\331\243\177\241Z''\007\272A\3469\243\202Z\243\014\212>8\032\223M\232\250cvF\310\310\200\265\347\200j*\251\240X\244\322\011NH\212\251\025\306\244\000\003[\234Z\254\327\003\252\027\004}\313zE\246\2121\250\312\245\0307\\_j2g\225\233\025\352\251\027:~\216j\234\267\272\246;T\251\341:\235!\352%#\272G\333\245 \377Id\203\025\237\310:^\312\252[\314J\033Yt\236\320\332\253\2214\255\004\370Z4e\230\327\263\206\350\246\214\217*\244\336:\207\036j\251\371\306\237\300\252\243\315\344G\001\312|\230C\210\266U\245}\377z\262\211\230\240\247\272\245\015*\\\254\232}`*\241\261\272\2579E\253\207g\253&;Z)K<\274\372kzX\206\177\325\260\233W5\204\265\242\340i\240\024\013\257\315\272R\203\032\255\370\302ty\346\237dT\217\212\032\262\331\212u\333\252.\024\220\026\367\211\263\242\262\264w\005N\212\231T\345tv\210\252Dd{\255\200\324N\354\032\252\245\341lV\352\266\3505>;K:Pf\250\256c\257\020\272#+\204\\\000\220\267e:\253\375z\264\342\221x"\221[\321\365Pl\272\253o\352\033\0279#\277Zs\301Z\247\336\262\020\005\341\260\026\232\247,\032\252\236\366\242\010j\261\201\352[.\302\023\207\313\261ex\256d\304\024\247\253Yf+{\235U\253\352\362\223\221\252\264\272jW\021\366\205,+\272.\333#\007\020k\353\244\256\202\344\0314[\240z\371\256\213\244\263\362\332Rs\373x\327\327\245\255\212Ic\377\344\270\234\244i\250\313\031F\273\211\233b`\034\212J\305\353\246\206\243\261\002\220\260\360\3274~\3656\016@xS\353\235\260x\254z\332\242\010\300_\005a\275\302\264\265\305\2441\310\013e`\373^\232:\241\007\340)\274\013,"\213\266n\330$\034p\020\353KW\355;<r\273\275\240\233G\345\272\300\002\003\271\351\2120|;\202\324+\032i\031gY{\275\204\233\275.\302\032\205\206\270\363\330\252\320\261I\0171\212\250{P\223\213\246"B\001\035\341P\232;\260\022\305J\034lQ\241K|\013\3734\021\242\2159X\276T\253\272\340\271\000)\254\302\244\012\212bs\2614J-E\374R\266+\266\217\321\274-\310sf+\237#\233\214\246\021\0223q\301D\012\267X\002\303\0149Nr\332W|\364\020\372\202\024\317\033\263\321[U\305\202\022\204\246\217\346\265\246\330\373"\033\020\225\326\362EH\203}\367\372w\217y{\000\272\253y\355\244\211\012\005\026}\374\303\231+]K\333\037\330\366\271\010k \202\001\021\234\334\311\237\014\312\032a\021\014q\277}\361\212^\221\312\252\374\022+\241\035[\331\027p\001:;\361\026%\251\023@1g\023\011g\034\020\312\274\334\313\027!\025\255\271\275.\223\031\252\\\314_Q\026\310,\022\375\345\027\010F*\314\311`\320\254\234v1e\204\014\227\326|\315\330\254h\001\001\000;', '2008-02-22 18:41:52.024002', '2012-12-21 19:00:18.847331', true, 7, 16, 16, 3);
INSERT INTO card_tbl VALUES (6, 'Maestro', 'GIF89a\264\000s\000\367\000\000\000;z\200\270\330\277\327\347P\241\315jFo\317+E\354\305\315-\205\274\020X\223;}\262\000\203\303\277\314\334\000N\216;.b\000i\250@g\226w\\\206\000A\200\000v\265\000\226\326\0361k\200\232\271\336\0353w(Q@\266\346\277\347\367\262!@\000[\233\337\346\356\373\306\313\000\211\311\224$I\200\316\356\317\0368\000\220\320\362Tc\262<[\357\362\366\301 <\000o\257\0173p M\205J,^`\200\250Y+Z\217\246\302\205&M\000H\207\020\243\337\000|\274\000b\241\317\331\345\237\263\313\376\361\362\356)<h)Up\215\260\357\371\375\000U\224\257\301\324\237\332\362\243#D\017\225\3220Z\215,/f\020A|\360FV\371\270\276\366\215\227\337\363\3730\257\343\364q}\3578I\317\355\371Ps\237 \251\341`\302\352\375\343\345P\274\350\224Lp\257\340\364\370\252\261\374\324\330\336#:hd\221\367\233\244\217\324\360p\310\354\205T{\243De\3012N\365\177\212\036\215\307\351\253\265\277\340\360Yl\234\363bp@\233\313\256\261\306 \234\324YR\201\217\267\323\020\235\331Jt\247\274\242\265 h\237\337\354\364\020o\252P\207\263\327{\215\273\223\250`\273\343\330\220\240`\215\265`\224\273\2630M\234\210\244 [\222\304Jc\241d\201;H}\200\247\306\200\255\315\257\315\341\377\377\377\000\235\335\355\033/\0004s\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000,\000\000\000\000\264\000s\000\000\010\377\000\377\010\034H\260\240\301\203\010\023*\\\310\260\241\303\207\020#J\234H\261\242\305\213\0303j\334\310\261\243\307\217 C\212\034I\262\244\311\223(S\252\\\311\262\245\313\2270c\312\234I\263\246\315\202\000\0300\320\341\240gO\031:#\324\244\320\240\301\215\013H\221\252(\212\342f\312\0272$(\350C\265\252\325\253\036b8`\000 %\212\006\027z\204\360C\266\254\331\263\032>\334\000\342\364c\004\0311&\\\235Kw\256\007\007/F\002qa\342\254\337\277~-\364`A\241\355E\000\033<\324]\314\330\252\210\023B9Rp1\026\260\345\313eM\260hj\030b\004\011\215C\213\356\243`CF\026\0320\253^m\341C\341\316\013\031L\035M\273\261\010\323\024YT^\315[\265\206\006\260\017F\230]\2738\343\333\021u\367^\316[\303\353\340\000N\030\237\036ZA^\206@R3\337\276\332\005g\303:\344R\377\037\317\330AW\204(\\p_\277:\204\212\266\000@\223\237\277\330\303\365\202@\372\262\337\217\371\303\367\231/\210@\337\200\213\311P\320\015\374%\210\231\011l\315\264\201x\004Fx\225\004\002\241\360\201\202\030Zf\001\01329 \341\207v\241\240_\206$\372u\001L\362\201\250b\037\007hQ\342\213~}\340R\212+~x\300\0240\346h\226\214+\321Xc\2047\352($Y<\242\344\343\217\003\372P\300\220L\026Y\222\207HJ\350\203\213L2ybI\033D\371a\002\020t\351\245\227TV\211!\207#\275\000\241\226H*)&\206\0264\010\022\000\212\241\211\346\001kbh\302\177\035I''\247\234\020\324\251\240\013 1\260\347\240$\370\231 p\035\001 \340\240\344\035\360\345\243\220B\360\204\241\374\205\200''FP2:\336\222\224\256y\245F\021hJ^\237\235\326\371\034FG\212:\232\0178\226\272\246\223\026\205\377\252\352t\244\272\272\346\251\025\245:kh\255\332*&\254\023\311\272\353\236\234\372\332\033\256\022\3519\254\234T\030\273\034\240\026\235\271l\224>8\333\233\005\026e9\355\236\223Z\273\032\231\023\305\260\255h>$`\356\271\350\246\253n\272\265z{Y\017\024\0010\256h_\270[\347\245\015i;/cY\330\273\346{\022\351\312\030\014\030\024l\260\301K\314e\304\301\014''l\234\230#D,\261\015\306\002\333\320\242\265\201\300\307\306\034s\014\302U0\344\320\361\310L\030\227@\225#\214\314\007\022\306\206 \221\260\243M\300\300\036*{l\225\003r\324\314\261\021\306\265\233\343\015t\214\\\203\265\310.\244Cq\012\374\301\201\316||L\225\010J3\315\307t\\\254k\365\325\351\366{\026\005-\214\334\201\265\000?\224\351h''\004!\265\323}\310\240\204\324IHH\360t\315\236\365\307\002#\023\241\240\304,\263\367\251C\304\215\377\266\301\003gS5\001\000t3\315\003U08\001B\006E\214\\\004\017\030,\006\303\025\031\214\014E\311u9\301C\022*7\321\201\020\032\374\321y\007\035\034A\026\351\250\017\341\207\015D\240>BY#D\321\204\320C\230\276]\350\020a<\332\0138\004\336\207\004)H\315\307\025T5.|\0060\314\305\204\310:''\301\263UF\030\317\264\015.\004\257\363\010!h\341\265\0205t,\204\037#t \274\024\337/gBD\306\375A\203\324VP\025\301\372Rc\020\303\013\302s\334\266U<\324\237\303\363}\030\301<\323M\360\003\013\326\2463$\\\000p\035\033B\3678&\005?\034\241~\033\253A\371z\003\021y\325&i3\030Y\3416\226\201>\304\300l\035[\332\310\322\206@\010bn\002\371\203`\007\177''B\251\251\256\001\025`\232\037`(\274#\264\001\202\034\253\001\305(\370\020A\325\206B#\233\301\006\377\371\320A\006\304\220c%\330\301\310\332\306\200\336ul\001\031\254\031\024\250\302\206\232\011\261\004*3\202\002V\000A\273\375A\2115\373\032\012\340\307\264;\340\260cUX\016\242\032\342C\332\310\240\204\033\243\301\0203\000\265\026\362\241\005C\344\303\341\376\260\202\026(!\005\005IA\0249\326A\031\330\221\017%x\200@\202@\306\215\201\200\001]\033\031\016\202@\020! \341|\207\\@\005\340p\004l\015\262f48\344\306v\000HF\252lh\275Y#C\3328\032\006p\261c8\230\343\006^\3111\353u\014\004P{\301\006\034\020\003\005\370\322\001B\201#\021\267\250\2620\374\362\017\266\334\230\027\346\2462\251\234\300\001x@\220\037z\000\302\216)r,\241\253Y\011*\360\200>\326L\014,\270\301k\362\310\207\327\361F\225\261)\316\037"\311\261\007\260\223\017\313\374\344\016~\2402\014L\340LK\300\200\342@\340\377\315\216e\200\001`$\244U\014\244\301/\252\214\007\311\353C\255\016\250\262\274\371\201\241*{@\003\232\022P\2165\301\002e\341\020\001;f\267s\366\2606\036`f\307\020p\304\215-@\230\335TYB\373\260\004\036H\217i\001\020\335\031G\371\007''\036\024\003\265j\200M#h\026\025\320\222c]\300\250\313jf;\262X\000\005\302\344CGW\203N\205\260241\220)\307\212`\304''62\011\021x''\037\212\2008+\2341\016I\255_LS\200E\235\031`\207\024h$\037\276V\026 h\265\234dqAXwX\226\006$u\251\252ijB\236\332\030\007\320\263cP\250*\022G\366\006\221rl\2120\340\334\031\037\020V\3411A(?U\231\024l\200\255<\342\325\260<%\213\012\222\332\300\263lVex\305\214^\021\302W\306\270rd\217,\251\312r\220\264\262z\314\003e\250\031\007\026\000E\225\377E@\2658\324\242@\036 J\216E!\233#\003CYB\347\332\215E\241\256Ie\253Y\032\020Y>l!\225\037\245\015\000p\213\001\0074\266i''H\346\3060P\007\225\221r \033\335X\022\376\200[\016T\340\274\350M\257\036\234\026\203\363\254\340\2239t\301_\275W\226\352\251\254\250\177H\256_\032@Ns2\365!\026\024\015\324\362\010\003\3532\015\006\033\010\357\306\326\260S>\010\340\004\012\360\300\0046\240U(\344wd\034\320\211\2067\314\000\032M\370<\017\200/\037\010\240`>\230\345\006Iu\350\205U\346\227\260.\007}\243im\307r\320\007\003\327\354p/\300m\016\000\240V\264\365\001\001\305m\332\037\346\313\261\310\321f\002\220\031rDq\253\334\031\342\026\225d\241\200v7\366\334\262\330!\310|P\035\017\037\022\247\3208@\273\035\264q\026\373`\320\216\011\000\263D\254\212\031D\214\201\274\0049\377\007\230\263\312\022\230\000\205+,\301\011+\355\303\004"\320`>\240\031\256~@B\2319\326d\340\210\262\006\266;\302\002G\346_\325\234\017"}c\214\016\024\374\261\354\326\254\203I;dL\337\232\001\020X\341\177\035[\202ipK\310\014\230\372\177\372\334X\021Lmj5\250\214\003\177\346X\033\242\306\321\023\327t\246\033\323rs"26\306\334vdN \215T;\026l\011T\223c\003\340#\256\205\035\204\336\352\354\005y\300\241\033b\275\261;\014{c\302\315\314\037\232=\323&\330\000\011Q\210\002]-\263\267\206\034-4\023\030\364\306\022\2264\225q\225\204*\033\203@\234}i\2520`\310X\2764\265G&\2059\334Zg\020\245\357r\361\215C\362\371a\010\244K#f\302\346\020\230-&\3233\246J\273GF\274>\010\226c4\276\367\017\362}\307!\036\256\017\036\350J\012\310\251\2622\320Zj]\377\260\201\313\270\3359\235\262\370,\226\032\262\210U\206\206\035\016\241\012QP\370e\212\266\020\335\325\345\004s\243\355\002\004\320>a\013}\350\310\243JM\217Nt\220\213\234\006\256\345\000\015\322\260N\241g \316\036\210\314\017\360\250\262\022,\000\004\007 omG\346\365\012\020\240\256\333V\253\001\266@\201\025\034\335\000:7\313\235\004\342v+\266\200\000\030%\213\020\2060\204q\377\305e\001\013\015n\006B\234t\023D_!-\010q\262n\020\3318\240 tIrB^Pc\243\221\301,m\032H\012\024\371\007\015`\253 \332\371\313\206\010\362\003\3062\366\017*\010=s,\306\220s3\306\003\276\214\375\231b\357K\010M\200\366\265\277\212\002|"\201E\211\200\366]\236\213\010\264\342\023\007H\305*\267W\3003y?\233n\231\245\007I\271\200\347\375`\001\015X\377\372y\327\020\364\227\322\000\025\204%\373\334a8\377\200\367\325\230ra\355\374\351\352\225\277\376R\021q\221\277.\325Z\377\220Xo\356\367/Fk\362\317\221\370#"-\373S\345\014\371\227#\237W\021\275\346\177TQ,\001\230!\345\3662\0068\027q\223\200\031\302s\020!0\344g~\350g5X\000\201;\222\021\016\327\200\344\201\200\020(\201\021A\201\0368\032\365\242\201D\262\021\021\320\177%H\033\254\202\202\324''\202\022Q\200-8\032\031\010\203\013\210\021>g\200\\@\002>\370\203@\030\204>\010\203~\020\002\024\020\002\032\220\203\024QZ\376w\203DxN7`\002\325\307\021\312\342\201j\362\204\252\001(\015`\001!\000/\034\021|\015H''Xx\031\217\366\007@0Q\035\261\2025\370\200cx\026\026 \203\031\341z%H\005\221\002\001N\210\202\243\345\021\372R\203s\341|\032\010.%A\202\015\350\207\020\010-F\302\207VA\210\011H\177#!\210\344\377\247\210\001\310\210$\341\210\343\3423\011h\210-\261\207\006\030\003(\240z\001\010\210.\021\036\015\010t\002\241\036\001h\001y\270\022\021\000\206\3432a\005\301\002\340\347.&\000\207+Q\205\333\302x\006A\001#\342-\230h\023\014\260\203\2522\001\217\247\020\027\020\213\256\022\002\2518\023\016\300\202\203\242\000\221\261\020\024\340\211\224b\001J\350\024\237\241*\036po\020\321\000\273\350''\256\021\034\006q\215{R\032\025\301\002\335\330$\264h\030\021p\002\314(!1\240\215\027\321\000=\300$\324\230\216\340\270\001\356\367!\036 \003\317\250\021\024\020\2050\322\003\373\007\216\015\001\000:\320{\364\021\003\3748\022\3778\217\225\362\001*\200/\004\031\021\021\300\023\012\000\214sq{\022 \003\367\201\022@p\003\0370}\252\201\204\027\240\002\3668\221/\263a2\260\001\032\326\2213q\206E\341}EQ\024''\211\2226y\2238\032\231\223:\271\223<\331\223>\371\223@\031\224B9\224DY\224Fy\224:\031\020\000;', '2008-02-22 18:41:52.024002', '2012-12-21 19:00:18.847331', true, 9, 16, 19, 3);
INSERT INTO card_tbl VALUES (7, 'Master Card', 'GIF89a\264\000s\000\304\037\000\340\234\013\212\221\257\323(\025\354\352\357\332Q\010\367\341\263(,\\\242\006!T]\212\350\252\256\250y\034\361\315\177{^/D\026H\306\312\330\344\200\210\254\261\305UGB\333LW\371\351\332\362\304\307\217blt\0175\324\202\011\355\270E\2232P\263\224\224\377\377\377\314\000\020\350\236\000\021\037^\000\000\000!\371\004\001\000\000\037\000,\000\000\000\000\264\000s\000\000\005\377\340''\216di\236h\252\256l\353\276p,\253^m\337x\256\357|\357\377\300\240pH,\352D\306\244r\311l:\233\310\247tJ\255Zs\321\253v\313\355b?\336\260x\014\005\223\317\350\364\321\254n\273\303\331v\243a\251\333\357\363\367-\022a\370\377\200|zqct\007\007\034\211\212\213\214\211\007\026\015d\006}\012\000\035\227\230\231\232\035\027\012\014\021i\204]\026\210\215\246\247\213\217\221Z\006\014\027\233\260\261\232\236\006c\242W\015\245\250\273\274\217\265R\255\257\262\303\304\234\021\277\\\267St\274\315\316\216\253K\021\012\305\325\305\000\012\310W\312N\006\026\317\340\316\007\321D\323\326\347\325\331Z\334K\314\341\357\315\343C\006\324\350\366\305\352U\354I\337\360\376\315\026\200\320\273G\360\032\003}l\234\344\372\307\260\031\271\034\021,\025\2348\354\202\2662R\3725\334\210\352\200\216\201\024C\016;\370d\237\020]\034S\3776\222g\303\2000\2210a\345cb\362G\003\2258Q\255\212\020\263g,\000\027\215\324\354q3\247\321F\026\030\370\\\012\013\324\222\241;\212\036\235\232\210\000\323\253\232\234&\201\232C*\325\243V\261\212\275\244\265\010\327\033^\277\346\014;vl\331!gk\244U\253\222m[\267B\023\006\231K\227\243\200\273\200;\004\375\021\267/X\002\210\023''\016\334\023(\221\263(\015\323\375\313\030\346\205\307z}h\224l\330n\345\211\012\340f\346\301\227\263\332\227\237''\276\355\001\325\264k\312\251'':\0062t\263\353\177\212s\353F\214:\366\275\320\264Gw\275\275\321\263\357\266\253\327\000\211L\374\335\361\312\227\011\013G\333\234\241\361\347x}\230\254\316\020;t\351>Js\347u\335\273\330\3447\3661\037?\331<\272\350<\330\211g\377\265\267\373a\350k\260\263M\277o\371\373\233\000\267\003;\375\335\006\2405\203\3517\335\377|\005."\300\203\020F(\341\204\023\332w\340&\371q\263^\203\215\\\310\230\200_\360\300a3\260yxW\202\31208\342\177La\340"\200\350)\303\237\000\017\324h\343\003\0228x\343\215\002\364e\241O\000,0\301\006D\0229\301\002\0300\026\344\002L. \021I!\352\300\234\004EVI\301"\017TYe\216t\215\025\244\226`.\020\330\002ZJ\004\037\016\3124\322@\000`np%\007\036\015\320\346\006SeY%\205x\3469!&\02780g\225I\236\010\201\226\231\0148\332\\\036\370\011\346\233\026 0\347\004GY\220@\225\220\032\365\012\003\212\376Id\240^&Ze\001\231$wKZq\266\371\346\232s\276\251\210\004\022\324(A\217\250\010\300*\216\254&\342i\221\011\234"+\255\341H\320\311\240\232\026\231\011\000.2\211\201D\325pz\011\261\230\204\246\245\230\230\210:\232m\036\245\012\247\377\007r\266\371\000\007\255R@d\266\337n\313\310\003C\266Ym\233\\r\353-\245\271.Be\225\334\016\031\200\243`\016\020\300\275\020\3109A\007\030\024P.\230\312\222Y$\250\002\007\212\301\277G2`\300\263\231@\231\336h\2215j\255\304s\346h\347\237\024\300\312\301\277`N@1\230\212\\\274(\254\026\210L\301\273\003\030\220i\221\001x\240\215\006b\026\020,\250\0350\260.\221\005\310\274\301\276\000\350\254\245\003\364\026\251,\2106\334\022Y\003Aky%\252\025{\000\254\246\333\036p\263\251H\2679@"\223j\372\246\007l\266\011A\322Dj\360\022-\012\\\006\356\237\307\336j\244\320\012\234\035\354,\312\345pt\327DfJA\251\337j\351\321\312s^\0156\230\0170\2554\007\025\004Kd\216N\317\211\000\3358\303\002\0006\036\030\276shn\027\251\300\323\222krf\321\2435\242\366\006tS\360\361\377\000+Op.\351\367\006\300\367\006\021\254\356\300\353\016L A\342`&`\201\333\020\004\200\371\006\011\354=''\266Z*\253\011\003\364\276\216\357\352\316\2669\301\337\232\322\\h\22480\2027\221\241\013\316\270\233\001!\020\324\365\010\360\215\300\015\327\256\014t\005\031po\003\343\003\234\013\246\003\013S\212\237\001\332\347p=\361s\252\256\345\000\332#P9\264\317\243\331y*\221\253R\320\034\000<"\201\355\001\220 E)\036Q\003-uOo\2078\304\265\264\244\001:4`e\214\362\000\330,\320\276*\261O\203\014\223\005\037\024P\211K\\\340 `\213\300\337 \000?0U\340\030 \014a\377\036\246\003F|\214H\035\304_\221\200\246\245tq`W\024\240\200\003v\2472\245i\214\0037\334@\324:H$ZI\000\001\033\004\333\000\350`\200\353\011O\026\030`\222\277^\247\245\010\\\317\001\307\270\036\264\242\343\266\377+\306-z\213\250Z\221\006\020@\352\261\354z\\\222\300\324~\267;"%\000Vj,R\216\222\030\254<\332\321VV,\306\301f\306\265\340\011\206o\015\373\334\006\220\205\2113\202/\215\214#`\225\300e\200\335%Bd\177" \363\016\307\001\301\021)\021\236\014\326\004B\311\245B\002\212\030>k\236"\001p\031-\355\253Y\212\204\205#m\300\210\317\265\254M\011\370\034\244\336e\270\\\306\020]\264\373\026 %G$\012(R\021\246\024\3320\004f8\015\024\320H5c\342\006\234\327\201\320\254\214\232\215\204\336\015t1=\320\265\261J\025\370&\366\326\227:-!\260\006\2334&\006mU\307?9\363l\252J\346\246dQ\270\237\341\213\202\322\004\025\375\252\304\277j\346s\023\233S\220\224\024\241>"\205\263M\031\220\246\006\314W\003i\356\261\241\001\250\2348\243&O\240A\361\242\027\225@Ay\207L\260a\377\023\023\014\300\034\376l\000\266\012\200ML*\324\0225\367Y\244~^\202h\002\225\233"\222\030\020\267\331\016l\272\263R)\222\310%\226\024\261J\322\244(\372\356@T$\036\260\243\000\203\205\001\316\346$\262\\/\245\247\204j\221^I\026\276]\021\246\036\270\205F\3748\200\200\360M\002\241| \256\024qA-I/\022`\003[\216<\242V]\371q\003\351\212\204\333& <\014\210\021\023K\245T\025\011%\030qn P\027\250##/\3410\316\351@*\236\024\335\347\256&\317\015\350R\021\273\033\200\000\022\340\303\274V\351z''{\2424\225\2501Y\0051\224\213\010\310\365\214\244E7~\312\204|+\200.\311\362L\322. \225\302\302\320,\345\202\314\225\235se\271Z\254"''@\201\312\235\314J\225s\300h\251\247\3109\345R\244\000\264\254\246\364\347J\330\332\261\2654;\310p\347\364\321\016Hk\007\377\216h\255F?''\200n\022p\272`\272\227\341\3465\247pn\322\234\237S\225\255\032\333&\004\264\263M\316\014ah6\373''P\365\253\000\2012\324\016\020\261\321\355f\012R\033u\246r5\225S\255\271lN\002@\247\341\016Z\245v\021\264\006\357-\322/\353\005\316\223\342\225\275s\212\331\004r\306\011\375\352\340\033I\014_\221\266\225\304\300Mx\207\214\023\253q\005\020\211\341"\021\235\225\253RB\315\311\010\002\220\204\271\312\253\005\363\012L$\251\316\363\2455\030n sv\244\232yx8\015\250\342\2754@\321\305\005\340\001=Jr\352\236\334\335\206B\000v_\323`\352\036\000?\325\301\356u\271\213@"\002\242\301/k\200Kd\326\362\227\207\030\000\011pp\312Pf\304\245Hze,\273\0108\356\3052(\234\0143\025\246\016fYAg\235\205\013\212?7\265\003Lb\355liI\216~x\204\266\240\244\377Nvq\360\015\264<\372\260\233I\263\015\216\026\025D\220\203/|J\216K\200|\003z\200H)8\340\311&\024VjO\354!\026(\232\316\206\374\021\301Y;\242\326\263\256uGp\035\016\343\220\360\327\233\300\006\011\177\204\216_\023;@\361Y\320\210\310c\242\273dh:\036X\366.J\324\354\253$(\253\320\346\217\264\025q\354jS\004\253\206%\315\266O\301"o\253\2065\320\366\200\255\227m\356\245\004\324\177\341\031\267)\004\260\233z\357\246\335\201F\367\017\344\235\023j\233\373\335\360\2167\277S\322\355\013\345''\334>X\367\300I\204o>\005\007\010*Z\370.\006k\356\203#\\3\022\257\312\0056\316\361\216{|\343\015\177\251\251\017\336\232\214\373;\344\304\000J\331H\250\357\275d\234\003\345Fy\250<0\354\302\006<\010\332\036w\301e. ?h''\3352\315\270\314\211\001\360d\027\341\345\002\370x\307\377Q>\033!\304%\342\003\227\271\305\027m\223\227\233b\3470\312K\022\240.m\254\337g\352G.\002\3279\344u\367\200=\354b\267z\331\315sv\264\247]\342Ii8\000\332\356\366\267\363{''\024\277P\323\225\020\227\375\216\233%\015]\373gf\362\024\240\013!\347\354\271t\253Mds\276\033^\010\013i\320CR\235\367\343Xd\012}\3078}4\315\003\220`\007\000\215\247\311\343\211\340\015\356\000\036\010\3468\016\341\245\220y\210#\376+\247\037B\352+\263z\314\217~\353\257\317I\354\2150{/\325\236\012\255''B\344\217\342\213)\004\003+\027\200a\027\202o\004R\250D\025[8>Lha\213\333/C\201\357\200>\031(Q\371Xt\342\023\241\260>.\350@T<L\276\015|\000\204\372?A\367u\210_\017\360\217\177\313\345O\377\372c\306\376\370\317\377\374\365\317\177\3753\277\377\000\270\005\377\027\200\027\004\010|\357W\200\010\210\0203\260\200\014\330\200\016\370\200\020\010\201!\000\000;', '2008-02-22 18:41:52.024002', '2012-12-21 19:00:18.847331', true, 2, 16, 16, 3);
INSERT INTO card_tbl VALUES (8, 'VISA', 'GIF89a\264\000s\000\367\000\000\377\240\000@Z\270\200\221\320\277\310\347\337\344\363 ?\254\237\255\333\0201\246`v\304\357\361\371\317\326\3550L\262Ph\276\217\237\325\257\272\341p\203\312\357\230\012\377\320\200\020+\226\377\371\357\377\363\337\377\254 \377\270@\377\347\277\200bP\377\246\020\377\325\217\377\312p`Rd@Bx\377\333\237\257y2\377\276P\377\26200:\202\377\341\257\277\201(PJn\237q<\357\266Z\217iF\237}\\@T\250\000#\240\377\377\377\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000,\000\000\000\000\264\000s\000\000\010\377\000Y\010\034H\260\240\301\203\010\023*\\\310\260\241\303\207\020#J\234H\261\242\305\213\0303j\334\310\261\243\307\217 C\212\034I\262\244\311\223(S\252\\\311\262\245\313\2270c\312\234I\263\246\315\2338s\352\334\311\263\247\317\237@\203\012\035J\264\250\321\243H\223*]\312\264\251\323\247P\243J\235J\265\252\325\253X\263j\335\312\265\253\327\257`\303\212\035K\266\254\331\263h\323\252e\250\300\200\200\000pW\310]\000\367A\203\001D#\000\330\313\267\257\337\277\000,h\2300\361\201\334\303\210\0217 ( \261\343\025\010 :@p\340\261\345\025\001\032\020\230h\200A\201\313r\027w\244\260!\004\340\323\200+P\220\370\031\264\334\004\004\003\270^!\240a\002\001\255g[\306\3730\201l\335\221AR\320`\032\265q\013\021\007\000/\230\3732o\205\015*\353\006\275\371\341o\335\005H\222\316`\374\364\005\210\010t?\377\027\250\273:B\337\323gC4\220\376u\311\011\036*t\367\033\241\267t\320\331\011*W\237P\301\375\366\217\005\000\321u\323\215GR|\363\355\205\234C\354\315f@A\015\202& B\376\001\010Zp\015)`am)E\000\301|\031X7\333\001\26016\033\206\005\021\360\237\205\216q\330Px\0002\260\222\012\037\314\267\032C\004,W\020\201\226\271\270#\213\2409\340P\002+b\267\222a%|\210\332\010\0155\240\233\002\006-0\233\201\002E\010\344cP6\011\244y''9 \227\004$\240V\037C\315Y\266\300AO\036T\246k\007\300\005\327\232r=\004\247nB\246\224\000b(\234\266\240B\032:hP\237\256\035d%~\232\035\244@\003\014\034vfC\373\261\350\343I\004r\000X\210\013\301\010\032\211\006y\351\332\242\005%zb\211\012%`@\00126\344)\213\023\246\324\030b\035(\331\027a\012\025\377\351\330\003\007\255\012Z\251\005\311\232X~\017q\231P\216W\036\260R\243\207\211\340\352^\337%4\250e\276\012t\352e\217\262p\347l\321fd\330\225+4[\322c"\320\247\320\263\226\245Z\220\224\256\325\251\237n\325b\244\253\\\007\220{\331\203*\201+\227\244|\201\220\320\264\256\301kP\232\005\021{Y\001\240j\264lb\010Xj\031\255*9\371\030\006|U\220\220\302\227R\250\333A\376^\266@\300\030\271k\231\003\003#&\356I\200:V\343^\260F9\033\302\006U\354\030\257\004\001\213\035\225\024\205\374Xv2?\306\222\256\022(\231lA5?\246-\013\266^\366\361@\353>\306\300\317\340\271\206\360lY\246dpb\035\3545fA\327\336\232\320\323\217\245\213\365\211H3\204\357eY\362\350\230h)u,\227\011\000\330kP\321r\231k\220\330\216\351\333/\220\010t\375\360\246\003Um\031\212''\271\377\374X\316\016\027\244)~\012\211\227\020\334\272\035\220\256Bs\036F\266\331s\261\324\370\012\014\227\354,\265\277N\374+\333\240-\3204C\203_f^\317\211\261\264\365a9\357,my\011\251\274\353B\241\263(\267B\210\037\206+y\256\301LR\354\216a05\013\220\313u\273AA\207\313P\360\230/\344\267e\263\327\036\332J_\377}\002A\032\357\246\220\336\0073\3129h\263\033\204\275c\302\026T\274c\303\237T\275c)\014\264\374\314\0139\377|C\004\270\237\373\275l\363\315\002\357\257\037\351\232\004y\317F6B\234\323\035B\242\003$\226AhJ\006Y\237c0f\022\374%\2067\223s\317\275t\303\300\205\334\006H\335\023\310\371\362\267\266\371\255\304\177\367;\321B\\\207\230\360I\204\000\247\353\034\305\222\3676\327,n$\356\213L\012\017#@\201@Lh\026A!\200\2325C\271\374l|\211\031\232I\377\200\210\230\213\261\315\200\007\371\336\25402\200\010"\306@\321{\014\247R\306&\226\220\0161\310\313\340\217\\\363\277\212$`\203\217\351"\021\021S\0007\231\021.`D\214\335B\302\2666V\2209\010\314H\002\234H\0338bk6nC\211\274\000d?4\315f\215\017\271!\264\010\202\274;\326q%\202\264\320\347\020BB\304p\244\2214\214\215!g#\304\222\\Q7ST\026%\017\002\310$\362\213\005\012\234\244cZBG\3465d\214\207\351c\233\362\210\243\355\021\244\207\242\\\301"O\002K\313`\212!\362\213\026\276\012 \000@6Q7\270"R,\363\305\222B>\006e\013q"\225*\266\200\0078`\226\004\350Lz\364e\314I"\023%\241\374\243CXw\300\331,\240\224d$\0108\015\231I\224\244\021\207\214\322\234\370\206\231\030}A\222\235+h\211\022\211\331\020\007\026\361 \362\003\222\270`\271\366\000\001\370\363\237\000\015(*\345RC\221\330\323\226\017\031\350\012\312\307\202q\246\347\000\346\311&\026!r\316\025t\361$QT\332C\366\230\230j\301s\005\007\370\234BW\360F\204\364\260\217&\251hb:\311\002\225\262\362\235\026*\300"9\307P\350\340\215%#\025\036D>9\220jN\207\001\030+$+\027rI\011\252\004\246m{HQ\343D\274X\026`\250\3623\341N=\370A#=\344\240\221K e\356\270\000-.\0252\023q\337\013G\302\321\216BD\241\225\024\310\000\336\002\240\000\010`\226\257\344\351C\346i;\264\020`\255\002`\300\031\003\200\000\001\030\240\240k\011\254`\007K\330\302\032\366\260\210M\254b\027\313\330\306:\366\261\220\215\254d''K\331\312Z\366\262\230\315\254f7\313\331\316z\366\263\240\015\255hGK\332\322\232\366\264\250M\255jW\313\332\211\004\004\000;', '2008-02-22 18:41:52.024002', '2012-12-21 19:00:18.847331', true, 3, 13, 19, 3);
INSERT INTO card_tbl VALUES (9, 'VISA Electron', 'GIF89a\264\000s\000\367\000\000@Z\270\377\240\000\277\310\347\200\221\320\357\361\371\0201\246`v\304 ?\2540L\262\237\255\333\317\326\355\257\272\341\337\344\363\217\237\325p\203\312Ph\276\377\347\277\377\325\217\377\304`\377\2620\020+\226\377\254 0:\202\357\230\012\377\355\317\377\371\357\237q<\217iF\377\363\337\377\270@`Rd\377\320\200\317\211\036\377\246\020\377\276P\377\341\257\257\213b 3\214\337\220\024\200bP@Bx\237\217\214pZZ\277\201(\377\333\237\000#\240\377\377\377\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000\000,\000\000\000\000\264\000s\000\000\010\377\000[\010\034(\320\205\301\203\010\023*\\\310\260\241\303\207\020#J\234H\261\242E\027\004\011^\334\310\261\243\307\217 -f\034\030\262\244\311\223(Sb\034\331B\245\313\2270c\032d\331R\246\315\2338+\322\314\311\263\247\317\231,\177\012\035\372r''\321\243H?\032M\312\264\251\304\245N\243JE\010u\252\325\246U\257j%\232u\253\327\236]\277\212\265\031v\254Y\227e\317\2525\231v\255[\217m\337\312\025\031t\256]\245u\357\352\275\030w\357D\006\002\002\023\370\012\025\003\204\303\210\023+^\234\201\243\202\300\220#GN(\271\262\000\005\026\005\014x\200\200f\013\000\006\032`\356\370\3302\344\301\033\241\206\010\300\272\265\353\327\260\003L\030q\361\200\347\201\007\020*\270-\020\300D\001\006\012\360\036y\300\200\200\212\004\006\010\347\335\200cU\014\021&\304\236\016\233E\305\335\303\233\037\\0\334@\304\005\235\207{\377\366=\221@\370\356\316\363&\344\360\241\002u\352!8P4 \036\265\301\001\303\007<d\000@<o\357\023\365\347_n\251\251\267\320\010\035\274\027[\004\024-w\033\200\007=0\334q\015%\340\240\1774\351''\221\000\030\012d\237N\0062\304\201\004\012\272&\302D\011\210G\341A\347y\306@C\370ux\333\212\020I\330!\215\024\365e\020\006\011\226X\301D6\336\206\200B\3425\324\200\214\274\275\030\021\003HjH\327H\025\245`B\211\215A\304\344p\011$\204\335m\344)\304\035\222\267M\024c\207\017\024\010%E\015P\260\201\202\020Dt$o\005|\350\302\227\017.D\300\205`f\324%Dx\212W\200\231\031]''\020\012\027P\367AD\266\375\247\320\230\2369\211\220\003yz\006\341C)\202\251$\210gR\224(\005 Lw\342C[\3366\032B\364\361\206\243\013w\016h\300\000\220-0\200\003\002\016\377\344\250C-v\270\000_!BT\252@\032\304\366\343C\220\3626\244B\261z6\352A\225\302\231eC\004,P\352\255\020\205*\343\254O\345JiF''\304VeC}\216\264lBE*\024\244g\005\034\353Pr\3462\264+\222{N\244#BW\022\344\001lm6D''\271\013\305\353\331\260\011u\233\221v&\021\020iAO\006jQ\242\362\276\306`C\343\322\344\300B\034\362V&\221\023\242\364f\244\351B\364\356\243,\255\331\232\004\314\212w)B\214f\250\220\264\016\243\2040K\007\004\353\331\267\356Z\373\320\275\004\365\312\332\004F\016\327\356A\353\322\004-B\021ci\022\315#9\220l\312\230\032\214\034M\234\266\326P\255,\301\214P\2614e\214!\300\037Q=\222\002(\353Y\260F\027A-\220\005\205\006P\257\226\303\021\270\220\277`+\304\366H\010\234j\221\276,\015;\334\327$]T2A*\260\377\266pB.7\312\220\300\3022\324pw#[\0248K\332i\235\221\334\016m\014\364m+\004\000\262B+\323\224\370AA\217\307\020\321\342\031\260yD\251\336\246\344\342#a\035\221\344Tyf\201l^\0167\361B\027\013\316\220\330\241\313\011\321\321,\315\316\373H\223\256.s\215\267\365\252P\317,\375\254\020\352\3366\324\265\177\005(\017\021\356\003}K7\334I\267mQ\355#Qp\301\331\250\246\355\220\343\217;\364{\207\006\350\0161\234\037\276\335\202\372\015\261~\320\363\003m\360\267\013\347gD-B\356\327\344\020\362\035B\000\374H\245\250\251\231*G\303\343\323mJp9\203Po \003$\334m\324\346\020\356\311H\200\016\271\336Hp\2647\375!0S\0279\\FHp\020\015f$x\224\321\331\206\372\347\231\331-\244\203\004\241\240A@G\020\027j,\201\327\272\215\005\016\302\274\362U(?\345\001\240\177\377\244\206\220\314\025M!&\304\315\007\225v\221$\016\204BF\314\210\014\027%4\212\010\200|\274\231\242A\362G\220\321E\261\2131\003am\2303\247\354<\004\213O\274\310\025\221DD\027\240\261\005\374J\210\010\011"\275\310\341\360!B\034\310\303\362\010\301\207|\221 \003\204\210\000\036\010<$V\221\212\267\331\037C\344\207\020\032\016\004\000\245\223\324\015o\363''\2174\200\205p<\336p\340\3279\226\354,~w<\027\226\206\223\261\311\361\346\223\026Q\000&\023\022I\232\024\000\000\260\214e,\011YI\341\2111lY,\\\016o\3630\220\320\217\211.\260\340\300XRJ\212\335Rq\303lcB`H\020\325u\204\217y;\310\037\207Y\275j\035\263"\235\224Q\234 \362\306\026@No\303\251\245\013\262I\315#\332\022\230\200B\022\012\027B\310\367\235l#s$\310\236\342Y\316\336X\023\235\027\351\246\213\316\351\377\312\205\000\340\000\011\010\244B\310iN\0278\261\236\004\233$>\267\227''T\242\355\224\013IT\001\034PL\204XH<?c&B\005R\321\2030\362\241l\374\016z\214\211\233\320\010@N\012h@;\005"NLR\323\231\013\371h\277\220$N\207hT \324"h9\235\304\305\215\012d\235$\325^G\350\2114\342\0350!=\035\3306\035\350S!\3613\232\226D\322\350\3309\234\315\3354R?\373\245\254\006\300\325\256z\365\253wS\250P9r\320\22184\250]\321''\222zi\020h\266\000\250\267;\252\035\2579F\014\325\221!e\375\3444\3634)\011\336\346\256\015\351\241\254\304\012U\217\270U\211\033\032)\270|\312V\203\0103#5\335\235\304\010\233\320\217$\225 \212|!\020S\210\320\350an8\215\215\210V\265\270X\272Z\304\257I\232\210[\353\350Hm\016@w\255\035HG\327\306\033\201\377\312T!+\265\341\031I\271\020g\271\2248\257eH7I\273\333\277>\344\266\011i\200,\227+\313o\372\223\271\314}\210f\036\260W\226\002`\000\025%\000t\231\013\323\335m7\226\312\364h(\355\362\230\004|u\000\011\020\300T\305\202\\\277\270\245\275\356U\013|\343k\226\371\322\227\275\343\275\357]\354\253\337\255\360\267\277W\371/\200\247"\340\001G\245\300\006\306\012K\326\233`\273\320$\274\015\236\013M\316\032\341\267x\306\271\025^\213g\016 \320\014\327w_\014\366\360X\3404\200\020\213\330\277\3429\300wW\314\342\026\273\370\3050\216\261\214gL\343\357\002\245\2518\316\261\216w\334\241\033\363\370\307@\016\262O},\344"\033\371\310aE\262\222\227\314d"3\231\232\013\250\314\233\214#\314\006X\346\204\013P\000\001\004\220\200D\305\3154\300\021\010d\314\033\034\226Y\331\240\013\320\232q~ge\3349\371\377\311\003[H\245\232\003\272Sm\251\000r\353Of]\020#\205\230''#\241\335bF\240E5\314\220\357\315p\006\023y\226\233\250\343t\320 \260\232%K1\223\034\004\024\0073\235Q1,{)K\333,\372\001\015\030\214_5\004\236\026\034\000Z\201\033M''\021]\223D\227\263L\250\355\343\341\010\024\305[\375\031\263#\321O6\275\023/\362\204\207<\213\273\225\276\014(\220\334\304:o\256\246\246\256\215\0025\362x\322 \372\244s\256\307\0116\001\035''\233\303\012\217\261\263\0244\337\350\224\325\311\366\317\255\204\351\354i\223\223\333\376q\364\006\371<\220\211\011\207@\2163\210\215\274\015o\234\006\3233\340\0167o5\303\325X\361\232q\006\355\352\256\006sXw\036\0163\220*@\372\360\367\323\360\261D\335-\320\317\221\262\364\245,\0116\337\372\016\3232\007KN\034E\214\326~b*>Uio\202\016\306FY\377\302\017\310\217\203E\214g\234e\367\351j\254 \236\221\301\230\227\253\245*\267\012WB\2208\272@\001\353:\2160+im7\012\344V)\032\314\037]\376r\257\361<#\230A^%\233\315\364\177g\004\326\336\374y\371\2049\261>&\312\327\340fz\3239n\024\255\351\274\347"\347\315\262=\310!\362\254k\355\345\373R%\011r\234\027\351\264\325c\307P\226\316g\267#>o0W\315\372\336\262\364\246\272\023\004@xZ\264=;\327\245\236\212=\3574\257\241\303\331\376`T\025\013\000\026|\021\362 >\2541m\363^\220,\243@\036F3\012i\364\361c\037\014`Ze\357-C\246Tu\227\314@\226\352\252\006\330\375LZ\203v5#\331K\006p\025Z$\267w\311\310CO\324\277<\262\324n\001\021c4\272\236\343\210\001\015\353{\014\015\2020\0021\312\001r\272$\335]p\270\343<\320\370\031\177%>s\303\203\000\346~}\374ps@\277Y"\376\221\264_\236PS8\316\215X\376>\251\2307\340\317\273\376\217\234\377\375\373\037\310\375\367\177\002\230c\0018\200\006\210P\005x\200\012\030g.\020\020\000;', '2008-02-22 18:41:52.024002', '2012-12-21 19:00:18.847331', true, 4, 13, 19, 3);


--
-- TOC entry 3182 (class 0 OID 0)
-- Dependencies: 218
-- Name: card_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('card_tbl_id_seq', 14, true);


--
-- TOC entry 3171 (class 0 OID 13042031)
-- Dependencies: 246
-- Data for Name: cardprefix_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO cardprefix_tbl VALUES (1, 2, 5019, 5019, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (2, 2, 4571, 4571, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (3, 5, 3528, 3589, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (4, 9, 402620, 402620, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (5, 9, 405245, 405245, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (6, 9, 405266, 405277, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (7, 9, 405832, 405839, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (8, 9, 405860, 405860, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (9, 9, 405864, 405864, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (10, 9, 405934, 405934, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (11, 9, 406432, 406438, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (12, 9, 406443, 406443, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (13, 9, 417500, 417527, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (14, 9, 417529, 417536, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (15, 9, 417538, 417548, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (16, 9, 417550, 417557, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (17, 9, 417559, 417599, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (18, 9, 417600, 417603, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (19, 9, 426069, 426072, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (20, 8, 4000, 4999, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (21, 8, 413222, 413222, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (22, 8, 428188, 428188, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (23, 8, 44844815, 44844815, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (24, 8, 475162, 475162, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (25, 8, 48573815, 48573815, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (26, 8, 489499, 489499, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (27, 7, 512733, 512733, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (28, 7, 515588, 515588, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (29, 7, 515613, 515622, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (30, 7, 515623, 515623, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (31, 7, 517947, 517947, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (32, 7, 519192, 519192, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (33, 7, 520368, 520368, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (34, 7, 520963, 520973, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (35, 7, 520974, 520974, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (36, 7, 520975, 520976, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (37, 7, 521033, 521033, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (38, 7, 521035, 521035, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (39, 7, 521036, 521036, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (40, 7, 521319, 521319, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (41, 7, 521349, 521349, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (42, 7, 521351, 521351, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (43, 7, 521352, 521352, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (44, 7, 521353, 521353, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (45, 7, 521733, 521733, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (46, 7, 521785, 521785, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (47, 7, 521786, 521786, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (48, 7, 521838, 521838, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (49, 7, 524730, 524730, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (50, 7, 524882, 524882, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (51, 7, 525750, 525750, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (52, 7, 525769, 525769, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (53, 7, 526045, 526045, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (54, 7, 526333, 526334, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (55, 7, 529798, 529798, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (56, 7, 533615, 533615, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (57, 7, 533617, 533617, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (58, 7, 534118, 534118, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (59, 7, 540287, 540287, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (60, 7, 540679, 540679, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (61, 7, 540958, 540958, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (62, 7, 541226, 541226, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (63, 7, 541274, 541274, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (64, 7, 541280, 541280, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (65, 7, 541303, 541303, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (66, 7, 541339, 541339, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (67, 7, 541373, 541373, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (68, 7, 541378, 541378, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (69, 7, 541398, 541398, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (70, 7, 541502, 541502, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (71, 7, 541546, 541546, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (72, 7, 541582, 541582, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (73, 7, 541595, 541595, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (74, 7, 541624, 541624, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (75, 7, 541648, 541648, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (76, 7, 542203, 542203, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (77, 7, 542612, 542612, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (78, 7, 542908, 542908, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (79, 7, 542931, 542931, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (80, 7, 544057, 544057, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (81, 7, 545114, 545114, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (82, 7, 545139, 545139, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (83, 7, 545162, 545162, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (84, 7, 545585, 545585, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (85, 7, 545857, 545857, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (86, 7, 545956, 545956, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (87, 7, 545996, 545996, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (88, 7, 546310, 546311, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (89, 7, 546322, 546322, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (90, 7, 547131, 547133, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (91, 7, 547499, 547499, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (92, 7, 547501, 547501, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (93, 7, 547505, 547505, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (94, 7, 547506, 547506, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (95, 7, 547509, 547509, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (96, 7, 547510, 547510, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (97, 7, 547512, 547512, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (98, 7, 548345, 548345, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (99, 7, 550028, 550028, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (100, 7, 552062, 552062, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (101, 7, 552203, 552203, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (102, 7, 552518, 552518, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (103, 7, 552569, 552569, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (104, 7, 552732, 552732, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (105, 7, 552753, 552753, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (106, 7, 552891, 552891, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (107, 7, 552954, 552955, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (108, 7, 552970, 552972, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (109, 7, 552980, 552994, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (110, 7, 553472, 553472, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (111, 7, 556000, 556003, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (112, 7, 556006, 556013, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (113, 7, 557032, 557032, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (114, 7, 557066, 557068, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (115, 7, 557111, 557111, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (116, 7, 557892, 557893, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (117, 7, 557962, 557962, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (118, 7, 558425, 558425, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (119, 7, 558429, 558429, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (120, 7, 558441, 558443, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (121, 7, 558615, 558615, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (122, 7, 558624, 558624, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (123, 7, 558631, 558631, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (124, 7, 559046, 559046, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (125, 7, 517006, 517039, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (126, 7, 517043, 517043, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (127, 7, 535939, 535939, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (128, 7, 549774, 549774, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (129, 7, 549775, 549775, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (130, 7, 552748, 552748, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (131, 7, 552957, 552957, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (132, 7, 552961, 552961, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (133, 7, 553052, 553052, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (134, 7, 553063, 553064, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (135, 7, 553109, 553109, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (136, 7, 558253, 558253, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (137, 7, 558267, 558267, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (138, 7, 558281, 558281, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (139, 7, 558295, 558295, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (140, 7, 558304, 558304, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (141, 7, 558312, 558317, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (142, 7, 5100, 5599, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (143, 6, 67683430, 67683430, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (144, 6, 676927, 676927, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (145, 6, 676928, 676928, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (146, 6, 50, 50, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (147, 6, 56, 56, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (148, 6, 57, 57, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (149, 6, 58, 58, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (150, 6, 60, 69, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (151, 3, 361480, 361480, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (152, 3, 361484, 361485, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (153, 3, 36, 36, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (154, 3, 60110000, 60110999, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (155, 3, 60112000, 60114999, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (156, 3, 60117400, 60117499, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (157, 3, 60117700, 60117999, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (158, 3, 60118600, 60119999, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (159, 3, 64400000, 65999999, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (160, 1, 3747, 3747, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (161, 1, 376658, 376658, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (162, 1, 34, 34, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);
INSERT INTO cardprefix_tbl VALUES (163, 1, 37, 37, '2012-12-21 19:00:18.847331', '2012-12-21 19:00:18.847331', true);


--
-- TOC entry 3183 (class 0 OID 0)
-- Dependencies: 245
-- Name: cardprefix_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('cardprefix_tbl_id_seq', 163, true);


--
-- TOC entry 3146 (class 0 OID 10765935)
-- Dependencies: 221
-- Data for Name: country_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO country_tbl VALUES (0, 'System Record', NULL, NULL, NULL, NULL, NULL, NULL, false, '2008-02-22 18:41:52.024002', '2009-08-19 22:19:54.041441', false, false, NULL, NULL, NULL, 100, 22500, 22500, 100000);
INSERT INTO country_tbl VALUES (100, 'Denmark', 'DKK', '10000000', '99999999', '1230', '{PRICE} {CURRENCY}', 2, false, '2008-02-22 18:41:59.280496', '2014-07-29 12:48:55.300886', true, false, 950000, 1000, '', 100, 0, 0, 0);
INSERT INTO country_tbl VALUES (101, 'Sweden', 'SEK', '10000000', '999999999', '72790', '{PRICE} {CURRENCY}', 2, false, '2009-01-21 02:05:01.695162', '2010-11-24 18:41:14.142081', true, false, NULL, 1000, 'kr.', 100, 22500, 22500, 100000);
INSERT INTO country_tbl VALUES (610, 'Pakistan', 'Rs.', '1000000000', '9999999999', '123', '{CURRENCY}{PRICE}', 2, false, '2013-03-27 14:16:39.206607', '2013-03-27 14:16:39.206607', true, false, NULL, 500, 'Rs.', 100, 200, 300, 400);
INSERT INTO country_tbl VALUES (102, 'Norway', 'NOK', '1000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (106, 'Israel', 'ILS', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (109, 'Switzerland', 'CHW', '100000000', '999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (112, 'Poland', 'PLN', '100000000', '999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (116, 'Afghanistan', 'AFN', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (117, 'Albania', 'ALL', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (119, 'Armenia', 'AMD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (120, 'Belarus', 'BYR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (121, 'Bosnia and Herzegovina', 'BAM', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (122, 'Bulgaria', 'BGN', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (123, 'Croatia', 'HRK', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (125, 'Czech Republic', 'CZK', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (127, 'Faroe Islands', 'DKK', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (129, 'Gibraltar', 'GIP', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (130, 'Greenland', 'DKK', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (131, 'Hungary', 'HUF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (132, 'Iceland', 'ISK', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (135, 'Latvia', 'LVL', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (136, 'Liechtenstein', 'CHF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (137, 'Lithuania', 'LTL', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (140, 'Moldova', 'MDL', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (143, 'Republic of Macedonia', 'MKD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (144, 'Monserrat', 'XCD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (146, 'Palestinian Territory', 'NIS', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (149, 'Romania', 'RON', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (150, 'Republic of Serbia', 'RSD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (154, 'Turkey', 'TRY', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (155, 'Ukraine', 'UAH', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (201, 'Mexico', 'MXV', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (202, 'Canada', 'CAD', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (203, 'Anguilla', 'XCD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (204, 'Antigua and Barbuda', 'XCD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (205, 'Barbados', 'BBD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (207, 'Cayman Islands', 'KYD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (208, 'Cuba', 'CUP', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (209, 'Dominican Republic', 'DOP', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (212, 'Jamaica', 'JMD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (214, 'Bermuda', 'BMD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (215, 'Bahamas', 'BSD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (300, 'Algeria', 'DZD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (301, 'Angola', 'AOA', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (302, 'Bangladesh', 'BDT', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (303, 'Benin', 'XOF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (304, 'Bolivia', 'BOV', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (305, 'Botswana', 'BWP', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (306, 'Burkina Faso', 'XOF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (307, 'Burundi', 'BIF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (308, 'Cameroon', 'XAF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (309, 'Cape Verde', 'CVE', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (310, 'Central African Republic', 'XAF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (311, 'Chad', 'XAF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (312, 'Comoros', 'KMF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (313, 'Congo', 'XAF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (314, 'Côte d''Ivoire', 'XOF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (315, 'Democratic Republic of the Congo', 'CDF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (316, 'Djibouti', 'DJF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (317, 'Egypt', 'EGP', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (318, 'Equatorial Guinea', 'XAF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (319, 'Ethiopia', 'ETB', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (320, 'Gabon', 'XAF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (321, 'Gambia', 'GMD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (322, 'Ghana', 'GHS', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (323, 'Guinea', 'GNF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (324, 'Guinea-Bissau', 'XOF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (325, 'Kenya', 'KES', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (326, 'Lesotho', 'LSL', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (327, 'Liberia', 'LRD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (328, 'Madagascar', 'MGA', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (329, 'Malawi', 'MWK', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (330, 'Mali', 'XOF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (331, 'Mauritania', 'MRO', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (332, 'Mauritius', 'MUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (333, 'Morocco', 'MAD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (334, 'Mozambique', 'MZN', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (336, 'Western Sahara', 'MAD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (337, 'Eritrea', 'ERN', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (338, 'Libyan Arab Jamahiriya', 'LYD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (340, 'Namibia', 'NAD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (341, 'Niger', 'XOF', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (342, 'Nigeria', 'NGN', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (344, 'Rwanda', 'RWF', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (345, 'Seychelles', 'SCR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (346, 'Sudan', 'SDG', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (347, 'St. Helena', 'SHP', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (348, 'Sierra Leone', 'SLL', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (349, 'Sao Tome and Principe', 'STD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (350, 'Swaziland', 'SZL', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (351, 'Togo', 'XOF', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (352, 'Tunisia', 'TND', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (353, 'Tanzania', 'TZS', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (354, 'Uganda', 'UGX', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (355, 'South Africa', 'ZAR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (356, 'Zambia', 'ZMW', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (357, 'Zaire', 'XAF', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (400, 'Argentina', 'ARS', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (401, 'Aruba', 'AWG', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (402, 'Belize', 'BZD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (403, 'Brazil', 'BRL', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (404, 'Chile', 'CLP', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (405, 'Colombia', 'COU', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (406, 'Costa Rica', 'CRC', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (410, 'Guatemala', 'GTQ', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (411, 'Guyana', 'GYD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (412, 'Honduras', 'HNL', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (413, 'Antarctica', 'AQD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (414, 'Carriacou', 'XCD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (415, 'Netherlands Antilles', 'CMG', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (416, 'Bouvet Island', 'NOK', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (417, 'Scott Base', 'NZD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (418, 'Dominica', 'XCD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (419, 'Falkland Islands', 'FKP', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (420, 'Grenada', 'XCD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (424, 'St. Christopher (St. Kitts) Nevis', 'XCD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (425, 'St. Lucia', 'XCD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (427, 'Nicaragua', 'NIO', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (429, 'Peru', 'PEN', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (430, 'Pitcairn Island', 'NZD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (432, 'Paraguay', 'PYG', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (433, 'Senegal', 'XOF', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (434, 'Somalia', 'SOS', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (435, 'Suriname', 'SRD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (436, 'St. Maarten', 'ANG', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (438, 'Trinidad and Tobago', 'TTD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (439, 'Uruguay', 'UYU', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (440, 'St. Vincent and The Grenadines', 'XCD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (441, 'Venezuela', 'VEF', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (500, 'Australia', 'AUD', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (501, 'Brunei Darussalam', 'BND', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (502, 'Cook Islands', 'NZD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (503, 'Fiji', 'FJD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (504, 'French Polynesia', 'XPF', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (505, 'Indonesia', 'IDR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (507, 'Cocos (Keeling) Islands', 'AUD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (508, 'Christmas Island', 'AUD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (509, 'New Caledonia', 'XPF', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (510, 'Norfolk Island', 'AUD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (511, 'Nauru', 'AUD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (512, 'Niue', 'NZD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (513, 'New Zealand', 'NZD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (514, 'Solomon Islands', 'SBD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (515, 'Tonga', 'TOP', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (516, 'Tuvalu', 'AUD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (517, 'Vanuatu', 'VUV', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (518, 'Wallis and Futuna Islands', 'XPF', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (519, 'Samoa', 'WST', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (600, 'Abu Dhabi', 'AED', '100000000', '999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (601, 'Bahrain', 'BHD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (602, 'Dubai', 'AED', '100000000', '999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (603, 'India', 'INR', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (604, 'Kuwait', 'KWD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (605, 'Oman', 'OMR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (606, 'Quatar', 'QAR', '1000000', '9999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (607, 'Russia', 'RUB', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (608, 'Saudi-Arabia', 'SAR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (609, 'China', 'CNY', '10000000000', '99999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (611, 'Azerbaijan', 'AZN', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (612, 'Bhutan', 'BTN', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (613, 'Cambodia', 'KHR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (614, 'Hong Kong', 'HKD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (615, 'Iran', 'IRR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (616, 'Japan', 'JPY', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (617, 'Jordan', 'JOD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (618, 'Kazakhstan', 'KZT', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (619, 'Kyrgyzstan', 'KGS', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (620, 'Lao P.D.R.', 'LAK', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (621, 'Lebanon', 'LBP', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (622, 'Macao', 'MOP', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (623, 'Maldives', 'MVR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (624, 'Mongolia', 'MNT', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (625, 'Burma', 'MMK', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (628, 'Iraq', 'IQD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (630, 'Kiribati', 'AUD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (631, 'North Korea', 'KPW', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (632, 'South Korea', 'KRW', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (633, 'Kazakstan', 'KZT', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (634, 'Sri Lanka', 'LKR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (636, 'Macau', 'MOP', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (638, 'Malaysia', 'MYR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (639, 'Nepal', 'NPR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (640, 'Philippines', 'PHP', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (642, 'Singapore', 'SGD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (643, 'Syrian Arab Republic', 'SYP', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (644, 'Thailand', 'THB', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (645, 'Turkmenistan', 'TMT', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (646, 'Taiwan', 'TWD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (647, 'United Arab Emirates', 'AED', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (648, 'Uzbekistan', 'UZS', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (649, 'Vietnam', 'VND', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (650, 'Yemen', 'YER', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (104, 'Finland', 'EUR', '10000000', '999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (105, 'Greece', 'EUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (107, 'Italy', 'EUR', '1000000000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (108, 'France', 'EUR', '100000000', '999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (110, 'Netherlands', 'EUR', '100000000', '999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (111, 'Belgium', 'EUR', '100000000', '999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (113, 'Spain', 'EUR', '100000000', '999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (114, 'Austria', 'EUR', '1000', '99999999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (115, 'Germany', 'EUR', '1000000000', '99999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (118, 'Andorra', 'EUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (124, 'Cyprus', 'EUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (126, 'Estonia', 'EUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (133, 'Ireland', 'EUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (138, 'Luxembourg', 'EUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (139, 'Malta', 'EUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (141, 'Monaco', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (142, 'Montenegro', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (145, 'St. Pierre and Miquelon', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (147, 'Portugal', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (148, 'Kosovo', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (151, 'San Marino', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (152, 'Slovakia', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (153, 'Slovenia', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (156, 'Vatican City State', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (210, 'Guadeloupe', 'EUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (339, 'Mayotte', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (343, 'Reunion', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (409, 'French Departments and Territories in the Indian Ocean', 'EUR', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (421, 'French Guiana', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (426, 'Martinique', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (629, 'Kerguelen Archipelago', 'EUR', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '€', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (200, 'USA', 'USD', '1000000000', '9999999999', '30100', '{CURRENCY}{PRICE}', 2, false, '2008-02-22 18:41:59.280496', '2013-11-04 13:27:37.01545', true, true, NULL, 500, '$', 50, 3000, 3000, 20000);
INSERT INTO country_tbl VALUES (206, 'British Virgin Islands', 'USD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (211, 'Haiti', 'USD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (213, 'American Samoa', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (216, 'Johnston Island', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (217, 'Midway Island', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (218, 'United States Minor Outlying Islands', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (358, 'Zimbabwe', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (407, 'Ecuador', 'USD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (408, 'El Salvador', 'USD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (423, 'British International Ocean Territory', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (428, 'Panama', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (431, 'Puerto Rico', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (437, 'Turks and Caicos Islands', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (442, 'Virgin Islands, United States', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (506, 'Micronesia', 'USD', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (626, 'Diego Garcia', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (627, 'Guam', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (635, 'Marshall Islands', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (637, 'Northern Mariana Islands', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (641, 'Palau', 'USD', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true, false, NULL, NULL, '$', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (128, 'Georgia', 'GBP', '10000000', '99999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-08 09:52:25.06161', true, false, NULL, NULL, '£', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (134, 'Isle of Man', 'GBP', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-08 09:52:25.06161', true, false, NULL, NULL, '£', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (335, 'Tristan Da Cunha', 'GBP', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-08 09:52:25.06161', true, false, NULL, NULL, '£', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (422, 'South Georgia And IS', 'GBP', '1000', '9999999999', '123', '', 0, false, '2013-11-04 13:27:37.01545', '2013-11-08 09:52:25.06161', true, false, NULL, NULL, '£', NULL, NULL, NULL, NULL);
INSERT INTO country_tbl VALUES (103, 'UK', 'GBP', '1000000000', '9999999999', '123', '{CURRENCY}{PRICE}', 2, false, '2010-10-15 10:12:52.514519', '2014-01-21 14:31:17.011175', true, false, NULL, 500, '£', 50, 0, 0, 0);


--
-- TOC entry 3158 (class 0 OID 10765985)
-- Dependencies: 233
-- Data for Name: pricepoint_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO pricepoint_tbl VALUES (1, 100, 0, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (2, 100, 100, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (3, 100, 150, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (4, 100, 200, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (5, 100, 250, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (6, 100, 300, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (7, 100, 350, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (8, 100, 400, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (9, 100, 450, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (10, 100, 500, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (11, 100, 550, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (12, 100, 600, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (13, 100, 650, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (14, 100, 700, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (15, 100, 750, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (16, 100, 800, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (17, 100, 850, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (18, 100, 900, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (19, 100, 950, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (20, 100, 1000, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (21, 100, 1100, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (22, 100, 1200, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (23, 100, 1300, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (24, 100, 1400, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (25, 100, 1500, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (26, 100, 1600, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (27, 100, 1700, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (28, 100, 1800, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (29, 100, 1900, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (30, 100, 2000, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (31, 100, 2100, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (32, 100, 2200, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (33, 100, 2300, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (34, 100, 2400, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (35, 100, 2500, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (36, 100, 2600, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (37, 100, 2700, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (38, 100, 2800, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (39, 100, 2900, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (40, 100, 3000, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (41, 100, 3500, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (42, 100, 3900, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (43, 100, 4000, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (44, 100, 4500, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (45, 100, 4900, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (46, 100, 5000, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (47, 100, 5500, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (48, 100, 5900, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (49, 100, 6000, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (50, 100, 6500, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (51, 100, 6900, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (52, 100, 7000, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (53, 100, 7500, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (54, 200, 0, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (55, 200, 30, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (56, 200, 50, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (57, 200, 99, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (58, 200, 100, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (59, 200, 199, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (60, 200, 249, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (61, 200, 299, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (62, 200, 399, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (63, 200, 499, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (64, 200, 599, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (65, 200, 699, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (66, 200, 799, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (67, 200, 899, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (68, 200, 999, '2008-03-24 19:08:58.071781', '2008-10-07 13:00:59.577181', true);
INSERT INTO pricepoint_tbl VALUES (0, 0, 0, '2008-03-24 19:08:58.071781', '2008-10-07 13:13:41.427839', true);
INSERT INTO pricepoint_tbl VALUES (-200, 200, -1, '2008-03-24 19:08:58.071781', '2009-02-16 05:24:02.871644', true);
INSERT INTO pricepoint_tbl VALUES (-100, 100, -1, '2008-03-24 19:08:58.071781', '2009-02-16 05:24:05.336785', true);
INSERT INTO pricepoint_tbl VALUES (-101, 101, -1, '2009-02-16 05:24:26.393163', '2009-02-16 05:24:26.393163', true);
INSERT INTO pricepoint_tbl VALUES (-103, 103, -1, '2010-10-15 10:12:52.514519', '2010-10-15 10:12:52.514519', true);
INSERT INTO pricepoint_tbl VALUES (-102, 102, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-104, 104, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-105, 105, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-106, 106, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-107, 107, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-108, 108, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-109, 109, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-110, 110, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-111, 111, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-112, 112, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-113, 113, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-114, 114, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-115, 115, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-116, 116, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-117, 117, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-118, 118, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-119, 119, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-120, 120, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-121, 121, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-122, 122, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-123, 123, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-124, 124, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-125, 125, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-126, 126, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-127, 127, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-128, 128, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-129, 129, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-130, 130, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-131, 131, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-132, 132, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-133, 133, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-134, 134, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-135, 135, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-136, 136, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-137, 137, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-138, 138, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-139, 139, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-140, 140, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-141, 141, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-142, 142, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-143, 143, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-144, 144, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-145, 145, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-146, 146, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-147, 147, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-148, 148, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-149, 149, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-150, 150, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-151, 151, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-152, 152, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-153, 153, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-154, 154, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-155, 155, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-156, 156, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-201, 201, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-202, 202, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-203, 203, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-204, 204, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-205, 205, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-206, 206, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-207, 207, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-208, 208, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-209, 209, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-210, 210, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-211, 211, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-212, 212, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-213, 213, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-214, 214, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-215, 215, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-216, 216, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-217, 217, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-218, 218, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-300, 300, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-301, 301, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-302, 302, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-303, 303, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-304, 304, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-305, 305, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-306, 306, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-307, 307, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-308, 308, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-309, 309, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-310, 310, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-311, 311, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-312, 312, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-313, 313, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-314, 314, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-315, 315, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-316, 316, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-317, 317, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-318, 318, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-319, 319, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-320, 320, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-321, 321, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-322, 322, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-323, 323, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-324, 324, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-325, 325, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-326, 326, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-327, 327, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-328, 328, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-329, 329, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-330, 330, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-331, 331, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-332, 332, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-333, 333, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-334, 334, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-335, 335, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-336, 336, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-337, 337, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-338, 338, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-339, 339, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-340, 340, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-341, 341, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-342, 342, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-343, 343, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-344, 344, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-345, 345, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-346, 346, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-347, 347, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-348, 348, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-349, 349, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-350, 350, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-351, 351, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-352, 352, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-353, 353, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-354, 354, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-355, 355, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-356, 356, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-357, 357, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-358, 358, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-400, 400, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-401, 401, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-402, 402, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-403, 403, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-404, 404, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-405, 405, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-406, 406, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-407, 407, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-408, 408, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-409, 409, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-410, 410, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-411, 411, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-412, 412, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-413, 413, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-414, 414, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-415, 415, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-416, 416, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-417, 417, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-418, 418, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-419, 419, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-420, 420, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-421, 421, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-422, 422, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-423, 423, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-424, 424, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-425, 425, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-426, 426, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-427, 427, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-428, 428, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-429, 429, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-430, 430, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-431, 431, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-432, 432, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-433, 433, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-434, 434, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-435, 435, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-436, 436, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-437, 437, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-438, 438, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-439, 439, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-440, 440, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-441, 441, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-442, 442, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-500, 500, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-501, 501, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-502, 502, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-503, 503, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-504, 504, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-505, 505, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-506, 506, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-507, 507, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-508, 508, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-509, 509, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-510, 510, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-511, 511, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-512, 512, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-513, 513, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-514, 514, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-515, 515, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-516, 516, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-517, 517, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-518, 518, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-519, 519, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-600, 600, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-601, 601, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-602, 602, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-603, 603, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-604, 604, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-605, 605, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-606, 606, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-607, 607, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-608, 608, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-609, 609, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-611, 611, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-612, 612, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-613, 613, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-614, 614, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-615, 615, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-616, 616, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-617, 617, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-618, 618, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-619, 619, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-620, 620, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-621, 621, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-622, 622, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-623, 623, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-624, 624, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-625, 625, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-626, 626, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-627, 627, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-628, 628, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-629, 629, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-630, 630, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-631, 631, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-632, 632, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-633, 633, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-634, 634, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-635, 635, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-636, 636, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-637, 637, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-638, 638, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-639, 639, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-640, 640, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-641, 641, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-642, 642, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-643, 643, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-644, 644, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-645, 645, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-646, 646, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-647, 647, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-648, 648, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-649, 649, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pricepoint_tbl VALUES (-650, 650, -1, '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);


--
-- TOC entry 3144 (class 0 OID 10765927)
-- Dependencies: 219
-- Data for Name: cardpricing_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

/*
INSERT INTO cardpricing_tbl VALUES (0, 0, 0, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', false);
INSERT INTO cardpricing_tbl VALUES (21, 1, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (22, 2, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (23, 3, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (24, 4, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (25, 5, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (26, 6, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (27, 7, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (28, 8, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (29, 9, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (30, 10, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (31, 11, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (32, 12, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (33, 13, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (34, 14, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (35, 15, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (36, 16, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (37, 17, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (38, 18, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (39, 19, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (40, 20, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (41, 21, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (42, 22, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (43, 23, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (44, 24, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (45, 25, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (46, 26, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (47, 27, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (48, 28, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (49, 29, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO cardpricing_tbl VALUES (50, 30, 10, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
*/


--
-- TOC entry 3184 (class 0 OID 0)
-- Dependencies: 220
-- Name: cardpricing_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('cardpricing_tbl_id_seq', 5503, true);


--
-- TOC entry 3185 (class 0 OID 0)
-- Dependencies: 222
-- Name: country_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('country_tbl_id_seq', 1, false);


--
-- TOC entry 3148 (class 0 OID 10765945)
-- Dependencies: 223
-- Data for Name: depositoption_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO depositoption_tbl VALUES (0, 0, 0, '2009-07-21 12:00:16.579755', '2009-07-21 12:00:16.579755', false);
INSERT INTO depositoption_tbl VALUES (1, 100, 5000, '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO depositoption_tbl VALUES (2, 100, 10000, '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO depositoption_tbl VALUES (3, 100, 20000, '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO depositoption_tbl VALUES (4, 100, 50000, '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO depositoption_tbl VALUES (5, 100, 100000, '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);


--
-- TOC entry 3186 (class 0 OID 0)
-- Dependencies: 224
-- Name: depositoption_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('depositoption_tbl_id_seq', 5, true);


--
-- TOC entry 3152 (class 0 OID 10765961)
-- Dependencies: 227
-- Data for Name: feetype_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO feetype_tbl VALUES (0, 'System Record', '2009-08-02 13:06:01.285267', '2009-08-02 13:06:01.285267', false);
INSERT INTO feetype_tbl VALUES (1, 'Top-Up', '2009-08-02 13:06:01.285267', '2009-08-02 13:06:01.285267', true);
INSERT INTO feetype_tbl VALUES (2, 'Transfer', '2009-08-02 13:06:01.285267', '2009-08-02 13:06:01.285267', true);
INSERT INTO feetype_tbl VALUES (3, 'Withdrawal', '2009-08-02 13:06:01.285267', '2009-08-02 13:06:01.285267', true);


--
-- TOC entry 3150 (class 0 OID 10765953)
-- Dependencies: 225
-- Data for Name: fee_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO fee_tbl VALUES (0, 0, 0, 0, NULL, NULL, NULL, '2009-08-02 13:06:01.285267', '2009-08-02 13:06:01.285267', false);
INSERT INTO fee_tbl VALUES (1, 1, 100, 100, 100, 50, 0.0100000000000000002, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (2, 1, 101, 101, 100, 50, 0.0100000000000000002, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (3, 1, 200, 200, 100, 50, 0.0100000000000000002, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (4, 2, 100, 100, 0, 0, 0, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (5, 2, 100, 101, 500, 250, 0.0149999999999999994, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (6, 2, 100, 200, 500, 250, 0.0149999999999999994, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (7, 2, 101, 101, 0, 0, 0, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (8, 2, 101, 100, 500, 250, 0.0149999999999999994, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (9, 2, 101, 200, 500, 250, 0.0149999999999999994, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (10, 2, 200, 200, 300, 200, 0.0100000000000000002, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (11, 2, 200, 100, 500, 250, 0.0149999999999999994, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (12, 2, 200, 101, 500, 250, 0.0149999999999999994, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (13, 3, 100, 100, 100, 50, 0.0100000000000000002, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (14, 3, 101, 101, 100, 50, 0.0100000000000000002, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);
INSERT INTO fee_tbl VALUES (15, 3, 200, 200, 100, 50, 0.0100000000000000002, '2009-08-02 13:06:15.898172', '2009-08-02 13:06:15.898172', true);


--
-- TOC entry 3187 (class 0 OID 0)
-- Dependencies: 226
-- Name: fee_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('fee_tbl_id_seq', 15, true);


--
-- TOC entry 3188 (class 0 OID 0)
-- Dependencies: 228
-- Name: feetype_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('feetype_tbl_id_seq', 1, false);


--
-- TOC entry 3154 (class 0 OID 10765969)
-- Dependencies: 229
-- Data for Name: flow_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO flow_tbl VALUES (0, 'System Record', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', false);
INSERT INTO flow_tbl VALUES (1, 'Electronic', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', true);
INSERT INTO flow_tbl VALUES (2, 'Physical', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', true);


--
-- TOC entry 3189 (class 0 OID 0)
-- Dependencies: 230
-- Name: flow_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('flow_tbl_id_seq', 2, true);


--
-- TOC entry 3156 (class 0 OID 10765977)
-- Dependencies: 231
-- Data for Name: iprange_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO iprange_tbl VALUES (0, 0, NULL, NULL, 'System Record', '2009-07-21 12:00:16.579755', '2009-07-21 12:00:16.579755', false);
INSERT INTO iprange_tbl VALUES (1, 0, 3642070112, 3642070127, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (2, 0, 1072935872, 1072935935, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (3, 0, 3564431568, 3564431583, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (4, 0, 1049547296, 1049547327, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (5, 0, 3392712704, 3392716799, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (6, 0, 982745088, 982761471, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (7, 0, 3523600896, 3523601151, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (8, 0, 1358400256, 1358400511, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (9, 0, 3394637824, 3394641919, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (10, 0, 1360512824, 1360512831, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (11, 0, 3400440520, 3400440527, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (12, 0, 1389215232, 1389217791, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (13, 0, 3400440532, 3400440535, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (14, 0, 1389218304, 1389219839, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (15, 0, 3400440704, 3400440711, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (16, 0, 1072926464, 1072926471, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (17, 0, 3400440720, 3400440727, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (18, 0, 1072928272, 1072928287, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (19, 0, 3400440792, 3400440799, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (20, 0, 3400441080, 3400441087, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (21, 0, 3400440816, 3400440823, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (22, 0, 3400440896, 3400440959, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (23, 0, 3400441024, 3400441055, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (24, 0, 3400441320, 3400441327, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (25, 0, 3400441280, 3400441311, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (26, 0, 3400437760, 3400437767, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (27, 0, 3400438912, 3400438919, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (28, 0, 3400437976, 3400437983, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (29, 0, 3400438832, 3400438839, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (30, 0, 3400440500, 3400440507, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (31, 0, 3400440248, 3400440255, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (32, 0, 3400440320, 3400440327, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (33, 0, 3400440344, 3400440351, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (34, 0, 3400440416, 3400440431, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (35, 0, 3400440448, 3400440455, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (36, 0, 3400440480, 3400440487, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (37, 0, 3400434976, 3400434991, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (38, 0, 3400434952, 3400434959, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (39, 0, 3400435200, 3400435207, 'AFGHANISTAN', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (40, 0, 1361037876, 1361037879, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (41, 0, 3642290176, 3642294271, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (42, 0, 1432255488, 1432256511, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (43, 0, 3587290368, 3587290431, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (44, 0, 1347305472, 1347309567, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (45, 0, 3587291968, 3587292031, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (46, 0, 3254649088, 3254649855, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (47, 0, 3583337728, 3583337983, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (48, 0, 1410613248, 1410621439, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (49, 0, 3583339520, 3583340287, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (50, 0, 3252439552, 3252439583, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (51, 0, 3583341040, 3583341055, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (52, 0, 1348096000, 1348100095, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (53, 0, 1045119232, 1045119743, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (54, 0, 1348169728, 1348173823, 'ALBANIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (55, 0, 1049708544, 1049710079, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (56, 0, 1362403584, 1362405887, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (57, 0, 1049707776, 1049708031, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (58, 0, 1361036664, 1361036667, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (59, 0, 3522122048, 3522122111, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (60, 0, 1361036720, 1361036727, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (61, 0, 3285926912, 3285927423, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (62, 0, 1361036848, 1361036851, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (63, 0, 3582616576, 3582616831, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (64, 0, 1358512128, 1358513183, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (65, 0, 3582736384, 3582737407, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (66, 0, 1361036828, 1361036831, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (67, 0, 3567606272, 3567606783, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (68, 0, 3273150464, 3273152511, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (69, 0, 3587292160, 3587292223, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (70, 0, 1361036972, 1361036979, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (71, 0, 1361037084, 1361037087, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (72, 0, 1361037364, 1361037367, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (73, 0, 1361037208, 1361037215, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (74, 0, 1361037296, 1361037311, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (75, 0, 1361037444, 1361037447, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (76, 0, 1361037528, 1361037551, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (77, 0, 1361037632, 1361037639, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (78, 0, 1361037668, 1361037671, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (79, 0, 3254495488, 3254495743, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (80, 0, 3254496768, 3254497247, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (81, 0, 1361036316, 1361036327, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (82, 0, 1361036208, 1361036223, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (83, 0, 3254489088, 3254489343, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (84, 0, 3254491136, 3254491391, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (85, 0, 1361036360, 1361036367, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (86, 0, 3274168832, 3274169343, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (87, 0, 1361036336, 1361036351, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (88, 0, 1361036384, 1361036387, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (89, 0, 1361036524, 1361036527, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (90, 0, 1357365248, 1357365887, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (91, 0, 1361036596, 1361036599, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (92, 0, 1357369344, 1357369599, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (93, 0, 1357367296, 1357368063, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (94, 0, 1361036604, 1361036607, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (95, 0, 1357370112, 1357370879, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (96, 0, 1361035628, 1361035631, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (97, 0, 1361035752, 1361035759, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (98, 0, 1361035788, 1361035807, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (99, 0, 1425968241, 1425968304, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (100, 0, 1425968133, 1425968168, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (101, 0, 1425968173, 1425968232, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (102, 0, 1425968332, 1425968383, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (103, 0, 1425968557, 1425968564, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (104, 0, 1425968309, 1425968328, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (105, 0, 1425968409, 1425968464, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (106, 0, 1425968385, 1425968404, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (107, 0, 1425968541, 1425968552, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (108, 0, 1425968469, 1425968532, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (109, 0, 1425968569, 1425968580, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (110, 0, 1425968601, 1425968612, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (111, 0, 1425968585, 1425968596, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (112, 0, 1425968673, 1425968688, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (113, 0, 1425969153, 1425969160, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (114, 0, 1425969169, 1425969184, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (115, 0, 3240727040, 3240727551, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (116, 0, 1360420864, 1360421119, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (117, 0, 1360429056, 1360433151, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (118, 0, 3224692736, 3224692991, 'ALGERIA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (119, 0, 3393613824, 3393617919, 'AMERICAN SAMOA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (120, 0, 1424736000, 1424736383, 'AMERICAN SAMOA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (121, 0, 1424751360, 1424751615, 'AMERICAN SAMOA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (122, 0, 3651939776, 3651939791, 'ANDORRA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (123, 0, 3262479282, 3262479282, 'ANDORRA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (124, 0, 3278943735, 3278943735, 'ANDORRA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (125, 0, 3265150976, 3265159167, 'ANDORRA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (126, 0, 3278943684, 3278943684, 'ANDORRA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (127, 0, 1432264704, 1432272895, 'ANDORRA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (128, 0, 3278939592, 3278939595, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (129, 0, 3262478121, 3262478121, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (130, 0, 3278939756, 3278939759, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (131, 0, 3272086832, 3272086879, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (132, 0, 3642028320, 3642028327, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (133, 0, 3253469184, 3253471231, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (134, 0, 3642052864, 3642053119, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (135, 0, 3272085504, 3272086015, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (136, 0, 3641355264, 3641355519, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (137, 0, 3272087296, 3272087551, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (138, 0, 3585837568, 3585837695, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (139, 0, 3252425472, 3252425535, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (140, 0, 3585838080, 3585838143, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (141, 0, 1347984712, 1347984719, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (142, 0, 3585838272, 3585838335, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (143, 0, 3262478347, 3262478347, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (144, 0, 1043918336, 1043918847, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (145, 0, 1118963488, 1118963495, 'ANGOLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (146, 0, 3475752448, 3475752703, 'ANGUILLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (147, 0, 3423533056, 3423535103, 'ANGUILLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (148, 0, 3510324736, 3510324991, 'ANGUILLA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (149, 0, 1438900224, 1438908415, 'ANTARCTICA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (150, 0, 1438892032, 1438892287, 'ANTARCTICA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (151, 0, 3475670016, 3475671039, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (152, 0, 1161427456, 1161428223, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (153, 0, 3459267328, 3459267583, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (154, 0, 1161425408, 1161427199, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (155, 0, 3459266560, 3459267071, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (156, 0, 1160921088, 1160925183, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (157, 0, 3459267840, 3459268607, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (158, 0, 1161423360, 1161423871, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (159, 0, 3459348224, 3459348479, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (160, 0, 1161420800, 1161422079, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (161, 0, 3449874688, 3449874943, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (162, 0, 1161422592, 1161422847, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (163, 0, 3448263424, 3448263935, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (164, 0, 1161424128, 1161424383, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (165, 0, 3434913792, 3434914047, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (166, 0, 1121247824, 1121247839, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (167, 0, 3434914304, 3434915327, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (168, 0, 1120913344, 1120913351, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (169, 0, 3434916608, 3434917119, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (170, 0, 1120914688, 1120914719, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (171, 0, 3434916096, 3434916351, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (172, 0, 1121248000, 1121248007, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (173, 0, 3434917376, 3434917887, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (174, 0, 1121248160, 1121248191, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (175, 0, 3489719040, 3489720063, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (176, 0, 1081410048, 1081410559, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (177, 0, 3489718272, 3489718527, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (178, 0, 1081416448, 1081416703, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (179, 0, 3510324224, 3510324735, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (180, 0, 1081417728, 1081418751, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (181, 0, 3510322176, 3510323199, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (182, 0, 3649709296, 3649709311, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (183, 0, 3510321664, 3510321919, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (184, 0, 3589267856, 3589267863, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (185, 0, 3510324992, 3510326271, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (186, 0, 3510327296, 3510328319, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (187, 0, 3510328576, 3510329343, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (188, 0, 3512221696, 3512223231, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (189, 0, 3512223488, 3512223743, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (190, 0, 3506404352, 3506404863, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO iprange_tbl VALUES (191, 0, 3506184192, 3506185215, 'ANTIGUA AND BARBUDA', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);

--
-- TOC entry 3190 (class 0 OID 0)
-- Dependencies: 232
-- Name: iprange_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('iprange_tbl_id_seq', 58850, true);


--
-- TOC entry 3175 (class 0 OID 14351971)
-- Dependencies: 263
-- Data for Name: state_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO state_tbl VALUES (-100, 100, NULL, 'N/A', 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO state_tbl VALUES (1, 200, 'Test State', '', 0, '2014-01-18 09:59:38.211843', '2014-01-18 09:59:38.211843', true);
INSERT INTO state_tbl VALUES (2, 100, 'ÆØ', '', 0, '2014-01-18 10:15:06.213434', '2014-01-18 10:15:06.213434', true);


--
-- TOC entry 3177 (class 0 OID 14351989)
-- Dependencies: 265
-- Data for Name: postalcode_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO postalcode_tbl VALUES (1, -100, '1000', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (2, -100, '1001', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (3, -100, '1002', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (4, -100, '1003', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (5, -100, '1004', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (6, -100, '1005', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (7, -100, '1006', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (8, -100, '1007', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (9, -100, '1008', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (10, -100, '1009', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (11, -100, '1010', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (12, -100, '1011', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (13, -100, '1012', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (14, -100, '1013', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (15, -100, '1014', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (16, -100, '1015', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (17, -100, '1016', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (18, -100, '1017', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (19, -100, '1018', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (20, -100, '1019', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (21, -100, '1020', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (22, -100, '1021', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (23, -100, '1022', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (24, -100, '1023', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (25, -100, '1024', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (26, -100, '1025', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (27, -100, '1026', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (28, -100, '1045', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (29, -100, '1050', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (30, -100, '1051', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (31, -100, '1052', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (32, -100, '1053', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (33, -100, '1054', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (34, -100, '1055', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (35, -100, '1056', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (36, -100, '1057', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (37, -100, '1058', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (38, -100, '1059', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (39, -100, '1060', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (40, -100, '1061', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (41, -100, '1062', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (42, -100, '1063', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (43, -100, '1064', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (44, -100, '1065', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (45, -100, '1066', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (46, -100, '1067', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (47, -100, '1068', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (48, -100, '1069', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (49, -100, '1070', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (50, -100, '1071', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (51, -100, '1072', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (52, -100, '1073', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (53, -100, '1074', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (54, -100, '1092', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (55, -100, '1093', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (56, -100, '1095', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (57, -100, '1098', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (58, -100, '1100', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (59, -100, '1101', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (60, -100, '1102', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (61, -100, '1103', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (62, -100, '1104', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (63, -100, '1105', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (64, -100, '1106', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (65, -100, '1107', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (66, -100, '1110', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (67, -100, '1111', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (68, -100, '1112', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (69, -100, '1113', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (70, -100, '1114', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (71, -100, '1115', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (72, -100, '1116', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (73, -100, '1117', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (74, -100, '1118', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (75, -100, '1119', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (76, -100, '1120', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (77, -100, '1121', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (78, -100, '1122', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (79, -100, '1123', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (80, -100, '1124', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (81, -100, '1125', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (82, -100, '1126', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (83, -100, '1127', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (84, -100, '1128', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (85, -100, '1129', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (86, -100, '1130', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (87, -100, '1131', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (88, -100, '1140', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (89, -100, '1147', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (90, -100, '1148', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (91, -100, '1150', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (92, -100, '1151', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (93, -100, '1152', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (94, -100, '1153', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (95, -100, '1154', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (96, -100, '1155', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (97, -100, '1156', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (98, -100, '1157', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (99, -100, '1158', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);
INSERT INTO postalcode_tbl VALUES (100, -100, '1159', 'København K', 55.6832352, 12.5855455, 3600, 0, '2013-11-08 09:27:43.706365', '2013-11-08 09:27:43.706365', true);


--
-- TOC entry 3191 (class 0 OID 0)
-- Dependencies: 264
-- Name: postalcode_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('postalcode_tbl_id_seq', 1146, true);


--
-- TOC entry 3192 (class 0 OID 0)
-- Dependencies: 234
-- Name: pricepoint_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('pricepoint_tbl_id_seq', 68, true);


--
-- TOC entry 3160 (class 0 OID 10765993)
-- Dependencies: 235
-- Data for Name: psp_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO psp_tbl VALUES (0, 'System Record', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', false);
INSERT INTO psp_tbl VALUES (1, 'Cellpoint Mobile', '2008-02-22 18:41:59.280496', '2008-11-19 06:48:24.452613', true);
INSERT INTO psp_tbl VALUES (2, 'DIBS - Custom Pages', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO psp_tbl VALUES (3, 'IHI', '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO psp_tbl VALUES (4, 'WorldPay', '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO psp_tbl VALUES (5, 'PayEx', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO psp_tbl VALUES (6, 'Authorize.Net', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO psp_tbl VALUES (7, 'WannaFind', '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO psp_tbl VALUES (8, 'NetAxept', '2012-12-19 08:45:22.30609', '2012-12-19 08:46:09.28319', true);
INSERT INTO psp_tbl VALUES (9, 'CPG', '2013-11-01 08:57:50.594616', '2013-11-01 08:57:50.594616', true);


--
-- TOC entry 3193 (class 0 OID 0)
-- Dependencies: 236
-- Name: psp_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('psp_tbl_id_seq', 8, true);


--
-- TOC entry 3162 (class 0 OID 10766001)
-- Dependencies: 237
-- Data for Name: pspcard_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO pspcard_tbl VALUES (0, 0, 0, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', false);
INSERT INTO pspcard_tbl VALUES (1, 10, 1, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (2, 0, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (3, 2, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (4, 7, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (5, 8, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (6, 9, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (7, 3, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (8, 1, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (9, 5, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (10, 4, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (11, 6, 2, '2008-03-24 19:08:58.071781', '2008-03-24 19:08:58.071781', true);
INSERT INTO pspcard_tbl VALUES (12, 0, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (13, 2, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (14, 7, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (15, 8, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (16, 9, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (17, 3, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (18, 1, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (19, 5, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (20, 4, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (21, 6, 3, '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcard_tbl VALUES (22, 11, 1, '2009-06-06 14:41:03.13733', '2009-06-06 14:41:03.13733', true);
INSERT INTO pspcard_tbl VALUES (23, 6, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (24, 9, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (25, 0, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (26, 3, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (27, 7, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (28, 5, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (29, 4, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (30, 8, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (31, 2, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (32, 1, 4, '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcard_tbl VALUES (33, 6, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (34, 4, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (35, 8, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (36, 5, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (37, 9, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (38, 1, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (39, 2, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (40, 0, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (41, 7, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (42, 3, 5, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (43, 6, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (44, 4, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (45, 8, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (46, 5, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (47, 9, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (48, 1, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (49, 2, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (50, 0, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (51, 7, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (52, 3, 6, '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcard_tbl VALUES (57, 11, 7, '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcard_tbl VALUES (58, 8, 7, '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcard_tbl VALUES (59, 5, 7, '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcard_tbl VALUES (60, 1, 7, '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcard_tbl VALUES (61, 2, 7, '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcard_tbl VALUES (62, 0, 7, '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcard_tbl VALUES (63, 7, 7, '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcard_tbl VALUES (64, 3, 7, '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcard_tbl VALUES (66, 12, 4, '2012-06-28 12:51:12.364958', '2012-06-28 12:51:12.364958', true);
INSERT INTO pspcard_tbl VALUES (67, 13, 4, '2012-06-28 12:51:12.364958', '2012-06-28 12:51:12.364958', true);
INSERT INTO pspcard_tbl VALUES (68, 14, 4, '2012-06-28 12:51:12.364958', '2012-06-28 12:51:12.364958', true);
INSERT INTO pspcard_tbl VALUES (93, 6, 8, '2012-12-19 11:36:09.256163', '2012-12-19 11:36:09.256163', true);
INSERT INTO pspcard_tbl VALUES (94, 9, 8, '2012-12-19 11:36:09.256163', '2012-12-19 11:36:09.256163', true);
INSERT INTO pspcard_tbl VALUES (95, 2, 8, '2012-12-19 11:36:09.256163', '2012-12-19 11:36:09.256163', true);
INSERT INTO pspcard_tbl VALUES (96, 7, 8, '2012-12-19 11:36:09.256163', '2012-12-19 11:36:09.256163', true);
INSERT INTO pspcard_tbl VALUES (97, 8, 8, '2012-12-19 11:36:09.256163', '2012-12-19 11:36:09.256163', true);


--
-- TOC entry 3194 (class 0 OID 0)
-- Dependencies: 238
-- Name: pspcard_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('pspcard_tbl_id_seq', 97, true);


--
-- TOC entry 3164 (class 0 OID 10766009)
-- Dependencies: 239
-- Data for Name: pspcurrency_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO pspcurrency_tbl VALUES (1, 100, 1, '208', '2008-02-22 18:41:59.280496', '2008-10-07 13:00:59.577181', true);
INSERT INTO pspcurrency_tbl VALUES (3, 100, 2, '208', '2008-02-22 18:41:59.280496', '2008-10-07 13:00:59.577181', true);
INSERT INTO pspcurrency_tbl VALUES (2, 200, 1, '840', '2008-02-22 18:41:59.280496', '2008-10-07 13:00:59.577181', true);
INSERT INTO pspcurrency_tbl VALUES (4, 200, 2, '840', '2008-02-22 18:41:59.280496', '2008-10-07 13:00:59.577181', true);
INSERT INTO pspcurrency_tbl VALUES (0, 0, 0, '   ', '2008-02-22 18:41:52.024002', '2008-10-07 13:13:41.427839', false);
INSERT INTO pspcurrency_tbl VALUES (5, 100, 3, '208', '2008-11-19 06:51:30.3796', '2008-11-19 06:51:30.3796', true);
INSERT INTO pspcurrency_tbl VALUES (7, 101, 2, '752', '2009-02-16 05:33:11.174643', '2009-02-16 05:33:11.174643', true);
INSERT INTO pspcurrency_tbl VALUES (8, 100, 4, 'DKK', '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcurrency_tbl VALUES (9, 101, 4, 'SEK', '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcurrency_tbl VALUES (10, 200, 4, 'USD', '2010-10-14 16:59:50.034982', '2010-10-14 16:59:50.034982', true);
INSERT INTO pspcurrency_tbl VALUES (12, 100, 5, 'DKK', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcurrency_tbl VALUES (13, 100, 6, 'DKK', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcurrency_tbl VALUES (14, 101, 6, 'SEK', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcurrency_tbl VALUES (15, 200, 6, 'USD', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO pspcurrency_tbl VALUES (177, 102, 9, 'NOK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (178, 104, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (179, 105, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (24, 100, 7, '208', '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcurrency_tbl VALUES (25, 101, 7, '840', '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcurrency_tbl VALUES (26, 200, 7, '752', '2012-03-02 11:01:31.155778', '2012-03-02 11:44:10.417562', true);
INSERT INTO pspcurrency_tbl VALUES (27, 103, 7, '826', '2012-04-19 15:12:07.4104', '2012-04-19 15:12:07.4104', true);
INSERT INTO pspcurrency_tbl VALUES (11, 103, 4, 'GBP', '2010-10-15 10:12:52.514519', '2012-05-08 17:54:02.375936', true);
INSERT INTO pspcurrency_tbl VALUES (29, 103, 2, '826', '2012-07-26 16:59:24.792126', '2012-07-26 16:59:24.792126', true);
INSERT INTO pspcurrency_tbl VALUES (31, 103, 1, 'GBP', '2012-08-01 10:20:06.815696', '2012-08-01 10:20:06.815696', true);
INSERT INTO pspcurrency_tbl VALUES (180, 106, 9, 'ILS', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (181, 107, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (182, 108, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (183, 109, 9, 'CHW', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (184, 110, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (185, 111, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (186, 112, 9, 'PLN', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (35, 100, 8, 'DKK', '2012-12-19 08:45:22.30609', '2012-12-19 08:46:09.28319', true);
INSERT INTO pspcurrency_tbl VALUES (36, 101, 8, 'SEK', '2012-12-19 08:45:22.30609', '2012-12-19 08:46:09.28319', true);
INSERT INTO pspcurrency_tbl VALUES (187, 113, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (188, 114, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (189, 115, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (190, 116, 9, 'AFN', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (191, 117, 9, 'ALL', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (192, 118, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (193, 119, 9, 'AMD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (194, 120, 9, 'BYR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (195, 121, 9, 'BAM', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (196, 122, 9, 'BGN', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (197, 123, 9, 'HRK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (198, 124, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (199, 125, 9, 'CZK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (200, 126, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (201, 127, 9, 'DKK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (202, 128, 9, 'GBP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (203, 129, 9, 'GIP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (204, 130, 9, 'DKK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (205, 131, 9, 'HUF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (206, 132, 9, 'ISK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (207, 133, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (208, 134, 9, 'GBP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (209, 135, 9, 'LVL', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (210, 136, 9, 'CHF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (211, 137, 9, 'LTL', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (212, 138, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (213, 139, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (214, 140, 9, 'MDL', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (215, 141, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (216, 142, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (217, 143, 9, 'MKD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (218, 144, 9, 'XCD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (219, 145, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (220, 146, 9, 'NIS', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (221, 147, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (222, 148, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (223, 149, 9, 'RON', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (224, 150, 9, 'RSD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (225, 151, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (226, 152, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (227, 153, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (228, 154, 9, 'TRY', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (229, 155, 9, 'UAH', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (230, 156, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (231, 201, 9, 'MXV', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (232, 202, 9, 'CAD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (233, 203, 9, 'XCD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (234, 204, 9, 'XCD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (235, 205, 9, 'BBD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (236, 206, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (237, 207, 9, 'KYD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (238, 208, 9, 'CUP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (239, 209, 9, 'DOP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (240, 210, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (241, 211, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (242, 212, 9, 'JMD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (243, 213, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (244, 214, 9, 'BMD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (245, 215, 9, 'BSD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (246, 216, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (247, 217, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (248, 218, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (249, 300, 9, 'DZD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (250, 301, 9, 'AOA', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (251, 302, 9, 'BDT', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (252, 303, 9, 'XOF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (253, 304, 9, 'BOV', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (254, 305, 9, 'BWP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (255, 306, 9, 'XOF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (256, 307, 9, 'BIF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (257, 308, 9, 'XAF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (258, 309, 9, 'CVE', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (259, 310, 9, 'XAF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (260, 311, 9, 'XAF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (261, 312, 9, 'KMF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (262, 313, 9, 'XAF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (263, 314, 9, 'XOF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (264, 315, 9, 'CDF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (265, 316, 9, 'DJF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (266, 317, 9, 'EGP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (267, 318, 9, 'XAF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (268, 319, 9, 'ETB', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (269, 320, 9, 'XAF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (270, 321, 9, 'GMD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (271, 322, 9, 'GHS', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (272, 323, 9, 'GNF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (273, 324, 9, 'XOF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (274, 325, 9, 'KES', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (275, 326, 9, 'LSL', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (276, 327, 9, 'LRD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (277, 328, 9, 'MGA', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (278, 329, 9, 'MWK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (279, 330, 9, 'XOF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (280, 331, 9, 'MRO', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (281, 332, 9, 'MUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (282, 333, 9, 'MAD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (283, 334, 9, 'MZN', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (284, 335, 9, 'GBP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (285, 336, 9, 'MAD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (286, 337, 9, 'ERN', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (287, 338, 9, 'LYD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (288, 339, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (289, 340, 9, 'NAD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (290, 341, 9, 'XOF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (291, 342, 9, 'NGN', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (292, 343, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (293, 344, 9, 'RWF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (294, 345, 9, 'SCR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (295, 346, 9, 'SDG', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (296, 347, 9, 'SHP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (297, 348, 9, 'SLL', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (298, 349, 9, 'STD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (299, 350, 9, 'SZL', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (300, 351, 9, 'XOF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (301, 352, 9, 'TND', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (302, 353, 9, 'TZS', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (303, 354, 9, 'UGX', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (304, 355, 9, 'ZAR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (305, 356, 9, 'ZMW', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (306, 357, 9, 'XAF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (307, 358, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (308, 400, 9, 'ARS', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (309, 401, 9, 'AWG', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (310, 402, 9, 'BZD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (311, 403, 9, 'BRL', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (312, 404, 9, 'CLP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (313, 405, 9, 'COU', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (314, 406, 9, 'CRC', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (315, 407, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (316, 408, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (317, 409, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (318, 410, 9, 'GTQ', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (319, 411, 9, 'GYD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (320, 412, 9, 'HNL', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (321, 413, 9, 'AQD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (322, 414, 9, 'XCD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (323, 415, 9, 'CMG', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (324, 416, 9, 'NOK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (325, 417, 9, 'NZD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (326, 418, 9, 'XCD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (327, 419, 9, 'FKP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (328, 420, 9, 'XCD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (329, 421, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (330, 422, 9, 'GBP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (331, 423, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (332, 424, 9, 'XCD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (333, 425, 9, 'XCD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (334, 426, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (335, 427, 9, 'NIO', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (336, 428, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (337, 429, 9, 'PEN', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (338, 430, 9, 'NZD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (339, 431, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (340, 432, 9, 'PYG', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (341, 433, 9, 'XOF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (342, 434, 9, 'SOS', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (343, 435, 9, 'SRD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (344, 436, 9, 'ANG', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (345, 437, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (346, 438, 9, 'TTD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (347, 439, 9, 'UYU', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (348, 440, 9, 'XCD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (349, 441, 9, 'VEF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (350, 442, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (351, 500, 9, 'AUD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (352, 501, 9, 'BND', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (353, 502, 9, 'NZD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (354, 503, 9, 'FJD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (355, 504, 9, 'XPF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (356, 505, 9, 'IDR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (357, 506, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (358, 507, 9, 'AUD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (359, 508, 9, 'AUD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (360, 509, 9, 'XPF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (361, 510, 9, 'AUD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (362, 511, 9, 'AUD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (363, 512, 9, 'NZD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (364, 513, 9, 'NZD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (365, 514, 9, 'SBD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (366, 515, 9, 'TOP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (367, 516, 9, 'AUD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (368, 517, 9, 'VUV', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (369, 518, 9, 'XPF', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (370, 519, 9, 'WST', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (371, 600, 9, 'AED', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (372, 601, 9, 'BHD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (373, 602, 9, 'AED', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (374, 603, 9, 'INR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (375, 604, 9, 'KWD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (376, 605, 9, 'OMR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (377, 606, 9, 'QAR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (378, 607, 9, 'RUB', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (379, 608, 9, 'SAR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (380, 609, 9, 'CNY', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (381, 611, 9, 'AZN', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (382, 612, 9, 'BTN', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (383, 613, 9, 'KHR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (384, 614, 9, 'HKD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (385, 615, 9, 'IRR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (386, 616, 9, 'JPY', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (387, 617, 9, 'JOD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (388, 618, 9, 'KZT', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (389, 619, 9, 'KGS', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (390, 620, 9, 'LAK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (391, 621, 9, 'LBP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (392, 622, 9, 'MOP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (393, 623, 9, 'MVR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (394, 624, 9, 'MNT', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (395, 625, 9, 'MMK', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (396, 626, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (397, 627, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (398, 628, 9, 'IQD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (399, 629, 9, 'EUR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (400, 630, 9, 'AUD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (401, 631, 9, 'KPW', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (402, 632, 9, 'KRW', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (403, 633, 9, 'KZT', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (404, 634, 9, 'LKR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (405, 635, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (406, 636, 9, 'MOP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (407, 637, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (408, 638, 9, 'MYR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (409, 639, 9, 'NPR', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (410, 640, 9, 'PHP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (411, 641, 9, 'USD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (412, 642, 9, 'SGD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (413, 643, 9, 'SYP', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (414, 644, 9, 'THB', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (415, 645, 9, 'TMT', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (416, 646, 9, 'TWD', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (417, 647, 9, 'AED', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (418, 648, 9, 'UZS', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (419, 649, 9, 'VND', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);
INSERT INTO pspcurrency_tbl VALUES (420, 650, 9, 'YER', '2013-11-04 13:27:37.01545', '2013-11-04 13:27:37.01545', true);


--
-- TOC entry 3195 (class 0 OID 0)
-- Dependencies: 240
-- Name: pspcurrency_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('pspcurrency_tbl_id_seq', 420, true);


--
-- TOC entry 3166 (class 0 OID 10766017)
-- Dependencies: 241
-- Data for Name: shipping_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO shipping_tbl VALUES (0, 'System Record', NULL, '2008-11-12 12:26:27.197317', '2008-11-12 12:26:27.197317', false);
INSERT INTO shipping_tbl VALUES (1, 'Post Danmark', 'http://mpoint.cellpointmobile.com/img/post_denmark_logo.gif', '2008-11-12 12:26:58.035568', '2008-11-12 12:26:58.035568', true);
INSERT INTO shipping_tbl VALUES (2, 'Posten', 'http://mpoint.cellpointmobile.com/img/swedish_post_logo.gif', '2009-02-16 05:38:41.701811', '2009-02-16 05:39:04.257871', true);


--
-- TOC entry 3196 (class 0 OID 0)
-- Dependencies: 242
-- Name: shipping_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('shipping_tbl_id_seq', 2, true);


--
-- TOC entry 3197 (class 0 OID 0)
-- Dependencies: 262
-- Name: state_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('state_tbl_id_seq', 2, true);


--
-- TOC entry 3168 (class 0 OID 10766025)
-- Dependencies: 243
-- Data for Name: type_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO type_tbl VALUES (0, 'System Record', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', false);
INSERT INTO type_tbl VALUES (10, 'Call Centre Purchase', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', true);
INSERT INTO type_tbl VALUES (11, 'Call Centre Subscription', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', true);
INSERT INTO type_tbl VALUES (20, 'SMS Purchase', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', true);
INSERT INTO type_tbl VALUES (21, 'SMS Subscription', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', true);
INSERT INTO type_tbl VALUES (30, 'Web Purchase', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', true);
INSERT INTO type_tbl VALUES (31, 'Web Subscription', '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', true);
INSERT INTO type_tbl VALUES (100, 'Top-Up Purchase', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO type_tbl VALUES (101, 'Top-Up Subscription', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO type_tbl VALUES (1000, 'E-Money Top-Up', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO type_tbl VALUES (1001, 'E-Money Purchase', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO type_tbl VALUES (1002, 'E-Money Transfer', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO type_tbl VALUES (1003, 'E-Money Withdrawal', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO type_tbl VALUES (1009, 'Card Purchase', '2009-07-21 12:05:06.220411', '2009-07-21 12:05:06.220411', true);
INSERT INTO type_tbl VALUES (40, 'Mobile App. Purchase', '2012-06-26 17:42:58.185661', '2012-06-26 17:42:58.185661', true);
INSERT INTO type_tbl VALUES (41, 'Mobile App. Subscription', '2012-06-26 17:42:58.185661', '2012-06-26 17:42:58.185661', true);
INSERT INTO type_tbl VALUES (102, 'Points Top-Up Purchase', '2012-09-29 11:46:12.060279', '2012-09-29 11:46:12.060279', true);
INSERT INTO type_tbl VALUES (1004, 'Points Top-Up', '2012-09-29 11:46:12.060279', '2012-09-29 11:46:12.060279', true);
INSERT INTO type_tbl VALUES (1005, 'Points Purchase', '2012-09-29 11:46:12.060279', '2012-09-29 11:46:12.060279', true);
INSERT INTO type_tbl VALUES (1007, 'Points Reward', '2012-09-30 10:42:49.38124', '2012-09-30 10:42:49.38124', true);


--
-- TOC entry 3198 (class 0 OID 0)
-- Dependencies: 244
-- Name: type_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('type_tbl_id_seq', 1, false);


--
-- TOC entry 3173 (class 0 OID 13141804)
-- Dependencies: 249
-- Data for Name: urltype_tbl; Type: TABLE DATA; Schema: system; Owner: jona
--

INSERT INTO urltype_tbl VALUES (1, 'Import Customer Data', '2013-05-01 06:41:32.657927', '2013-05-01 06:41:32.657927', true);
INSERT INTO urltype_tbl VALUES (2, 'Single Sign-On Authentication', '2013-08-07 17:24:52.984769', '2013-08-07 17:24:52.984769', true);


--
-- TOC entry 3199 (class 0 OID 0)
-- Dependencies: 248
-- Name: urltype_tbl_id_seq; Type: SEQUENCE SET; Schema: system; Owner: jona
--

SELECT pg_catalog.setval('urltype_tbl_id_seq', 1, false);


-- Completed on 2014-11-17 15:26:37 CET


CREATE SCHEMA template;


SET search_path = template, pg_catalog;

--
-- TOC entry 199 (class 1259 OID 202578)
-- Name: general_tbl; Type: TABLE; Schema: template; Owner: -; Tablespace:
--

CREATE TABLE general_tbl (
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);



--
-- TOC entry 7 (class 2615 OID 202402)
-- Name: enduser; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA enduser;


SET search_path = enduser, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 197 (class 1259 OID 202566)
-- Name: account_tbl; Type: TABLE; Schema: enduser; Owner: -; Tablespace:
--

CREATE TABLE account_tbl (
    id integer NOT NULL,
    countryid integer NOT NULL,
    firstname character varying(50),
    lastname character varying(50),
    mobile character varying(15),
    email character varying(50),
    passwd character varying(50),
    balance integer DEFAULT 0,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    attempts integer DEFAULT 0,
    points integer DEFAULT 0,
    mobile_verified boolean DEFAULT false,
    externalid character varying(50)
);


--
-- TOC entry 198 (class 1259 OID 202576)
-- Name: account_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: -
--

CREATE SEQUENCE account_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2520 (class 0 OID 0)
-- Dependencies: 198
-- Name: account_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: -
--

ALTER SEQUENCE account_tbl_id_seq OWNED BY account_tbl.id;


--
-- TOC entry 200 (class 1259 OID 202584)
-- Name: activation_tbl; Type: TABLE; Schema: enduser; Owner: -; Tablespace:
--

CREATE TABLE activation_tbl (
    id integer NOT NULL,
    accountid integer NOT NULL,
    code integer,
    address character varying(50),
    active boolean DEFAULT false,
    expiry timestamp without time zone DEFAULT (now() + '24:00:00'::interval)
)
INHERITS (template.general_tbl);


--
-- TOC entry 201 (class 1259 OID 202592)
-- Name: activation_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: -
--

CREATE SEQUENCE activation_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2521 (class 0 OID 0)
-- Dependencies: 201
-- Name: activation_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: -
--

ALTER SEQUENCE activation_tbl_id_seq OWNED BY activation_tbl.id;


--
-- TOC entry 202 (class 1259 OID 202594)
-- Name: address_tbl; Type: TABLE; Schema: enduser; Owner: -; Tablespace:
--

CREATE TABLE address_tbl (
    id integer NOT NULL,
    accountid integer,
    cardid integer,
    countryid integer NOT NULL,
    stateid integer NOT NULL,
    firstname character varying(50),
    lastname character varying(50),
    company character varying(50),
    street character varying(50),
    postalcode character varying(10),
    city character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    CONSTRAINT address_tbl_check CHECK ((((accountid IS NULL) AND (cardid IS NOT NULL)) OR ((accountid IS NOT NULL) AND (cardid IS NULL))))
);


--
-- TOC entry 203 (class 1259 OID 202601)
-- Name: address_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: -
--

CREATE SEQUENCE address_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2522 (class 0 OID 0)
-- Dependencies: 203
-- Name: address_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: -
--

ALTER SEQUENCE address_tbl_id_seq OWNED BY address_tbl.id;


--
-- TOC entry 204 (class 1259 OID 202603)
-- Name: card_tbl; Type: TABLE; Schema: enduser; Owner: -; Tablespace:
--

CREATE TABLE card_tbl (
    id integer NOT NULL,
    accountid integer NOT NULL,
    cardid integer NOT NULL,
    pspid integer NOT NULL,
    mask character varying(20),
    expiry character varying(5),
    preferred boolean DEFAULT false,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    clientid integer,
    name character varying(50),
    ticket character varying(255),
    card_holder_name character varying(255)
);


--
-- TOC entry 205 (class 1259 OID 202613)
-- Name: card_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: -
--

CREATE SEQUENCE card_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2523 (class 0 OID 0)
-- Dependencies: 205
-- Name: card_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: -
--

ALTER SEQUENCE card_tbl_id_seq OWNED BY card_tbl.id;


--
-- TOC entry 206 (class 1259 OID 202615)
-- Name: claccess_tbl; Type: TABLE; Schema: enduser; Owner: -; Tablespace:
--

CREATE TABLE claccess_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    accountid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


--
-- TOC entry 207 (class 1259 OID 202621)
-- Name: claccess_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: -
--

CREATE SEQUENCE claccess_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2524 (class 0 OID 0)
-- Dependencies: 207
-- Name: claccess_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: -
--

ALTER SEQUENCE claccess_tbl_id_seq OWNED BY claccess_tbl.id;


--
-- TOC entry 208 (class 1259 OID 202623)
-- Name: transaction_tbl; Type: TABLE; Schema: enduser; Owner: -; Tablespace:
--

CREATE TABLE transaction_tbl (
    id integer NOT NULL,
    accountid integer NOT NULL,
    typeid integer NOT NULL,
    fromid integer,
    toid integer,
    txnid integer,
    amount integer,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    fee integer DEFAULT 0,
    ip inet,
    address character varying(50),
    message text,
    pending boolean DEFAULT false,
    stateid integer DEFAULT 1800,
    CONSTRAINT transaction_chk CHECK ((((fromid IS NULL) AND (toid IS NULL)) OR (txnid IS NULL)))
);


--
-- TOC entry 209 (class 1259 OID 202636)
-- Name: transaction_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: -
--

CREATE SEQUENCE transaction_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2525 (class 0 OID 0)
-- Dependencies: 209
-- Name: transaction_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: -
--

ALTER SEQUENCE transaction_tbl_id_seq OWNED BY transaction_tbl.id;


--
-- TOC entry 2337 (class 2604 OID 202875)
-- Name: id; Type: DEFAULT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY account_tbl ALTER COLUMN id SET DEFAULT nextval('account_tbl_id_seq'::regclass);


--
-- TOC entry 2343 (class 2604 OID 202876)
-- Name: created; Type: DEFAULT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY activation_tbl ALTER COLUMN created SET DEFAULT now();


--
-- TOC entry 2344 (class 2604 OID 202877)
-- Name: modified; Type: DEFAULT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY activation_tbl ALTER COLUMN modified SET DEFAULT now();


--
-- TOC entry 2345 (class 2604 OID 202878)
-- Name: enabled; Type: DEFAULT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY activation_tbl ALTER COLUMN enabled SET DEFAULT true;


--
-- TOC entry 2346 (class 2604 OID 202879)
-- Name: id; Type: DEFAULT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY activation_tbl ALTER COLUMN id SET DEFAULT nextval('activation_tbl_id_seq'::regclass);


--
-- TOC entry 2350 (class 2604 OID 202880)
-- Name: id; Type: DEFAULT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY address_tbl ALTER COLUMN id SET DEFAULT nextval('address_tbl_id_seq'::regclass);


--
-- TOC entry 2356 (class 2604 OID 202881)
-- Name: id; Type: DEFAULT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY card_tbl ALTER COLUMN id SET DEFAULT nextval('card_tbl_id_seq'::regclass);


--
-- TOC entry 2360 (class 2604 OID 202882)
-- Name: id; Type: DEFAULT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY claccess_tbl ALTER COLUMN id SET DEFAULT nextval('claccess_tbl_id_seq'::regclass);


--
-- TOC entry 2367 (class 2604 OID 202883)
-- Name: id; Type: DEFAULT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY transaction_tbl ALTER COLUMN id SET DEFAULT nextval('transaction_tbl_id_seq'::regclass);


--
-- TOC entry 2372 (class 2606 OID 203010)
-- Name: account_pk; Type: CONSTRAINT; Schema: enduser; Owner: -; Tablespace:
--

ALTER TABLE ONLY account_tbl
    ADD CONSTRAINT account_pk PRIMARY KEY (id);


--
-- TOC entry 2374 (class 2606 OID 203012)
-- Name: activate_uq; Type: CONSTRAINT; Schema: enduser; Owner: -; Tablespace:
--

ALTER TABLE ONLY activation_tbl
    ADD CONSTRAINT activate_uq UNIQUE (accountid, code);


--
-- TOC entry 2376 (class 2606 OID 203014)
-- Name: activation_pk; Type: CONSTRAINT; Schema: enduser; Owner: -; Tablespace:
--

ALTER TABLE ONLY activation_tbl
    ADD CONSTRAINT activation_pk PRIMARY KEY (id);


--
-- TOC entry 2378 (class 2606 OID 203016)
-- Name: address_pk; Type: CONSTRAINT; Schema: enduser; Owner: -; Tablespace:
--

ALTER TABLE ONLY address_tbl
    ADD CONSTRAINT address_pk PRIMARY KEY (id);


--
-- TOC entry 2381 (class 2606 OID 203018)
-- Name: card_pk; Type: CONSTRAINT; Schema: enduser; Owner: -; Tablespace:
--

ALTER TABLE ONLY card_tbl
    ADD CONSTRAINT card_pk PRIMARY KEY (id);


--
-- TOC entry 2383 (class 2606 OID 203020)
-- Name: card_uq; Type: CONSTRAINT; Schema: enduser; Owner: -; Tablespace:
--

ALTER TABLE ONLY card_tbl
    ADD CONSTRAINT card_uq UNIQUE (accountid, clientid, cardid, mask, expiry);


--
-- TOC entry 2386 (class 2606 OID 203022)
-- Name: claccess_pk; Type: CONSTRAINT; Schema: enduser; Owner: -; Tablespace:
--

ALTER TABLE ONLY claccess_tbl
    ADD CONSTRAINT claccess_pk PRIMARY KEY (id);


--
-- TOC entry 2389 (class 2606 OID 203024)
-- Name: transaction_pk; Type: CONSTRAINT; Schema: enduser; Owner: -; Tablespace:
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT transaction_pk PRIMARY KEY (id);


--
-- TOC entry 2391 (class 2606 OID 203026)
-- Name: transaction_uq; Type: CONSTRAINT; Schema: enduser; Owner: -; Tablespace:
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT transaction_uq UNIQUE (typeid, txnid);


--
-- TOC entry 2369 (class 1259 OID 203098)
-- Name: account_email_idx; Type: INDEX; Schema: enduser; Owner: -; Tablespace:
--

CREATE INDEX account_email_idx ON account_tbl USING btree (countryid, upper((email)::text), enabled) WHERE (enabled = true);


--
-- TOC entry 2370 (class 1259 OID 203099)
-- Name: account_mobile_idx; Type: INDEX; Schema: enduser; Owner: -; Tablespace:
--

CREATE INDEX account_mobile_idx ON account_tbl USING btree (countryid, mobile, enabled) WHERE (enabled = true);


--
-- TOC entry 2379 (class 1259 OID 203559)
-- Name: card_account_idx; Type: INDEX; Schema: enduser; Owner: -; Tablespace:
--

CREATE INDEX card_account_idx ON card_tbl USING btree (accountid);


--
-- TOC entry 2384 (class 1259 OID 203100)
-- Name: claccess_account; Type: INDEX; Schema: enduser; Owner: -; Tablespace:
--

CREATE INDEX claccess_account ON claccess_tbl USING btree (accountid);


--
-- TOC entry 2387 (class 1259 OID 203101)
-- Name: transaction_account_idx; Type: INDEX; Schema: enduser; Owner: -; Tablespace:
--

CREATE INDEX transaction_account_idx ON transaction_tbl USING btree (accountid, txnid);



--
-- TOC entry 8 (class 2615 OID 202403)
-- Name: log; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA log;


SET search_path = log, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 210 (class 1259 OID 202638)
-- Name: auditlog_tbl; Type: TABLE; Schema: log; Owner: -; Tablespace:
--

CREATE TABLE auditlog_tbl (
    id integer NOT NULL,
    operationid integer NOT NULL,
    mobile bigint,
    email character varying(255),
    customer_ref character varying(50),
    code integer NOT NULL,
    message character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


--
-- TOC entry 211 (class 1259 OID 202647)
-- Name: auditlog_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: -
--

CREATE SEQUENCE auditlog_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2499 (class 0 OID 0)
-- Dependencies: 211
-- Name: auditlog_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: -
--

ALTER SEQUENCE auditlog_tbl_id_seq OWNED BY auditlog_tbl.id;


--
-- TOC entry 212 (class 1259 OID 202649)
-- Name: message_tbl; Type: TABLE; Schema: log; Owner: -; Tablespace:
--

CREATE TABLE message_tbl (
    id integer NOT NULL,
    txnid integer NOT NULL,
    stateid integer NOT NULL,
    data text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


--
-- TOC entry 213 (class 1259 OID 202658)
-- Name: message_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: -
--

CREATE SEQUENCE message_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2500 (class 0 OID 0)
-- Dependencies: 213
-- Name: message_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: -
--

ALTER SEQUENCE message_tbl_id_seq OWNED BY message_tbl.id;


--
-- TOC entry 214 (class 1259 OID 202660)
-- Name: note_tbl; Type: TABLE; Schema: log; Owner: -; Tablespace:
--

CREATE TABLE note_tbl (
    id integer NOT NULL,
    txnid integer NOT NULL,
    userid integer NOT NULL,
    message text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


--
-- TOC entry 215 (class 1259 OID 202669)
-- Name: note_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: -
--

CREATE SEQUENCE note_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2501 (class 0 OID 0)
-- Dependencies: 215
-- Name: note_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: -
--

ALTER SEQUENCE note_tbl_id_seq OWNED BY note_tbl.id;


--
-- TOC entry 216 (class 1259 OID 202671)
-- Name: operation_tbl; Type: TABLE; Schema: log; Owner: -; Tablespace:
--

CREATE TABLE operation_tbl (
    id integer NOT NULL,
    name character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


--
-- TOC entry 217 (class 1259 OID 202677)
-- Name: operation_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: -
--

CREATE SEQUENCE operation_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2502 (class 0 OID 0)
-- Dependencies: 217
-- Name: operation_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: -
--

ALTER SEQUENCE operation_tbl_id_seq OWNED BY operation_tbl.id;


--
-- TOC entry 218 (class 1259 OID 202679)
-- Name: state_tbl; Type: TABLE; Schema: log; Owner: -; Tablespace:
--

CREATE TABLE state_tbl (
    id integer NOT NULL,
    name character varying(50),
    module character varying(255),
    func character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


--
-- TOC entry 219 (class 1259 OID 202688)
-- Name: state_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: -
--

CREATE SEQUENCE state_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2503 (class 0 OID 0)
-- Dependencies: 219
-- Name: state_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: -
--

ALTER SEQUENCE state_tbl_id_seq OWNED BY state_tbl.id;


--
-- TOC entry 220 (class 1259 OID 202690)
-- Name: transaction_tbl; Type: TABLE; Schema: log; Owner: -; Tablespace:
--

CREATE TABLE transaction_tbl (
    id integer NOT NULL,
    typeid integer NOT NULL,
    clientid integer NOT NULL,
    accountid integer NOT NULL,
    countryid integer NOT NULL,
    pspid integer,
    cardid integer,
    keywordid integer,
    amount integer,
    orderid character varying(40),
    extid character varying(40),
    lang character(2) DEFAULT 'gb'::bpchar,
    mobile character varying(15),
    operatorid integer,
    logourl character varying(255),
    cssurl character varying(255),
    callbackurl character varying(255),
    accepturl character varying(255),
    cancelurl character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    mode integer DEFAULT 0,
    email character varying(50),
    gomobileid integer DEFAULT (-1),
    auto_capture boolean,
    euaid integer,
    ip inet NOT NULL,
    iconurl character varying(255),
    markup character varying(5),
    points integer,
    reward integer,
    refund integer DEFAULT 0,
    authurl character varying(255),
    customer_ref character varying(50),
    description text,
    fee integer DEFAULT 0,
    captured integer DEFAULT 0
);


--
-- TOC entry 221 (class 1259 OID 202705)
-- Name: transaction_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: -
--

CREATE SEQUENCE transaction_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2504 (class 0 OID 0)
-- Dependencies: 221
-- Name: transaction_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: -
--

ALTER SEQUENCE transaction_tbl_id_seq OWNED BY transaction_tbl.id;


--
-- TOC entry 2333 (class 2604 OID 202884)
-- Name: id; Type: DEFAULT; Schema: log; Owner: -
--

ALTER TABLE ONLY auditlog_tbl ALTER COLUMN id SET DEFAULT nextval('auditlog_tbl_id_seq'::regclass);


--
-- TOC entry 2337 (class 2604 OID 202885)
-- Name: id; Type: DEFAULT; Schema: log; Owner: -
--

ALTER TABLE ONLY message_tbl ALTER COLUMN id SET DEFAULT nextval('message_tbl_id_seq'::regclass);


--
-- TOC entry 2341 (class 2604 OID 202886)
-- Name: id; Type: DEFAULT; Schema: log; Owner: -
--

ALTER TABLE ONLY note_tbl ALTER COLUMN id SET DEFAULT nextval('note_tbl_id_seq'::regclass);


--
-- TOC entry 2345 (class 2604 OID 202887)
-- Name: id; Type: DEFAULT; Schema: log; Owner: -
--

ALTER TABLE ONLY operation_tbl ALTER COLUMN id SET DEFAULT nextval('operation_tbl_id_seq'::regclass);


--
-- TOC entry 2349 (class 2604 OID 202888)
-- Name: id; Type: DEFAULT; Schema: log; Owner: -
--

ALTER TABLE ONLY state_tbl ALTER COLUMN id SET DEFAULT nextval('state_tbl_id_seq'::regclass);


--
-- TOC entry 2359 (class 2604 OID 202889)
-- Name: id; Type: DEFAULT; Schema: log; Owner: -
--

ALTER TABLE ONLY transaction_tbl ALTER COLUMN id SET DEFAULT nextval('transaction_tbl_id_seq'::regclass);


--
-- TOC entry 2361 (class 2606 OID 203028)
-- Name: auditlog_pk; Type: CONSTRAINT; Schema: log; Owner: -; Tablespace:
--

ALTER TABLE ONLY auditlog_tbl
    ADD CONSTRAINT auditlog_pk PRIMARY KEY (id);


--
-- TOC entry 2363 (class 2606 OID 203030)
-- Name: message_pk; Type: CONSTRAINT; Schema: log; Owner: -; Tablespace:
--

ALTER TABLE ONLY message_tbl
    ADD CONSTRAINT message_pk PRIMARY KEY (id);


--
-- TOC entry 2366 (class 2606 OID 203032)
-- Name: note_pk; Type: CONSTRAINT; Schema: log; Owner: -; Tablespace:
--

ALTER TABLE ONLY note_tbl
    ADD CONSTRAINT note_pk PRIMARY KEY (id);


--
-- TOC entry 2368 (class 2606 OID 203034)
-- Name: operation_pk; Type: CONSTRAINT; Schema: log; Owner: -; Tablespace:
--

ALTER TABLE ONLY operation_tbl
    ADD CONSTRAINT operation_pk PRIMARY KEY (id);


--
-- TOC entry 2370 (class 2606 OID 203036)
-- Name: state_pk; Type: CONSTRAINT; Schema: log; Owner: -; Tablespace:
--

ALTER TABLE ONLY state_tbl
    ADD CONSTRAINT state_pk PRIMARY KEY (id);


--
-- TOC entry 2376 (class 2606 OID 203038)
-- Name: transaction_pk; Type: CONSTRAINT; Schema: log; Owner: -; Tablespace:
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT transaction_pk PRIMARY KEY (id);


--
-- TOC entry 2364 (class 1259 OID 203102)
-- Name: message_transaction_state; Type: INDEX; Schema: log; Owner: -; Tablespace:
--

CREATE INDEX message_transaction_state ON message_tbl USING btree (txnid, stateid);


--
-- TOC entry 2371 (class 1259 OID 203103)
-- Name: transaction_customer_ref_idx; Type: INDEX; Schema: log; Owner: -; Tablespace:
--

CREATE INDEX transaction_customer_ref_idx ON transaction_tbl USING btree (customer_ref);


--
-- TOC entry 2372 (class 1259 OID 203104)
-- Name: transaction_email_idx; Type: INDEX; Schema: log; Owner: -; Tablespace:
--

CREATE INDEX transaction_email_idx ON transaction_tbl USING btree (email);


--
-- TOC entry 2373 (class 1259 OID 203105)
-- Name: transaction_mobile_idx; Type: INDEX; Schema: log; Owner: -; Tablespace:
--

CREATE INDEX transaction_mobile_idx ON transaction_tbl USING btree (mobile);


--
-- TOC entry 2374 (class 1259 OID 203106)
-- Name: transaction_order_idx; Type: INDEX; Schema: log; Owner: -; Tablespace:
--

CREATE INDEX transaction_order_idx ON transaction_tbl USING btree (clientid, orderid);


--
-- TOC entry 2377 (class 2606 OID 203332)
-- Name: auditlog2operation_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY auditlog_tbl
    ADD CONSTRAINT auditlog2operation_fk FOREIGN KEY (operationid) REFERENCES operation_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2378 (class 2606 OID 203337)
-- Name: msg2state_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY message_tbl
    ADD CONSTRAINT msg2state_fk FOREIGN KEY (stateid) REFERENCES state_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2379 (class 2606 OID 203342)
-- Name: msg2txn_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY message_tbl
    ADD CONSTRAINT msg2txn_fk FOREIGN KEY (txnid) REFERENCES transaction_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2380 (class 2606 OID 203347)
-- Name: note2transaction_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY note_tbl
    ADD CONSTRAINT note2transaction_fk FOREIGN KEY (txnid) REFERENCES enduser.transaction_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2381 (class 2606 OID 203357)
-- Name: txn2account_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2account_fk FOREIGN KEY (accountid) REFERENCES client.account_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2382 (class 2606 OID 203362)
-- Name: txn2card_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2card_fk FOREIGN KEY (cardid) REFERENCES system.card_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2383 (class 2606 OID 203367)
-- Name: txn2client_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2384 (class 2606 OID 203372)
-- Name: txn2country_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2388 (class 2606 OID 203584)
-- Name: txn2eua_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2eua_fk FOREIGN KEY (euaid) REFERENCES enduser.account_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2385 (class 2606 OID 203382)
-- Name: txn2keyword_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2keyword_fk FOREIGN KEY (keywordid) REFERENCES client.keyword_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2386 (class 2606 OID 203387)
-- Name: txn2psp_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2387 (class 2606 OID 203392)
-- Name: txn2type_fk; Type: FK CONSTRAINT; Schema: log; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2type_fk FOREIGN KEY (typeid) REFERENCES system.type_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;



SET search_path = enduser, pg_catalog;


--
-- TOC entry 2402 (class 2606 OID 203242)
-- Name: access2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY claccess_tbl
    ADD CONSTRAINT access2account_fk FOREIGN KEY (accountid) REFERENCES account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2403 (class 2606 OID 203247)
-- Name: access2client_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY claccess_tbl
    ADD CONSTRAINT access2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2392 (class 2606 OID 203252)
-- Name: account2country_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY account_tbl
    ADD CONSTRAINT account2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2393 (class 2606 OID 203257)
-- Name: activation2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY activation_tbl
    ADD CONSTRAINT activation2account_fk FOREIGN KEY (accountid) REFERENCES account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2394 (class 2606 OID 203262)
-- Name: address2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY address_tbl
    ADD CONSTRAINT address2account_fk FOREIGN KEY (accountid) REFERENCES account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2395 (class 2606 OID 203267)
-- Name: address2card_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY address_tbl
    ADD CONSTRAINT address2card_fk FOREIGN KEY (cardid) REFERENCES card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2396 (class 2606 OID 203272)
-- Name: address2country_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY address_tbl
    ADD CONSTRAINT address2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2397 (class 2606 OID 203277)
-- Name: address2state_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY address_tbl
    ADD CONSTRAINT address2state_fk FOREIGN KEY (stateid) REFERENCES system.state_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2398 (class 2606 OID 203282)
-- Name: card2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY card_tbl
    ADD CONSTRAINT card2account_fk FOREIGN KEY (accountid) REFERENCES account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2399 (class 2606 OID 203287)
-- Name: card2card_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY card_tbl
    ADD CONSTRAINT card2card_fk FOREIGN KEY (cardid) REFERENCES system.card_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2400 (class 2606 OID 203292)
-- Name: card2client_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY card_tbl
    ADD CONSTRAINT card2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2401 (class 2606 OID 203297)
-- Name: card2psp_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY card_tbl
    ADD CONSTRAINT card2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2404 (class 2606 OID 203302)
-- Name: transaction2state_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT transaction2state_fk FOREIGN KEY (stateid) REFERENCES log.state_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2405 (class 2606 OID 203307)
-- Name: txn2txn_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2txn_fk FOREIGN KEY (txnid) REFERENCES log.transaction_tbl(id) ON UPDATE CASCADE;


--
-- TOC entry 2406 (class 2606 OID 203312)
-- Name: txn2type_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txn2type_fk FOREIGN KEY (typeid) REFERENCES system.type_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2408 (class 2606 OID 203596)
-- Name: txnfrom2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txnfrom2account_fk FOREIGN KEY (fromid) REFERENCES account_tbl(id) ON UPDATE CASCADE;


--
-- TOC entry 2407 (class 2606 OID 203322)
-- Name: txnowner2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txnowner2account_fk FOREIGN KEY (accountid) REFERENCES account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2409 (class 2606 OID 203606)
-- Name: txnto2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: -
--

ALTER TABLE ONLY transaction_tbl
    ADD CONSTRAINT txnto2account_fk FOREIGN KEY (toid) REFERENCES account_tbl(id) ON UPDATE CASCADE;




SET search_path = log, pg_catalog;

INSERT INTO operation_tbl (id, name, created, modified, enabled) VALUES (1, 'Card saved', '2013-11-08 09:23:31.708568', '2013-11-08 09:23:31.708568', true);
INSERT INTO operation_tbl (id, name, created, modified, enabled) VALUES (2, 'Card deleted', '2013-11-08 09:23:31.708568', '2013-11-08 09:23:31.708568', true);
INSERT INTO operation_tbl (id, name, created, modified, enabled) VALUES (3, 'Logged in', '2013-11-08 09:23:31.708568', '2013-11-08 09:23:31.708568', true);

INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (0, 'System Record', NULL, NULL, '2008-02-22 18:41:52.024002', '2008-02-22 18:41:52.024002', false);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (10, 'Undefined Client ID', 'Validate', 'valBasic', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (12, 'Invalid Client ID', 'Validate', 'valBasic', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (13, 'Unknown Client ID', 'Validate', 'valBasic', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (14, 'Client Disabled', 'Validate', 'valBasic', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (21, 'Undefined Account', 'Validate', 'valBasic', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (22, 'Invalid Account', 'Validate', 'valBasic', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (23, 'Unknown Account', 'Validate', 'valBasic', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (24, 'Account Disabled', 'Validate', 'valBasic', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (41, 'Undefined Operator ID', 'Validate', 'valOperator', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (42, 'Operator ID is too short', 'Validate', 'valOperator', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (43, 'Operator ID is too big', 'Validate', 'valOperator', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (44, 'Operator not supported', 'Call Centre', 'sendLink', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (51, 'Undefined Amount', 'Validate', 'valAmount', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (61, 'Undefined Product Names', 'Validate', 'valProducts', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (62, 'Undefined Product Quantities', 'Validate', 'valProducts', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (63, 'Undefined Product Prices', 'Validate', 'valProducts', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (64, 'Invalid Arrays sizes', 'Validate', 'valProducts', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (65, 'Array key not found in Product Quantities', 'Validate', 'valProducts', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (66, 'Array key not found in Product Prices', 'Validate', 'valProducts', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (67, 'Invalid URL found in array of Logo URLs', 'Validate', 'valProducts', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (71, 'Undefined Client Logo URL', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (72, 'Client Logo URL is too short', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (73, 'Client Logo URL is too long', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (74, 'Client Logo URL is malformed', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (75, 'Client Logo URL is Invalid, no Protocol specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (76, 'Client Logo URL is Invalid, no Host specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (77, 'Client Logo URL is Invalid, no Path specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (81, 'Undefined CSS URL', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (82, 'CSS URL is too short', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (83, 'CSS URL is too long', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (84, 'CSS URL is malformed', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (85, 'CSS URL is Invalid, no Protocol specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (86, 'CSS URL is Invalid, no Host specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (87, 'CSS URL is Invalid, no Path specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (91, 'Undefined Accept URL', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (92, 'Accept URL is too short', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (93, 'Accept URL is too long', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (94, 'Accept URL is malformed', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (95, 'Accept URL is Invalid, no Protocol specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (96, 'Accept URL is Invalid, no Host specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (97, 'Accept URL is Invalid, no Path specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (101, 'Undefined Cancel URL', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (102, 'Cancel URL is too short', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (103, 'Cancel URL is too long', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (104, 'Cancel URL is malformed', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (105, 'Cancel URL is Invalid, no Protocol specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (106, 'Cancel URL is Invalid, no Host specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (107, 'Cancel URL is Invalid, no Path specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (111, 'Undefined Callback URL', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (112, 'Callback URL is too short', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (113, 'Callback URL is too long', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (114, 'Callback URL is malformed', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (115, 'Callback URL is Invalid, no Protocol specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (116, 'Callback URL is Invalid, no Host specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (117, 'Callback URL is Invalid, no Path specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (121, 'Undefined Return URL', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (122, 'Return URL is too short', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (123, 'Return URL is too long', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (124, 'Return URL is malformed', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (125, 'Return URL is Invalid, no Protocol specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (126, 'Return URL is Invalid, no Host specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (127, 'Return URL is Invalid, no Path specified', 'Validate', 'valURL', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (131, 'Undefined Language', 'Validate', 'valLanguage', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (132, 'Invalid Language', 'Validate', 'valLanguage', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (133, 'Language not supported', 'Validate', 'valLanguage', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1001, 'Input Valid', 'API', 'Controller', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1002, 'Products', 'Call Centre', 'logProducts', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1003, 'Client Variables', 'API', 'logClientVars', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1004, 'Delivery Information', 'Shop', 'logDeliveryInfo', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1005, 'Shipping Information', 'Shop', 'logShippingInfo', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1010, 'Message accepted by GoMobile', 'Call Centre', 'sendLink', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1011, 'Unable to connect to GoMobile', 'Call Centre', 'sendLink', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1012, 'Message rejected by GoMobile', 'Call Centre', 'sendLink', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1020, 'Payment link Constructed', 'Call Centre', 'constLink', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1021, 'Payment link Activated', 'Payment', 'Overview', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1990, 'Callback Accepted', 'Callback', 'send', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1991, 'Callback Constructed', 'Callback', 'send', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1993, 'Callback Connection Failed', 'Callback', 'send', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1994, 'Callback Transmission Failed', 'Callback', 'send', '2008-02-22 18:41:59.280496', '2008-02-22 18:41:59.280496', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1030, 'E-Mail accepted', 'Callback', 'sendEMailReceipt', '2008-03-17 14:27:59.814434', '2008-03-17 14:27:59.814434', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1031, 'E-Mail Rejected', 'Callback', 'sendEMailReceipt', '2008-03-17 14:27:59.814434', '2008-03-17 14:27:59.814434', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1022, 'Payment link Resent as Embedded', 'SurePay', 'produceSurePays', '2009-03-19 10:10:29.352906', '2009-03-19 10:10:29.352906', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1023, 'Payment link Resent as WAP Push', 'SurePay', 'produceSurePays', '2009-03-19 10:10:29.352906', '2009-03-19 10:10:29.352906', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1029, 'Client Customer Service Notified', 'SurePay', 'produceSurePays', '2009-03-19 10:10:29.352906', '2009-03-19 10:10:29.352906', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1992, 'Callback Connected', 'Callback', 'send', '2008-02-22 18:41:59.280496', '2010-05-20 18:37:01.260827', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1995, 'Callback Rejected', 'Callback', 'send', '2008-02-22 18:41:59.280496', '2010-05-20 18:37:44.203619', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (2000, 'Payment authorized by PSP', 'Callback', 'completeTransaction', '2008-02-22 18:41:59.280496', '2010-05-20 18:38:08.410528', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (2001, 'Payment captured by PSP', 'Callback', 'completeTransaction', '2008-05-17 13:57:33.11949', '2010-05-20 18:38:13.51148', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (2010, 'Payment rejected by PSP', 'Callback', 'completeTransaction', '2008-03-24 19:08:58.071781', '2010-05-20 18:38:18.04145', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (31, 'Undefined Mobile Number', 'Validate', 'valAddress', '2008-02-22 18:41:59.280496', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (32, 'Mobile Number is too short', 'Validate', 'valAddress', '2008-02-22 18:41:59.280496', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (33, 'Mobile Number is too long', 'Validate', 'valAddress', '2008-02-22 18:41:59.280496', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (52, 'Amount is too small', 'Validate', 'valAmount', '2008-02-22 18:41:59.280496', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (53, 'Amount is too great', 'Validate', 'valAmount', '2008-02-22 18:41:59.280496', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (151, 'Undefined flag for Auto Store Card', 'Validate', 'valBoolean', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (152, 'Invalid flag for Auto Store Card', 'Validate', 'valBoolean', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (161, 'Undefined Icon URL', 'Validate', 'valURL', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (162, 'Icon URL is too short', 'Validate', 'valURL', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (163, 'Icon URL is too long', 'Validate', 'valURL', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (164, 'Icon URL is malformed', 'Validate', 'valURL', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (165, 'Icon URL is Invalid, no Protocol specified', 'Validate', 'valURL', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (166, 'Icon URL is Invalid, no Host specified', 'Validate', 'valURL', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (167, 'Icon URL is Invalid, no Path specified', 'Validate', 'valURL', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (171, 'Undefined mPoint ID', 'Validate', 'valmPointID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (172, 'Invalid mPoint ID', 'Validate', 'valmPointID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (173, 'Transaction not found for mPoint ID', 'Validate', 'valmPointID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (174, 'Transaction for mPoint ID has been disabled', 'Validate', 'valmPointID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (175, 'Payment Rejected for Transaction', 'Validate', 'valmPointID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (176, 'Payment already Captured for Transaction', 'Validate', 'valmPointID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (181, 'Undefined Order ID', 'Validate', 'valOrderID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (182, 'Transaction not found', 'Validate', 'valOrderID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (183, 'Order ID doesn''t match Transaction', 'Validate', 'valOrderID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (184, 'Transaction Disabled', 'Validate', 'valOrderID', '2010-05-27 12:25:11.315624', '2010-05-27 12:25:11.315624', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (2009, 'Ticket Created', 'Callback', '', '2010-09-29 15:33:01.576623', '2010-09-29 15:33:01.576623', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (191, 'Undefined Markup Language', 'Validate', 'valMarkupLanguage', '2011-02-04 21:18:40.844732', '2011-02-04 21:18:40.844732', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (192, 'Markup Language not found in Template', 'Validate', 'valMarkupLanguage', '2011-02-04 21:18:40.862506', '2011-02-04 21:18:40.862506', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (2008, 'Payment with account', 'Payment', '', '2011-02-04 21:18:40.86314', '2011-02-04 21:18:40.86314', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (2011, 'Payment declined', 'Payment', 'capture', '2011-02-04 21:18:40.863792', '2011-02-04 21:18:40.863792', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (2019, 'Payment duplicated', 'Payment', 'completeTransaction', '2011-02-04 21:18:40.864444', '2011-02-04 21:18:40.864444', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (2002, 'Payment Cancelled', 'Cancel', '', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (2003, 'Payment Refunded', 'Refund', '', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (177, 'Payment already Refunded for Transaction', 'Refund', '', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (179, 'Payment in invalid State for Transaction', '', '', '2011-11-28 22:45:42.587193', '2011-11-28 22:45:42.587193', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (141, 'Undefined E-Mail address', 'Validate', 'valEMail', '2011-12-12 06:59:03.789411', '2011-12-12 06:59:03.789411', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (142, 'E-Mail address is too short', 'Validate', 'valEMail', '2011-12-12 06:59:03.789411', '2011-12-12 06:59:03.789411', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (143, 'E-Mail address is too long', 'Validate', 'valEMail', '2011-12-12 06:59:03.789411', '2011-12-12 06:59:03.789411', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (144, 'E-Mail address contains invalid characters', 'Validate', 'valEMail', '2011-12-12 06:59:03.789411', '2011-12-12 06:59:03.789411', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (145, 'E-Mail has an invalid form', 'Validate', 'valEMail', '2011-12-12 06:59:03.789411', '2011-12-12 06:59:03.789411', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1999, 'Callback retried', 'Callback', 'SurePay', '2014-08-28 21:15:52.931089', '2014-08-28 21:15:52.931089', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1009, 'Payment Initialized with Payment Service Provider', 'mApp', 'initialize', '2012-07-30 17:25:50.611074', '2012-07-30 17:25:50.611074', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1800, 'Transfer Completed', 'Transfer', 'makeTransfer', '2013-07-03 13:11:26.588864', '2013-07-03 13:11:26.588864', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1808, 'Transfer Pending', 'Transfer', 'makeTransfer', '2013-07-03 13:11:26.588864', '2013-07-03 13:11:26.588864', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1809, 'Transfer Cancelled', 'Transfer', 'cancelTransfer', '2013-07-03 13:11:26.588864', '2013-07-03 13:11:26.588864', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (1, 'Reminder sent as Push Notification', NULL, NULL, '2014-11-19 11:04:53.047055', '2014-11-19 11:04:53.047055', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (201, 'Undefined Auth URL', 'Validate', 'valURL', '2013-08-05 11:23:33.934246', '2013-08-05 11:23:33.934246', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (202, 'Auth URL is too short', 'Validate', 'valURL', '2013-08-05 11:23:33.934246', '2013-08-05 11:23:33.934246', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (203, 'Auth URL is too long', 'Validate', 'valURL', '2013-08-05 11:23:33.934246', '2013-08-05 11:23:33.934246', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (204, 'Auth URL is malformed', 'Validate', 'valURL', '2013-08-05 11:23:33.934246', '2013-08-05 11:23:33.934246', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (205, 'Auth URL is Invalid, no Protocol specified', 'Validate', 'valURL', '2013-08-05 11:23:33.934246', '2013-08-05 11:23:33.934246', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (206, 'Auth URL is Invalid, no Host specified', 'Validate', 'valURL', '2013-08-05 11:23:33.934246', '2013-08-05 11:23:33.934246', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (207, 'Auth URL is Invalid, no Path specified', 'Validate', 'valURL', '2013-08-05 11:23:33.934246', '2013-08-05 11:23:33.934246', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (208, 'URL domain doesn''t match configured URL', 'Validate', 'valURL', '2013-08-05 11:23:33.934246', '2013-08-05 11:23:33.934246', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (209, 'Auth URL must be configured for Client', 'Validate', 'valURL', '2013-08-05 11:23:33.934246', '2013-08-05 11:23:33.934246', true);
INSERT INTO state_tbl (id, name, module, func, created, modified, enabled) VALUES (210, 'Invalid Message Authentication Code (MAC)', 'Validate', 'valMAC', '2014-05-25 11:42:34.53579', '2014-05-25 11:42:34.53579', true);



SELECT pg_catalog.setval('state_tbl_id_seq', 1, true);


CREATE SCHEMA admin;


ALTER SCHEMA admin OWNER TO postgres;

SET search_path = admin, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 176 (class 1259 OID 553686)
-- Name: access_tbl; Type: TABLE; Schema: admin; Owner: postgres; Tablespace:
--

CREATE TABLE access_tbl (
  id integer NOT NULL,
  userid integer NOT NULL,
  clientid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE admin.access_tbl OWNER TO postgres;

--
-- TOC entry 177 (class 1259 OID 553692)
-- Name: access_tbl_id_seq; Type: SEQUENCE; Schema: admin; Owner: postgres
--

CREATE SEQUENCE access_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE admin.access_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 2272 (class 0 OID 0)
-- Dependencies: 177
-- Name: access_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: postgres
--

ALTER SEQUENCE access_tbl_id_seq OWNED BY access_tbl.id;


--
-- TOC entry 178 (class 1259 OID 553694)
-- Name: role_tbl; Type: TABLE; Schema: admin; Owner: postgres; Tablespace:
--

CREATE TABLE role_tbl (
  id integer NOT NULL,
  name character varying(100),
  assignable boolean DEFAULT true,
  note text,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE admin.role_tbl OWNER TO postgres;

--
-- TOC entry 179 (class 1259 OID 553704)
-- Name: role_tbl_id_seq; Type: SEQUENCE; Schema: admin; Owner: postgres
--

CREATE SEQUENCE role_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE admin.role_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 2275 (class 0 OID 0)
-- Dependencies: 179
-- Name: role_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: postgres
--

ALTER SEQUENCE role_tbl_id_seq OWNED BY role_tbl.id;


--
-- TOC entry 180 (class 1259 OID 553706)
-- Name: roleaccess_tbl; Type: TABLE; Schema: admin; Owner: postgres; Tablespace:
--

CREATE TABLE roleaccess_tbl (
  id integer NOT NULL,
  roleid integer NOT NULL,
  userid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE admin.roleaccess_tbl OWNER TO postgres;

--
-- TOC entry 181 (class 1259 OID 553712)
-- Name: roleaccess_tbl_id_seq; Type: SEQUENCE; Schema: admin; Owner: postgres
--

CREATE SEQUENCE roleaccess_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE admin.roleaccess_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 2278 (class 0 OID 0)
-- Dependencies: 181
-- Name: roleaccess_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: postgres
--

ALTER SEQUENCE roleaccess_tbl_id_seq OWNED BY roleaccess_tbl.id;


--
-- TOC entry 182 (class 1259 OID 553714)
-- Name: roleinfo_tbl; Type: TABLE; Schema: admin; Owner: postgres; Tablespace:
--

CREATE TABLE roleinfo_tbl (
  id integer NOT NULL,
  roleid integer NOT NULL,
  languageid integer NOT NULL,
  name character varying(100),
  note text,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE admin.roleinfo_tbl OWNER TO postgres;

--
-- TOC entry 183 (class 1259 OID 553723)
-- Name: roleinfo_tbl_id_seq; Type: SEQUENCE; Schema: admin; Owner: postgres
--

CREATE SEQUENCE roleinfo_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE admin.roleinfo_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 2281 (class 0 OID 0)
-- Dependencies: 183
-- Name: roleinfo_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: postgres
--

ALTER SEQUENCE roleinfo_tbl_id_seq OWNED BY roleinfo_tbl.id;


--
-- TOC entry 184 (class 1259 OID 553725)
-- Name: user_tbl; Type: TABLE; Schema: admin; Owner: postgres; Tablespace:
--

CREATE TABLE user_tbl (
  id integer NOT NULL,
  countryid integer NOT NULL,
  firstname character varying(50),
  lastname character varying(50),
  mobile character varying(15),
  email character varying(50),
  username character varying(50),
  passwd character varying(50),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true
);


ALTER TABLE admin.user_tbl OWNER TO postgres;

--
-- TOC entry 185 (class 1259 OID 553731)
-- Name: user_tbl_id_seq; Type: SEQUENCE; Schema: admin; Owner: postgres
--

CREATE SEQUENCE user_tbl_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


ALTER TABLE admin.user_tbl_id_seq OWNER TO postgres;

--
-- TOC entry 2284 (class 0 OID 0)
-- Dependencies: 185
-- Name: user_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: postgres
--

ALTER SEQUENCE user_tbl_id_seq OWNED BY user_tbl.id;


--
-- TOC entry 2116 (class 2604 OID 554138)
-- Name: id; Type: DEFAULT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY access_tbl ALTER COLUMN id SET DEFAULT nextval('access_tbl_id_seq'::regclass);


--
-- TOC entry 2121 (class 2604 OID 554139)
-- Name: id; Type: DEFAULT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY role_tbl ALTER COLUMN id SET DEFAULT nextval('role_tbl_id_seq'::regclass);


--
-- TOC entry 2125 (class 2604 OID 554140)
-- Name: id; Type: DEFAULT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY roleaccess_tbl ALTER COLUMN id SET DEFAULT nextval('roleaccess_tbl_id_seq'::regclass);


--
-- TOC entry 2129 (class 2604 OID 554141)
-- Name: id; Type: DEFAULT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY roleinfo_tbl ALTER COLUMN id SET DEFAULT nextval('roleinfo_tbl_id_seq'::regclass);


--
-- TOC entry 2133 (class 2604 OID 554142)
-- Name: id; Type: DEFAULT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY user_tbl ALTER COLUMN id SET DEFAULT nextval('user_tbl_id_seq'::regclass);


--
-- TOC entry 2135 (class 2606 OID 554203)
-- Name: access_pk; Type: CONSTRAINT; Schema: admin; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY access_tbl
ADD CONSTRAINT access_pk PRIMARY KEY (id);


--
-- TOC entry 2137 (class 2606 OID 554205)
-- Name: access_uq; Type: CONSTRAINT; Schema: admin; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY access_tbl
ADD CONSTRAINT access_uq UNIQUE (userid, clientid);


--
-- TOC entry 2139 (class 2606 OID 554207)
-- Name: role_pk; Type: CONSTRAINT; Schema: admin; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY role_tbl
ADD CONSTRAINT role_pk PRIMARY KEY (id);


--
-- TOC entry 2142 (class 2606 OID 554209)
-- Name: roleaccess_pk; Type: CONSTRAINT; Schema: admin; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY roleaccess_tbl
ADD CONSTRAINT roleaccess_pk PRIMARY KEY (id);


--
-- TOC entry 2144 (class 2606 OID 554211)
-- Name: roleaccess_uq; Type: CONSTRAINT; Schema: admin; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY roleaccess_tbl
ADD CONSTRAINT roleaccess_uq UNIQUE (roleid, userid);


--
-- TOC entry 2146 (class 2606 OID 554213)
-- Name: roleinfo_pk; Type: CONSTRAINT; Schema: admin; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY roleinfo_tbl
ADD CONSTRAINT roleinfo_pk PRIMARY KEY (id);


--
-- TOC entry 2148 (class 2606 OID 554215)
-- Name: roleinfo_uq; Type: CONSTRAINT; Schema: admin; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY roleinfo_tbl
ADD CONSTRAINT roleinfo_uq UNIQUE (roleid, languageid);


--
-- TOC entry 2152 (class 2606 OID 554217)
-- Name: user_pk; Type: CONSTRAINT; Schema: admin; Owner: postgres; Tablespace:
--

ALTER TABLE ONLY user_tbl
ADD CONSTRAINT user_pk PRIMARY KEY (id);


--
-- TOC entry 2140 (class 1259 OID 554332)
-- Name: role_uq; Type: INDEX; Schema: admin; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX role_uq ON role_tbl USING btree (lower((name)::text));


--
-- TOC entry 2149 (class 1259 OID 554333)
-- Name: user_email_uq; Type: INDEX; Schema: admin; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX user_email_uq ON user_tbl USING btree (countryid, upper((email)::text));


--
-- TOC entry 2150 (class 1259 OID 554334)
-- Name: user_mobile_uq; Type: INDEX; Schema: admin; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX user_mobile_uq ON user_tbl USING btree (countryid, mobile);


--
-- TOC entry 2153 (class 1259 OID 554335)
-- Name: user_username_uq; Type: INDEX; Schema: admin; Owner: postgres; Tablespace:
--

CREATE UNIQUE INDEX user_username_uq ON user_tbl USING btree (username);


--
-- TOC entry 2154 (class 2606 OID 554361)
-- Name: access2client_fk; Type: FK CONSTRAINT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY access_tbl
ADD CONSTRAINT access2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2155 (class 2606 OID 554366)
-- Name: access2user_fk; Type: FK CONSTRAINT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY access_tbl
ADD CONSTRAINT access2user_fk FOREIGN KEY (userid) REFERENCES user_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2156 (class 2606 OID 554371)
-- Name: roleaccess2role_fk; Type: FK CONSTRAINT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY roleaccess_tbl
ADD CONSTRAINT roleaccess2role_fk FOREIGN KEY (roleid) REFERENCES role_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2157 (class 2606 OID 554376)
-- Name: roleaccess2user_fk; Type: FK CONSTRAINT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY roleaccess_tbl
ADD CONSTRAINT roleaccess2user_fk FOREIGN KEY (userid) REFERENCES user_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2158 (class 2606 OID 554381)
-- Name: user2country_fk; Type: FK CONSTRAINT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY user_tbl
ADD CONSTRAINT user2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2270 (class 0 OID 0)
-- Dependencies: 12
-- Name: admin; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA admin FROM PUBLIC;
REVOKE ALL ON SCHEMA admin FROM postgres;
GRANT ALL ON SCHEMA admin TO postgres;
GRANT ALL ON SCHEMA admin TO postgres;


--
-- TOC entry 2271 (class 0 OID 0)
-- Dependencies: 176
-- Name: access_tbl; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON TABLE access_tbl FROM PUBLIC;
REVOKE ALL ON TABLE access_tbl FROM postgres;
GRANT ALL ON TABLE access_tbl TO postgres;
GRANT ALL ON TABLE access_tbl TO postgres;


--
-- TOC entry 2273 (class 0 OID 0)
-- Dependencies: 177
-- Name: access_tbl_id_seq; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON SEQUENCE access_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE access_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE access_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE access_tbl_id_seq TO postgres;


--
-- TOC entry 2274 (class 0 OID 0)
-- Dependencies: 178
-- Name: role_tbl; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON TABLE role_tbl FROM PUBLIC;
REVOKE ALL ON TABLE role_tbl FROM postgres;
GRANT ALL ON TABLE role_tbl TO postgres;
GRANT ALL ON TABLE role_tbl TO postgres;


--
-- TOC entry 2276 (class 0 OID 0)
-- Dependencies: 179
-- Name: role_tbl_id_seq; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON SEQUENCE role_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE role_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE role_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE role_tbl_id_seq TO postgres;


--
-- TOC entry 2277 (class 0 OID 0)
-- Dependencies: 180
-- Name: roleaccess_tbl; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON TABLE roleaccess_tbl FROM PUBLIC;
REVOKE ALL ON TABLE roleaccess_tbl FROM postgres;
GRANT ALL ON TABLE roleaccess_tbl TO postgres;
GRANT ALL ON TABLE roleaccess_tbl TO postgres;


--
-- TOC entry 2279 (class 0 OID 0)
-- Dependencies: 181
-- Name: roleaccess_tbl_id_seq; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON SEQUENCE roleaccess_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE roleaccess_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE roleaccess_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE roleaccess_tbl_id_seq TO postgres;


--
-- TOC entry 2280 (class 0 OID 0)
-- Dependencies: 182
-- Name: roleinfo_tbl; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON TABLE roleinfo_tbl FROM PUBLIC;
REVOKE ALL ON TABLE roleinfo_tbl FROM postgres;
GRANT ALL ON TABLE roleinfo_tbl TO postgres;
GRANT ALL ON TABLE roleinfo_tbl TO postgres;


--
-- TOC entry 2282 (class 0 OID 0)
-- Dependencies: 183
-- Name: roleinfo_tbl_id_seq; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON SEQUENCE roleinfo_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE roleinfo_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE roleinfo_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE roleinfo_tbl_id_seq TO postgres;


--
-- TOC entry 2283 (class 0 OID 0)
-- Dependencies: 184
-- Name: user_tbl; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON TABLE user_tbl FROM PUBLIC;
REVOKE ALL ON TABLE user_tbl FROM postgres;
GRANT ALL ON TABLE user_tbl TO postgres;
GRANT ALL ON TABLE user_tbl TO postgres;


--
-- TOC entry 2285 (class 0 OID 0)
-- Dependencies: 185
-- Name: user_tbl_id_seq; Type: ACL; Schema: admin; Owner: postgres
--

REVOKE ALL ON SEQUENCE user_tbl_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE user_tbl_id_seq FROM postgres;
GRANT ALL ON SEQUENCE user_tbl_id_seq TO postgres;
GRANT ALL ON SEQUENCE user_tbl_id_seq TO postgres;


-- from setup_pg_v1.88 --

INSERT INTO System.PSP_Tbl (id, name) VALUES (11, 'MobilePay');
INSERT INTO System.PSPCurrency_Tbl (id, pspid, countryid, name) SELECT 430, 11, 100, 'DKK';

INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (17, 'MobilePay', 15, -1, -1, -1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (11, 17);
-- INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.id * -1 AS pricepointid, 17 FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;


INSERT INTO System.URLType_Tbl (id, name) VALUES (4, 'Mobile Enterprise Servicebus');

ALTER TABLE client.client_tbl
ADD COLUMN transaction_ttl integer DEFAULT 0;
COMMENT ON COLUMN client.client_tbl.transaction_ttl
IS 'Transaction Time To Live in seconds';

-- Table: Client.InfoType_Tbl
-- Definition table for all information types: About Text, Contact Info, Terms & Conditions, Help Text, Reminder Message, OTP Message etc.
CREATE TABLE Client.InfoType_Tbl
(
  id			SERIAL,

  name		VARCHAR(100),
  note		TEXT,

  CONSTRAINT InfoType_PK PRIMARY KEY (id),
  LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX InfoType_UQ ON Client.InfoType_Tbl (Lower(name) );

-- Table: Client.Info_Tbl
-- Data table for all Client Information, texts may be in XHTML format
CREATE TABLE Client.Info_Tbl
(
  id			SERIAL,
  infotypeid	INT4 NOT NULL,	-- ID of the Information Type
  clientid	INT4 NOT NULL,	-- ID of the Client who owns the Info
  pspid		INT4,
  language	CHAR(2) DEFAULT 'gb',

  text		TEXT,			-- Information text

  CONSTRAINT Info_PK PRIMARY KEY (id),
  CONSTRAINT Info2InfoType_FK FOREIGN KEY (infotypeid) REFERENCES Client.InfoType_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT Info2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT Info2PSP_FK FOREIGN KEY (pspid) REFERENCES System.PSP_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
  LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX Info_PSP_UQ ON Client.Info_Tbl (infotypeid, clientid, language, pspid) WHERE pspid IS NOT NULL;
CREATE UNIQUE INDEX Info_UQ ON Client.Info_Tbl (infotypeid, clientid, language) WHERE pspid IS NULL;

-- Table: System.CardState_Tbl
CREATE TABLE System.CardState_Tbl
(
  id			SERIAL,
  name		VARCHAR(100),

  CONSTRAINT CardState_PK PRIMARY KEY (id),
  LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.CardState_Tbl TO postgres;
GRANT SELECT, UPDATE, USAGE ON TABLE System.CardState_Tbl_id_seq TO postgres;
/* ==================== SYSTEM SCHEMA END ==================== */

INSERT INTO System.CardState_Tbl (id, name) VALUES (1, 'Enabled');
INSERT INTO System.CardState_Tbl (id, name) VALUES (2, 'Disabled By Merchant');
INSERT INTO System.CardState_Tbl (id, name) VALUES (3, 'Disabled By PSP');
INSERT INTO System.CardState_Tbl (id, name) VALUES (4, 'Prerequisite not Met');
INSERT INTO System.CardState_Tbl (id, name) VALUES (5, 'Temporarily Unavailable');

ALTER TABLE Client.CardAccess_tbl ADD COLUMN stateid integer DEFAULT 1;
ALTER TABLE Client.CardAccess_tbl ADD CONSTRAINT CardAccess2CardState_FK FOREIGN KEY (stateid) REFERENCES System.CardState_Tbl ON UPDATE CASCADE ON DELETE RESTRICT;

CREATE TABLE System.IINAction_Tbl
(
  id			INT4,

  name		VARCHAR(100),
  note		TEXT,

  CONSTRAINT IINAction_PK PRIMARY KEY (id),
  LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;

CREATE UNIQUE INDEX IINAction_UQ ON System.IINAction_Tbl (Lower(name) );

GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE System.IINAction_Tbl TO postgres;


-- Table: Client.IINList_Tbl
-- Data table for each client's lists of actions taken for a range of Issuer Identification Numbers
CREATE TABLE Client.IINList_Tbl
(
  id			SERIAL,
  iinactionid	INT4 NOT NULL,	-- ID of the action to take for the defined range of Issuer Identification Numbers
  clientid	INT4 NOT NULL,	-- ID of the client for which the specified action is defined

  min			INT8,
  max			INT8,

  CONSTRAINT IINList_PK PRIMARY KEY (id),
  CONSTRAINT IINList2Client_FK FOREIGN KEY (clientid) REFERENCES Client.Client_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT IINList2IINAction_FK FOREIGN KEY (iinactionid) REFERENCES System.IINAction_Tbl ON UPDATE CASCADE ON DELETE CASCADE,
  LIKE Template.General_Tbl INCLUDING DEFAULTS
) WITHOUT OIDS;


GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Client.IINList_Tbl TO postgres;
GRANT SELECT, UPDATE, INSERT ON TABLE Client.IINList_Tbl_id_seq TO postgres;

CREATE INDEX IINRanges_Idx ON Client.IINList_Tbl (clientid, min, max);


INSERT INTO System.IINAction_Tbl (id, name, note) VALUES (1, 'Blocked', 'Used for blocking cards based on their Issuer Identification Number');
INSERT INTO System.IINAction_Tbl (id, name, note) VALUES (2, 'Whitelisted', 'Used for whitelisting cards based on their Issuer Identification Number');

-- Create link to EndUser.Card_Tbl
ALTER TABLE EndUser.Card_Tbl ADD COLUMN chargetypeid INT4 DEFAULT 0;

CREATE INDEX Transaction_Created_Idx ON Log.Transaction_Tbl (created);


-- SETUP v1.88 --
INSERT INTO Client.InfoType_Tbl (id, name, note) VALUES (1, 'PSP Message', 'A message which is shown during payment through a specific Payment Service Provider');

-- SETUP v1.89 --

/* ========== CONFIGURE DSB PSP AND VOUCHER PAYMENT ========= */
INSERT INTO System.PSP_Tbl (id, name) VALUES (19, 'DSB');
INSERT INTO System.PSPCurrency_Tbl (pspid, countryid, name) VALUES (19, 100, 'DKK');
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (26, 'Voucher', 22, -1, -1, -1);
INSERT INTO System.PSPCard_Tbl (pspid, cardid) VALUES (19, 26);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.id * -1 AS pricepointid, 26 FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;

INSERT INTO Log.State_Tbl (id, name) VALUES (2007, 'Payment with voucher');
/* ========== CONFIGURE DSB PSP AND VOUCHER PAYMENT ========= */

------- MASTER v1.90 ---
/* ==================== Client SCHEMA START ==================== */
ALTER TABLE Client.CardAccess_tbl ADD position integer default NULL;
ALTER TABLE Client.CardAccess_tbl
DROP CONSTRAINT cardaccess_uq;

/*ALTER TABLE Client.CardAccess_tbl
ADD CONSTRAINT cardaccess_uq UNIQUE (clientid, cardid);

ALTER TABLE Client.CardAccess_tbl
ADD CONSTRAINT cardaccess_card_country_uq UNIQUE (clientid, cardid, countryid);
*/

CREATE UNIQUE INDEX cardaccess_uq ON Client.CardAccess_tbl (clientid, cardid) WHERE countryid IS NULL;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON Client.CardAccess_tbl (clientid, cardid, countryid) WHERE countryid IS NOT NULL;

/* ==================== Client SCHEMA END ==================== */

------- MASTER v1.93 ---
/* ==================== Client SCHEMA START ==================== */
ALTER TABLE Client.Client_Tbl ADD num_masked_digits INT4 DEFAULT 4;
ALTER TABLE Client.Client_Tbl ADD CONSTRAINT MaskedDigits_Chk CHECK (0 <= num_masked_digits AND num_masked_digits <= 4);
/* ==================== Client SCHEMA END ==================== */

------- MASTER v1.94 ---
/* ==================== CLIENT SCHEMA START ==================== */
ALTER TABLE Client.MerchantAccount_Tbl ALTER name TYPE VARCHAR(255);
/* ==================== CLIENT SCHEMA END ==================== */

------- MASTER v1.95 ---
/* ==================== Client SCHEMA START ==================== */
ALTER TABLE Client.Client_Tbl ADD declineurl character varying(255);
/* ==================== Client SCHEMA END ==================== */

-- MASTER V1.97 ---
/* ==================== CLIENT.CLIENT_TBL SCHEMA START ==================== */
ALTER TABLE Client.Client_Tbl ADD COLUMN salt VARCHAR(20);
/* ==================== CLIENT.CLIENT_TBL SCHEMA END ==================== */

-- SETUP V1.99 ---
/* ========== Mobile Optimized 3D Secure BEGIN ========== */
INSERT INTO System.URLType_Tbl (id, name) VALUES (12, 'Parse 3D Secure Challenge URL');
INSERT INTO Log.State_Tbl (id, name) VALUES (1100, '3D Secure Activated');
/* ========== Mobile Optimized 3D Secure END ========== */

/* ==================== LOG.ORDER_TBL SCHEMA START ==================== */
CREATE TABLE log.order_tbl
(
  id serial NOT NULL,
  txnid integer NOT NULL,
  countryid integer NOT NULL,
  amount integer,
  productsku character varying(40),
  productname character varying(40),
  productdescription text,
  productimageurl character varying(255),
  points integer,
  reward integer,
  quantity integer,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT order_pk PRIMARY KEY (id),
  CONSTRAINT order2country_fk FOREIGN KEY (countryid)
      REFERENCES system.country_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT order2txn_fk FOREIGN KEY (txnid)
      REFERENCES log.transaction_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT
)
WITH (
  OIDS=FALSE
);

CREATE INDEX order_created_idx
  ON log.order_tbl
  USING btree
  (created);

CREATE INDEX order_transaction_idx
  ON log.order_tbl
  USING btree
  (id, txnid);
  
/* ==================== LOG.ORDER_TBL SCHEMA START ==================== */




-------------  Airline Data ----------------



-- Type: log.additional_data_ref

-- DROP TYPE log.additional_data_ref;

CREATE TYPE log.additional_data_ref AS ENUM
   ('Flight',
    'Passenger');
    
-- Table: log.additional_data_tbl

-- DROP TABLE log.additional_data_tbl;

CREATE TABLE log.additional_data_tbl
(
 id serial NOT NULL,
  name character varying(20),
  value character varying(20),
  type log.additional_data_ref,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  CONSTRAINT additional_data_pk PRIMARY KEY (id)
)
WITHOUT OIDS;


-- Table: log.flight_tbl

-- DROP TABLE log.flight_tbl;

CREATE TABLE log.flight_tbl
(
  id serial NOT NULL,
  service_class character varying(10) NOT NULL,
  departure_airport character varying(10) NOT NULL,
  arrival_airport character varying(10) NOT NULL,
  airline_code character varying(10) NOT NULL,
  order_id integer NOT NULL,
  arrival_date timestamp without time zone NOT NULL,
  departure_date timestamp without time zone NOT NULL,
  created timestamp without time zone NOT NULL DEFAULT now(),
  modified timestamp without time zone NOT NULL DEFAULT now(),
  additional_data_ref integer NOT NULL,
  CONSTRAINT flight_pk PRIMARY KEY (id),
  CONSTRAINT order_fk FOREIGN KEY (order_id)
      REFERENCES log.order_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ref_additional_fk FOREIGN KEY (additional_data_ref)
      REFERENCES log.additional_data_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITHOUT OIDS;

  
  
  
-- Table: log.passenger_tbl

-- DROP TABLE log.passenger_tbl;

CREATE TABLE log.passenger_tbl
(
   id serial NOT NULL,
  first_name character varying(20) NOT NULL,
  last_name character varying(20) NOT NULL,
  type character varying(10) NOT NULL,
  order_id integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  additional_data_ref integer NOT NULL,
  CONSTRAINT passenger_pk PRIMARY KEY (id),
  CONSTRAINT order_fk FOREIGN KEY (order_id)
      REFERENCES log.order_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ref_additional_fk FOREIGN KEY (additional_data_ref)
      REFERENCES log.additional_data_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITHOUT OIDS;

-- Table: system.processortype_tbl

-- DROP TABLE system.processortype_tbl;

CREATE TABLE system.processortype_tbl
(
  id serial NOT NULL,
  name character varying(50),
  CONSTRAINT id_pk PRIMARY KEY (id),
  CONSTRAINT iduk UNIQUE (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE system.processortype_tbl
  OWNER TO postgres;

-- Column: system_type

-- ALTER TABLE system.psp_tbl DROP COLUMN system_type;

ALTER TABLE system.psp_tbl ADD COLUMN system_type integer;

   -- Insert data : system.processortype_tbl;

  INSERT INTO system.processortype_tbl(id, name) VALUES (1, 'PSP');
  INSERT INTO system.processortype_tbl(id, name) VALUES (2, 'Bank');
  INSERT INTO system.processortype_tbl(id, name) VALUES (3, 'Wallet');
  INSERT INTO system.processortype_tbl(id, name) VALUES (4, 'APM');

   -- Insert data : system.psp_tbl
   -- Value : system_type;

    UPDATE system.psp_tbl SET system_type=1 WHERE id=0;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=1;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=2;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=3;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=4;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=5;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=6;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=7;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=8;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=9;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=10;
    UPDATE system.psp_tbl SET system_type=3 WHERE id=11;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=12;
    UPDATE system.psp_tbl SET system_type=3 WHERE id=13;
    UPDATE system.psp_tbl SET system_type=3 WHERE id=14;
    UPDATE system.psp_tbl SET system_type=3 WHERE id=15;
    UPDATE system.psp_tbl SET system_type=3 WHERE id=16;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=17;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=18;
    UPDATE system.psp_tbl SET system_type=2 WHERE id=19;
    UPDATE system.psp_tbl SET system_type=3 WHERE id=20;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=21;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=22;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=23;
    UPDATE system.psp_tbl SET system_type=4 WHERE id=24;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=25;
    UPDATE system.psp_tbl SET system_type=1 WHERE id=26;
    UPDATE system.psp_tbl SET system_type=2 WHERE id=27;
    UPDATE system.psp_tbl SET system_type=2 WHERE id=28;

-- Foreign Key: system.psptoproccessingtype_fk
-- ALTER TABLE system.psp_tbl DROP CONSTRAINT psptoproccessingtype_fk;
ALTER TABLE system.psp_tbl ALTER COLUMN system_type SET NOT NULL;

ALTER TABLE system.psp_tbl
  ADD CONSTRAINT psptoproccessingtype_fk FOREIGN KEY (system_type)
      REFERENCES system.processortype_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

-- Table: client.gomobileconfiguration_tbl

-- DROP TABLE client.gomobileconfiguration_tbl;

CREATE TABLE client.gomobileconfiguration_tbl
(
  id serial NOT NULL,
  clientid integer NOT NULL,
  name character varying(100),
  value character varying(100),
  channel character varying(5),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT gomobileconfiguration_pk PRIMARY KEY (id),
  CONSTRAINT gomobileconfiguration2client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.gomobileconfiguration_tbl
  OWNER TO postgres;




/*---------START : ADDED CHANGE FOR SUPPORTING CURRENCY SCHEMA-------------*/
-- Table: system.currency_tbl

-- DROP TABLE system.currency_tbl;

CREATE TABLE system.currency_tbl
(
  id serial NOT NULL,
  name character varying(100),
  code character(3),
  decimals integer,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT currency_pk PRIMARY KEY (id)
)
WITH (
OIDS=FALSE
);
ALTER TABLE system.currency_tbl
  OWNER TO postgres;


ALTER TABLE system.country_tbl ADD COLUMN alpha2code character(2);
ALTER TABLE system.country_tbl ADD COLUMN alpha3code character(3);
ALTER TABLE system.country_tbl ADD COLUMN code integer;
ALTER TABLE system.country_tbl ADD COLUMN currencyid integer;
ALTER TABLE system.country_tbl ADD CONSTRAINT Country2Currency_FK FOREIGN KEY (currencyid) REFERENCES System.Currency_Tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE system.country_tbl DROP COLUMN currency;

/*---------END : ADDED CHANGE FOR SUPPORTING CURRENCY SCHEMA-------------*/

/* ==================== ALTER TRANSACTION LOG START ==================== */
ALTER TABLE Log.Transaction_Tbl ADD COLUMN currencyid integer;
ALTER TABLE Log.Transaction_Tbl ADD CONSTRAINT Txn2Currency_FK FOREIGN KEY (currencyid)
REFERENCES System.currency_tbl (id)
ON UPDATE CASCADE ON DELETE RESTRICT;
/* ==================== ALTER TRANSACTION LOG END ==================== */

ALTER TABLE system.pspcurrency_tbl ADD COLUMN currencyid integer;
ALTER TABLE system.pspcurrency_tbl  ADD CONSTRAINT Psp2Currency_FK FOREIGN KEY (currencyid)
REFERENCES System.currency_tbl (id);



/* ================ Update pricepoint table  ===================*/

ALTER TABLE system.pricepoint_tbl ADD COLUMN currencyid integer;
ALTER TABLE system.pricepoint_tbl  ADD CONSTRAINT Price2Currency_FK FOREIGN KEY (currencyid)
REFERENCES System.currency_tbl (id);
ALTER TABLE system.pricepoint_tbl DROP COLUMN countryid;


/* ========= Create client.countrycurrency_tbl =============== */

-- Table: client.countrycurrency_tbl

-- DROP TABLE client.countrycurrency_tbl;

CREATE TABLE client.countrycurrency_tbl
(
  id serial NOT NULL,
  clientid integer NOT NULL,
  countryid integer NOT NULL,
  currencyid integer NOT NULL,
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean,
  CONSTRAINT countrycurrency_pk PRIMARY KEY (id),
  CONSTRAINT client_fk FOREIGN KEY (clientid)
  REFERENCES client.client_tbl (id) MATCH SIMPLE
  ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT country_fk FOREIGN KEY (countryid)
  REFERENCES system.country_tbl (id) MATCH SIMPLE
  ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT currency_fk FOREIGN KEY (currencyid)
  REFERENCES system.currency_tbl (id) MATCH SIMPLE
  ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
OIDS=FALSE
);
ALTER TABLE client.countrycurrency_tbl
  OWNER TO postgres;

ALTER TABLE log.transaction_tbl ADD deviceid VARCHAR(50) NULL;


INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (208,'Danish Krone','DKK',2);
UPDATE System.Country_Tbl SET alpha2code = 'DK', alpha3code = 'DNK', code = 208, currencyid = 208 WHERE id = 100;

INSERT INTO System.Currency_Tbl (id, name, code, decimals) VALUES (840,'US Dollar','USD',2);
UPDATE System.Country_Tbl SET alpha2code = 'US', alpha3code = 'USA', code = 840, currencyid = 840 WHERE id = 200;

DELETE FROM system.cardpricing_tbl;
DELETE FROM system.pricepoint_tbl;

INSERT INTO system.pricepoint_tbl (id, currencyid, amount) values (-208,208,-1) ;
INSERT INTO system.pricepoint_tbl (id, currencyid, amount) values (-840,840,-1) ;

UPDATE system.pspcurrency_tbl pc SET currencyid = (SELECT currencyid FROM system.country_tbl WHERE id = pc.countryid) ;

/* Run Alter Scripts to update currency Id before deleting country id column */
ALTER TABLE system.pspcurrency_tbl DROP COLUMN countryid ;


CREATE TABLE client.additionalproperty_tbl
(
  id serial NOT NULL,
  key character varying(200) NOT NULL,
  value character varying(4000) NOT NULL,
  modified timestamp without time zone DEFAULT now(),
  created timestamp without time zone DEFAULT now(),
  enabled boolean NOT NULL DEFAULT true,
  externalid integer NOT NULL,
  type VARCHAR(20) NOT NULL,
  CONSTRAINT additionalprop_pk PRIMARY KEY (id)
)
WITH (
OIDS=FALSE
);
ALTER TABLE client.additionalproperty_tbl
  OWNER TO postgres;



/* Update process type 2's name from Bank to Acquirer*/

UPDATE system.processortype_tbl SET name = 'Acquirer' WHERE id = 2;

CREATE TABLE system.paymenttype_tbl
(
  id SERIAL PRIMARY KEY NOT NULL,
  name VARCHAR(50) NOT NULL
);
CREATE UNIQUE INDEX paymenttype_tbl_name_uindex ON system.paymenttype_tbl (name);

INSERT INTO system.paymenttype_tbl (name) VALUES ('Card');
INSERT INTO system.paymenttype_tbl (name) VALUES ('Voucher');
INSERT INTO system.paymenttype_tbl (name) VALUES ('Wallet');
INSERT INTO system.paymenttype_tbl (name) VALUES ('APM');
INSERT INTO system.paymenttype_tbl (name) VALUES ('Card Token');


ALTER TABLE system.card_tbl ADD paymenttype INTEGER DEFAULT 1 NOT NULL;

ALTER TABLE system.card_tbl
  ADD CONSTRAINT card_tbl_paymenttype_tbl_id_fk
FOREIGN KEY (paymenttype) REFERENCES system.paymenttype_tbl (id);

ALTER TABLE log.transaction_tbl ADD mask VARCHAR(20) NULL;
ALTER TABLE log.transaction_tbl ADD expiry VARCHAR(5) NULL;
ALTER TABLE log.transaction_tbl ADD token CHARACTER VARYING(512) COLLATE pg_catalog."default" NULL;
ALTER TABLE log.transaction_tbl ADD authoriginaldata CHARACTER VARYING(512) NULL;

ALTER TABLE enduser.account_tbl ADD COLUMN pushid character varying(100);

/*  ===========  START : Adding column attempts to Log.Transaction_Tbl  ==================  */
ALTER TABLE Log.Transaction_Tbl ADD COLUMN attempt integer DEFAULT 1;
/*  ===========  END : Adding column attempts to Log.Transaction_Tbl  ==================  */

/*  ===========  START : Adding column preferred to Client.CardAccess_Tbl  ==================  */
ALTER TABLE Client.CardAccess_Tbl ADD COLUMN preferred boolean DEFAULT false;
/*  ===========  END : Adding column preferred to Client.CardAccess_Tbl  ==================  */

/*  ===========  START : Adding communicationchannels to Client.Client_Tbl  ==================  */
ALTER TABLE client.client_tbl ADD COLUMN communicationchannels integer DEFAULT 0;
/*  ===========  END : Adding communicationchannels to Client.Client_Tbl  ==================  */