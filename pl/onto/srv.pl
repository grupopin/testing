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

:-dynamic object/2.
:-multifile object/2.

:-dynamic object/3.
:-multifile object/3.

:-dynamic object/4.
:-multifile object/4.

/*
:- load_files('../kbase/onto/sql_text_onto_2009-06-15_090414').
:- load_files('../kbase/onto/tap_onto_2009-06-15_090408').
:- load_files('../kbase/onto/paint_onto_2009-06-15_090357').
:- load_files('../kbase/onto/jw_onto_2009-06-15_090346').
:- load_files('../kbase/onto/hwg_onto_2009-06-15_090340').
:- load_files('../kbase/onto/arch_onto_2009-06-15_090331').
:- load_files('../kbase/onto/apa_onto_2009-06-15_090324').
:- load_files('../kbase/onto/skin.pl').

:- load_files('../kbase/onto/current/kbase.pl').

:- load_files('kbase/sql_text_type.pl').
:- load_files('kbase/sql_text_cat.pl').
*/
%:- load_files('kbase/trans.pl').

load_http_file(Url,Opts):-
  parse_url(Url, Parts),
  http_open(Parts, In,Opts),
  load_files(Url, [stream(In)]),
  close(In).

% loading instead of kbase/trans.pl
:- load_http_file('http://hw-archive.com/kb/get-trans/?ps=an1234',[authorization(basic('srv','pl745Ghj'))]).
%:- load_files('kbase/trans.pl').


:-index(object(1,1,1,1)).
:-index(link(1,1,1,1)).

server :-
  server(3100, []).

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
  memberchk(user=UserStr, Search),
  memberchk(term=TermStr, Search),
  atom_chars(TermStr,TermChars),
  length(TermChars,Len),
  Len > 0,
  format(user_error, 'assert(~w,user=~w) Starting work ...', [TermStr,UserStr]),
  %read_from_chars(TermStr,X),assert(X),
  import_term(TermStr),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format("Done: ~w <br>\n",[TermStr]).

reply(Request):-
        member(path('/updateTitle'), Request),
        memberchk(search(Search), Request),
        memberchk(type=Type, Search),
        memberchk(id=Id, Search),
        memberchk(title=Title, Search),
        update_object_title(Type, Id, Title),
        format('Content-type: text/plain~n~n', []),
        format('Done',[]).

reply(Request):-
 member(path('/createHl'), Request),
 member(method(post), Request), !,
 http_read_data(Request, Data, ['application/x-www-form-urlencoded']),
 memberchk(nextId=Id, Data),
 memberchk(hl_short=HlShort, Data), memberchk(hl_text=HlText, Data), memberchk(child=Child, Data), memberchk(child_id=ChildId, Data),
 memberchk(parent=Parent, Data), memberchk(parent_id=ParentId, Data),
 retract(object(hl,Id,'',[])),
 assert(object(hl,Id,hl_short,[id-Id,title-HlShort,text-HlText])),
 assert(link(Child,ChildId,hl,Id)),
 assert(link(hl,Id,Parent,ParentId)),
 format('Content-type: text/plain~n~n', []),
 format('Done',[]).

reply(Request):-
 member(path('/updateObjectListProperty'), Request),
 member(method(post), Request), !,
 http_read_data(Request, Data, ['application/x-www-form-urlencoded']),
 memberchk(type=Type, Data),
 memberchk(id=Id, Data),
 memberchk(key=Key, Data),
 memberchk(val=Val, Data),
 memberchk(user=User, Data),
 update_object_list_property(Type, Id, Key, Val),
 %write updated object into the log%%%%%%%%%%%%%
 open('../kbase/onto/current/objects.pl',append,ObjStream,[lock(write),close_on_abort(true)]),
 object(Type,Id,Title,List),
 get_time(T),stamp_date_time(T,date(Year,Month,Day,Hour,Min,Sec,_,_,_),0),
 format(ObjStream,"%~w###~w-~w-~w ~w:~w:~w###object###~w###~w###~w~n",[User, Year, Month, Day, Hour, Min, Sec, Type, Id,Title]),
 writeq(ObjStream,List),
 format(ObjStream,"~n~n",[]),
 close(ObjStream),
 %%%%%
 %format(user_error,'updateObjectListProperty(~w,~w,~w)~n',[Type, Id, Key]),
 format('Content-type: text/plain~n~n', []),
 format('Done',[]).



%find out next ID for new highlight
reply(Request) :-
        member(path('/next_hl'),Request),
        %memberchk(search(Search),Request),
        %find maximum id of highlight
        find_last_hl(N),
        N1 is N+1,
        %reserve next number
        atom_number(N1Str,N1),
        assert(object(hl,N1Str,'',[])),
        format('Content-type: text/plain~n~n', []),
        format('~w',[N1]),
        format(user_error, 'Last HL Num: ~w~n', [N1]).


%assert object and retract object of same id and type
 reply(Request) :-
  member(path('/insertTerm'), Request),
  member(method(post), Request), !,
  http_read_data(Request, Data, ['application/x-www-form-urlencoded']),
  memberchk(term=TermStr, Data),
  %memberchk(type=Type, Data),
  %memberchk(id=Id, Data),
  format(user_error, 'insertTerm/Starting work ...', []),
  import_term(TermStr),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format('Done: ~w<br>~n',[TermStr]).

reply(Request) :-
  member(path('/updateTerm'), Request),
  member(method(post), Request), !,
  http_read_data(Request, Data, ['application/x-www-form-urlencoded']),
  memberchk(term=TermStr, Data),
  memberchk(type=Type, Data),
  memberchk(id=Id, Data),
  format(user_error, 'updateTerm/ Starting work ...', []),
  retract(object(Type,Id,_,_)),
  format(user_error, 'retracted ...', []),
  import_term(TermStr),
  format(user_error, '~w~n', [TermStr]),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format('Done: ~w<br>~n',[TermStr]).



reply(Request) :-
  member(path('/assert3'), Request),
  member(method(post), Request), !,
  http_read_data(Request, Data, ['application/x-www-form-urlencoded']),
  memberchk(term1=TermStr1, Data),
  memberchk(term2=TermStr2, Data),
  memberchk(term3=TermStr3, Data),

  format(user_error, 'assert3 Starting work ...', []),
  %read_from_chars(TermStr,X),assert(X),
  import_term(TermStr1),
  import_term(TermStr2),
  import_term(TermStr3),

  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format('Done: ~w, ~w, ~w <br>~n',[TermStr1,TermStr2,TermStr3]).

reply(Request) :-
  member(path('/assert4'), Request),
  memberchk(search(Search), Request),
  memberchk(term1=TermStr1, Search),
  memberchk(term2=TermStr2, Search),
  memberchk(term3=TermStr3, Search),
  memberchk(term4=TermStr4, Search),
  %format(user_error, 'assert4 Starting work ...', []),
  %read_from_chars(TermStr,X),assert(X),
  retract_term(TermStr1),
  import_term(TermStr2),
  import_term(TermStr3),
  import_term(TermStr4),
  %format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format('Done',[]).


reply(Request) :-
  member(path('/retract'), Request),
  memberchk(search(Search), Request),
  memberchk(term=TermStr, Search),
  format(user_error, 'retract term ...', []),
  %read_from_chars(TermStr,X),retract(X),
  retract_term(TermStr),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format("Done",[]).

reply(Request) :-
  member(path('/retract3'), Request),
  memberchk(search(Search), Request),
  memberchk(term1=TermStr1, Search),
  memberchk(term2=TermStr2, Search),
  memberchk(term3=TermStr3, Search),
  format(user_error, 'Retract3...', []),
  %read_from_chars(TermStr,X),assert(X),
  retract_term(TermStr1),
  retract_term(TermStr2),
  retract_term(TermStr3),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format('Done~n',[]).

