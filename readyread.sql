-- SQL DDL



drop table if exists obiettivo cascade;
create table obiettivo (
	num_libri_richiesto integer primary key,
	tipo_socio varchar(6)
);



drop sequence if exists socio_seq cascade;
create sequence socio_seq
start 1
increment 1
minvalue 1
maxvalue 9999999
cache 1;



drop table if exists socio cascade;
create table socio (
	id integer primary key default nextval('socio_seq'::regclass),
	idObiettivo integer default 100,
	cf varchar(16),
	nome varchar(50) not null,
	cognome varchar(20),
	email varchar(320) not null,
	num_recensioni integer default 0,
	valoreDonazione real default 0,
	superatoObiettivo boolean default false,
	username varchar(20),
	password varchar (20) not null,
	unique (cf, username),
	constraint vincSocio foreign key (idObiettivo) references obiettivo(num_libri_richiesto)
);



drop table if exists junior cascade;
create table junior (
	idSocio integer primary key,
	num_domande integer default 0,
	constraint vincJunior foreign key (idSocio) references socio(id) on delete cascade on update cascade
);



drop table if exists ente;
create table ente (
	idSocio integer primary key,
	tipologia varchar(40),
	constraint vincEnte foreign key (idSocio) references socio(id) on delete cascade on update cascade
);



drop table if exists consiglio cascade;
create table consiglio (
	id integer primary key,
	anno_sociale varchar(9) unique,
	num_membri integer
);



drop sequence if exists premio_seq cascade;
create sequence premio_seq
start 1
increment 1
minvalue 1
maxvalue 9999999
cache 1;



drop table if exists premio cascade;
create table premio (
	codice integer primary key default nextval('premio_seq'::regclass),
	valore integer
);



drop table if exists viaggio cascade;
create table viaggio (
	codicePremio integer primary key,
	evento varchar(60),
	periodo varchar(10),
	constraint vincViaggio foreign key (codicePremio) references premio(codice) on delete cascade on update cascade
);



drop table if exists senior cascade;
create table senior (
	idSocio integer primary key,
	idConsiglio integer,
	idViaggio integer,
	n_dom_risposte integer default 0,
	n_dom_attese integer default 0,
	n_rec_risposte integer default 0,
	n_rec_attese integer default 0,
	voti_elim integer default 0,
	votato boolean default false,
	constraint vincSenior1 foreign key (idSocio) references socio(id) on delete cascade on update cascade,
	constraint vincSenior2 foreign key (idConsiglio) references consiglio(id),
	constraint vincSenior3 foreign key (idViaggio) references viaggio(codicePremio)
);



drop table if exists domanda;
create table domanda (
	idJunior integer,
	idSenior integer,
	data date,
	ora time,
	titolo varchar(30) not null,
	testo varchar(500) not null,
	risposta varchar(500) default null,
	primary key (idJunior, idSenior, data, ora),
	constraint vincDomanda1 foreign key (idJunior) references junior(idSocio),
	constraint vincDomanda2 foreign key (idSenior) references senior(idSocio)
);



drop table if exists libro cascade;
create table libro (
	isbn bigint primary key,
	titolo varchar(40) not null,
	autore varchar(30) not null,
	genere varchar(60),
	edizione varchar(30),
	lingua varchar(30)
);



drop sequence if exists recensione_seq cascade;
create sequence recensione_seq
start 1
increment 1
minvalue 1
maxvalue 9999999
cache 1;



drop table if exists recensione cascade;
create table recensione (
	id integer primary key default nextval('recensione_seq'::regclass),
	idLibro bigint,
	idSocio int,
	autore varchar(30) not null,
	data date not null,
	stelle integer not null,
	titolo varchar(40) not null,
	testo varchar(1000) not null,
	check (stelle>0 and stelle<6),
	constraint vincRecensione1 foreign key (idLibro) references libro(isbn),
	constraint vincRecensione2 foreign key (idSocio) references socio(id)
);



drop table if exists recensione_j;
create table recensione_j (
	idRecensione integer primary key,
	idJunior integer,
	idSenior integer,
	esitoValutazione boolean,
	constraint vincRecensione_j1 foreign key (idRecensione) references recensione(id) on delete cascade on update cascade,
	constraint vincRecensione_j2 foreign key (idJunior) references junior(idSocio),
	constraint vincRecensione_j3 foreign key (idSenior) references senior(idSocio)
);



