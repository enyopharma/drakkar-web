--
-- PostgreSQL database dump
--

-- Dumped from database version 12.6 (Ubuntu 12.6-0ubuntu0.20.04.1)
-- Dumped by pg_dump version 12.6 (Ubuntu 12.6-0ubuntu0.20.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: pg_trgm; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pg_trgm WITH SCHEMA public;


--
-- Name: EXTENSION pg_trgm; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION pg_trgm IS 'text similarity measurement and index searching based on trigrams';


--
-- Name: constrain_taxon(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.constrain_taxon() RETURNS integer
    LANGUAGE sql STRICT SECURITY DEFINER
    AS $$
CREATE RULE rule_taxon_i
       AS ON INSERT TO taxon
       WHERE (
             SELECT taxon_id FROM taxon 
             WHERE ncbi_taxon_id = new.ncbi_taxon_id
             )
       	     IS NOT NULL
       DO INSTEAD NOTHING
;
SELECT 1;
$$;


--
-- Name: unconstrain_taxon(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.unconstrain_taxon() RETURNS integer
    LANGUAGE sql STRICT SECURITY DEFINER
    AS $$
DROP RULE rule_taxon_i ON taxon;
SELECT 1;
$$;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: associations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.associations (
    id integer NOT NULL,
    run_id integer NOT NULL,
    pmid bigint NOT NULL,
    state character varying(10) DEFAULT 'pending'::character varying NOT NULL,
    annotation text DEFAULT ''::text NOT NULL,
    updated_at timestamp(0) without time zone DEFAULT now() NOT NULL,
    CONSTRAINT associations_state_check CHECK (((state)::text = ANY (ARRAY[('pending'::character varying)::text, ('selected'::character varying)::text, ('discarded'::character varying)::text, ('curated'::character varying)::text])))
);


--
-- Name: associations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.associations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: associations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.associations_id_seq OWNED BY public.associations.id;


--
-- Name: descriptions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.descriptions (
    id integer NOT NULL,
    stable_id character(10) NOT NULL,
    version smallint NOT NULL,
    association_id integer NOT NULL,
    method_id integer NOT NULL,
    protein1_id integer NOT NULL,
    name1 character varying(32) NOT NULL,
    start1 integer NOT NULL,
    stop1 integer NOT NULL,
    mapping1 json NOT NULL,
    protein2_id integer NOT NULL,
    name2 character varying(32) NOT NULL,
    start2 integer NOT NULL,
    stop2 integer NOT NULL,
    mapping2 json NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now() NOT NULL,
    deleted_at timestamp(0) without time zone
);


--
-- Name: methods; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.methods (
    id integer NOT NULL,
    psimi_id character varying(7) NOT NULL,
    name character varying(255) NOT NULL,
    search text
);


--
-- Name: proteins; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.proteins (
    id integer NOT NULL,
    type character(1) NOT NULL,
    ncbi_taxon_id integer NOT NULL,
    accession character varying(10) NOT NULL,
    name character varying(255) NOT NULL,
    description text NOT NULL,
    version character(7) NOT NULL,
    sequences jsonb NOT NULL,
    CONSTRAINT proteins_type_check CHECK (((type)::text = ANY (ARRAY[('h'::character varying)::text, ('v'::character varying)::text])))
);


--
-- Name: proteins_versions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.proteins_versions (
    accession character varying(10) NOT NULL,
    version character(7) NOT NULL,
    current_version character(7) NOT NULL,
    names character varying[] NOT NULL,
    features jsonb NOT NULL,
    search text
);


--
-- Name: runs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.runs (
    id integer NOT NULL,
    type character(2) NOT NULL,
    name text NOT NULL,
    populated boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now() NOT NULL,
    info text DEFAULT ''::text NOT NULL,
    CONSTRAINT runs_type_check CHECK (((type)::text = ANY (ARRAY[('hh'::character varying)::text, ('vh'::character varying)::text])))
);


--
-- Name: taxon_pk_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.taxon_pk_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: taxon; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.taxon (
    taxon_id integer DEFAULT nextval('public.taxon_pk_seq'::regclass) NOT NULL,
    ncbi_taxon_id integer,
    parent_taxon_id integer,
    node_rank character varying(32),
    genetic_code smallint,
    mito_genetic_code smallint,
    left_value integer,
    right_value integer
);


--
-- Name: taxon_name; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.taxon_name (
    taxon_id integer NOT NULL,
    name character varying(255) NOT NULL,
    name_class character varying(32) NOT NULL
);


--
-- Name: dataset; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW public.dataset AS
 SELECT d.stable_id,
    r.type,
    r.id AS run_id,
    r.name AS run,
    a.id AS association_id,
    a.state,
    a.annotation,
    d.id AS description_id,
    a.pmid,
    m.id AS method_id,
    m.psimi_id,
    m.name AS method,
    p1.id AS protein1_id,
    p1.type AS type1,
    p1.accession AS accession1,
    p1.name AS name1,
    d.start1,
    d.stop1,
    p1.description AS description1,
    'Homo sapiens'::text AS taxon1,
    4480672 AS left_value1,
    4480677 AS right_value1,
    d.mapping1,
    p1.version AS original_version1,
    v1.current_version AS current_version1,
    (v1.current_version IS NULL) AS is_obsolete1,
    p2.id AS protein2_id,
    p2.type AS type2,
    p2.accession AS accession2,
    d.name2,
    d.start2,
    d.stop2,
    p2.description AS description2,
    COALESCE(tn2.name, 'Obsolete'::character varying) AS taxon2,
    t2.left_value AS left_value2,
    t2.right_value AS right_value2,
    d.mapping2,
    p2.version AS original_version2,
    v2.current_version AS current_version2,
    (v2.current_version IS NULL) AS is_obsolete2,
    d.created_at,
    d.deleted_at
   FROM public.runs r,
    public.associations a,
    public.descriptions d,
    public.methods m,
    (public.proteins p1
     LEFT JOIN public.proteins_versions v1 ON ((((p1.accession)::text = (v1.accession)::text) AND (p1.version = v1.version)))),
    (public.proteins p2
     LEFT JOIN public.proteins_versions v2 ON ((((p2.accession)::text = (v2.accession)::text) AND (p2.version = v2.version)))),
    (public.taxon t2
     LEFT JOIN public.taxon_name tn2 ON (((t2.taxon_id = tn2.taxon_id) AND ((tn2.name_class)::text = 'scientific name'::text))))
  WHERE ((r.id = a.run_id) AND (a.id = d.association_id) AND (m.id = d.method_id) AND (p1.id = d.protein1_id) AND (p2.id = d.protein2_id) AND (p2.ncbi_taxon_id = t2.ncbi_taxon_id));


--
-- Name: dataset_coronaviridae; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW public.dataset_coronaviridae AS
 SELECT dataset.stable_id,
    dataset.type,
    dataset.run_id,
    dataset.run,
    dataset.association_id,
    dataset.state,
    dataset.annotation,
    dataset.description_id,
    dataset.pmid,
    dataset.method_id,
    dataset.psimi_id,
    dataset.method,
    dataset.protein1_id,
    dataset.type1,
    dataset.accession1,
    dataset.name1,
    dataset.start1,
    dataset.stop1,
    dataset.description1,
    dataset.taxon1,
    dataset.left_value1,
    dataset.right_value1,
    dataset.mapping1,
    dataset.original_version1,
    dataset.current_version1,
    dataset.is_obsolete1,
    dataset.protein2_id,
    dataset.type2,
    dataset.accession2,
    dataset.name2,
    dataset.start2,
    dataset.stop2,
    dataset.description2,
    dataset.taxon2,
    dataset.left_value2,
    dataset.right_value2,
    dataset.mapping2,
    dataset.original_version2,
    dataset.current_version2,
    dataset.is_obsolete2,
    dataset.created_at,
    dataset.deleted_at
   FROM public.dataset
  WHERE ((dataset.type = 'vh'::bpchar) AND (dataset.left_value2 >= 309095) AND (dataset.right_value2 <= 312416));


--
-- Name: descriptions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.descriptions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: descriptions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.descriptions_id_seq OWNED BY public.descriptions.id;


--
-- Name: keywords; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.keywords (
    id integer NOT NULL,
    type character(1) NOT NULL,
    pattern character varying(255) NOT NULL
);


--
-- Name: keywords_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.keywords_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: keywords_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.keywords_id_seq OWNED BY public.keywords.id;


--
-- Name: methods_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.methods_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: methods_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.methods_id_seq OWNED BY public.methods.id;


--
-- Name: proteins_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.proteins_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: proteins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.proteins_id_seq OWNED BY public.proteins.id;


--
-- Name: publications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.publications (
    pmid bigint NOT NULL,
    populated boolean DEFAULT false,
    metadata jsonb
);


--
-- Name: runs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.runs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: runs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.runs_id_seq OWNED BY public.runs.id;


--
-- Name: associations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.associations ALTER COLUMN id SET DEFAULT nextval('public.associations_id_seq'::regclass);


--
-- Name: descriptions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.descriptions ALTER COLUMN id SET DEFAULT nextval('public.descriptions_id_seq'::regclass);


--
-- Name: keywords id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keywords ALTER COLUMN id SET DEFAULT nextval('public.keywords_id_seq'::regclass);


--
-- Name: methods id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.methods ALTER COLUMN id SET DEFAULT nextval('public.methods_id_seq'::regclass);


--
-- Name: proteins id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proteins ALTER COLUMN id SET DEFAULT nextval('public.proteins_id_seq'::regclass);


--
-- Name: runs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.runs ALTER COLUMN id SET DEFAULT nextval('public.runs_id_seq'::regclass);


--
-- Name: associations associations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.associations
    ADD CONSTRAINT associations_pkey PRIMARY KEY (id);


--
-- Name: associations associations_run_id_pmid_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.associations
    ADD CONSTRAINT associations_run_id_pmid_key UNIQUE (run_id, pmid);


--
-- Name: descriptions descriptions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.descriptions
    ADD CONSTRAINT descriptions_pkey PRIMARY KEY (id);


--
-- Name: descriptions descriptions_stable_id_version_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.descriptions
    ADD CONSTRAINT descriptions_stable_id_version_id_key UNIQUE (stable_id, version);


--
-- Name: keywords keywords_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.keywords
    ADD CONSTRAINT keywords_pkey PRIMARY KEY (id);


--
-- Name: methods methods_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.methods
    ADD CONSTRAINT methods_pkey PRIMARY KEY (id);


--
-- Name: methods methods_psimi_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.methods
    ADD CONSTRAINT methods_psimi_id_key UNIQUE (psimi_id);


--
-- Name: proteins proteins_accession_version_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proteins
    ADD CONSTRAINT proteins_accession_version_key UNIQUE (accession, version);


--
-- Name: proteins proteins_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proteins
    ADD CONSTRAINT proteins_pkey PRIMARY KEY (id);


--
-- Name: proteins_versions proteins_versions_accession_current_version_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proteins_versions
    ADD CONSTRAINT proteins_versions_accession_current_version_key UNIQUE (accession, current_version);


--
-- Name: proteins_versions proteins_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proteins_versions
    ADD CONSTRAINT proteins_versions_pkey PRIMARY KEY (accession, version);


--
-- Name: publications publications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.publications
    ADD CONSTRAINT publications_pkey PRIMARY KEY (pmid);


--
-- Name: runs runs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.runs
    ADD CONSTRAINT runs_pkey PRIMARY KEY (id);


--
-- Name: taxon_name taxon_name_name_name_class_taxon_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon_name
    ADD CONSTRAINT taxon_name_name_name_class_taxon_id_key UNIQUE (name, name_class, taxon_id);


--
-- Name: taxon taxon_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT taxon_pkey PRIMARY KEY (taxon_id);


--
-- Name: taxon xaktaxon_left_value; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT xaktaxon_left_value UNIQUE (left_value);


--
-- Name: taxon xaktaxon_ncbi_taxon_id; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT xaktaxon_ncbi_taxon_id UNIQUE (ncbi_taxon_id);


--
-- Name: taxon xaktaxon_right_value; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT xaktaxon_right_value UNIQUE (right_value);


--
-- Name: descriptions_association_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX descriptions_association_id_key ON public.descriptions USING btree (association_id);


--
-- Name: descriptions_method_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX descriptions_method_id_key ON public.descriptions USING btree (method_id);


--
-- Name: descriptions_protein1_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX descriptions_protein1_id_key ON public.descriptions USING btree (protein1_id);


--
-- Name: descriptions_protein2_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX descriptions_protein2_id_key ON public.descriptions USING btree (protein2_id);


--
-- Name: methods_search_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX methods_search_key ON public.methods USING gin (search public.gin_trgm_ops);


--
-- Name: proteins_ncbi_taxon_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX proteins_ncbi_taxon_id_key ON public.proteins USING btree (ncbi_taxon_id);


--
-- Name: proteins_versions_search_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX proteins_versions_search_key ON public.proteins_versions USING gin (search public.gin_trgm_ops);


--
-- Name: taxnamename; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX taxnamename ON public.taxon_name USING btree (name);


--
-- Name: taxnametaxonid; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX taxnametaxonid ON public.taxon_name USING btree (taxon_id);


--
-- Name: taxparent; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX taxparent ON public.taxon USING btree (parent_taxon_id);


--
-- Name: taxon rule_taxon_i; Type: RULE; Schema: public; Owner: -
--

CREATE RULE rule_taxon_i AS
    ON INSERT TO public.taxon
   WHERE (( SELECT taxon.taxon_id
           FROM public.taxon
          WHERE (taxon.ncbi_taxon_id = new.ncbi_taxon_id)) IS NOT NULL) DO INSTEAD NOTHING;


--
-- Name: taxon_name rule_taxon_name_i; Type: RULE; Schema: public; Owner: -
--

CREATE RULE rule_taxon_name_i AS
    ON INSERT TO public.taxon_name
   WHERE (( SELECT taxon_name.taxon_id
           FROM public.taxon_name
          WHERE ((taxon_name.taxon_id = new.taxon_id) AND ((taxon_name.name)::text = (new.name)::text) AND ((taxon_name.name_class)::text = (new.name_class)::text))) IS NOT NULL) DO INSTEAD NOTHING;


--
-- Name: associations associations_pmid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.associations
    ADD CONSTRAINT associations_pmid_fkey FOREIGN KEY (pmid) REFERENCES public.publications(pmid);


--
-- Name: associations associations_run_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.associations
    ADD CONSTRAINT associations_run_id_fkey FOREIGN KEY (run_id) REFERENCES public.runs(id);


--
-- Name: taxon_name fktaxon_taxonname; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon_name
    ADD CONSTRAINT fktaxon_taxonname FOREIGN KEY (taxon_id) REFERENCES public.taxon(taxon_id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

