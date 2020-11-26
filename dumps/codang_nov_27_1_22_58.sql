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
    body character varying(10000) NOT NULL,
    contestcode character varying(20) NOT NULL,
    author character varying(50) NOT NULL
);


ALTER TABLE public.problem OWNER TO nandeesh;

--
-- Name: problemlanguage; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.problemlanguage (
    problemcode character varying(15) NOT NULL,
    languagecode integer NOT NULL
);


ALTER TABLE public.problemlanguage OWNER TO nandeesh;

--
-- Name: problemtag; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.problemtag (
    problemcode character varying(15) NOT NULL,
    tagcode integer NOT NULL
);


ALTER TABLE public.problemtag OWNER TO nandeesh;

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
-- Name: tag; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.tag (
    code integer NOT NULL,
    name character varying(100) NOT NULL,
    owner character varying(50) DEFAULT 'public'::character varying NOT NULL
);


ALTER TABLE public.tag OWNER TO nandeesh;

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
-- Name: tagcategory; Type: TABLE; Schema: public; Owner: nandeesh
--

CREATE TABLE public.tagcategory (
    tagcode integer NOT NULL,
    categorycode integer NOT NULL
);


ALTER TABLE public.tagcategory OWNER TO nandeesh;

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
-- Data for Name: apiauth; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.apiauth (one_row, client_id, client_secret, api_endpoint, authorization_code_endpoint, access_token_endpoint, redirect_uri, website_base_url, authorization_code, access_token, refresh_token, gen_time, scope) FROM stdin;
t	5cd537499b6a22a4898dbcb9e68c6783	9a6b652f2bd4dbd97a451b1125984007	https://api.codechef.com/	https://api.codechef.com/oauth/authorize	https://api.codechef.com/oauth/token	http://localhost:8080/	http://localhost/	189b2d919b5e9a3d4d7b82e6ef8d761b94852183	b31f9efc9d00cd011f0c9f36b9bcaeae5b4e47d3	224bf0405181a56771b65b9f1e28ec0cc97c1a3c	1606419190	public
\.


--
-- Data for Name: category; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.category (code, name) FROM stdin;
\.


--
-- Data for Name: contest; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.contest (code, name, banner, announcement, startdate, enddate) FROM stdin;
COZI2019	CODEZILLA	https://www.codechef.com/download/small-banner/COZI2019/1567689508.jpg	&lt;p&gt;14:30, 15th October 2017: The exam is extended by 10 minutes.&lt;br /&gt;&lt;b&gt;&lt;br /&gt;The problem weightages are given below in rules section.&lt;/b&gt;&lt;/p&gt;\r\n&lt;p&gt;The content of Recent Activity block from exam page has been made inaccessible. In case if you try to access it, you will get an error stating&nbsp;&lt;b&gt;&quot;You are not allowed to check this contest. Please reload&quot;&lt;/b&gt;. Please ignore the error and continue with your exam.&lt;/p&gt;\r\n&lt;p&gt;14:44, 15th October 2017: Problem accuracy will not be displayed. It has been restricted for this exam.&lt;/p&gt;\r\n&lt;p&gt;Also, the score shown on the exam page is not final. It is subject to change after final verification.&lt;/p&gt;\r\n&lt;p&gt;Additionally, you cannot leave the exam hall before 3:30 pm.&lt;/p&gt;	2019-09-07	2019-09-08
CDVT2016	Codester veteran	https://www.codechef.com/download/small-banner/CDVT2016/1476959658.jpg	&lt;p&gt;14:30, 15th October 2017: The exam is extended by 10 minutes.&lt;br /&gt;&lt;b&gt;&lt;br /&gt;The problem weightages are given below in rules section.&lt;/b&gt;&lt;/p&gt;\r\n&lt;p&gt;The content of Recent Activity block from exam page has been made inaccessible. In case if you try to access it, you will get an error stating&nbsp;&lt;b&gt;&quot;You are not allowed to check this contest. Please reload&quot;&lt;/b&gt;. Please ignore the error and continue with your exam.&lt;/p&gt;\r\n&lt;p&gt;14:44, 15th October 2017: Problem accuracy will not be displayed. It has been restricted for this exam.&lt;/p&gt;\r\n&lt;p&gt;Also, the score shown on the exam page is not final. It is subject to change after final verification.&lt;/p&gt;\r\n&lt;p&gt;Additionally, you cannot leave the exam hall before 3:30 pm.&lt;/p&gt;	2016-10-23	2016-10-23
PRACTICE	Practice Contest	https://www.codechef.com/misc/banner-practice.jpg	&lt;p&gt; NO ANNOUNCEMENTS &lt;/p&gt;	1970-01-01	infinity
\.


