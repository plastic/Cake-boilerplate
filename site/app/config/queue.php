<?php

 #Queue plugin
 
 #seconds to sleep() when no executable job is found
 #$config['queue']['sleeptime'] = 10;

 #Propability in percent of a old job cleanup happening
 #$config['queue']['gcprop'] = 10;

 #Default timeout after which a job is requeued if the worker doesn’t report back
 #$config['queue']['defaultworkertimeout'] = 120;

 #Default number of retries if a job fails or times out.
 #$config['queue']['defaultworkerretries'] = 2;

 #Seconds of runnig time after which the worker will terminate (0 = unlimited)
 $config['queue']['workermaxruntime'] = 300;

?>