reply(Request) :-
  member(path('/quest'), Request),
  memberchk(search(Search), Request),
  memberchk(goal=GoalStr, Search),
  format(user_error, 'quest Starting work ...', []),
  exec_term_once(GoalStr,Res),
  format(user_error, 'done!~n', []),
  format('Content-type: text/plain~n~n', []),
  format("Done: ~w : ~w <br>",[GoalStr, Res]).

reply(Request) :-
  member(path('/list'), Request),
  memberchk(search(Search), Request),
  memberchk(goal=GoalStr, Search),
  format(user_error, 'list Starting work ...', []),
  format('Content-type: text/plain~n~n', []),
  print_result(GoalStr),
  %format("Done: ~w<br>",[GoalStr]),
  format(user_error, 'done!~n', []).



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

 reply(Request):-
  member(path('/printAllObjects'), Request),
  memberchk(search(Search), Request),
  memberchk(type=TypeSearch, Search),
  memberchk(title=TitleSearch, Search),
  memberchk(id=IdSearch, Search),
  memberchk(fields=Fields, Search),
  read_from_chars(Fields,FieldsTerm),
  format('Content-type: text/plain~n~n', []),
  %format('~w',[Fields]),
  printAllObjectLists(TypeSearch, IdSearch, TitleSearch, FieldsTerm).

 reply(Request):-
  member(path('/print_objects'), Request),
  memberchk(search(Search), Request),
  memberchk(type=TypeOfObject, Search),
  memberchk(qry=Text, Search),
  format('Content-type: text/plain~n~n', []),
  printObjectList(TypeOfObject,Text).

 reply(Request):-
  member(path('/selectOrderLimit'), Request),
  memberchk(search(Search), Request),
  memberchk(type=Type, Search),
  memberchk(key=PropKey, Search),
  memberchk(search=PropSearch, Search),
  memberchk(selected=SelectedKeys, Search),
  memberchk(from=FromEl, Search),
  memberchk(limit=LimitEl, Search),
  memberchk(order_by=OrderBy, Search),
  memberchk(order_dir=OrderDir, Search),
  read_from_chars(SelectedKeys,FieldsTerm),
  read_from_chars(FromEl, FromTerm),
  read_from_chars(LimitEl, LimitTerm),
  fixAtom(PropSearch,PropSearchTerm),
  format('Content-type: text/plain~n~n', []),
  format(user_error, 'selectOrderByLimit(~w, ~w, ~w, ~w, ~w, ~w, ~w, ~w) ~n', [Type, PropKey, PropSearchTerm, FieldsTerm, FromTerm, LimitTerm, OrderBy, OrderDir]),
  %selectOrderLimit(Type, PropKey, PropSearchTerm, FieldsTerm, FromTerm, LimitTerm, Bag),
  selectOrderByLimit(Type, PropKey, PropSearchTerm, FieldsTerm, FromTerm, LimitTerm, OrderBy, OrderDir, Bag),
  write_terms(Bag).
  %format('selectOrderLimit(~w, ~w, ~w, ~w, ~w, ~w) ~n', [Type, PropKey, PropSearch, FieldsTerm, FromEl, LimitEl]).
  %format(user_error, 'selectOrderLimit(~w, ~w, ~w, ~w, ~w, ~w) ~n', [Type, PropKey, PropSearch, FieldsTerm, FromEl, LimitEl]).




%for request without propSearch
reply(Request):-
  member(path('/selectLinked'), Request),
  memberchk(search(Search), Request),
  %path is [mt,hl,apa] or [skin,mt] (currently 2-3 levels deep, need to write multi level, recursive, OR just 4,5,6. Which is sufficient)
  memberchk(path=Path, Search),
  %parent_id is list of parents
  memberchk(parent_id=Parents, Search),
  %memberchk(type=Type, Search),
  memberchk(key=PropKey, Search),
  memberchk(search=PropSearch, Search),
  memberchk(selected=SelectedKeys, Search),
  memberchk(from=FromEl, Search),
  memberchk(limit=LimitEl, Search),
  memberchk(order_by=OrderBy, Search),
  memberchk(order_dir=OrderDir, Search),
  %read_from_chars(PropSearch,PropSearchTerm),
  %string_to_atom(PropSearchStr,PropSearchTerm),
  read_from_chars(SelectedKeys,FieldsTerm),
  read_from_chars(FromEl, FromTerm),
  read_from_chars(LimitEl, LimitTerm),
  read_from_chars(Path, PathList),
  read_from_chars(Parents, ParentsList),
  fixAtom(PropSearch,PropSearchTerm),

  format('Content-type: text/plain~n~n', []),
  format(user_error,'selectLinkedOrderByLimit(~w, ~w, ~w, ~w, ~w, ~w, ~w, ~w, ~w) ~n', [Path, ParentsList, PropKey, PropSearchTerm, FieldsTerm, FromTerm, LimitTerm, OrderBy, OrderDir]),
  %selectLinkedOrderLimit(PathList, ParentsList, PropKey, PropSearchTerm, FieldsTerm, FromTerm, LimitTerm, Bag),
  selectLinkedOrderByLimit(PathList,ParentsList, PropKey, PropSearchTerm, FieldsTerm, FromTerm, LimitTerm, OrderBy, OrderDir, Bag),
  write_terms(Bag).


reply(Request):-
  member(path('/countLinkedObjects'), Request),
  memberchk(search(Search), Request),
  memberchk(path=Path, Search),
  memberchk(parent_id=Parents, Search),
  %memberchk(type=Type, Search),
  memberchk(key=PropKey, Search),
  memberchk(search=PropSearch, Search),
  memberchk(selected=SelectedKeys, Search),
  %read_from_chars(PropSearch,PropSearchTerm),
  %string_to_atom(PropSearchStr,PropSearchTerm),
  read_from_chars(SelectedKeys,FieldsTerm),
  read_from_chars(Path,PathList),
  read_from_chars(Parents, ParentsList),
  fixAtom(PropSearch,PropSearchTerm),
  format('Content-type: text/plain~n~n', []),
  format(user_error,'countLinked(~w, ~w, ~w, ~w, ~w) ~n', [PathList, ParentsList, PropKey, PropSearchTerm, FieldsTerm]),
  countLinked(PathList, ParentsList, PropKey, PropSearchTerm, FieldsTerm , Length),
  format('~w',[Length]).


reply(Request):-
  member(path('/countObjects'), Request),
  memberchk(search(Search), Request),
  memberchk(type=Type, Search),
  memberchk(key=PropKey, Search),
  memberchk(search=PropSearch, Search),
  memberchk(selected=SelectedKeys, Search),
  %read_from_chars(PropSearch,PropSearchTerm),
  %string_to_atom(PropSearchStr,PropSearchTerm),
  read_from_chars(SelectedKeys,FieldsTerm),
  fixAtom(PropSearch,PropSearchTerm),
  format('Content-type: text/plain~n~n', []),
  format(user_error,'count(~w, ~w, ~w, ~w) ~n', [Type, PropKey, PropSearchTerm, FieldsTerm]),
  count(Type, PropKey, PropSearchTerm, FieldsTerm , Length),
  format('~w',[Length]).