--
-- Data for Name: country; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.country (name, code) FROM stdin;
India	IN
EMPTY	EMPTY
\.


--
-- Data for Name: enduser; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.enduser (username, fullname, band, rating, country, institution) FROM stdin;
prnjl_rai	Pranjal Rai	5	2109	IN	52
gagan86nagpal	Gagan Nagpal	5	2007	IN	53
public	public	0	0	EMPTY	0
\.


--
-- Data for Name: institution; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.institution (name, code) FROM stdin;
Birla Institute of Technology Mesra	52
InMobi Technologies Pvt Ltd	53
EMPTY	0
\.


--
-- Data for Name: language; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.language (code, name) FROM stdin;
0	ANY
\.


--
-- Data for Name: problem; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.problem (code, name, date, maxtimelimit, sourcesizelimit, body, contestcode, author) FROM stdin;
A2J001	Count the K-tuples	2019-08-31	1	50000	You have been given two numbers N and K.Your task is to find the number of $K-tuples$    <br />$($ $Tuples$ $of$ $size$ $K$ $)$ <br /><br />($a$<sub>$1$</sub> $,$ $a$<sub>$2$</sub> $....$ $a$<sub>$k$</sub>) such that:-<br /><br />1. For any pair $(i,j)$ such that $i \\neq j$ , $div$($a$<sub>$i$</sub>)$*$$div$($a$<sub>$j$</sub>)$=$$div$($a$<sub>$i$</sub>$*$$a$<sub>$j$</sub>) where $div(X)$ represents the $number$ $of$ $divisors$ $of$ $X.$<br /><br />2. $a$<sub>$1$</sub> * $a$ <sub>$2$</sub> *$.....$ $a$ <sub>$k$</sub>= $Factorial$ $of$ $N$ $.$<br /><br />Two $K-tuples$ <br />  $($ $a$<sub>$1$</sub> $,$ $a$<sub>$2$</sub> $,$ $a$<sub>$3$</sub>......$a$<sub>$k$</sub> $)$ and $($ $b$<sub>$1$</sub> $,$ $b$<sub>$2$</sub> $,$ $b$<sub>$3$</sub> $,$ ......$b$<sub>$k$</sub>$)$ are considered different iff $a$<sub>$i$</sub> $is$ $not$ $equal$ $to$ $b$<sub>$i$</sub>  for atleast one $1\\leq i \\leq K$<br /><br />###Input:<br /><br />- First line will contain $T$, number of testcases. Then the testcases follow. <br />- Each testcase contains of a single line of input, two integers $N, K$ as described above. <br /><br />###Output:<br />For each testcase, output in a new line the required answer modulo $1000000007$ $.$<br /><br />###Constraints <br />- $1 \\leq T \\leq 100000$<br />- $1 \\leq N \\leq 10^6$<br />- $1 \\leq K \\leq 100^5$<br /><br /><br />###Sample Input:<br />\t1<br />\t2 3<br /><br />###Sample Output:<br />\t3<br />\t<br />###EXPLANATION:<br />This valid tuples are :<br /><br />$[1,1,2],[1,2,1],[2,1,1]$<br />	COZI2019	prnjl_rai
AAKASHB	Foodie Aakash	2016-10-18	1	50000	<p>Aakash is the number one foodie of USICT and he loves to eat a lot. On his birthday his friends wanted to give him<br />the best feast he ever had. So they took him to restaurant and ordered N number of dishes.</p> <br /><p>Initially his hunger is 0,<br />as the time passes the hunger of Aakash increases by K ounces per second(i.e he can eat K ounces after 1 sec, K more after another<br />second and so on). Now at end of certain time T[i] seconds, dish weighing D[i] ounces arrives.Aakash can reject this dish if he wants, but Aakash can only eat this dish if his hunger is greater than or equal to the D[i] and D[i] will be subtracted from his current hunger.<br />Else he has to reject the dish, the dish is thrown away and nothing is reduced from his current hunger.<br /></p><br /><p><br />Aakash wants to eat maximum number of dishes<br />so he asks for your help. Help him figure out the maximum number of dishes he can eat.<br><br />Note : Aakash will eat the dish completely, so no partial eating and whole weight of the dish is reduced for his hunger.<br /></p><br /><b>Input :</b><br><br /><p><br />First Line consists of a single integer N - number of dishes.<br />Next N lines consists of T[i] and D[i] - arrival time of a dish and it weight.<br><br />All T[i] are distinct.<br><br />Last line consists of a single integer K - the rate at which Aakash's hunger increases.<br><br /></p><br /><p><br /><b>Output:</b><br /><br><br />Output a single integer , telling the maximum number of dishes Aakash can eat.<br /></p><br /><p><br /><b>Constraints :</b><br /><br><br /><b>Subtask 1 :</b><br><br />\t1 <= N <= 1000<br><br />\t1 <= T[i] , D[i] <= 1000 <br><br />\t1 <= K <= 10^7<br><br /><br /><b>Subtask 2:</b><br> <br />\t1 <= N <= 500000<br><br />\t1 <= T[i] , D[i] <= 10^9<br><br />\t1 <= K <= 10^7<br><br /></p><br /><p><br /><b>Sample input 1 : </b><br /></p><br /><p><br />5<br><br />11 2<br><br />2 3<br><br />4 2<br><br />10 7<br><br />12 1<br><br />1<br><br /></p><br /><p><br /><b>Sample output 1 :</b><br /><br><br />4<br><br /></p><br /><br /><p><br /><b>Explaination :</b><br><br />Aakash can eat maximum 4 dishes. Aakash cannot eat the dish arrived at T[i] = 2 with D[i]=3 as his current hunger is 2 while the weight of the dish is 3, <br />rest he can eat all 4 dishes afterwards.<br /></p><br /><p><br /><b>Sample input 2 :</b><br><br />3<br><br />2 100<br><br />3 101<br><br />5 200<br><br />12<br><br /></p><br /><p><br /><b>Sample output 2:</b><br><br />0<br><br /></p><br /><p><br /><b>Explaination :</b><br><br />Aakash cannot eat any of the dishes as his hunger is always less than the D[i] at corresponding T[i].<br><br /></p><br />	CDVT2016	gagan86nagpal
\.


