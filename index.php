<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/generic-api
//

include_once('conf.php');
include_once('include.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title><?= $conf['doc']['name'] ?></title>

    <link href="bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js">
    </script>
    <![endif]-->
  </head>
  <body style="padding: 2%">

  <h1><?= $conf['doc']['name'] ?></h1>

  <h2>Methods</h2>
<?php
include_once('methods.php');

function getLabelType($type) {
  if (is_int($type)) {
    if ($type < 300)
      return 'success';
    if ($type < 500)
      return 'warning';
    return 'danger';
  }
  if ($type == 'GET')
    return 'primary';
  elseif ($type == 'POST')
    return 'success';
  elseif ($type == 'PUT')
    return 'warning';
  elseif ($type == 'DELETE')
    return 'danger';
  return 'default';
}

function showError($code, $method = null) {
  global $errors;
?>
      <tr>
	<td><span class="label label-<?= getLabelType($code) ?>"><?= $code ?></span> <?= $errors[$code][0] ?></td>
	<td><pre><?= is_callable($errors[$code][1]) ? $errors[$code][1]($method) : $errors[$code][1] ?></pre></td>
      </tr>
<?php
}

foreach ($methods as $idx => $method) {
  ?>
<div class="well" onclick="toggle_visibility('method_<?= $idx ?>');" style="cursor: pointer">
  <p class="pull-right">
    <?= $method['doc'] ?>
  </p>
  <span class="label label-<?= getLabelType($method['type']) ?>"><?= $method['type'] ?></span>
  <code><?= $method['resource'] ?><?= $method['one'] ? '/{id}' : '' ?></code>
  <?php if ($method['auth_required']) { ?>
  <span class="badge">Auth</span>
  <?php } ?>
  <div id="method_<?= $idx ?>" style="display: none">
    <hr>

    <h4>Parameters</h4>
    <?php if (empty($method['required_params']) && empty($method['optional_params'])) { ?>
    <p>None</p>
    <?php } else { ?>
    <table class="table table-bordered table-hover table-striped">
      <tr>
	<th>Name</th>
	<th>Type</th>
	<th>Default value</th>
      </tr>
      <?php foreach ($method['required_params'] as $name => $type) { ?>
      <tr>
	<td><?= $name ?></td>
	<td><?= $type ?></td>
	<td><span style="color: red;">Required</span></td>
      </tr>
      <?php } ?>
      <?php foreach ($method['optional_params'] as $name => $default_value) { ?>
      <tr>
	<td><?= $name ?></td>
	<td><?= gettype($default_value) == 'NULL' ? 'string' : gettype($default_value) ?></td>
	<td><?= !$default_value ? 'null' : $default_value ?></td>
      </tr>
      <?php } ?>
    </table>
    <?php } ?>
    <h4>Response</h4>
    <table class="table table-bordered table-hover table-striped">
      <tr>
	<th>HTTP Code</th>
	<th>Response body</th>
      </tr>
      <tr>
	<td><span class="label label-success">200</span> Success</td>
	<td><pre><?= convertResponse($method['response']) ?></pre></td>
      </tr>
      <?php if ($method['type'] == 'GET' && $method['one'] === false) { ?>
      <?php showError(202, $method) ?>
      <?php } ?>
      <?php if (!empty($method['required_params'])) { ?>
      <?php showError(400, $method) ?>
      <?php } ?>
      <?php if ($method['auth_required'] === true) { ?>
      <?php showError(403, $method) ?>
      <?php } ?>
    </table>
  </div>

</div>
  <?php
}

?>

  <hr>

  <h2>Global Errors</h2>
  <table class="table table-bordered table-hover table-striped">
    <tr>
      <th>HTTP Code</th>
      <th>Response body</th>
    </tr>
    <?php foreach ($errors as $code => $error) {
	      if ($error[2])
	       showError($code);
    } ?>
  </table>

  <hr>

  <h2>Notes</h2>
  <ul>
    <li>An optional <code>type</code> parameter can be specified to overwrite the HTTP Type (GET, POST, ...).</li>
    <li>An optional <code>format</code> parameter can be specified to overwrite the format (json, php, raw, ...)</li>
  </ul>

  <script type="text/javascript">
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
	  e.style.display = 'none';
       else
	  e.style.display = 'block';
    }
  </script>

  </body>
</html>