drop table if exists recensione_s;
create table recensione_s (
	idRecensione integer primary key,
	idSenior integer,
	constraint vincRecensione_s1 foreign key (idRecensione) references recensione(id) on delete cascade on update cascade,
	constraint vincRecensione_s2 foreign key (idSenior) references senior(idSocio)
);



drop table if exists collezione;
create table collezione (
	idSocio integer,
	anno varchar(9),
	num_libri integer,
	primary key(idSocio, anno),
	constraint vincCollezione foreign key (idSocio) references socio(id)
);



drop table if exists buono cascade;
create table buono (
	codicePremio integer primary key,
	scadenza date,
	venditori_convenzionati varchar(500),
	constraint vincBuono foreign key (codicePremio) references premio(codice) on delete cascade on update cascade
);



drop table if exists traguardo;
create table traguardo (
	idObiettivo integer primary key default 80,
	idBuono integer,
	grado integer,
	constraint vincTraguardo1 foreign key (idObiettivo) references obiettivo(num_libri_richiesto) on delete cascade on update cascade,
	constraint vincTraguardo2 foreign key (idBuono) references buono(codicePremio)
);



-- SQL DML: Trigger e popolamento del database



create or replace function differenzia_socio() returns trigger as
$$
	begin
		if char_length(new.cf)<16 then insert into ente values (new.id, null);
			else if new.idObiettivo=80 then insert into senior values (new.id, null);
				else insert into junior values (new.id, null);
			end if;
		end if;
		return null;
	end;
$$ language plpgsql;

create trigger trigger_differenzia_socio after insert on socio
for each row execute procedure differenzia_socio();



create or replace function differenzia_premio() returns trigger as
$$
	begin
		if new.valore>100 then insert into viaggio values (new.codice, null, null);
			else insert into buono values (new.codice, null, null);
		end if;
		return null;
	end;
$$ language plpgsql;

create trigger trigger_differenzia_premio after insert on premio
for each row execute procedure differenzia_premio();



create or replace function differenzia_recensione() returns trigger as
$$
	begin
		if exists (SELECT idSocio FROM junior WHERE idSocio=new.idSocio) then insert into recensione_j values (new.id, null, null, null);
				else insert into recensione_s values (new.id, null);
		end if;
		return null;
	end;
$$ language plpgsql;

create trigger trigger_differenzia_recensione after insert on recensione
for each row execute procedure differenzia_recensione();



insert into obiettivo values
(100, 'Junior'),
(75, 'Junior'),
(60, 'Junior'),
(45, 'Junior'),
(30, 'Junior'),
(15, 'Junior'),
(80, 'Senior');



