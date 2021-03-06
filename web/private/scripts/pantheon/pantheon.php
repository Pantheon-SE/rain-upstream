<?php

// Enable Pantheon options
if (isset($_POST['environment'])) {

  // Get site settings
  $req = pantheon_curl('https://api.live.getpantheon.com/sites/self/settings', NULL, 8443);
  $settings = json_decode($req['body'], true);

  // Enable Redis
  if ($settings['allow_cacheserver'] != 1) {
    $req = pantheon_curl('https://api.live.getpantheon.com/sites/self/settings', '{"allow_cacheserver":true}', 8443, 'PUT');
  }

  // Get bindings
  $req = pantheon_curl('https://api.live.getpantheon.com/sites/self/environments/self/bindings', NULL, 8443);
  $bindings = json_decode($req['body'], true);
  print_r($bindings);
  $nr = false;
  foreach ($bindings as $binding) {
    if ($binding['type'] == 'newrelic') {
      $nr = true;
      break;
    }
  }

  // If New Relic not detected, enable New Relic.
  if ($nr == false) {
    // disable for now
    //$req = pantheon_curl('https://api.live.getpantheon.com/sites/self/workflows', '{"type":"enable_new_relic_for_site","params":{"converge":true}}', 8443, 'PUT');
  }
}
