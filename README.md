bStat Light
===========

bStat Light is a trimmed-down version of [bStat](https://github.com/misterbisson/bstat) that only [localizes](https://github.com/misterbisson/bstat-light/blob/master/components/class-bstat.php#L42) and [enqueues](https://github.com/misterbisson/bstat-light/blob/master/components/js/bstat.js) the JS necessary for tracking to a remote endpoint.

Be sure to set the tracking endpoint via [the `go_config` filter](https://github.com/misterbisson/bstat-light/blob/master/components/class-bstat.php#L24).

Do not attempt to run this and [bStat](https://github.com/misterbisson/bstat) on the same blog at the same time. It will cause fatals.
