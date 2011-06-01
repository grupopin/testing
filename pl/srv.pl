:- use_module(library('http/thread_httpd')).
:- use_module(library('http/http_client')).
:- use_module(library('http/http_dispatch')).
:- use_module(library('http/html_write')).
:- use_module(library('http/http_session')).
:- use_module(library('http/http_error')).
:- use_module(library('http/http_mime_plugin')).
:- use_module(library('http/http_json.pl')).
:- use_module(library('http/json')).
:- use_module(library('http/json_convert.pl')).
:- use_module(library('http/http_open')).

:- use_module(library('url')).

%:- load_files(http_load).

%charsio.pl was copied from 5.6.64
:- use_module(library('charsio')).

:- style_check(-atom).



:- load_files('kbase/xml_graph.pl').
:- load_files('kbase/xml_apa.pl').
:- load_files('kbase/xml_tap.pl').
:- load_files('kbase/xml_arch.pl').
% below file includes jw and paint
:- load_files('kbase/xml_paint_2009-05-27_172901.pl').


:- load_files('kbase/sql_text_1.03.pl').
:- load_files('kbase/sql_text_type.pl').
:- load_files('kbase/sql_text_cat.pl').


:- load_files('kbase/skins.pl').

%:- load_files('kbase/trans.pl').

load_http_file(Url):-
  parse_url(Url, Parts),
  http_open(Parts, In,[]),
  load_files(Url, [stream(In)]),
  close(In).
        
% loading instead of kbase/trans.pl        
:- load_http_file('http://hw-archive.com/kb/get-trans/?ps=ad1234').


server :-
  server(3000, []).

server(Port, Options) :-
  http_server(reply,
        [ port(Port),
          timeout(20)
        | Options
        ]).
        
reply(_) :-
  flag(request, N, N+1),
  fail.
  
reply(Request) :-
  member(path('/assert'), Request),
  memberchk(search(Search), Request),
  memberchk(term=TermStr, Search),
  format(user_error, 'Starting work ...', []),
  %read_from_chars(TermStr,X),assert(X),
  import_term(TermStr),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format("Done: ~w <br>\n",[TermStr]).
  
reply(Request) :-
  member(path('/retract'), Request),
  memberchk(search(Search), Request),
  memberchk(term=TermStr, Search),
  format(user_error, 'Starting work ...', []),
  %read_from_chars(TermStr,X),retract(X),
  retract_term(TermStr),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format("Done: ~w <br>\n",[TermStr]).
  
reply(Request) :-
  member(path('/quest'), Request),
  memberchk(search(Search), Request),
  memberchk(goal=GoalStr, Search),
  format(user_error, 'Starting work ...', []),
  exec_term_once(GoalStr,Res),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format("Done: ~w : ~w <br>",[GoalStr, Res]).
  
reply(Request) :-
  member(path('/list'), Request),
  memberchk(search(Search), Request),
  memberchk(goal=GoalStr, Search),
  format(user_error, 'Starting work ...', []),
  format('Content-type: text/plain~n~n', []),
  print_result(GoalStr),
  %format("Done: ~w<br>",[GoalStr]),
  format(user_error, 'done!~n', []).
  
reply(Request) :-
  member(path('/skinSearch'), Request),
  memberchk(search(Search), Request),
  memberchk(qry=QryStr, Search),
  findall_skin_json(QryStr,JSONOut),
  reply_json(JSONOut).
  
reply(Request) :-
  member(path('/graphSearch'), Request),
  memberchk(search(Search), Request),
  memberchk(qry=QryStr, Search),
  findall_graph_json(QryStr,JSONOut),
  reply_json(JSONOut).
  
reply(Request) :-
  member(path('/search'), Request),
  memberchk(search(Search), Request),
  memberchk(qry=QryStr, Search),
  memberchk(itemType=ItemType, Search),
  memberchk(parentItemType=ParentItemType, Search),
  memberchk(parentItemId=ParentId, Search),
  atom_number(ParentId, ParentIdNum),
  concat_atom([findall,ItemType,json],'_',Find),
  %format(user_error, 'Starting work ...~w,~w,~w,~w~n', [Find,QryStr,ParentItemType,ParentIdNum]),
  call(Find,QryStr,JSONOut,ParentItemType,ParentIdNum),
  %findall_graph_json(QryStr,JSONOut,ParentItemType,ParentIdNum),
  reply_json(JSONOut).
  
  