reply(Request):-
  member(path('/countLinkedPerParent'), Request),
  memberchk(search(Search), Request),
  memberchk(path=Path, Search),
  memberchk(parent_id=Parents, Search),
  %memberchk(type=Type, Search),
  memberchk(key=PropKey, Search),
  memberchk(search=PropSearch, Search),
  memberchk(selected=SelectedKeys, Search),
  read_from_chars(SelectedKeys,FieldsTerm),
  read_from_chars(Path,PathList),
  read_from_chars(Parents, ParentsList),
  format('Content-type: text/plain~n~n', []),
  format(user_error,'countLinkedPerParent(~w, ~w, ~w, ~w) ~n', [PathList, ParentsList, PropKey,PropSearch, FieldsTerm]),
  countLinkedPerParent(PathList, ParentsList, PropKey, PropSearch, FieldsTerm, ListLen),
  write_terms(ListLen).

reply(Request):-
  member(path('/getObjectById'), Request),
  memberchk(search(Search), Request),
  memberchk(type=Type, Search),
  memberchk(id=Id, Search),
  memberchk(selected=SelectedKeys, Search),
  read_from_chars(SelectedKeys,FieldsTerm),
  %atom_chars(IdStr,Id),
  format(user_error,'getObjectById(~w,~w,~w)~n',[Type,Id,FieldsTerm]),
  format('Content-type: text/plain~n~n', []),
  getObjectById(Type, Id, FieldsTerm, Obj),
  write_terms(Obj).


 reply(Request):-
  member(path('/getObjectTitleById'), Request),
  memberchk(search(Search), Request),
  memberchk(type=Type, Search),
  memberchk(id=Id, Search),
  %atom_chars(IdStr,Id),
  format(user_error,'/getObjectTitleById(~w,~w,~w)~n',[Type,Id,Title]),
  format('Content-type: text/plain~n~n', []),
  object(Type, Id, Title, _),
  format('~w',[Title]).


% /findParent?childType=$childType&childId=$childId&parentType=$parentType&selected=$selected
 reply(Request):-
  member(path('/findParent'), Request),
  memberchk(search(Search), Request),
  memberchk(childType=ChildType, Search),
  memberchk(childId=ChildId, Search),
  memberchk(parentType=ParentType, Search),
  memberchk(selected=SelectedKeys, Search),
  read_from_chars(SelectedKeys,Selected),
  %atom_chars(IdStr,Id),
  format(user_error,'/findParent(~w,~w,~w,...)~n',[ChildType,ChildId,ParentType]),
  format('Content-type: text/plain~n~n', []),

  link(ChildType,ChildId,ParentType,ParentId),
  object(ParentType,ParentId,_, List),
  include(filter(Selected), List, SelectedList),

  write_terms(SelectedList).

 reply(Request):-
  member(path('/deleteText'), Request),
  memberchk(search(Search), Request),
  memberchk(id=Id, Search),
  read_from_chars(Id,TextId),

  format(user_error,'/deleteText(~w)~n',[TextId]),
  format('Content-type: text/plain~n~n', []),

  retractall(object(text,TextId,_,_)),

  findall(Hl,link(text,TextId,hl,Hl),Hlist),
  member(Hl,Hlist),
  retractall(object(hl,Hl,_,_)),
  retractall(link(hl,Hl,_,_)),
  retractall(link(_,_,hl,Hl)),

  retractall(link(text,TextId,_,_)),
  retractall(link(_,_,text,TextId)),

  write('Done').


reply(Request):-
  member(path('/searchObjectOrderLimit'), Request),
  memberchk(search(Search), Request),
  memberchk(types=Types, Search),
  memberchk(search_fields=SearchFields, Search),
  memberchk(selected_fields=SelectedFields, Search),
  memberchk(from=From, Search),
  memberchk(limit=Limit, Search),
  memberchk(orderby=OrderBy, Search),
  memberchk(orderdir=OrderDir, Search),
  read_from_chars(Types,Types1),
  read_from_chars(SearchFields,SearchFields1),
  read_from_chars(SelectedFields,SelectedFields1),
  read_from_chars(From,From1),
  read_from_chars(Limit,Limit1),
  read_from_chars(OrderBy,OrderBy1),
  read_from_chars(OrderDir,OrderDir1),
  searchObjectOrderLimit(Types1,SearchFields1,SelectedFields1,From1,Limit1,OrderBy1,OrderDir1,Result),
  format(user_error,'/searchObjectOrderLimit(~w, ~w, ~w, ~w, ~w, ~w, ~w, List)~n',[Types1,SearchFields1,SelectedFields1,From1,Limit1,OrderBy1,OrderDir1]),
  format('Content-type: text/plain~n~n', []),
  write_terms(Result).

reply(Request):-
  member(path('/countSearchObject'), Request),
  memberchk(search(Search), Request),
  memberchk(types=Types, Search),
  memberchk(search_fields=SearchFields, Search),
  memberchk(selected_fields=SelectedFields, Search),

  read_from_chars(Types,Types1),
  read_from_chars(SearchFields,SearchFields1),
  read_from_chars(SelectedFields,SelectedFields1),

  count_search_object_by_type(Types1,SearchFields1,SelectedFields1,Result),
  format(user_error,'/count_search_object_by_type(~w, ~w, ~w, List)~n',[Types1,SearchFields1,SelectedFields1]),
  format('Content-type: text/plain~n~n', []),
  write_terms(Result).


reply(Request):-
  member(path('/searchObjectRange'), Request),
  memberchk(search(Search), Request),
  memberchk(types=Types, Search),
  memberchk(search_fields=SearchFields, Search),
  memberchk(selected_fields=SelectedFields, Search),
  memberchk(from=From, Search),
  memberchk(limit=Limit, Search),
  memberchk(orderby=OrderBy, Search),
  memberchk(orderdir=OrderDir, Search),
  memberchk(from_field=FromField, Search),
  memberchk(from_val=FromValue, Search),
  memberchk(to_field=ToField, Search),
  memberchk(to_val=ToValue, Search),
  read_from_chars(Types,Types1),
  read_from_chars(SearchFields,SearchFields1),
  read_from_chars(SelectedFields,SelectedFields1),
  read_from_chars(From,From1),
  read_from_chars(Limit,Limit1),
  read_from_chars(OrderBy,OrderBy1),
  read_from_chars(OrderDir,OrderDir1),
  read_from_chars(FromField,FromField1),
  read_from_chars(FromValue,FromValue1),
  read_from_chars(ToField,ToField1),
  read_from_chars(ToValue,ToValue1),
  %searchObjectOrderLimit(Types1,SearchFields1,SelectedFields1,From1,Limit1,OrderBy1,OrderDir1,Result),
  searchObjectByRangeOrderLimit(Types1,SearchFields1, SelectedFields1, From1, Limit1, OrderBy1, OrderDir1,FromField1-FromValue1,ToField1-ToValue1, Result),
  format(user_error,'/searchObjectByRangeOrderLimit(~w, ~w, ~w, ~w, ~w, ~w, ~w, List)~n',[Types1,SearchFields1,SelectedFields1,From1,Limit1,OrderBy1,FromField1-FromValue1,ToField1-ToValue1,OrderDir1]),
  format('Content-type: text/plain~n~n', []),
  write_terms(Result).


  reply(Request):-
  member(path('/countSearchObjectRange'), Request),
  memberchk(search(Search), Request),
  memberchk(types=Types, Search),
  memberchk(search_fields=SearchFields, Search),
  memberchk(selected_fields=SelectedFields, Search),
  memberchk(from_field=FromField, Search),
  memberchk(from_val=FromValue, Search),
  memberchk(to_field=ToField, Search),
  memberchk(to_val=ToValue, Search),

  read_from_chars(Types,Types1),
  read_from_chars(SearchFields,SearchFields1),
  read_from_chars(SelectedFields,SelectedFields1),
  read_from_chars(FromField,FromField1),
  read_from_chars(FromValue,FromValue1),
  read_from_chars(ToField,ToField1),
  read_from_chars(ToValue,ToValue1),

  count_search_object_range_by_type(Types1,SearchFields1,SelectedFields1,FromField1-FromValue1,ToField1-ToValue1,Result),
  format(user_error,'/count_search_object_by_type(~w, ~w, ~w, List)~n',[Types1,SearchFields1,SelectedFields1,FromField1-FromValue1,ToField1-ToValue1]),
  format('Content-type: text/plain~n~n', []),
  write_terms(Result).

