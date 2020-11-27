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
-- Name: contest; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.contest (
    code character varying(20) NOT NULL,
    done integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.contest OWNER TO nandeesh;

--
-- Name: contest_old; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.contest_old (
    code character varying(20),
    done integer
);


ALTER TABLE public.contest_old OWNER TO nandeesh;

--
-- Name: problem; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.problem (
    code character varying(30) NOT NULL,
    contest character varying(20) DEFAULT 'x'::character varying NOT NULL,
    done integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.problem OWNER TO nandeesh;

--
-- Name: problem_old; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.problem_old (
    code character varying(30),
    contest character varying(20)
);


ALTER TABLE public.problem_old OWNER TO nandeesh;

--
-- Name: contest contest_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.contest
    ADD CONSTRAINT contest_pkey PRIMARY KEY (code);


--
-- Name: problem problem_pkey; Type: CONSTRAINT; Schema: public; Owner: nandeesh
--

ALTER TABLE ONLY public.problem
    ADD CONSTRAINT problem_pkey PRIMARY KEY (code);


--
-- PostgreSQL database dump complete
--

