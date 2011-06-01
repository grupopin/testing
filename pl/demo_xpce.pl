#!/usr/bin/pl -f none -t server -s
:- use_module(library('http/xpce_httpd')).
:- use_module(demo_body).

server(Port) :-
	http_server(reply,
		    [ port(Port)
		    ]).


