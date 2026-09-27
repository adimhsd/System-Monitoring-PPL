<?php

passthru('powershell -Command "$env:APP_ENV=\'testing\'; vendor\\bin\\phpunit"');
