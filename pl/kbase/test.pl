:- module(test,
    [ reply/1
    ]).
:- use_module(library('http/http_client')).
:- use_module(library('http/http_mime_plugin')). % Decode multipart data


man(george).

reply(Request) :-
  member(path('/work'), Request),
  format(user_error, 'Starting work ...', []),
  forall(between(1, 10000000, _), atom_codes(_, "hello")),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format('ok~n').