--
-- Data for Name: problemlanguage; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.problemlanguage (problemcode, languagecode) FROM stdin;
A2J001	0
AAKASHB	0
\.


--
-- Data for Name: problemtag; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.problemtag (problemcode, tagcode) FROM stdin;
\.


--
-- Data for Name: submission; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.submission (id, sourcecode, memory, result, link, username, languagecode, problemcode, contestcode, score, "time", date) FROM stdin;
\.


--
-- Data for Name: tag; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.tag (code, name, owner) FROM stdin;
\.


--
-- Data for Name: tagcategory; Type: TABLE DATA; Schema: public; Owner: nandeesh
--

COPY public.tagcategory (tagcode, categorycode) FROM stdin;
\.


--
-- Name: category_code_seq; Type: SEQUENCE SET; Schema: public; Owner: nandeesh
--

SELECT pg_catalog.setval('public.category_code_seq', 1, false);


--
-- Name: institution_code_seq; Type: SEQUENCE SET; Schema: public; Owner: nandeesh
--

SELECT pg_catalog.setval('public.institution_code_seq', 56, true);


--
-- Name: language_code_seq; Type: SEQUENCE SET; Schema: public; Owner: nandeesh
--

SELECT pg_catalog.setval('public.language_code_seq', 107, true);


--
-- Name: tag_code_seq; Type: SEQUENCE SET; Schema: public; Owner: nandeesh
--

SELECT pg_catalog.setval('public.tag_code_seq', 1, true);


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

