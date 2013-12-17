<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/invite
//

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Invite API</title>

    <link href="bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js">
    </script>
    <![endif]-->
  </head>
  <body style="padding: 2%">

  <h2>Methods</h2>
<?php
include_once('methods.php');

function getLabelType($type) {
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
        <td><?= gettype($default_value) ?></td>
        <td><?= $default_value ?></td>
      </tr>
      <?php } ?>
    </table>
    <h4>Response</h4>
    <table class="table table-bordered table-hover table-striped">
      <tr>
        <th>HTTP Code</th>
        <th>Response body</th>
      </tr>
      <tr>
        <td><span class="label label-success">200</span> Success</td>
        <td><pre><?= $method['response'] ?></pre></td>
      </tr>
      <?php if ($method['type'] == 'GET' && $method['one'] === false) { ?>
      <tr>
        <td><span class="label label-success">202</span> No Result</td>
        <td><pre> </pre></td>
      </tr>
      <?php } ?>
      <?php if (!empty($method['required_params'])) { ?>
      <tr>
        <td><span class="label label-warning">400</span> Bad Request</td>
        <td><pre>Required parameters missing: ...</pre></td>
      </tr>
      <?php } ?>
      <?php if (isset($method['auth_required'])) { ?>
      <tr>
        <td><span class="label label-warning">403</span> Forbidden</td>
        <td><pre>Authentication failed.</pre></td>
      </tr>
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
    <tr>
    <tr>
      <td><span class="label label-warning">404</span> Not Found</td>
      <td><pre>No such method.</pre></td>
    </tr>
    <tr>
      <td><span class="label label-danger">500</span> Internal Server Error</td>
      <td><pre>Something went wrong</pre></td>
    </tr>
    <tr>
      <td><span class="label label-danger">501</span> Not implemented</td>
      <td><pre>This method is not implemented. It should be!</pre></td>
    </tr>
  </table>

  <hr>

  <h2>Notes</h2>
  <ul>
    <li>An optional <code>type</code> parameter can be specified to overwrite the HTTP Type (GET, POST, ...).</li>
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
