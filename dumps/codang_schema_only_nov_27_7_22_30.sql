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
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: apiauth; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.apiauth (
    one_row boolean DEFAULT true NOT NULL,
    client_id text DEFAULT '5cd537499b6a22a4898dbcb9e68c6783'::text NOT NULL,
    client_secret text DEFAULT '9a6b652f2bd4dbd97a451b1125984007'::text NOT NULL,
    api_endpoint text DEFAULT 'https://api.codechef.com/'::text NOT NULL,
    authorization_code_endpoint text DEFAULT 'https://api.codechef.com/oauth/authorize'::text NOT NULL,
    access_token_endpoint text DEFAULT 'https://api.codechef.com/oauth/token'::text NOT NULL,
    redirect_uri text DEFAULT 'http://localhost:8080/'::text NOT NULL,
    website_base_url text DEFAULT 'http://localhost/'::text NOT NULL,
    authorization_code text DEFAULT 'dummy'::text NOT NULL,
    access_token text DEFAULT 'dummy'::text NOT NULL,
    refresh_token text DEFAULT 'dummy'::text NOT NULL,
    gen_time bigint DEFAULT 0 NOT NULL,
    scope text DEFAULT 'dummy'::text NOT NULL,
    CONSTRAINT apiauth_onerow_check CHECK (one_row),
    CONSTRAINT notempty CHECK ((length(authorization_code) > 0)),
    CONSTRAINT notempty2 CHECK ((length(access_token) > 0)),
    CONSTRAINT notempty3 CHECK ((length(refresh_token) > 0))
);


ALTER TABLE public.apiauth OWNER TO nandeesh;