insert into socio values
(default, 100, 'MRCSRZ66M07F205O', 'Marcovaldo', 'Sforza', 'marcolino.forzas@gmail.com', 2, 20, false, 'valdomarco81', 'Pj!C$OpZL4sSt4uh'),
(default, 80, 'MSAWLM96D51Z221Q', 'Maisie', 'Williams', 'maisiemail@hotmail.com', 0, 300, false, 'maisiekiss<3', 'bMVb^@dAU1Hv!S&T'),
(default, 80, 'MYZHYA37R12Z606X', 'Hayo', 'Miyazaki', 'hayo.miyak@yahoo.com', 0, 200, false, 'miyaksooka33', '^KhpzgUuIcK6w2Bz'),
(default, 100, 'MNCSRI99C55G698Q', 'Siria', 'Mencucci', 'siria.mencucci@gmail.com', 3, 15, false, 'sirycuccicu99', 'P^yHVxNoI0j4Ro5F'),
(default, 100, 'WLLMSA96P44Z222L', 'Massimo', 'Iridio', 'maxiridio@sslazio.it', 0, 20, false, 'maxiridioboy', '@5aCS1a#c9rUgWIS'),
(default, 100, 'RDNZEI88H11D612M', 'Ezio', 'Redentore', 'ezio.redento@libero.it', 0, 10, false, 'ziosoezioRR', 'Gj1#7t&&7QdsA9nv'),
(default, null, '05329570963', 'LaPeltrinelli Internet Bookshop S.r.l.', null, 'mailinfo@lapeltrinelli.it', 0, 8000, null, 'lapeltrinelli', 'W76c!w4o#WR9QSs1'),
(default, 80, 'SPNGGN92M42Z103H', 'Georgiana', 'Spencer', 'ladyg@inbox.com', 1, 40, false, 'ladygg82', '1i03D^RenSQtcMgB'),
(default, 100, 'MRBSRA97S67E329C', 'Sara', 'Mirabelli', 'saretta37@alice.it', 0, 0, false, 'saretta37', 'r0bOlU#4fJ6QJi45'),
(default, 100, 'CPDFNC82R56A707H', 'Francesca', null, 'francy.wolfhead@gmail.com', 1, 25, false, 'wolfhead.f', '7^ndNq@0dQm10ZsM'),
(default, null, '08973230967', 'Amazzon EU S.a.r.L', null, 'mailinfo@amazzon.it', 0, 22000, null, 'amazzonEU', '#6dHqxWZMBu%zNZ4'),
(default, 100, 'CKRMKS86L54Z219U', 'Mikasa', 'Ackermann', 'mikasatuckasa@ubbi.com', 0, 0, false, 'mikasatukasa', 'wrb6Y%FZnb0d$2mz'),
(default, 100, 'ZMKMNS02C20Z107K', 'Magnus', 'Uzumaki', 'magnuzumaki@hotmail.com', 0, 100, false, 'magnuzumaki02', 'O5T&YflcOtOAj@09'),
(default, null, '00722360153', 'Casa Editrice Libraria Ulderico Oepli S.p.A', null, 'mailinfo@oepli.it', 0, 2000, null, 'oepli', 'phKp3z51MN!U$4^S'),
(default, 100, 'MLLNCK85B10E472I', 'Nick', 'Millman', 'nicky.millman@yahoo.com', 0, 0, false, 'nicky69', 'DcctWALN8#F9t8#5'),
(default, 100, 'DCHLRA69C56E472P', 'Debian', 'Dachille', 'lalla42@libero.it', 1, 5, false, 'lalla42', 'aIBE^9xPKSWA#^1d'),
(default, 100, 'DBNKKY01E49H501Q', 'Kikyo', 'Higurashi', 'debykiky@yahoo.com', 0, 0, false, 'kikydeby', 'hnO!pRjA#H6VB1DZ'),
(default, 100, 'RBRNTN67A24A512G', 'Antonio', 'Ruberti', 'antonio.ruberti@uniroma1.it', 0, 100, false, 'antonio.ruberti', '$9@5iLC7XWgsJhBC');



update junior set num_domande = 1 where idSocio = 1;
update junior set num_domande = 2 where idSocio = 5;
update junior set num_domande = 1 where idSocio = 9;
update junior set num_domande = 1 where idSocio = 10;
update junior set num_domande = 1 where idSocio = 12;
update junior set num_domande = 1 where idSocio = 17;



update ente set tipologia = 'libreria, casa editrice' where idSocio = 7;
update ente set tipologia = 'e-commerce' where idSocio = 11;
update ente set tipologia = 'casa editrice' where idSocio = 14;



insert into consiglio values
(1, '2017-2018', 1),
(2, '2018-2019', 2),
(3, '2019-2020', 2),
(4, '2020-2021', 3);



insert into premio values
(default, 700),
(default, 600),
(default, 500),
(default, 50),
(default, 40),
(default, 30),
(default, 20),
(default, 15);



update viaggio set evento = 'Fiera internazionale del libro di Francoforte' where codicePremio = 1;
update viaggio set periodo = 'Ottobre' where codicePremio = 1;

update viaggio set evento = 'Salone Internazionale del libro di Torino' where codicePremio = 2;
update viaggio set periodo = 'Maggio' where codicePremio = 2;

update viaggio set evento = 'Salone del libro di Parigi' where codicePremio = 3;
update viaggio set periodo = 'Marzo' where codicePremio = 3;



update senior set idConsiglio = 4 where idSocio = 2;
update senior set idViaggio = 1 where idSocio = 2;
update senior set n_dom_risposte = 2 where idSocio = 2;
update senior set n_dom_attese = 1 where idSocio = 2;
update senior set n_rec_risposte = 2 where idSocio = 2;
update senior set n_rec_attese = 0 where idSocio = 0;