reply(Request):-
  member(path('/searchObjectRange2'), Request),
  memberchk(search(Search), Request),
  memberchk(types=Types, Search),
  memberchk(search_fields=SearchFields, Search),
  memberchk(strict_search=StrictSearch, Search),
  memberchk(selected_fields=SelectedFields, Search),
  memberchk(from=From, Search),
  memberchk(limit=Limit, Search),
  memberchk(orderby=OrderBy, Search),
  memberchk(orderdir=OrderDir, Search),
  memberchk(from_field=FromField, Search),
  memberchk(from_val=FromValue, Search),
  memberchk(to_field=ToField, Search),
  memberchk(to_val=ToValue, Search),
  read_from_chars(Types,Types1),
  read_from_chars(SearchFields,SearchFields1),
  read_from_chars(StrictSearch,StrictSearch1),
  read_from_chars(SelectedFields,SelectedFields1),
  read_from_chars(From,From1),
  read_from_chars(Limit,Limit1),
  read_from_chars(OrderBy,OrderBy1),
  read_from_chars(OrderDir,OrderDir1),
  read_from_chars(FromField,FromField1),
  read_from_chars(FromValue,FromValue1),
  read_from_chars(ToField,ToField1),
  read_from_chars(ToValue,ToValue1),
  %searchObjectOrderLimit(Types1,SearchFields1,SelectedFields1,From1,Limit1,OrderBy1,OrderDir1,Result),
  searchObjectByRangeOrderLimit2(Types1,SearchFields1,StrictSearch1, SelectedFields1, From1, Limit1, OrderBy1, OrderDir1,FromField1-FromValue1,ToField1-ToValue1, Result),
  format(user_error,'/searchObjectByRangeOrderLimit2(~w, ~w, ~w, ~w, ~w, ~w, ~w, ~w,~w,~w, List)~n',[Types1,SearchFields1,StrictSearch1, SelectedFields1, From1, Limit1, OrderBy1, OrderDir1,FromField1-FromValue1,ToField1-ToValue1]),
  format('Content-type: text/plain~n~n', []),
  write_terms(Result).


  reply(Request):-
  member(path('/countSearchObjectRange2'), Request),
  member(method(post), Request), !,
  http_read_data(Request, Data, ['application/x-www-form-urlencoded']),
  %memberchk(search(Search), Request),
  memberchk(types=Types, Data),
  memberchk(search_fields=SearchFields, Data),
  memberchk(strict_search=StrictSearch, Data),
  memberchk(selected_fields=SelectedFields, Data),
  memberchk(from_field=FromField, Data),
  memberchk(from_val=FromValue, Data),
  memberchk(to_field=ToField, Data),
  memberchk(to_val=ToValue, Data),

  read_from_chars(Types,Types1),
  read_from_chars(SearchFields,SearchFields1),
  read_from_chars(StrictSearch,StrictSearch1),
  read_from_chars(SelectedFields,SelectedFields1),
  read_from_chars(FromField,FromField1),
  read_from_chars(FromValue,FromValue1),
  read_from_chars(ToField,ToField1),
  read_from_chars(ToValue,ToValue1),
  format(user_error,'/count_search_object_range_by_type2(~w, ~w,~w, ~w, ~w,~w, List)~n',[Types1,SearchFields1,StrictSearch1,SelectedFields1,FromField1-FromValue1,ToField1-ToValue1]),
  count_search_object_range_by_type2(Types1,SearchFields1,StrictSearch1,SelectedFields1,FromField1-FromValue1,ToField1-ToValue1,Result),
  format('Content-type: text/plain~n~n', []),
  write_terms(Result).


  reply(Request):-
  member(path('/searchIntersect'), Request),
  memberchk(search(Search), Request),
  memberchk(types=Types, Search),
  memberchk(search_fields=SearchFields, Search),
  memberchk(strict_search1=StrictSearch1, Search),
  memberchk(strict_search2=StrictSearch2, Search),
  memberchk(selected_fields=SelectedFields, Search),
  memberchk(from=From, Search),
  memberchk(limit=Limit, Search),
  memberchk(orderby=OrderBy, Search),
  memberchk(orderdir=OrderDir, Search),
  memberchk(from_field=FromField, Search),
  memberchk(from_val=FromValue, Search),
  memberchk(to_field=ToField, Search),
  memberchk(to_val=ToValue, Search),
  read_from_chars(Types,Types1),
  read_from_chars(SearchFields,SearchFields1),
  read_from_chars(StrictSearch1,StrictSearch1_1),
  read_from_chars(StrictSearch2,StrictSearch2_1),
  read_from_chars(SelectedFields,SelectedFields1),
  read_from_chars(From,From1),
  read_from_chars(Limit,Limit1),
  read_from_chars(OrderBy,OrderBy1),
  read_from_chars(OrderDir,OrderDir1),
  read_from_chars(FromField,FromField1),
  read_from_chars(FromValue,FromValue1),
  read_from_chars(ToField,ToField1),
  read_from_chars(ToValue,ToValue1),
  %searchObjectOrderLimit(Types1,SearchFields1,SelectedFields1,From1,Limit1,OrderBy1,OrderDir1,Result),
  searchIntersect(Types1,SearchFields1,StrictSearch1_1,StrictSearch2_1, SelectedFields1, From1, Limit1, OrderBy1, OrderDir1,FromField1-FromValue1,ToField1-ToValue1, Result),
  format(user_error,'/searchIntersect(~w, ~w, ~w, ~w, ~w, ~w, ~w, ~w,~w,~w, List)~n',[Types1,SearchFields1,StrictSearch1, SelectedFields1, From1, Limit1, OrderBy1, OrderDir1,FromField1-FromValue1,ToField1-ToValue1]),
  format('Content-type: text/plain~n~n', []),
  write_terms(Result).



  reply(Request):-
  member(path('/countIntersect'), Request),
  memberchk(search(Search), Request),
  memberchk(types=Types, Search),
  memberchk(search_fields=SearchFields, Search),
  memberchk(strict_search1=StrictSearch1, Search),
  memberchk(strict_search2=StrictSearch2, Search),
  memberchk(selected_fields=SelectedFields, Search),
  memberchk(from_field=FromField, Search),
  memberchk(from_val=FromValue, Search),
  memberchk(to_field=ToField, Search),
  memberchk(to_val=ToValue, Search),

  read_from_chars(Types,Types1),
  read_from_chars(SearchFields,SearchFields1),
  read_from_chars(StrictSearch1,StrictSearch1_1),
  read_from_chars(StrictSearch2,StrictSearch2_1),
  read_from_chars(SelectedFields,SelectedFields1),
  read_from_chars(FromField,FromField1),
  read_from_chars(FromValue,FromValue1),
  read_from_chars(ToField,ToField1),
  read_from_chars(ToValue,ToValue1),

  count_intersect(Types1,SearchFields1,StrictSearch1_1,StrictSearch2_1,SelectedFields1,FromField1-FromValue1,ToField1-ToValue1,Count),
  format(user_error,'/count_intersect(~w, ~w,~w, ~w, ~w,~w, List)~n',[Types1,SearchFields1,StrictSearch1_1,StrictSearch2_1,SelectedFields1,FromField1-FromValue1,ToField1-ToValue1]),
  format('Content-type: text/plain~n~n', []),
  write_terms(Count).


