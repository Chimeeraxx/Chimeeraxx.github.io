create table athlete
(
    id      integer not null
        constraint athlete_pk
            primary key autoincrement,
    name text not null,
    sport_name text not null,
    age integer not null
);
