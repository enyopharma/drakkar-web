--
-- PostgreSQL database dump
--

-- Dumped from database version 10.10 (Ubuntu 10.10-0ubuntu0.18.04.1)
-- Dumped by pg_dump version 10.10 (Ubuntu 10.10-0ubuntu0.18.04.1)

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
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: pg_trgm; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pg_trgm WITH SCHEMA public;


--
-- Name: EXTENSION pg_trgm; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION pg_trgm IS 'text similarity measurement and index searching based on trigrams';


SET default_tablespace = '';

SET default_with_oids = false;

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
    association_id integer NOT NULL,
    method_id integer NOT NULL,
    interactor1_id integer NOT NULL,
    interactor2_id integer NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now() NOT NULL,
    deleted_at timestamp(0) without time zone,
    stable_id character(10) NOT NULL
);


--
-- Name: interactors; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.interactors (
    id integer NOT NULL,
    protein_id integer NOT NULL,
    name character varying(32) NOT NULL,
    start integer NOT NULL,
    stop integer NOT NULL,
    mapping json NOT NULL
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
    taxon_id integer NOT NULL,
    accession character varying(10) NOT NULL,
    name character varying(255) NOT NULL,
    description text NOT NULL,
    search text,
    CONSTRAINT proteins_type_check CHECK (((type)::text = ANY (ARRAY[('h'::character varying)::text, ('v'::character varying)::text])))
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
-- Name: descriptions_details; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW public.descriptions_details AS
 SELECT r.type,
    r.name AS run,
    a.state,
    d.stable_id,
    a.pmid,
    m.psimi_id,
    p1.accession AS accession1,
    i1.name AS name1,
    i1.start AS start1,
    i1.stop AS stop1,
    i1.mapping AS mapping1,
    p2.accession AS accession2,
    i2.name AS name2,
    i2.start AS start2,
    i2.stop AS stop2,
    i2.mapping AS mapping2,
    d.created_at
   FROM public.runs r,
    public.associations a,
    public.descriptions d,
    public.methods m,
    public.interactors i1,
    public.proteins p1,
    public.interactors i2,
    public.proteins p2
  WHERE ((r.id = a.run_id) AND (a.id = d.association_id) AND (m.id = d.method_id) AND (i1.id = d.interactor1_id) AND (i2.id = d.interactor2_id) AND (p1.id = i1.protein_id) AND (p2.id = i2.protein_id) AND (d.deleted_at IS NULL));


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
-- Name: features; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.features (
    id integer NOT NULL,
    sequence_id integer NOT NULL,
    key character varying(8) NOT NULL,
    description text NOT NULL,
    start integer NOT NULL,
    stop integer NOT NULL
);


--
-- Name: features_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.features_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: features_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.features_id_seq OWNED BY public.features.id;


--
-- Name: interactors_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.interactors_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: interactors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.interactors_id_seq OWNED BY public.interactors.id;


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
-- Name: sequences; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sequences (
    id integer NOT NULL,
    protein_id integer NOT NULL,
    accession character varying(12) NOT NULL,
    is_canonical boolean NOT NULL,
    sequence text NOT NULL
);


--
-- Name: sequences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.sequences_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sequences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.sequences_id_seq OWNED BY public.sequences.id;


--
-- Name: taxon; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.taxon (
    taxon_id integer NOT NULL,
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
-- Name: associations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.associations ALTER COLUMN id SET DEFAULT nextval('public.associations_id_seq'::regclass);


--
-- Name: descriptions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.descriptions ALTER COLUMN id SET DEFAULT nextval('public.descriptions_id_seq'::regclass);


--
-- Name: features id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.features ALTER COLUMN id SET DEFAULT nextval('public.features_id_seq'::regclass);


--
-- Name: interactors id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.interactors ALTER COLUMN id SET DEFAULT nextval('public.interactors_id_seq'::regclass);


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
-- Name: sequences id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sequences ALTER COLUMN id SET DEFAULT nextval('public.sequences_id_seq'::regclass);


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
-- Name: descriptions descriptions_interactor1_id_interactor2_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.descriptions
    ADD CONSTRAINT descriptions_interactor1_id_interactor2_id_key UNIQUE (interactor1_id, interactor2_id);


--
-- Name: descriptions descriptions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.descriptions
    ADD CONSTRAINT descriptions_pkey PRIMARY KEY (id);


--
-- Name: descriptions descriptions_stable_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.descriptions
    ADD CONSTRAINT descriptions_stable_id_key UNIQUE (stable_id);


--
-- Name: features features_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.features
    ADD CONSTRAINT features_pkey PRIMARY KEY (id);


--
-- Name: interactors interactors_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.interactors
    ADD CONSTRAINT interactors_pkey PRIMARY KEY (id);


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
-- Name: proteins proteins_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proteins
    ADD CONSTRAINT proteins_pkey PRIMARY KEY (id);


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
-- Name: sequences sequences_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sequences
    ADD CONSTRAINT sequences_pkey PRIMARY KEY (id);


--
-- Name: taxon taxon_left_value_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT taxon_left_value_key UNIQUE (left_value);


--
-- Name: taxon taxon_ncbi_taxon_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT taxon_ncbi_taxon_id_key UNIQUE (ncbi_taxon_id);


--
-- Name: taxon taxon_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT taxon_pkey PRIMARY KEY (taxon_id);


--
-- Name: taxon taxon_right_value_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon
    ADD CONSTRAINT taxon_right_value_key UNIQUE (right_value);


--
-- Name: taxon_name unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon_name
    ADD CONSTRAINT "unique" UNIQUE (taxon_id, name, name_class);


--
-- Name: descriptions_association_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX descriptions_association_id_key ON public.descriptions USING btree (association_id);


--
-- Name: descriptions_interactor1_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX descriptions_interactor1_id_key ON public.descriptions USING btree (interactor1_id);


--
-- Name: descriptions_interactor2_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX descriptions_interactor2_id_key ON public.descriptions USING btree (interactor2_id);


--
-- Name: descriptions_method_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX descriptions_method_id_key ON public.descriptions USING btree (method_id);


--
-- Name: descriptions_uniq_key; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX descriptions_uniq_key ON public.descriptions USING btree (association_id, method_id, interactor1_id, interactor2_id);


--
-- Name: features_sequence_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX features_sequence_id_key ON public.features USING btree (sequence_id);


--
-- Name: interactors_protein_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX interactors_protein_id_key ON public.interactors USING btree (protein_id);


--
-- Name: interactors_protein_id_name_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX interactors_protein_id_name_key ON public.interactors USING btree (protein_id, name);


--
-- Name: interactors_protein_id_start_stop_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX interactors_protein_id_start_stop_key ON public.interactors USING btree (protein_id, start, stop);


--
-- Name: methods_search_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX methods_search_key ON public.methods USING gin (search public.gin_trgm_ops);


--
-- Name: proteins_accession_key; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX proteins_accession_key ON public.proteins USING btree (accession);


--
-- Name: proteins_search_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX proteins_search_key ON public.proteins USING gin (search public.gin_trgm_ops);


--
-- Name: proteins_taxon_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX proteins_taxon_id_key ON public.proteins USING btree (taxon_id);


--
-- Name: sequences_accession_key; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX sequences_accession_key ON public.sequences USING btree (accession);


--
-- Name: sequences_protein_id_key; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sequences_protein_id_key ON public.sequences USING btree (protein_id);


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
-- Name: taxon_name taxon_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.taxon_name
    ADD CONSTRAINT taxon_id FOREIGN KEY (taxon_id) REFERENCES public.taxon(taxon_id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--