update senior set idConsiglio = 4 where idSocio = 3;
update senior set idViaggio = 1 where idSocio = 3;
update senior set n_dom_risposte = 1 where idSocio = 3;
update senior set n_dom_attese = 1 where idSocio = 3;
update senior set n_rec_risposte = 1 where idSocio = 3;
update senior set n_rec_attese = 2 where idSocio = 3;

update senior set idConsiglio = 4 where idSocio = 8;
update senior set idViaggio = 1 where idSocio = 8;
update senior set n_dom_risposte = 1 where idSocio = 8;
update senior set n_dom_attese = 1 where idSocio = 8;
update senior set n_rec_risposte = 1 where idSocio = 8;
update senior set n_rec_attese = 1 where idSocio = 8;



insert into domanda values
(1, 8, '2019-10-27', '13:24:56' , 'Lettura veloce', 'Cosa mi consiglia per velocizzare la lettura?', null),
(5, 2, '2020-12-13', '01:34:23' , 'Letture invernali', 'Avrei voglia di leggere qualcosa di lungo e coinvolgente per le vacanze invernali, che mi consiglia? I miei generi sono: fantasy, avventura, romanzi', null),
(5, 8, '2021-08-20', '16:45:46' , 'Meglio libro o film l.o.t.r.', 'Ho visto tutta la saga del signore degli anelli, pensa sia il caso di leggere il libro oppure è una delusione in confronto?' , 'Libri e film spesso mostrano vicende da prospettive differenti. Trovo che arricchirsi con una visione diversa ma complementare di una storia sia interessante e istruttivo.'),
(9, 3, '2021-06-21', '12:58:12' , 'Distrarsi mentre si legge', 'Mi piace leggere, ma non riesco mai a dedicarmici con cura, dopo un poco mi distraggo perché penso alle tante cose che mi passano per la testa, come posso fare? :(', null),
(10, 2, '2021-03-02', '08:01:35' , 'Braviii', 'Non avevo una domanda da fare, ma volevo solo dirvi che adoro ReadyRead *-*' , 'Ci fa piacere ;D'),
(12, 2, '2021-08-06', '11:12:02' , 'Leggere e studiare', 'Dopo tutto il giorno che studio non mi prende proprio leggere, come risolvo?' , 'Non devi forzarti. Fai come ti senti e tieni sempre a mente i dieci diritti del lettore di Daniel Pennac.'),
(17, 3, '2021-08-17', '18:21:07' , 'Alta voce o mentalmente', 'Meglio leggere ad alta voce o a mente, in quali situazioni mi aiuta l una o l altra cosa?' , 'Leggere a voce alta ti aiuta ad assimilare concetti e suoni, ma dopo un poco può diventare stancante, in tal caso leggere a mente è più coinvolgente e meno impegnativo(inoltre risparmi tempo).');



insert into libro values
(9788854165380, 'Il fu Mattia Pascal', 'Luigi Pirandello', 'Romanzo, narrativa', 'Newton Compton Editori', 'Italiano'),
(9788807903373, 'Le mille e una notte', 'vari sconosciuti', 'Fantasy', 'Feltrinelli', 'Italiano'),
(9781840226355, 'Ulysses', 'James Joyce', 'Romanzo, narrativa', 'Wordsworth', 'English'),
(9788845297465, 'La scimmia nuda', 'Desmond Morris', 'Saggio', 'Bombiani', 'Italiano'),
(9788854165052, 'Orgoglio e pregiudizio', 'Jane Austen', 'Romanzo rosa', 'Newton Compton Editori', 'Italiano'),
(9788854165533, 'Il ritratto di Dorian Gray', 'Oscar Wilde', 'Fiction gotica', 'Newton Compton Editori', 'Italiano'),
(9788804637462, 'Alla ricerca del tempo perduto', 'Marcel Proust', 'Romanzo', 'Oscar Mondadori', 'Italiano'),
(9788817124553, 'Delitto e castigo', 'Fëdor Dostoevskij', 'Narrativa filosofica, Narrativa psicologica, Romanzo giallo', 'BUR Biblioteca Univ. Rizzoli', 'Italiano');



