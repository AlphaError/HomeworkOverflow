-- Drop any if already present
drop table if exists Tiers CASCADE;
drop table if exists Categories CASCADE;
drop table if exists Topics CASCADE;
drop table if exists Likes CASCADE;
drop table if exists Answers CASCADE;
drop table if exists Questions CASCADE;
drop table if exists Users CASCADE;

-- Create the Tables
-- Users
Create table Users(
                      username varchar(24) primary key unique,
                      pw varchar(64) not null,
                      email varchar(48),
                      city varchar(24),
                      state varchar(24),
                      country varchar(24),
                      pf text
    /*tier varchar(24) not null,
    constraint chk_tier check (tier IN('Basic', 'Intermediate', 'Advanced', 'Expert'))*/
);

-- Questions
Create table Questions(
                          qid varchar(4) primary key,
                          title text not null,
                          body text not null,
                          resolved integer,
                          t datetime,  -- YYYY-MM-DD HH:MM:SS.SSS
                          username varchar(24),

                          constraint chk_resolved check (resolved IN(0, 1)),

                          foreign key (username) references Users(username)
);

-- Answers
Create table Answers(
                        aid varchar(4) primary key,
                        qid varchar(4) not null,
                        body text not null,
                        best integer,
                        t datetime,  -- YYYY-MM-DD HH:MM:SS.SSS
                        username varchar(24),

                        constraint chk_best check (best IN(0, 1)),

                        foreign key(username) references Users(username),
                        foreign key(qid) references Questions(qid)
);

-- Likes
Create table Likes(
                      aid varchar(4) not null,
                      username varchar(24),

                      primary key(aid, username),
                      foreign key(aid) references Answers(aid),
                      foreign key(username) references Users(username)
);

-- Categories
Create table Categories(
                           cat varchar(64) not null,
                           qid varchar(4) not null,

    -- constraint cat_chk check (cat IN(select cat from topics union select subcat as cat from topics)),
                           primary key(cat, qid),
                           foreign key(qid) references Questions(qid)
);
-- Topics: defines category heierarchy
Create table Topics(
                       cat varchar(64) not null,
                       subcat varchar(64) not null,

                       primary key (cat, subcat)
);
insert into Topics(cat, subcat) values('Science', 'Science');  -- needed for recognition
insert into Topics(cat, subcat) values('Science', 'Chemistry');
insert into Topics(cat, subcat) values('Science', 'Biology');
insert into Topics(cat, subcat) values('Science', 'Physics');

insert into Topics(cat, subcat) values('Math', 'Math');
insert into Topics(cat, subcat) values('Math', 'Computer Science');
insert into Topics(cat, subcat) values('Math', 'Economics');

insert into Topics(cat, subcat) values('English', 'English');
insert into Topics(cat, subcat) values('English', 'Literature');

insert into Topics(cat, subcat) values('Foreign Language', 'Foreign Language');
insert into Topics(cat, subcat) values('Foreign Language', 'Spanish');
insert into Topics(cat, subcat) values('Foreign Language', 'Chinese');
insert into Topics(cat, subcat) values('Foreign Language', 'French');
insert into Topics(cat, subcat) values('Foreign Language', 'Arabic');
insert into Topics(cat, subcat) values('Foreign Language', 'Russian');
insert into Topics(cat, subcat) values('Foreign Language', 'Afrikans');

insert into Topics(cat, subcat) values('History', 'History');
insert into Topics(cat, subcat) values('History', 'US History');
insert into Topics(cat, subcat) values('History', 'Oral History');