--
-- Name: category; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.category (
    code integer NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public.category OWNER TO nandeesh;

--
-- Name: category_code_seq; Type: SEQUENCE; Schema: public; Owner: nandeesh
--

CREATE SEQUENCE public.category_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.category_code_seq OWNER TO nandeesh;

--
-- Name: category_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: nandeesh
--

ALTER SEQUENCE public.category_code_seq OWNED BY public.category.code;


--
-- Name: contest; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.contest (
    code character varying(15) NOT NULL,
    name character varying(100) NOT NULL,
    banner character varying(1000) DEFAULT 'https://www.codechef.com/misc/banner-practice.jpg'::character varying NOT NULL,
    announcement character varying(1000) DEFAULT '&lt;p&gt; NO ANNOUNCEMENTS &lt;/p&gt;'::character varying NOT NULL,
    startdate date DEFAULT '1970-01-01'::date NOT NULL,
    enddate date DEFAULT '1970-01-01'::date NOT NULL
);


ALTER TABLE public.contest OWNER TO nandeesh;

--
-- Name: country; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.country (
    name character varying(100) NOT NULL,
    code character varying(15) NOT NULL
);


ALTER TABLE public.country OWNER TO nandeesh;

--
-- Name: enduser; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.enduser (
    username character varying(50) NOT NULL,
    fullname character varying(100) NOT NULL,
    band integer DEFAULT 0 NOT NULL,
    rating integer DEFAULT 0 NOT NULL,
    country character varying(15),
    institution integer
);


ALTER TABLE public.enduser OWNER TO nandeesh;

--
-- Name: institution; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.institution (
    name character varying(200) NOT NULL,
    code integer NOT NULL
);


ALTER TABLE public.institution OWNER TO nandeesh;

--
-- Name: institution_code_seq; Type: SEQUENCE; Schema: public; Owner: nandeesh
--

CREATE SEQUENCE public.institution_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.institution_code_seq OWNER TO nandeesh;

--
-- Name: institution_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: nandeesh
--

ALTER SEQUENCE public.institution_code_seq OWNED BY public.institution.code;


--
-- Name: language; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.language (
    code integer NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.language OWNER TO nandeesh;

--
-- Name: language_code_seq; Type: SEQUENCE; Schema: public; Owner: nandeesh
--

CREATE SEQUENCE public.language_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.language_code_seq OWNER TO nandeesh;

--
-- Name: language_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: nandeesh
--

ALTER SEQUENCE public.language_code_seq OWNED BY public.language.code;


--
-- Name: problem; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.problem (
    code character varying(15) NOT NULL,
    name character varying(100) NOT NULL,
    date date DEFAULT '1970-01-01'::date NOT NULL,
    maxtimelimit integer DEFAULT 1 NOT NULL,
    sourcesizelimit integer DEFAULT 50000 NOT NULL,
    body character varying(20000) NOT NULL,
    contestcode character varying(20) NOT NULL,
    author character varying(50) NOT NULL
);


ALTER TABLE public.problem OWNER TO nandeesh;

--
-- Name: problemtag; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.problemtag (
    problemcode character varying(15) NOT NULL,
    tagcode integer NOT NULL
);


ALTER TABLE public.problemtag OWNER TO nandeesh;

--
-- Name: tag; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.tag (
    code integer NOT NULL,
    name character varying(100) NOT NULL,
    owner character varying(50) DEFAULT 'public'::character varying NOT NULL
);


ALTER TABLE public.tag OWNER TO nandeesh;

--
-- Name: tagcategory; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.tagcategory (
    tagcode integer NOT NULL,
    categorycode integer NOT NULL
);


ALTER TABLE public.tagcategory OWNER TO nandeesh;

--
-- Name: problem_tag_category_view; Type: VIEW; Schema: public; Owner: nandeesh
--

CREATE VIEW public.problem_tag_category_view AS
 SELECT problem.name AS problem,
    problem.code AS problemcode,
    tag.name AS tag,
    tag.code AS tagcode,
    category.name AS category,
    category.code AS categorycode
   FROM ((((public.problem
     LEFT JOIN public.problemtag ON (((problemtag.problemcode)::text = (problem.code)::text)))
     LEFT JOIN public.tag ON ((tag.code = problemtag.tagcode)))
     LEFT JOIN public.tagcategory ON ((tagcategory.tagcode = tag.code)))
     LEFT JOIN public.category ON ((category.code = tagcategory.categorycode)))
  ORDER BY problem.code;


ALTER TABLE public.problem_tag_category_view OWNER TO nandeesh;

--
-- Name: problemlanguage; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.problemlanguage (
    problemcode character varying(15) NOT NULL,
    languagecode integer NOT NULL
);


ALTER TABLE public.problemlanguage OWNER TO nandeesh;

--
-- Name: submission; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.submission (
    id bigint NOT NULL,
    sourcecode text DEFAULT 'EMPTY'::text NOT NULL,
    memory integer NOT NULL,
    result character varying(50) NOT NULL,
    link character varying(1000) DEFAULT ''::character varying NOT NULL,
    username character varying(50) NOT NULL,
    languagecode integer NOT NULL,
    problemcode character varying(15) NOT NULL,
    contestcode character varying(15) NOT NULL,
    score double precision NOT NULL,
    "time" double precision NOT NULL,
    date date DEFAULT '1970-01-01'::date NOT NULL
);


ALTER TABLE public.submission OWNER TO nandeesh;

--
-- Name: tag_category_view; Type: VIEW; Schema: public; Owner: nandeesh
--

CREATE VIEW public.tag_category_view AS
 SELECT tag.name AS tag,
    tag.code AS tagcode,
    category.name AS category,
    category.code AS categorycode
   FROM ((public.tag
     LEFT JOIN public.tagcategory ON ((tagcategory.tagcode = tag.code)))
     LEFT JOIN public.category ON ((category.code = tagcategory.categorycode)))
  ORDER BY tag.code;


ALTER TABLE public.tag_category_view OWNER TO nandeesh;

--
-- Name: tag_code_seq; Type: SEQUENCE; Schema: public; Owner: nandeesh
--

CREATE SEQUENCE public.tag_code_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tag_code_seq OWNER TO nandeesh;

--
-- Name: tag_code_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: nandeesh
--

ALTER SEQUENCE public.tag_code_seq OWNED BY public.tag.code;


--
-- Name: category code; Type: DEFAULT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.category ALTER COLUMN code SET DEFAULT nextval('public.category_code_seq'::regclass);


--
-- Name: institution code; Type: DEFAULT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.institution ALTER COLUMN code SET DEFAULT nextval('public.institution_code_seq'::regclass);


--
-- Name: language code; Type: DEFAULT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.language ALTER COLUMN code SET DEFAULT nextval('public.language_code_seq'::regclass);


--
-- Name: tag code; Type: DEFAULT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.tag ALTER COLUMN code SET DEFAULT nextval('public.tag_code_seq'::regclass);


--
-- Name: apiauth apiauth_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.apiauth
    ADD CONSTRAINT apiauth_pkey PRIMARY KEY (one_row);


--
-- Name: category category_name_key; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.category
    ADD CONSTRAINT category_name_key UNIQUE (name);


--
-- Name: category category_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.category
    ADD CONSTRAINT category_pkey PRIMARY KEY (code);


--
-- Name: contest contest_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.contest
    ADD CONSTRAINT contest_pkey PRIMARY KEY (code);


--
-- Name: country country_name_key; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.country
    ADD CONSTRAINT country_name_key UNIQUE (name);


--
-- Name: country country_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.country
    ADD CONSTRAINT country_pkey PRIMARY KEY (code);


--
-- Name: enduser enduser_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.enduser
    ADD CONSTRAINT enduser_pkey PRIMARY KEY (username);


--
-- Name: institution institution_name_key; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.institution
    ADD CONSTRAINT institution_name_key UNIQUE (name);


--
-- Name: institution institution_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.institution
    ADD CONSTRAINT institution_pkey PRIMARY KEY (code);


--
-- Name: language language_name_key; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.language
    ADD CONSTRAINT language_name_key UNIQUE (name);


--
-- Name: language language_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.language
    ADD CONSTRAINT language_pkey PRIMARY KEY (code);


--
-- Name: problem problem_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problem
    ADD CONSTRAINT problem_pkey PRIMARY KEY (code);


--
-- Name: problemlanguage problemlanguage_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problemlanguage
    ADD CONSTRAINT problemlanguage_pkey PRIMARY KEY (problemcode, languagecode);


--
-- Name: problemtag problemtag_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problemtag
    ADD CONSTRAINT problemtag_pkey PRIMARY KEY (problemcode, tagcode);


--
-- Name: submission submission_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.submission
    ADD CONSTRAINT submission_pkey PRIMARY KEY (id);


--
-- Name: tag tag_name_owner_key; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.tag
    ADD CONSTRAINT tag_name_owner_key UNIQUE (name, owner);


--
-- Name: tag tag_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.tag
    ADD CONSTRAINT tag_pkey PRIMARY KEY (code);


--
-- Name: tagcategory tagcategory_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.tagcategory
    ADD CONSTRAINT tagcategory_pkey PRIMARY KEY (tagcode, categorycode);


--
-- Name: enduser enduser_country_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.enduser
    ADD CONSTRAINT enduser_country_fkey FOREIGN KEY (country) REFERENCES public.country(code);


--
-- Name: enduser enduser_institution_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.enduser
    ADD CONSTRAINT enduser_institution_fkey FOREIGN KEY (institution) REFERENCES public.institution(code);


--
-- Name: problem problem_author_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problem
    ADD CONSTRAINT problem_author_fkey FOREIGN KEY (author) REFERENCES public.enduser(username);


--
-- Name: problem problem_contestcode_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problem
    ADD CONSTRAINT problem_contestcode_fkey FOREIGN KEY (contestcode) REFERENCES public.contest(code);


--
-- Name: problemlanguage problemlanguage_languagecode_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problemlanguage
    ADD CONSTRAINT problemlanguage_languagecode_fkey FOREIGN KEY (languagecode) REFERENCES public.language(code);


--
-- Name: problemlanguage problemlanguage_problemcode_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problemlanguage
    ADD CONSTRAINT problemlanguage_problemcode_fkey FOREIGN KEY (problemcode) REFERENCES public.problem(code);


--
-- Name: problemtag problemtag_problemcode_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problemtag
    ADD CONSTRAINT problemtag_problemcode_fkey FOREIGN KEY (problemcode) REFERENCES public.problem(code);


--
-- Name: problemtag problemtag_tagcode_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problemtag
    ADD CONSTRAINT problemtag_tagcode_fkey FOREIGN KEY (tagcode) REFERENCES public.tag(code);


--
-- Name: submission submission_contestcode_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.submission
    ADD CONSTRAINT submission_contestcode_fkey FOREIGN KEY (contestcode) REFERENCES public.contest(code);


--
-- Name: submission submission_language_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.submission
    ADD CONSTRAINT submission_language_fkey FOREIGN KEY (languagecode) REFERENCES public.language(code);


--
-- Name: submission submission_problemcode_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.submission
    ADD CONSTRAINT submission_problemcode_fkey FOREIGN KEY (problemcode) REFERENCES public.problem(code);


--
-- Name: submission submission_username_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.submission
    ADD CONSTRAINT submission_username_fkey FOREIGN KEY (username) REFERENCES public.enduser(username);


--
-- Name: tag tag_owner_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.tag
    ADD CONSTRAINT tag_owner_fkey FOREIGN KEY (owner) REFERENCES public.enduser(username);


--
-- Name: tagcategory tagcategory_categorycode_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.tagcategory
    ADD CONSTRAINT tagcategory_categorycode_fkey FOREIGN KEY (categorycode) REFERENCES public.category(code);


--
-- Name: tagcategory tagcategory_tagcode_fkey; Type: FK CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.tagcategory
    ADD CONSTRAINT tagcategory_tagcode_fkey FOREIGN KEY (tagcode) REFERENCES public.tag(code);


--
-- PostgreSQL database dump complete
--