%******** search objects ************
%Types=[apa,tap,...]
%SearchFields=[apa-[workNumber-WorkText,title-TitleText],tap-[workNumber-WorkText,title-TitleText]]
%SelectedFields=[apa-[workNumber]]
search_object(Types,SearchFields,SelectedFields,OrderBy,Bag):-
findall(key(OrderByVal)-Result,
(
  member(Type,Types),
  member(Type-SearchList,SearchFields),
  member(Field-Search,SearchList),
  object(Type,Id,_,List),
  member(Type-TypeSelectedFields,SelectedFields),
  include(filter(TypeSelectedFields), List, Selected),

  member(Field-Value,Selected),
  search_atom(Value, Search),
  member(OrderBy-OrderByVal,Selected),
  append([type-Type,object_id-Id],Selected,Result)
  ),
  Pairs),
  list_to_set(Pairs,Bag).


searchObjectOrderLimit(Types,SearchFields, SelectedFields, From, Limit, OrderBy, OrderDir, List):-
  search_object(Types,SearchFields,SelectedFields, OrderBy, Pairs),
  sort(Pairs,Sorted),
  (
        OrderDir = desc ->
                reverse(Sorted,Sorted2),
                list_pairs_values(Sorted2, Bag);
        list_pairs_values(Sorted, Bag)
  ),
  sub_list(Bag,From,Limit,List).


%From, To fields SHOULD NOT BE be present in SearchFields and SHOULD BE in Selected Fields
%searches for works within time period of year1-year2 or exactly for the Year
%Year1=[year-1980], Year2=[year-1990], Year=[year-1985]
search_object_by_range(Types,SearchFields,SelectedFields,OrderBy,FromField-FromValue,ToField-ToValue,Bag):-
findall(key(OrderByVal)-Result,
(
  member(Type,Types),
  member(Type-SearchList,SearchFields),
  member(Field-Search,SearchList),
  pairs_keys(SearchList,SearchKeys),

  object(Type,Id,_,List),

  member(Type-TypeSelectedFields,SelectedFields),

  append([FromField,ToField],TypeSelectedFields,TypeSelectedFields3),

  include(filter(TypeSelectedFields3), List, Selected),

  include(filter(SearchKeys), List, SelectedSearch),

  member(FromField-Val1,Selected),
  Val1>=FromValue,
  member(ToField-Val2,Selected),
  Val2=<ToValue,

  member(Field-Value,SelectedSearch),
  search_atom(Value, Search),
  member(OrderBy-OrderByVal,Selected),
  append([type-Type,object_id-Id],Selected,Result)
  ),
  Pairs),
  list_to_set(Pairs,Bag).

searchObjectByRangeOrderLimit(Types,SearchFields, SelectedFields, From, Limit, OrderBy, OrderDir,FromField-FromValue,ToField-ToValue, List):-
  search_object_by_range(Types,SearchFields,SelectedFields, OrderBy,FromField-FromValue,ToField-ToValue, Pairs),
  sort(Pairs,Sorted),
  (
        OrderDir = desc ->
                reverse(Sorted,Sorted2),
                list_pairs_values(Sorted2, Bag);
        list_pairs_values(Sorted, Bag)
  ),
  sub_list(Bag,From,Limit,List).


%the same as above, but StrictSearch field, which is a list of fields with values : [apa-[year-1945, workNumber-100]] - wich is equal to SQL: year='1945' && workNumber='100' && (SearchField1 LIKE SearchVal1 || SearchField2 LIKE SearchVal2) && FromField>=FromValue && ToField<=ToValue
search_object_by_range2(Types,SearchFields,StrictSearch,SelectedFields,OrderBy,FromField-FromValue,ToField-ToValue,Pairs1):-
findall(key(OrderByVal)-Result,
(
  member(Type,Types),
  member(Type-StrictList, StrictSearch),
  member(Type-SearchList,SearchFields),
  member(Field-Search,SearchList),
  pairs_keys(SearchList,SearchKeys),
  object(Type,Id,_,List),
  member(Type-TypeSelectedFields,SelectedFields),
  pairs_keys(StrictList,StrictFields),
  append(StrictFields,TypeSelectedFields,TypeSelectedFields2),
  append([FromField,ToField],TypeSelectedFields2,TypeSelectedFields3),
  include(filter(TypeSelectedFields3), List, Selected),

  (\+ord_empty(StrictList)->
    intersection(StrictList,Selected,Intersected), Intersected=StrictList;
    true
  ),

  include(filter(SearchKeys), List, SearchSelected),
  %write(StrictList),nl,
  %write(Selected),nl,
  %write(Intersected), nl,
  member(FromField-Val1,Selected),
  Val1>=FromValue,
  member(ToField-Val2,Selected),
  Val2=<ToValue,

  member(Field-Value,SearchSelected),
  search_atom(Value, Search),
  member(OrderBy-OrderByVal,Selected),
  append([type-Type,object_id-Id],Selected,Result)


  ),
  Pairs),
  list_to_set(Pairs,Pairs1).


searchObjectByRangeOrderLimit2(Types,SearchFields,StrictSearch, SelectedFields, From, Limit, OrderBy, OrderDir,FromField-FromValue,ToField-ToValue, List):-
  search_object_by_range2(Types,SearchFields,StrictSearch,SelectedFields, OrderBy,FromField-FromValue,ToField-ToValue, Pairs),

  sort(Pairs,Sorted),

  (
        OrderDir = desc ->
                reverse(Sorted,Sorted2),
                list_pairs_values(Sorted2, Bag);
        pairs_values(Sorted, Bag)
  ),

  sub_list(Bag,From,Limit,List).

sub_list(List,From,Limit,Sublist):-
 To is From+Limit-1,
 findall(
 El,
 ( between(From,To,N), nth0(N,List,El) ),
 Sublist
 ).

search_intersect(Types,SearchFields,StrictSearch1,StrictSearch2,SelectedFields,OrderBy,FromField-FromValue,ToField-ToValue,Result):-
 setof(
  Pairs,
  (
   search_object_by_range2(Types,SearchFields,StrictSearch1,SelectedFields,OrderBy,FromField-FromValue,ToField-ToValue,Pairs1),
   search_object_by_range2(Types,SearchFields,StrictSearch2,SelectedFields,OrderBy,FromField-FromValue,ToField-ToValue,Pairs2),
   intersection(Pairs1,Pairs2,Pairs),length(Pairs,Len),Len>0
  ),
  Result
 ).

count_intersect(Types,SearchFields,StrictSearch1,StrictSearch2,SelectedFields,OrderBy,FromField-FromValue,ToField-ToValue,Len):-
 search_intersect(Types,SearchFields,StrictSearch1,StrictSearch2,SelectedFields,OrderBy,FromField-FromValue,ToField-ToValue,Result),
 length(Result,Len).

%************************************
%count objects
search_object_group_by_type(Types,SearchFields,SelectedFields,Bag):-
findall(Type-Result,
(
  member(Type,Types),
  member(Type-SearchList,SearchFields),
  member(Field-Search,SearchList),
  object(Type,Id,_,List),
  member(Type-TypeSelectedFields,SelectedFields),
  include(filter(TypeSelectedFields), List, Selected),

  member(Field-Value,Selected),
  search_atom(Value, Search),
  append([type-Type,object_id-Id],Selected,Result)

  ),
  Pairs),
  list_to_set(Pairs,Bag).

