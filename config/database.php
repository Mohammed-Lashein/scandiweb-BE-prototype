<?php

return [
  'host' => 'localhost',
  /* Don't use hyphens in db names as it gets encoded by mysql .
  Encoding won't cause a problem, but it just shows the database dir
  (in the mysql dir on using xampp for example) with the encoded value (which is a bit unpredicted . I expect to see the database name as I wrote it ) .  */
  'database' => 'scandiweb_task',
  'username' => 'root',
  'password' => ''
];