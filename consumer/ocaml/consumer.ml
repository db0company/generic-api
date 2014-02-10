(* ************************************************************************** *)
(* Project: Generic API Consumer                                              *)
(* Author: db0 (db0company@gmail.com, http://db0.fr/)                         *)
(* Latest Version on GitHub: https://github.com/db0company/invite             *)
(* ************************************************************************** *)

open Yojson.Basic.Util

(* ************************************************************************** *)
(* Types                                                                      *)
(* ************************************************************************** *)

type error_code = int
type error_message = string
type 'a t = Result of 'a | Error of (error_code * error_message)

type rtype =
  | GET
  | POST
  | PUT
  | DELETE

let rtype_to_string = function
  | GET    -> "GET"
  | POST   -> "POST"
  | PUT    -> "PUT"
  | DELETE -> "DELETE"
let rtype_of_string = function
  | "GET"    -> GET
  | "POST"   -> POST
  | "PUT"    -> PUT
  | "DELETE" -> DELETE
  | _        -> GET

(* ************************************************************************** *)
(* Curl Connection                                                            *)
(* ************************************************************************** *)

let connection = ref None

let writer accum data =
  Buffer.add_string accum data;
  String.length data
let result = Buffer.create 4096
let error_buffer = ref ""

let connect base_url =
  try (
    Curl.global_init Curl.CURLINIT_GLOBALALL;
    let c = Curl.init () in
    connection := Some (c, base_url);
    Curl.set_errorbuffer c error_buffer;
    Curl.set_writefunction c (writer result);
    Curl.set_followlocation c true;
    Result ())
  with _ -> Error (404, "Connection failure")

let disconnect () =
  match !connection with
    | Some (c, _) ->
      connection := None;
      Curl.cleanup c;
      Curl.global_cleanup ()
    | _ -> ()

(* ************************************************************************** *)
(* Format Helpers                                                             *)
(* ************************************************************************** *)

let format_raw_json text = Yojson.Basic.from_string text
let format_json f text = f (format_raw_json text)
let format_json_list f text =
  try
    (let open Yojson.Basic.Util in
     match format_raw_json text |> to_option (convert_each f) with
       | Some l -> l
       | None -> [])
  with Yojson.Json_error "Blank input data" -> []
let format_bool text = try bool_of_string text with _ -> false
let format_raw text = text

(* ************************************************************************** *)
(* Curl Method handling                                                       *)
(* ************************************************************************** *)

let go ?(rtype = GET) ?(resource = "") ?(id = "") ?(get = []) format =

  match !connection with
    | None -> Error (400, "Not connected")
    | Some (c, base_url) ->

      let parameters_to_string parameters =
        let str =
          let f = (fun f (s, v) -> f ^ "&" ^ s ^ "=" ^ v) in
          List.fold_left  f "" parameters in
        if (String.length str) = 0 then str
        else "?" ^ (Str.string_after str 1) in

      let url =
        base_url ^ resource
        ^ (if id = "" then "" else "/" ^ id)
        ^ (parameters_to_string get) in

      try (
        Buffer.reset result;
        Curl.set_customrequest c (rtype_to_string rtype);
        Curl.set_url c url;
        Curl.perform c;
        let text = Buffer.contents result in
        match Curl.get_responsecode c with
          | 200  -> Result (format text)
	  | 202  -> Result (format "")
          | code -> Error (code, text))
      with
        | Curl.CurlException (_, _, _) -> Error (400, !error_buffer)
        | Failure msg -> Error (400, msg)
        | Invalid_argument s -> Error (400, "Invalid argument: " ^ s)
        | Yojson.Basic.Util.Type_error (msg, tree) -> Error (400, "Invalid Json tree: "
          ^ (Yojson.Basic.to_string tree))
        | Yojson.Json_error msg -> Error (400, "Json error:" ^ msg)
        | _ -> Error (400, "Unexpected unknown error")