count_search_object_by_type(Types,SearchFields,SelectedFields, ListLen):-
  search_object_group_by_type(Types,SearchFields,SelectedFields, Bag),
  sort(Bag,Sorted),
  flatten(Sorted,Sorted1),
  group_pairs_by_key(Sorted1,SplitList),
  get_list_of_lengths(SplitList,ListLen).

%count objects within some range (e.g. by years etc)
search_object_range_by_type(Types,SearchFields,SelectedFields,FromField-FromValue,ToField-ToValue,Bag):-
findall(Type-Result,
(
  member(Type,Types),
  member(Type-SearchList,SearchFields),
  member(Field-Search,SearchList),
  object(Type,Id,_,List),
  member(Type-TypeSelectedFields,SelectedFields),

  append([FromField,ToField],TypeSelectedFields,TypeSelectedFields2),
  include(filter(TypeSelectedFields2), List, Selected),

  member(FromField-Val1,Selected),
  Val1>=FromValue,
  member(ToField-Val2,Selected),
  Val2=<ToValue,

  member(Field-Value,Selected),
  search_atom(Value, Search),
  append([type-Type,object_id-Id],Selected,Result)

  ),
  Pairs),
  list_to_set(Pairs,Bag).

count_search_object_range_by_type(Types,SearchFields,SelectedFields,FromField-FromValue,ToField-ToValue, ListLen):-
  search_object_range_by_type(Types,SearchFields,SelectedFields,FromField-FromValue,ToField-ToValue, Bag),
  sort(Bag,Sorted),
  flatten(Sorted,Sorted1),
  group_pairs_by_key(Sorted1,SplitList),
  get_list_of_lengths(SplitList,ListLen).


%count objects with strict search and within some range (e.g. by years etc)
search_object_range_by_type2(Types,SearchFields,StrictSearch,SelectedFields,FromField-FromValue,ToField-ToValue,Set):-
findall(Type-Result,
(
  member(Type,Types),
  member(Type-StrictList, StrictSearch),
  member(Type-SearchList,SearchFields),
  member(Field-Search,SearchList),
  pairs_keys(SearchList,SearchKeys),
  %write(SearchKeys),
  %write([Field,Search]),nl,
  object(Type,Id,_,List),
  member(Type-TypeSelectedFields,SelectedFields),

  pairs_keys(StrictList,StrictFields),
  append(StrictFields,TypeSelectedFields,TypeSelectedFields2),
  append([FromField,ToField],TypeSelectedFields2,TypeSelectedFields3),

  include(filter(TypeSelectedFields3), List, Selected),
  include(filter(SearchKeys),List,SearchSelected),

  intersection(StrictList,Selected,Intersected), Intersected=StrictList,

  member(FromField-Val1,Selected),
  Val1>=FromValue,
  member(ToField-Val2,Selected),
  Val2=<ToValue,

  member(Field-Value,SearchSelected),
  %write([Field,Value]),nl,
  search_atom(Value, Search),
  append([type-Type,object_id-Id],Selected,Result)

  ),
  Pairs),
  list_to_set(Pairs,Set).

count_search_object_range_by_type2(Types,SearchFields,StrictSearch,SelectedFields,FromField-FromValue,ToField-ToValue, ListLen):-
  search_object_range_by_type2(Types,SearchFields,StrictSearch,SelectedFields,FromField-FromValue,ToField-ToValue,Bag),

  sort(Bag,Sorted),
  flatten(Sorted,Sorted1),
  group_pairs_by_key(Sorted1,SplitList),
  get_list_of_lengths(SplitList,ListLen).

%******** update objects ************
%update only title of an object
update_object_title(Type, Id, Title):-
 object(Type,Id,T,L),
 retract(object(Type,Id,T,L)),
 assert(object(Type,Id,Title,L)).

update_object_list_property(Type,Id,Key,Val):-
  object(Type,Id,Title,List),
  list_replace_val(Key,Val,List,List2),
  retract(object(Type,Id,Title,List)),
  assert(object(Type,Id,Title,List2)).

list1([a-1,a-2,a-3,a-4]).
list_replace_val(Key,Val,L1,L2):-
   select(Key-_,L1,L),
   append([Key-Val],L,L2),!.

%************************************


strlist_intlist([],[]).
strlist_intlist([H|T],[Hi|Ti]):-
 atom(H),
 atom_number(H,Hi),
 strlist_intlist(T,Ti).

strlist_intlist([H|T],[H|Ti]):-
 integer(H),
 strlist_intlist(T,Ti).


find_min_int(Lst,Min):-
 strlist_intlist(Lst,ILst),
  min_list(ILst,Min).


find_max_int(Lst,Max):-
 strlist_intlist(Lst,ILst),
 max_list(ILst,Max).

find_last_hl(Num):-
 findall(Id,object(hl,Id,_,L),Bag),find_max_int(Bag,Num).

find_last_hl(0).



fixAtom('_all_','').
fixAtom(X,X).


filter(Allowed, El):-
  pairs_keys([El],Key),
  subset(Key,Allowed).

getObjectById(Type, Id, SelectedKeys, List2):-
  SelectedKeys=end_of_file,
  object(Type, Id, _, List2).

getObjectById(Type, Id, SelectedKeys, List2):-
  length(SelectedKeys,Len),
  Len>0,
  object(Type, Id, _, List),
  include(filter(SelectedKeys), List, List2).

getObjectById(Type, Id, SelectedKeys, List2):-
  Len is 0,
  object(Type, Id, _, List2).

%find cumulative set of linked objects
findLinked(Path, Parents, Type, PropKey, PropSearch, SelectKeys, Objects):-
setof( key(Id) - (Selected),
   (
     link_childs(Parents,Path,Id),
     %link(Type,Id,ParentType,ParentId),
     object(Type, Id, _, List),
     %write_term(List,[]),nl,
     include(filter(SelectKeys), List, Selected),
     %write_term(Selected,[]),nl,
     member(PropKey-PropValue, Selected),
     search_atom(PropValue, PropSearch)
   ),
   Pairs),
   pairs_values(Pairs, Objects).


%count links for each parent
countLinkedPerParent(Path, Parents, PropKey, PropSearch, SelectKeys, ListLen):-
  reverse(Path,Path2),Path2=[Type|_],
  findall(Objects, findLinkedSplit(Path, Parents, Type, PropKey, PropSearch, SelectKeys, Objects), Bag),
  sort(Bag,Sorted),
  flatten(Sorted,Sorted1),
  group_pairs_by_key(Sorted1,SplitList),
  get_list_of_lengths(SplitList,ListLen).



%find linked objects for each parent id
%used in related items
findLinkedSplit(Path, Parents, Type, PropKey, PropSearch, SelectKeys, Pairs):-
setof( ParentId - (Selected1),
   (
     member(ParentId, Parents),
     %create one-element list
     select(ParentId,ParentIdLst,[]),
     link_childs(ParentIdLst,Path,Id),
     %we need an object, when counting links, as we do search as per properties
     object(Type, Id, _, List),
     include(filter(SelectKeys), List, Selected),
     %add parent data to result list
     add_el_list(ParentId,Selected,Selected1),

     member(PropKey-PropValue, Selected),
     search_atom(PropValue, PropSearch)
   ),
   Pairs).

add_el_list(ParentId,List,List1):-
 select(parentId-ParentId,List1,List),!.


count_length_of_sublist(List,List1):-
   setof(
    Pid-Len,
    (
      member(Pid-Sublst,List),
      length(Sublst,Len)
    ),List1
   ).

get_list_of_lengths(List,List1):-
 findall(List2,count_length_of_sublist(List,List2),List1).

