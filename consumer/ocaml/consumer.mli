(* ************************************************************************** *)
(* Project: Generic API Consumer                                              *)
(* Author: db0 (db0company@gmail.com, http://db0.fr/)                         *)
(* Latest Version on GitHub: https://github.com/db0company/invite             *)
(* ************************************************************************** *)

type error_code = int
type error_message = string
type 'a t = Result of 'a | Error of (error_code * error_message)

type rtype =
  | GET
  | POST
  | PUT
  | DELETE

val rtype_to_string : rtype -> string
val rtype_of_string : string -> rtype

val format_json : (Yojson.Basic.json -> 'a) -> string -> 'a
val format_raw_json : string -> Yojson.Basic.json
val format_bool : string -> bool
val format_raw : string -> string

val connect : string -> unit t
val disconnect : unit -> unit

val go :
  ?rtype:rtype
  -> ?resource:string
  -> ?id:string
  -> ?get:(string * string) list
  -> (string -> 'a)
  -> 'a t
