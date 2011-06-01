% Author:  Levan
% Date: 17/03/2009
:- use_module(library('http/thread_httpd')).
:- use_module(library('http/http_dispatch')).
:- use_module(library('http/html_write')).
:- use_module(library('http/http_session')).
:- use_module(library('http/http_error')).
:- use_module(library('http/http_json.pl')).
:- use_module(library(http/json)).
:- use_module(library('http/json_convert.pl')).

%charsio.pl was copied from 5.6.64
:- use_module(library('charsio')).

:- style_check(-atom).


:- load_files('kbase/xml_graph.pl').
:- load_files('kbase/sql_text.pl').
:- load_files('kbase/sql_text_type.pl').
:- load_files('kbase/sql_text_cat.pl').


:- load_files('kbase/skins.pl').
:- load_files('kbase/trans.pl').

server :-
  server(5000, []).

server(Port, Options) :-
  http_server(reply,
        [ port(Port),
          timeout(20)
        | Options
        ]).

reply(_) :-
  flag(request, N, N+1),
  fail.
  
reply(Request):-
  member(path('/exec_join'), Request),
  memberchk(search(Search), Request),
  memberchk(qry=QryStr, Search),
  exec_all_dynamic_list(QryStr,Lists),
  reply_json(Lists).

%find all results
reply(Request):-
  member(path('/exec_join_list'), Request),
  memberchk(search(Search), Request),
  memberchk(qry=QryStr, Search),
  exec_many(QryStr,Lists),
  %prolog_to_json(Lists,Json),
  %reply_json(Json).
  format('Content-type: text/plain~n~n', []),
  %length(Lists,Sz),
  %format('~w~n~n', [QryStr]),
  %format('~w~n', [Sz]),
  print_list(Lists).

  %terms_to_list(Lst,Lists).
  %prolog_to_json(Lists,Json).

mergelist(Lst,Merged):-
 nth0(1,Lst,El1),
 nth0(2,Lst,El2),
 El2=..Lst2,
 select((','),Lst2,Rest),
 select(El1,Merged,Rest),!.

exec_two(QryStr, SortedBag):-
  findall(Lists,exec_goal2(QryStr,Lists),Bag),
  %will sort additionaly in php. But here will do simple asc sort and remove duplicates
  sort(Bag,SortedBag).

exec_goal2(TermStr,Lists):-
 read_from_chars(TermStr,X),
 call(X),
 X=..Lists.

exec_many(QryStr, SortedBag):-
  findall(Lists,exec_goal(QryStr,Lists),Bag),
  %will sort additionaly in php. But here will do simple asc sort and remove duplicates
  sort(Bag,SortedBag).

%converts text to terms and executes them, then puts into list
exec_goal(TermStr,Lists):-
 read_from_chars(TermStr,X),
 call(X),
 X=..Lst,
 mergelist(Lst,Lists).
 %length(Lists,2).

list_empty([]).
list_not_empty(X):-
  length(X,Sz), Sz>0.
  
print_list([]).
print_list([H|T]):-
 format("~w~n",[H]),
 print_list(T).  
  
search_atom(Fullstr,Str):-
  downcase_atom(Fullstr, FullstrL),
  downcase_atom(Str, StrL),
  sub_atom(FullstrL,_,_,_,StrL).