%OrderBy is one of [Type, Id, Title] or list of Selected keys.
%currently we support one Field for Order (sorting). Sorting is done in ASC order
find(Type, PropKey, PropSearch, SelectKeys, Objects):-
   setof( key(Id) - (Selected),
   (
        object(Type, Id, _, List),
        %write_term(List,[]),nl,
	append(SelectKeys,[PropKey],SelectKeys1),
        include(filter(SelectKeys1), List, Selected),
        %write_term(Selected,[]),nl,
        member(PropKey-PropValue, Selected),
        search_atom(PropValue, PropSearch)
    ),
    Pairs),
    pairs_values(Pairs, Objects).

count(Type, PropKey, PropSearch, SelectKeys,Len):-
  findall(Objects,find(Type, PropKey, PropSearch, SelectKeys, Objects),Bag),
  sort(Bag,Sorted),
  length(Sorted,Len).

countLinked(Path, Parents, PropKey, PropSearch, SelectKeys, Len):-
 reverse(Path,Path2),Path2=[Type|_],
 findall(Objects, findLinked(Path, Parents, Type, PropKey, PropSearch, SelectKeys, Objects), Bag),
 sort(Bag,Sorted),
 length(Sorted,Len).


findLimit(Type, PropKey, PropSearch, SelectKeys, From, Limit, Bag2):-
  findall(Objects,find(Type, PropKey, PropSearch, SelectKeys, Objects),Bag),
  To is From+Limit-1,
  include(selectEl(Bag,From,To),Bag,Bag2).

selectOrderLimit(Type, PropKey, PropSearch, SelectKeys, From, Limit, Bag2):-
  findall(Objects,find(Type, PropKey, PropSearch, SelectKeys, Objects),Bag),
  To is From+Limit-1,
  include(selectEl(Bag,From,To),Bag,Bag2).

selectLinkedOrderLimit(ParentType, ParentId, Type, PropKey, PropSearch, SelectKeys, From, Limit, Bag2):-
  findall(Objects,findLinked(ParentType, ParentId, Type, PropKey, PropSearch, SelectKeys, Objects),Bag),
  To is From+Limit-1,
  include(selectEl(Bag,From,To),Bag,Bag2).


%findOrderBy and selectOrderByLimit
findOrderBy(Type, PropKey, PropSearch, SelectKeys, _, SplitKey, Pairs):-
SplitKey==true,
Type\==text,
%write('1~n'),
setof( [Dig,Rom,Alf] - Selected,
  (
    object(Type, Id, _, List),
    %write_term(List,[]),nl,
    append(SelectKeys,[PropKey],SelectKeys1),
    include(filter(SelectKeys1), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),
    atom_codes(Id,Id1),
    phrase(complex_key([D,R,A]),Id1),
    number_codes(Dig,D),atom_codes(Rom,R),atom_codes(Alf,A),
    search_atom(PropValue, PropSearch)
  ),
Pairs).

findOrderBy(Type, PropKey, PropSearch, SelectKeys, OrderBy, SplitKey, Pairs):-
SplitKey==false,
OrderBy\==id,
OrderBy\==name,
%write('2~n'),
setof( key(OrderByVal) - Selected,
  (
    object(Type, Id, _, List),
    %write_term(List,[]),nl,
    append(SelectKeys,[PropKey],SelectKeys1),
    include(filter(SelectKeys1), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),
    member(OrderBy-OrderByVal, Selected),
    search_atom(PropValue, PropSearch)
  ),
Pairs).


findOrderBy(Type, PropKey, PropSearch, SelectKeys, OrderBy, SplitKey, Pairs):-
SplitKey==false,
OrderBy==name,
%write('3~n'),
setof( key(Title) - Selected,
  (
    object(Type, Id, Title, List),
    %write_term(List,[]),nl,
    append(SelectKeys,[PropKey],SelectKeys1),
    include(filter(SelectKeys1), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),

    search_atom(PropValue, PropSearch)
  ),
Pairs).

findOrderBy(Type, PropKey, PropSearch, SelectKeys, OrderBy, SplitKey, Pairs):-
SplitKey==false,
OrderBy==id,
%write('4~n'),
setof( key(Id) - Selected,
  (
    object(Type, Id, Title, List),
    %write_term(List,[]),nl,
    append(SelectKeys,[PropKey],SelectKeys1),
    include(filter(SelectKeys1), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),

    search_atom(PropValue, PropSearch)
  ),
Pairs).

findOrderBy(Type, PropKey, PropSearch, SelectKeys, OrderBy, SplitKey, Pairs):-
SplitKey==true,
OrderBy==id,
Type=text,
%write('5~n'),
setof( key(Id) - Selected,
  (
    object(Type, Id, Title, List),
    %write_term(List,[]),nl,
    append(SelectKeys,[PropKey],SelectKeys1),
    include(filter(SelectKeys1), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),

    search_atom(PropValue, PropSearch)
  ),
Pairs).

selectOrderByLimit(Type, PropKey, PropSearch, SelectKeys, From, Limit, OrderBy, OrderDir, Bag2):-
  (OrderBy==name -> SplitKey = false;!),
  (OrderBy==id -> SplitKey = true;!),
  findall(Objects,findOrderBy(Type, PropKey, PropSearch, SelectKeys, OrderBy, SplitKey,Objects),Pairs),
  sort(Pairs,Sorted),
  (
        OrderDir = desc ->
                reverse(Sorted,Sorted2),
                list_pairs_values(Sorted2, Bag);
        list_pairs_values(Sorted, Bag)
  ),
  To is From+Limit-1,
  include(selectEl(Bag,From,To),Bag,Bag2).


%findLinkedOrderBy and selectLinkedOrderByLimit
findLinkedOrderBy(Path, Parents, Type, PropKey, PropSearch, SelectKeys, _, SplitKey, Pairs):-
%write('1'),nl,
SplitKey==true,
Type\==text,
setof( [Dig,Rom,Alf] - Selected,
  (
    link_childs(Parents,Path,Id),
    %write(Id),nl,
    %link(Type,Id,ParentType,ParentId),
    object(Type, Id, _, List),
    %write_term(List,[]),nl,
    include(filter(SelectKeys), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),
    atom_codes(Id,Id1),
    phrase(complex_key([D,R,A]),Id1),
    number_codes(Dig,D),atom_codes(Rom,R),atom_codes(Alf,A),
    search_atom(PropValue, PropSearch)
  ),
Pairs).

findLinkedOrderBy(Path,Parents, Type, PropKey, PropSearch, SelectKeys, OrderBy, SplitKey, Pairs):-
%write('2'),nl,
SplitKey==false,
OrderBy\==id,
OrderBy\==name,
setof( key(OrderByVal) - Selected,
  (
    link_childs(Parents,Path,Id),
    %link(Type,Id,ParentType,ParentId),
    object(Type, Id, _, List),
    %write_term(List,[]),nl,
    include(filter(SelectKeys), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),
    member(OrderBy-OrderByVal, Selected),
    search_atom(PropValue, PropSearch)
  ),
Pairs).


findLinkedOrderBy(Path,Parents, Type,PropKey, PropSearch, SelectKeys, OrderBy, SplitKey, Pairs):-
%write('3'),nl,
SplitKey==false,
OrderBy==name,
setof( key(Title) - Selected,
  (

    link_childs(Parents,Path,Id),
    %link(Type,Id,ParentType,ParentId),
    object(Type, Id, Title, List),
    %write_term(List,[]),nl,
    include(filter(SelectKeys), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),

    search_atom(PropValue, PropSearch)
  ),
Pairs).