insert into recensione values
(default, 9788807903373, 1, 'Marcovaldo Sforza', '2021-05-02', 5, 'Sublime', 'Le mille e una notte parla di blablabla'),
(default, 9788804637462, 1, 'Marcovaldo Sforza', '2021-06-13', 2, 'Tempo perso, haha capita?', 'Il tempo perduto è quello de legge sto libro, so 3724 pagine mica bruscolini'),
(default, 9788845297465, 4, 'Siria Mencucci', '2021-07-30', 4, 'Emozionante!', 'La scimmia nuda parla di blablabla'),
(default, 9788854165380, 4, 'Siria Mencucci', '2021-08-18', 1, 'Bruttissimo', 'Il fu Mattia Pascal parla di blablabla'),
(default, 9788804637462, 4, 'Siria Mencucci', '2021-06-08', 4, 'Antropologicamente antropologico', 'Alla ricerca del tempo perduto parla di blablabla'),
(default, 9788854165052, 8, 'Georgiana Spencer', '2021-08-15', 3, 'Interessante...', 'Orgoglio e pregiudizio parla di blablabla'),
(default, 9788854165380, 10, 'Francesca', '2021-08-20', 5, 'Fighissimoo', 'Il fu Mattia Pascal parla di blablabla'),
(default, 9781840226355, 16, 'Debian Dachille', '2021-07-27', 3, 'Troppo intripposo', 'Ulysses parla di blablabla');



update recensione_j set idJunior = 1 where idRecensione = 2;
update recensione_j set idSenior = 3 where idRecensione = 2;

update recensione_j set idJunior = 4 where idRecensione = 3;
update recensione_j set idSenior = 8 where idRecensione = 3;

update recensione_j set idJunior = 4 where idRecensione = 4;
update recensione_j set idSenior = 3 where idRecensione = 4;

update recensione_j set idJunior = 1 where idRecensione = 1;
update recensione_j set idSenior = 2 where idRecensione = 1;
update recensione_j set esitoValutazione = true where idRecensione = 1;

update recensione_j set idJunior = 16 where idRecensione = 8;
update recensione_j set idSenior = 2 where idRecensione = 8;
update recensione_j set esitoValutazione = false where idRecensione = 8;

update recensione_j set idJunior = 4 where idRecensione = 5;
update recensione_j set idSenior = 3 where idRecensione = 5;
update recensione_j set esitoValutazione = true where idRecensione = 5;

update recensione_j set idJunior = 10 where idRecensione = 7;
update recensione_j set idSenior = 8 where idRecensione = 7;
update recensione_j set esitoValutazione = true where idRecensione = 7;



update recensione_s set idSenior = 8 where idRecensione = 6;



insert into collezione values
(1, '2020-2021', 1),
(2, '2017-2018', 101),
(2, '2018-2019', 106),
(2, '2019-2020', 85),
(2, '2020-2021', 0),
(3, '2017-2018', 112),
(3, '2018-2019', 98),
(3, '2019-2020', 81),
(3, '2020-2021', 0),
(4, '2020-2021', 1),
(5, '2020-2021', 0),
(6, '2020-2021', 0),
(7, '2020-2021', 0),
(8, '2019-2020', 130),
(8, '2020-2021', 1),
(9, '2020-2021', 0),
(10, '2019-2020', 63),
(10, '2020-2021', 1),
(11, '2020-2021', 0),
(12, '2020-2021', 0),
(13, '2020-2021', 0),
(14, '2018-2019', 23),
(14, '2019-2020', 36),
(14, '2020-2021', 0),
(15, '2020-2021', 0),
(16, '2020-2021', 0),
(17, '2020-2021', 0),
(18, '2020-2021', 0);



update buono set scadenza = '2023-05-18' where codicePremio = 4;
update buono set venditori_convenzionati = 'laPeltrinelli, Oepli, Libreria universidaria' where codicePremio = 4;

update buono set scadenza = '2023-06-30' where codicePremio = 5;
update buono set venditori_convenzionati = 'Amazzon, laPeltrinelli, Oepli' where codicePremio = 5;

update buono set scadenza = '2023-07-24' where codicePremio = 6;
update buono set venditori_convenzionati = 'Amazzon' where codicePremio = 6;

update buono set scadenza = '2023-08-11' where codicePremio = 7;
update buono set venditori_convenzionati = 'Amazzon' where codicePremio = 7;

update buono set scadenza = '2023-10-21' where codicePremio = 8;
update buono set venditori_convenzionati = 'Amazzon, laPeltrinelli' where codicePremio = 8;



insert into traguardo values
(75, 4, 5),
(60, 5, 4),
(45, 6, 3),
(30, 7, 2),
(15, 8, 1);
