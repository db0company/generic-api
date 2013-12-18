(* ************************************************************************** *)
(* Project: Generic API Consumer                                              *)
(* Author: db0 (db0company@gmail.com, http://db0.fr/)                         *)
(* Latest Version on GitHub: https://github.com/db0company/invite             *)
(* ************************************************************************** *)

let check_invite service_name invite =
  match Consumer.connect "http://invite.paysdu42.fr/" with
    | Consumer.Error e -> Consumer.Error e
    | Consumer.Result _ ->
      Consumer.go
	~resource:"invites"
	~id:invite
	~get:[("service_name", service_name)]
	Consumer.format_bool

let _ =
  let service_name = ref "test"
  and invite       = ref "test" in
  (Arg.parse
     [
       ("-s", Arg.Set_string service_name, "the service name");
       ("-i", Arg.Set_string invite,       "the invite");
     ]
     (fun _ -> ()) "Check an invite");
  print_endline
    (match check_invite !service_name !invite with
      | Consumer.Result r -> if r
	then "This invite is valid and can be used"
	else "This invite is invalid or has already been used"
      | Consumer.Error (code, msg) ->
	"Oops! " ^ (string_of_int code) ^ " " ^ msg)