findLinkedOrderBy(Path,Parents, Type, PropKey, PropSearch, SelectKeys, OrderBy, SplitKey, Pairs):-
%write('4'),nl,
SplitKey==false,
OrderBy==id,
setof( key(Id) - Selected,
  (
    link_childs(Parents,Path,Id),
    %link(Type,Id,ParentType,ParentId),
    object(Type, Id, Title, List),
    %write_term(List,[]),nl,
    include(filter(SelectKeys), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),

    search_atom(PropValue, PropSearch)
  ),
Pairs).

findLinkedOrderBy(Path,Parents, Type,PropKey, PropSearch, SelectKeys, OrderBy, SplitKey, Pairs):-
%write('5'),nl,
SplitKey==true,
Type==text,
setof( key(Id) - Selected,
  (
    link_childs(Parents,Path,Id),
    %write(Id),nl,
    %link(Type,Id,ParentType,ParentId),
    object(Type, Id, Title, List),
    %write_term(List,[]),nl,
    include(filter(SelectKeys), List, Selected),
    %write_term(Selected,[]),nl,
    member(PropKey-PropValue,  Selected),

    search_atom(PropValue, PropSearch)
  ),
Pairs).

selectLinkedOrderByLimit(Path,Parents, PropKey, PropSearch, SelectKeys, From, Limit, OrderBy, OrderDir, Bag2):-
  /*
  (OrderBy==name -> SplitKey = false;!),
  (OrderBy==id -> SplitKey = true;!),
  */
  (OrderBy==id -> SplitKey = true;SplitKey = false),

  %get last item in path, it is a Type
  reverse(Path,Path2),Path2=[Type|_],
  %format('Type:~w~n',[Type]),
  findall(Objects,findLinkedOrderBy(Path,Parents,Type, PropKey, PropSearch, SelectKeys, OrderBy, SplitKey,Objects),Pairs),
  sort(Pairs,Sorted),
  (
        OrderDir = desc ->
                reverse(Sorted,Sorted2),
                list_pairs_values(Sorted2, Bag);
        list_pairs_values(Sorted, Bag)
  ),
  To is From+Limit-1,
  include(selectEl(Bag,From,To),Bag,Bag2).

link_childs(Parents,Path,Id):-
 %write('link_childs - 3'),nl,
 is_list(Path),
 %write(Path),nl,
 length(Path,3),
 childs3(Parents,Path,Id).

link_childs(Parents,Path,Id):-
 %write('link_childs - 2'),nl,
 is_list(Path),
 length(Path,2),
 %write(Path),nl,
 nth1(1,Path,Par),
 nth1(2,Path,El2),
 member(ParentId,Parents),
 atom_codes(ParentId, Temp1),atom_codes(ParStr,Temp1),
 %write([El2,Id,Par,ParentId]),nl,
 link(El2,Id,Par,ParStr).

childs3(Par_list,Path,Id3):-
   %write('childs3'),nl,
   nth1(1,Path,Par),
   nth1(2,Path,El2),
   nth1(3,Path,El3),
   member(Par_id,Par_list),
   atomize(Par_id,ParStr),


   link(El2,Id2,Par,ParStr),
   %write([El2,Id2,Par,ParStr]),nl,
   link(El3,Id3,El2,Id2).
   %write([El3,Id3,El2,Id2]),nl.

 %converts integer to atom
 atomize(A1,A2):-
    atom_chars(A1,A3),
    atom_chars(A2,A3).

list_pairs_values([],[]).
list_pairs_values([HofPairs|TofPairs],[HofValues|TofValues]):-
  pairs_values(HofPairs,HofValues),
  list_pairs_values(TofPairs,TofValues).



find2(Type, IdSearch, TitleSearch, OrderByKey, Objects):-
   setof( key(OrderByVal) - (Type, Id, Title),
   (
        object(Type, Id, Title, List),
        member(OrderByKey-OrderByVal, List),
        search_atom(Id,IdSearch),
        search_atom(Title,TitleSearch)
       ),
       Pairs),
       pairs_values(Pairs, Objects).

selectEl(List,From,To,El):-
 nth0(I,List,El),
 I>=From,
 I=<To,!.


find2Limit(Type, IdSearch, TitleSearch, OrderByKey, Objects, From, Limit, Bag2):-
 findall(Objects,find2(Type, IdSearch, TitleSearch, OrderByKey, Objects),Bag),
 To is From+Limit-1,
 include(selectEl(Bag,From,To),Bag,Bag2).

%complex id parser
digits([D|T]) -->
        digit(D), !,
        digits(T).
digits([]) -->
        [].

digit(D) -->
        [D],
        { code_type(D, digit)
        }.

romans([D|T]) -->
 roman(D), !,
 romans(T).

romans([])-->[].
roman(D)-->
 [D],
 {
  %I X V L M C
  member(D,[73, 86, 88, 76, 67, 68, 77])
 }.

lits([D|T]) -->
 lit(D), !,
 lits(T).

lits([])-->[].
lit(D)-->
 [D],
 {
  code_type(D,alpha),
  \+member(D,[73, 86, 88, 76, 67, 68, 77])
 }.

complex_key([Dig,Rom,Alf])-->
  digits(Dig),("/"|""|" "),romans(Rom),("/"|""|" "),lits(Alf),!.
%complex index parser


fall(Str,SortedBag):-
 read_from_chars(Str,X),
 findall(X,call(X),Bag),
 sort(Bag,SortedBag).

write_terms([]).
write_terms([H|T]):-
 is_list(H),
 write_terms(H),format("@#*@#*",[]),nl,

 write_terms(T).

write_terms([H|T]):-
 \+is_list(H),
 write_term(H,[attributes(write),portray(true),backquoted_string(true),quoted(true),character_escapes(true)]),format("###",[]),nl,
 write_terms(T).


write_terms2([]).
write_terms2([H|T]):-
 write_term(H,[attributes(write),portray(true),backquoted_string(true),quoted(true),character_escapes(true)]),nl,
 write_terms2(T).



terms_to_atoms([],[]).
terms_to_atoms([H|T],[H1|T1]):-
 term_to_atom(H,H1),
  terms_to_atoms(T,T1).






%%%%%%%%%%% print objects %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
%printAllObjectLists('', '', 'in ', [tap, date, id, title]).
printAllObjectLists(TypeSearch, IdSearch, TitleSearch, Fields):-
 findall(List,printObjectList(TypeSearch, IdSearch, TitleSearch, List, Fields), Bag),
 sort(Bag, SortedList),
 write_terms(SortedList).

printObjectList(TypeSearch, IdSearch, TitleSearch, ResultList, Fields):-
  object(Type, Id, Title, List),
  search_atom(Type, TypeSearch),
  search_atom(Id, IdSearch),
  search_atom(Title, TitleSearch),
  include(filter(Fields),List, ResultList).



%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%




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


%%%%%%%%%%TESTS%%%%%%%%%%%%%%%%%%%%%%%

chkCount(Type,Str,List):-
count_search_object_range_by_type2([Type],
[Type-[title-Str],Type-[titleEnglish-Str]],
[Type-[]],
[Type-[id, Type,title,titleEnglish,year]], year-0,year-3000, List).

chkSearch(Type,Str,List):-
search_object_range_by_type2([Type],
[Type-[title-Str],Type-[titleEnglish-Str]],
[Type-[]],
[Type-[id, title]],year-0,year-3000,List).


chkSearch2(Type, Search,List):-
searchObjectByRangeOrderLimit2([Type],[Type-[title-Search],Type-[titleEnglish-Search]],[Type-[]],[Type-[id, Type,title,titleEnglish,year]], 0, 1000, id, asc,year-0,year-3000, List).