reply(Request) :-
  member(path('/search_parent'), Request),
  memberchk(search(Search), Request),
  memberchk(qry=QryStr, Search),
  memberchk(itemType=ItemType, Search),
  memberchk(parentItemType=ParentItemType, Search),
  memberchk(childItemId=ChildId, Search),
  atom_number(ChildId, ChildIdNum),
  concat_atom([findall_parent,ItemType,json],'_',Find),
  call(Find,QryStr,JSONOut,ParentItemType,ChildIdNum),
  reply_json(JSONOut). 

%find one result  
reply(Request):-
  member(path('/exec'), Request),
  memberchk(search(Search), Request),
  memberchk(qry=QryStr, Search),
  exec_all_dynamic(QryStr,JSONOut),
  reply_json(JSONOut).

reply(Request):-
  member(path('/check'), Request),
  memberchk(search(Search), Request),
  memberchk(qry=QryStr, Search),
  check_once(QryStr),
  format('Content-type: text/plain~n~n', []),
  format("true",[]).

%find all results  
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
  fall(QryStr,Lists),
  format('Content-type: text/plain~n~n', []),
  write_terms(Lists).
  %print_list(Lists). 



fall(Str,SortedBag):-
 read_from_chars(Str,X),
 findall(X,call(X),Bag),
 sort(Bag,SortedBag).

write_terms([]).
write_terms([H|T]):-
 write_term(H,[attributes(write),portray(true),backquoted_string(true),quoted(true),character_escapes(true)]),format("###",[]),nl,
 write_terms(T).
 
print_list([]).
print_list([H|T]):-
 format("~w~n",[H]),
 print_list(T).
 
terms_to_atoms([],[]).
terms_to_atoms([H|T],[H1|T1]):-
 term_to_atom(H,H1),
  terms_to_atoms(T,T1). 
   
:-json_object
  skinJSON(n:integer,title:atom),
  graphJSON(n:integer,title:atom).

graph_to_(_,_).
apa_to_(_,_).
tap_to_(_,_).
arch_to_(_,_).
paint_to_(_,_).
text_to_(_,_).
audio_to_(_,_).
video_to_(_,_).
maintitle_to_(_,_).
highlight_to_(_,_).
skin_to_(_,_).

hw_comments(Id,Comment):-
 hundertwasser_comment(Id,Comment);
 hundertwasser_comment_en(Id,Comment);
 hundertwasser_comment_ge(Id,Comment).  

%Find items
%find graphics
findall_graph_json(QryStr,JSONOut,ParentItemType,ParentItemId):-
  findall(JsonObj,find_graph_json(QryStr,JsonObj,ParentItemType,ParentItemId),Bag),
  %will sort additionaly in php. But here will do simple asc sort and remove duplicates
  sort(Bag,SortedBag),
  prolog_to_json(SortedBag, JSONOut).

find_graph_json(QryStr,JsonObj,ParentItemType,ParentItemId):-
 find_graph(QryStr,N,Fullstr,ParentItemType,ParentItemId),
 JsonObj=graphJSON(N,Fullstr).
 
find_graph(Str,N,Fullstr,ParentItemType,ParentItemId):-
  graph_title(N,Fullstr),
  concat_atom(['graph_to','_',ParentItemType],LinkTerm),
  %format(user_error,"~w,~w,~w~n",[LinkTerm,N,ParentItemId]),
  call(LinkTerm,N,ParentItemId),
  %graph_to_skin(N,ParentItemId),
  downcase_atom(Fullstr, FullstrL),
  downcase_atom(Str, StrL),
  sub_atom(FullstrL,_,_,_,StrL).
  
%parent search version   
findall_parent_graph_json(QryStr,JSONOut,ParentItemType,ChildItemId):-
  findall(JsonObj,find_parent_graph_json(QryStr,JsonObj,ParentItemType,ChildItemId),Bag),
  %will sort additionaly in php. But here will do simple asc sort and remove duplicates
  sort(Bag,SortedBag),
  prolog_to_json(SortedBag, JSONOut).

find_parent_graph_json(QryStr,JsonObj,ParentItemType,ChildItemId):-
 find_parent_graph(QryStr,N,Fullstr,ParentItemType,ChildItemId),
 JsonObj=graphJSON(N,Fullstr).
 
find_parent_graph(Str,N,Fullstr,ParentItemType,ChildItemId):-
  graph_title(N,Fullstr),
  concat_atom(['graph_to','_',ParentItemType],LinkTerm),
  %format(user_error,"~w,~w,~w~n",[LinkTerm,N,ChildItemId]),
  call(LinkTerm,ChildItemId,N),
  %graph_to_skin(N,ChildItemId),
  downcase_atom(Fullstr, FullstrL),
  downcase_atom(Str, StrL),
  sub_atom(FullstrL,_,_,_,StrL).  

