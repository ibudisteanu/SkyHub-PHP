<?php
$bDone = false;
$events = new Events();

function callback($text) {
    printf("I'm a timer callback %s\n", $text);
}

function callback_end($sText)
{
    global $bDone;
    $bDone=true;
}

$events->AddTimer("25s", "callback_end", "every 5 seconds", EVENTS::EVENT_REPEAT);
$events->AddTimer("5s", "callback", "every 5 seconds", EVENTS::EVENT_REPEAT);
$events->AddTimer("2s..15s", "callback", "random time 2 to 15 seconds", EVENTS::EVENT_REPEAT);
$events->AddTimer("1m", create_function('', "echo 'Im a LAMBDA function that runs every minute\n';"), false, EVENTS::EVENT_REPEAT);
$events->AddTimer("1h..2h", "callback", "I will run once, in between 1 and 2 hours");

# This is just an example, in reality, you would make sure to call CheckTimer() regulary (ideally not more than once a second)

while (!$bDone ) {
    $events->CheckTimers();
    printf("Next timer in %s seconds...\n", $ne = $events->GetNextTimer(), gettype($ne));
    sleep(1);
}


class Events {
    const EVENT_REPEAT      = 0x0001;
    const EVENT_SEQUENCE    = 0x0002;
    var $events;
    var $timers;

    function __construct() {
        $this->events = array();
        $this->timers = array();
    }

    function AddTimer($when, $action, $args = false, $flags = 0) {
        if (preg_match('#([0-9a-zA-Z]+)..([0-9a-zA-Z]+)#', $when, $a)) {
            $time = time(NULL) + rand($this->time2seconds($a[1]), $this->time2seconds($a[2]));
        } else {
            $time = time(NULL) + $this->time2seconds($when);
        }
        if ($flags & self::EVENT_SEQUENCE) {
            while ($this->IsArrayCount($this->timers[$time])) {
                $time ++;
            }
        }
        $this->timers[$time][] = array("when" => $when, "action" => $action, "args" => $args, "flags" => $flags);
        ksort($this->timers);
    }

    function GetNextTimer() {
        if (!$this->IsArrayCount($this->timers)) {
            return false;
        }
        reset($this->timers);
        $firstevent = each($this->timers);
        if ($firstevent === false) {
            return false;
        }
        $time = $firstevent["key"];
        $nextEvent = $time - time(NULL);
        if ($nextEvent < 1) {
            return 1;
        }

        return $nextEvent;
    }

    function CheckTimers() {
        $rv = false;
        $now = time(NULL);
        foreach ($this->timers as $time => $events) {
            if ($time > $now) {
                break;
            }
            foreach ($events as $key => $event) {
                # debug("Event::CheckTimer: {$event["action"]}");
                # ircPubMsg("Event::CheckTimer: {$event["action"]}", "#bots");
                if (!$event["args"]) {
                    call_user_func($event["action"]);
                } else {
                    $rv = call_user_func_array($event["action"], is_array($event["args"]) ? $event["args"] : array($event["args"]));
                }
                unset($this->timers[$time][$key]);
                if ($event["flags"] & self::EVENT_REPEAT) {
                    $this->AddTimer($event["when"], $event["action"], $event["args"], $event["flags"]);
                }
                if ($rv) {
                    # break;
                }
            }
            if (!$this->IsArrayCount($this->timers[$time])) {
                unset($this->timers[$time]);
            }

            if (0 && $rv) {
                break;
            }
        }

        if ($rv) {
            return $rv;
        }
    }

    function time2seconds($timeString) {
        $end = substr($timeString, strlen($timeString) - 1);
        $seconds = intval($timeString); //  = preg_replace("#[^0-9]#", "", $a);

        if (is_numeric($end)) {
            return $seconds;
        }

        $unim = array("s","m","h","d", "w", "m", "y");
        $divs = array(1, 60, 60, 24, 7, 28, 12, false);
        $found = false;
        while (!$found) {
            $u = array_shift($unim);
            $d = array_shift($divs);
            $seconds *= $d;
            if ($end === $u) {
                return $seconds;
            }
        }

        return intval($timeString);
    }

    function IsArrayCount($possibleArray) {
        return (isset($possibleArray) && is_array($possibleArray)) ? count($possibleArray) : false;
    }
}
?>