:- use_module(library('http/thread_httpd')).
:- use_module(library('http/html_write')).
:- use_module(library('http/http_session')).
:- use_module(library('http/http_error')).
%charsio.pl was copied from 5.6.64
:- use_module(library('charsio')).

:- style_check(-atom).

exec_term(TermStr,X):-
 read_from_chars(TermStr,X),once(X).


print_list([]).
print_list([H|T]):-
 format("~w~n",[H]),
 print_list(T).

print_result(TermStr):-
  exec_term(TermStr,Y),
  findall(X,Y,Bag),
  print_list(Bag).
 