-- Part 1 Queries
-- (1) (+part d's extra sample data)
INSERT INTO Users(username, pw, email, city, state, country, pf) VALUES ('kora', '123', 'khughes@nyu.edu', 'Brooklyn', 'NY', 'USA', 'help with hw plz i am ignorant colleg student');
INSERT INTO Users(username, pw, email, city, state, country, pf) VALUES ('prof. lando', '345', 'profussy@nyu.edu', 'Springfield', 'CL', 'USA', 'professor tryna catch students plagerizing');
INSERT INTO Users(username, pw, email, city, state, country, pf) VALUES ('michael', '12321', 'michaelwang@nyu.edu', 'New York', 'NY', 'USA', 'how do you code?');
INSERT INTO Users(username, pw, email, city, state, country, pf) VALUES ('john', 'password', 'johnsmith@nyu.edu', 'Bronx', 'NY', 'USA', 'just john smithin it up');
INSERT INTO Users(username, pw, email, city, state, country, pf) VALUES ('elmo', 'cookiemonster', 'seseamest@gmail.edu', 'Brooklyn', 'NY', 'USA', 'who even a');

INSERT INTO Users(username, pw, email, city, state, country, pf) VALUES ('test', 'testPW', 'test@test.edu', 'Toronto', 'Ontario', 'Canada', 'test profile');


-- (2) (+part d's extra sample data)
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0001', 'Quic Mafs', 'What is 1+1?', 1, '2022-04-12 18:59:00', 'kora');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0002', 'Human Disection', 'how do you disect a human?', 0, '2022-04-12 18:11:00', 'kora');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0003', 'Hard Mafs', 'What is the integral of cotan^4(x)?', 0, '2022-05-12 12:41:00', 'kora');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0004', 'Middleschool curriculumn: integrals???', 'A middleschooler of mine turned in a complex integral dissertaion instead of his algebra homework, what do I do?', 0, '2022-03-12 08:59:00', 'prof. lando');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0005', 'How to be a polyglot?', 'I want to know all the languages but I only know english tehe - advice?', 0, '2021-04-12 10:23:00', 'prof. lando');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0006', 'What is the the lost history of Spanish colonization', 'Mas sorces en espanol por favor?', 0, '2019-04-12 15:48:00', 'michael');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0007', 'How to maximize capital gains tax for the rich?', 'What even is capital gains?', 0, '2020-04-12 12:15:00', 'michael');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0008', 'How to dissolve a body?', 'What is the optimal strong acid/base and how do I get rid of the waste wihtout anyone knowing?', 0, '2021-04-12 17:36:00', 'michael');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0009', 'Quantum Superposition', 'Why does quantum computing use superposition when subatomic read/writes are costly?', 0, '2021-04-12 20:51:00', 'john');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0010', 'This book changed my life', 'Haha gottem, you read? NEEEEERD', 0, '2019-04-12 20:01:00', 'john');
INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0011', 'My grandma is a secret spy?', 'she told me that her ancestors were passed down from royalty to exterminate the Queen? What do I do?', 0, '2022-04-12 14:13:00', 'elmo');

INSERT INTO Categories(cat, qid) VALUES ('Math', '0001');
INSERT INTO Categories(cat, qid) VALUES ('Science', '0002');
INSERT INTO Categories(cat, qid) VALUES ('Math', '0003');
INSERT INTO Categories(cat, qid) VALUES ('Math', '0004');
INSERT INTO Categories(cat, qid) VALUES ('Foreign Language', '0005');
INSERT INTO Categories(cat, qid) VALUES ('History', '0006');
INSERT INTO Categories(cat, qid) VALUES ('Spanish', '0006');
INSERT INTO Categories(cat, qid) VALUES ('Economics', '0007');
INSERT INTO Categories(cat, qid) VALUES ('Chemistry', '0008');
INSERT INTO Categories(cat, qid) VALUES ('Computer Science', '0009');
INSERT INTO Categories(cat, qid) VALUES ('Chemistry', '0009');
INSERT INTO Categories(cat, qid) VALUES ('Literature', '0010');
INSERT INTO Categories(cat, qid) VALUES ('Oral History', '0011');

INSERT INTO Questions(qid, title, body, resolved, t, username) VALUES ('0012', 'Test Title', 'Test Body', 0, '2022-04-17 14:13:00', 'test');
INSERT INTO Categories(cat, qid) VALUES ('Math', '0012');


INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0001', '0001', '1+1=2 doy', 1, '2022-04-13 14:11:19', 'kora');
INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0002', '0002', 'just dont', 1, '2022-04-16 19:12:13', 'elmo');
INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0003', '0003', 'not worth the calculation', 1, '2022-04-19 17:13:51', 'elmo');
INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0004', '0001', '1+1=21 easy', 0, '2022-04-18 13:01:12', 'elmo');
INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0005', '0004', 'give them an A+++', 1, '2022-04-20 11:05:24', 'michael');
INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0006', '0007', 'invest in crypto', 1, '2022-04-21 12:32:14', 'michael');
INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0007', '0010', 'rude', 1, '2022-04-22 08:53:42', 'michael');
INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0008', '0011', 'tap into the super secret spy network', 1, '2022-04-23 10:12:12', 'kora');
INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0009', '0001', '1+1=10 binary right?', 0, '2022-04-21 02:11:19', 'michael');
INSERT INTO Answers(aid, qid, body, best, t, username) VALUES ('0010', '0002', 'Are you actually asking this right now?', 0, '2022-04-14 14:11:19', 'prof. lando');

INSERT INTO Likes(aid, username) VALUES ('0001', 'kora');
INSERT INTO Likes(aid, username) VALUES ('0002', 'michael');
INSERT INTO Likes(aid, username) VALUES ('0003', 'michael');
INSERT INTO Likes(aid, username) VALUES ('0004', 'john');
INSERT INTO Likes(aid, username) VALUES ('0005', 'prof. lando');
INSERT INTO Likes(aid, username) VALUES ('0001', 'john');
INSERT INTO Likes(aid, username) VALUES ('0002', 'elmo');
INSERT INTO Likes(aid, username) VALUES ('0003', 'elmo');



-- (3) Note: this view is just the basis of the query --> php code with the logic below to output the right tiers
/* Logic: #posts = p
     Beginner => p<20
     Intermediate => 20<p<50
     Advanced => 50<p<100
     Expert => 100<p
*/
DROP VIEW IF EXISTS Posts;
create view Posts as
with all_posts as
         ((select username, count(*) as posts
           from Questions
           group by username)
          union
          (select username, count(*) as posts
           from Answers
           group by username))
select username, sum(posts) as num_posts
from all_posts
group by username;

select *  -- for testing
from Posts;

-- (4)
with variable as (select '0001' as givenQID)
SELECT a.body, a.t, best
FROM Answers as a, variable as var
WHERE a.qid = var.givenQID
ORDER BY t ASC;

-- (5)
with q as (select cat, count(*) as num_questions
           from Questions join Categories using(qid)
           GROUP BY cat),
     a as (select cat, count(*) as num_answers
           from Answers join Questions using(qid) join Categories using(qid)
           group by cat),
     t as (select cat, subcat
           from Topics)
select t.cat, sum(q.num_questions) as num_questions, sum(a.num_answers) as num_answers
from t, q left outer join a on q.cat = a.cat
where t.subcat = q.cat
group by t.cat;


-- (6) *in php*


-- Extra Queries:

-- List of Answers to a given question by likes
-- Note: 1 like ranked the same as 0 likes ==> we assume the creator of the answer like it
with variable as (select '0001' as givenQID)
SELECT a.aid, count(*) as num_likes, t as post_time
FROM Answers as a left join Likes as l on a.aid = l.aid, variable as var
WHERE a.qid = var.givenQID
group by a.aid
ORDER BY count(*) desc, t asc;


-- for project:
DROP table IF EXISTS keywords;
create temporary table keywords(
     word varchar(24)
 );
insert into keywords(word) values ("math");
insert into keywords(word) values ("is");
insert into keywords(word) values ("hard");

Select c.qid, title, count(aid) as numA, numQ
from answers right join (
    SELECT title, qid, count(qid) as numQ
    FROM Questions JOIN Categories USING(qid), keywords
    where LOCATE(keywords.word, questions.title) = 1
    group by qid
) as c on Answers.qid = c.qid
group by c.qid
order by numQ desc, numA desc;


with top as (select * from topics join categories using(cat))
Select *
From top, (
    Select resolved, c.username, c.t, c.qid, title, count(aid) as numA
    from answers right join (
        SELECT resolved, username, t, title, qid, count(qid) as numQ
        FROM Questions JOIN Categories USING(qid), keywords
        WHERE LOCATE(keywords.word, questions.title) > 0
        group by qid
    ) as c on Answers.qid = c.qid
    group by c.qid
    order by numQ desc, resolved desc, numA desc) as d
where (d.cat = top.subcat and d.qid = top.qid);