%find skin
findall_skin_json(QryStr,JSONOut,ParentItemType,ParentItemId):-
  findall(JsonObj,find_skin_json(QryStr,JsonObj,ParentItemType,ParentItemId),Bag),
  %will sort additionaly in php. But here will do simple asc sort and remove duplicates
  sort(Bag,SortedBag),
  prolog_to_json(SortedBag, JSONOut).

find_skin_json(QryStr,JsonObj,ParentItemType,ParentItemId):-
 find_skin(QryStr,N,Fullstr,ParentItemType,ParentItemId),
 JsonObj=skinJSON(N,Fullstr).

%case insensitive search
find_skin(Str,N,Fullstr,ParentItemType,ParentItemId):-
  skin(N,Fullstr),
  concat_atom(['skin_to','_',ParentItemType],LinkTerm),
  call(LinkTerm,N,ParentItemId),
  downcase_atom(Fullstr, FullstrL),
  downcase_atom(Str, StrL),
  sub_atom(FullstrL,_,_,_,StrL).
  
%parent search version    
findall_parent_skin_json(QryStr,JSONOut,ParentItemType,ChildItemId):-
  findall(JsonObj,find_parent_skin_json(QryStr,JsonObj,ParentItemType,ChildItemId),Bag),
  %will sort additionaly in php. But here will do simple asc sort and remove duplicates
  sort(Bag,SortedBag),
  prolog_to_json(SortedBag, JSONOut).

find_parent_skin_json(QryStr,JsonObj,ParentItemType,ChildItemId):-
 find_parent_skin(QryStr,N,Fullstr,ParentItemType,ChildItemId),
 JsonObj=skinJSON(N,Fullstr).

%case insensitive search
find_parent_skin(Str,N,Fullstr,ParentItemType,ChildItemId):-
  skin(N,Fullstr),
  concat_atom(['skin_to','_',ParentItemType],LinkTerm),
  call(LinkTerm,ChildItemId,N),
  downcase_atom(Fullstr, FullstrL),
  downcase_atom(Str, StrL),
  sub_atom(FullstrL,_,_,_,StrL).

   
import_term(TermStr):-
 read_from_chars(TermStr,X),assert(X).
 
retract_term(TermStr):-
 read_from_chars(TermStr,X),retract(X).

check_once(TermStr):-
 read_from_chars(TermStr,X),once(X).

exec_term_once(TermStr,X):-
 read_from_chars(TermStr,X),once(X).
 
exec_term(TermStr,X):-
 read_from_chars(TermStr,X),call(X).

exec_all_dynamic(QryStr,JSONOut):-
  findall(List,exec_term_dynamic(QryStr,List),Bag),
  %will sort additionaly in php. But here will do simple asc sort and remove duplicates
  sort(Bag,SortedBag),
  prolog_to_json(SortedBag, JSONOut). 

exec_term_dynamic(TermStr, List):-
 read_from_chars(TermStr,X),
 call(X),
 X=..List. 

exec_all_dynamic_list(QryStr, SortedBag):-
  findall(List,exec_dynamic_list(QryStr,List),Bag),
  %will sort additionaly in php. But here will do simple asc sort and remove duplicates
  sort(Bag,SortedBag). 

%converts text to terms and executes them, then puts into list
exec_dynamic_list(TermStr,Lists):-
 read_from_chars(TermStr,X),
 call(X),
 X=..Lst,
 terms_to_list(Lst,Lists).
 
exec_all_list(TermStr, SortedBag):-
  findall(List,exec_list(TermStr,List),Bag),
  %will sort additionaly in php. But here will do simple asc sort and remove duplicates
  sort(Bag,SortedBag). 

%executes terms
exec_list(TermStr,Lists):-
 call(TermStr),
 TermStr=..Lst,
 terms_to_list(Lst,Lists).     

 
%convert list of terms to list of lists
% [skin(1,X),skin(1,Y)] => [[skin, 1, X], [skin, 1, Y]]
terms_to_list([],[]).
terms_to_list([H|T],[Lst|Lsts]):-
  H=..Lst,
  terms_to_list(T,Lsts). 


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

exec_many(QryStr, JSONOut):-
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

print_result(TermStr):-
  findall(X,exec_term(TermStr,X),Bag),
  print_list(Bag).

search_atom(Fullstr,Str):-
  downcase_atom(Fullstr, FullstrL),
  downcase_atom(Str, StrL),
  sub_atom(FullstrL,_,_,_,StrL).
 
