<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/invite
//

class InviteModel {
  private $db;
  public $verbose;
  public function __construct($mysqlLogin, $mysqlPass, $dbname, $host = 'localhost', $verbose = false) {
    $this->db = new PDO('mysql:host='.$host.';dbname='.$dbname,
                        $mysqlLogin, $mysqlPass);
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->verbose = $verbose;
  }

  private function execute($request, $params = array()) {
    try { return $request->execute($params); }
    catch (PDOException $e) {
      if ($this->verbose) {
        echo '<pre>';
        print_r($e);
        echo '</pre>'."\n";
      }
      return false;
    }
  }

  public function addService($params) {
    $r = $this->db->prepare('INSERT INTO services(name, password) VALUES(?, md5(?))');
    if (!($this->execute($r, array($params['service_name'], $params['password']))))
      return array(500);

    $r = $this->db->prepare('INSERT INTO invites(service_name, invite, used) VALUES(?, ?, false)');
    for ($i = 0; $i < $params['nb_invite']; $i++) {
      if (!($this->execute($r, array($params['service_name'], uniqid()))))
        return array(500);
    }
    return true;
  }

  private function checkPassword($params) {
    $r = $this->db->prepare('SELECT * FROM services WHERE name=? AND password=md5(?)');
    if (!($this->execute($r, array($params['service_name'], $params['password']))))
      return array(500);
    if ($r->rowCount() === 0)
      return false;
    return true;
  }

  public function get1Invite($params) {
    if (!$this->checkPassword($params))
      return array(403);
    $r = $this->db->prepare('SELECT invite FROM invites WHERE service_name=? AND used=false '
                            .($params['unique'] ? 'AND sent=false ' : '')
                            .'ORDER BY rand() LIMIT 1');
    if (!($r->execute(array($params['service_name']))))
      return array(500);
    if (!($r->rowCount()))
      return array(202);
    $invite = $r->fetch();
    $invite = $invite[0];
    $r_update = $this->db->prepare('UPDATE invites SET sent=true WHERE service_name=? AND invite=?');
    if (!($this->execute($r_update, array($params['service_name'], $params['id']))))
      return array(500);
    return $invite;
  }

  public function useInvite($params) {
    $r = $this->db->prepare('UPDATE invites SET used=true WHERE service_name=? AND invite=? AND used=false');
    if (!($this->execute($r, array($params['service_name'], $params['id']))))
      return array(500);
    return $r->rowCount() != 0;
  }

  public function checkInvite($params) {
    $r = $this->db->prepare('SELECT * FROM invites WHERE used=false AND service_name=? AND invite=?');
    if (!($this->execute($r, array($params['service_name'], $params['id']))))
      return array(500);
    return $r->rowCount() != 0;
  }

}
